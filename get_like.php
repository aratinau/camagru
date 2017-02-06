<?php

session_start();
include_once('config/database.php');

if (!isset($_SESSION['user']) || !isset($_GET['id_photo']))
{
	header('Location: login.php');
}

// si elle est deja dans les likes on l'a supprime
// sinon on l'ajoute
$find = $bdd->prepare('SELECT count(id) as is_liked FROM liked WHERE login = ? AND id_photo = ?');
$find->execute(array(
	$_SESSION['user'],
	htmlentities($_GET['id_photo'])
));
$data = $find->fetch();
$find->closeCursor();

if ($data['is_liked'] == 0)
{
	echo '0';
}
else
{
	echo '1';
}