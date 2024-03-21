<?php
/**
 * The "Notorious Novelist" badge.
 *
 * This badge is awarded when the user has published 50 new posts.
 *
 * @package ProgressPlanner
 */

use ProgressPlanner\Base;
use ProgressPlanner\Settings;
use ProgressPlanner\Badges;

Badges::register_badge(
	'notorious-novelist',
	[
		'category'          => 'content_writing',
		'name'              => __( 'Awesome Author', 'progress-planner' ),
		'icons-svg'         => [
			'pending'  => [
				'path' => \PROGRESS_PLANNER_DIR . '/assets/images/badges/writing_badge3_gray.svg',
				'url'  => \PROGRESS_PLANNER_URL . '/assets/images/badges/writing_badge3_gray.svg',
			],
			'complete' => [
				'path' => \PROGRESS_PLANNER_DIR . '/assets/images/badges/writing_badge3.svg',
				'url'  => \PROGRESS_PLANNER_URL . '/assets/images/badges/writing_badge3.svg',
			],
		],
		'progress_callback' => function () {
			$saved_progress = (int) Settings::get( [ 'badges', 'notorious-novelist' ], [] );

			// If the badge is already complete, return 100% progress.
			if ( isset( $saved_progress['progress'] ) && 100 === $saved_progress ) {
				return [
					'percent'   => 100,
					'remaining' => 0,
				];
			}

			// Get the number of new posts published.
			$new_count = count(
				\progress_planner()->get_query()->query_activities(
					[
						'category'   => 'content',
						'type'       => 'publish',
						'start_date' => Base::get_activation_date(),
					],
				)
			);

			$percent   = min( 100, floor( 100 * $new_count / 50 ) );
			$remaining = 50 - min( 50, $new_count );

			// If the user has published 50 new posts, save the badge as complete and return.
			if ( 0 === $remaining ) {
				Settings::set(
					[ 'badges', 'notorious-novelist' ],
					[
						'progress' => 100,
						'date'     => ( new \DateTime() )->format( 'Y-m-d H:i:s' ),
					]
				);
			}

			return [
				'percent'   => $percent,
				'remaining' => $remaining,
			];
		},
	]
);
