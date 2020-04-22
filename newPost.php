<?php

include_once('classes/get_class.php');

include('classes/jwt_token.php');


//header("Access-Control-Allow-Origin: *");
//header("Content-Type: application/json; charset=UTF-8");

echo "Test";
echo "Ny testt <br><br>";

echo <<< _END

    <form method='post' action='newPost.php' enctype='multipart/form-data'>
        <label for="company"> Company name: </label>
        <input type ="text" id="company" name="company"><br>
        <label for ="title"> Title: </label>
        <input type ="text" id="title" name="title"><br>
        <label for ="author"> Author: </label>
        <input type="text" id="author" name="author"><br>
        <label for ="start-date">Start date: </label>
        <input type ="date" id="start-date" name="start-date"><br>
        <label for ="end-date">End date: </label>
        <input type ="date" id ="end-date" name="end-date"><br><br>
        <label for="description">Description: </label>
        <textarea id="description" name="description">
        </textarea><br><br>
        <label for="noofstudents">Number of students: </label>
            <input list="noofstudents" name="noofstudents">
                <datalist id="noofstudents">
                    <option value = "1">
                    <option value = "2">
                </datalist> <br><br>
        

        <input type ="radio" id="internship" name="internship" value="Interneship">        
        <label for ="internship"> Internship</label><br>
        <input type="radio" id="bachelorproject" name="bachelorproject" value="Bachelor Project">
        <label for="bachelorproject">Bachelor project</label><br><br>
        


        <label for="tags">Tags: </label>
        <input type="text" id="tags" name="tags"><br>
        <label for ="iamstudent">I am student: </label>
        <input type="checkbox" id="iamstudent" name="iamstudent" value="I am student"><br> <br>
        
        <input type='submit' value = 'Submit'>
    </form>

_END;

//print_r($_POST);

if($_SERVER["REQUEST_METHOD"] == "POST") {
    
    
    
    // du trenger denne for å snakke med databasen. benytt så enten eksisterende metoder eller skriv
    // en ny
    $connect = new Connection;

    $company = $_POST['company'];
    $title = $_POST['title'];
    $author = $_POST['author'];
    $startDate = $_POST['start-date'];
    $endDate = $_POST['end-date'];
    $description = $_POST['description'];
    $noOfstudents = $_POST['noofstudents'];

    $query = "INSERT INTO internship(`companyName`, `title`, `author`, `startDate`, `endDate`, `description`, `noOfstudents`) VALUES('$company', '$title', '$author', $startDate, $endDate, '$description', '$noOfstudents')";
    $connect->postData($query);
    
    //$connect->close();
   
}



?>