--TEST--
CFUtilities - json_encode float

--FILE--
<?php
	// Pre-config
	ini_set('disable_functions', 'json_encode');

	// Dependencies
	require_once dirname(__FILE__) . '/../../cloudfusion.class.php';

	// Instantiate
	$util = new CFUtilities();

	// Test data
	var_dump($util->json_encode(1.2));
?>

--EXPECT--
string(3) "1.2"