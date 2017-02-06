<?php

session_start();
include_once('config/database.php');

if (isset($_SESSION['verif']) && isset($_SESSION['login']))
{
	$verif = $bdd->prepare('SELECT count(id) as is_count_ok FROM user WHERE login = ? AND token = ?');
	$verif->execute(array(htmlentities($_SESSION['login']), htmlentities($_SESSION['verif'])));
	$data = $verif->fetch();
	$verif->closeCursor();
	if ($data['is_count_ok'] == 1)
	{
		$update = $bdd->prepare('UPDATE user SET valide = ? WHERE login = ?');
		$update->execute(array(1, htmlentities($_SESSION['login'])));
		$update->execute();
		$update->closeCursor();
		$_SESSION['user'] = htmlentities($_SESSION['login']);
		$_SESSION['status'] = 'Votre compte est valide';
	}
	unset($_SESSION['verif']);
	unset($_SESSION['login']);
}

if (!isset($_SESSION['user']))
{
	$_SESSION['status'] = 'Merci de bien vouloir vous connecter ou vous inscrire';
	header('Location: login.php');
}

// verif si le compte est valide
$find = $bdd->prepare('SELECT valide FROM user WHERE login = ?');
$find->execute(array(htmlentities($_SESSION['user'])));
$data = $find->fetch();
$find->closeCursor();
$compte_valide = $data['valide'];
if ($compte_valide == 0)
{
	$_SESSION['status'] = 'Merci de bien vouloir valide votre compte en validant votre mail';
}

