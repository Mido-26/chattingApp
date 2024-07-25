<?php 
	$user = "root";
	$pass = "";
	$host = "localhost";
	$name = "chat_app";

	$conn = mysqli_connect($host,$user,$pass,$name);
	if (!$conn) {
		die(json_encode(array('status' => 'error', 'message' => 'Database connection failed')));
	}
 ?>