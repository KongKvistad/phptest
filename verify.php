<?php



header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");



if($_SERVER["REQUEST_METHOD"] == "GET") {
    
    $param = urldecode($_SERVER['QUERY_STRING']);
    
    $queries = array();
    parse_str($_SERVER['QUERY_STRING'], $queries);
    echo $queries["token"];
   
}



?>