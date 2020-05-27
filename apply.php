<?php

include_once('classes/get_class.php');

include('classes/jwt_token.php');


header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept; charset=UTF-8");



if($_SERVER["REQUEST_METHOD"] == "POST") {
    

    $param = array();
    parse_str($_SERVER['QUERY_STRING'], $param);
    
    $userType = array_keys($param)[0];

    $userNo = array_values($param)[1] === "internships" ? array_values($param)[0]: array_values($param)[2];

    $category = array_values($param)[1] === "internships" ? "internpriorities" : "projectpriorities";
    
    $studOrGroup = array_values($param)[1] === "internships" ? "studentNo" : "groupNo";

    $connect = new Connection;
    //$dataObj = new stdClass();
   
    $data = json_decode(file_get_contents('php://input'), true)["data"];
    
    $prio1 = $data[0]["id"];
    $prio2 = $data[1]["id"];
    $prio3 = $data[2]["id"];
     

    
    $res = $connect->postData("UPDATE $category SET priorityOne = $prio1, priorityTwo = $prio2, priorityThree = $prio3 WHERE $studOrGroup = $userNo");
    if($res){
        echo json_encode("success");
    } else {
        echo json_encode($param);
    }
 


    
    
    

}

?>