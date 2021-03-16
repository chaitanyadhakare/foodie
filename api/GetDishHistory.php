<?php
    $dish_arr = array(); 
    function getDishName($dish_id, $conn){
        $dish_name;
        $sql = "SELECT dish_name FROM dishes WHERE dish_id = ".$dish_id;
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        if(mysqli_num_rows($res) != 0) {
            $row = mysqli_fetch_assoc($res);
            $dish_name = $row["dish_name"];       
        }
        return $dish_name;
    }

    function getAllDishes($dish_id, $rating ,$conn){
        global $dish_arr;
        $dish_item = array(
            "dish_name" => getDishName($dish_id, $conn),
            "rating" => $rating
        );
        array_push($dish_arr, $dish_item);
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
                
            $user_id= mysqli_real_escape_string($conn, $_POST["user_id"]);
            $sql = "SELECT * FROM user_dish_history WHERE f_user_id = ". $user_id;
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_execute($stmt);
            $res = mysqli_stmt_get_result($stmt);
            if(mysqli_num_rows($res) != 0) {
                while($row = mysqli_fetch_assoc($res)){
                    getAllDishes($row["f_dish_id"],$row["rating"], $conn);
                }
                $obj->statusCode = 200;
                $obj->statusMessage = "success";
                $obj->userId = $user_id;
                $obj->dishes = $dish_arr;
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