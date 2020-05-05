<?php

include_once('classes/get_class.php');

include('classes/jwt_token.php');


header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");


// need to query for:
    // -enddate for internships and projects
    // -associated coordinator for student if student
    // -events
    // priorities if student

if($_SERVER["REQUEST_METHOD"] == "GET") {
    

    $param = array();
    parse_str($_SERVER['QUERY_STRING'], $param);
    
    $userType = array_keys($param)[0];

    $userNo = array_values($param)[0];

    

    function fillEmpty($arr){
        
        

        $interval = 3 - count($arr);
        while ($interval > 0){
            
            $emptyObj = new stdClass();
            $emptyObj->title = null;
            $emptyObj->id = $interval;
            
            array_push($arr, $emptyObj);
            $interval--;
        }

        return $arr;
    }



    $connect = new Connection;

    $dataObj = new stdClass();
    $dataObj->internships = new stdClass();
    $dataObj->projects = new stdClass();

    $internPrio = $connect->fetchPrio(
        "SELECT i.title, i.internID as id FROM `internpriorities` p
        INNER JOIN internship i on i.internID = p.priorityOne OR i.internID = p.priorityTwo OR i.internID = p.priorityThree
        WHERE p.studentNo = $userNo"
    );

    $dataObj->internships = fillEmpty($internPrio);
    

    // only fetch group prios if gorup exists
    $hasGroup = $connect->fetchData("SELECT groupNo FROM `projectgroups` WHERE leader = $userNo or member1 = $userNo or member2 = $userNo")["groupNo"];
    if($hasGroup){
        $dataObj->groupNo = $hasGroup;


        $projPrio = $connect->fetchPrio(
            "SELECT p.title, p.projectID as id FROM `projectpriorities` j
            INNER JOIN projects p on p.projectID = j.priorityOne OR p.projectID = j.priorityTwo OR p.projectID = j.priorityThree 
            WHERE j.GroupNo = $hasGroup"
        );


        $dataObj->projects = fillEmpty($projPrio);
    } else {
        $dataObj->projects = "you don't have a group yet";
    }



    
    
    
    
    
    
    
    
    
    echo json_encode($dataObj);
   
}



?>