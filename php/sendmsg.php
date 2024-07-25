<?php 
session_start();
if (isset($_SESSION["id"])) {

    $recipientId = intval($_POST['id']);
    $message = htmlspecialchars(trim($_POST['message']));
    $senderId = $_SESSION['id'];

    // Validate inputs
    if (empty($message) || $recipientId <= 0) {
        echo json_encode(array('status' => 'error', 'msg' => 'Invalid input'));
        exit();
    }

    // Include database configuration
    include_once('config.php');

    // Prepare the SQL statement
    $sql = 'INSERT INTO message (`from`, `to`, `message`) VALUES (?, ?, ?)';
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        // Bind parameters
        $stmt->bind_param('iis', $senderId, $recipientId, $message);

        // Execute the statement
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo json_encode(array('status' => 'success', 'msg' => 'Message sent'));
        } else {
            echo json_encode(array('status' => 'error', 'msg' => 'Cannot send the message. Try again'));
        }

        // Close the statement
        $stmt->close();
    } else {
        echo json_encode(array('status' => 'error', 'msg' => 'Database error'));
    }

    // Close the database connection
    $conn->close();
} else {
    echo json_encode(array('status' => 'error', 'msg' => 'You don\'t have access'));
}
exit();
?>
