<?php
/**
 * The sidebar containing the main widget area.
 *
 * @package kriate
 */

if ( ! is_active_sidebar( 'bottom-widget-1' ) ) {
	return;
}
?>

<div id="secondary" class="widget-area" role="complementary">
	<div id="widget_col-1" class="widget-col col-6 col-sm-3"><?php dynamic_sidebar( 'bottom-widget-1' ); ?></div>
	<div id="widget_col-2" class="widget-col col-6 col-sm-3"><?php dynamic_sidebar( 'bottom-widget-2' ); ?></div>
	<div id="widget_col-3" class="widget-col col-6 col-sm-3"><?php dynamic_sidebar( 'bottom-widget-3' ); ?></div>
	<div id="widget_col-4" class="widget-col col-6 col-sm-3"><?php dynamic_sidebar( 'bottom-widget-4' ); ?></div>
	
	
	
</div><!-- #secondary -->
