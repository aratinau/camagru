<?php

session_start();
$_SESSION['install'] = 1;
include('database.php');

try {
	$sql = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
} catch (PDOException $e) {
	die('Connection failed: ' . $e->getMessage());
}

$sql->exec("CREATE DATABASE $DB_NAME");
$sql->exec("USE $DB_NAME");
if ($sql->query('CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `id_photo` int(11) NOT NULL,
  `login` text NOT NULL,
  `commentaire` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;') == false)
	echo $sql->errorInfo()[2];
if ($sql->query('CREATE TABLE `jpeg` (
  `id` int(11) NOT NULL,
  `login` text NOT NULL,
  `name_timestamp` int(11) NOT NULL,
  `count_like` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;') == false)
	echo $sql->errorInfo()[2];
if ($sql->query('CREATE TABLE `liked` (
  `id` int(11) NOT NULL,
  `id_photo` int(11) NOT NULL,
  `login` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;') == false)
	echo $sql->errorInfo()[2];
if ($sql->query('CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `login` text NOT NULL,
  `password` text NOT NULL,
  `mail` text NOT NULL,
  `token` text NOT NULL,
  `valide` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;') == false)
	echo $sql->errorInfo()[2];
if ($sql->query('ALTER TABLE `comments` ADD PRIMARY KEY (`id`);') == false)
	echo $sql->errorInfo()[2];
if ($sql->query('ALTER TABLE `jpeg` ADD PRIMARY KEY (`id`);') == false)
	echo $sql->errorInfo()[2];
if ($sql->query('ALTER TABLE `liked` ADD PRIMARY KEY (`id`);') == false)
	echo $sql->errorInfo()[2];
if ($sql->query('ALTER TABLE `user` ADD PRIMARY KEY (`id`);') == false)
	echo $sql->errorInfo()[2];

if ($sql->query('ALTER TABLE `comments`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;') == false)
	echo $sql->errorInfo()[2];
if ($sql->query('ALTER TABLE `jpeg`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;') == false)
	echo $sql->errorInfo()[2];
if ($sql->query('ALTER TABLE `liked`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;') == false)
	echo $sql->errorInfo()[2];
if ($sql->query('ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;') == false)
	echo $sql->errorInfo()[2];

if (!file_exists('../uploads'))
{
  mkdir('../uploads');
}


$_SESSION['status'] = 'Installation finie';
header('Location: ../login.php');

?>
