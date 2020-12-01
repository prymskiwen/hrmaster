<?php     
    require("../email.class.php");
    
    require("../db.class.php");
    function email($due_date, $period, $days_prior, $email_con, $array_text_exchanged, $email_to){
        date_default_timezone_set("Australia/Sydney");
        $today = new DateTime();
        
        if(strpos($email_con, 'birthday') === false){
            
            switch($period){
                case 0:{
                    if($days_prior == 0){
                        
                        
                        if($today->format("Y-m-d") == $due_date){
                            
                            sendEmail($email_to, $email_con, $array_text_exchanged);
                            return 1;
                        }
                    }else{
                        $date1 = date_create($due_date);
                        $diff = date_diff($today, $date1);
                        if($diff->format("%R%a") == "+". $days_prior){
                            sendEmail($email_to, $email_con, $array_text_exchanged);
                            return 1;
                        }
                    }
                    break;
                }
                default: {
                    $date1 = date_create($due_date);
                    $diff = date_diff($today, $date1);
                    if($diff->format("%R") == "-" && $period == 1){
                        sendEmail($email_to, $email_con, $array_text_exchanged);
                        return 1;
                    }else if($diff->format("%R") == "-" && $period == 3 && ($diff->format("%a") / 1) % 3 == 0){
                        sendEmail($email_to, $email_con, $array_text_exchanged);
                        return 1;
                    }
                }
            }
        }else {
            switch($period){
                case 0:{
                    
                    if($days_prior == 0){
                        $td = date_format($today, "m/d");
                        $dd = date_format(date_create($due_date), "m/d");
                        if($td === $dd){
                            
                            sendEmail($email_to, $email_con, $array_text_exchanged);
                            return 1;
                        }
                    }else{
                        $date1 = date_format(date_create($due_date), "m/d");
                        $date2 = date_format(date_add($today, date_interval_create_from_date_string($days_prior. " days")), "m/d");
                        if($date1 === $date2){
                            sendEmail($email_to, $email_con, $array_text_exchanged);
                            return 1;
                        }
                    }
                    break;
                }
                default: {
                   break;
                }
            }
        }
        
    
        return 0;
    }
    function sendEmail($email_to, $email_con, $array_text_exchanged){
        
        foreach($array_text_exchanged as $key => $value){
            $email_con = str_replace($key, $value, $email_con);
        }
        
        $array = explode("<p></p>", $email_con, 2);
        $subject = $array[0];
        $message = $array[1];
        $m= new email(); // create the mail

        $m->From("HR Master Support <support@hrmaster.com.au>");
        //$m->To("EarthShakerKing@hotmail.com");
        $m->To($email_to);

        $m->Subject($subject);

        //$message = "<a href='https://hrmaster.com.au/?#/resetpassword/$hash'>Click to reset password</a>";

        $m->Body($message);

        $m->Priority(3) ;

        $m->Send();	// send the mail
        
        echo "sent email successfully!";
    }
    function getEmployeeName($emp_id){
        $e = new db("user");
        $e->select("id = :id", false, false, array("id" => $emp_id));
        $e->getRow();
        return array("firstname" => $e->row["firstname"], "lastname" => $e->row["lastname"]);
    }
    function getCourseName($course_id){
        $e = new db("course");
        $e->select("course_id = :id", false, false, array("id" => $course_id));
        $e->getRow();
        return $e->row["course_name"];
    }
    function getSiteLocation($emp_id){
        $e = new db("user_work");
        $sql = "SELECT data.display_text FROM data INNER JOIN user_work AS empwk on empwk.site_location = data.id WHERE empwk.user_id = ". $emp_id;
        $e->select(false, false, $sql);
        $e->getRow();
        return $e->row["display_text"];
    }
    function getCompanyName($emp_id){
        $e = new db("user");
        $e->select("id = :id", false, false, array("id" => $emp_id));
        $e->getRow();
        return $e->row["companyname"];
    }

    date_default_timezone_set("Australia/Sydney");
    $var = date("H");
    $sql = "";
    switch($var){
        case "08":{
            $sql = "SELECT reminders.* FROM reminders INNER JOIN user ON user.id = reminders.employee_id WHERE reminders.alert_status = 1 AND DATE(NOW()) < reminders.alert_expiry AND user.active = 1 AND reminders.email_name = 'Birthday'";
            break;
        }
        case "09":{
            $sql = "SELECT reminders.* FROM reminders INNER JOIN user ON user.id = reminders.employee_id WHERE reminders.alert_status = 1 AND DATE(NOW()) < reminders.alert_expiry AND user.active = 1 AND reminders.email_name = 'VISA Check'";
            break;
        }
        case "10":{
            $sql = "SELECT reminders.* FROM reminders INNER JOIN user ON user.id = reminders.employee_id WHERE reminders.alert_status = 1 AND DATE(NOW()) < reminders.alert_expiry AND user.active = 1 AND reminders.email_name = 'Training Course Due'";
            break;
        }
        case "11":{
            $sql = "SELECT reminders.* FROM reminders INNER JOIN user ON user.id = reminders.employee_id WHERE reminders.alert_status = 1 AND DATE(NOW()) < reminders.alert_expiry AND user.active = 1 AND reminders.email_name = 'Safety Data Sheet Expiry'";
            break;
        }
        case "12":{
            $sql = "SELECT reminders.* FROM reminders INNER JOIN user ON user.id = reminders.employee_id WHERE reminders.alert_status = 1 AND DATE(NOW()) < reminders.alert_expiry AND user.active = 1 AND reminders.email_name = 'Schedule a Service'";
            break;
        }
        case "13":{
            $sql = "SELECT reminders.* FROM reminders INNER JOIN user ON user.id = reminders.employee_id WHERE reminders.alert_status = 1 AND DATE(NOW()) < reminders.alert_expiry AND user.active = 1 AND reminders.email_name = 'Test and Tag'";
            break;
        }
        case "14":{
            $sql = "SELECT reminders.* FROM reminders INNER JOIN user ON user.id = reminders.employee_id WHERE reminders.alert_status = 1 AND DATE(NOW()) < reminders.alert_expiry AND user.active = 1 AND reminders.email_name = 'Safe Work Procedures'";
            break;
        }
        case "15":{
            $sql = "SELECT reminders.* FROM reminders INNER JOIN user ON user.id = reminders.employee_id WHERE reminders.alert_status = 1 AND DATE(NOW()) < reminders.alert_expiry AND user.active = 1 AND reminders.email_name = 'Licence and Qualification'";
            break;
        }
        case "16":{
            $sql = "SELECT reminders.* FROM reminders INNER JOIN user ON user.id = reminders.employee_id WHERE reminders.alert_status = 1 AND DATE(NOW()) < reminders.alert_expiry AND user.active = 1 AND reminders.email_name = 'Probation Period'";
            break;
        }
        case "17":{
            $sql = "SELECT reminders.* FROM reminders INNER JOIN user ON user.id = reminders.employee_id WHERE reminders.alert_status = 1 AND DATE(NOW()) < reminders.alert_expiry AND user.active = 1 AND reminders.email_name = 'Qualification Period'";
            break;
        }
        case "18":{
            $sql = "SELECT reminders.* FROM reminders INNER JOIN user ON user.id = reminders.employee_id WHERE reminders.alert_status = 1 AND DATE(NOW()) < reminders.alert_expiry AND user.active = 1 AND reminders.email_name = 'Injury Register'";
            break;
        }
    }
    $rm_table = new db("reminders");
    $rm_table->select(false, false, $sql);
    
    $reminders = array();
    while($rm_table->getRow()){
        $row = $rm_table->row;
        
        $preferred_employee_name = getEmployeeName($row["employee_id"]);
        switch($row["email_name"]){
            case "Birthday": {
                
                $bd_table = new db("user");
                $bd_table->select("account_id = :id AND deleted = :notdel", false, false, array("id" => $row["account_id"], "notdel" => 0));
                while($bd_table->getRow()){
                    $employee_row = $bd_table->row;
                    $array_text_exchanged = array();
                    $array_text_exchanged["<\$preferredEmailUsersFirstName>"] = $preferred_employee_name["firstname"];
                    $array_text_exchanged["<\$preferredEmailUserLastname>"] = $preferred_employee_name["lastname"];
                    $array_text_exchanged["<\$employeeFirstName>"] = $employee_row["firstname"];
                    $array_text_exchanged["<\$employeeLastname>"] = $employee_row["lastname"];
                    $array_text_exchanged["<\$insertSiteLocaiton>"] = getSiteLocation($row["employee_id"]); 
                    $array_text_exchanged["<\$companyName>"] = getCompanyName($row["employee_id"]); // not clear maybe cause some errors
                    $array_text_exchanged["<\$insertDOB>"] = $employee_row["dob"];
                    email($employee_row["dob"], $row["period"], $row["days_prior"], $row["email_con"], $array_text_exchanged, $row["reminder_email"]);
                }
                break;
            }
            case "VISA Check": {
                $bd_table = new db("user");
                $bd_table->select("account_id = :id  AND deleted = :notdel", false, false, array("id" => $row["account_id"], "notdel" => 0));
                
                while($bd_table->getRow()){
                    $employee_row = $bd_table->row;
                    $array_text_exchanged = array();
                    $array_text_exchanged["<\$preferredEmailUsersFirstName>"] = $preferred_employee_name["firstname"];
                    $array_text_exchanged["<\$preferredEmailUserLastname>"] = $preferred_employee_name["lastname"];
                    $array_text_exchanged["<\$employeeFirstName>"] = $employee_row["firstname"];
                    $array_text_exchanged["<\$employeeLastname>"] = $employee_row["lastname"];
                    $array_text_exchanged["<\$insertSiteLocaiton>"] = getSiteLocation($row["employee_id"]);
                    $array_text_exchanged["<\$companyName>"] = getCompanyName($row["employee_id"]);
                    $array_text_exchanged["<\$insertdate>"] = $employee_row["visaexpiry"];
                    email($employee_row["visaexpiry"], $row["period"], $row["days_prior"], $row["email_con"], $array_text_exchanged, $row["reminder_email"]);
                }
                break;
            }
            case "Training Course Due": {
                $course_table = new db("alloc_course");
                $course_table->select(false, false, "SELECT * FROM alloc_course");
                
                while($bd_table->getRow()){
                    $course_table_row = $course_table->row;
                    $employee_name = getEmployeeName($course_table_row["employee_id"]);
                    $array_text_exchanged = array();
                    $array_text_exchanged["<\$preferredEmailUsersFirstName>"] = $preferred_employee_name["firstname"];
                    $array_text_exchanged["<\$preferredEmailUserLastname>"] = $preferred_employee_name["lastname"];
                    $array_text_exchanged["<\$employeeFirstName>"] = $employee_name["firstname"];
                    $array_text_exchanged["<\$employeeLastname>"] = $employee_name["lastname"];
                    $array_text_exchanged["<\$insertSiteLocaiton>"] = getSiteLocation($row["employee_id"]);
                    $array_text_exchanged["<\$companyName>"] = getCompanyName($row["employee_id"]);
                    $array_text_exchanged["<\$insertdate>"] = date_format(date_add(date_create($course_table_row["alloc_date"]),date_interval_create_from_date_string($course_table_row["expire_hours"]. " hours")), "Y-m-d");
                    $array_text_exchanged["<\$insertCourseName>"] = getCourseName($course_table_row["course_id"]);
                    email($array_text_exchanged["<\$insertdate>"], $row["period"], $row["days_prior"], $row["email_con"], $array_text_exchanged);
                }
                break;
            }
            case "Safety Data Sheet Expiry": {
                $table = new db("hazardous_substance");
                $table->select("account_id = :id", false, false, array("id" => $row["account_id"]));
                
                while($table->getRow()){
                    $substance_row = $table->row;
                    $supplier_name = getEmployeeName($substance_row["supplier_id"]);
                    $array_text_exchanged = array();
                    $array_text_exchanged["<\$preferredEmailUsersFirstName>"] = $preferred_employee_name["firstname"];
                    $array_text_exchanged["<\$preferredEmailUserLastname>"] = $preferred_employee_name["lastname"];
                    $array_text_exchanged["<\$insertChemicalName>"] = $substance_row["chemical_name"];
                    $array_text_exchanged["<\$insertSupplierName>"] = $supplier_name["firstname"]. " " .$supplier_name["lastname"];
                    $array_text_exchanged["<\$insertSiteLocaiton>"] = getSiteLocation($row["employee_id"]);
                    $array_text_exchanged["<\$companyName>"] = getCompanyName($row["employee_id"]);
                    $array_text_exchanged["<\$insertdate>"] = $substance_row["expiry_date"];
                    email($substance_row["expiry_date"], $row["period"], $row["days_prior"], $row["email_con"], $array_text_exchanged, $row["reminder_email"]);
                }
                break;
            }
            case "Schedule a Service": {
                $table = new db("asset_register");
                $table->select("account_id = :id", false, false, array("id" => $row["account_id"]));
                
                while($table->getRow()){
                    $asset_row = $table->row;
                    
                    $array_text_exchanged = array();
                    $array_text_exchanged["<\$preferredEmailUsersFirstName>"] = $preferred_employee_name["firstname"];
                    $array_text_exchanged["<\$preferredEmailUserLastname>"] = $preferred_employee_name["lastname"];
                    $array_text_exchanged["<\$insertPlantName>"] = $asset_row["name"];
                    $array_text_exchanged["<\$insertSerialNumber>"] = $asset_row["serial"];
                    $array_text_exchanged["<\$insertSiteLocaiton>"] = getSiteLocation($row["employee_id"]);
                    $array_text_exchanged["<\$companyName>"] = getCompanyName($row["employee_id"]);
                    $array_text_exchanged["<\$insertdate>"] = $asset_row["service_date"];
                    $array_text_exchanged["<\$insertServiceProvider>"] = $asset_row["service_provider"];
                    $array_text_exchanged["<\$phoneNumber>"] = $asset_row["service_phone_number"];
                    email($asset_row["service_date"], $row["period"], $row["days_prior"], $row["email_con"], $array_text_exchanged, $row["reminder_email"]);
                }
                break;
            }
            case "Test and Tag": {
                $table = new db("asset_register");
                $table->select("account_id = :id", false, false, array("id" => $row["account_id"]));
                
                while($table->getRow()){
                    $asset_row = $table->row;
                    
                    $array_text_exchanged = array();
                    $array_text_exchanged["<\$preferredEmailUsersFirstName>"] = $preferred_employee_name["firstname"];
                    $array_text_exchanged["<\$preferredEmailUserLastname>"] = $preferred_employee_name["lastname"];
                    $array_text_exchanged["<\$insertPlantName>"] = $asset_row["name"];
                    $array_text_exchanged["<\$insertSerialNumber>"] = $asset_row["serial"];
                    $array_text_exchanged["<\$insertSiteLocaiton>"] = getSiteLocation($row["employee_id"]);
                    $array_text_exchanged["<\$companyName>"] = getCompanyName($row["employee_id"]);
                    $array_text_exchanged["<\$insertdate>"] = $asset_row["next_test_date"];
                    
                    email($asset_row["next_test_date"], $row["period"], $row["days_prior"], $row["email_con"], $array_text_exchanged, $row["reminder_email"]);
                }
                break;
            }
            case "Safe Work Procedures": {
                break;
            }
            case "Licence and Qualification": {
                $table = new db("user_license");
                $table->select("account_id = :id", false, false, array("id" => $row["account_id"]));
                
                while($table->getRow()){
                    $license_row = $table->row;
                    
                    $array_text_exchanged = array();
                    $array_text_exchanged["<\$preferredEmailUsersFirstName>"] = $preferred_employee_name["firstname"];
                    $array_text_exchanged["<\$preferredEmailUserLastname>"] = $preferred_employee_name["lastname"];
                    $array_text_exchanged["<\$employeeFirstName>"] = $license_row["employee_firstname"];
                    $array_text_exchanged["<\$employeeLastname>"] = $license_row["employee_lastname"];
                    $array_text_exchanged["<\$insertSiteLocaiton>"] = getSiteLocation($row["employee_id"]);
                    $array_text_exchanged["<\$companyName>"] = getCompanyName($row["employee_id"]);
                    $array_text_exchanged["<\$insertdate>"] = $license_row["date_expire"];
                    $array_text_exchanged["<\$licenseQualificationName>"] = $license_row["license_name"];
                    email($license_row["license_name"], $row["period"], $row["days_prior"], $row["email_con"], $array_text_exchanged, $row["reminder_email"]);
                }
                break;
            }
            case "Probation Period": {
                $table = new db("user");
                $table->select("account_id = :id AND deleted = :notdel", false, false, array("id" => $row["account_id"], "notdel" => 0));
                
                while($table->getRow()){
                    $employee_row = $table->row;
                    $user_work = new db("user_work");
                    $user_work->select("user_id = :id", false, false, array("id" => $employee_row["id"]));
                    $user_work->getRow();
                    $user_work_row = $user_work->row;
                    
                    
                    $array_text_exchanged = array();
                    $array_text_exchanged["<\$preferredEmailUsersFirstName>"] = $preferred_employee_name["firstname"];
                    $array_text_exchanged["<\$preferredEmailUserLastname>"] = $preferred_employee_name["lastname"];
                    $array_text_exchanged["<\$employeeFirstName>"] = $employee_row["employee_firstname"];
                    $array_text_exchanged["<\$employeeLastname>"] = $employee_row["employee_lastname"];
                    $array_text_exchanged["<\$insertSiteLocaiton>"] = getSiteLocation($row["employee_id"]);
                    $array_text_exchanged["<\$companyName>"] = getCompanyName($row["employee_id"]);
                    $array_text_exchanged["<\$insertdate>"] = date_add(date_create($user_work_row["start_date"]), date_interval_create_from_date_string("90 days"));// 3 months
                    
                    email($array_text_exchanged["<\$insertdate>"], $row["period"], $row["days_prior"], $row["email_con"], $array_text_exchanged, $row["reminder_email"]);
                }
                break;
            }
            case "Qualification Period": {
                $table = new db("user");
                $table->select("account_id = :id AND deleted = :notdel", false, false, array("id" => $row["account_id"], "notdel" => 0));
                
                while($table->getRow()){
                    $employee_row = $table->row;
                    $user_work = new db("user_work");
                    $user_work->select("user_id = :id", false, false, array("id" => $employee_row["id"]));
                    $user_work->getRow();
                    $user_work_row = $user_work->row;
                    
                    
                    $array_text_exchanged = array();
                    $array_text_exchanged["<\$preferredEmailUsersFirstName>"] = $preferred_employee_name["firstname"];
                    $array_text_exchanged["<\$preferredEmailUserLastname>"] = $preferred_employee_name["lastname"];
                    $array_text_exchanged["<\$employeeFirstName>"] = $employee_row["employee_firstname"];
                    $array_text_exchanged["<\$employeeLastname>"] = $employee_row["employee_lastname"];
                    $array_text_exchanged["<\$insertSiteLocaiton>"] = getSiteLocation($row["employee_id"]);
                    $array_text_exchanged["<\$companyName>"] = getCompanyName($row["employee_id"]);
                    $array_text_exchanged["<\$insertdate>"] = date_add(date_create($user_work_row["start_date"]), date_interval_create_from_date_string("180 days"));// 6 months
                    
                    email($array_text_exchanged["<\$insertdate>"], $row["period"], $row["days_prior"], $row["email_con"], $array_text_exchanged, $row["reminder_email"]);
                }
                break;
            }
            case "Injury Register": {
                $table = new db("injury_register");
                $table->select("account_id = :id AND deleted = :notdel", false, false, array("id" => $row["account_id"], "notdel" => 0));
                
                while($table->getRow()){
                    $injury_register_row = $table->row;
                    $employee_name = getEmployeeName($injury_register_row["employee_id"]);
                    $severity_list = array(
                        "!!!!Kill or cause permanent disablility or ill health",
                        "!!!Long term illness or serious injury",
                        "!!Medical attention and several days off work",
                        "!First aid needed"
                    );
                    $likelihood_list = array(
                        "++Very likely Could happen any time",
                        "+Likely Could happen any time",
                        "-Unlikely Could happen, but very rarely",
                        "--Very unlikely Could happen, but probably never will"
                    );
                    
                    $array_text_exchanged = array();
                    $array_text_exchanged["<\$preferredEmailUsersFirstName>"] = $preferred_employee_name["firstname"];
                    $array_text_exchanged["<\$preferredEmailUserLastname>"] = $preferred_employee_name["lastname"];
                    $array_text_exchanged["<\$employeeFirstName>"] = $employee_name["firstname"];
                    $array_text_exchanged["<\$employeeLastname>"] = $employee_name["lastname"];
                    $array_text_exchanged["<\$insertSiteLocaiton>"] = getSiteLocation($row["employee_id"]);
                    $array_text_exchanged["<\$companyName>"] = getCompanyName($row["employee_id"]);
                    $array_text_exchanged["<\$insertdate>"] = $injury_register_row["incident_date"]; //incident_date
                    $array_text_exchanged["<\$likelihood>"] = $likelihood_list[$injury_register_row["risk_likelihood"] / 1];
                    $array_text_exchanged["<\$severity>"] = $severity_list[$injury_register_row["level_of_risk"] / 1];
                    $period = explode(" ", $injury_register_row["email_frequency"]);
                    email($array_text_exchanged["<\$insertdate>"], $period[2], 0, $row["email_con"], $array_text_exchanged, $row["reminder_email"]);
                }
                break;
            }
        }
        //array_push($reminders, $rm_table->row);
    }
    
?>