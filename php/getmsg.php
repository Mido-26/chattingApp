<?php
session_start();
if (!isset($_SESSION['id']) && !isset($_POST['rid'])) {
	session_unset();
	session_destroy();
	header("../index.html");
	exit();
} else {
	$senderId = $_SESSION['id'];
	$receiverId = $_POST['rid'];

	include_once 'config.php';
	$query = 'SELECT id, username, avatar from users where id = ? ';
	$stmt = $conn->prepare($query);
	$stmt->bind_param('i', $receiverId);
	$stmt->execute();
	$result = $stmt->get_result();
	$row = $result->fetch_assoc();
	$stmt->close();

	$sql = 'SELECT * from message m where (m.from = ? OR m.From = ?) and (m.to = ? OR m.to = ?)';
	$stmt = $conn->prepare($sql);
	$stmt->bind_param('iiii', $senderId, $receiverId, $senderId, $receiverId);
	$stmt->execute();
	$result = $stmt->get_result();
	$msg = $result->fetch_all(MYSQLI_ASSOC);
	echo json_encode(array('status' => 'success', 'u_info' => $row, 'messages' => $msg));
	exit();
}
