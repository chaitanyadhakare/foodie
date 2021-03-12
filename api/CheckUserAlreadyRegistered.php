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
            $user_mob_no = mysqli_real_escape_string($conn, $_POST["user_mob_no"]);
            $sql = "SELECT * from users where user_mob_no = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, 'i', $user_mob_no);
            mysqli_stmt_execute($stmt);
            $res = mysqli_stmt_get_result($stmt);
            if(mysqli_num_rows($res) != 0) {
                $row = mysqli_fetch_assoc($res);
                $obj->statusCode = 200;
                $obj->statusMessage = "success";
                $obj->isRegistered = true; 
                $obj->userId = $row["user_id"];
                $obj->userName = $row["user_username"];
                if($row["f_diet_id"] != 1)
                    $obj->isPreferenceSet = true;
                else
                    $obj->isPreferenceSet = false;
            }
            else {
                $obj->statusCode = 200;
                $obj->statusMessage = "success";
                $obj->isRegistered = false;
                $obj->isPreferenceSet = false;
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