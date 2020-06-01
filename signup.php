<?php

include_once('classes/get_class.php');

include('classes/jwt_token.php');


header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept; charset=UTF-8");



if($_SERVER["REQUEST_METHOD"] == "POST") {
    


    $connect = new Connection;
    $dataObj = new stdClass();
   
    $data = json_decode(file_get_contents('php://input'), true);
    
    

    $poc = utf8_decode($data["poc"]);
    $phone = utf8_decode($data["phone"]);
    $email = utf8_decode($data["email"]);
    $pass = utf8_decode($data["pass"]);
    $pass_enc = password_hash($pass, PASSWORD_BCRYPT);
    $name = utf8_decode($data["name"]);
    $loc = utf8_decode($data["location"]);
    $desc = utf8_decode($data["desc"]);

    //first check if email or name is taken
    $check = $connect->fetchData("SELECT contactEmail as email, name FROM company WHERE contactEmail = '$email' OR name = '$name'");

    if($check["email"] === $email){
        echo json_encode("email is taken");
    } elseif ($check["name"] === $name){
        echo json_encode("name is taken!");
    } else {
        $match = $connect->postData("INSERT INTO company (name, status, location, description, contactName, contactPhone, contactEmail, password) 
        VALUES ('$name', 'Active', '$loc', '$desc', '$poc', '$phone', '$email', '$pass_enc')");
        
        if($match){
            $row = $connect->fetchData("SELECT name, status, contactName, contactPhone, contactEmail FROM company WHERE name LIKE '$name'");

            if($row){
                $token = genToken($row);
                echo json_encode(["token" => $token]);
            } else {
                
                echo json_encode("couldnt find the addition");
            }
            
        }
    }

    
        
    
    

}

?>