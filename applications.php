<?php

include_once('classes/get_class.php');

include('classes/jwt_token.php');


header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept; charset=UTF-8");



if($_SERVER["REQUEST_METHOD"] == "POST") {
    


    $connect = new Connection;
   
    $data = json_decode(file_get_contents('php://input'), true)["data"];
    
 
    $appExist = $data["appExist"];
    $postId = $data["postId"];
    $userId = $data["userId"];

  

    $stringified = json_encode($data);


    if(!$appExist){
        $res = $connect->postData("INSERT INTO intApplications (studentNo, internID, text) VALUES ($userId, $postId, ('$stringified'))");
        echo $res ? json_encode(true) : json_encode("something went wrong! please check all fields!");
       
    } else {
        $res = $connect->postData("UPDATE intApplications SET text = ('$stringified') WHERE studentNo = $userId AND internID = $postId");
        echo $res ? json_encode(true) : json_encode("something went wrong! please check all fields!");
    }
    
    

}

if($_SERVER["REQUEST_METHOD"] == "GET") {
    $connect = new Connection;
   
    $param = array();
    parse_str($_SERVER['QUERY_STRING'], $param);
    
    $userId = array_values($param)[0];
    $postId = array_values($param)[1];
    
    $postOrApp = array_values($param)[2];

    if($postOrApp){
        $res = $connect->fetchData("SELECT * FROM internship WHERE internID = $postId");
        echo json_encode($res);
    } else {
        $res = $connect->fetchData("SELECT * FROM intApplications WHERE internID = $postId AND studentNo = $userId");
        echo json_encode($res);
    }
    
}

?>