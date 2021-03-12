<?php

    $obj = new stdClass();
    if($_SERVER['REQUEST_METHOD'] === 'POST') {
        require_once('../db/conn.php');
       // include_once("../../config.php");

        $headers = apache_request_headers();
        $Token = $headers['Token'];

        if(verify($Token)) {
            $params = json_decode(file_get_contents("php://input"), true);
            if($params != null)
                $_POST = $params;
            $user_mob_no = mysqli_real_escape_string($conn, $_POST["user_mob_no"]);
            $user_username = mysqli_real_escape_string($conn, $_POST["user_username"]);
            $user_name = mysqli_real_escape_string($conn, $_POST["user_name"]);         
            try {
                $sql = "INSERT INTO users(
                    user_mob_no, user_username, user_name
                )
                VALUES(?,?,?)";
                if($stmt = mysqli_prepare($conn,$sql)) {
                    mysqli_stmt_bind_param($stmt, "iss", $user_mob_no, $user_username, $user_name);
                    if(mysqli_stmt_execute($stmt)) {
                        $obj->statusCode = 200;
                        $obj->statusMessage = "success";
                        $obj->userId = mysqli_insert_id($conn);
                        $obj->userName = $user_username;
                    }
                    else {
                        $obj->statusCode = 300;
                        $obj->statusMessage = "Unable to execute the MYSQL statement: ".mysqli_stmt_error($stmt);
                    }
                }
            }
            catch(Exception $e) {
                $obj->statusCode = 300;
                $obj->statusMessage = $e->getMessage();
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