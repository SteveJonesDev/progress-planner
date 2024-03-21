<?php
/**
 * ProgressPlanner widget.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner;

use ProgressPlanner\Activities\Content_Helpers;

// phpcs:ignore WordPress.Security.NonceVerification.Recommended
$prpl_active_range = isset( $_GET['range'] ) ? sanitize_text_field( wp_unslash( $_GET['range'] ) ) : '-6 months';
// phpcs:ignore WordPress.Security.NonceVerification.Recommended
$prpl_active_frequency = isset( $_GET['frequency'] ) ? sanitize_text_field( wp_unslash( $_GET['frequency'] ) ) : 'monthly';

// Arguments for the query.
$prpl_query_args = [
	'category' => 'content',
	'type'     => 'publish',
];

/**
 * Callback to count the words in the activities.
 *
 * @param \ProgressPlanner\Activity[] $activities The activities array.
 *
 * @return int
 */
$prpl_count_words_callback = function ( $activities ) {
	$words = 0;
	foreach ( $activities as $activity ) {
		if ( ! $activity->get_post() ) {
			continue;
		}
		$words += Content_Helpers::get_word_count(
			$activity->get_post()->post_content,
			$activity->get_data_id()
		);
	}
	return $words;
};

// Get the weekly words count.
$prpl_this_week_words = $prpl_count_words_callback(
	\progress_planner()->get_query()->query_activities(
		array_merge(
			$prpl_query_args,
			[
				'start_date' => new \DateTime( '-7 days' ),
				'end_date'   => new \DateTime(),
			]
		)
	)
);

/**
 * Callback to get the color for the chart.
 *
 * @return string
 */
$prpl_color_callback = function () {
	return '#14b8a6';
};

?>
<div class="two-col">
	<div class="prpl-top-counter-bottom-content">
		<div class="counter-big-wrapper">
			<span class="counter-big-number">
				<?php echo esc_html( number_format_i18n( $prpl_this_week_words ) ); ?>
			</span>
			<span class="counter-big-text">
				<?php esc_html_e( 'words written', 'progress-planner' ); ?>
			</span>
		</div>
		<div class="prpl-widget-content">
			<p>
				<?php if ( 0 === $prpl_this_week_words ) : ?>
					<?php esc_html_e( 'No words written last week', 'progress-planner' ); ?>
				<?php else : ?>
					<?php
					printf(
						/* translators: %1$s: number of posts published this week. %2$s: Total number of posts. */
						esc_html__( 'Great! You have written %1$s words in the past 7 days.', 'progress-planner' ),
						esc_html( number_format_i18n( $prpl_this_week_words ) ),
					);
					?>
				<?php endif; ?>
			</p>
		</div>
	</div>
	<div class="prpl-graph-wrapper">
		<?php
		( new Chart() )->the_chart(
			[
				'query_params'   => [
					'category' => 'content',
					'type'     => 'publish',
				],
				'dates_params'   => [
					'start'     => \DateTime::createFromFormat( 'Y-m-d', \gmdate( 'Y-m-01' ) )->modify( $prpl_active_range ),
					'end'       => new \DateTime(),
					'frequency' => $prpl_active_frequency,
					'format'    => 'M',
				],
				'chart_params'   => [
					'type' => 'line',
				],
				'count_callback' => $prpl_count_words_callback,
				'additive'       => false,
				'colors'         => [
					'background' => $prpl_color_callback,
					'border'     => $prpl_color_callback,
				],
			],
		);
		?>
	</div>
</div>
