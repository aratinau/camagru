<?php

session_start();
include_once('config/database.php');

if (!isset($_SESSION['user']))
{
	header('Location: login.php');
}

$find = $bdd->prepare('SELECT count(id) as count_user FROM jpeg WHERE login = ? AND name_timestamp = ?');
$find->execute(array($_SESSION['user'], (int)$_POST['name_img']));
$data = $find->fetch();
$find->closeCursor();

if ($data['count_user'] == 1) // alors on le delete
{
	$delete = $bdd->prepare('DELETE FROM jpeg WHERE name_timestamp = ?');
	$delete->execute(array((int)$_POST['name_img']));
	$delete->closeCursor();
	unlink('uploads/' . (int)$_POST['name_img'] . '.jpeg');
}