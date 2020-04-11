<?php

include_once('classes/get_class.php');


header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");



// generate token and write to db - issue token to student
if($_SERVER["REQUEST_METHOD"] == "GET") {
    
    $param = urldecode($_SERVER['QUERY_STRING']);
   
    // $token = genToken($param);
    // sendToken($token);
    $connect = new Connection;
    $query = "SELECT * from timeline";
    $returnedRow = $connect->fetchtData($query);
   
   
}



?>