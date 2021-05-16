<?php
    function getStateName($conn, $state_id){
        $state_name;
        $sql = "SELECT state_name FROM states WHERE state_id = ".$state_id;
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        if(mysqli_num_rows($res) != 0) {
            $row = mysqli_fetch_assoc($res);
            $state_name = $row["state_name"];       
        }
        return $state_name;
    }


    $obj = new stdClass();
    if($_SERVER['REQUEST_METHOD'] === 'POST') {
        require_once('../db/conn.php');
        $headers = apache_request_headers();
        $Token = $headers['Token'];

        if(verify($Token)) {
            $params = json_decode(file_get_contents("php://input"), true);
            if($params != null)
                $_POST = $params;
            $user_id = mysqli_real_escape_string($conn, $_POST["user_id"]);
            $sql = "SELECT * from users where user_id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, 'i', $user_id);
            mysqli_stmt_execute($stmt);
            $res = mysqli_stmt_get_result($stmt);
            if(mysqli_num_rows($res) != 0) {
                $row = mysqli_fetch_assoc($res);
                $obj->statusCode = 200;
                $obj->statusMessage = "success";
                $obj->state = getStateName($conn, $row["f_state_id"]);
            }
            else {
                $obj->statusCode = 200;
                $obj->statusMessage = "success";
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