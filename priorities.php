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
        "SELECT i.title, i.internID as id, 
        CASE 
            WHEN i.internID = p.priorityOne THEN 1
            WHEN i.internID = p.priorityTwo THEN 2
            WHEN i.internID = p.priorityThree THEN 3
            ELSE 'The quantity is under 30'
        END AS rank
        FROM `internpriorities` p
        
                INNER JOIN internship i on i.internID = p.priorityOne OR i.internID = p.priorityTwo OR i.internID = p.priorityThree
                WHERE p.studentNo = $userNo
                
                ORDER BY RANK"
    );

    $dataObj->internships = fillEmpty($internPrio);
    

    // only fetch group prios if gorup exists
    $hasGroup = $connect->fetchData("SELECT groupNo FROM `projectgroups` WHERE leader = $userNo or member1 = $userNo or member2 = $userNo")["groupNo"];
    if($hasGroup){
        $dataObj->groupNo = $hasGroup;


        $projPrio = $connect->fetchPrio(
            "SELECT i.title, i.projectID as id, 
            CASE 
                WHEN i.projectID = p.priorityOne THEN 1
                WHEN i.projectID = p.priorityTwo THEN 2
                WHEN i.projectID = p.priorityThree THEN 3
                ELSE 'The quantity is under 30'
            END AS rank
            FROM `projectpriorities` p
            
                    INNER JOIN projects i on i.projectID = p.priorityOne OR i.projectID = p.priorityTwo OR i.projectID = p.priorityThree
                    WHERE p.groupNo = $hasGroup
                    
                    ORDER BY RANK"
        );


        $dataObj->projects = fillEmpty($projPrio);
    } else {
        $dataObj->projects = "you don't have a group yet";
    }



    
    
    
    
    
    
    
    
    
    echo json_encode($dataObj);
   
}



?>