<?php
    $states_arr = array(); 
    
    $obj = new stdClass();
    if($_SERVER['REQUEST_METHOD'] === 'POST') {
        require_once('../db/conn.php');
        $headers = apache_request_headers();
        $Token = $headers['Token'];

        if(verify($Token)) {
            $sql = "SELECT * FROM states order by state_name";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_execute($stmt);
            $res = mysqli_stmt_get_result($stmt);
            if(mysqli_num_rows($res) != 0) {
                while($row = mysqli_fetch_assoc($res)){
                    $state_item = array(
                        "stateId" => $row["state_id"],
                        "stateName" => $row["state_name"]
                    );
                    array_push($states_arr , $state_item);
                }
                $obj->statusCode = 200;
                $obj->statusMessage = "success";
                $obj->states = $states_arr;
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