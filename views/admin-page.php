<?php
/**
 * View for the admin page.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner;

$prpl_scan_pending = empty(
	\progress_planner()->get_query()->query_activities(
		[
			'category' => 'post',
			'type'     => 'publish'
		]
	)
);
?>
<div class="wrap prpl-wrap">
	<h1><?php \esc_html_e( 'Progress Planner', 'progress-planner' ); ?></h1>

	<?php if ( $prpl_scan_pending ) : ?>
		<?php include 'admin-page-form-scan.php'; ?>
	<?php else : ?>
		<?php include 'widget-published-posts.php'; ?>
		<?php include 'admin-page-debug.php'; ?>
		<hr>
	<?php endif; ?>
</div>
<?php
