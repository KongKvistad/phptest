<?php
class Connection {
    private $dbServername;
    private $dbUsername;
    private $dbPassword;
    private $dbName;
    private $conChk;

    public function __construct()
    {
        $this->dbServername = "localhost"; #make localhost if deployed to aws database /  13.48.129.131 if testing locally with aws
        $this->dbUsername = "root"; #webproject if aws database
        $this->dbPassword = "rootymcroot"; #rootymcroot if aws database. 
        $this->dbName = "webprosjekt2";
    }

    

    public function makeCon()
    {
        $conn = mysqli_connect($this->dbServername, $this->dbUsername, $this->dbPassword, $this->dbName);
        
        // Check connection
        if (!$conn) {

            die("Connection failed: " . mysqli_connect_error());
        
        } else {
            
            return $conn;
            
        }
        
    }
    public function readAdminToken($token){
        
    }

    public function fetchData($query) {
        $result = mysqli_query($this->makeCon(), $query);
        $row = mysqli_fetch_row($result);
            echo json_encode($row);
            mysqli_close($this->makeCon());
    }

    public function postData($query){
        if (mysqli_query($this->makeCon(), $query)){    
            return $this->getLast();
            
        }
    }

    public function getLast(){
        $result = mysqli_query($this->makeCon(), "SELECT * FROM students WHERE s_id = (SELECT MAX(s_id) FROM students);");
        $row = mysqli_fetch_assoc($result);
        mysqli_close($this->makeCon());    
        return $row;
           
    }
}   

?>