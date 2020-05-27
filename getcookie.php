<?php

include_once('classes/get_class.php');

include('classes/jwt_token.php');


header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");



if($_SERVER["REQUEST_METHOD"] == "GET") {


    $dataObj = new stdClass();
    $dataObj->testarr = [];

    
    
    setcookie("mycookie", "This cookie tastes good", time()+3600, "", "192.168.64.3");

    

    if (isset($_COOKIE["mycookie"])) {
        // success
        print_r($_COOKIE);
      } else {
          echo "fail";
      }
    

}

?>