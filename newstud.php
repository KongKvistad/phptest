<?php

include_once('classes/get_class.php');

include('classes/jwt_token.php');


header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");



// generate token and write to db - issue token to student
if($_SERVER["REQUEST_METHOD"] == "GET") {
    
    $param = urldecode($_SERVER['QUERY_STRING']);
   
    // $token = genToken($param);
    // sendToken($token);
    $connect = new Connection;
    $query = "INSERT INTO student (`email`, `name`, `programme`) VALUES ('$param', 'testy', 'BWU')";
    $returnedRow = $connect->postData($query);
    
    print_r($param);

    $token = genToken($returnedRow);
    sendToken($token, $returnedRow);
   
}



?>