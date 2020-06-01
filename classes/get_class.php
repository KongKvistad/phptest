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
                #echo "sucess";
                return true;
            } else {
                return false;
            }
        
        mysqli_close($this->makeCon());
        
    }

    
    public function dashboard($userType, $userNo){
        $dataObj = new stdClass();
        $dataObj->timeline = new stdClass();
        $dataObj->timeline->internships = new stdClass();
        $dataObj->timeline->projects = new stdClass();
        
        
        
       

        
        //fetch enddates
        $dateRes = mysqli_query($this->makeCon(), "SELECT UNIX_TIMESTAMP(internships) as internships, UNIX_TIMESTAMP(projects) as projects  FROM `endDates`");
        $endDate = mysqli_fetch_assoc($dateRes); 
        $dataObj->timeline->internships->endDate = $endDate["internships"];
        $dataObj->timeline->projects->endDate = $endDate["projects"];
        
        $dataObj->timeline->internships->events = [];
        $dataObj->timeline->projects->events = [];

        

        //fetch events
        $eventRes = mysqli_query($this->makeCon(), "SELECT eventID, title, place, category, type, UNIX_TIMESTAMP(time) as time from timeline");
        while ($row = mysqli_fetch_assoc($eventRes)) {
            if($row["type"] === "Internship"){
                array_push($dataObj->timeline->internships->events, $row);  
            }
            if($row["type"] === "Bachelor"){
                array_push($dataObj->timeline->projects->events, $row);  
            }
    
        }
        

        if($userType === "studentNo"){
            //fetch coordinator for internships
            $coordRes = mysqli_query($this->makeCon(), "SELECT name, email FROM admin LIMIT 1");
            $coordinator = mysqli_fetch_assoc($coordRes);
            $dataObj->timeline->internships->coordinator = $coordinator;

            //fetch poc
            $pocRes = mysqli_query($this->makeCon(), "SELECT c.contactEmail from company c
            inner join internship i on i.companyName = c.name
            WHERE i.internID = (SELECT internshipID from student where studentNo = $userNo)");
            $poc = mysqli_fetch_assoc($pocRes);

            if($poc["contactEmail"] !== null){
                $dataObj->timeline->internships->poc = $poc["contactEmail"];
            } else {
                $dataObj->timeline->internships->poc = "you have not been assigned an internship yet";
            }
            
            $mentorRes = mysqli_query($this->makeCon(), "SELECT m.email, m.name from mentor m inner join projects p on m.mentorID = p.mentorID WHERE p.groupNo = (SELECT g.groupNo from projectgroups g where g.leader = '$userNo' OR g.member1 = $userNo OR g.member2 = $userNo)");
            $mentor = mysqli_fetch_assoc($mentorRes);
             if($mentor["email"] !== null){
                $dataObj->timeline->projects->mentor = $mentor;
            } else {
                $dataObj->timeline->projects->mentor = "you have not been assigned a Mentor";
            }
            


            
            
            $dataObj->timeline->projects->groups = [];
            $dataObj->timeline->projects->isGroup = false;
            
            
            
            // get group members
            $groupRes = mysqli_query($this->makeCon(),
            "SELECT s.studentNo as id, s.email from student s\n"
            . "INNER JOIN projectgroups p ON p.leader =s.studentNo OR p.member1 = s.studentNo OR p.member2 = s.studentNo\n"
            . "WHERE p.groupNo =\n"
            . "\n"
            . "(SELECT groupNo FROM `projectgroups` WHERE leader = $userNo or member1 = $userNo or member2 = $userNo)\n"
            ."ORDER BY CASE WHEN p.leader = s.studentNo THEN 0 ELSE 1 END");
            while ($row = mysqli_fetch_assoc($groupRes)) {
                array_push($dataObj->timeline->projects->groups, $row);
            }
            // if no group exists, serve users own email
            if(count($dataObj->timeline->projects->groups) == 0 ){
                $res = $this->fetchData("SELECT studentNo as id, email from student WHERE studentNo = $userNo");
                array_push($dataObj->timeline->projects->groups, $res);
                
            }else {
                $dataObj->timeline->projects->isGroup = true;
            }
        
        } elseif ($userType === "employeeNo"){

            
            //aggregate statistics for dashboard
            $pitches = $this->fetchData("SELECT COUNT(title) as count FROM `internship` WHERE status = 'Not Approved'");
            $dataObj->timeline->internships->pitches = $pitches["count"];
            

            $countStud = $this->fetchData("SELECT COUNT(studentNo) as count FROM `internpriorities` WHERE priorityOne IS NULL OR priorityTwo IS NULL OR priorityThree IS NULL");
            $dataObj->timeline->internships->studApply = $countStud["count"];
           

            $countComp= $this->fetchData("SELECT COUNT(*) FROM (SELECT p.internID FROM `compInternPrio` p RIGHT JOIN internship i on i.internID = p.internID WHERE p.internID IS NULL) as counter");
            $dataObj->timeline->internships->compApply = $countComp["COUNT(*)"];
            

            $pitchesProj = $this->fetchData("SELECT COUNT(title) as count FROM `projects` WHERE status = 'Not Approved'");
            $dataObj->timeline->projects->pitches = $pitchesProj["count"];

            $countStudProj = $this->fetchData("SELECT COUNT(groupNo) as count FROM `projectpriorities` WHERE priorityOne IS NULL OR priorityTwo IS NULL OR priorityThree IS NULL");
            $dataObj->timeline->projects->studApply = $countStudProj["count"];

            $countCompProj= $this->fetchData("SELECT COUNT(*) FROM (SELECT p.projectID FROM `compProjectPrio` p RIGHT JOIN projects i on i.projectID = p.projectID WHERE p.projectID IS NULL) as counter");
             $dataObj->timeline->projects->compApply = $countCompProj["COUNT(*)"];
        }

        $myJSON = json_encode($dataObj);

        return $myJSON;
        
        mysqli_close($this->makeCon());
    }

    
    public function getMp($userType, $userNo){
        
        $dataObj = new stdClass();
        $dataObj->entries = new stdClass();

        $dataObj->entries->internships = [];
        
        
        $internships = mysqli_query($this->makeCon(), "SELECT internID as id, author, companyName, title, startDate, endDate, tags, description, status, visibility FROM internship WHERE status = 'Approved'");
        while ($row = mysqli_fetch_assoc($internships)) {
            array_push($dataObj->entries->internships, $row);  
    
        }

        $dataObj->entries->projects = [];
        $projArr = [];
        $projects = mysqli_query($this->makeCon(), "SELECT projectID as id, author, companyName, title, startDate, endDate, tags, description, status, visibility FROM projects WHERE status = 'Approved'");
        while ($row1 = mysqli_fetch_assoc($projects)) {
            array_push($dataObj->entries->projects, $row1);  
            
    
        }     
        
        // admins have access to extra tabs
        if($userType === "employeeNo"){
            $dataObj->entries->pitched = new stdClass();
            $dataObj->entries->pitched->internships = [];
            $dataObj->entries->pitched->projects = [];
            $dataObj->entries->students = [];
            $dataObj->entries->companies = new stdClass();
            $dataObj->entries->companies->projects = [];
            $dataObj->entries->companies->internships = [];
            


            $pitched = mysqli_query($this->makeCon(), "SELECT internID as id, author, companyName, title, startDate, endDate, tags, description, status FROM internship WHERE status = 'Not Approved'");
            while ($row1 = mysqli_fetch_assoc($pitched)) {
                array_push($dataObj->entries->pitched->internships, $row1);  
                
        
            }
            $pitchedproj = mysqli_query($this->makeCon(), "SELECT projectID as id, author, companyName, title, startDate, endDate, tags, description, status FROM projects WHERE status = 'Not Approved'");
            while ($row2 = mysqli_fetch_assoc($pitchedproj)) {
                array_push($dataObj->entries->pitched->projects, $row2); 
            }


            $students = mysqli_query($this->makeCon(), "SELECT studentNo as id, studyProgramme, name from student");
            while ($row = mysqli_fetch_assoc($students)) {
                $studno = $row["id"];

                $internprios = $this->fetchPrio("SELECT i.title, i.internID as id FROM `internpriorities` p
                INNER JOIN internship i on i.internID = p.priorityOne OR i.internID = p.priorityTwo OR i.internID = p.priorityThree
                WHERE p.studentNo = $studno");


               
                $row["priorities"] = ["internships" => $internprios];
                
                array_push($dataObj->entries->students, $row);  
            }

            //company overview
            $companyProj = mysqli_query($this->makeCon(), "SELECT pr.projectID as id, p.priorities, pr.companyName, pr.title FROM `compProjectPrio` p
            right join projects pr on pr.projectID = p.projectID");
            while ($row = mysqli_fetch_assoc($companyProj)) {
                
                array_push($dataObj->entries->companies->projects, $row);  
            }
            
            $companyInt = mysqli_query($this->makeCon(), "SELECT i.internID as id, c.priorities, i.companyName, i.title FROM `compInternPrio` c
            right join internship i on i.internID = c.internID");
            while ($row = mysqli_fetch_assoc($companyInt)) {
               
                array_push($dataObj->entries->companies->internships, $row);  
            }


           

            // special handling for when admin filters  by projects @ the student tab;
            $dataObj->studProjPrio = [];

            
            
            $studProjectPrio = mysqli_query($this->makeCon(), "SELECT p.groupNo as id, p.leader as leaderNo, s.name as leaderName,
             pp.priorityOne, pp.priorityTwo, pp.priorityThree FROM
            `projectgroups` p INNER JOIN `student` s ON
             s.studentNo = p.leader
            LEFT JOIN `projectpriorities` pp ON pp.groupNo = p.groupNo");
            
            
            while($row = mysqli_fetch_assoc($studProjectPrio)){
                
                
                
                $row["priorities"] = [["id" => $row["priorityOne"]], ["id" => $row["priorityTwo"]], ["id" => $row["priorityThree"]]];
                unset($row["priorityOne"]);
                unset($row["priorityTwo"]);
                unset($row["priorityThree"]);
                array_push($dataObj->studProjPrio, $row);

            }

            

        
            
        } elseif ($userType === "name") {

            $dataObj->entries->my_posts = new stdClass();
            $dataObj->entries->my_posts->internships = [];
            $dataObj->entries->my_posts->projects = [];
            $dataObj->entries->my_posts->intActive = false;
            $dataObj->entries->my_posts->projActive = false;


            $my_int = mysqli_query($this->makeCon(), "SELECT internID as id, author, companyName, title, startDate, endDate, tags, description, status FROM internship WHERE companyName = '$userNo'");
            while($row = mysqli_fetch_assoc($my_int)){
                array_push($dataObj->entries->my_posts->internships, $row);
            }
            

            $my_proj = mysqli_query($this->makeCon(), "SELECT projectID as id, author, companyName, title, startDate, endDate, tags, description, status FROM projects WHERE companyName = '$userNo'");
            while($row = mysqli_fetch_assoc($my_proj)){
                array_push($dataObj->entries->my_posts->projects, $row);
            }

            $intActive = $this->fetchData("SELECT * FROM makeApply WHERE type = 'internships'")["isActive"];

            $dataObj->entries->my_posts->intActive = $intActive;

            $projActive = $this->fetchData("SELECT * FROM makeApply WHERE type = 'projects'")["isActive"];

            $dataObj->entries->my_posts->projActive = $projActive;
            
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