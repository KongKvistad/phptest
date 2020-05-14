<?php

include_once('classes/get_class.php');

include('classes/jwt_token.php');


header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");



if($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $param = array();
    parse_str($_SERVER['QUERY_STRING'], $param);
    
    $userType = array_keys($param)[0];

    $userNo = array_values($param)[0];

    $eType = array_values($param)[1];


    $connect = new Connection;
    $dataObj = new stdClass();


    $data = json_decode(file_get_contents('php://input'), true);
    $column = array_keys($data)[0];
    $email = $data[$column]["email"];

    # if user is adding members
    if($eType === "add") {
        

        
        
        #1: check that user exists
        $userExists = $connect->fetchData("SELECT studentNo FROM student WHERE email = '$email'");
        if($userExists === NULL){
            echo json_encode("user does not exist");
        } else {

            $id = $userExists["studentNo"];
            
            $hasGroup = $connect->fetchData("SELECT groupNo FROM projectgroups WHERE leader = $id or member1 = $id or member2 = $id");
            #2: make sure user isnt already part of group
            if($hasGroup !== NULL){
                echo json_encode("user already has group");
            } else {
                
                // #3: if request sender has group -> add new member, otherwise create new group with req sender as leader
                $isGroup = $connect->fetchData("SELECT groupNo from projectgroups WHERE leader = $userNo or member1 = $userNo or member2 = $userNo");

                $gId = $isGroup["groupNo"];

                if($isGroup === NULL ){
                    $connect->postData("INSERT INTO projectgroups (leader, $column) VALUES($userNo, $id)");
                    echo json_encode(true);
                    
                } else {
                    $connect->postData("UPDATE projectgroups SET $column = $id WHERE groupNo = $gId");
                    echo json_encode(true);
                }
            }
            
        }
    }

    # otherwise, the user is removing members/ disbanding group

    else{
        $isGroup = $connect->fetchData("SELECT groupNo from projectgroups WHERE leader = $userNo or member1 = $userNo or member2 = $userNo");
        $gId = $isGroup["groupNo"];

        if($eType === "destroy") {
            #1: seperate button to disband group
            $connect->postData("DELETE FROM projectgroups WHERE groupNo = $gId");
            echo json_encode(true);
             
         } else {
             #2: user is removing members
             $connect->postData("UPDATE projectgroups SET $column = NULL WHERE groupNo = $gId");
             echo json_encode(true);
         }
    }

    

    
    
    
    
    
    
    
    


}

?>