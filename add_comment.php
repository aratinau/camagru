<?php

session_start();
include_once('config/database.php');

if (!isset($_SESSION['user']))
{
	header('Location: login.php');
}

if (isset($_POST['new_comment']) && isset($_POST['user']) && isset($_POST['photo_id']))
{
	if (!empty($_POST['new_comment']))
	{
		$write = $bdd->prepare('INSERT INTO comments(id_photo, login, commentaire) VALUES(:id_photo, :login, :commentaire)');
		$write->execute(array(
		    'id_photo' => htmlentities($_POST['photo_id']),
		    'login' => htmlentities($_POST['user']),
		    'commentaire' => addslashes(nl2br(htmlentities($_POST['new_comment'])))
		    )
		);
		$write->closeCursor();

		$find = $bdd->prepare('SELECT * FROM jpeg WHERE id = ?');
		$find->execute(array(htmlentities($_POST['photo_id'])));
		$data = $find->fetch();
		$find->closeCursor();

		if ($_SESSION['user'] != $data['login'])
		{
			$find = $bdd->prepare('SELECT * FROM user WHERE login = ?');
			$find->execute(array($data['login']));
			$data = $find->fetch();
			$find->closeCursor();


			$to      = $data['mail'];
			$subject = 'new comment';
			$message = 'you have a new commen on a picture on camagru';
			$headers = 'From: aratinau@student.42.fr' . "\r\n" .
					    'Reply-To: aratinau@student.42.fr' . "\r\n" .
					    'X-Mailer: PHP/' . phpversion();
			mail($to, $subject, $message, $headers);
		}
	}
}