<?php

include_once('classes/get_class.php');

include('classes/jwt_token.php');


header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");



if($_SERVER["REQUEST_METHOD"] == "GET") {
    
    $param = array();
    parse_str($_SERVER['QUERY_STRING'], $param);
    

    $prioType = array_values($param)[0];

    $internId = array_values($param)[1];

    


    $connect = new Connection;
    $dataObj = new stdClass();
    $dataObj->students = [];


   

    // first check if prios exists in compInternPrio
    if($prioType === "internships"){
        $prios = $connect->fetchData("SELECT * FROM compInternPrio WHERE internID = $internId");
        
        if($prios["priorities"] === null){
            $priorities = mysqli_query($connect->makeCon(), "SELECT s.name, s.studentNo FROM `internpriorities` i 
            INNER JOIN student s on s.studentNo = i.studentNo
            WHERE i.priorityOne = $internId OR i.priorityTwo = $internId OR i.priorityThree = $internId");
            
            while($row = mysqli_fetch_assoc($priorities)){
                array_push($dataObj->students, $row);
                
            }
            echo json_encode($dataObj);    
        } else {
            $dataObj->students = json_decode($prios["priorities"]);
            echo json_encode($dataObj);
        }

        
        
        
        //********** WRITE THIS: same as above but for projects **********/
    } else{
        $prios = $connect->fetchData("SELECT * FROM compProjectPrio WHERE projectID = $internId");
        
        if($prios["priorities"] === null){
            $priorities = mysqli_query($connect->makeCon(), "SELECT g.groupNo, s.name FROM `projectpriorities` i 
            INNER JOIN projectgroups g on g.groupNo = i.groupNo 
            INNER JOIN student s on g.leader = s.studentNo
            WHERE i.priorityOne = $internId OR i.priorityTwo = $internId OR i.priorityThree = $internId");
            
            while($row = mysqli_fetch_assoc($priorities)){
                array_push($dataObj->students, $row);
                
            }
            echo json_encode($dataObj);    
        } else {
            $dataObj->students = json_decode($prios["priorities"]);
            echo json_encode($dataObj);
        }
    }


    
    
    
    
    


} elseif($_SERVER["REQUEST_METHOD"] == "POST"){
    $param = array();
    parse_str($_SERVER['QUERY_STRING'], $param);
    

    $prioType = array_values($param)[0];

    $internId = array_values($param)[1];

    


    $connect = new Connection;
    $dataObj = new stdClass();
    $dataObj->students = [];


    $data = json_decode(file_get_contents('php://input'), true)["data"];



    if($prioType === "internships"){
        
        $prios = $connect->fetchData("SELECT * FROM compInternPrio WHERE internID = $internId");
        
        $stringified = json_encode($data);

        if($prios["priorities"] === null){
            
            
            $connect->postData("INSERT INTO compInternPrio (internID, priorities) VALUES ($internId, ('$stringified'))");
            echo json_encode(true);          
            
        } else {
        
            $connect->postData("UPDATE compInternPrio SET priorities = ('$stringified') WHERE internID = $internId");

            echo json_encode(true);
        }
   
       
        

        
        
        //********** WRITE THIS: same as above but for projects **********/
    } else{
        $prios = $connect->fetchData("SELECT * FROM compProjectPrio WHERE projectID = $internId");
        
        $stringified = json_encode($data);

        if($prios["priorities"] === null){
            
            
            $connect->postData("INSERT INTO compProjectPrio (projectID, priorities) VALUES ($internId, ('$stringified'))");
            echo json_encode(true);          
            
        } else {
        
            $connect->postData("UPDATE compProjectPrio SET priorities = ('$stringified') WHERE projectID = $internId");

            echo json_encode(true);
        }
    }

}

?>