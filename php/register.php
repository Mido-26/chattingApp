<?php 
if (isset($_POST['username'])) {
    $username = $_POST['username'];
    $pass = $_POST['pass'];

    include_once('config.php');

    // Enable detailed error reporting
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    try {
        // Check if the username already exists
        $sql = "SELECT username FROM users WHERE username = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare statement failed: " . $conn->error);
        }
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo json_encode(['status' => 'error', 'message' => 'Username already exists']);
            exit();
        }

        // Hash the password securely
        $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);

        // Insert the new user into the database
        $query = 'INSERT INTO users (username, pass) VALUES (?, ?)';
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            throw new Exception("Prepare statement failed: " . $conn->error);
        }
        $stmt->bind_param('ss', $username, $hashed_pass);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo json_encode(['status' => 'success', 'message' => 'Registered successfully']);
            exit();
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to register user']);
            exit();
        }
    } catch (mysqli_sql_exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
        exit();
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
        exit();
    }
} else {
    header('Location: ../index.html');
    exit();
}
?>
