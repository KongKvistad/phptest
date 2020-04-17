<?php

include_once('classes/get_class.php');

include('classes/jwt_token.php');


header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");




if($_SERVER["REQUEST_METHOD"] == "GET") {
    
    $param = urldecode($_SERVER['QUERY_STRING']);
   
    
    $connect = new Connection;
    $query = "INSERT INTO student (`name`, `email`, `studyProgramme`, `password`) VALUES ('testy', '$param', 'BWU', 'student')";
    $returnedRow = $connect->postData($query);
    
    print_r($returnedRow);

   
}



?>