<?php
class Connection {
    private $dbServername;
    private $dbUsername;
    private $dbPassword;
    private $dbName;
    private $conChk;


    

    public function __construct()
    {
        $this->dbServername = "13.48.129.131"; #make localhost if deployed to aws database /  13.48.129.131 if testing locally with aws
        $this->dbUsername = "webproject"; 
        $this->dbPassword = "rootymcroot"; 
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
    
    

    public function fetchData($query) {
        $result = mysqli_query($this->makeCon(), $query);
        $row = mysqli_fetch_assoc($result);
        return $row;
            mysqli_close($this->makeCon());
    }
    
    
    public function fetchPrio($query) {
        $resArr = [];
        $result = mysqli_query($this->makeCon(), $query);
        
        while ($row = mysqli_fetch_assoc($result)) {
            array_push($resArr, $row);    
        }
        
        return $resArr;

        mysqli_close($this->makeCon());
    }

    public function postData($query) {
        
            if (mysqli_query($this->makeCon(), $query)){    
                echo "sucess";
                
            }
        
        mysqli_close($this->makeCon());
        
    }

    
    public function dashStudent($userNo){
        $dataObj = new stdClass();
        $dataObj->timeline = new stdClass();
        $dataObj->timeline->internships = new stdClass();
        $dataObj->timeline->projects = new stdClass();
        
        
        
        $mentorRes = mysqli_query($this->makeCon(), 
        "SELECT m.name, m.email, m.phoneNo FROM student s
        INNER JOIN mentor m on s.mentorID = m.mentorId
        WHERE s.studentNo = $userNo");
        $mentorData = mysqli_fetch_assoc($mentorRes);
        

        
        $dateRes = mysqli_query($this->makeCon(), "SELECT UNIX_TIMESTAMP(internships) as internships, UNIX_TIMESTAMP(projects) as projects  FROM `endDates`");
        $endDate = mysqli_fetch_assoc($dateRes);

        
        
        $dataObj->timeline->internships->endDate = $endDate["internships"];
        $dataObj->timeline->projects->endDate = $endDate["projects"];
        $dataObj->timeline->internships->mentor = $mentorData;
        
        $coordRes = mysqli_query($this->makeCon(), "SELECT name, email FROM admin LIMIT 1");
        $coordinator = mysqli_fetch_assoc($coordRes);


        $dataObj->timeline->projects->coordinator = $coordinator;
        
        
        $dataObj->timeline->internships->events = [];
        $dataObj->timeline->projects->events = [];

        


        $eventRes = mysqli_query($this->makeCon(), "SELECT eventID, title, place, category, type, UNIX_TIMESTAMP(time) as time from timeline");
        while ($row = mysqli_fetch_assoc($eventRes)) {
            if($row["type"] === "Internship"){
                array_push($dataObj->timeline->internships->events, $row);  
            }
            if($row["type"] === "Bachelor"){
                array_push($dataObj->timeline->projects->events, $row);  
            }
    
        }
        
        
        $myJSON = json_encode($dataObj);

        return $myJSON;
        
        mysqli_close($this->makeCon());
    }

    
    public function getMp($userType){
        
        $dataObj = new stdClass();
        $dataObj->entries = new stdClass();

        $dataObj->entries->internships = [];
        
        
        $internships = mysqli_query($this->makeCon(), "SELECT internID as id, author, companyName, title, startDate, endDate, tags, description, status FROM internship WHERE status = 'Approved'");
        while ($row = mysqli_fetch_assoc($internships)) {
            array_push($dataObj->entries->internships, $row);  
    
        }

        $dataObj->entries->projects = [];
        $projArr = [];
        $projects = mysqli_query($this->makeCon(), "SELECT projectID as id, author, companyName, title, startDate, endDate, tags, description, status FROM projects WHERE status = 'Approved'");
        while ($row1 = mysqli_fetch_assoc($projects)) {
            array_push($dataObj->entries->projects, $row1);  
            
    
        }     
        
        // admins have access to extra tabs
        if($userType === "employeeNo"){
            $dataObj->entries->pitched = [];
            $dataObj->entries->students = [];
            $dataObj->entries->companies = [];
            


            $pitched = mysqli_query($this->makeCon(), "SELECT internID as id, author, companyName, title, startDate, endDate, tags, description, status FROM internship WHERE status = 'Not Approved'");
            while ($row1 = mysqli_fetch_assoc($pitched)) {
                array_push($dataObj->entries->pitched, $row1);  
                
        
            }
            $students = mysqli_query($this->makeCon(), "SELECT studentNo as id, studyProgramme, name from student");
            while ($row = mysqli_fetch_assoc($students)) {
                $studno = $row["id"];

                $internprios = $this->fetchPrio("SELECT i.title, i.internID as id FROM `internpriorities` p
                INNER JOIN internship i on i.internID = p.priorityOne OR i.internID = p.priorityTwo OR i.internID = p.priorityThree
                WHERE p.studentNo = $studno");

                $projprios = $this->fetchPrio("SELECT p.title, p.projectID as id FROM `projectpriorities` j
                INNER JOIN projects p on p.projectID = j.priorityOne OR p.projectID = j.priorityTwo OR p.projectID = j.priorityThree 
                WHERE j.studentNo = $studno");

               
                $row["priorities"] = ["internships" => $internprios, "projects" => $projprios];
                array_push($dataObj->entries->students, $row);  
            }
            $companies = mysqli_query($this->makeCon(), "SELECT name from company");
            while ($row = mysqli_fetch_assoc($companies)) {
                $name = $row["name"];
                

                $projprios = $this->fetchPrio("SELECT s.name, s.studentNo as id FROM `compProjectPrio` c
                INNER JOIN student s on s.studentNo = c.priorityOne OR s.studentNo = c.priorityTwo OR s.studentNO = c.priorityThree 
                WHERE c.companyName = '$name'");

                $internprios = $this->fetchPrio("SELECT s.name, s.studentNo as id FROM `compInternPrio` c
                INNER JOIN student s on s.studentNo = c.priorityOne OR s.studentNo = c.priorityTwo OR s.studentNO = c.priorityThree 
                WHERE c.companyName = '$name'");

                $row["priorities"] = ["internships" => $internprios, "projects" => $projprios];

                array_push($dataObj->entries->companies, $row);  
            
            }

            

        }
        
        $myJSON = json_encode($dataObj);

        return $myJSON;
        
        mysqli_close($this->makeCon());
    }
    

    public function newStud($query){
        if (mysqli_query($this->makeCon(), $query)){    
            $newStud = $this->getLast();
            $studNo = $newStud["studentNo"];
            $this->postData("INSERT INTO `internpriorities` (`studentNo`, `priorityOne`, `priorityTwo`, `priorityThree`) VALUES ($studNo, NULL, NULL, NULL)");
            $this->postData("INSERT INTO `projectpriorities` (`studentNo`, `priorityOne`, `priorityTwo`, `priorityThree`) VALUES ($studNo, NULL, NULL, NULL)");
            
            return $newStud;
            
        }
    }

    public function getLast(){
        $result = mysqli_query($this->makeCon(), "SELECT * FROM student WHERE studentNo = (SELECT MAX(studentNo) FROM student);");
        $row = mysqli_fetch_assoc($result);
        mysqli_close($this->makeCon());    
        return $row;
           
    }
}   

?>