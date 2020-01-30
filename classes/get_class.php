<?php
class Connection {
    private $dbServername;
    private $dbUsername;
    private $dbPassword;
    private $dbName;
    private $conChk;

    public function __construct()
    {
        $this->dbServername = "localhost";
        $this->dbUsername = "root";
        $this->dbPassword = "rootymcroot"; #rootymcroot
        $this->dbName = "schoolproj";
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


    public function fetchData($query) {
        $result = mysqli_query($this->makeCon(), $query);
        $row = mysqli_fetch_row($result);
            echo json_encode($row);
            mysqli_close($this->makeCon());
    }
}
?>