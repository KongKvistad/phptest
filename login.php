<?php

include_once('classes/get_class.php');

include('classes/jwt_token.php');


header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");



if($_SERVER["REQUEST_METHOD"] == "POST") {
    


    $connect = new Connection;
    $dataObj = new stdClass();
   
    

    $data = json_decode(file_get_contents('php://input'), true);
    $type = $data["type"];
    $email = $data["email"];
    $password = $data["password"];

    function passCorrect($pass, $obj){
        return $pass === $obj["password"] ? true : false;
            
    }


    if($type === "reguser"){
        $match = $connect->fetchData("SELECT contactEmail, contactName, password, name FROM company WHERE contactEmail = '$email'");
        if($match === NULL ){
            echo json_encode("no such user!");
        } else{
            $token = genToken($match);
            echo passCorrect($password, $match) ? json_encode(["token" => $token]) : "false";
        }

    }elseif ($type === "employeeNo") {
        $match = $connect->fetchData("SELECT * FROM admin WHERE email = '$email'");
        if($match === NULL ){
            echo json_encode("no such user!");
        } else{
            $token = genToken($match);
            echo passCorrect($password, $match) ? json_encode(["token" => $token]) : "false";
            
        }
    }

    

}

?>