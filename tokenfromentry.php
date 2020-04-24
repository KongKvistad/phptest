<?php

include_once('classes/get_class.php');

include('classes/jwt_token.php');

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// use this to generate tokens from existing entries in the database

if($_SERVER["REQUEST_METHOD"] == "GET") {
    
    $param = array();
    parse_str($_SERVER['QUERY_STRING'], $param);
    
    $userType = array_keys($param)[0];

    $id = array_values($param)[0];


    $connect = new Connection;

    $query = "";

    if($userType === "admin"){
        $query = "SELECT * FROM $userType WHERE employeeNo = $id";
        $returnedRow = $connect->fetchData($query);
    }
    if($userType === "student"){
        $query = "SELECT * FROM $userType WHERE studentNo = $id";
        $returnedRow = $connect->fetchData($query);
    }
    if($userType === "company"){
        $query = "SELECT * FROM $userType WHERE name = '$id'";
        $returnedRow = $connect->fetchData($query);
        $returnedRow["email"] = $returnedRow["contactEmail"];
        unset($returnedRow["contactEmail"]);
    }

    
    

    $token = genToken($returnedRow);
    sendToken($token, $returnedRow);
    
    print_r($returnedRow);
   
}



?>