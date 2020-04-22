<?php

include_once('classes/get_class.php');

include('classes/jwt_token.php');


header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

echo "Test";


if($_SERVER["REQUEST_METHOD"] == "POST") {
    
    echo "Test";
    
    // du trenger denne for å snakke med databasen. benytt så enten eksisterende metoder eller skriv
    // en ny
    $connect = new Connection;
    

   
}



?>