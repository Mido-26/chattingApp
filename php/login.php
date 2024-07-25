<?php
session_start();

if (isset($_POST['username']) && isset($_POST['pass'])) {
    $username = $_POST['username'];
    $pass = $_POST['pass']; // Raw password input

    include_once('config.php');

    // Prepare and execute the query to fetch user details
    $sql = 'SELECT * FROM users WHERE username = ?';
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            // Debugging: Output stored hash and provided password
            error_log("Stored hash: " . $user['pass']);
            error_log("Provided password: " . $pass);

            // Verify the password using password_verify
            $isPasswordCorrect = password_verify($pass, $user['pass']);
            error_log("Password verify result: " . ($isPasswordCorrect ? 'true' : 'false'));
            // var_dump($isPasswordCorrect);

            if ($isPasswordCorrect) {
                error_log("User details: " . print_r($user, true));

                $_SESSION['id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['avatar'] = $user['avatar'];

                // Check user status
                $query = 'SELECT user_id FROM user_satus WHERE user_id = ?';
                if ($stmt = $conn->prepare($query)) {
                    $stmt->bind_param('i', $user['id']);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    $id = $user['id'];

                    if ($result->num_rows > 0) {
                        $query = "UPDATE user_satus SET status = NOW() WHERE user_id = ?";
                    } else {
                        $query = "INSERT INTO user_satus (user_id, status) VALUES (?, NOW())";
                    }

                    if ($stmt = $conn->prepare($query)) {
                        $stmt->bind_param('i', $user['id']);
                        $stmt->execute();

                        if ($stmt->affected_rows > 0) {
                            echo json_encode(['status' => 'success', 'message' => 'Login successfully']);
                            exit();
                        } else {
                            echo json_encode(['status' => 'error', 'message' => 'Failed to update status']);
                            exit();
                        }
                    } else {
                        echo json_encode(['status' => 'error', 'message' => 'Database error']);
                        exit();
                    }
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Database error']);
                    exit();
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Wrong credentials']);
                exit();
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Wrong credentials']);
            exit();
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Database error']);
        exit();
    }
} else {
    header('Location: ../index.html');
    exit();
}
?>
