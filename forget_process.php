<?php

session_start();
include_once('config/database.php');

if (isset($_POST['new_password']) && isset($_POST['login']) && isset($_POST['token']))
{
	$verif = $bdd->prepare('SELECT count(id) as is_data_ok FROM user WHERE token = ? AND login = ?');
	$verif->execute(array(
		htmlentities($_POST['token']),
		htmlentities($_POST['login'])
	));
	$data = $verif->fetch();
	$verif->closeCursor();
	if ($data['is_data_ok'] == 1)
	{
		$password_hash = htmlentities(hash('whirlpool', hash('whirlpool', $_POST['new_password'])));

		$update = $bdd->prepare('UPDATE user SET password = ? WHERE login = ? AND token = ?');
		$update->execute(array($password_hash, htmlentities($_POST['login']), htmlentities($_POST['token'])));
		$update->execute();
		$update->closeCursor();
		$_SESSION['status'] = 'Votre mot de passe a bien ete change';
		header('Location: login.php');
	}
}