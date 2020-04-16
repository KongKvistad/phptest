<?php

include_once('classes/get_class.php');

include('classes/jwt_token.php');


header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");



// generate token and write to db - issue token to student
if($_SERVER["REQUEST_METHOD"] == "GET") {
    
    $param = urldecode($_SERVER['QUERY_STRING']);
   
    
    $connect = new Connection;
    $query = "INSERT INTO student (`name`, `email`, `studyProgramme`) VALUES ('testy', '$param', 'BWU')";
    $returnedRow = $connect->postData($query);
    
    print_r($returnedRow);

    // $token = genToken($returnedRow);
    // sendToken($token, $returnedRow);
   
}



?>