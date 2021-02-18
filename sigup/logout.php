<?php

session_start();
$level = $_SESSION['FlowerShop']['level'];
unset($_SESSION['FlowerShop']);

if($level == 'admin') {
	header("Location: ../index.php?ref=login");
} else {
	header("Location: ../index.php");
}