<?php

session_start();
include_once('config/database.php');

if (isset($_GET['id_photo']))
{
	$find = $bdd->prepare('SELECT * FROM comments WHERE id_photo = ? ORDER BY id');
	$find->execute(array(htmlentities($_GET['id_photo'])));
	$data = $find->fetchAll();
	$find->closeCursor();

	foreach ($data as $row)
	{
		echo '<span style="font-weight: bold;">'.$row['login'].'</span>';
		echo ' : ';
		echo stripslashes($row['commentaire']);
		echo '<br>';
	}
}