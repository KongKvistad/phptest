<?php

include_once('classes/get_class.php');

include('classes/jwt_token.php');


header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");



if($_SERVER["REQUEST_METHOD"] == "GET") {
    
    $param = array();
    parse_str($_SERVER['QUERY_STRING'], $param);
    

    $prioType = array_values($param)[0];

    $company = array_values($param)[1];

    $postId = array_values($param)[2];


    $connect = new Connection;
    $dataObj = new stdClass();
    $dataObj->students = [];


    $data = json_decode(file_get_contents('php://input'), true)["data"];
   

    // first check if prios exists in compInternPrio
    if($prioType === "internships"){
        $prios = $connect->fetchData("SELECT * FROM compInternPrio WHERE companyName = '$company'");
        
        if($prios["companyName"] === null){
            $connect->postData("INSERT INTO compInternPrio (companyName) VALUES ('$company')");
        }
        //company has no values - generate them and send them to compInternprio
       
        $priorities = mysqli_query($connect->makeCon(), "SELECT s.name, s.studentNo FROM `internpriorities` i 
        INNER JOIN student s on s.studentNo = i.studentNo
        WHERE i.priorityOne = $postId OR i.priorityTwo = $postId OR i.priorityThree = $postId");
        
        while($row = mysqli_fetch_assoc($priorities)){
            array_push($dataObj->students, $row);
            
        }

        $stringified = json_encode($dataObj);

        $connect->postData("UPDATE compInternPrio SET priorities = ('$stringified') WHERE companyName = '$company'");


        echo json_encode($dataObj);

       
        
        
        
        //********** WRITE THIS: same as above but for projects **********/
    } else{
        echo json_encode("asd");
    }


    
    
    
    
    


}

?>