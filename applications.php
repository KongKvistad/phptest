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
    $postType = $data["postType"];
    $isApplication = $data["isApplication"];

  

    $stringified = json_encode($data);


    if(!$appExist && $postType === "internships" && $isApplication){
        $res = $connect->postData("INSERT INTO intApplications (studentNo, internID, text) VALUES ($userId, $postId, ('$stringified'))");
        echo $res ? json_encode(true) : json_encode("error 1!");
       
    }
    elseif($appExist && $postType === "internships" && $isApplication === false){
        $res = $connect->postData("UPDATE internship SET description = ('$stringified')  WHERE internID = $postId");
        echo $res ? json_encode(true) : json_encode("error 2!");
    }
    elseif($appExist && $postType === "projects" && $isApplication === false){
        $res = $connect->postData("UPDATE projects SET description = ('$stringified')  WHERE projectID = $postId");
        echo $res ? json_encode(true) : json_encode("error 3!");
    }
    elseif($appExist && $postType === "internships" && $isApplication) {
        $res = $connect->postData("UPDATE intApplications SET text = ('$stringified') WHERE studentNo = $userId AND internID = $postId");
        echo $res ? json_encode(true) : json_encode("error 4!");
    }
    
    

}

if($_SERVER["REQUEST_METHOD"] == "GET") {
    $connect = new Connection;
   
    $param = array();
    parse_str($_SERVER['QUERY_STRING'], $param);
    
    $userId = array_values($param)[0];
    $postId = array_values($param)[1];
    
    $postOrApp = array_values($param)[2];
    $type = array_values($param)[3];


    // request is a post
    if($postOrApp === "true"){
        if($type === "internships"){
            $res = $connect->fetchData("SELECT * FROM internship WHERE internID = $postId");
            echo json_encode($res);
        } else {
            $res = $connect->fetchData("SELECT * FROM projects WHERE projectID = $postId");
            echo json_encode($res);
        }
        
        //otherwise
    } else {

        
        $res = $connect->fetchData("SELECT * FROM intApplications WHERE internID = $postId AND studentNo = $userId");
        echo json_encode($res);
    }
    
}

?>