<?php


// needed for AWS LAMBA CRONJOBS

include_once('classes/get_class.php');


header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");



if($_SERVER["REQUEST_METHOD"] == "GET") {
    
    $param = urldecode($_SERVER['QUERY_STRING']);
   
    $connect = new Connection;
    $query = "SELECT * FROM timeline";
    $res = $connect->fetchData($query);
    echo json_encode($res);
    
   
}
    


?>