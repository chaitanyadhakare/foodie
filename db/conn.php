<?php

    error_reporting(0);
    ini_set('display_errors', '1');

    $conn = mysqli_connect("localhost","root","","foodiedb","3308");
    if(!$conn)
        echo "Failed to connect to the database";

    function verify($key) {
       // if($key === "2pdLunSKzyeqcAiBbEaUx9Xu4Br9AnGyKWKmiBdVrwb7VA2V52SZKxUBDZmvJL2D") {
            return true;
        //}
        //return false;
    }

?>