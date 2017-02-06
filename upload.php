<?php

session_start();
include_once('config/database.php');

if (!isset($_SESSION['user']))
{
    header('Location: login.php');
}

$json_decoded = json_decode($_POST['jsontifer']);

$data = explode(',', htmlentities($_POST['bits']));
$data = base64_decode($data[1]);
$img = imagecreatefromstring($data);
if ($img !== false) {
    header('Content-Type: image/jpeg');

    foreach($json_decoded as $elem)
    {
        $otr = imagecreatefrompng($elem->png);
        $otr = imagescale($otr, 100);
        imagecopy($img, $otr, $elem->left, $elem->top, 0, 0, imagesx($otr), imagesy($otr));
    }

    $date = new DateTime();
    $timestamp = $date->getTimestamp();

    $write = $bdd->prepare('INSERT INTO jpeg(login, name_timestamp, count_like) VALUES(:login, :name_timestamp, :count_like)');
    $write->execute(array(
        'login' => $_SESSION['user'],
        'name_timestamp' => $timestamp,
        'count_like' => 0
        )
    );
    $write->closeCursor();

    imagejpeg($img, 'uploads/'.$timestamp.'.jpeg', 100); // cree l'image
    imagedestroy($img);
}
else {
    echo 'An error occurred.';
}

echo "uploads/".$timestamp.".jpeg";