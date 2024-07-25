<?php 
    session_start();
    if (isset($_POST['id'])){
        $id = $_POST['id'];
        

        include_once('config.php');
        $sql = "select status from user_satus where user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if ($user) {
                $last_active = strtotime($user["status"]);
                $current_time = time();
                $online_time = 300;
                if (($current_time - $last_active) <= $online_time) {
                    echo json_encode(['status' => 'success', 'status' => 'online']);
                     exit();
                }else{
                    echo json_encode(['status' => 'success', 'status' => 'offline']);
                    exit();
                }
            }
            
        }else{
            echo json_encode(['status'=> 'error','message'=> 'user not found']);
            exit();
        }
    }else{
        header('Location: ../index.html');
        exit();
    }
?>