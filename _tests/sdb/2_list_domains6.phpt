--TEST--
AmazonSDB::list_domains MaxNumberOfDomains + returnCurlHandle

--FILE--
<?php
	require_once dirname(__FILE__) . '/../../cloudfusion.class.php';
	$sdb = new AmazonSDB();

	// First time pulls live data
	$response = $sdb->list_domains(array(
		'MaxNumberOfDomains' => 1,
		'returnCurlHandle' => true
	));
	var_dump($response);
?>

--EXPECT--
resource(9) of type (curl)
