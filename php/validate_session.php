<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
	// Redireccion al login
	header("Location: ./login.php");
	exit();
}

?>