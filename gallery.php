<?php

session_start();
include_once('config/database.php');

$count = $bdd->prepare('SELECT count(id) as count_jpeg FROM jpeg');
$count->execute();
$data = $count->fetch();
$count->closeCursor();
$total = $data['count_jpeg'];
$imagesParPage = 6;
$nombreDePages = ceil($total / $imagesParPage);

if(isset($_GET['page']))
{
    $pageActuelle = intval($_GET['page']);

    if($pageActuelle > $nombreDePages)
    {
		$pageActuelle = $nombreDePages;
    }
}
else
{
	$pageActuelle = 1;
}
$premiereEntree = ($pageActuelle - 1) * $imagesParPage;

$find = $bdd->prepare('SELECT * FROM jpeg ORDER BY id DESC LIMIT '.$premiereEntree.', '.$imagesParPage.'');
$find->execute(array());
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
		<script src="js/redondant.js"></script>
		<script type="text/javascript">
			document.addEventListener('DOMContentLoaded', function ()
			{

				var form = document.getElementsByTagName('form');
				for (var i = 0; i < form.length; i++) {
					form[i].onsubmit = function (e) {
						e.preventDefault();
						var fd = new FormData(this);
						// console.log(this.photo_id.value);
						var xhr = new XMLHttpRequest();
						xhr.open('POST', 'add_comment.php');
						xhr.send(fd);
						var id_photo = this.photo_id.value;
						xhr.onload = function () {
							if (xhr.readyState === xhr.DONE) {
							    if (xhr.status === 200) {
							        // console.log(xhr.response);
							        // recup les comments
									var request = new XMLHttpRequest();
									request.open('GET', 'get_comments.php?id_photo=' + id_photo, true);
									request.onload = function() {
									if (request.status >= 200 && request.status < 400) {
											document.getElementById(id_photo).innerHTML = request.response;
										}
									};
									request.onerror = function() {
										alert('erreur get comments');
									};
									request.send();
							    }
							}
						};
						this.new_comment.value = '';
					};
				}

				function add_comment(id_photo, elem)
				{
					var request = new XMLHttpRequest();
					request.open('GET', 'get_comments.php?id_photo=' + id_photo, true);
					request.onload = function() {
						if (request.status >= 200 && request.status < 400) {
							elem.innerHTML = request.response;
						}
					};
					request.onerror = function() {
						gallery.innerHTML = 'error loading coments';
					};
					request.send();
				}

				var comments_photo = document.getElementsByClassName('comments_photo');
				for (var i = 0; i < comments_photo.length; i++) {
					var id_photo = parseInt(comments_photo[i].id);
					add_comment(id_photo, comments_photo[i]);
				}

				function get_like_status(id_photo, elem) {
					var request = new XMLHttpRequest();
					request.open('GET', 'get_like.php?id_photo=' + id_photo, true);
					request.onload = function() {
						if (request.status >= 200 && request.status < 400) {
							if (request.response == 1)
							{
								elem.innerHTML = '♥';
							}
							else
							{
								elem.innerHTML = '♡';
							}
						}
					};
					request.onerror = function() {

					};
					request.send();
				}

				var button_like = document.getElementsByClassName('button_like');
				for (var i = 0; i < button_like.length; i++) {

					get_like_status(button_like[i].dataset.idButton, button_like[i]);

					button_like[i].onclick = function (e) {
						e.preventDefault();
						var id_photo = this.dataset.idButton;
						var this_button = this;
						var request = new XMLHttpRequest();
						request.open('GET', 'liked.php?id_photo=' + id_photo, true);
						request.onload = function() {
							if (request.status >= 200 && request.status < 400) {
								if (request.response == 1)
								{
									this_button.innerHTML = '♥';
								}
								else
								{
									this_button.innerHTML = '♡';
								}
							}
						};
						request.onerror = function() {

						};
						request.send();
					}
				}

			}, false );
		</script>
	</head>
	<body>

		<div id="header">
			<h1>camagru</h1>
			<h2>
				<?php if(isset($_SESSION['user']) && $_SESSION['user']) { ?>
					<?php echo $_SESSION['user']; ?> - <a href="index.php">camera</a> - <a href="gallery.php">gallery</a> - <a href="my_likes.php">my likes</a>
					<a style="float:right;" href="logout.php">logout</a> </h2>
				<?php } else { ?>
					<a href="login.php">login or register</a>
				<?php } ?>
		</div>
		<?php

			echo '<div id="container">';

			for($i = 1; $i <= $nombreDePages; $i++)
			{
			    if($i == $pageActuelle)
			    {
			    	echo ' [ '.$i.' ] ';
			    }
			    else
			    {
			        echo ' <a href="gallery.php?page='.$i.'">'.$i.'</a> ';
			    }
			}

			foreach ($data as $row)
			{
				echo '<div class="cadre_photo">';
				echo '<img src="uploads/'.$row["name_timestamp"].'.jpeg">';

				if (isset($_SESSION['user'])) {
				?>

				<!-- up vote -->
				<button class="button_like"	data-id-button="<?php echo $row['id'] ?>"></button>

				<!-- add new comments -->
				<form>
					<textarea name="new_comment" style="width: 100%;"></textarea>
					<input type="hidden" name="user" value="<?php echo $_SESSION['user']; ?>">
					<input type="hidden" name="photo_id" value="<?php echo $row['id'] ?>">
					<input type="submit" style="width: 100%;" value="Comment">
				</form>
				<?php } ?>

				<!-- get commetnts -->
				<h6>Comments</h6>
				<div class="comments_photo" id="<?php echo $row['id'] ?>"></div>
				<?php
				echo '</div>';
			}
			echo '</div>';

		?>
	</body>
</html>
