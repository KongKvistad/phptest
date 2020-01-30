<?php


//js
// fetch('http://192.168.64.3/webproject/index.php?a')
//   .then((response) => {
//     return response.json();
//   })
//   .then((myJson) => {
//     console.log(myJson);
//   });

include_once('classes/get_class.php');

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");




if($_SERVER["REQUEST_METHOD"] == "GET") {
    $connect = new Connection;
    $query = "SELECT * FROM companies";
    $connect->fetchData($query);

}


?>