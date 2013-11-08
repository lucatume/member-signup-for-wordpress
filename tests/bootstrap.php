<?php
// Include the interfaces

foreach (glob(__DIR__ . '/../../adapter-classes-for-wordpress/includes/interface-*.php') as $filename) {
	require_once $filename;
}
// Include the adapter classes

foreach (glob(__DIR__ . '/../../adapter-classes-for-wordpress/includes/class-*.php') as $filename) {
	require_once $filename;
}
// Include all the plugin classes

foreach (glob(__DIR__ . '/../includes/class-*.php') as $file) {
	require_once $file;
}
// Include the adapter classes test case
require_once __DIR__ . '/../../adapter-classes-for-wordpress/tests/class-adapter_classes_testcase.php';
?>