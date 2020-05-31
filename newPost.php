<?php

include_once('classes/get_class.php');

include('classes/jwt_token.php');


header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept; charset=UTF-8");



if($_SERVER["REQUEST_METHOD"] == "POST") {
    


    $connect = new Connection;
    //$dataObj = new stdClass();
   
    $data = json_decode(file_get_contents('php://input'), true)["data"];
    
    
    $userType = array_keys($data["user"])[0];

    $user = $data["user"][$userType];
    
    $postType = $data["postType"];

 

    $title = $data["title"];
    $author = $data["author"];
    $cName = $data["companyName"];
    $sDate = $data["startDate"];
    $eDate = $data["endDate"];
    $noStudent = $data["noofstudents"];
    $kh = $data["keephidden"] === false ? 'visible' : 'invisible';
    $desc = $data["description"];
    $tags = $data["tags"];
    
    $stringified = json_encode($desc);

    function compExists($con, $company){
        $res = $con->fetchData("SELECT * from company WHERE name = '$company'");
        return $res;
    }




    // if company submits, inject data and set status to not approved. set visibility equal to $kh
    if($userType === "name"){
        if(compExists($connect, $cName)){
            if($postType === "internship"){
                $res = $connect->postData("INSERT INTO internship (companyName, title, author, startDate, endDate, description, noOfStudents, status, tags, visibility) VALUES ('$cName', '$title', '$author', '$sDate', '$eDate', ('$stringified'), '$noStudent', 'Not Approved', '$tags', '$kh')");
                echo $res ? json_encode(true) : json_encode("something went wrong! please check all fields!");
            
            } elseif($postType === "projects"){
                $res = $connect->postData("INSERT INTO projects (companyName, title, author, startDate, endDate, description,  status, tags, visibility) VALUES ('$cName', '$title', '$author', '$sDate', '$eDate', ('$stringified'), 'Not Approved', '$tags', '$kh')");
                echo $res ? json_encode(true) : json_encode("something went wrong! please check all fields!");   
            
            } else{
                echo json_encode("please specify a post type");
            }
            
        } else{
            echo json_encode("company does not exist");
        }
    }
    elseif($userType === "employeeNo" || $userType === "studentNo"){
        if(compExists($connect, $cName)){
            
            if($postType === "internship"){
                $res = $connect->postData("INSERT INTO internship (companyName, title, author, startDate, endDate, description, noOfStudents, status, tags, visibility) VALUES ('$cName', '$title', '$author', '$sDate', '$eDate', '$desc', '$noStudent', 'Not Approved', '$tags', '$kh')");
                echo $res ? json_encode(true) : json_encode("something went wrong! please check all fields!");
            
            } elseif($postType === "projects"){
                $res = $connect->postData("INSERT INTO projects (companyName, title, author, startDate, endDate, description,  status, tags, visibility) VALUES ('$cName', '$title', '$author', '$sDate', '$eDate', '$desc', 'Not Approved', '$tags', '$kh')");
                echo $res ? json_encode(true) : json_encode("something went wrong! please check all fields!"); 
            
            } else{
                echo json_encode("please specify a post type");
            }
            
        } else{
            echo json_encode("company does not exist");
        }
        
        
        
    }


    
    
    

}

?>