<?php

session_start();
include_once('config/database.php');

if (isset($_POST['mail']))
{
	// verif dans la base
	$verif = $bdd->prepare('SELECT count(id) as is_mail_ok FROM user WHERE mail = ?');
	$verif->execute(array(htmlentities($_POST['mail'])));
	$data = $verif->fetch();
	$verif->closeCursor();
	if ($data['is_mail_ok'] == 1)
	{
		$find = $bdd->prepare('SELECT * FROM user WHERE mail = ?');
		$find->execute(array(htmlentities($_POST['mail'])));
		$data = $find->fetch();
		$find->closeCursor();

		$actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		
		$to      = htmlentities($_POST['mail']);
		$subject = 'reset password camagru';
		$message = 'Please click the follow link to restore your password'."\n".$actual_link.'?forget=' . $data['token'] . '&amp;login=' . $data['login'];
		$headers = 'From: aratinau@student.42.fr' . "\r\n" .
				    'Reply-To: aratinau@student.42.fr' . "\r\n" .
				    'X-Mailer: PHP/' . phpversion();

		if (mail($to, $subject, $message, $headers))
		{
			$_SESSION['status'] = 'Merci de verifier vos mails';
		}
		else
		{
			$_SESSION['status'] = 'Erreur lors de la procedure de recuperation de mot de passe';
		}
	}
}
?><!doctype html>
<html class="no-js" lang="en">
	<head>
		<meta charset="utf-8" />
		<title></title>
		<link rel="stylesheet" href="style/style.css" />
	</head>
	<body>

		<div id="container">

			<div id="header">
				<h1>camagru</h1>
				<h2> <a href="login.php">login or register</a> </h2>
			</div>

			<div id="wrapper">

				<?php
				if (isset($_SESSION['status']))
				{
					echo '<div class="message">' . $_SESSION['status'] . '</div>';
					unset($_SESSION['status']);
				}
				?>

				<?php

				if (isset($_GET['forget']) && isset($_GET['login']))
				{
					$verif = $bdd->prepare('SELECT count(id) as is_data_ok FROM user WHERE token = ? AND login = ?');
					$verif->execute(array(
						htmlentities($_GET['forget']),
						htmlentities($_GET['login'])
					));
					$data = $verif->fetch();
					$verif->closeCursor();
					if ($data['is_data_ok'] == 1)
					{
						$find = $bdd->prepare('SELECT * FROM user WHERE token = ? AND login = ?');
						$find->execute(array(
							htmlentities($_GET['forget']),
							htmlentities($_GET['login'])
						));
						$data = $find->fetch();
						$find->closeCursor();
						?>
						
							<form action="forget_process.php" method="post">
								Veuillez entrer votre nouveau mot de passe<input type="password" name="new_password">
								<input type="hidden" name="login" value="<?php echo $data['login'] ?>">
								<input type="hidden" name="token" value="<?php echo $data['token'] ?>">
								<input type="submit">
							</form>
						
						<?php
					}
				}

				?>

				<?php if (!isset($_GET['forget']) && !isset($_GET['login'])) { ?>
				<form action="forget.php" method="post">
					Veuillez entrer votre mail<input type="text" name="mail">
					<input type="submit">
				</form>
				<?php } ?>

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