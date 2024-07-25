<?php 
    session_start();
    if (isset($_SESSION["id"])){

        $id = $_SESSION["id"];
        include_once("config.php");
        $sql = 'Select * from Users WHERE id != ? ORDER BY username ASC';
        $stmt= $conn->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if($result->num_rows > 0){
            $row = $result->fetch_all(MYSQLI_ASSOC);
            echo json_encode(array('users'=> $row,'status' =>'success'));
            exit();
        }else{
            echo json_encode(array('status'=> 'error','msg'=> 'failed to get users'));
            exit();
        }
    }else {
        header("Location: ../index.html");
        exit();
      }
?>