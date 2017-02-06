<?php

session_start();
include_once('config/database.php');

$find = $bdd->prepare('SELECT * FROM jpeg WHERE login = ? ORDER BY id DESC');
$find->execute(array($_SESSION['user']));
$data = $find->fetchAll();
$find->closeCursor();

foreach ($data as $row)
{
	echo '<img src="uploads/'.$row["name_timestamp"].'.jpeg">';
}

?>