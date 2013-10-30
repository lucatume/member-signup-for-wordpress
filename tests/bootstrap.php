<?php
// Include all the plugin classes

foreach (glob(__DIR__ . '/../includes/class-*.php') as $file) {
	include_once $file;
}
// Include the adapter classes

foreach (glob(__DIR__ . '/../../adapter-classes-for-wordpress/includes/class-*.php') as $filename) {
	require_once $filename;
}
?>