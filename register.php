<?php

session_start();
include_once('config/database.php');

if (isset($_POST['mail']) && isset($_POST['login']) && isset($_POST['password']))
{
	if (!empty($_POST['mail']) && !empty($_POST['login']) && !empty($_POST['password']))
	{
		// on verifie si le login est dispo
		// on verifie si le mail est dispo
		$verif = $bdd->prepare('SELECT count(id) as verif FROM user WHERE login = ? OR mail = ?');
		$verif->execute(array(htmlentities($_POST['login']), htmlentities($_POST['mail'])));
		$data_verif = $verif->fetch();
		$verif->closeCursor();

		if ($data_verif['verif'] != 0) // erreur
		{
			$_SESSION['status'] = 'Erreur : mail ou login deja utilise';
			header('Location: login.php');
		}
		else
		{
			$write = $bdd->prepare('INSERT INTO user(login, password, mail, token, valide) VALUES(:login, :password, :mail, :token, :valide)');
			$token = base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);

			$link_to_active = "http://$_SERVER[HTTP_HOST]";
			$page_array = array();
			$page_array = explode('/', $_SERVER['PHP_SELF']);
			$i = 1;
			while ($i < count($page_array) - 1) {
				$link_to_active .= '/' . $page_array[$i];
				$i++;
			}
			$link_to_active .= '/valide.php';

			$to      = htmlentities($_POST['mail']);
			$subject = 'welcome to camagru';
			$message = 'Please click the follow link to activate your mail'."\n".$link_to_active.'?verif=' . $token . '&amp;login=' . htmlentities($_POST['login']);
			$headers = 'From: aratinau@student.42.fr' . "\r\n" .
					    'Reply-To: aratinau@student.42.fr' . "\r\n" .
					    'X-Mailer: PHP/' . phpversion();

			if (mail($to, $subject, $message, $headers))
			{
			    $write->execute(array('login' => htmlentities($_POST['login']),
			    	'password' => htmlentities(hash('whirlpool', hash('whirlpool', $_POST['password']))),
			    	'mail' => htmlentities($_POST['mail']),
			    	'token' => $token,
			    	'valide' => 0 ));

		    	$write->closeCursor();
				$_SESSION['status'] = 'Inscription ok, validez votre mail';
				header('Location: login.php');
			}
			else
			{
				$_SESSION['status'] = 'Erreur lors de la creation du compte';
				header('Location: login.php');
			}
		}
	}
	else
	{
		$_SESSION['status'] = 'Erreur lors de la creation du compte';
		header('Location: login.php');
	}
}
header('Location: login.php');
