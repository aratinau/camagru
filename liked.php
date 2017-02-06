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
	$write = $bdd->prepare('INSERT INTO liked(id_photo, login) VALUES(:id_photo, :login)');
    $write->execute(array(
        'login' => $_SESSION['user'],
        'id_photo' => htmlentities($_GET['id_photo'])
        )
    );
    $write->closeCursor();
	// on l'ajoute
	echo '1';
}
else
{
	$delete = $bdd->prepare('DELETE FROM liked WHERE id_photo = ? AND login = ?');
	$delete->execute(array(
	        htmlentities($_GET['id_photo']),
	        $_SESSION['user']
        )
    );
	$delete->closeCursor();
	// on l'enleve
	echo '0';
}