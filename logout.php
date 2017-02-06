<?php

session_start();
session_unset();

$_SESSION['status'] = 'Vous etes bien deconnecte';
header('Location: login.php');
