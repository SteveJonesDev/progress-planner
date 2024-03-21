<?php
/**
 * The "Progress Professional" badge.
 *
 * This badge is awarded when the user has a 6-week streak of completing a weekly activity.
 *
 * @package ProgressPlanner
 */

use ProgressPlanner\Goals\Goal_Recurring;
use ProgressPlanner\Goals\Goal_Posts;
use ProgressPlanner\Base;
use ProgressPlanner\Settings;
use ProgressPlanner\Badges;

/*
 * Create a recurring goal for any type of weekly activity.
 */
$prpl_goal = Goal_Recurring::get_instance(
	'weekly_activity',
	[
		'class_name'  => Goal_Posts::class,
		'id'          => 'weekly_activity',
		'title'       => \esc_html__( 'Weekly activity', 'progress-planner' ),
		'description' => \esc_html__( 'Streak: The number of weeks this goal has been accomplished consistently.', 'progress-planner' ),
		'status'      => 'active',
		'priority'    => 'low',
		'evaluate'    => function ( $goal_object ) {
			return (bool) count(
				\progress_planner()->get_query()->query_activities(
					[
						'start_date' => $goal_object->get_details()['start_date'],
						'end_date'   => $goal_object->get_details()['end_date'],
					]
				)
			);
		},
	],
	[
		'frequency'     => 'weekly',
		'start'         => Base::get_activation_date(),
		'end'           => new \DateTime(), // Today.
		'allowed_break' => 1, // Allow break in the streak for 1 week.
	]
);

/**
 * The "Progress Professional" badge.
 *
 * This badge is awarded when the user has completed 6 weeks of a weekly activity.
 */
Badges::register_badge(
	'progress-professional',
	[
		'category'          => 'streak_any_task',
		'name'              => __( 'Progress Professional', 'progress-planner' ),
		'icons-svg'         => [
			'pending'  => [
				'path' => \PROGRESS_PLANNER_DIR . '/assets/images/badges/streak_badge1_gray.svg',
				'url'  => \PROGRESS_PLANNER_URL . '/assets/images/badges/streak_badge1_gray.svg',
			],
			'complete' => [
				'path' => \PROGRESS_PLANNER_DIR . '/assets/images/badges/streak_badge1.svg',
				'url'  => \PROGRESS_PLANNER_URL . '/assets/images/badges/streak_badge1.svg',
			],
		],
		'progress_callback' => function () use ( $prpl_goal ) {
			$saved_progress = (int) Settings::get( [ 'badges', 'progress-professional' ], [] );

			// If the badge is already complete, return 100% progress.
			if ( isset( $saved_progress['progress'] ) && 100 === $saved_progress ) {
				return [
					'percent'   => 100,
					'remaining' => 0,
				];
			}

			// In order to avoid querying the database every time, we save the progress and date.
			// This works as a cache for the progress, and will get updated every 2 days.
			$use_saved = false;
			if ( isset( $saved_progress['date'] ) ) {
				$last_date = new \DateTime( $saved_progress['date'] );
				$diff      = $last_date->diff( new \DateTime() );
				if ( $diff->days <= 2 ) {
					$use_saved = true;
				}
			}

			// If we're using the saved value, return it.
			if ( $use_saved ) {
				return [
					'percent'   => $saved_progress['progress'],
					'remaining' => $saved_progress['remaining'],
				];
			}

			$max_streak = $prpl_goal->get_streak()['max_streak'];
			$percent    = min( 100, floor( 100 * $max_streak / 6 ) );
			$remaining  = 6 - min( 6, $max_streak );

			Settings::set(
				[ 'badges', 'progress-professional' ],
				[
					'progress'  => $percent,
					'remaining' => $remaining,
					'date'      => ( new \DateTime() )->format( 'Y-m-d H:i:s' ),
				]
			);

			return [
				'percent'   => $percent,
				'remaining' => $remaining,
			];
		},
	]
);
