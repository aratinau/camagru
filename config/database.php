<?php

$DB_NAME = 'camagru';
$DB_USER =  'root';
$DB_PASSWORD = 'root';

$DB_DSN = 'mysql:host=localhost;dbname='.$DB_NAME.';charset=utf8';

if (isset($_SESSION['install']) && $_SESSION['install'] == 1)
{
	unset($_SESSION['install']);
	$DB_DSN = 'mysql:host=localhost;charset=utf8';
}
else
{

	$DB_OPT = array(
		PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION
	);

	try
	{
		$bdd = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD, $DB_OPT);
	}
	catch(Exception $e)
	{
		die('Erreur : '.$e->getMessage() . '<br>maybe you need to install it : configure the config/setup.php file and then go to <a href="./config/setup.php">/config/setup.php</a>');
	}

}
?>
