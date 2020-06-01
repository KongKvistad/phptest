<?php

include_once('classes/get_class.php');

include('classes/jwt_token.php');


header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");



if($_SERVER["REQUEST_METHOD"] == "GET") {
    
    $param = array();
    parse_str($_SERVER['QUERY_STRING'], $param);
    


    $connect = new Connection;
    $dataObj = new stdClass();
    $dataObj->companies = [];
    $dataObj->students = [];

    $comp = mysqli_query($connect->makeCon(), "SELECT p.priorities, i.* from internship i LEFT JOIN compInternPrio p on p.internID = i.internID WHERE i.status = 'Approved'");

    while($row = mysqli_fetch_assoc($comp)){
        $row["priorities"] = json_decode($row["priorities"]);
        $row["studSlots"] = range(0,$row["noOfStudents"] - 1);
        array_push($dataObj->companies, $row);
        
    }

    

    $students = mysqli_query($connect->makeCon(), "SELECT p.priorityOne, p.priorityTwo, p.priorityThree, s.* from student s LEFT JOIN internpriorities p on p.studentNo = s.studentNo");

    while($row = mysqli_fetch_assoc($students)){
        
        $rankArr = array();

        array_push($rankArr, $row["priorityOne"]);
        array_push($rankArr, $row["priorityTwo"]);
        array_push($rankArr, $row["priorityThree"]);

        unset($row["priorityOne"]);
        unset($row["priorityTwo"]);
        unset($row["priorityThree"]);
        
        $row["priorities"] = $rankArr;
        
        array_push($dataObj->students, $row);
        
    }


    

    echo json_encode($dataObj);


   

}

?>