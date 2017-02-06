<?php

session_start();
include_once('config/database.php');

if (!isset($_SESSION['user']))
{
	header('Location: login.php');
}


$find = $bdd->prepare('SELECT * FROM liked WHERE login = ?');
$find->execute(array(
	$_SESSION['user'],
));
$data = $find->fetchAll();
$find->closeCursor();

?><!doctype html>
<html class="no-js" lang="en">
	<head>
		<meta charset="utf-8" />
		<title></title>
		<style type="text/css">

		/* apply a natural box layout model to all elements, but allowing components to change */
		html {
		  box-sizing: border-box;
		}
		*, *:before, *:after {
		  box-sizing: inherit;
		}

		#container {
			-webkit-column-count: 3;
			-moz-column-count: 3;
			column-count: 3;
		}
		
		#container img {
			width: 100%;
		}

		.cadre_photo {
			margin-bottom: 35px;
			-webkit-column-break-inside: avoid;
				      page-break-inside: avoid;
				           break-inside: avoid;
		}

		.button_like {

		}

		a {
			color: #FA63F0;
			text-decoration: none;
		}

		a:hover {
			text-decoration: underline;
		}
		
		@media screen and (max-width: 777px)
		{

			#container {
				-webkit-column-count: 1;
				-moz-column-count: 1;
				column-count: 1;
			}
			#container img {
				width: 100%;
			}
		}

		</style>
	</head>
	<body>

		<div id="header">
			<h1>camagru</h1>
			<h2> <?php echo $_SESSION['user']; ?> - <a href="index.php">camera</a> - <a href="gallery.php">gallery</a> - <a href="my_likes.php">my likes</a>
			<a style="float:right;" href="logout.php">logout</a> </h2>
		</div>

		<?php

		foreach ($data as $row)
		{
			$find_picture = $bdd->prepare('SELECT * FROM jpeg WHERE id = ?');
			$find_picture->execute(array(
				$row['id_photo']
			));
			$data_picure = $find_picture->fetch();
			$find_picture->closeCursor();

			echo '<img src="uploads/'.$data_picure["name_timestamp"].'.jpeg">';
		}

		?>

	</body>
</html>