?><!doctype html>
<html class="no-js" lang="en">
	<head>
		<meta charset="utf-8" />
		<title></title>
		<link rel="stylesheet" href="style/style.css" />
		<script src="js/redondant.js"></script>
		<script type="text/javascript">

			document.addEventListener('DOMContentLoaded', function ()
			{

				/*********************************************************** no_webcam */
				var fileToUpload_bool = false;
				var fileToUpload = document.getElementById('fileToUpload');
				fileToUpload.onchange = function () {
					var file = document.getElementById('fileToUpload').files[0];
					if (file) {
						if (file.type == 'image/jpeg')
						{
							var reader = new FileReader();
					        reader.onload = function (e) {
					        	var no_webcam = document.getElementById('no_webcam');
								no_webcam.src = e.target.result;
								no_webcam.style.width = '100%';
								no_webcam.style.position = 'static';

								canvas.width = no_webcam.width;
								canvas.height = no_webcam.height;
								fileToUpload_bool = true;
								videoElement.parentElement.removeChild(video);
					        }
					        reader.readAsDataURL(file);
					    }
					    else
					    {
					    	alert('seulement jpeg svp');
					    }
					}
				}
				/*********************************************************** no_webcam */
				var id = 0;
				var cliposition = function (id) {
					this.id = id;
					this.png = "";
					this.top = 0;
					this.left = 0;
				};
				var array_png_objet = new Array();

				/*********************************************************** xhr */
				function uploadComplete(evt) {
					/* This event is raised when the server send back a reponse */
					//var img_ret = document.getElementById("img_ret");
					//img_ret.src = evt.target.responseText + '?rand=' + Math.random();
					//console.log(evt.target.responseText);
				}

				function uploadFailed(evt) {
					alert('There was an error attempting to upload file.');
				}

				function uploadCanceled(evt) {
					alert('The upload has been canceled by the user or the browser dropped the connection');
				}
				/*********************************************************** xhr */

				var convertCanvasToImage = document.getElementById("canvas"),
					context = canvas.getContext("2d"),
					video = document.getElementById("video"),
					videoObj = { "video": true },
					errBack = function(error) {
						// console.log("Video capture error: ", error.code);
					};

				var video = document.querySelector("#videoElement");

				navigator.getUserMedia = navigator.getUserMedia || navigator.webkitGetUserMedia || navigator.mozGetUserMedia || navigator.msGetUserMedia || navigator.oGetUserMedia;

				if (navigator.getUserMedia) {
				    navigator.getUserMedia({video: true}, handleVideo, videoError);
				}

				function handleVideo(stream) {
				    video.src = window.URL.createObjectURL(stream);
				}

				function videoError(e) {
				    // do something
				}

				/*********************************************************** drag */
				var cliparts = document.getElementsByClassName('clipart');
				var selected = null, // Object of the element to be moved
				    x_pos = 0, y_pos = 0, // Stores x & y coordinates of the mouse pointer
				    x_elem = 0, y_elem = 0; // Stores top, left values (edge) of the element

				// Will be called when user starts dragging an element
				function _drag_init(elem) {
				    // Store the object of the element which needs to be moved
				    selected = elem;
				    x_elem = x_pos - selected.offsetLeft;
				    y_elem = y_pos - selected.offsetTop;
				}

				// Will be called when user dragging an element
				function _move_elem(e) {
				    x_pos = document.all ? window.event.clientX : e.pageX;
				    y_pos = document.all ? window.event.clientY : e.pageY;
				    if (selected !== null) {
				        selected.style.left = (x_pos - x_elem) + 'px';
				        selected.style.top = (y_pos - y_elem) + 'px';
						// on cherche l'objet dans array_png_objet
						// console.log(selected.id);
						for (index = 0; index < array_png_objet.length; ++index) {
							if (selected.id == array_png_objet[index].id)
							{
								array_png_objet[index].left = selected.style.left;
								array_png_objet[index].top = selected.style.top;
							}
						}
				    }
				}

				// Destroy the object when we are done
				function _destroy() {
				    selected = null;
				}

				document.onmousemove = _move_elem;
				document.onmouseup = _destroy;

				var i = 0;
				var workplan = document.getElementById('workplan');
				while (i < cliparts.length)
				{
					cliparts[i].onclick = function() {
						if (submit.disabled == true)
							submit.disabled = false;
						id++;
						var clone = this.cloneNode(true);
						clone.id = id;

						var splitted = clone.src.split('/');
						splitted = splitted[splitted.length - 2] + '/' + splitted[splitted.length - 1];
						var tmp = new cliposition(id);
						tmp.png = splitted;
						array_png_objet.push(tmp);
						clone.onmousedown = function () {
						    _drag_init(this);
						    return false;
						};
						clone.onmouseup = function () {
							// console.log(parseInt(clone.style.top));
						};
						workplan.appendChild(clone);
					};
					i++;
				}
				/*********************************************************** end drag */

				var getGallery = function () {
					var gallery = document.getElementById('gallery');
					var request = new XMLHttpRequest();
					request.open('GET', 'gallery_xhr.php', true);
					request.onload = function() {
					if (request.status >= 200 && request.status < 400) {
							gallery.innerHTML = request.response;
							delete_img();
						}
					};
					request.onerror = function() {
						gallery.innerHTML = 'error - please reload';
					};
					request.send();
				}

				var delete_img = function () {
					var img = document.getElementById('gallery').getElementsByTagName('img');
					// console.log(img.length);
					for (var i = 0; i < img.length; i++) {
						img[i].onclick = function () {
							if (window.confirm('Supprimer ?')) {
								var fd = new FormData();
								var clean_name_img = this.src.split('/');
								clean_name_img = parseInt(clean_name_img[clean_name_img.length - 1]);
								fd.append('name_img', clean_name_img);
								var xhr = new XMLHttpRequest();
								xhr.open('POST', 'delete_img.php');
								xhr.send(fd);
								xhr.onload = function () {
								    if (xhr.readyState === xhr.DONE) {
								        if (xhr.status === 200) {
								            // console.log(xhr.response);
								        }
								    }
								};
								this.remove();
							}
						}
					}
				}

				var submit_clicked = document.getElementById('submit');
				var jsontifer_form = document.getElementById('jsontifer');
				submit_clicked.onclick = function(e) {
					e.preventDefault();
					if (fileToUpload_bool)
						context.drawImage(no_webcam, 0, 0, 640, 480);
					else
						context.drawImage(video, 0, 0, 640, 480);
					var bits = document.getElementById('bits');
					var image = new Image();
					image.src = canvas.toDataURL("image/jpeg");
					bits.value = image.src;
					jsontifer_form.value = JSON.stringify(array_png_objet);

					/*********** xhr post */
					var form = document.getElementById('form');
					var fd = new FormData(form);
					var xhr = new XMLHttpRequest();
					xhr.addEventListener('load', uploadComplete, false);
					xhr.addEventListener('error', uploadFailed, false);
					xhr.addEventListener('abort', uploadCanceled, false);
					xhr.open('POST', 'upload.php');
					xhr.send(fd);
					/*********** xhr end post */
					getGallery();
				};
				getGallery();

				var navigation = document.getElementById('navigation');

				function scrollDownUp(goback) {
				var intervalScroll = setInterval(function () {
					if (goback == 0) {
						if (navigation.scrollTop < 50) {
							navigation.scrollTop += 2;
						} else {
							clearInterval(intervalScroll);
							scrollDownUp(1);
						}
					} else {
						if (navigation.scrollTop >= 0) {
							navigation.scrollTop -= 2;
						} else {
							clearInterval(intervalScroll);
						}
					}
					if (navigation.scrollTop == 0 && goback == 1) {
						clearInterval(intervalScroll);
					}
				}, 40);
				}
				scrollDownUp(0)

			}, false ); // end function DOMContentLoaded
		</script>
	</head>
	<body>

		<div id="container">

				<div id="header">
					<h1>camagru</h1>
					<h2> <?php echo $_SESSION['user']; ?> - <a href="gallery.php">gallery</a> - <a href="my_likes.php">my likes</a>
					<a style="float:right;" href="logout.php">logout</a> </h2>
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
					if ($compte_valide == 1) { ?>

					<div id="content">

						<input type="file" name="fileToUpload" id="fileToUpload">

						<div id="workplan">
							<img id="no_webcam">
							<video autoplay="true" id="videoElement"></video>
						</div>

						<canvas id="canvas" width="640" height="480" style="display:none;"></canvas>
						<img id="img_ret">

						<form action="upload.php" method="post" id="form">
							<input type="hidden" name="bits" id="bits">
							<input type="hidden" name="jsontifer" id="jsontifer">
							<input type="submit" id="submit" value="Snap Photo" disabled>
						</form>

						<div id="gallery">

						</div>


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
						<li><img class="clipart" src="png/21.png"></li>
						<li><img class="clipart" src="png/22.png"></li>
					</ul>
				</div>
						<?php } ?>

				<div id="footer"><p>camagru</p></div>

		</div>

	</body>
</html>
