<?php
// Include the interfaces

foreach (glob(dirname(__FILE__) . '/../../adapter-classes-for-wordpress/includes/interface-*.php') as $filename) {
	require_once $filename;
}
// Include the adapter classes

foreach (glob(dirname(__FILE__) . '/../../adapter-classes-for-wordpress/includes/class-*.php') as $filename) {
	require_once $filename;
}
// Include all the plugin classes

foreach (glob(dirname(__FILE__) . '/../includes/class-*.php') as $file) {
	require_once $file;
}
// Include the adapter classes test case
require_once dirname(__FILE__) . '/../../adapter-classes-for-wordpress/tests/class-adapter_classes_testcase.php';
?>