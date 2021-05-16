<?php
    function getDietTypeName($conn, $diet_id){
        $diet_type;
        $sql = "SELECT diet_type FROM diet WHERE diet_id = ".$diet_id;
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        if(mysqli_num_rows($res) != 0) {
            $row = mysqli_fetch_assoc($res);
            $diet_type = $row["diet_type"];       
        }
        return $diet_type;
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
                $obj->preference = getDietTypeName($conn, $row["f_diet_id"]);
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