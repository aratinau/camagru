<?php

session_start();
include_once('config/database.php');

if (isset($_POST['login']) and isset($_POST['password']))
{
	if (!empty($_POST['login']) and !empty($_POST['password']))
	{
		$password_hash = htmlentities(hash('whirlpool', hash('whirlpool', $_POST['password'])));

		$find = $bdd->prepare('SELECT count(id) as count_user FROM user WHERE login = ? AND password = ?');
		$find->execute(array(htmlentities($_POST['login']), $password_hash));
		$data = $find->fetch();
		$find->closeCursor();

		if ($data['count_user'] == 1)
		{
		    $_SESSION['user'] = htmlentities($_POST['login']);
		    header('Location: index.php');
		}

		$verif = $bdd->prepare('SELECT count(id) as verif FROM user WHERE login');
		$verif->execute(array(htmlentities($_POST['login'])));
		$data_verif = $verif->fetch();
		$verif->closeCursor();
		if ($data_verif['verif'] == 1) // erreur
		{
			$_SESSION['status'] = 'Erreur : mauvais login ou mot de passe';
			header('Location: login.php');
		}
	}
	else
	{
		$_SESSION['status'] = 'Erreur lors de l\'identification';
	}
	$_SESSION['status'] = 'Erreur lors de l\'identification';
}

?><!doctype html>
<html class="no-js" lang="en">
	<head>
		<meta charset="utf-8" />
		<title></title>
		<link rel="stylesheet" href="style/style.css" />
		<script src="js/redondant.js"></script>
		<style type="text/css">
		</style>
	</head>
	<body>

		<div id="container">

				<div id="header">
					<h1>camagru</h1>
					<h2><a href="gallery.php">gallery</a></h2>

				<div id="wrapper">

					<?php
					if (isset($_SESSION['status']))
					{
						echo '<div class="message">' . $_SESSION['status'] . '</div>';
						unset($_SESSION['status']);
					}
					?>

					<div id="content">

						<h1>Login</h1>
						<form action="login.php" method="post">
							login<input type="text" name="login">
							password<input type="password" name="password">
							<input type="submit"><a href="forget.php">password forget</a>
						</form>

						<h1>Register</h1>
						<form action="register.php" method="post">
							login<input type="text" name="login">
							password<input type="password" name="password">
							mail<input type="mail" name="mail">
							<input type="submit">
						</form>

					</div>
				</div>

				<div id="navigation">
					<ul>
						<li><img class="clipart" src="png/1.png"></li>
						<li><img class="clipart" src="png/2.png"></li>
						<li><img class="clipart" src="png/3.png"></li>
						<li><img class="clipart" src="png/4.png"></li>
						<li><img class="clipart" src="png/5.png"></li>
						<li><img class="clipart" src="png/6.png"></li>
						<li><img class="clipart" src="png/7.png"></li>
						<li><img class="clipart" src="png/8.png"></li>
						<li><img class="clipart" src="png/9.png"></li>
						<li><img class="clipart" src="png/10.png"></li>
						<li><img class="clipart" src="png/12.png"></li>
						<li><img class="clipart" src="png/14.png"></li>
						<li><img class="clipart" src="png/15.png"></li>
						<li><img class="clipart" src="png/17.png"></li>
						<li><img class="clipart" src="png/18.png"></li>
						<li><img class="clipart" src="png/19.png"></li>
						<li><img class="clipart" src="png/20.png"></li>
					</ul>
				</div>

				<div id="footer"><p>Footer</p></div>

		</div>

	</body>
</html>
