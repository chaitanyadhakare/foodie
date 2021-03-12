<?php

    $obj = new stdClass();
    if($_SERVER['REQUEST_METHOD'] === 'POST') {
        require_once('../db/conn.php');
        $headers = apache_request_headers();
        $Token = $headers['Token'];

        if(verify($Token)) {
            $params = json_decode(file_get_contents("php://input"), true);
            if($params != null)
                $_POST = $params;
            $user_username = mysqli_real_escape_string($conn, $_POST["user_username"]);
            $sql = "SELECT * from users where user_username = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, 's', $user_username);
            mysqli_stmt_execute($stmt);
            $res = mysqli_stmt_get_result($stmt);
            if(mysqli_num_rows($res) != 0) {
                $row = mysqli_fetch_assoc($res);
                $obj->statusCode = 200;
                $obj->statusMessage = "success";
                $obj->isAvailable = false;
            }
            else {
                $obj->statusCode = 200;
                $obj->statusMessage = "success";
                $obj->isAvailable = true;
            }
        }
        else {
            $obj->statusCode = 400;
            $obj->statusMessage = "Invalid API token";
        }
    }
    else {
        $obj->statusCode = 400;
        $obj->statusMessage = "Invalid request type";
    }
    echo json_encode($obj);
?>