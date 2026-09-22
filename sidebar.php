<?php
/**
 * The sidebar containing the footer widget areas.
 *
 * @package kriate
 */

$kriate_widget_areas = array(
	'bottom-widget-1',
	'bottom-widget-2',
	'bottom-widget-3',
	'bottom-widget-4',
);

$kriate_has_active_widget_area = false;
foreach ( $kriate_widget_areas as $kriate_widget_area ) {
	if ( is_active_sidebar( $kriate_widget_area ) ) {
		$kriate_has_active_widget_area = true;
		break;
	}
}

if ( ! $kriate_has_active_widget_area ) {
	return;
}
?>

<div id="secondary" class="widget-area" role="complementary">
	<div id="widget_col-1" class="widget-col col-6 col-sm-3"><?php dynamic_sidebar( 'bottom-widget-1' ); ?></div>
	<div id="widget_col-2" class="widget-col col-6 col-sm-3"><?php dynamic_sidebar( 'bottom-widget-2' ); ?></div>
	<div id="widget_col-3" class="widget-col col-6 col-sm-3"><?php dynamic_sidebar( 'bottom-widget-3' ); ?></div>
	<div id="widget_col-4" class="widget-col col-6 col-sm-3"><?php dynamic_sidebar( 'bottom-widget-4' ); ?></div>
</div><!-- #secondary -->
