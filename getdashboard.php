<?php

include_once('classes/get_class.php');

include('classes/jwt_token.php');


header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");


// need to query for:
    // -enddate for internships and projects
    // -associated coordinator for student if student
    // -events
    // priorities if student

if($_SERVER["REQUEST_METHOD"] == "GET") {
    

    $param = array();
    parse_str($_SERVER['QUERY_STRING'], $param);
    
    $userType = array_keys($param)[0];

    $userNo = array_values($param)[0];

    
    $connect = new Connection;
    
   
        echo $connect->dashboard($userType, $userNo);
    

    
      
    
    
    

   
}



?>