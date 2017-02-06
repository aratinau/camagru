<?php

session_start();
include_once('config/database.php');

if (isset($_GET['verif']) && isset($_GET['login']))
{
	$_SESSION['verif'] = htmlentities($_GET['verif']);
	$_SESSION['login'] = htmlentities($_GET['login']);
}

header('Location: index.php');