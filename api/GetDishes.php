<?php

$dish_arr = array();  

function getAllDishes($sqlDish, $conn){
    global $dish_arr;
	$stmt = mysqli_prepare($conn, $sqlDish);
	mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    if(mysqli_num_rows($res) != 0) {
        while($row = mysqli_fetch_assoc($res)){
        	$dish_item = array(
        		"dish_id" => $row["dish_id"],
        		"dish_name" => $row["dish_name"]
        	);
            array_push($dish_arr, $dish_item);
        }
    }
}

function getDietType($user_id, $conn){
	$sql = "SELECT f_diet_id FROM users WHERE user_id= " . $user_id;
	$stmt = mysqli_prepare($conn, $sql);
	mysqli_stmt_execute($stmt);
	$res = mysqli_stmt_get_result($stmt);
	if(mysqli_num_rows($res) != 0) {
		$row = mysqli_fetch_assoc($res);
		return $row["f_diet_id"];
	} 
}

function getDishLimit($f_cat_id){
	switch ($f_cat_id) {
	  case 1:
	    return 2;
	    break;
	  case 2:
	    return 2;
	    break;
	  case 3:
	    return 4;
	    break;
	  case 3:
	    return 2;
	    break;
	  default:
	    return 2;
	}
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
            $f_diet_id = getDietType($user_id, $conn);
            $dish_limit = 2;

            $sql = "SELECT * from dish_category";
            $stmt = mysqli_prepare($conn, $sql);

            mysqli_stmt_execute($stmt);
            $res = mysqli_stmt_get_result($stmt);
            if(mysqli_num_rows($res) != 0) {
                while($row = mysqli_fetch_assoc($res)){
               		$dish_limit = getDishLimit($row["cat_id"]);
               		$sqlDish = "SELECT dish_id, dish_name FROM dishes WHERE f_cat_id = ". $row["cat_id"] ." AND f_diet_id = ". $f_diet_id ." ORDER BY RAND() LIMIT ". $dish_limit;
               		getAllDishes($sqlDish, $conn);
                }
               
               $obj->statusCode = 200;
               $obj->statusMessage = "success";
               $obj->items = $dish_arr;

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