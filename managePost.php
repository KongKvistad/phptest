<?php

include_once('classes/get_class.php');

include('classes/jwt_token.php');


header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");



if($_SERVER["REQUEST_METHOD"] == "POST") {
    
    
    $connect = new Connection;

    $param = array();
    parse_str($_SERVER['QUERY_STRING'], $param);
    
    $mode = array_values($param)[0];

    $data = json_decode(file_get_contents('php://input'), true)["data"];

    $type = $data["radioVal"] === "internships" ? "internship" : "projects";

    $idType = $data["radioVal"] === "internships" ? "internID" : "projectID";
    
    $id = $data["id"];

    if ($mode === "Approve"){
        $connect->postData("UPDATE $type SET status = 'Approved' WHERE $idType = $id");
        echo json_encode(true);
    } else {

    }

}

?>