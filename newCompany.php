<?php

include_once('classes/get_class.php');


echo <<<_END
<form method="POST" action="newCompany.php">
<label for="cname">Company name:</label><br>
<input type="text" id="cname" name="cname" required><br>
<label for="location">Location:</label><br>
<input type="text" id="location" name="location" placeholder="Where are you located?" required><br>
<label for="desc">Description:</label><br>
<input type="text" id="desc" name="desc" placeholder="Short description of the company" required><br>
<label for="contactName">Contact person:</label><br>
<input type="text" id="contactName" name="contactName" required><br>
<label for="phone">Contact phone number:</label><br>
<input type="tel" id="phone" name="phone" placeholder="123 45 678" required><br>
<label for="email">Contact email:</label><br>
<input type="email" id="email" name="email" required><br>
<input type="submit" value="Submit">
</form>
_END;

if($_SERVER["REQUEST_METHOD"] == "POST") {

    $connect = new Connection;
    $name = $_POST['cname'];
    $location = $_POST['location'];
    $description = $_POST['desc'];
    $contactName = $_POST['contactName'];
    $contactPhone = $_POST['phone'];
    $contactEmail = $_POST['email'];
    $query = "INSERT INTO company('name','location','description','contactName','contactPhone','contactEmail')
            VALUES($name, $location, $description, $contactName, $contactPhone, $contactEmail)";
   
   $newrow = $connect->postData($query);
   print_r($newrow);
}

?>
