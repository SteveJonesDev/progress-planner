<?php
/**
 * Activity Scores Widget.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner\Popups;

use Progress_Planner\Badges as Root_Badges;
use Progress_Planner\Base;

/**
 * Activity Scores Widget.
 */
final class Badges extends Popup {

	/**
	 * An array of badge IDs.
	 *
	 * @var array<string, string[]>
	 */
	const BADGES = [
		'content'     => [
			'wonderful-writer',
			'bold-blogger',
			'awesome-author',
		],
		'maintenance' => [
			'progress-padawan',
			'maintenance-maniac',
			'super-site-specialist',
		],
	];

	/**
	 * The popover ID.
	 *
	 * @var string
	 */
	protected $id = 'badges-details';

	/**
	 * Render the widget content.
	 *
	 * @return void
	 */
	protected function the_content() {
		?>
		<h2><?php \esc_html_e( 'You are on the right track!', 'progress-planner' ); ?></h2>
		<p><?php \esc_html_e( 'Find out which badges to unlock next and become a Progress Planner Professional!', 'progress-planner' ); ?></p>

		<div class="prpl-widgets-container">
			<div class="prpl-widget-wrapper">
				<h3><?php \esc_html_e( 'Don’t break your streak and stay active every week!', 'progress-planner' ); ?></h3>
				<p><?php \esc_html_e( 'Execute at least one website maintenance task every week. That could be publishing content, adding content, updating a post, or updating a plugin.', 'progress-planner' ); ?></p>
				<p><?php \esc_html_e( 'Not able to work on your site for a week? Use your streak freeze!', 'progress-planner' ); ?></p>
				<div id="popover-badges-content">
					<?php $this->print_badges( 'maintenance' ); ?>
				</div>
				<div class="progress-badges">
					<span class="badges-popover-progress-total">
						<span style="width: <?php echo (int) Root_Badges::get_badge_progress( 'super-site-specialist' )['progress']; ?>%"></span>
					</span>
					<div class="indicators-maintenance">
						<?php foreach ( self::BADGES['maintenance'] as $badge ) : ?>
							<div class="indicator">
								<?php $badge_progress = Root_Badges::get_badge_progress( $badge ); ?>
								<span class="indicator-label">
									<?php if ( 0 === (int) $badge_progress['remaining'] ) : ?>
										✔️
									<?php else : ?>
										<?php
										printf(
											/* translators: The number of weeks remaining to complete the badge. */
											\esc_html__( '%s to go', 'progress-planner' ),
											Base::weeks( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
												(int) $badge_progress['remaining'],
												'<span class="number">' . (int) $badge_progress['remaining'] . '</span>'
											)
										)
										?>
									<?php endif; ?>
								</span>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			</div>

			<div class="prpl-widget-wrapper">
				<h3><?php \esc_html_e( 'Keep adding posts and pages', 'progress-planner' ); ?></h3>
				<p><?php \esc_html_e( 'The more you write, the sooner you unlock new badges. You can earn level 1 of this badge immediately after installing the plugin if you have written 20 or more blog posts.', 'progress-planner' ); ?></p>
				<div id="popover-badges-maintenance">
					<?php $this->print_badges( 'content' ); ?>
				</div>
			</div>
		</div>
		<div class="footer">
			<div class="string-freeze-explain">
				<h2><?php \esc_html_e( 'Streak freeze', 'progress-planner' ); ?></h2>
				<p><?php \esc_html_e( 'Going on a holiday? Or don\'t have any time this week? You can skip your website maintenance for a maximum of one week. Your streak will continue afterward.', 'progress-planner' ); ?></p>
			</div>
		</div>
		<?php
	}

	/**
	 * Print badges by category.
	 *
	 * @param string $category The category of badges.
	 *
	 * @return void
	 */
	public static function print_badges( $category ) {
		?>
		<?php foreach ( self::BADGES[ $category ] as $badge ) : ?>
			<?php
			$badge_progress  = Root_Badges::get_badge_progress( $badge );
			$badge_completed = 100 === (int) $badge_progress['progress'];
			$badge_args      = Root_Badges::get_badge( $badge );
			?>
			<span
				class="prpl-badge"
				data-value="<?php echo \esc_attr( $badge_progress['progress'] ); ?>"
			>
				<div class="inner">
					<?php
					include $badge_completed
						? $badge_args['icons-svg']['complete']['path']
						: $badge_args['icons-svg']['pending']['path'];
					?>
					<?php echo \esc_html( Root_Badges::get_badge( $badge )['name'] ); ?>
				</div>
				<p><?php echo \esc_html( Root_Badges::get_badge( $badge )['description'] ); ?></p>
			</span>
		<?php endforeach; ?>
		<?php
	}
}
