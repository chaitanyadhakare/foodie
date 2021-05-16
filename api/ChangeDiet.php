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
            $user_id = mysqli_real_escape_string($conn, $_POST["user_id"]);
            $f_diet_id = mysqli_real_escape_string($conn, $_POST["f_diet_id"]);        
            try {
                $sql = "UPDATE users 
                        SET f_diet_id = ". $f_diet_id ."
                        WHERE user_id = ?";
                if($stmt = mysqli_prepare($conn,$sql)) {
                    mysqli_stmt_bind_param($stmt, "i", $user_id);
                    if(mysqli_stmt_execute($stmt)) {
                        $obj->statusCode = 200;
                        $obj->statusMessage = "success";
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