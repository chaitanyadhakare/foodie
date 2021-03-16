<?php
$obj = new stdClass();
function setDishHistory($user_id,$dish,$conn){
    global $obj;
    $sql = "INSERT INTO user_dish_history(
        f_user_id, f_dish_id, rating, timestamp
    )
    VALUES(?,?,?,now())";

    if($stmt = mysqli_prepare($conn,$sql)) {
       $dish_id = getDishId($conn,$dish["dish_name"]);
       mysqli_stmt_bind_param($stmt, "iii", $user_id, $dish_id, $dish["rating"]);
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

function getDishId($conn,$dish_name){
    $dish_id;
    $sql = "SELECT dish_id FROM dishes WHERE dish_name = '".$dish_name."'";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    if(mysqli_num_rows($res) != 0) {
        $row = mysqli_fetch_assoc($res);
        $dish_id = $row["dish_id"];       
    }
    return $dish_id;
}


    
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
            $dishes_arr = $params["dishes"];       
            try {
                foreach ($dishes_arr as $item) {  
                    setDishHistory($user_id,$item,$conn);
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