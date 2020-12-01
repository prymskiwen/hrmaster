<?php
date_default_timezone_set('Australia/Sydney');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);

if (!class_exists('db')) {
    require('db.class.php');
}
require('email.class.php');

if( ! ini_get('date.timezone') ){
    date_default_timezone_set('GMT');
}

class transaction {
    var $lockoutLengthMins = 15;
    var $database = '';
    var $user = array();
    var $server_url = "https://www.hrmaster.com.au";
    var $font = array();
    //var $server_url = "http://localhost:8011";
    var $email_template = "
            <!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
            <html xmlns=\"http://www.w3.org/1999/xhtml\">
                <head>
                    <meta http-equiv=\"Content-Type\" content=\"text/html; \" />
                    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no\">
                    <style type=\"text/css\">
                        ExternalClass,.ExternalClass div,.ExternalClass font,.ExternalClass p,.ExternalClass span,.ExternalClass td,h1,img{line-height:100%}
                        h1,h2{display:block;font-family:Helvetica;font-style:normal;font-weight:700}
                        #outlook a{padding:0}
                        .ExternalClass,.ReadMsgBody{width:100%}
                        a,blockquote,body,li,p,table,td{-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%}
                        table,td{mso-table-lspace:0;mso-table-rspace:0}
                        img{-ms-interpolation-mode:bicubic;border:0;height:auto;outline:0;text-decoration:none}
                        table{border-collapse:collapse!important}
                        #bodyCell,#bodyTable,body{height:100%!important;margin:0;padding:0;width:100%!important}
                        #bodyCell{padding:20px;}
                        #templateContainer{width:600px;border:1px solid #ddd;background-color:#fff}
                        #bodyTable,body{background-color:#FAFAFA}
                        h1{color:#202020!important;font-size:26px;letter-spacing:normal;text-align:left;margin:0 0 10px}
                        h2{color:#404040!important;font-size:20px;line-height:100%;letter-spacing:normal;text-align:left;margin:0 0 10px}
                        h3,h4{display:block;font-style:italic;font-weight:400;letter-spacing:normal;text-align:left;margin:0 0 10px;font-family:Helvetica;line-height:100%}
                        h3{color:#606060!important;font-size:16px}
                        h4{color:grey!important;font-size:14px}
                        .headerContent{background-color:#f8f8f8;border-bottom:1px solid #ddd;color:#505050;font-family:Helvetica;font-size:20px;font-weight:700;line-height:100%;text-align:left;vertical-align:middle;padding:0}
                        .bodyContent,.footerContent{font-family:Helvetica;line-height:150%;text-align:left;}
                        .footerContent{text-align:center}
                        .bodyContent pre{padding:15px;background-color:#444;color:#f8f8f8;border:0}
                        .bodyContent pre code{white-space:pre;word-break:normal;word-wrap:normal}
                        .bodyContent table{margin:10px 0;background-color:#fff;border:1px solid #ddd}
                        .bodyContent table th{padding:4px 10px;background-color:#f8f8f8;border:1px solid #ddd;font-weight:700;text-align:center}
                        .bodyContent table td{padding:3px 8px;border:1px solid #ddd}
                        .table-responsive{border:0}
                        .bodyContent a{word-break:break-all}
                        .headerContent a .yshortcuts,.headerContent a:link,.headerContent a:visited{color:#1f5d8c;font-weight:400;text-decoration:underline}
                        #headerImage{height:auto;max-width:600px;padding:20px}
                        #templateBody{background-color:#fff}
                        .bodyContent{color:#505050;font-size:14px;padding:20px}
                        .bodyContent a .yshortcuts,.bodyContent a:link,.bodyContent a:visited{color:#1f5d8c;font-weight:400;text-decoration:underline}
                        .bodyContent a:hover{text-decoration:none}
                        .bodyContent img{display:inline;height:auto;max-width:560px}
                        .footerContent{color:grey;font-size:12px;padding:20px}
                        .footerContent a .yshortcuts,.footerContent a span,.footerContent a:link,.footerContent a:visited{color:#606060;font-weight:400;text-decoration:underline}
                        @media only screen and (max-width:640px){h1,h2,h3,h4{line-height:100%!important}
                        #templateContainer{max-width:600px!important;width:100%!important}
                        #templateContainer,body{width:100%!important}
                        a,blockquote,body,li,p,table,td{-webkit-text-size-adjust:none!important}
                        body{min-width:100%!important}
                        #bodyCell{padding:10px!important}
                        h1{font-size:24px!important}
                        h2{font-size:20px!important}
                        h3{font-size:18px!important}
                        h4{font-size:16px!important}
                        #templatePreheader{display:none!important}
                        .headerContent{font-size:20px!important;line-height:125%!important}
                        .footerContent{font-size:14px!important;line-height:115%!important}
                        .footerContent a{display:block!important}
                        .hide-mobile{display:none;}
                     }
                    </style>
                </head>
                <body leftmargin=\"0\" marginwidth=\"0\" topmargin=\"0\" marginheight=\"0\" offset=\"0\">
                    <center>
                        <table align=\"center\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" height=\"100%\" width=\"100%\" id=\"bodyTable\">
                            <tr>
                                <td align=\"center\" valign=\"top\" id=\"bodyCell\">
                                    <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" id=\"templateContainer\">
                                        <tr>
                                            <td align=\"center\" valign=\"top\">
                                                <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" id=\"templateHeader\">
                                                    <tr>
                                                        <td valign=\"top\" class=\"headerContent\">
                                                            <a href=\"https://www.hrmaster.com.au\">
                                                                <img src=\"https://hrmaster.com.au/assets/images/hrmlogo-small.png\" style=\"max-width:100px;padding:20px\" id=\"headerImage\" alt=\"HR Master\" />
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align=\"center\" valign=\"top\">
                                                <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" id=\"templateBody\">
                                                    <tr>
                                                        <td valign=\"top\" class=\"bodyContent\">
                                                        <div id='message-content'></div>
                                                        <br />
                                                        <br />
                                                        HR Master Staff</p><!-- message footer start -->
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align=\"center\" valign=\"top\">
                                                <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" id=\"templateFooter\">
                                                    <tr>
                                                        <td valign=\"top\" class=\"footerContent\">
                                                            &nbsp;<a href=\"https://www.hrmaster.com.au\">visit our website</a>
                                                            <span class=\"hide-mobile\"> | </span>
                                                            <a href=\"https://www.hrmaster.com.au/\">log in to your account</a>
                                                            <span class=\"hide-mobile\"> | </span>
                                                            <a href=\"https://www.hrmaster.com.au\">get support</a>&nbsp;<br />
                                                            Copyright &copy; HR Master, All rights reserved.
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </center>
                </body>
            </html>
        ";
    function __construct() {

    }

    public function initialDataDprt($userId, $raw=false) {
        $d      = new db('user_work');
        $sql = "SELECT
                    data.id,
                    data.display_text AS department
                FROM
                (SELECT department
                FROM 
                    (SELECT department FROM user_work INNER JOIN 
                    (SELECT id from user WHERE deleted = 0 AND account_id = :uid) t_employee 
                    ON user_work.user_id = t_employee.id ) t1
                GROUP BY department) t_department
                INNER JOIN data
                ON data.id = t_department.department";

        $d->select(false, false, $sql, array('uid' => $userId));
        if ($d->numRows == 0) {
            return ;
        }

        $temp_id = null;
        $data = array();
        if ($raw) {
            array_push($data, array('id' => 0, 'department' => 'All Departments'));
        }
        while ($d->getRow()) {
            if($temp_id == $d->id)
            continue;
            array_push($data, array('id' => $d->id, 'department' => $d->department));
            $temp_id = $d->id;
        }

        if ($raw) {
            return $data;
        } else {
           echo json_encode(array('res' => $data));
        }

    }

    public function initialDataPosition($userId, $raw=false) {
        $d      = new db('user_work');
        $sql = "SELECT DISTINCT(position) AS PID, 
                (SELECT display_text FROM data WHERE id = PID) AS PositionName
                  FROM user_work
                 WHERE active = :active
                   AND user_id IN (SELECT id FROM user WHERE account_id = :uid)
                ORDER BY position ASC";
        $d->select(false, false, $sql, array('uid' => $userId,'active' => 1));
        if ($d->numRows == 0) {
            return ;
        }

        $temp_id = null;
        $data = array();
        if ($raw) {
            array_push($data, array('id' => 0, 'position' => 'All Positions'));
        }
        while ($d->getRow()) {
            if ($d->PositionName == '') {
                continue;
            }
            array_push($data, array('id' => $d->PID, 'position' => $d->PositionName));
        }

        if ($raw) {
            return $data;
        } else {
            echo json_encode(array('res' => $data));
        }

    }
    public function initialDataLctn($userId, $raw=false) {
        $d      = new db('user_work');
        $sql = "SELECT
                    data.id,
                    data.display_text AS location
                FROM
                (SELECT
                    site_location
                FROM 
                    (SELECT site_location FROM user_work INNER JOIN 
                    (SELECT id from user WHERE deleted = 0 AND account_id = :uid) t_employee 
                    ON user_work.user_id = t_employee.id ) t1
                GROUP BY site_location) t_sitelocation
                INNER JOIN data
                ON data.id = t_sitelocation.site_location 
                WHERE data.account_id=:uid ORDER BY location ASC";

        if ($raw) {
            /*$sql = "SELECT DISTINCT(site_location) as SID, site_location as id,
                            (SELECT display_text FROM data WHERE id = SID) AS 'location'
                      FROM user_work
                     WHERE user_id IN (SELECT user_id FROM user WHERE account_id = :uid)";*/
        }

        $d->select(false, false, $sql, array('uid' => $userId));
        if ($d->numRows == 0) {
            return ;
        }

        $temp_id = null;
        $data = array();
        if ($raw) {
            array_push($data, array('id' => 0, 'location' => 'All Locations'));
        }
        while ($d->getRow()) {
            if($temp_id == $d->id) {
                continue;
            }
            array_push($data,  array('id' => $d->id, 'location' => $d->location));
            $temp_id = $d->id;
        }

        if ($raw) {
            return $data;
        } else {
            echo json_encode(array('res' => $data));
        }
    }
    public function initialDataMechanism($userId, $raw=false) {
        $d      = new db('data');
        $sql = "SELECT id, display_text 
                FROM data
                 WHERE type='injurymechanism'
                ORDER BY display_text ASC";
        $d->select(false, false, $sql);
        $data = array();
        while ($d->getRow()) {
            array_push($data, array('id' => $d->row['id'], 'mechanism' => $d->row['display_text']));
        }
        echo json_encode(array('res' => $data));

    }
    public function initialDataNature($userId, $raw=false) {
        $d      = new db('data');
        $sql = "SELECT id, display_text 
                FROM data
                 WHERE type='injurynature'
                ORDER BY display_text ASC";
        $d->select(false, false, $sql);
        $data = array();
        while ($d->getRow()) {
            array_push($data, array('id' => $d->row['id'], 'nature' => $d->row['display_text']));
        }
        echo json_encode(array('res' => $data));

    }
      //newly modified

      public function initialDataYr($userId, $raw=false) {

        $d      = new db('user_work');
        $sql = "SELECT DISTINCT YEAR(usrwk.workdate_added) AS year FROM user_work AS usrwk INNER JOIN user AS usr ON usr.id = usrwk.user_id AND usr.account_id = :uid AND usr.deleted = 0 ORDER BY year";
        $d->select(false, false, $sql, array('uid' => $userId));

        if ($d->numRows == 0) {
            return ;
        }

        $i=0;

        while ($d->getRow()) {
            $data[$i] = array('year' => $d->year);
            $i++;
        }

        if ($raw) {
            return $data;
        } else {
            echo json_encode(array('res' => $data));
        }

      }

      //newly modified
        public function getEmpList($post){
            $d = new db('user');
            $sql = "SELECT id, CONCAT(firstname, ' ', lastname) as employee FROM user WHERE deleted=0";
            if($post->userData->account_id!=1)
                $sql.=" AND account_id=".$post->userData->account_id;
            if($post->showMode==0)
                $sql.=" AND active=1";
            $sql.=" ORDER BY firstname ASC, lastname";
            $d->select(false, false, $sql);
            $result = array();
            while($d->getRow()){
                array_push($result, $d->row);
            }
            return json_encode(array('res'=>$result));
        }
      public function initialDataEply($userId, $raw=false) {
        $d      = new db('user');
        $sql = "SELECT DISTINCT usr.id, usr.firstname, usr.lastname
                  FROM user AS usr 
            INNER JOIN user_work AS usrwk ON usrwk.user_id = usr.id 
                 WHERE usr.active = :active
                   AND usr.deleted = :notdel
                   AND usr.account_id = :aid
            ORDER BY usr.firstname ASC, usr.lastname";

        if ($raw) {
            $sql = "SELECT DISTINCT(usr.id), usr.firstname, usr.lastname, usrwk.department, usrwk.position 
                      FROM user AS usr 
                INNER JOIN user_work AS usrwk ON usrwk.user_id = usr.id 
                     WHERE usr.active = :active
                       AND usr.id IN (SELECT employee_id FROM injury_register WHERE deleted = :notdel)
                       AND usr.deleted = :notdel
                       AND usr.account_id = :aid";
        }

        $d->select(false, false, $sql, array('active' => 1, 'notdel'=> 0, 'aid' => $userId));
        if ($d->numRows == 0) {
            return ;
        }

        $temp_id = null;
        $data = array();
        if ($raw) {
            array_push($data, array('id' => 0, 'employee' => 'All Employees'));
        }
        while ($d->getRow()) {
            if($temp_id == $d->id)
            continue;
            array_push($data, array('id' => $d->id, 'employee' => $d->firstname.' '.$d->lastname));
            $temp_id = $d->id;
        }


        if ($raw) {
            return $data;
        }
        echo json_encode(array('res' => $data));

      }
    public function getInjuredEmployees($userId){
        $d = new db('injury_register');
        $sql = "SELECT u.id, u.firstname, u.lastname FROM injury_register ir JOIN user u ON u.id=ir.employee_id WHERE u.active=1 AND u.deleted=0 AND u.account_id=".$userId;
        $d->select(false, false, $sql);
        $result = array();
        while($d->getRow()){
            array_push($result, array('id'=>$d->row['id'], 'employee'=>$d->row['firstname']." ".$d->row['lastname']));
        }
        echo json_encode(array('res'=>$result));
    }
    public function initialDataFreeEply($userId, $param){
        $d      = new db('user');
        $sql = "SELECT CONCAT(u.firstname,' ',u.lastname) AS employeename, u.telephone, u.email, 
                    (SELECT data.display_text FROM user_work JOIN data ON data.id=user_work.site_location WHERE user_work.user_id=u.id ORDER BY user_work.workdate_added DESC LIMIT 1) AS location, 
                    (SELECT data.display_text FROM user_work JOIN data ON data.id=user_work.position WHERE user_work.user_id=u.id ORDER BY user_work.workdate_added DESC LIMIT 1) AS position,
                    (SELECT data.display_text FROM user_work JOIN data ON data.id=user_work.department WHERE user_work.user_id=u.id ORDER BY user_work.workdate_added DESC LIMIT 1) AS department 
                    FROM user u
                    WHERE u.id NOT IN 
                    	(SELECT usr.id FROM user AS usr 
                    	INNER JOIN alloc_course ac ON ac.employee_id = usr.id 
                    	WHERE usr.active = :active
                    		AND usr.deleted = :notdel
                    		AND usr.account_id = :aid
                    	GROUP BY usr.id) 
                    AND u.account_id=:aid AND u.active= :active AND u.deleted=:notdel";
        $d->select(false, false, $sql, array('active' => 1, 'notdel'=> 0, 'aid' => $userId));
        $data = array();
        if ($d->numRows == 0) {return ;}
        while ($d->getRow()) {
            //var_dump($d->row) ;
            if($param->dep!="" && $d->row['department']==$param->dep)
                array_push($data, $d->row);
            else if($param->pos!="" && $d->row['position']==$param->pos)
                array_push($data, $d->row);
            else if($param->loc!="" && $d->row['location']==$param->loc)
                array_push($data, $d->row);
            else if($param->emp!="" && $d->row['employeename']==$param->emp)
                array_push($data, $d->row);
            else if($param->dep=="" && $param->pos=="" && $param->loc=="" && $param->emp=="")
                array_push($data, $d->row);
        }
        echo json_encode(array('res' => $data));
    }


      public function initialDataUserCount($userId, $raw=false) {
        $d      = new db('user_work');
        $sql    = "SELECT	
                        t_year.year AS myYear,
                        Ccount
                    FROM 
                        (SELECT YEAR(user_work.workdate_added) AS year FROM user_work GROUP BY YEAR(user_work.workdate_added)) t_year INNER JOIN	 
                        ( 
                        SELECT
                            t1.year,
                            COUNT(user_id) AS Ccount 
                        FROM
                            (SELECT YEAR(user_work.workdate_added) AS year, user_id FROM user_work INNER JOIN 
                            (SELECT id from user e WHERE deleted = 0 AND account_id = :uid) employee_t ON user_work.user_id = employee_t.id ) t1 GROUP BY t1.year
                        ) t1 ON t_year.year = t1.year
                    ORDER BY t_year.year";
        $d->select(false, false, $sql, array('uid' => $userId));
        if ($d->numRows == 0) {
            return;
        }

        $i = 0;
        while ($d->getRow()) {
            $data[$i] = array('year' =>$d->myYear,'count'=>$d->Ccount);
            $i++;
        }

        if ($raw) {
            return $data;
        }
        echo json_encode(array('res' => $data));
      }
    public function departmentBarCalc($get,$userId) {
        $d      = new db('user_work');
        $sql    = "SELECT	
                        t_year.year AS Cyear,
                        Ccount
                    FROM 
                        (SELECT YEAR(user_work.workdate_added) AS year FROM user_work GROUP BY YEAR(user_work.workdate_added)) t_year LEFT JOIN
                        (
                        SELECT
                            t1.year,
                            COUNT(t1.department) AS Ccount         
                        FROM 
                            (SELECT YEAR(user_work.workdate_added) AS year, department, user_id FROM user_work INNER JOIN 
                            (SELECT id from user e WHERE deleted = 0 AND account_id = :uid) employee_t ON user_work.user_id = employee_t.id) t1  WHERE t1.department = :id GROUP BY t1.year
                        ) t1 ON t_year.year = t1.year
                    ORDER BY t_year.year";
        $d->select(false, false, $sql, array('id' => $get,'uid' => $userId));
        if ($d->numRows == 0) {
            return;
        }
        $i = 0;
        while ($d->getRow()) {
            $data[$i] = array('year' =>$d->Cyear,'count'=>$d->Ccount);
            $i++;
        }
         echo json_encode(array('res' => $data));
    }
    public function locationBarCalc($get,$userId) {
        $d      = new db('user_work');
        $sql    = "SELECT	
                        t_year.year AS Cyear,
                        Ccount
                    FROM 
                        (SELECT YEAR(user_work.workdate_added) AS year FROM user_work GROUP BY YEAR(user_work.workdate_added)) t_year LEFT JOIN	
                        (	
                        SELECT 
                            t1.year, 
                            COUNT(t1.site_location) AS Ccount 
                        FROM 
                            (SELECT YEAR(user_work.workdate_added) AS year, site_location, user_id FROM user_work INNER JOIN 
                            (SELECT id from user e WHERE deleted = 0 AND account_id = :uid) employee_t ON user_work.user_id = employee_t.id) t1 WHERE t1.site_location = :id GROUP BY t1.year
                        ) t1 ON t_year.year = t1.year
                    ORDER BY t_year.year";
        $d->select(false, false, $sql, array('id' => $get,'uid' => $userId));
        if ($d->numRows == 0) {
            return;
        }
        $i = 0;
        while ($d->getRow()) {
            $data[$i] = array('year' =>$d->Cyear,'count'=>$d->Ccount);
            $i++;
        }
        echo json_encode(array('res' => $data));
    }
    public function departmentPieCalc($get,$userId) {
        $d      = new db('user_work');
        $sql    = "SELECT 
            t2.id,
            data.display_text AS Cname 
        FROM 
            data INNER JOIN 
            (SELECT 
                t1.department AS id 
            FROM 
                (SELECT * 
                FROM 
                    user_work INNER JOIN 
                    (SELECT id from user e WHERE deleted = 0 AND account_id = :uid) employee_t ON user_work.user_id = employee_t.id ) t1 
            WHERE 
                YEAR(t1.workdate_added) = :year) t2 ON data.id = t2.id
            ORDER BY t2.id";
        $d->select(false, false, $sql, array('year' => $get,'uid' => $userId));
        if ($d->numRows == 0) {
            return;
        }
        $i = 0;
        while ($d->getRow()) {
            $data[$i] = array('name' =>$d->Cname);
            $i++;
        }
         echo json_encode(array('res' => $data));
      }
      public function locationPieCalc($get,$userId){
        $d      = new db('user_work');
        $sql    = "SELECT 
            t2.id,
            data.display_text AS Cname 
        FROM 
            data INNER JOIN 
            (SELECT 
                t1.site_location AS id 
            FROM 
                (SELECT * FROM user_work INNER JOIN 
                (SELECT id from user e WHERE deleted = 0 AND account_id = :uid) employee_t ON user_work.user_id = employee_t.id ) t1 
            WHERE YEAR(t1.workdate_added) = :year) t2 ON data.id = t2.id
        ORDER BY t2.id";
        $d->select(false, false, $sql, array('year' => $get,'uid' => $userId));
        if ($d->numRows == 0) {
            return;
        }
        $i = 0;
        while ($d->getRow()) {
            $data[$i] = array('id' =>$d->id, 'name' =>$d->Cname);
            $i++;
        }
         echo json_encode(array('res' => $data));
      }
      public function departmentCompensationCalc($get,$userId){
        $d      = new db('user_work');
        $sql    = "SELECT 
            t2.Cname,
            SUM(annual_rate) AS salary,
            SUM(bonus) AS bonus,
            SUM(overtime) AS overtime,
            SUM(commission) AS commission 
        FROM (
            SELECT 
                data.display_text AS Cname,
                t1.* 
            FROM 
                data INNER JOIN 
                (SELECT * FROM user_work INNER JOIN 
                (SELECT id from user e WHERE deleted = 0 AND account_id = :uid) employee_t ON user_work.user_id = employee_t.id) t1 ON data.id = t1.department
            ) t2 
        WHERE YEAR(t2.workdate_added) = :year GROUP BY Cname";
        $d->select(false, false, $sql, array('year' => $get,'uid' => $userId));
        if ($d->numRows == 0) {
            return;
        }
        $i = 0;
        while ($d->getRow()) {
            $data[$i] = array('name' =>$d->Cname, 'salary' =>$d->salary,'bonus'=>$d->bonus,'overtime'=>$d->overtime,'commission'=>$d->commission);
            $i++;
        }
         echo json_encode(array('res' => $data));
      }
      public function locationCompensationCalc($get,$userId){
        $d      = new db('user_work');
        $sql    = "SELECT 
            t2.Cname,
            SUM(annual_rate) AS salary,
            SUM(bonus) AS bonus,
            SUM(overtime) AS overtime,
            SUM(commission) AS commission 
        FROM (
            SELECT 
                data.display_text AS Cname,
                t1.* 
            FROM 
                data INNER JOIN 
                (SELECT * FROM user_work INNER JOIN 
                (SELECT id from user e WHERE deleted = 0 AND account_id = :uid) employee_t ON user_work.user_id = employee_t.id) t1 ON data.id = t1.site_location
            ) t2 
        WHERE YEAR(t2.workdate_added) = :year GROUP BY Cname";
        $d->select(false, false, $sql, array('year' => $get,'uid' => $userId));
        if ($d->numRows == 0) {
            return;
        }
        $i = 0;
        while ($d->getRow()) {
            $data[$i] = array('name' =>$d->Cname, 'salary' =>$d->salary,'bonus'=>$d->bonus,'overtime'=>$d->overtime,'commission'=>$d->commission);
            $i++;
        }
         echo json_encode(array('res' => $data));
      }
      public function baseSalaryCalc($get,$userId){
        $d      = new db('user_work');
        $sql    = "SELECT 
            annual_rate 
        FROM 
            (SELECT * FROM user_work INNER JOIN 
            (SELECT id from user e WHERE deleted = 0 AND account_id = :uid) employee_t ON user_work.user_id = employee_t.id ) t1 
        WHERE 
            YEAR(t1.workdate_added) = :year ORDER BY annual_rate";
        $d->select(false, false, $sql, array('year' => $get,'uid' => $userId));
        if ($d->numRows == 0) {
            return;
        }
        $i = 0;
        while ($d->getRow()) {
            $data[$i] = $d->annual_rate;
            $i++;
        }
         $counts = $this->compareSize($data);
         $label = ['>$10,000','$90,000-$100,000','$80,000-$90,000','$70,000-$80,000','$60,000-$70,000',
         '$50,000-$60,000','$40,000-$50,000','$30,000-$40,000','<$30,000'];
         $result = array('label'=>$label,'data'=>$counts);
        echo json_encode(array('res' => $result ));
      }
      public function totalCompensationCalc($get,$userId){
        $d      = new db('user_work');
        $sql    = "SELECT	
                        t_year.year,
                        t1.annual_rate,
                        t1.overtime,
                        t1.bonus,
                        t1.commission
                    FROM 
                        (SELECT YEAR(user_work.workdate_added) AS year FROM user_work GROUP BY YEAR(user_work.workdate_added)) t_year LEFT JOIN		
                        (
                        SELECT 
                            YEAR(t1.workdate_added) AS year,
                            SUM(annual_rate) AS annual_rate,
                            SUM(overtime) AS overtime,
                            SUM(bonus) AS bonus,
                            SUM(commission) AS commission 
                        FROM 
                            (SELECT * FROM user_work INNER JOIN 
                            (SELECT id from user e WHERE deleted = 0 AND account_id = :uid) employee_t ON user_work.user_id = employee_t.id ) t1 
                        WHERE user_id = :id
                        ) t1 ON t_year.year = t1.year
                    ORDER BY t_year.year";
        $d->select(false, false, $sql, array('id' => $get,'uid' => $userId));
        if ($d->numRows == 0) {
            return;
        }

        $i = 0;
        $total = 0;
        while ($d->getRow()) {
            $total = $d->annual_rate+$d->overtime+$d->bonus+$d->commission;
            $data[$i] = array('year' =>$d->year, 'total' =>$total);
            $i++;
            $total = 0;
        }
         echo json_encode(array('res' => $data));
      }
      public function compareSize($get){
        $value = array();
        for($i = 0; $i < 9; $i++){
            $value[$i] = 0;
        }
       for($i = 0; $i < count($get); $i++ ){
        if($get[$i] < 30000)
        {
            $value[8]++;
            continue;
        }
        if($get[$i] >= 30000 && $get[$i] < 40000)
        {
            $value[7]++;
            continue;

        } if($get[$i] >= 4000 && $get[$i] < 50000)
        {
            $value[6]++;
            continue;
        } if($get[$i] >= 5000 && $get[$i] < 60000)
        {
            $value[5]++;
            continue;
        } if($get[$i] >= 6000 && $get[$i] < 70000)
        {
            $value[4]++;
            continue;
        } if($get[$i] >= 7000 && $get[$i] < 80000)
        {
            $value[3]++;
            continue;
        } if($get[$i] >= 8000 && $get[$i] < 90000)
        {
            $value[2]++;
            continue;
        } if($get[$i] >= 9000 && $get[$i] < 100000)
        {
            $value[1]++;
            continue;
        } if($get[$i] >= 10000)
        {
            $value[0]++;
            continue;
        }
       }
       return $value;
      }

      public function selectedEmplyCalc($id,$year,$userId) {
        $d      = new db('user_work');
        $sql    = "SELECT
        t1.*,
        (SELECT
            SUM(bonus) + SUM(overtime) + SUM(commission) + SUM(annual_rate) AS total_comp
        FROM
            user_work
        WHERE
            user_id = :id AND YEAR(workdate_added) = :year) AS 'Total Comp.',
        (SELECT	
            firstname
        FROM
            user
        WHERE
            deleted = 0 AND id = :id) AS firstname,
        (SELECT	
            lastname
        FROM
            user
        WHERE
            deleted = 0 AND id = :id) AS lastname
        FROM
        (SELECT
            start_date AS 'Hire Date',
            end_date AS 'Term. Date',
            YEAR(workdate_added) AS 'Year',
            annual_rate,
            workdate_added,
            bonus AS 'Bonus',
            overtime AS 'Overtime',
            commission AS 'Commission',
            annual_leave_owing AS 'Annual Leave',
            (
                CASE 
                    WHEN employment_type_id = 341 THEN personal_leave_taken / 7.6
                    WHEN employment_type_id = 342 THEN personal_leave_taken / hours_week * days_week
                    ELSE ''
                END
            ) AS 'Sick Days',
            (SELECT display_text FROM data WHERE data.id = user_work.site_location) AS 'Location',
            (SELECT display_text FROM data WHERE data.id = user_work.department) AS 'Department',
            (SELECT display_text FROM data WHERE data.id = user_work.employment_type_id) AS 'Empl.Type'
        FROM
            user_work
        WHERE
            user_id = :id AND YEAR(workdate_added) = :year) AS t1
        INNER JOIN
        (SELECT
            MAX(workdate_added) max_workdate_added
        FROM
            user_work
        WHERE
            user_id = :id AND YEAR(workdate_added) = :year
        GROUP BY 
            user_id) t2
        ON t1.workdate_added = t2.max_workdate_added
        ";
        $d->select(false, false, $sql, array('id'=>$id,'year'=>$year,'uid' => $userId));
        if ($d->numRows == 0) {
            return;
        }
        $i=0;
        while ($d->getRow()) {
            $data[$i] = array('employee_data' => $d);
            $i++;
        }
         echo json_encode(array('res' => $data[0]));
      }
      public function totalSalaryCalc($get,$userId) {
        $d      = new db('user_work');
        $sql    = "SELECT
            SUM(annual_rate) AS 'Total Salaries',
            SUM(bonus) AS 'Total Bonuses',
            SUM(overtime) AS 'Total Overtimes',
            SUM(commission) AS 'Total Commissions',
            SUM(annual_rate) + SUM(bonus) + SUM(overtime) + SUM(commission) AS 'Total Compensations',
            SUM(CASE 
                WHEN employment_type_id = 341 THEN personal_leave_taken / 7.6
                WHEN employment_type_id = 342 THEN personal_leave_taken / hours_week * days_week
                ELSE 0
            END) AS 'Total Sick Days'
        FROM
        (SELECT * FROM user_work WHERE YEAR(workdate_added) = :year) t1 
        INNER JOIN
        (SELECT id from user WHERE deleted = 0 AND account_id = :uid) t2
        ON t1.user_id = t2.id";
        $d->select(false, false, $sql, array('year'=>$get,'uid' => $userId));
        if ($d->numRows == 0) {
            return ;
        }
        $i=0;
        while ($d->getRow()) {
            $data[$i] = array('total_data' => $d);
            $i++;
        }
         echo json_encode(array('res' => $data[0]));
      }
    public function doLogin($post) {
        $result = $this->checkLogin($post->username, $post->password);
        echo $result;
    }
    public function checkLogin($login, $password) {
        if (!$login) {
            return json_encode(array('success' => 0, 'message' => "Username is required"));
        }else if(!$password){
            return json_encode(array('success' => 0, 'message' => "Password is required"));
        }
        $data   = array();
        $l      = new db('user');
        $l->select("username = :login AND password = :pw AND active = :e AND deleted = :del",false, false, array('login' => $login, 'pw' => md5($password), 'e' => 1, 'del' => 0));
        if ($l->numRows == 0) {  // ie. wrong password, inactive or deleted
            $l->select("username = :login",false, false, array('login' => $login));
            $l->getRow();
            //newly added
            //echo "ddfsfafdaf";
            //exit;
            if(!isset($l->active)){
                return json_encode(array('success' => 0, 'message' => "This username is not found in our records."));
            }
            if ($l->active == 0 || $l->deleted == 1) {
                return json_encode(array('success' => 0, 'message' => "This username is not found in our records."));
            } else {
                $loginAttempts = $l->login_attempts + 1;
                if ($loginAttempts > 3) {
                    $nextLogin = mktime(date('H'),date('i') + $this->lockoutLengthMins, date('s'),date('m'),date('d'),date('Y'));
                    $l->update(array('login_attempts' => 0, 'can_next_login' => 0), "id = :id", false, array('login_attempts' => $loginAttempts, 'can_next_login' => $nextLogin, 'id' => $l->id));
                } else {
                    $l->update(array('login_attempts' => 0), "id = :id", false, array('login_attempts' => $loginAttempts, 'id' => $l->id));
                }
            }
            if ($l->can_next_login > 0) {
                $currTime = strtotime('now');
                if ($currTime < $l->can_next_login) {
                    $message = "Your account has been temporarily locked. Please contact HRM or alternatively, your account will be unlocked at ".date('H:i', $l->can_next_login)." on the ".date('d/m/Y', $l->can_next_login).".";
                    return json_encode(array('success' => 0, 'message' => $message));
                }
            }
            return json_encode(array('success' => 0, 'message' => "Invalid username or password"));
        }
        $l->getRow();
        if ($l->can_next_login > 0) {
            if (strtotime('now') < $l->can_next_login) {
                $message = "Your account has been temporarily locked. Please contact HRM or alternatively, your account will be unlocked at ".date('H:i', $l->can_next_login)." on the ".date('d/m/Y', $l->can_next_login).".";
                return json_encode(array('success' => 0, 'message' => $message));
            }
        }
        $numLogins = $l->total_logins + 1;
        $lastLogin = date('Y-m-d H:i:s');
        $l->update(array('total_logins' => 0, 'last_login' => $lastLogin, 'can_next_login' => 0, 'login_attempts' => 0), "id = :id", false, array('total_logins' => $numLogins, 'last_login' => $lastLogin, 'can_next_login' => 0, 'id' => $l->id, 'login_attempts' => 0));


        $perms = $this->_getPermissions($l->usertype_id, $l->id);

        return json_encode(array('userdetail' => $l->row, 'success' => "1", 'permissions' => $perms));
    }
    private function _getPermissions($usertype, $user_id) {

        $data   = array();
        $p      = new db('permissions');
        $sql    = "SELECT p.*, p.module_id AS MID,
                    (SELECT controller FROM modules WHERE id = MID) AS 'controller'
                     FROM permissions p
                    WHERE usertype_id = :u";
        $p->select(false, false, $sql, array('u' => $usertype));
        if ($p->numRows == 0) {
            return array();
        }
        while ($p->getRow()) {
            $data[$p->module_id] = array('r' => $p->_read, 'w' => $p->_write, 'd' => $p->_delete, 'c' => $p->controller);
        }
        return $data;
    }
    public function getEmployees($post, $returnData=false) {
        $data = array();
        $e      = new db('user');
        /*
        $sql = "SELECT *, CONCAT(firstname,' ',lastname) as 'name', state as STATE, e.id as EID, 'employee' AS `table`,

                (SELECT display_text FROM data WHERE id = STATE) as 'StateName',

                IF(gender='M','Male','Female') as 'gender',

                DATE_FORMAT(dob, '%d-%m-%Y') as 'dob',

                DATE_FORMAT(visaexpiry, '%d-%m-%Y') as 'visaexpiry'

                 from user e
                WHERE deleted = :del

                  AND account_id = :aid";

        */
        $sql = "UPDATE user u
                 JOIN user_work uw ON u.id=uw.user_id
                 SET u.active=0
                WHERE u.deleted = :del AND uw.active=0
                  AND u.account_id = :aid
                  AND uw.end_date< :now";
        $e->select(false,false, $sql, array('del' => 0, 'aid' => $post['account_id'], 'now'=>date('Y-m-d')));

        $sql = "UPDATE user u
                 JOIN user_work uw ON u.id=uw.user_id
                 SET u.active=1
                WHERE u.deleted = :del AND uw.active=0
                  AND u.account_id = :aid
                  AND !(uw.end_date< :now)";
        $e->select(false,false, $sql, array('del' => 0, 'aid' => $post['account_id'], 'now'=>date('Y-m-d')));

        $sql = "SELECT *, CONCAT(firstname,' ',lastname) as 'name', state as STATE, u.id as UID, 'user' AS `table`,
                (SELECT display_text FROM data WHERE id = STATE) as 'StateName',
                IF(gender='M','Male','Female') as 'gender',
                DATE_FORMAT(dob, '%d-%m-%Y') as 'dob',
                DATE_FORMAT(visaexpiry, '%d-%m-%Y') as 'visaexpiry'
                 FROM user u
                WHERE deleted = :del
                  AND account_id = :aid";
        $e->select(false,false, $sql, array('del' => 0, 'aid' => $post['account_id']));
        while ($e->getRow()) {
            array_push($data,$e->row);
        }
        $data1 = array();
        $e = new db("data");
        foreach($data as $row){
            $sql = "SELECT dt.display_text FROM user_work as usrwk INNER JOIN data AS dt ON usrwk.site_location = dt.id WHERE usrwk.user_id = " . $row['id'].  " ORDER BY usrwk.workdate_added DESC LIMIT 1";
            $e->select(false,false, $sql);
            $e->getRow();
            $row["StateName"] = $e->row["display_text"];
            array_push($data1, $row);
        }

        if ($returnData) {
            return $data1;
        }
        echo json_encode($data1);
    }

    public function getData($type, $account=0, $blankOption=false) {
        $data   = array();
        if ($blankOption) {
            array_push($data, $blankOption);
        }
        $d      = new db('data');
        $d->select("type = :type AND account_id IN (0, :account)", 'display_text ASC', false, array('type' => $type, 'account' => $account));
        while ($d->getRow()) {
            array_push($data, $d->row);
        }
        return $data;
    }
    public function getFlag(){
        $data = array();
        $d = new db('flags');
        $sql = "SELECT * FROM flags";
        $d->select(false, 'id ASC', $sql);
        while ($d->getRow()) {
            array_push($data, $d->row);
        }
        return $data;
    }
    public function getEmployeeData($post) {

        $data = array();
        $data['employees'] = $this->getEmployees(array('account_id' => $post->currUser->account_id), true);
        $data['states'] = $this->getData('state');
        $data['countries'] = $this->getData('country');
        $data['persontype'] = $this->getData('persontitle');
        //$data['workstate'] = $this->getData('state', $post->currUser->account_id);
        $data['positions'] = $this->getData('position', $post->currUser->account_id);
        $data['levels'] = $this->getData('level', $post->currUser->account_id);
        $data['departments'] = $this->getData('department', $post->currUser->account_id);
        $data['sitelocation'] = $this->getData('sitelocation', $post->currUser->account_id);
        $data['emptype'] = $this->getData('emptype');
        $data['hr_issue'] = $this->getData('hr_issue', $post->currUser->account_id);
        $data['hr_action'] = $this->getData('hr_action', $post->currUser->account_id);
        $data['license_name'] = $this->getData('license_name', $post->currUser->account_id);
        $data['event_license_name'] = $this->getData('event_license_name', $post->currUser->account_id);
        $data['license_type'] = $this->getData('license_type', $post->currUser->account_id);
        $data['event_license_type'] = $this->getData('event_license_type', $post->currUser->account_id);
        $data['flag'] = $this->getFlag();
        return json_encode($data);
    }
    public function getEmailFromHash($post) {
        $u      = new db('password_reset');
        $u->select("hash = :hash",false, false, array('hash' => $post->hash));
        if ($u->numRows == 0) {
            echo json_encode(array('success' => 0, 'message' => 'Invalid hash key for password reset'));
            return;
        }
        $u->getRow();
        
        $expire = strtotime("+30 minutes", strtotime(date('Y-m-d H:i:s', strtotime($u->date_created))));
        $expire = date('Y-m-d H:i:s', $expire);

        if (date('Y-m-d H:i:s') > $expire) {
            echo json_encode(array('success' => 0, 'message' => 'Link to reset password has expired.'));
            return;
        }
        echo json_encode(array('success' => 1, 'message' => '', 'email' => $u->email, 'username' => $u->username));
    }

    public function resetPassword($post) {
        //var_dump($post);
        $u      = new db('user');
        $u->update(array('password' => md5($post->password)),"username = :u", false, array('password' => md5($post->password), 'u' => $post->username));
        if ($u->rowsAffected > 0) {
            echo json_encode(array('success' => 1, 'message' => 'Your password has reset successfully.'));
        } else {
            echo json_encode(array('success' => 0, 'message' => 'Your password could not be reset.'));
        }
    }

    //newly added

    public function updateFlag($post){

        $e = new db('user_notes');
        $today = date("Y-m-d H:i:s");
        $emp_notes['updated_time'] = $today;

        $sql = "SELECT * FROM flags";
        $f = new db("flags");

        $f->select(false, false, $sql);

        $flags = array();

        while ($f->getRow()) {
            array_push($flags, $f->row['display_text']);
        }
        $e->select("id = :id", false, false, array('id' => $post->log_id));

        $rows = array();

        while ($e->getRow()) {
            array_push($rows, $e->row);
        }

        $row = array();

        $row = $rows[0];

        $data1 = array();
        $user_id = $row['user_id'];
        $entered_user_id = $row['entered_user_id'];

        $data1['who'] = $row['entered_user_name'];

        $data1['what'] = "Removed a ". $flags[$row['flag_id']] ." flag from ". $row['firstname'] ." ". $row['lastname'] ." file.";

        $date1['time'] = $today;

        $data1['account_id'] = $row['entered_user_id'];

        $sl = new db("system_logs");

        $sl->insert($data1);

        $e->update(array('flag_id' => 0), "id = :id", 1, array('flag_id' => 0, 'id' => $post->log_id));

    }

    public function forgotPassword($post) {
        $u      = new db('user');
        $u->select("email = :email AND active = :e AND deleted = :del",false, false, array('email' => $post->email, 'e' => 1, 'del' => 0));
        if ($u->numRows == 0) {
            echo json_encode(array('success' => 0, 'message' => 'The email address does not match our records. Please try again or email support@hrmaster.com.au'));
            return;
        }
        $u->getRow();
        $hashStr = $post->email.time();
        $hash = hash('md5', $hashStr);
        $data = array();
        $data['email'] = $post->email;
        $data['hash'] = $hash;
        $data['username'] = $u->username;
        $data['date_created'] = date('Y-m-d H:i:s');
        $pr = new db('password_reset');
        $pr->insert($data);
        // Send email to the user

        $m= new email(); // create the mail
        $m->From("HR Master Support <support@hrmaster.com.au>");
        $m->To($post->email);
        $m->Subject("Forgotten password reset");
        $message = "Hello ".$u->firstname.' '.$u->lastname.",<br><br>You recently requested to reset your password for your HR Master account. In order to do so, please <a href='https://hrmaster.com.au/?#/resetpassword/$hash'>Click Here</a> and you will be redirected to HR Masters \"change password\" page.<br><br>
        If you did not request a password reset, please ignore this email or contact us and let us know. This password reset is valid for the next 30 minutes at which time, it will expire.<br><br>
        Kind regards <br>
        HRM Technical Support";
        //$message = "<a href='https://hrmaster.com.au/?#/resetpassword/$hash'>Click to reset password</a>";

        $m->Body($message);
        $m->Priority(3) ;
        $m->Send();	// send the mail
        echo json_encode(array('success' => 1, 'message' => 'You will receive an email shortly with instructions on how to reset your password'));

    }

    public function searchSiteData($post) {
        $data = array();
        $db = new db('data');
        if (isset($post->account_id)) {
            $db->select('display_text LIKE :dt AND type = :type AND account_id = :aid', 'display_text ASC', false, array('dt' => '%'.$post->keyword.'%',  'type' => $post->type, 'aid' => $post->account_id));
        } else {
            $db->select('display_text LIKE :dt AND type = :type', 'display_text ASC', false, array('dt' => '%'.$post->keyword.'%',  'type' => $post->type));
        }

        while ($db->getRow()) {
            array_push($data, $db->row);
        }
        echo json_encode(array('data' => $data));
    }

    public function searchUser($post, $returnJson=true) {
        $keyword = $post->keyword;
        $user_table = new db('user');
        $data = array();
        $sql = "SELECT * FROM user WHERE account_id = :aid AND (username LIKE :kw OR firstname LIKE :kw OR lastname LIKE :kw) ";
        if (isset($post->usertype) && 1==2) { // Make this fail in case it's changed later
            $sql .= " AND usertype_id = :ut";
            $user_table->select(false,false, $sql, array('kw' => '%'.$keyword.'%', 'ut' => $post->usertype, 'aid' => $post->userData->account_id));
        } else {
            $user_table->select(false,false, $sql, array('kw' => '%'.$keyword.'%', 'aid' => $post->userData->account_id));
        }

        while ($user_table->getRow()) {
            array_push($data, $user_table->row);
        }

        if ($returnJson) {
            echo json_encode(array('users' => $data));
        } else {
            return $data;
        }
    }

    //newly added

    public function getEmailNames($post, $returnJson=true) {

        $e = new db('email_name');
        $sql = "SELECT * FROM email_name";
        $e->select(false, false, $sql);
        $data = array();
        while ($e->getRow()) {
            array_push($data, $e->row);
        }
        echo json_encode($data);
    }

    //newly added

    public function getPosition($post, $returnJson=true) {
        $e = new db('user_work');
        $sql = "SELECT * FROM user_work WHERE user_id=:id ORDER BY workdate_added DESC LIMIT 1";
        $e->select(false, false, $sql, array('id' => $post->emp_id));
        $e->getRow();
        $emp_work = $e->row;
        $e = new db("data");
        $e->select('id = :id', false, false, array('id' => $emp_work['position']));
        $e->getRow();
        $position = $e->row;
        echo json_encode($position);
    }

    //newly added

    public function getLogHistory($post, $returnJson=true){
        $e = new db('user_notes');
        $data = array();
        $sql = "SELECT *, entered_user_id AS UID, tagged_user_id AS TID,
                    (SELECT CONCAT(firstname,' ',lastname) FROM user WHERE id = UID) AS entered_user_name,
                    (SELECT CONCAT(firstname,' ',lastname) FROM user WHERE id = TID) AS tagged_user_name
                  FROM user_notes un
                 WHERE (account_id = :aid AND user_id = :eid) 
                    OR (account_id = :aid AND tagged_user_id = :eid)
                ORDER BY updated_time desc    
                    ";
        $e->select(false , false, $sql, array('eid' => $post->employee_id, 'aid' => $post->userData->account_id));
        while ($e->getRow()) {
            $e->row['account_id'] = $post->userData->account_id;
            array_push($data, $e->row);
        }
        if ($returnJson) {
            echo json_encode(array('logs' => $data));
        } else {
            return $data;
        }
    }

    //newly added

    public function getLQ($post, $returnJson=true){
        $e = new db('user_license');
        $data = array();
        $e->select("account_id = :aid AND user_id = :eid" , 'id DESC', false, array('eid' => $post->employee_id, 'aid' => $post->userData->account_id));
        while ($e->getRow()) {
            array_push($data, $e->row);
        }
        if ($returnJson) {
            echo json_encode(array('logs' => $data));
        } else {
            return $data;
        }
    }

    //newly added

    public function getOnlyEmployeeList($post, $returnJson=true){
        $e = new db('user');
        $data = array();
        $e->select("account_id = :aid", 'id ASC', false, array('aid' => $post->userData->account_id));
        while ($e->getRow()) {
            array_push($data, $e->row);
        }
        if ($returnJson) {
            echo json_encode(array('employees' => $data));
        } else {
            return $data;
        }
    }
    //newly added

    public function getEmployeeName($id){
        $e = new db("user");
        $e->select("id = :id", false, false, array("id" => $id));
        $e->getRow();
        return $e->row['firstname'] ." ". $e->row['lastname'];
    }

    //newly added
    public function getSiteLocation($id){
        $e = new db("user_work");
        $sql = "SELECT user_work.site_location FROM user_work JOIN user ON user.id = user_work.user_id WHERE user_work.user_id = :user_id order by user_work.workdate_added desc limit 1";
        $e->select(false, false, $sql, array("user_id" => $id));
        $e->getRow();
        $site_id = $e->row['site_location'];
        $d = new db("data");
        $d->select("id = :id", false, false, array("id" => $site_id));
        $d->getRow();
        $site_loc = $d->row['display_text'];
        return $site_loc;
    }
    //newly added
    public function getAllReminders($post){
        $e = new db("reminders");
        $e->select("account_id = :account_id", false, false, array("account_id" => $post->account_id));
        $reminders = array();
        while($e->getRow()){
            $e->row["employee_name"] = $this->getEmployeeName($e->row["employee_id"]);
            $d = new db("user");
            $d->select("id = :id", false, false, array("id" => $e->row["employee_id"]));
            $d->getRow();
            $e->row["active"] = $d->row["active"];
            date_default_timezone_get("Australia/Sydney");
            $da = strtotime($e->row["alert_expiry"]);
            $today = date("Y-m-d");
            if($today > date("Y-m-d", $da)){
                $e->row["alert_expiry_status"] = 1;
            }else{
                $e->row["alert_expiry_status"] = 0;
            }
            array_push($reminders, $e->row);
        }
        echo json_encode(array("reminders" => $reminders));
    }

    //newly added

    public function getEmployeeNameById($user_id){
        $e = new db("user");
        $e->select("id = :id", false, false, array("id" => $user_id));
        $e->getRow();
        return array("firstname" => $e->row["firstname"], "lastname" => $e->row["lastname"]);
    }

    //newly added

    public function getManagerNameById($id){
        $e = new db("user");
        $e->select("id = :id", false, false, array("id" => $id));
        $e->getRow();
        return array("firstname" => $e->row["firstname"], "lastname" => $e->row["lastname"]);

    }

    //newly added

    public function getPositionName($id){
        $e = new db("data");
        $e->select("id = :id", false, false, array("id" => $id));
        $e->getRow();
        return $e->row["display_text"];
    }

    //newly added

    public function getEmployeeWorkById($user_id){
        $e = new db("user_work");
        $sql = "SELECT usrwk.*, data.display_text as site_location FROM data INNER JOIN user_work AS usrwk on usrwk.site_location = data.id WHERE usrwk.user_id = ". $user_id ." ORDER BY usrwk.workdate_added DESC LIMIT 1";
        $e->select(false, false, $sql);
        $e->getRow();
        return $e->row;
    }

    //newly added

    public function getUserStatus($user_id){
        $e = new db("user");
        $e->select("id = :id", false, false, array("id" => $user_id));
        $e->getRow();
        return $e->row["active"];
    }

    //newly added

    public function getAssessmentDate($p_forms_id){
        $e = new db("form_reviews");
        $sql = "SELECT * FROM form_reviews WHERE p_forms_id = ". $p_forms_id ." AND form_status = 'pending'";
        $e->select(false, false, $sql);
        $e->getRow();
        return $e->row["assessment_date"];
    }

    //newly added

    public function getPerformanceForm($e){
        $employee_name = $this->getEmployeeNameById($e["employee_id"]);
        $user_work = $this->getEmployeeWorkById($e["employee_id"]);
        $manager_name = $this->getEmployeeNameById($e["manager_id"]);
        $row = array("id" => $e["id"], "manager_name" => $manager_name["firstname"]. " " .$manager_name["lastname"],
                    "employee_name" => $employee_name["firstname"]. " " .$employee_name["lastname"],
                    "assessment_date" => $this->getAssessmentDate($e["id"]),
                    "start_date" => $user_work["start_date"],
                    "questions" => $e["questions"],
                    "user_status" => $this->getUserStatus($e["employee_id"]),
                    "site_location" => $user_work["site_location"],
                    "frequency" => $e["frequency"] > 1 ? $e["frequency"]. " months" : $e["frequency"]. " month",
                    "email_score"=> $e['email_score']);
        return $row;
    }

    //newly added

    public function formatDate($date){
        $a = explode("-", $date);
        return $a[2]. "-" .$a[1]. "-" .$a[0];
    }

    //newly added

    public function getPerformanceForms($post){
        $e = new db("performance_forms");
        $e->select("account_id = :account_id", false, false, array("account_id" => $post->account_id));
        $performance_forms = array();
        while($e->getRow()){
            $row = $this->getPerformanceForm($e->row);
            array_push($performance_forms, $row);
        }
        $employees = array();
        $emp = new db("user");
        $emp->select('account_id = :aid AND deleted = :notdel AND active = :active', false, false, array('aid' => $post->account_id, 'notdel' => 0, "active" => 1));
        while ($emp->getRow()) {
            $sql = "SELECT emp.*, data.display_text as site_location FROM user_work as emp INNER JOIN data ON data.id = emp.site_location WHERE emp.user_id =". $emp->row["id"]." ORDER BY emp.workdate_added DESC LIMIT 1";
            $empwk = new db("user_work");
            $empwk->select(false, false, $sql);
            $empwk->getRow();
            $row = array("id" => $emp->row["id"], "firstname" => $emp->row["firstname"], "lastname" => $emp->row["lastname"], "position" => $this->getPositionName($empwk->row["position"]), "site_location" => $empwk->row["site_location"], "start_date" => $empwk->row["start_date"]);
            array_push($employees, $row);

        }
        $standard_questions = array();
        $emp = new db("standard_questions");
        $emp->select(false, false, "SELECT * FROM standard_questions");
        while ($emp->getRow()) {
            array_push($standard_questions, $emp->row);
        }
        echo json_encode(array("performance_forms" => $performance_forms, "employees" => $employees, "standard_questions" => $standard_questions));
    }

    //newly added

    public function saveForm($post){

        $data = array();
        $data["account_id"] = $post->userData->account_id;
        $data["created_by"] = $post->userData->id;
        $data["employee_id"] = $post->form->employee_id;
        $data["manager_id"] = $post->form->manager_id;
        $data["frequency"] = $post->form->frequency;
        $data["email_score"] = $post->form->email_score;
        $time = strtotime($post->form->start_date);
        $final = date("Y-m-d", strtotime("+". $post->form->frequency / 1 ." month", $time));
        // $data["assessment_date"] = $post->form->start_date == null ? null : $final;
        $data["questions"] = "";
        foreach($post->form->specializedQuestionList as $question){
            $data["questions"] .= $question->question_text;
            $data["questions"] .= "~#";
        }
        $e = new db("performance_forms");
        $e->insert($data);
        $p_forms_id = $e->lastInsertId;
        $e = new db("form_reviews");
        $data = array();
        $data["p_forms_id"] = $p_forms_id;
        $data["assessment_date"] = $final;
        $e->insert($data);
        $e = new db("performance_forms");
        $e->select("account_id = :account_id", false, false, array("account_id" => $post->userData->account_id));
        $performance_forms = array();
        while($e->getRow()){
            $row = $this->getPerformanceForm($e->row);
            array_push($performance_forms, $row);
        }
        echo json_encode(array("performance_forms" => $performance_forms));
    }

    //newly added

    public function updateForm($post){
        $data = array();
        // $e = new db("employee");
        // $fl_name = explode(" ", $post->form->employee_name);
        // $e->select("firstname = :firstname AND lastname = :lastname", false, false, array("firstname" => $fl_name[0], "lastname" => $fl_name[1]));
        // $e->getRow();
        // $data["employee_id"] = $e->row["id"];
        // $e = new db("user");
        // $fl_name = explode(" ", $post->form->manager_name);
        // $e->select("firstname = :firstname AND lastname = :lastname", false, false, array("firstname" => $fl_name[0], "lastname" => $fl_name[1]));
        // $e->getRow();
        $data["employee_id"] = $post->form->employee_id;
        $data["manager_id"] = $post->form->manager_id;
        $data["account_id"] = $post->userData->account_id;
        $data["created_by"] = $post->userData->id;
        $data["frequency"] = $post->form->frequency;
        $data["email_score"] = $post->form->email_score;
        $data["questions"] = "";
        foreach($post->form->specializedQuestionList as $question){
            $data["questions"] .= $question->question_text;
            $data["questions"] .= "~#";
        }

        $e = new db("performance_forms");
        $data1 = $data;
        $data1["id"] = $post->form->id;
        $e->update($data, "id = :id", false, $data1);
        $e->select("account_id = :account_id", false, false, array("account_id" => $post->userData->account_id));
        $performance_forms = array();
        while($e->getRow()){
            $row = $this->getPerformanceForm($e->row);
            array_push($performance_forms, $row);
        }
        echo json_encode(array("performance_forms" => $performance_forms));
    }

    //newly added

    public function deleteForm($post){
        $e      = new db('performance_forms');
        $e->delete('id = :id', false, array('id' => $post->form->id));
        $e->select("account_id = :account_id", false, false, array("account_id" => $post->userData->account_id));
        $performance_forms = array();
        while($e->getRow()){
            $row = $this->getPerformanceForm($e->row);
            array_push($performance_forms, $row);
        }
        echo json_encode(array("performance_forms" => $performance_forms));
    }
    public function calcEmployeePerformanceReviewScore($score_str){
        $score_str = str_replace(' ', '', $score_str);
        $score_array = explode(",", $score_str);
        $score_sum = 0;
        foreach($score_array as $score){
            if($score!="")
                $score_sum += (int)$score;
        }
        return $score_sum;
    }
    public function getDayDifference($date){
        $now = time(); // or your date as well
        $your_date = strtotime($date);
        $datediff = $your_date - $now;

        return round($datediff / (60 * 60 * 24));
    }
    public function getInjuryReportByDepartment($post){
        $data = array();
        $post->filter_params = json_decode($post->filter_params);
        if($post->filter_params->filter_fromdate!="") $post->filter_params->filter_fromdate = date('Y-m-d', strtotime($post->filter_params->filter_fromdate));
        if($post->filter_params->filter_todate!="") $post->filter_params->filter_todate = date('Y-m-d', strtotime($post->filter_params->filter_todate));
        //echo $post->showMode;
        $e = new db("injury_register");
        $sql = "SELECT ir.employee_id, 
                    (SELECT data.display_text FROM user_work JOIN data on user_work.site_location=data.id WHERE user_work.user_id = ir.employee_id ORDER BY user_work.workdate_added DESC LIMIT 1) as site_location,
                    (SELECT data.display_text FROM user_work JOIN data on user_work.department=data.id WHERE user_work.user_id = ir.employee_id ORDER BY user_work.workdate_added DESC LIMIT 1) as department,
                    (SELECT data.display_text FROM user_work JOIN data on user_work.position=data.id WHERE user_work.user_id = ir.employee_id ORDER BY user_work.workdate_added DESC LIMIT 1) as position
                FROM injury_register AS ir 
                INNER JOIN user AS u ON u.id=ir.employee_id WHERE u.account_id=".$post->user_data->account_id;
        if($post->showMode==0) $sql.=" AND u.active=1 ";
        if($post->filter_params->filter_fromdate!="") $sql.= " AND ir.incident_date>='".$post->filter_params->filter_fromdate."'";
        if($post->filter_params->filter_todate!="") $sql.= " AND ir.incident_date<='".$post->filter_params->filter_todate."'";
        if($post->filter_params->dep!="") $sql.= " AND (SELECT data.id FROM user_work JOIN data on user_work.department=data.id WHERE user_work.user_id = ir.employee_id ORDER BY user_work.workdate_added DESC LIMIT 1) = '".$post->filter_params->dep."'";
        if($post->filter_params->pos!="") $sql.= " AND (SELECT data.id FROM user_work JOIN data on user_work.position=data.id WHERE user_work.user_id = ir.employee_id ORDER BY user_work.workdate_added DESC LIMIT 1) = '".$post->filter_params->pos."'";
        if($post->filter_params->loc!="") $sql.= " AND (SELECT data.id FROM user_work JOIN data on user_work.site_location=data.id WHERE user_work.user_id = ir.employee_id ORDER BY user_work.workdate_added DESC LIMIT 1) = '".$post->filter_params->loc."'";
        if($post->filter_params->emp!="") $sql.= " AND ir.employee_id = '".$post->filter_params->emp."' ";
        if($post->filter_params->mechanism!="") $sql.= " AND ir.mechanismofinjury_id=".$post->filter_params->mechanism;
        if($post->filter_params->nature!="") $sql.= " AND ir.natureofinjury_id=".$post->filter_params->nature;
        $e->select(false, false, $sql);
        while ($e->getRow()) {
            array_push($data, $e->row);
        }
        $departments = array();
        $d = new db("data");
        $sql = "SELECT display_text FROM data WHERE type='department' AND account_id=".$post->user_data->account_id." ORDER BY display_text ASC";
        $d->select(false, false, $sql);
        while($d->getRow()){
            array_push($departments, $d->row['display_text']);
        }

        $result = array();
        foreach($departments as $dep){
            $result[$dep] = 0;
            foreach($data as $one){
                if($one['department']==$dep)
                    $result[$dep]++;
            }
        }
        echo json_encode(array('department_report' => $result));
    }
    public function getInjuryReportByLocation($post){
        $data = array();
        $post->filter_params = json_decode($post->filter_params);
        if($post->filter_params->filter_fromdate!="") $post->filter_params->filter_fromdate = date('Y-m-d', strtotime($post->filter_params->filter_fromdate));
        if($post->filter_params->filter_todate!="") $post->filter_params->filter_todate = date('Y-m-d', strtotime($post->filter_params->filter_todate));
        //echo $post->showMode;
        $e = new db("injury_register");
        $sql = "SELECT ir.employee_id, 
                    (SELECT data.display_text FROM user_work JOIN data on user_work.site_location=data.id WHERE user_work.user_id = ir.employee_id ORDER BY user_work.workdate_added DESC LIMIT 1) as site_location,
                    (SELECT data.display_text FROM user_work JOIN data on user_work.department=data.id WHERE user_work.user_id = ir.employee_id ORDER BY user_work.workdate_added DESC LIMIT 1) as department,
                    (SELECT data.display_text FROM user_work JOIN data on user_work.position=data.id WHERE user_work.user_id = ir.employee_id ORDER BY user_work.workdate_added DESC LIMIT 1) as position
                FROM injury_register AS ir 
                INNER JOIN user AS u ON u.id=ir.employee_id WHERE u.account_id=".$post->user_data->account_id;
        if($post->showMode==0) $sql.=" AND u.active=1 ";
        if($post->filter_params->filter_fromdate!="") $sql.= " AND ir.incident_date>='".$post->filter_params->filter_fromdate."'";
        if($post->filter_params->filter_todate!="") $sql.= " AND ir.incident_date<='".$post->filter_params->filter_todate."'";
        if($post->filter_params->dep!="") $sql.= " AND (SELECT data.id FROM user_work JOIN data on user_work.department=data.id WHERE user_work.user_id = ir.employee_id ORDER BY user_work.workdate_added DESC LIMIT 1) = '".$post->filter_params->dep."'";
        if($post->filter_params->pos!="") $sql.= " AND (SELECT data.id FROM user_work JOIN data on user_work.position=data.id WHERE user_work.user_id = ir.employee_id ORDER BY user_work.workdate_added DESC LIMIT 1) = '".$post->filter_params->pos."'";
        if($post->filter_params->loc!="") $sql.= " AND (SELECT data.id FROM user_work JOIN data on user_work.site_location=data.id WHERE user_work.user_id = ir.employee_id ORDER BY user_work.workdate_added DESC LIMIT 1) = '".$post->filter_params->loc."'";
        if($post->filter_params->emp!="") $sql.= " AND ir.employee_od = '".$post->filter_params->emp."' ";
        if($post->filter_params->mechanism!="") $sql.= " AND ir.mechanismofinjury_id=".$post->filter_params->mechanism;
        if($post->filter_params->nature!="") $sql.= " AND ir.natureofinjury_id=".$post->filter_params->nature;

        $e->select(false, false, $sql);
        while ($e->getRow()) {
            array_push($data, $e->row);
        }
        $departments = array();
        $d = new db("data");
        $sql = "SELECT display_text FROM data WHERE type='sitelocation' AND account_id=".$post->user_data->account_id." ORDER BY display_text ASC";
        $d->select(false, false, $sql);
        while($d->getRow()){
            array_push($departments, $d->row['display_text']);
        }

        $result = array();
        foreach($departments as $dep){
            $result[$dep] = 0;
            foreach($data as $one){
                if($one['site_location']==$dep)
                    $result[$dep]++;
            }
        }
        echo json_encode(array('location_report' => $result));
    }
    public function getInjuryLostReportByLocation($post){
        $data = array();
        $post->filter_params = json_decode($post->filter_params);
        if($post->filter_params->filter_fromdate!="") $post->filter_params->filter_fromdate = date('Y-m-d', strtotime($post->filter_params->filter_fromdate));
        if($post->filter_params->filter_todate!="") $post->filter_params->filter_todate = date('Y-m-d', strtotime($post->filter_params->filter_todate));
        //echo $post->showMode;
        $e = new db("injury_register");
        $sql = "SELECT ir.employee_id, ir.lost_time,
                    (SELECT data.display_text FROM user_work JOIN data on user_work.site_location=data.id WHERE user_work.user_id = ir.employee_id ORDER BY user_work.workdate_added DESC LIMIT 1) as site_location,
                    (SELECT data.display_text FROM user_work JOIN data on user_work.department=data.id WHERE user_work.user_id = ir.employee_id ORDER BY user_work.workdate_added DESC LIMIT 1) as department,
                    (SELECT data.display_text FROM user_work JOIN data on user_work.position=data.id WHERE user_work.user_id = ir.employee_id ORDER BY user_work.workdate_added DESC LIMIT 1) as position
                FROM injury_register AS ir 
                INNER JOIN user AS u ON u.id=ir.employee_id WHERE u.account_id=".$post->user_data->account_id;
        if($post->showMode==0) $sql.=" AND u.active=1 ";
        if($post->filter_params->filter_fromdate!="") $sql.= " AND ir.incident_date>='".$post->filter_params->filter_fromdate."'";
        if($post->filter_params->filter_todate!="") $sql.= " AND ir.incident_date<='".$post->filter_params->filter_todate."'";
        if($post->filter_params->dep!="") $sql.= " AND (SELECT data.id FROM user_work JOIN data on user_work.department=data.id WHERE user_work.user_id = ir.employee_id ORDER BY user_work.workdate_added DESC LIMIT 1) = '".$post->filter_params->dep."'";
        if($post->filter_params->pos!="") $sql.= " AND (SELECT data.id FROM user_work JOIN data on user_work.position=data.id WHERE user_work.user_id = ir.employee_id ORDER BY user_work.workdate_added DESC LIMIT 1) = '".$post->filter_params->pos."'";
        if($post->filter_params->loc!="") $sql.= " AND (SELECT data.id FROM user_work JOIN data on user_work.site_location=data.id WHERE user_work.user_id = ir.employee_id ORDER BY user_work.workdate_added DESC LIMIT 1) = '".$post->filter_params->loc."'";
        if($post->filter_params->emp!="") $sql.= " AND ir.employee_id = '".$post->filter_params->emp."' ";
        if($post->filter_params->mechanism!="") $sql.= " AND ir.mechanismofinjury_id=".$post->filter_params->mechanism;
        if($post->filter_params->nature!="") $sql.= " AND ir.natureofinjury_id=".$post->filter_params->nature;
        $e->select(false, false, $sql);
        while ($e->getRow()) {
            array_push($data, $e->row);
        }
        $departments = array();
        $d = new db("data");
        $sql = "SELECT display_text FROM data WHERE type='sitelocation' AND account_id=".$post->user_data->account_id." ORDER BY display_text ASC";
        $d->select(false, false, $sql);
        while($d->getRow()){
            array_push($departments, $d->row['display_text']);
        }

        $result = array();
        foreach($departments as $dep){
            $result[$dep] = 0;
            foreach($data as $one){
                if($one['site_location']==$dep){
                    if($one['lost_time']!=null){
                        $tmp = explode(":", $one['lost_time']);
                        $hours = (int)$tmp[0]*24+(int)$tmp[1];
                        $result[$dep]+=$hours;
                    }
                }
            }
        }
        echo json_encode(array('location_report' => $result));
    }
    private function _printIncludes() {
        // Page Dimensions for A4 - 595 wide by 842 high

        define('PAGE_WIDTH', 595);
        define('PAGE_HEIGHT', 842);
        define('PAGE_WIDTH_HALF', 297);
        define('PAGE_HEIGHT_HALF', 421);


        set_include_path('/home/hrmaster/public_html/assets/php' );

        require_once '/home/hrmaster/public_html/assets/php/Zend/Pdf.php';
        require_once '/home/hrmaster/public_html/assets/php/Zend/Pdf/Style.php';
        require_once '/home/hrmaster/public_html/assets/php/Zend/Pdf/Color/Cmyk.php';
        require_once '/home/hrmaster/public_html/assets/php/Zend/Pdf/Color/Html.php';
        require_once '/home/hrmaster/public_html/assets/php/Zend/Pdf/Color/GrayScale.php';
        require_once '/home/hrmaster/public_html/assets/php/Zend/Pdf/Color/Rgb.php';
        require_once '/home/hrmaster/public_html/assets/php/Zend/Pdf/Page.php';
        require_once '/home/hrmaster/public_html/assets/php/Zend/Pdf/Font.php';

        $this->font['normal'] = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA);
        $this->font['bold']   = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_BOLD);
    }
    private function _getTextWidth($text, $resource, $fontSize = null, $encoding = 'UTF-8') {

        if( $resource instanceof Zend_Pdf_Page ){
            $font = $resource->getFont();
            $fontSize = $resource->getFontSize();
        }elseif( $resource instanceof Zend_Pdf_Resource_Font ){
            $font = $resource;
            if( $fontSize === null ) throw new Exception('The fontsize is unknown');
        }

        if( !$font instanceof Zend_Pdf_Resource_Font ){
            throw new Exception('Invalid resource passed');
        }

        $drawingText = $text;//iconv ( '', $encoding, $text );
        $characters = array ();
        for($i = 0; $i < strlen ( $drawingText ); $i ++) {
            $characters [] = ord ( $drawingText [$i] );
        }
        $glyphs = $font->glyphNumbersForCharacters ( $characters );
        $widths = $font->widthsForGlyphs ( $glyphs );
        $textWidth = (array_sum ( $widths ) / $font->getUnitsPerEm ()) * $fontSize;
        return $textWidth;
    }
    private function setupPage($page, $font, $abn, $company_name, $page_no, $y) {
        $page->setFont($this->font['bold'] , 18)
             ->drawText("INJURY REPORT", PAGE_WIDTH_HALF - ($this->_getTextWidth("INJURY REPORT", $this->font['bold'] , 18) / 2), $y);

        $page->setFont($this->font['bold'] , 12)
             ->drawText($company_name, PAGE_WIDTH_HALF - ($this->_getTextWidth($company_name, $this->font['bold'] , 12) / 2), $y-20);

        $page->setFont($this->font['bold'] , 12)
             ->drawText($abn, PAGE_WIDTH_HALF - ($this->_getTextWidth($abn, $this->font['bold'] , 12) / 2), $y-40);

        $y -= 60;


        return array('page' => $page, 'y' => $y);
    }
    private function print_injuries_body($page, $injury, $font, $y, $page_no=1) {
        // Sub header
        $yy = $y;
        $page->drawRectangle(50, $y, 300, $y-330, Zend_Pdf_Page::SHAPE_DRAW_STROKE);
        $page->drawRectangle(300, $y, 550, $y-330, Zend_Pdf_Page::SHAPE_DRAW_STROKE);

        $page->setFont($this->font['bold'], 12)
             ->drawText("Employee Name: ", 60, $y-20);
        $page->setFont($this->font['normal'], 12)
             ->drawText($injury['employee_name'], 70, $y-40);

        $page->setFont($this->font['bold'], 12)
             ->drawText("Date and Time of Injury: ", 60, $y-60);
        $page->setFont($this->font['normal'], 12)
             ->drawText($injury['incident_date']." ".$injury['incident_time'], 70, $y-80);

        $page->setFont($this->font['bold'], 12)
             ->drawText("Site location: ", 60, $y-100);
        $page->setFont($this->font['normal'], 12)
             ->drawText($injury['site_location'], 70, $y-120);

        $page->setFont($this->font['bold'], 12)
             ->drawText("Location of Incident: ", 60, $y-140);
        $page->setFont($this->font['normal'], 12)
             ->drawText($injury['injury_location'], 70, $y-160);

        $page->setFont($this->font['bold'], 12)
             ->drawText("Injured Body Part: ", 60, $y-180);
        $page->setFont($this->font['normal'], 12)
             ->drawText($injury['injured_body_part'], 70, $y-200);

        $page->setFont($this->font['bold'], 12)
             ->drawText("Nature of Injury: ", 60, $y-220);
        $page->setFont($this->font['normal'], 12)
             ->drawText($injury['nature_of_injury'], 70, $y-240);

        $page->setFont($this->font['bold'], 12)
             ->drawText("Mechanism of Injury: ", 60, $y-260);
        $page->setFont($this->font['normal'], 12)
             ->drawText($injury['mechanism_of_injury'], 70, $y-280);

        $page->setFont($this->font['bold'], 12)
             ->drawText("Eyewitnesses: ", 60, $y-300);
        $page->setFont($this->font['normal'], 12)
             ->drawText($injury['eyewitness'], 70, $y-320);

        $page->setFont($this->font['bold'], 12)
             ->drawText("Total Time Lost: ", 310, $y-20);
        $page->setFont($this->font['normal'], 12)
             ->drawText($injury['total_lost_time'], 320, $y-40);

        $page->setFont($this->font['bold'], 12)
             ->drawText("Was Insurer notified? ", 310, $y-60);
        $page->setFont($this->font['normal'], 12)
             ->drawText($injury['insurer_notified'], 320, $y-80);
        if($injury['insurer_notified']=="Yes"){
            $page->setFont($this->font['normal'], 12)
             ->drawText(" ( ".$injury['insurernotified_date']." ) ", 320+$this->_getTextWidth($injury['insurer_notified'], $this->font['bold'], 12), $y-80);
        }
        $page->setFont($this->font['bold'], 12)
             ->drawText("Was Safe Work notified? ", 310, $y-100);
        $page->setFont($this->font['normal'], 12)
             ->drawText($injury['safework_notified'], 320, $y-120);
        if($injury['safework_notified']=="Yes"){
            $page->setFont($this->font['normal'], 12)
             ->drawText(" ( ".$injury['safeworknotified_date']." ) ", 320+$this->_getTextWidth($injury['safework_notified'], $this->font['bold'], 12), $y-120);
        }

        $page->setFont($this->font['bold'], 12)
             ->drawText("Investigated By: ", 310, $y-140);
        $page->setFont($this->font['normal'], 12)
             ->drawText($injury['investigated_by'], 320, $y-160);

        $page->setFont($this->font['bold'], 12)
             ->drawText("Likelihood: ", 310, $y-180);
        $page->setFont($this->font['normal'], 12);
        $likelihood = wordwrap($injury['likelihood'], 40, "\n");
        $lines = explode("\n", $likelihood);
        foreach($lines as $index=>$line){
            $page->drawText($line, 320, $y-180-(20*($index+1)));
        }
        $y = $y-180-20*(count($lines)+1);

        $page->setFont($this->font['bold'], 12)
             ->drawText("Severity: ", 310, $y);
        $page->setFont($this->font['normal'], 12);
        $severity = wordwrap($injury['severity'], 40, "\n");
        $lines = explode("\n", $severity);
        foreach($lines as $index=>$line){
            $page->drawText($line, 320, $y-(20*($index+1)));
        }
        $y = $y-20*(count($lines)+1);

        $page->setFont($this->font['bold'], 12)
             ->drawText("Is this closed? ", 310, $y);
        $page->setFont($this->font['normal'], 12)
             ->drawText($injury['is_closed'], 320, $y-20);
        if($injury['is_closed']=="Yes"){
            $page->setFont($this->font['normal'], 12)
             ->drawText(" ( ".$injury['closed_date']." ) ", 320+$this->_getTextWidth($injury['is_closed'], $this->font['bold'], 12), $y-20);
        }
        $y = $yy-360;
        $page->setFont($this->font['bold'], 12)
             ->drawText("Risk Identification: ", 50, $y);
        $page->setFont($this->font['normal'], 12);
        $risk_identification = wordwrap($injury['risk_identification'], 90, "\n");
        $lines = explode("\n", $risk_identification);
        foreach($lines as $index=>$line){
            $page->drawText($line, 50, $y-(20*($index+1)));
        }
        $y = $y - (count($lines)+1)*20;
        $page->setFont($this->font['bold'], 12)
             ->drawText("Risk Assessment: ", 50, $y);
        $page->setFont($this->font['normal'], 12);
        $risk_assessment = wordwrap($injury['risk_assessment'], 90, "\n");
        $lines = explode("\n", $risk_assessment);
        foreach($lines as $index=>$line){
            $page->drawText($line, 50, $y-(20*($index+1)));
        }
        $y = $y - (count($lines)+1)*20;
        $page->setFont($this->font['bold'], 12)
             ->drawText("Risk Controls: ", 50, $y);
        $page->setFont($this->font['normal'], 12);
        $risk_controls = wordwrap($injury['risk_controls'], 90, "\n");
        $lines = explode("\n", $risk_controls);
        foreach($lines as $index=>$line){
            $page->drawText($line, 50, $y-(20*($index+1)));
        }
        $y = $y - (count($lines)+1)*20;
        $page->setFont($this->font['bold'], 12)
             ->drawText("Employees File Notes: ", 50, $y);
        return $page;
    }
    public function printInjuryHistory($post){
        $post->filter_params = json_decode($post->filter_params);
        if($post->filter_params->filter_fromdate!="") $post->filter_params->filter_fromdate = date('Y-m-d', strtotime($post->filter_params->filter_fromdate));
        if($post->filter_params->filter_todate!="") $post->filter_params->filter_todate = date('Y-m-d', strtotime($post->filter_params->filter_todate));
        $d = new db('user');
        $sql = "SELECT account_id FROM user WHERE user.id=".$post->emp;
        $d->select(false, false, $sql);
        $d->getRow();
        $account_id = $d->row['account_id'];
        $sql = "SELECT abn, companyname FROM user WHERE user.id=".$account_id;
        $d->select(false, false, $sql);
        $d->getRow();
        $abn = $d->row['abn'];
        $company_name = $d->row['companyname'];

        $d = new db('injury_register');
        $sql = "SELECT CONCAT(u.firstname, ' ', u.lastname) as employee_name, ir.incident_date, ir.incident_time, (SELECT d.display_text FROM data d WHERE d.id=ir.location_id) as injury_location, 
                        (SELECT d.display_text FROM data d WHERE d.id=ir.site_location_id) as site_location, (SELECT d.display_text FROM data d WHERE d.id=ir.injuredbodypart_id) as injured_body_part, 
                        (SELECT d.display_text FROM data d WHERE d.id=ir.natureofinjury_id) as nature_of_injury, (SELECT d.display_text FROM data d WHERE d.id=ir.mechanismofinjury_id) as mechanism_of_injury, 
                        ir.eyewitness, ir.lost_time, ir.insurer_notified, ir.insurernotified_date, ir.safework_notified, ir.safeworknotified_date, ir.investigated_by, ir.risk_likelihood, ir.level_of_risk as severity,
                        ir.closed_date, ir.risk_identification, ir.risk_assessment, ir.risk_controls
                FROM injury_register ir 
                JOIN user u ON u.id=ir.employee_id
                WHERE ir.employee_id=".$post->emp;
        if($post->filter_params->filter_fromdate!="") $sql.= " AND ir.incident_date>='".$post->filter_params->filter_fromdate."'";
        if($post->filter_params->filter_todate!="") $sql.= " AND ir.incident_date<='".$post->filter_params->filter_todate."'";
        $d->select(false, false, $sql);
        $liklihood_list = array(
            "++Very likely Could happen any time",
            "+Likely Could happen any time",
            "-Unlikely Could happen, but very rarely",
            "--Very unlikely Could happen, but probably never will"
        );
        $severity_list = array(
            "!!!!Kill or cause permanent disablility or ill health",
            "!!!Long term illness or serious injury",
            "!!Medical attention and several days off work",
            "!First aid needed"
        );
        $history = array();
        $employee_name = "";
        while($d->getRow()){
            $employee_name = $d->row['employee_name'];
            $d->row['likelihood'] = $liklihood_list[$d->row['risk_likelihood']];
            $d->row['severity'] = $severity_list[$d->row['severity']];
            $d->row['total_lost_time'] = 0;
            if($d->row['lost_time']!=null){
                $datetime = explode(":", $d->row['lost_time']);
                $days = $datetime[0];
                $hours = $datetime[1];
                $total = 24*$days+$hours;
                if($total==0 || $total==1) $d->row['total_lost_time']= $total." hour";
                else if($total>1) $d->row['total_lost_time']= $total." hours";
            }
            if($d->row['insurer_notified']==1) $d->row['insurer_notified'] = "Yes";
            else $d->row['insurer_notified'] = "No";
            if($d->row['safework_notified']==1) $d->row['safework_notified'] = "Yes";
            else $d->row['safework_notified'] = "No";
            if($d->row['closed_date']!=null) $d->row['is_closed']="Yes";
            else $d->row['is_closed']="No";
            array_push($history, $d->row);
        }
        $page_no        = 0;
        $date           = date("d/m/Y");

        // Create new PDF Document
        $font = '';
        $this->_printIncludes();
        $http_filenames = array();
        foreach($history as $injury){

            $save_filename  = '/home/hrmaster/public_html/assets/files/injury_history/injury_history-'.$injury['employee_name'].'-'.$injury['incident_date'].'.pdf';
            $http_filename = '//hrmaster.com.au/assets/files/injury_history/injury_history-'.$injury['employee_name'].'-'.$injury['incident_date'].'.pdf';
            $this->pdf            = new Zend_Pdf();

            $page           = new Zend_Pdf_Page(Zend_Pdf_Page::SIZE_A4);
            $page_no++;
            $y          = 800;
            $pageDat = $this->setupPage($page, $font, $abn, $company_name, $page_no, $y);
            $page = $pageDat['page'];
            $y = $pageDat['y'];

            $y  = $y - 20;
            $page = $this->print_injuries_body($page, $injury, $font, $y, $page_no);

            $this->pdf->pages[]   = $page;

            // Update the PDF document
            $this->pdf->save($save_filename, true);

            // Save document as a new file
            $this->pdf->save($save_filename);

            unset($this->pdf);
            unset($page);
            array_push($http_filenames, $http_filename);
        }

        return json_encode(array('url' => $http_filenames));
    }
    public function getAnalyticsOfPerformanceReviews($post){
        $data = array();
        $post->filter_params = json_decode($post->filter_params);
        if($post->filter_params->filter_fromdate!="") $post->filter_params->filter_fromdate = date('Y-m-d', strtotime("+1 day", strtotime($post->filter_params->filter_fromdate)));
        if($post->filter_params->filter_todate!="") $post->filter_params->filter_todate = date('Y-m-d', strtotime("+1 day", strtotime($post->filter_params->filter_todate)));
        //echo $post->showMode;
        $e = new db("form_reviews");
        $sql = "SELECT pf.employee_id, CONCAT(u.firstname, ' ', u.lastname) as employee_name, fr.form_status, fr.completed_date, fr.assessment_date, fr.scores, pf.frequency, 
                    (SELECT data.display_text FROM user_work JOIN data on user_work.site_location=data.id WHERE user_work.user_id = pf.employee_id ORDER BY user_work.workdate_added DESC LIMIT 1) as site_location,
                    (SELECT data.display_text FROM user_work JOIN data on user_work.department=data.id WHERE user_work.user_id = pf.employee_id ORDER BY user_work.workdate_added DESC LIMIT 1) as department,
                    (SELECT data.display_text FROM user_work JOIN data on user_work.position=data.id WHERE user_work.user_id = pf.employee_id ORDER BY user_work.workdate_added DESC LIMIT 1) as position
                FROM form_reviews AS fr 
                INNER JOIN performance_forms AS pf ON pf.id = fr.p_forms_id 
                INNER JOIN user AS u ON u.id=pf.employee_id";

        if($post->review_status=="pending")$sql.= " WHERE fr.form_status='pending' AND fr.assessment_date > '".date('Y-m-d')."'";
        else if($post->review_status=="overdue")$sql.= " WHERE fr.form_status='pending' AND fr.assessment_date <= '".date('Y-m-d')."'";
        else if($post->review_status=="completed")$sql.= " WHERE fr.form_status='completed'";
        if($post->showMode==0) $sql.=" AND u.active=1 ";
        if($post->filter_params->filter_fromdate!="") $sql.= " AND fr.assessment_date>='".$post->filter_params->filter_fromdate."'";
        if($post->filter_params->filter_todate!="") $sql.= " AND fr.assessment_date<='".$post->filter_params->filter_todate."'";
        if($post->filter_params->dep!="") $sql.= " AND (SELECT data.display_text FROM user_work JOIN data on user_work.department=data.id WHERE user_work.user_id = pf.employee_id ORDER BY user_work.workdate_added DESC LIMIT 1) = '".$post->filter_params->dep."'";
        if($post->filter_params->pos!="") $sql.= " AND (SELECT data.display_text FROM user_work JOIN data on user_work.position=data.id WHERE user_work.user_id = pf.employee_id ORDER BY user_work.workdate_added DESC LIMIT 1) = '".$post->filter_params->pos."'";
        if($post->filter_params->loc!="") $sql.= " AND (SELECT data.display_text FROM user_work JOIN data on user_work.site_location=data.id WHERE user_work.user_id = pf.employee_id ORDER BY user_work.workdate_added DESC LIMIT 1) = '".$post->filter_params->loc."'";
        if($post->filter_params->emp!="") $sql.= " AND (SELECT CONCAT(user.firstname,' ',user.lastname) FROM user WHERE user.id=pf.employee_id) = '".$post->filter_params->emp."' ";
        $e->select(false, false, $sql);
        while ($e->getRow()) {
            if($post->review_status=="pending" && $e->row['form_status']=="pending" && $e->row['frequency']*30/4>= $this->getDayDifference($e->row['assessment_date']))
                array_push($data, $e->row);
            else if($post->review_status=="overdue" && $e->row['form_status']=="pending" && $e->row['frequency']*30/4>=$this->getDayDifference($e->row['assessment_date']))
                array_push($data, $e->row);
            else if($post->review_status=="completed" && $e->row['form_status']=="completed")
                array_push($data, $e->row);
        }
        $locations = array();
        $d = new db("data");
        $sql = "SELECT display_text FROM data WHERE type='sitelocation' AND account_id=".$post->user_data->account_id." ORDER BY display_text ASC";
        $d->select(false, false, $sql);
        while($d->getRow()){
            array_push($locations, $d->row['display_text']);
        }

        $result = array();
        foreach($locations as $location){
            $result[$location] = 0;
            foreach($data as $one){
                if($one['site_location']==$location)
                    $result[$location]++;
            }
        }
        echo json_encode(array('location_report' => $result));
    }
    public function getAnalyticsOfPerformanceEmployees($post){
        $data = array();
        $post->filter_params = json_decode($post->filter_params);
        if($post->filter_params->filter_fromdate!="") $post->filter_params->filter_fromdate = date('Y-m-d', strtotime("+1 day", strtotime($post->filter_params->filter_fromdate)));
        if($post->filter_params->filter_todate!="") $post->filter_params->filter_todate = date('Y-m-d', strtotime("+1 day", strtotime($post->filter_params->filter_todate)));
        //echo $post->showMode;
        $e = new db("form_reviews");
        $sql = "SELECT DISTINCT CONCAT(u.firstname, ' ', u.lastname) as employee_name, fr.assessment_date, pf.frequency, fr.form_status,
                    (SELECT data.display_text FROM user_work JOIN data on user_work.site_location=data.id WHERE user_work.user_id = pf.employee_id ORDER BY user_work.workdate_added DESC LIMIT 1) as site_location,
                    (SELECT data.display_text FROM user_work JOIN data on user_work.department=data.id WHERE user_work.user_id = pf.employee_id ORDER BY user_work.workdate_added DESC LIMIT 1) as department,
                    (SELECT data.display_text FROM user_work JOIN data on user_work.position=data.id WHERE user_work.user_id = pf.employee_id ORDER BY user_work.workdate_added DESC LIMIT 1) as position
                FROM form_reviews AS fr 
                INNER JOIN performance_forms AS pf ON pf.id = fr.p_forms_id 
                INNER JOIN user AS u ON u.id=pf.employee_id";

        if($post->review_status=="pending")$sql.= " WHERE fr.form_status='pending' AND fr.assessment_date > '".date('Y-m-d')."'";
        else if($post->review_status=="overdue")$sql.= " WHERE fr.form_status='pending' AND fr.assessment_date <= '".date('Y-m-d')."'";
        else if($post->review_status=="completed")$sql.= " WHERE fr.form_status='completed'";
        if($post->showMode==0) $sql.=" AND u.active=1 ";
        if($post->filter_params->filter_fromdate!="") $sql.= " AND fr.assessment_date>='".$post->filter_params->filter_fromdate."'";
        if($post->filter_params->filter_todate!="") $sql.= " AND fr.assessment_date<='".$post->filter_params->filter_todate."'";
        if($post->filter_params->dep!="") $sql.= " AND (SELECT data.display_text FROM user_work JOIN data on user_work.department=data.id WHERE user_work.user_id = pf.employee_id ORDER BY user_work.workdate_added DESC LIMIT 1) = '".$post->filter_params->dep."'";
        if($post->filter_params->pos!="") $sql.= " AND (SELECT data.display_text FROM user_work JOIN data on user_work.position=data.id WHERE user_work.user_id = pf.employee_id ORDER BY user_work.workdate_added DESC LIMIT 1) = '".$post->filter_params->pos."'";
        if($post->filter_params->loc!="") $sql.= " AND (SELECT data.display_text FROM user_work JOIN data on user_work.site_location=data.id WHERE user_work.user_id = pf.employee_id ORDER BY user_work.workdate_added DESC LIMIT 1) = '".$post->filter_params->loc."'";
        if($post->filter_params->emp!="") $sql.= " AND (SELECT CONCAT(user.firstname,' ',user.lastname) FROM user WHERE user.id=pf.employee_id) = '".$post->filter_params->emp."' ";
        $sql.= " GROUP BY employee_name";
        $e->select(false, false, $sql);
        while ($e->getRow()) {
            if($post->review_status=="pending" && $e->row['form_status']=="pending" && $e->row['frequency']*30/4>= $this->getDayDifference($e->row['assessment_date']))
                array_push($data, $e->row);
            else if($post->review_status=="overdue" && $e->row['form_status']=="pending" && $e->row['frequency']*30/4>=$this->getDayDifference($e->row['assessment_date']))
                array_push($data, $e->row);
            else if($post->review_status=="completed" && $e->row['form_status']=="completed")
                array_push($data, $e->row);
        }
        $locations = array();
        $d = new db("data");
        $sql = "SELECT display_text FROM data WHERE type='sitelocation' AND account_id=".$post->user_data->account_id." ORDER BY display_text ASC";
        $d->select(false, false, $sql);
        while($d->getRow()){
            array_push($locations, $d->row['display_text']);
        }

        $result = array();
        foreach($locations as $location){
            $result[$location] = 0;
            foreach($data as $one){
                if($one['site_location']==$location)
                    $result[$location]++;
            }
        }
        echo json_encode(array('location_report' => $result));
    }
    public function getAnalyticsOfEmployeePerformanceReviews($post){
        $post->filter_params = json_decode($post->filter_params);
        if($post->filter_params->filter_fromdate!="") $post->filter_params->filter_fromdate = date('Y-m-d', strtotime("+1 day", strtotime($post->filter_params->filter_fromdate)));
        if($post->filter_params->filter_todate!="") $post->filter_params->filter_todate = date('Y-m-d', strtotime("+1 day", strtotime($post->filter_params->filter_todate)));
        if($post->filter_params->emp!=""){
            $e = new db("form_reviews");
            $sql = "SELECT CONCAT(u.firstname, ' ', u.lastname) as employee_name, fr.*, pf.employee_id, pf.frequency, pf.manager_id, pf.questions 
                    FROM form_reviews AS fr 
                    INNER JOIN performance_forms AS pf ON pf.id = fr.p_forms_id 
                    JOIN user u ON u.id=pf.employee_id 
                    WHERE CONCAT(u.firstname, ' ', u.lastname)='".$post->filter_params->emp."'";
            if($post->filter_params->filter_fromdate!="")$sql.= " AND fr.completed_date>='".$post->filter_params->filter_fromdate."'";
            if($post->filter_params->filter_todate!="")$sql.= " AND fr.completed_date<='".$post->filter_params->filter_todate."'";
            $sql.= " AND fr.form_status='completed' ORDER BY fr.completed_date ASC";
            $e->select(false, false, $sql);
            $data = array();
            while($e->getRow()){
                array_push($data, $e->row);
            }
            $q = new db("standard_questions");
            $sql = "SELECT question_text FROM standard_questions";
            $q->select(false, false, $sql);
            $standard_questions = array();
            while($q->getRow()){
                array_push($standard_questions, $q->row['question_text']);
            }
            $emp_questions = null;
            $reviews = array();
            if(count($data)!=0){
                $one = $data[0];
                $emp_questions = $standard_questions;
                $temp = explode("~#", $one['questions']);
                for($i=0; $i<count($temp)-1; $i++){
                    array_push($emp_questions, $temp[$i]);
                }
                for($i=0; $i<count($emp_questions); $i++){
                    $reviews[$emp_questions[$i]] = array();
                    foreach($data as $one){
                        $scores = explode(",", $one['scores']);
                        array_push($reviews[$emp_questions[$i]], array('score'=>$scores[$i], 'date'=>date('d-m-Y', strtotime($one['completed_date']))));
                    }
                }
            }
            return json_encode(array('questions'=>$emp_questions, 'reviews'=>$reviews));
        }
        return false;
    }
    public function getAnalyticsOfEmployeePerformanceScore($post){
        $post->filter_params = json_decode($post->filter_params);

        if($post->filter_params->filter_fromdate!="") $post->filter_params->filter_fromdate = date('Y-m-d', strtotime("+1 day", strtotime($post->filter_params->filter_fromdate)));
        if($post->filter_params->filter_todate!="") $post->filter_params->filter_todate = date('Y-m-d', strtotime("+1 day", strtotime($post->filter_params->filter_todate)));
        $e = new db("form_reviews");
        $sql = "SELECT CONCAT(u.firstname, ' ', u.lastname) as employee_name, fr.*, pf.employee_id, pf.frequency, pf.manager_id, pf.questions, 
                    (SELECT data.display_text FROM user_work JOIN data on user_work.site_location=data.id WHERE user_work.user_id = pf.employee_id ORDER BY user_work.workdate_added DESC LIMIT 1) as site_location,
                    (SELECT data.display_text FROM user_work JOIN data on user_work.department=data.id WHERE user_work.user_id = pf.employee_id ORDER BY user_work.workdate_added DESC LIMIT 1) as department,
                    (SELECT data.display_text FROM user_work JOIN data on user_work.position=data.id WHERE user_work.user_id = pf.employee_id ORDER BY user_work.workdate_added DESC LIMIT 1) as position
                FROM form_reviews AS fr 
                INNER JOIN performance_forms AS pf ON pf.id = fr.p_forms_id 
                JOIN user u ON u.id=pf.employee_id 
                WHERE fr.form_status='completed'";
        if($post->filter_params->filter_fromdate!="")$sql.= " AND fr.completed_date>='".$post->filter_params->filter_fromdate."'";
        if($post->filter_params->filter_todate!="")$sql.= " AND fr.completed_date<='".$post->filter_params->filter_todate."'";
        if($post->filter_params->dep!="") $sql.= " AND (SELECT data.display_text FROM user_work JOIN data on user_work.department=data.id WHERE user_work.user_id = pf.employee_id ORDER BY user_work.workdate_added DESC LIMIT 1) = '".$post->filter_params->dep."'";
        if($post->filter_params->pos!="") $sql.= " AND (SELECT data.display_text FROM user_work JOIN data on user_work.position=data.id WHERE user_work.user_id = pf.employee_id ORDER BY user_work.workdate_added DESC LIMIT 1) = '".$post->filter_params->pos."'";
        if($post->filter_params->loc!="") $sql.= " AND (SELECT data.display_text FROM user_work JOIN data on user_work.site_location=data.id WHERE user_work.user_id = pf.employee_id ORDER BY user_work.workdate_added DESC LIMIT 1) = '".$post->filter_params->loc."'";
        if($post->filter_params->emp!="") $sql.= " AND (SELECT CONCAT(user.firstname,' ',user.lastname) FROM user WHERE user.id=pf.employee_id) = '".$post->filter_params->emp."' ";
        if($post->show_mode==0) $sql.=" AND u.active=1";
        $e->select(false, false, $sql);
        $data = array();
        $scores = array();
        $employees = array();
        while($e->getRow()){
            $score = $this->calcEmployeePerformanceReviewScore($e->row['scores']);
            if(!isset($data[$e->row['employee_name']]))
                $data[$e->row['employee_name']] = array();
            array_push($data[$e->row['employee_name']], $score) ;
        }
        if(count($data)!=0){
            foreach($data as $key=>$one){
                $sum = 0;
                for($i=0; $i<count($one); $i++)
                    $sum+=$one[$i];
                $scores[$key] = $sum;
                array_push($employees, $key);
            }
        }
        echo json_encode(array('employees'=>$employees, 'scores'=>$scores));
    }
    public function getInTimeCompletedReviews($post){
        $data = array();
        $post->filter_params = json_decode($post->filter_params);
        if($post->filter_params->filter_fromdate!="") $post->filter_params->filter_fromdate = date('Y-m-d', strtotime("+1 day", strtotime($post->filter_params->filter_fromdate)));
        if($post->filter_params->filter_todate!="") $post->filter_params->filter_todate = date('Y-m-d', strtotime("+1 day", strtotime($post->filter_params->filter_todate)));
        //echo $post->showMode;
        $e = new db("form_reviews");
        $sql = "SELECT CONCAT(u.firstname, ' ', u.lastname) as employee_name, fr.assessment_date, fr.completed_date, pf.frequency, fr.form_status,
                    (SELECT data.display_text FROM user_work JOIN data on user_work.site_location=data.id WHERE user_work.user_id = pf.employee_id ORDER BY user_work.workdate_added DESC LIMIT 1) as site_location,
                    (SELECT data.display_text FROM user_work JOIN data on user_work.department=data.id WHERE user_work.user_id = pf.employee_id ORDER BY user_work.workdate_added DESC LIMIT 1) as department,
                    (SELECT data.display_text FROM user_work JOIN data on user_work.position=data.id WHERE user_work.user_id = pf.employee_id ORDER BY user_work.workdate_added DESC LIMIT 1) as position
                FROM form_reviews AS fr 
                INNER JOIN performance_forms AS pf ON pf.id = fr.p_forms_id 
                INNER JOIN user AS u ON u.id=pf.employee_id 
                WHERE fr.form_status='completed' AND fr.assessment_date>fr.completed_date";
        if($post->showMode==0) $sql.=" AND u.active=1 ";
        if($post->filter_params->filter_fromdate!="") $sql.= " AND fr.assessment_date>='".$post->filter_params->filter_fromdate."'";
        if($post->filter_params->filter_todate!="") $sql.= " AND fr.assessment_date<='".$post->filter_params->filter_todate."'";
        if($post->filter_params->dep!="") $sql.= " AND (SELECT data.display_text FROM user_work JOIN data on user_work.department=data.id WHERE user_work.user_id = pf.employee_id ORDER BY user_work.workdate_added DESC LIMIT 1) = '".$post->filter_params->dep."'";
        if($post->filter_params->pos!="") $sql.= " AND (SELECT data.display_text FROM user_work JOIN data on user_work.position=data.id WHERE user_work.user_id = pf.employee_id ORDER BY user_work.workdate_added DESC LIMIT 1) = '".$post->filter_params->pos."'";
        if($post->filter_params->loc!="") $sql.= " AND (SELECT data.display_text FROM user_work JOIN data on user_work.site_location=data.id WHERE user_work.user_id = pf.employee_id ORDER BY user_work.workdate_added DESC LIMIT 1) = '".$post->filter_params->loc."'";
        if($post->filter_params->emp!="") $sql.= " AND (SELECT CONCAT(user.firstname,' ',user.lastname) FROM user WHERE user.id=pf.employee_id) = '".$post->filter_params->emp."' ";
        $e->select(false, false, $sql);
        while ($e->getRow()) {
            $ass_date = $e->row['assessment_date'];
            $com_date = $e->row['completed_date'];
            $freq = $e->row['frequency'];
            $datetime1 = new DateTime($com_date);
            $datetime2 = new DateTime($ass_date);
            $interval = $datetime1->diff($datetime2);
            $interval = $interval->format('%a');
            if($interval<=$freq*30)
                array_push($data, $e->row);
        }
        $locations = array();
        $d = new db("data");
        $sql = "SELECT display_text FROM data WHERE type='sitelocation' AND account_id=".$post->user_data->account_id." ORDER BY display_text ASC";
        $d->select(false, false, $sql);
        while($d->getRow()){
            array_push($locations, $d->row['display_text']);
        }

        $result = array();
        foreach($locations as $location){
            $result[$location] = 0;
            foreach($data as $one){
                if($one['site_location']==$location)
                    $result[$location]++;
            }
        }
        echo json_encode(array('location_report' => $result));
    }
    public function NoReviewEply($userId, $param){
        $d      = new db('user');
        $sql = "SELECT CONCAT(u.firstname,' ',u.lastname) AS employeename,
                    (SELECT data.display_text FROM user_work JOIN data ON data.id=user_work.site_location WHERE user_work.user_id=u.id ORDER BY user_work.workdate_added DESC LIMIT 1) AS location, 
                    (SELECT data.display_text FROM user_work JOIN data ON data.id=user_work.position WHERE user_work.user_id=u.id ORDER BY user_work.workdate_added DESC LIMIT 1) AS position,
                    (SELECT data.display_text FROM user_work JOIN data ON data.id=user_work.department WHERE user_work.user_id=u.id ORDER BY user_work.workdate_added DESC LIMIT 1) AS department 
                    FROM user u
                    WHERE u.id NOT IN 
                    	(SELECT usr.id FROM user AS usr 
                    	INNER JOIN performance_forms pf ON pf.employee_id = usr.id 
                    	WHERE usr.active = :active
                    		AND usr.deleted = :notdel
                    		AND usr.account_id = :aid
                    	GROUP BY usr.id) 
                    AND u.account_id=:aid AND u.active= :active AND u.deleted=:notdel";
        $d->select(false, false, $sql, array('active' => 1, 'notdel'=> 0, 'aid' => $userId));
        $data = array();
        if ($d->numRows == 0) {return ;}
        while ($d->getRow()) {
            //var_dump($d->row) ;
            if($param->dep!="" && $d->row['department']==$param->dep)
                array_push($data, $d->row);
            else if($param->pos!="" && $d->row['position']==$param->pos)
                array_push($data, $d->row);
            else if($param->loc!="" && $d->row['location']==$param->loc)
                array_push($data, $d->row);
            else if($param->emp!="" && $d->row['employeename']==$param->emp)
                array_push($data, $d->row);
            else if($param->dep=="" && $param->pos=="" && $param->loc=="" && $param->emp=="")
                array_push($data, $d->row);
        }
        echo json_encode(array('res' => $data));
    }
    //newly added
    public function NonInjuryEventEply($userId, $param){
        $d      = new db('user');
        $sql = "SELECT u.id, CONCAT(u.firstname,' ',u.lastname) AS employeename,
                    (SELECT data.id FROM user_work JOIN data ON data.id=user_work.site_location WHERE user_work.user_id=u.id ORDER BY user_work.workdate_added DESC LIMIT 1) AS location_id, 
                    (SELECT data.display_text FROM user_work JOIN data ON data.id=user_work.site_location WHERE user_work.user_id=u.id ORDER BY user_work.workdate_added DESC LIMIT 1) AS location, 
                    (SELECT data.id FROM user_work JOIN data ON data.id=user_work.position WHERE user_work.user_id=u.id ORDER BY user_work.workdate_added DESC LIMIT 1) AS position_id,
                    (SELECT data.display_text FROM user_work JOIN data ON data.id=user_work.position WHERE user_work.user_id=u.id ORDER BY user_work.workdate_added DESC LIMIT 1) AS position,
                    (SELECT data.id FROM user_work JOIN data ON data.id=user_work.department WHERE user_work.user_id=u.id ORDER BY user_work.workdate_added DESC LIMIT 1) AS department_id,
                    (SELECT data.display_text FROM user_work JOIN data ON data.id=user_work.department WHERE user_work.user_id=u.id ORDER BY user_work.workdate_added DESC LIMIT 1) AS department
                    FROM user u
                    WHERE u.account_id=:aid AND u.active= :active AND u.deleted=:notdel";
        $d->select(false, false, $sql, array('active' => 1, 'notdel'=> 0, 'aid' => $userId));
        $data = array();
        while ($d->getRow()) {
            //var_dump($d->row) ;
            if($param->dep!="" && $d->row['department_id']==$param->dep)
                array_push($data, $d->row);
            else if($param->pos!="" && $d->row['position_id']==$param->pos)
                array_push($data, $d->row);
            else if($param->loc!="" && $d->row['location_id']==$param->loc)
                array_push($data, $d->row);
            else if($param->emp!="" && $d->row['id']==$param->emp)
                array_push($data, $d->row);
            else if($param->dep=="" && $param->pos=="" && $param->loc=="" && $param->emp=="")
                array_push($data, $d->row);
        }
        $array = array();
        $e = new db('injury_register');
        $sql = "SELECT * FROM injury_register WHERE deleted=0 AND account_id=".$userId;
        $e->select(false, false, $sql);
        while($e->getRow()){
            array_push($array, $e->row['employee_id']);
        }

        $result = array();
        foreach($data as $one){
            if(!in_array($one['id'], $array))
                array_push($result, $one);
        }
        echo json_encode(array('res' => $result));
    }
    public function NoScheduleEventEply($userId, $param){
        $d      = new db('user');
        $sql = "SELECT u.id, CONCAT(u.firstname,' ',u.lastname) AS employeename,
                    (SELECT data.display_text FROM user_work JOIN data ON data.id=user_work.site_location WHERE user_work.user_id=u.id ORDER BY user_work.workdate_added DESC LIMIT 1) AS location, 
                    (SELECT data.display_text FROM user_work JOIN data ON data.id=user_work.position WHERE user_work.user_id=u.id ORDER BY user_work.workdate_added DESC LIMIT 1) AS position,
                    (SELECT data.display_text FROM user_work JOIN data ON data.id=user_work.department WHERE user_work.user_id=u.id ORDER BY user_work.workdate_added DESC LIMIT 1) AS department 
                    FROM user u
                    WHERE u.account_id=:aid AND u.active= :active AND u.deleted=:notdel";
        $d->select(false, false, $sql, array('active' => 1, 'notdel'=> 0, 'aid' => $userId));
        $data = array();
        while ($d->getRow()) {
            //var_dump($d->row) ;
            if($param->dep!="" && $d->row['department']==$param->dep)
                array_push($data, $d->row);
            else if($param->pos!="" && $d->row['position']==$param->pos)
                array_push($data, $d->row);
            else if($param->loc!="" && $d->row['location']==$param->loc)
                array_push($data, $d->row);
            else if($param->emp!="" && $d->row['employeename']==$param->emp)
                array_push($data, $d->row);
            else if($param->dep=="" && $param->pos=="" && $param->loc=="" && $param->emp=="")
                array_push($data, $d->row);
        }
        $event_users = array();
        $e = new db('alloc_event');
        $sql = "SELECT * FROM alloc_event WHERE deleted=0 AND account_id=".$userId;
        $e->select(false, false, $sql);
        while($e->getRow()){
            $users = json_decode($e->row['user_id']);
            foreach($users as $user){
                if(!in_array($user, $event_users))
                    array_push($event_users, $user);
            }
        }
        $result = array();
        foreach($data as $one){
            if(!in_array($one['id'], $event_users))
                array_push($result, $one);
        }
        echo json_encode(array('res' => $result));
    }
    public function getFormReviews($post){
        $sql = "SELECT site_location FROM user_work WHERE user_id = ".$post->userData->id ." ORDER BY workdate_added DESC LIMIT 1";
        $e = new db("user_work");
        $e->select(false, false, $sql);
        $e->getRow();
        $site_location_id = $e->row["site_location"];
        $e = new db("form_reviews");
        $sql = "SELECT fr.*, pf.employee_id, pf.frequency, pf.manager_id, pf.questions 
                FROM form_reviews AS fr 
                INNER JOIN performance_forms AS pf ON pf.id = fr.p_forms_id
                JOIN user u ON u.id=pf.employee_id
                WHERE fr.form_status = 'pending' AND u.active=1";
        $e->select(false, false, $sql);
        $form_reviews = array();
        while($e->getRow()){
            $ek = new db("user_work");
            $sql = "SELECT site_location FROM user_work WHERE user_id = ".$e->row["employee_id"] ." ORDER BY workdate_added DESC LIMIT 1";
            $ek->select(false, false, $sql);
            $ek->getRow();
            if($ek->row["site_location"] == $site_location_id){
                $employee_name = $this->getEmployeeNameById($e->row["employee_id"]);
                $user_work = $this->getEmployeeWorkById($e->row["employee_id"]);
                $manager_name = $this->getEmployeeNameById($e->row["manager_id"]);
                $row = array("id" => $e->row["id"], "form_status" => $e->row["form_status"], "manager_name" => $manager_name["firstname"]. " " .$manager_name["lastname"],
                            "employee_name" => $employee_name["firstname"]. " " .$employee_name["lastname"],
                            "completed_date" => $e->row["completed_date"],
                            "start_date" => $user_work["start_date"],
                            "site_location" => $user_work["site_location"],
                            "questions" => $e->row["questions"],
                            "scores" => $e->row["scores"],
                            "comments" => $e->row["comments"],
                            "assessment_date" => $e->row["assessment_date"],
                            "user_status" => $this->getUserStatus($e->row["employee_id"]),
                            "frequency" => $e->row["frequency"],
                            "p_forms_id" => $e->row["p_forms_id"]);
                array_push($form_reviews, $row);
            }
        }

        $standard_questions = array();
        $emp = new db("standard_questions");
        $emp->select(false, false, "SELECT * FROM standard_questions");
        while ($emp->getRow()) {
            array_push($standard_questions, $emp->row);
        }
        echo json_encode(array("form_reviews" => $form_reviews, "standard_questions" => $standard_questions));
    }

    //newly added
    public function saveFormReview($post){
        $data = array();
        $data["scores"] = $post->scores;
        $data["comments"] = $post->comments;
        $e = new db("form_reviews");
        $sql = "SELECT fr.*, pf.account_id, pf.employee_id, pf.manager_id, pf.frequency, pf.questions FROM form_reviews AS fr 
                INNER JOIN performance_forms AS pf ON pf.id = fr.p_forms_id 
                WHERE fr.id = ".$post->id;
        $e->select(false, false, $sql);
        $e->getRow();
        date_default_timezone_get("Australia/Sydney");
        $data["completed_date"] = date("Y-m-d");
        $data["form_status"] = "completed";
        $time = strtotime(date("Y-m-d"));
        $final = date("Y-m-d", strtotime("+". $e->row["frequency"] / 1 ." month", $time));
        // $data["assessment_date"] = $post->form->start_date == null ? null : $final;
        $data2 = array();
        $data2["p_forms_id"] = $e->row["p_forms_id"];
        $data2["assessment_date"] = $final;
        $sys_log = array();
        $manager_name = $this->getEmployeeNameById($e->row["manager_id"]);
        $sys_log['who'] = $manager_name["firstname"]. " " . $manager_name["lastname"];
        $employee_name = $this->getEmployeeNameById($e->row["employee_id"]);
        $sys_log['what'] = "Completed a review for ".$employee_name["firstname"]. " ". $employee_name["lastname"];
        $sys_log['time'] = $data["completed_date"];
        $sys_log['account_id'] = $e->row["account_id"];
        $sl = new db("system_logs");
        $sl->insert($sys_log);
        $e = new db("form_reviews");
        $data1 = $data;
        $data1["id"] = $post->id;
        $e->update($data, "id = :id", false, $data1);
        $e->insert($data2);
        $this->getFormReviews($post);
        //storing system log

        $this->sendPRScoreEmail($post);
    }

    public function sendPRScoreEmail($post){
        
    } 

    //newly added
    public function deleteFormReview($post){
        $e      = new db('form_reviews');
        $e->delete('id = :id', false, array('id' => $post->form->id));
        $this->getFormReviews($post);
    }

    //newly added

    public function getFormReviewsForView($post){
        $sql = "SELECT site_location FROM user_work WHERE user_id = ".$post->userData->id ." ORDER BY workdate_added DESC LIMIT 1";
        $e = new db("user_work");
        $e->select(false, false, $sql);
        $e->getRow();
        $site_location_id = $e->row["site_location"];
        $e = new db("form_reviews");
        $sql = "SELECT fr.*, pf.id as pf_id, pf.account_id, pf.manager_id, pf.questions FROM form_reviews AS fr INNER JOIN performance_forms AS pf ON pf.id = fr.p_forms_id AND pf.employee_id = ".$post->userData->id." WHERE fr.form_status = 'completed'";
        $e->select(false, false, $sql);
        $form_reviews = array();
        while($e->getRow()){
            $ek = new db("user_work");
            $sql = "SELECT site_location FROM user_work WHERE user_id = ".$e->row["manager_id"]." ORDER BY workdate_added DESC LIMIT 1";
            $ek->select(false, false, $sql);
            $ek->getRow();
            if($ek->row["site_location"] == $site_location_id){
                $manager_name = $this->getEmployeeNameById($e->row["manager_id"]);
                $row = array("id" => $e->row["id"],
                            "manager_name" => $manager_name["firstname"]. " " .$manager_name["lastname"],
                            "assessment_date" => $this->getAssessmentDate($e->row['pf_id']),
                            "p_forms_id" => $e->row["p_forms_id"],
                            "questions" => $e->row["questions"],
                            'form_status'=>$e->row['form_status'],
                            "scores" => $e->row["scores"],
                            "comments" => $e->row["comments"],
                            "completed_date" => $e->row["completed_date"]);
                array_push($form_reviews, $row);
            }
        }

        $standard_questions = array();
        $emp = new db("standard_questions");
        $emp->select(false, false, "SELECT * FROM standard_questions");
        while ($emp->getRow()) {
            array_push($standard_questions, $emp->row);
        }
        echo json_encode(array("form_reviews" => $form_reviews, "standard_questions" => $standard_questions));
    }

     public function getAllFormReviewsForView($post){ //post: userData, showMode, ownMode, locationMode;
        $sql = "SELECT site_location FROM user_work WHERE user_id = ".$post->userData->id ." ORDER BY workdate_added DESC LIMIT 1";
        $e = new db("user_work");
        $e->select(false, false, $sql);
        $e->getRow();
        $site_location_id = $e->row["site_location"];
        $e = new db("form_reviews");
        $sql = "SELECT fr.*, pf.id as pf_id, pf.account_id, pf.manager_id, pf.employee_id, pf.questions FROM form_reviews AS fr 
                INNER JOIN performance_forms AS pf ON pf.id = fr.p_forms_id 
                INNER JOIN user as u ON u.id=pf.employee_id 
                WHERE pf.account_id=".$post->userData->account_id." AND fr.form_status = 'completed'";
        if($post->showMode==0) $sql .= " AND u.active=1";
        if($post->ownMode==0) $sql .= " AND pf.manager_id=".$post->userData->id;
        $sql.=" order by fr.completed_date desc";
        $e->select(false, false, $sql);
        $form_reviews = array();
        while($e->getRow()){
            $ek = new db("user_work");
            $sql = "SELECT site_location FROM user_work WHERE user_id = ".$e->row["employee_id"]." ORDER BY workdate_added DESC LIMIT 1";
            $ek->select(false, false, $sql);
            $ek->getRow();
            if($post->locationMode==0 && $ek->row["site_location"] == $site_location_id){
                $site_location = $this->getSiteLocation($e->row['employee_id']);
                $person_name = $this->getEmployeeNameById($e->row["employee_id"]);
                $manager_name = $this->getEmployeeNameById($e->row["manager_id"]);
                $row = array(
                    "id" => $e->row["id"],
                    "person_name" => $person_name["firstname"]. " " .$person_name["lastname"],
                    "manager_name"=> $manager_name['firstname']. " " . $manager_name['lastname'],
                    "assessment_date" => $this->getAssessmentDate($e->row['pf_id']),
                    "p_forms_id" => $e->row["p_forms_id"],
                    "questions" => $e->row["questions"],
                    'site_location'=>$site_location,
                    "scores" => $e->row["scores"],
                    "comments" => $e->row["comments"],
                    "completed_date" => $e->row["completed_date"]
                );
                array_push($form_reviews, $row);
            }
            else if($post->locationMode==1){
                $site_location = $this->getSiteLocation($e->row['employee_id']);
                $person_name = $this->getEmployeeNameById($e->row["employee_id"]);
                $manager_name = $this->getEmployeeNameById($e->row["manager_id"]);
                $row = array(
                    "id" => $e->row["id"],
                    "person_name" => $person_name["firstname"]. " " .$person_name["lastname"],
                    "manager_name"=> $manager_name['firstname']. " " . $manager_name['lastname'],
                    "assessment_date" => $this->getAssessmentDate($e->row['pf_id']),
                    "p_forms_id" => $e->row["p_forms_id"],
                    "questions" => $e->row["questions"],
                    'site_location'=>$site_location,
                    "scores" => $e->row["scores"],
                    "comments" => $e->row["comments"],
                    "completed_date" => $e->row["completed_date"]
                );
                array_push($form_reviews, $row);
            }
        }

        $standard_questions = array();
        $emp = new db("standard_questions");
        $emp->select(false, false, "SELECT * FROM standard_questions");
        while ($emp->getRow()) {
            array_push($standard_questions, $emp->row);
        }
        echo json_encode(array("form_reviews" => $form_reviews, "standard_questions" => $standard_questions));
    }
    //newly added
    public function saveReminder($post, $returnJson=true){
        $data = (array)$post->cloned_reminder;
        $employee_name = $this->getEmployeeName($data['employee_id']);
        $site_location = $this->getSiteLocation($data['employee_id']);
        switch($data['email_name']){
            case "Birthday": {
                //$due_date = $this->bdDue($data['employee_id']);
                $email_con = "Email advising an <\$employeeFirstName> <\$employeeLastname>'s birthday is approaching<p></p><br>Dear <\$preferredEmailUsersFirstName> <\$preferredEmailUserLastname>,<br>".
                "<br>This email is an automated reminder advising you that our human resources database, has identified that <\$employeeFirstName> <\$employeeLastname> who is working at <\$insertSiteLocaiton>, is having their birthday on the <\$insertDOB>.<br>".
                "<br>Please do not reply email. This is an automated message and replies are not monitored. Should you wish to unsubscribe, please contact your human resources manager.<br>".
                "<br>Kind Regards.<br>System Administrator<br>".
                "<\$companyName>";
                break;
            }
            case "VISA Check": {
                //$due_date = $this->visaDue($data['employee_id']);
                $email_con = "Email advising that an <\$employeeFirstName> <\$employeeLastname>'s visa expiry date is approaching<p></p><br>Dear <\$preferredEmailUsersFirstName> <\$preferredEmailUserLastname>,<br>".
                "<br>This email is an automated reminder advising you that our human resources database, has identified that <\$employeeFirstName> <\$employeeLastname> who is working at <\$insertSiteLocaiton>, has a visa expiry date set for <\$insertdate>.<br>".
                "<br>Please ensure you conduct the relevant VEVO check prior to this date and record the results on her human resources file.<br>".
                "<br>Please do not reply email. This is an automated message and replies are not monitored. Should you wish to unsubscribe, please contact your human resources manager.<br>".
                "<br>Kind Regards.<br>System Administrator<br>".
                "<\$companyName>";
                break;
            }
            case "Training Course Due": {
                //$due_date = $this->trainingDue($data['employee_id']);
                //$course_name = "";
                $email_con = "Email advising that an <\$employeeFirstName> <\$employeeLastname>'s training course is overdue<p></p><br>Dear <\$preferredEmailUsersFirstName> <\$preferredEmailUserLastname>,<br>".
                "<br>This email is an automated reminder advising you that our human resources database, has identified that <\$employeeFirstName> <\$employeeLastname>  who is working at <\$insertSiteLocaiton>, is currently undertaking training and development courses specifically, the course labelled <\$insertCourseName>.".
                "This course has a due date for <\$insertdate> and is not yet completed.<br>".
                "<br>Please ensure this course is completed within the allocated timeframe.<br>".
                "<br>Please do not reply email. This is an automated message and replies are not monitored. Should you wish to unsubscribe, please contact your human resources manager.<br>".
                "<br>Kind Regards.<br>System Administrator<br>".
                "<\$companyName>";
                break;
            }

            case "Safety Data Sheet Expiry": {
                //$due_date = $this->safetyDue($data['employee_id']);
                //$chemical_name = "";
                //$supplier_name = "";
                $email_con = "Email advising that a SDS is about to expire<p></p><br>Dear <\$preferredEmailUsersFirstName> <\$preferredEmailUserLastname>,<br>".
                "<br>This email is an automated reminder advising you that our Hazardous Substance Register contained within our human resources database, has identified that the chemical labelled <\$insertChemicalName> at <\$insertSiteLocaiton>, has a safety data sheet that is due to expire on <\$insertdate>.<br>".
                "<br>Please ensure you contact the supplier, <\$insertSupplierName>, prior to the due date to organise an up-to date safety data sheet and notify the human resources department via email of the details.";
                "<br>Please do not reply email. This is an automated message and replies are not monitored. Should you wish to unsubscribe, please contact your human resources manager.<br>".
                "<br>Kind Regards.<br>System Administrator<br>".
                "<\$companyName>";
                break;
            }
            case "Schedule a Service": {
                //$due_date = $this->scheduleDue($data['employee_id']);
                //$plan_name = "";
                //$serial_number = "";
                //$service_provider = "";
                //$phone_number = "";
                $email_con = "Email advising plant or equipment is due for a schedule or service<p></p><br>Dear <\$preferredEmailUsersFirstName> <\$preferredEmailUserLastname>,<br>".
                "<br>This email is an automated reminder advising you that our Asset Register contained within our human resources database, has identified that the plant/equipment called <\$insertPlantName> with the serial number of <\$insertSerialNumber> at <\$insertSiteLocaiton> has a service date scheduled for <\$insertdate>.<br>";
                "<br>Please ensure you contact the supplier, <\$insertServiceProvider> on <\$phoneNumber>, prior to the scheduled date to ensure everything is in order. Following the service, please advise the human resources department via email of the details so the asset register can be updated.";
                "<br>Please do not reply email. This is an automated message and replies are not monitored. Should you wish to unsubscribe, please contact your human resources manager.<br>".
                "<br>Kind Regards.<br>System Administrator<br>".
                "<\$companyName>";
                break;
            }
            case "Test and Tag": {
                //$due_date = $this->testDue($data['employee_id']);
                //$plan_name = "";
                //$serial_number = "";
                $email_con = "Email advising testing and tagging of an item is required<p></p><br>Dear <\$preferredEmailUsersFirstName> <\$preferredEmailUserLastname>,<br>".
                "<br>This email is an automated reminder advising you that our electrical testing and tagging register contained within our human resources database, has identified that the plant/equipment called <\$insertPlantName> with the serial number of <\$insertSerialNumber> at <\$insertSiteLocaiton> electrical testing and tagging due on <\$insertdate>.<br>";
                "<br>Please ensure you organise this prior to the due date and advise the human resources department via email of the details so the testing and tagging register can be updated.";
                "<br>Please do not reply email. This is an automated message and replies are not monitored. Should you wish to unsubscribe, please contact your human resources manager.<br>".
                "<br>Kind Regards.<br>System Administrator<br>".
                "<\$companyName>";
                break;
            }
            case "Safe Work Procedures": {
                //$due_date = $this->safeworkDue($data['employee_id']);
                $email_con = "Email advising an <\$employeeFirstName> <\$employeeLastname>'s SWP competency sign off is overdue<p></p><br>Dear <\$preferredEmailUsersFirstName> <\$preferredEmailUserLastname>,<br>".
                "<br>This email is an automated reminder advising you that our training register contained within our human resources database, has identified that that <\$employeeFirstName> <\$employeeLastname> who is working at <\$insertSiteLocaiton>, has their competency assessment due on the <\$insertdate><br>".
                "<br>Please ensure this employee has been assessed and marked as competent in all safe work procedures that they were trained in when commencing with the business. If they are not competent, they are not permitted to undertake any duties in the workplace until deemed competent by their manager. Until that point, they must be suitably monitored to ensure this is the case.<br>".
                "<br>NOTE: You are required to contact human resources immediately if you are unsure of the safe work procedures needing assessment and/or, if there any safe work procedures required, which this person has not done (for example, a new piece of equipment not yet reported to HR).<br>".
                "<br>Once, this assessment is completed in full, please advise the human resources department of the safe work procedure assessed, when it was assessed and whether they are competent in the safe work procedure.<br>".
                "<br>Please do not reply email. This is an automated message and replies are not monitored. Should you wish to unsubscribe, please contact your human resources manager.<br>".
                "<br>Kind Regards.<br>System Administrator<br>".
                "<\$companyName>";
                break;
            }

            case "Licence and Qualification": {
                //$due_date = $this->licenceDue($data['employee_id']);
                //$licence_name = "";
                $email_con = "Email advising an <\$employeeFirstName> <\$employeeLastname>'s licence or qualification is about to expire<p></p><br>Dear <\$preferredEmailUsersFirstName> <\$preferredEmailUserLastname>,<br>".
                "<br>This email is an automated reminder advising you that our license and qualification register contained within our human resources database, has identified that <\$employeeFirstName> <\$employeeLastname> who is working at <\$insertSiteLocaiton>, has their <\$licenseQualificationName> due to expire on <\$insertdate><br>".
                "<br>Please ensure the business is not adversely affected by this expired license/qualification and if required, advise the employee they are to update their <\$licenseQualificationName> qualification/license.<br>".
                "<br>Once this is updated, please email the human resources department of the details so <\$employeeFirstName> <\$employeeLastname>'s employment records can be adjusted accordingly.<br>".
                "<br>Please do not reply email. This is an automated message and replies are not monitored. Should you wish to unsubscribe, please contact your human resources manager.<br>".
                "<br>Kind Regards.<br>System Administrator<br>".
                "<\$companyName>";
                break;
            }
            case "Probation Period": {
               // $due_date = $this->probationDue($data['employee_id']);
                $email_con = "Email advising that an <\$employeeFirstName> <\$employeeLastname>'s 3 month probationary period is about to expire<p></p><br>Dear <\$preferredEmailUsersFirstName> <\$preferredEmailUserLastname>,<br>".
                "<br>This email is an automated reminder advising you that employment database contained within our human resources system, has identified that that <\$employeeFirstName> <\$employeeLastname> who is working at <\$insertSiteLocaiton>, has their 3 month probationary period due to expire on <\$insertdate><br>".
                "<br>Please ensure a probationary period assessment form was filled in for this employee (in its entirety) and emailed to the human resources before this due date so <\$employeeFirstName> <\$employeeLastname>'s employment records can be updated accordingly.<br>".
                "<br>Please do not reply email. This is an automated message and replies are not monitored. Should you wish to unsubscribe, please contact your human resources manager.<br>".
                "<br>Kind Regards.<br>System Administrator<br>".
                "<\$companyName>";
                break;
            }
            case "Qualification Period": {
                //$due_date = $this->qualificationDue($data['employee_id']);
                $email_con = "Email advising that an <\$employeeFirstName> <\$employeeLastname>'s 6 month qualification period is about to expire<p></p><br>Dear <\$preferredEmailUsersFirstName> <\$preferredEmailUserLastname>,<br>".
                "<br>This email is an automated reminder advising you that employment database contained within our human resources system, has identified that that <\$employeeFirstName> <\$employeeLastname> who is working at <\$insertSiteLocaiton>, has their 6 month qualification period due to expire on <\$insertdate><br>".
                "<br>Please ensure a second probationary period assessment form was filled in for this employee (in its entirety) and emailed to the human resources before this due date so <\$employeeFirstName> <\$employeeLastname>'s employment records can be updated accordingly.<br>".
                "<br>Be aware, after this date, the employee will be automatically be offered unconditional employment and be automatically entitlement to unfair dismissal protections and laws.<br>".
                "<br>Please do not reply email. This is an automated message and replies are not monitored. Should you wish to unsubscribe, please contact your human resources manager.<br>".
                "<br>Kind Regards.<br>System Administrator<br>".
                "<\$companyName>";
                break;
            }
            case "Injury Register": {
                //$due_date = $this->injuryDue($data['employee_id']);
                $email_con = "Email advising an outstanding item on a prior injury needs to be reviewed<p></p><br>Dear <\$preferredEmailUsersFirstName> <\$preferredEmailUserLastname>,<br>".
                "<br>This email is an automated reminder advising you that our business injury register, located in our human resources database, has identified that <\$employeeFirstName> <\$employeeLastname> who is working at <\$insertSiteLocaiton>, sustained a work related injury on <\$insertdate>.<br>".
                "<br>At the time of the injury, the likelihood of it reoccuring, was marked as <\$likelihood>(in nature) and had a severity rating of <\$severity>.<br>".
                "<br>As such, this automated reminder was created to advise you of this to ensure you are taking the appropriate measures to ensure this incident does not occur again.<br>".
                "<br>Once this risk has been remedied, please email the human resources department to ensure they update the records.<br>".
                "Please do not reply email. This is an automated message and replies are not monitored. Should you wish to unsubscribe, please contact your human resources manager.<br>".
                "<br>Kind Regards.<br>System Administrator<br>".
                "<\$companyName>";
                break;
            }
            case "Performance Review": {
                //$due_date = $this->injuryDue($data['employee_id']);
                $email_con = "Email advising <\$employeeFirstName> <\$employeeLastname> performance review is approaching<p></p><br>Dear <\$preferredEmailUsersFirstName> <\$preferredEmailUserLastname>,<br>".
                "<br>This email is an automated reminder advising you that our human resources database, has identified that <\$employeeFirstName> <\$employeeLastname> who is working at <\$insertSiteLocaiton>, has their performance review due on <\$insertdate>.<br>".
                "<br>This performance review was set for you to conduct on <\$insertdate>. Please ensure you conduct <\$employeeFirstName> <\$employeeLastname>’s review prior to this date.<br>".
                "<br>Please do not reply email. This is an automated message and replies are not monitored. Should you wish to unsubscribe, please contact your human resources manager.".
                "<br>Kind Regards.<br>System Administrator<br>".
                "<\$companyName>";
                break;
            }
        }
        $data['email_con'] = $email_con;
        date_default_timezone_set("Australia/Sydney");
        // $date = date('Y-m-d H:i:s');
        $today = date("Y-m-d H:i:s");
        $data['created_at'] = $today;
        $data['due_date'] = $today;
        $data['alerts_count'] = 0;
        //$data['employee_name'] = $this->getEmployeeName($data['employee_id']);
        $e = new db("reminders");
        $sql = "SELECT count(*) AS alerts_count FROM reminders WHERE alert_status = 1 AND employee_id =". $data["employee_id"];
        $e->select(false, false, $sql);
        $e->getRow();
        $alerts_count = $e->row["alerts_count"];
        $data["alerts_count"] = $alerts_count + 1;
        $e->insert($data);
        $e->update(array("alerts_count" => 0), "employee_id = :id", false, array("alerts_count" => $data["alerts_count"], "id" => $data["employee_id"]));
        echo json_encode(array("reminders" => $this->getReminder($data["employee_id"])));
    }

    //newly added
    public function saveEmployeeNotes($post, $returnJson=true){
        $data = (array)$post;
        $emp_notes = (array)$data['emp_notes'];
        $empNotes = $emp_notes;
        date_default_timezone_set("Australia/Sydney");
        $today = date("Y-m-d H:i:s", $emp_notes['updated_time']/1000);
        $emp_notes['updated_time'] = $today;
        
        $e = new db("user_notes");

        unset($emp_notes['log_date']);
        unset($emp_notes['tagged_user_name']);
        unset($emp_notes['entered_user_name']);
        $emp_notes['mark_c'] = isset($emp_notes['mark_c']) ? $emp_notes['mark_c'] : 0;
        $emp_notes['user_id'] = $emp_notes['employee_id'];
        $emp_notes['injury_id'] = isset($emp_notes['injury_id']) ? $emp_notes['injury_id'] : 0;
        unset($emp_notes['employee_id']);

        //$emp_notes['user_firstname'] = $emp_notes['employee_firstname'];
        unset($emp_notes['employee_firstname']);
        //$emp_notes['user_lastname'] = $emp_notes['employee_lastname'];
        unset($emp_notes['employee_lastname']);


        //print_r($emp_notes); die;
        $insert = array();
        foreach($emp_notes as $key => $val) {
            if (trim($val) === '') {
                continue;
            }
            $insert[$key] = $val;
        }
        $e->insert($insert);
        $id = $e->lastInsertId;
        $data1 = array();

        $f = new db("flags");
        $sql = "SELECT * FROM flags";
        $f->select(false, false, $sql);
        $flags = array();
        while ($f->getRow()) {
            array_push($flags, $f->row['display_text']);
        }

        $sl = new db("system_logs");

        $emp_notes['flag_id'] = (isset($emp_notes['flag_id'])) ? $emp_notes['flag_id'] : '';

        if($emp_notes['flag_id'] == ''){
            $emp_notes['flag_id'] = 0;
        } else {
            $data1['who'] = $empNotes['entered_user_name'];
            $data1['what'] = "Added a ". $flags[$empNotes['flag_id'] - 1] ." flag to ". $empNotes['employee_firstname'] ." ". $empNotes['employee_lastname'] ." file.";
            $date1['time'] = $today;
            $data1['account_id'] = $empNotes['entered_user_id'];
            $sl->insert($data1);
        }

        $empNotes['mark_c'] = isset($empNotes['mark_c']) ? $empNotes['mark_c'] : '';
        if($empNotes['mark_c'] == ''){
            $empNotes['mark_c'] = 0;
        } else {
                $data1['who'] = $empNotes['entered_user_name'];
                $data1['what'] = "Added a confidentiality flag on ". $empNotes['employee_firstname'] ." ". $empNotes['employee_lastname'] ." file.";
                $date1['time'] = $today;
                $data1['account_id'] = $empNotes['entered_user_id'];
                $sl->insert($data1);
        }
        if(isset($empNotes['tagged_user_name'])){
                $data1['who'] = $empNotes['entered_user_name'];
                $data1['what'] = "Tagged ". $empNotes["tagged_user_name"] ." call log ". $id ." on ". $empNotes['employee_firstname'] ." ". $empNotes['employee_lastname'] ." file.";
                $date1['time'] = $today;
                $data1['account_id'] = $empNotes['entered_user_id'];
                $sl->insert($data1);
        }

        $data1['who'] = $empNotes['entered_user_name'];
        $data1['what'] = "Created a call log ". $e->getInsertId() ." on ". $empNotes['employee_firstname'] ." ". $empNotes['employee_lastname'] ." file.";
        $date1['time'] = $today;
        $data1['account_id'] = $empNotes['entered_user_id'];
        $sl = new db("system_logs");
        $sl->insert($data1);
    }
    
    public function getSortedEmployeeNotes($params){
        $sort_key = $params->sort_key;
        $order = $params->reverse ? 'desc' : 'asc';
        $eid = $params->employee_id;
        $aid = $params->userData->account_id;
        $db = new db('user_notes');
        $sql = "SELECT *, entered_user_id AS UID, tagged_user_id AS TID,
                    (SELECT CONCAT(firstname,' ',lastname) FROM user WHERE id = UID) AS entered_user_name,
                    (SELECT CONCAT(firstname,' ',lastname) FROM user WHERE id = TID) AS tagged_user_name
                  FROM user_notes un
                 WHERE (account_id = ".$aid." AND user_id = ".$eid.") 
                    OR (account_id = ".$aid." AND tagged_user_id = ".$eid.")
                ORDER BY ".$sort_key." ".$order;
        $db->select(false, false, $sql);
        
        $data = array();
        while ($db->getRow()) {
            $db->row['account_id'] = $params->userData->account_id;
            array_push($data, $db->row);
        }
        return json_encode(array('logs' => $data));
    }
    //newly added

    public function updateEmployeeNotes($post, $returnJson=true){
        $data = (array)$post;
        $emp_notes = (array)$data['emp_notes'];
        unset($emp_notes['log_date']);
        $id = $data['id'];
        date_default_timezone_set("Australia/Sydney");
        $today = date("Y-m-d H:i:s", $emp_notes['updated_time']/1000);
        $emp_notes['updated_time'] = $today;
        $update_data = $emp_notes;
        $update_data['id'] = $id;
        $f = new db("flags");
        $sql = "SELECT * FROM flags";
        $f->select(false, false, $sql);
        $flags = array();
        while ($f->getRow()) {
            array_push($flags, $f->row['display_text']);
        }
        $data1 = array();
        $e = new db("user_notes");
        $sql = "SELECT *, tagged_user_id AS TID,
                    (SELECT CONCAT(firstname,' ',lastname) FROM user WHERE id = TID) as tagged_user_name
                  FROM user_notes
                 WHERE id = :id";
        $e->select(false, false, $sql, array('id' => $id));
        $empl = new db("user");
        // Get Entered Employee
        $empl->select('id = :id', false, false, array('id' => $emp_notes['entered_user_id']));
        $emp_notes['entered_user_name'] = '';
        if ($empl->numRows > 0) {
            $empl->getRow();
            $emp_notes['entered_user_name'] = $empl->firstname.' '.$empl->lastname;
        }
        // Get Tagged Employee
        $empl->select('id = :id', false, false, array('id' => $emp_notes['tagged_user_id']));
        $emp_notes['tagged_user_name'] = '';
        if ($empl->numRows > 0) {
            $empl->getRow();
            $emp_notes['tagged_user_name'] = $empl->firstname.' '.$empl->lastname;
        }
        // Get Employee
        $empl->select('id = :id', false, false, array('id' => $emp_notes['employee_id']));
        $emp_notes['employee_name'] = '';
        if ($empl->numRows > 0) {
            $empl->getRow();
            $emp_notes['employee_firstname'] = $empl->firstname;
            $emp_notes['employee_lastname'] = $empl->lastname;
        }
        $rows = array();
        while ($e->getRow()) {
            array_push($rows, $e->row);
        }
        $row = array();
        $row = $rows[0];
        $sl = new db("system_logs");
        if($emp_notes['flag_id'] == ''){
            $emp_notes['flag_id'] = 0;
        }
        if($row['flag_id'] != $emp_notes['flag_id']){
            if($emp_notes["flag_id"] != 0){
                $data1['who'] = $emp_notes['entered_user_name'];
                $data1['what'] = "Added a ". $flags[$emp_notes['flag_id'] - 1] ." flag to ". $emp_notes['employee_firstname'] ." ". $emp_notes['employee_lastname'] ." file.";
                $date1['time'] = $today;
                $data1['account_id'] = $emp_notes['entered_user_id'];
                $sl->insert($data1);
            }
            else{
                $data1['who'] = $emp_notes['entered_user_name'];
                $data1['what'] = "Removed a ". $flags[$row['flag_id'] - 1] ." flag from ". $emp_notes['employee_firstname'] ." ". $emp_notes['employee_lastname'] ." file.";
                $date1['time'] = $today;
                $data1['account_id'] = $emp_notes['entered_user_id'];
                $sl->insert($data1);
            }
        }
        if($emp_notes['mark_c'] == ''){
            $emp_notes['mark_c'] = 0;
        }
        if($row['mark_c'] != $emp_notes['mark_c']){
            if($emp_notes["mark_c"] != 0){
                $data1['who'] = $emp_notes['entered_user_name'];
                $data1['what'] = "Added a confidentiality flag on ". $emp_notes['employee_firstname'] ." ". $emp_notes['employee_lastname'] ." file.";
                $date1['time'] = $today;
                $data1['account_id'] = $emp_notes['entered_user_id'];
                $sl->insert($data1);
            }
            else{
                $data1['who'] = $emp_notes['entered_user_name'];
                $data1['what'] = "Removed a confidentiality flag from ". $emp_notes['employee_firstname'] ." ". $emp_notes['employee_lastname'] ." file.";
                $date1['time'] = $today;
                $data1['account_id'] = $emp_notes['entered_user_id'];
                $sl->insert($data1);
            }
        }
        if(!isset($emp_notes['tagged_user_name'])){
            $emp_notes['tagged_user_name'] = $row['tagged_user_name'];
            $update_data['tagged_user_name'] = $row['tagged_user_name'];
        }
        if($row['tagged_user_name'] != $emp_notes['tagged_user_name']){
            if($emp_notes["tagged_user_name"] != ''){
                $data1['who'] = $emp_notes['entered_user_name'];
                $data1['what'] = "Tagged ". $emp_notes["tagged_user_name"] ." call log ". $id ." on ". $emp_notes['employee_firstname'] ." ". $emp_notes['employee_lastname'] ." file.";
                $date1['time'] = $today;
                $data1['account_id'] = $emp_notes['entered_user_id'];
                $sl->insert($data1);
            }
            else{
                $data1['who'] = $emp_notes['entered_user_name'];
                $data1['what'] = "Removed ". $row["tagged_user_name"] ." Tag from call log ". $id ." on ". $emp_notes['employee_firstname'] ." ". $emp_notes['employee_lastname'] ." file.";
                $date1['time'] = $today;
                $data1['account_id'] = $emp_notes['entered_user_id'];
                $sl->insert($data1);
            }
        }
        $data1['who'] = $emp_notes['entered_user_name'];
        $data1['what'] = "Edited call log ". $id ." on ". $emp_notes['employee_firstname'] ." ". $emp_notes['employee_lastname'] ." file.";
        $date1['time'] = $today;
        $data1['account_id'] = $emp_notes['entered_user_id'];
        //$sl->insert($data1);
        $fields = array();
        $fields['hr_issue'] = $update_data['hr_issue'];
        $fields['flag_id'] = $update_data['flag_id'];
        $fields['injury_id'] = $update_data['injury_id'];
        $fields['hr_action'] = $update_data['hr_action'];
        $fields['hr_issue_note'] = $update_data['hr_issue_note'];
        $fields['hr_action_note'] = $update_data['hr_action_note'];
        $fields['tagged_user_id'] = $update_data['tagged_user_id'];
        $fields['upload_file_path'] = $update_data['upload_file_path'];
        $fields['hour'] = $update_data['hour'];
        $fields['min'] = $update_data['min'];
        $fields['mark_c'] = $update_data['mark_c'];
        $fields['entered_user_id'] = $update_data['entered_user_id'];
        $fields['timer'] = $update_data['timer'];
        $fields['updated_time'] = $update_data['updated_time'];
        $fields['entered_user_id'] = $update_data['entered_user_id'];
        $fields['user_id'] = $update_data['employee_id'];
        $fields['account_id'] = $update_data['account_id'];
        $fields['tagged_user_id'] = $update_data['tagged_user_id'];
        $data = $fields;
        $data['id'] = $update_data['id'];
        //print_r($data);
        $e->bindParams = true;
        $e->update($fields, "id = :id", 1, $data);
    }
    //newly added
    public function startAlert($post, $returnJson=true){
        $e = new db("reminders");
        $data['alert_status'] = 0;
        $e->update($data, "id = :id", 1, array("id" => $post->id, "alert_status" => 1));
        $e->select("id = :id", false, false, array('id' => $post->id));
        $e->getRow();
        $row = $e->row;
        echo json_encode(array("reminders" => $this->getReminder($row["employee_id"])));
    }

    //newly added
    public function getReminder($emp_id){
        $e = new db("reminders");
        $data = array();
        $e->select("employee_id = :id", false, false, array('id' => $emp_id));
        while ($e->getRow()) {
            switch($e->row["email_name"]){
                case "Birthday": $e->row['description'] = "Email advising an employee’s birthday is approaching"; break;
                case "VISA Check": $e->row['description'] = "Email advising that an employee’s visa expiry date is approaching"; break;
                case "Training Course Due": $e->row['description'] = "Email advising that an employee’s training course is overdue"; break;
                case "Safety Data Sheet Expiry": $e->row['description'] = "Email advising that a SDS is about to expire"; break;
                case "Schedule a Service": $e->row['description'] = "Email advising plant or equipment is due for a schedule or service"; break;
                case "Test and Tag": $e->row['description'] = "Email advising testing and tagging of an item is required"; break;
                case "Safe Work Procedures": $e->row['description'] = "Email advising an employee’s SWP competency sign off is overdue"; break;
                case "Licence and Qualification": $e->row['description'] = "Email advising an employee’s licence or qualification is about to expire"; break;
                case "Probation Period": $e->row['description'] = "Email advising that an employee’s 3 month probationary period is about to expire"; break;
                case "Qualification Period": $e->row['description'] = "Email advising that an employee’s 6 month qualification period is about to expire"; break;
                case "Injury Register": $e->row['description'] = "Email advising an outstanding item on a prior injury needs to be reviewed."; break;
            }
            array_push($data, $e->row);
        }
        return $data;
    }

    //newly added
    public function stopAlert($post, $returnJson=true){
        $e = new db("reminders");
        $data['alert_status'] = 0;
        $e->update($data, "id = :id", 1, array("id" => $post->id, "alert_status" => 0));
        $e->select("id = :id", false, false, array('id' => $post->id));
        $e->getRow();
        $row = $e->row;
        echo json_encode(array("reminders" => $this->getReminder($row["employee_id"])));
    }

    //cron test
    public function cron(){
        $e = new db("reminders");
        $data['alert_status'] = 0;
        $e->update($data, "id = :id", 1, array("id" => 11, "alert_status" => 0));
    }

    //newly added

    public function emailUpdate($post, $returnJson=true){
        $e = new db("reminders");
        $data = array();
        $data['email_con'] = $post->email_con;
        switch($post->days_prior){
            case 0: {
                $data['days_prior'] = 1;
                $data['period'] = 0;
                break;
            }
            case 1:
            {
                $data['days_prior'] = 3;
                $data['period'] = 0;
                break;
            }
            case 2: {
                $data['days_prior'] = 7;
                $data['period'] = 0;
                break;
            }
            case 3: {
                $data['days_prior'] = 14;
                $data['period'] = 0;
                break;
            }
            case 4: {
                $data['days_prior'] = 28;
                $data['period'] = 0;
                break;
            }
            case 5: {
                $data['days_prior'] = 0;
                $data['period'] = 0;
                break;
            }
            case 6: {
                $data['days_prior'] = 0;
                $data['period'] = 1;
                break;
            }
            case 7: {
                $data['days_prior'] = 0;
                $data['period'] = 3;
                break;
            }
        }
        $data['id'] = $post->id;
        $e->update($data, "id = :id", 1, $data);
        $e->select("id = :id", false, false, array('id' => $post->id));
        $e->getRow();
        switch($e->row["email_name"]){
            case "Birthday": $e->row['description'] = "Email advising an employee’s birthday is approaching"; break;
            case "VISA Check": $e->row['description'] = "Email advising that an employee’s visa expiry date is approaching"; break;
            case "Training Course Due": $e->row['description'] = "Email advising that an employee’s training course is overdue"; break;
            case "Safety Data Sheet Expiry": $e->row['description'] = "Email advising that a SDS is about to expire"; break;
            case "Schedule a Service": $e->row['description'] = "Email advising plant or equipment is due for a schedule or service"; break;
            case "Test and Tag": $e->row['description'] = "Email advising testing and tagging of an item is required"; break;
            case "Safe Work Procedures": $e->row['description'] = "Email advising an employee’s SWP competency sign off is overdue"; break;
            case "Licence and Qualification": $e->row['description'] = "Email advising an employee’s licence or qualification is about to expire"; break;
            case "Probation Period": $e->row['description'] = "Email advising that an employee’s 3 month probationary period is about to expire"; break;
            case "Qualification Period": $e->row['description'] = "Email advising that an employee’s 6 month qualification period is about to expire"; break;
            case "Injury Register": $e->row['description'] = "Email advising an outstanding item on a prior injury needs to be reviewed."; break;

        }
        $row = $e->row;
        echo json_encode(array("reminder" => $row));
    }

    //newly added

    public function updateReminder($post, $returnJson=true){
        $data = (array)$post->cloned_reminder;
        $id = $data['id'];
        date_default_timezone_set("Australia/Sydney");
        // $date = date('Y-m-d H:i:s');
        $today = date("Y-m-d H:i:s");
        $data['created_at'] = $today;
        $e = new db("reminders");
        $e->update($data, "id = :id", 1, $data);
    }

    public function saveLQ($post, $returnJson=true){
        $data = (array)$post;
        $lq = (array)$data['lq'];
        $en = new db('user_license');
        $lq['entered_user_id'] = $lq['entered_user_id'];
        unset($lq['entered_user_id']);
        $lq['entered_user_name'] = $lq['entered_user_name'];
        unset($lq['entered_user_name']);
        $lq['user_id'] = $lq['employee_id'];
        unset($lq['employee_id']);
        $lq['user_firstname'] = $lq['employee_firstname'];
        unset($lq['employee_firstname']);
        $lq['user_lastname'] = $lq['employee_lastname'];
        unset($lq['employee_lastname']);
        $lq['date_from'] = date('Y-m-d', strtotime($lq['date_from']));
        $lq['date_expire'] = date('Y-m-d', strtotime($lq['date_expire']));
        $en->bindParams = true;
        $en->insert($lq);
    }

     public function updateLQ($post, $returnJson=true){
        $data = (array)$post;
        $lq = (array)$data['lq'];
        $id = $data['id'];
        $lq['entered_user_id'] = $lq['entered_user_id'];
        unset($lq['entered_user_id']);
        $lq['entered_user_name'] = $lq['entered_user_name'];
        unset($lq['entered_user_name']);
        $lq['user_id'] = $lq['employee_id'];
        unset($lq['employee_id']);
        $lq['user_firstname'] = $lq['employee_firstname'];
        unset($lq['employee_firstname']);
        $lq['user_lastname'] = $lq['employee_lastname'];
        unset($lq['employee_lastname']);
        $lq['date_from'] = date('Y-m-d', strtotime($lq['date_from']));
        $lq['date_expire'] = date('Y-m-d', strtotime($lq['date_expire']));
        $update_data = $lq;
        $update_data['id'] = $id;
        $e = new db("user_license");
        print_r($update_data);
        $e->bindParams = true;
        $e->update($lq, "id = :id", 1, $update_data);
    }

    //newly added
    public function getSystemLogs($post, $returnJson=true){
        $account_id = $post->account_id;
        $sl = new db("system_logs");
        $sl->select("account_id = :account_id", "time DESC", false, array('account_id' => $account_id));
        $data = array();
        while ($sl->getRow()) {
            array_push($data, $sl->row);
        }
        if ($returnJson) {
            echo json_encode(array('logs' => $data));
        } else {
            return $data;
        }
    }

    public function searchEmployee($post, $returnJson=true) {
        $keyword = $post->keyword;
        $e = new db('employee');
        $data = array();
        $sql = "SELECT * from user WHERE account_id = :aid AND (firstname LIKE :kw OR lastname LIKE :kw) ";
        $e->select("account_id = :aid AND (firstname LIKE :kw OR lastname LIKE :kw)", false, false, array('kw' => '%'.$keyword.'%', 'aid' => $post->userData->account_id));
        while ($e->getRow()) {
            array_push($data, $e->row);
        }
        if ($returnJson) {
            echo json_encode(array('employees' => $data));
        } else {
            return $data;
        }
    }
    public function searchEmployeeUser($post) {
        $users = $this->searchUser($post, false);
        $emps = $this->searchEmployee($post, false);
        $data = array_merge($users, $emps);
        $data = $users;
        echo json_encode(array('users' => $data));
    }

    public function getEmployeeList($post) {
        $ut = new db('user');
        $data = array();
        $ut->select('account_id IN (SELECT account_id FROM user WHERE id = :uid ) AND usertype_id = :type',false, false, array('uid' => $post->user_id, 'type' => $post->usertype));
        while ($ut->getRow()) {
            $ut->row['name'] = $ut->firstname.' '.$ut->lastname;
            array_push($data, $ut->row);
        }
        echo json_encode(array('users' => $data));
    }

    public function getPermissionData() {
        $data = array();
        $data['roles'] = $this->getData('usertype');
        $n      = 0;
        $m      = new db('modules');
        $sm      = new db('modules');
        $m->select("status = :status AND parent_id = :top", 'parent_id ASC, display_order ASC', false, array('status' => 1, 'top' => 0));
        while ($m->getRow()) {
            $data['modules'][$n] = array('id' => $m->id, 'text' => $m->module, 'sub' => 0);
            $n++;
            $sm->select("status = :status AND parent_id = :p", 'display_order ASC', false, array('status' => 1, 'p' => $m->id));
            while ($sm->getRow()) {
                $data['modules'][$n] = array('id' => $sm->id, 'text' => $sm->module, 'sub' => 1);
                $n++;
            }
        }
        /*
        $output = array();
        foreach($data['modules'] as $key => $obj) {
            $output[$key] = array('id' => $obj['id'], 'text' => $obj['text']);
            if (isset($obj['sub'])) {
                foreach($obj['sub'] as $k => $arr) {
                    $output[$arr['id']] = array('id' => $arr['id'], 'text' => $arr['text']);
                }
            }
        }*/
        //$data['modules'] = $output;
        echo json_encode($data);
    }
    public function savePermissions($post) {
        $p      = new db('permissions');
        $p->delete('usertype_id = :role', false, array('role' => $post->role));
        $modules = get_object_vars($post->modules);
        $types = array('read','write','delete');
        foreach($types as $k => $type) {
            foreach($modules[$type] as $key => $val) {
                if (!is_numeric($val) || $val == 0) {
                    continue;
                }
                $data = array();
                $data['module_id'] = $key;
                $data['usertype_id'] = $post->role;
                $data['_'.$type] = 1;
                $p->select('usertype_id = :ut AND module_id = :m', false, false, array('ut' => $post->role, 'm' => $key));
                if ($p->numRows == 0) {
                    $p->insert($data);
                } else {
                    $p->update(array('_'.$type => 1), 'usertype_id = :ut AND module_id = :m', 1, array('_'.$type => 1,'ut' => $post->role, 'm' => $key));
                }
            }
        }
    }
    public function getUserLoginDetail($id) {
        $user = $this->getUser($id);
        $perms = $this->getPermissions($user['user']['usertype_id'], true);
        echo json_encode(array('user' => $user, 'permissions' => $perms));
    }
    public function getPermissions($role, $returnArray=false) {
        $data   = array();
        $p      = new db('permissions');
        $p->select('usertype_id = :ut', false, false, array('ut' => $role));
        while($p->getRow()) {
            $read = ($p->_read == 1) ? $p->module_id : 0;
            $write = ($p->_write == 1) ? $p->module_id : 0;
            $delete = ($p->_delete == 1) ? $p->module_id : 0;
            $data[] = array('read' => $read,'delete' => $delete,'write' => $write,'module' => $p->module_id);
        }
        if ($returnArray) {
            return $data;
        } else {
            echo json_encode($data);
        }
    }
    public function getRoles($post) {
        $data = $this->getData('usertype');
        echo json_encode($data);
    }
    public function getStates($post) {
        $data = $this->getData('state');
        echo json_encode($data);
    }
    public function getLicenseTypes($post){
        $data = $this->getData('event_license_type', 2);
        echo json_encode($data);
    }
    private function _checkUsernameExists($username) {
        $u      = new db('user');
        $u->select('username = :u', false, false, array('u' => $username));
        return ($u->numRows > 0) ? true : false;
    }
    private function _describeTable($table, $db) {
        $data   = array();
        $a      = new db(false, $db);
        $a->select(false,false, "DESCRIBE $table", array());
        while($a->getRow()) {
            array_push($data, $a->row);
        }
        return $data;
    }
    private function _save($table, $data, $method=false, $db=null) {
        $Fields = $this->_describeTable($table, $db);
        $params = array();
        $id = 0;
        foreach($Fields as $key => $field) {
            $fldname = $field['Field'];
            if ($field['Key'] == 'PRI') {
                if (isset($data[$fldname])) {
                    $id = $data[$fldname];
                }
                continue;
            }
            if ($field['Type'] == "date") {
                if(isset($data[$fldname])) {
                    $data[$fldname] = date('Y-m-d', strtotime($data[$fldname]));//$this->_formatDateToDb($data[$fldname]);
                }
            }
            if (isset($data[$fldname])) {
                $params[$fldname] = $data[$fldname];
            }
        }
        $a = new db($table);
        if ($method == "replace") {
            $a->replace($params);
            return;
        }
        $a->bindParams = true;
        if ($id == 0) {
            $a->insert($params);
            return $a->lastInsertId;
        } else {


            unset($params['id']);
            unset($params['date_added']);
            unset($params['date_updated']);
            $data = $params;
            $data['id'] = $id;
            $a->update($params, "id = :id", 1, $data);
            return $id;
        }
    }
    public function saveEmployee($post, $returnJson=true) {
        $data = (array)$post;
        $emp = (array)$data['emp'];
        // var_dump($post);
        // exit;
        $empwork = (array)$data['empwork'];
        $uw_db = new db('user_work');
        if($emp['id']==0)
            $sql = "SELECT * FROM user_work uw JOIN user u ON u.id=uw.user_id WHERE uw.emp_code=".$empwork['emp_code']." AND u.account_id=".$emp['account_id'];
        else 
            $sql = "SELECT * FROM user_work uw JOIN user u ON u.id=uw.user_id WHERE uw.user_id!=".$emp['id']." AND uw.emp_code=".$empwork['emp_code']." AND u.account_id=".$emp['account_id'];
        $uw_db->select(false, false, $sql);
        $uw_db->getRow();
        if($uw_db->numRows>0){
            return json_encode(array('success'=>0, 'message'=>'Employee code already exists. Please use another code.'));
        }
        
        $currUser = (array)$data['currUser'];
        if($emp['dob']) $emp['dob'] = date('Y-m-d',strtotime($emp['dob']));
        else $emp['dob']=null;
        if($emp['visaexpiry']) $emp['visaexpiry'] = date('Y-m-d',strtotime($emp['visaexpiry']));
        else $emp['visaexpiry']=null;
        if (!isset($emp['usertype_id'])) {
            if (!$emp['id']) {
                $emp['usertype_id'] = 281;
            }
        }

        $emp['updated_by'] = $emp['update_by'];
        unset($emp['update_by']);

        $data = array();
        if(isset($emp['account_id']))              $data['account_id'] = $emp['account_id'];
        if(isset($emp['usertype_id']))             $data['usertype_id'] = $emp['usertype_id'];
        if(isset($emp['employee_id']))             $data['employee_id'] = $emp['employee_id'];
        if(isset($emp['username']))                $data['username'] = $emp['username'];
        if(isset($emp['password']))                $data['password'] = $emp['password'];
        //if(isset($emp['public_password']))         $data['public_password'] = $emp['public_password'];
        //else                                       $data['public_password'] = "1234567890";
        if(isset($emp['tradingname']))             $data['tradingname'] = $emp['tradingname'];
        if(isset($emp['companyname']))             $data['companyname'] = $emp['companyname'];
        if(isset($emp['abn']))                     $data['abn'] = $emp['abn'];
        if(isset($emp['firstname']))               $data['firstname'] = $emp['firstname'];
        if(isset($emp['lastname']))                $data['lastname'] = $emp['lastname'];
        if(isset($emp['address1']))                $data['address1'] = $emp['address1'];
        if(isset($emp['suburb']))                  $data['suburb'] = $emp['suburb'];
        if(isset($emp['state']))                   $data['state'] = $emp['state'];
        if(isset($emp['postcode']))                $data['postcode'] = $emp['postcode'];
        if(isset($emp['country']))                 $data['country'] = $emp['country'];
        if(isset($emp['title']))                   $data['title'] = $emp['title'];
        if(isset($emp['email']))                   $data['email'] = $emp['email'];
        if(isset($emp['telephone']))               $data['telephone'] = $emp['telephone'];
        if(isset($emp['mobile']))                  $data['mobile'] = $emp['mobile'];
        if(isset($emp['gender']))                  $data['gender'] = $emp['gender'];
        if(isset($emp['dob']))                     $data['dob'] = $emp['dob'];
        if(isset($emp['nationality']))             $data['nationality'] = $emp['nationality'];
        if(isset($emp['visatype']))                $data['visatype'] = $emp['visatype'];
        else                                       $data['visatype'] = null;
        if(isset($emp['visaexpiry']))              $data['visaexpiry'] = $emp['visaexpiry'];
        else                                       $data['visaexpiry'] = null;
        if(isset($emp['numEmployees']))            $data['numEmployees'] = $emp['numEmployees'];
        if(isset($emp['active']))                  $data['active'] = $emp['active'];
        if(isset($emp['deleted']))                 $data['deleted'] = $emp['deleted'];
        if(isset($emp['expire_date']))             $data['expire_date'] = $emp['expire_date'];
        if(isset($emp['login_attempts']))          $data['login_attempts'] = $emp['login_attempts'];
        if(isset($emp['total_logins']))            $data['total_logins'] = $emp['total_logins'];
        if(isset($emp['last_login']))              $data['last_login'] = ($emp['last_login'] == '0000-00-00 00:00:00') ?  '' : $emp['last_login'];
        if(isset($emp['can_next_login']))          $data['can_next_login'] = ($emp['can_next_login']) ? $emp['can_next_login'] : '';
        if(isset($emp['added_by']))                $data['added_by'] = $emp['added_by'];
        if(isset($emp['updated_by']))              $data['updated_by'] = $emp['updated_by'];

        $db = new db('user');
        $db->bindParams = true;
        if ($emp['id'] == 0) {
            unset($data['updated_by']);
            $db->insert($data);
            $newEmpId = $db->lastInsertId;
            /*insert into user (account_id, usertype_id, firstname, lastname, address1, suburb, state, postcode, title, email, telephone, mobile, gender, dob, nationality, visaexpiry, added_by) VALUES ("2", 281, "sss", "sss", "sss", "sss", "3", "235623", "9", "s@s.com", "457845824", "23423763134", "M", "1990-03-23", 40, "1970-01-01","2")*/
        } else {
            unset($data['added_by']);
            $params = $data;
            $params['id'] = $emp['id'];
            $db->update($data,'id = :id', 1, $params);
            $newEmpId = $emp['id'];
        }

        $fieldList = $this->_describeTable('user_work', false);
        $fields = array();
        foreach($fieldList as $key => $val) {
            array_push($fields, $val['Field']);
        }
        $workdata = array();
        foreach($empwork as $key => $val) {
            if (!in_array($key, $fields)) {
                continue;
            }
            if (in_array($key, array('start_date','end_date')) && $val!=null) {
                $val = date('Y-m-d', strtotime($val));
            }
            $workdata[$key] = $val;
        }
        $workdata['user_id'] = $newEmpId;
        $db = new db('user_work');
        $db->update(array('active'=>1), 'user_id = :uid', false, array('uid'=>$newEmpId, 'active'=>1));
        /*$sql = "UPDATE user_work SET active = 1 where active=0 and user_id=:uid";
        $e->select(false,false, $sql, array('uid'=>$newEmpId));*/
        $workdata['active'] = 0;
        $db->insert($workdata);
        /*echo $post->currUser->account_id;
        exit;*/
        return $this->getEmployeeData($post);
    }

    public function delete($post) {
        if ($post->type == "employee") {
            $e = new db('employee');
            $e->update(array('deleted' => 1), 'id = :id', 1, array('id' => $post->typeDetail->id, 'deleted' => 1));
            $employees = $this->getEmployees(array('account_id' => $post->currUser->account_id), true);
            return json_encode(array('employees' => $employees));
        } elseif ($post->type == "user") {
            $u = new db('user');
            $u->update(array('deleted' => 1), 'id = :id', 1, array('id' => $post->typeDetail->id, 'deleted' => 1));
            $data = array();
            $sql = "SELECT *, CONCAT(firstname,' ',lastname) as 'name', state as STATE, usertype_id AS UTYPE, 'user' AS `table`,
                (SELECT display_text FROM data WHERE id = STATE) as 'StateName',
                (SELECT display_text FROM data WHERE id = UTYPE) as 'UserRole',
                IF(gender='M','Male','Female') as 'gender'
                 FROM user u
                WHERE deleted = :del";
            $params = array();
            $params['del'] = 0;
            $getAllUsers = false;
            if (isset($post->admin)) {
                $getAllUsers = ($post->admin == 1) ? true : false;
            }


            $u->select(false,false, $sql, $params);
            while ($u->getRow()) {
                array_push($data, $u->row);
            }
            return json_encode($data);
        } elseif ($post->type == "event") {
            $e = new db('events');
            $e->update(array('deleted' => 1), 'id = :id', 1, array('id' => $post->typeDetail->id, 'deleted' => 1));
            return json_encode($this->getEvents(array(), true, false));
        } elseif ($post->type == "hs") {
            $e = new db('hazardous_substance');
            $e->update(array('deleted' => 1), 'id = :id', 1, array('id' => $post->typeDetail->id, 'deleted' => 1));
            $d = new stdClass();
            $d->user = new stdClass();
            $d->user->account_id = $post->currUser->account_id;
            $this->getHSData($d);
        } elseif ($post->type == "sitedata") {
            $d = new db('data');
            $d->delete('id = :id', false, array('id' => $post->typeDetail->id));
            $this->getSiteData(array());
        } elseif ($post->type == "ar") {

            $register = new register();
            $register->delete('asset_register',$post->typeDetail->id);
            return json_encode($register->getARData($post->currUser->account_id));
        } else {
            return json_encode(array());
        }
    }
    public function getEmployee($id) {
        $e = new db('user');
        $e->select('id = :id', false, false, array('id' => $id));
        $emp = $e->getRow();


        $ew = new db('user_work');
        $sql = "SELECT w.*, site_location AS SL,
                    (SELECT display_text FROM data WHERE id = SL) AS site_location_name 
                    FROM user_work w
                    WHERE w.user_id = :id
                    ORDER BY workdate_added DESC
                 LIMIT 1";
        $ew->select(false, false, $sql, array('id' => $id));
        $empwork = $ew->getRow();
        //newly added to get flag only one at present
        $en =  new db('user_notes');
        $en->select('user_id = :id AND flag_id <> :flag', false, false, array('id' => $id, 'flag' => 0));
        $flags = array();
        while ($en->getRow()) {
            array_push($flags, $en->row);
        }
        $data1 = array();
        $db = new db('injury_register');
        $sql = "SELECT *, DATE_FORMAT(incident_date,'%d-%m-%Y') AS dateOfIncident, investigated_by AS investigatedBy, remedial_priority AS remedialPriority,
                            natureofinjury_id AS NID, site_location_id AS SID,
                            (SELECT display_text FROM data WHERE id = SID) AS siteLocation, 
                            (SELECT display_text FROM data WHERE id = NID) AS natureOfInjury,
                        employee_id AS EID, (SELECT CONCAT(firstname,' ',lastname) FROM user WHERE id = EID) AS injuredName
                 FROM injury_register 
                WHERE account_id = :aid               
                AND employee_id = :eid
                AND deleted = :del
                AND is_complete = :complete";
        $db->select(false, false, $sql, array('aid' => $emp['account_id'], 'eid' => $id, 'del' => 0, 'complete' => 0));
        while ($db->getRow()) {
            if(!$db->row['lost_time']) $db->row['lost_time'] =  '00:00';
            $status = 'Active';
            $workcover_date = $db->row['workcover_date'];
            $closed_date = $db->row['closed_date'];
            $today = date('Y-m-d');
            if($closed_date != '' && $closed_date < $today) $status = 'Closed';
            else{
                if($workcover_date != '' && $workcover_date < $today) $status = "Overdue";
            }
            $db->row['status'] = $status;
            if($db->row['insurer_notified']==1){
                array_push($data1, array('injury_id'=>$db->row['id'], 'injury_date'=>date('d.m.Y', strtotime($db->row['incident_date']))));
            }
        }
        return array('emp' => $emp, 'empwork' => $empwork, 'flags' => $flags, 'incident_dates' => $data1);
    }
    public function getUser($id) {
        $u      = new db('user');
        $sql = "SELECT *, CONCAT(firstname,' ',lastname) as 'name', state as STATE,
                (SELECT display_text FROM data WHERE id = STATE) as 'StateName'
                 FROM user u
                WHERE id = :id";
        $u->select(false,false, $sql, array('id' => $id));
        $u->getRow();
        $u->row['dob'] = date('d-m-Y', strtotime($u->row['dob']));
        return array('user' => $u->row);
    }
    public function get($type, $id) {
        switch($type) {
            case 'employee': $data = $this->getEmployee($id); break;
            case 'user': $data = $this->getUser($id); break;
            case 'hs': $data = $this->getHazardousSubsatance($id); break;
            case 'ar': $reg = new register();
                       $data = $reg->getARData(false, $id); break;
            case 'sitedata': $data = $this->getSitewideData($id); break;
        }
        return json_encode($data);
    }
    public function getSitewideData($id) {
        $db = new db('data');
        $db->select('id = :id', false, false, array('id' => $id));
        $db->getRow();
        return $db->row;
    }
    public function getHazardousSubsatance($id) {
        $db = new db('hazardous_substance');
        $db->select('id = :id', false, false, array('id' => $id));
        $db->getRow();
        return $db->row;
    }
    private function _formatDateToDb($date) {
        if (!$date) {
            return '0000-00-00';
        }
        if (strpos($date, 'T') === false) {
            $a = explode('-', $date);
            return $a[2]."-".$a[1]."-".$a[0];
        } else {
            $a = explode('T', $date);
            return $a[0];
        }
    }
    private function _formatDateFromDb($date) {
        if (!$date || $date == '0000-00-00') {
            return '';
        }

        $date = str_replace('/', '-', $date);
        return date('Y-m-d', strtotime($date));
    }
    public function saveUser($post) {
        $data = get_object_vars($post->user);
        if (isset($data['password'])) {
            if ($data['password']!='') {

                $data['password'] = md5($data['password']);
            } else {
                unset($data['password']);

            }
        }
        if (isset($data['username']) && !isset($data['id'])) {
            if ($this->_checkUsernameExists($data['username'])) {
                return json_encode(array('success' => 0, 'message' => 'Username already exists. Please choose another.'));

            }
        }
        $newAccount = (isset($data['account_id']) && intval($data['account_id']) > 0) ? false : true;
        if(!isset($data['id'])){
            $userId = $this->_save('user', $data);

            $users_db = new db('user');
            $sql = "SELECT max(account_id) as max_account_id FROM user";
            $users_db->select(false, false, $sql);
            $result = $users_db->getRow();
            $account_id = $result['max_account_id']+1;
            // If we're creating a brand new account
            if ($newAccount) {
                $added_by = (isset($post->userData->id)) ? $post->userData->id : 0;
                $this->_save('user', array('account_id' => $account_id, 'id' => $userId, 'added_by'=>$added_by));
                $users = $this->getUsers(array(), true, false);
                return json_encode(array('success' => 1, 'message' => 'User account has been created successfully.', 'users'=>$users));
            }
        }

        $data['updated_by'] = (isset($post->userData->id)) ? $post->userData->id : 0;
        $this->_save('user', $data);


        return json_encode(array('users' => $this->getUsers(array(), true, false), 'success' => 1, 'message' => 'User account has been updated successfully'));
    }

    // updated by Alex-cobra -20-04-13
    public function saveChildUser($post) {
        $data = get_object_vars($post->user);

        if(isset($data['firstname']) && isset($data['lastname']) && isset($data['username']) && isset($data['email']) && isset($data['password'])) {
            if($data['firstname'] == '' || $data['lastname'] == '' || $data['username'] == '' || $data['email'] == '' || $data['password'] == '') {
                return json_encode(array('success' => 0, 'message' => 'Please input user information correctly!'));
            }
        }
        else {
            return json_encode(array('success' => 0, 'message' => 'Please input user information correctly!'));
        }

        if (isset($data['password'])) {
            if ($data['password']!='') {
                $data['password'] = md5($data['password']);
            } else {
                unset($data['password']);
            }
        }

        $params = array();
        $params['id'] = $data['id'];
        $params['account_id'] = $data['account_id'];
        $params['firstname'] = $data['firstname'];
        $params['lastname'] = $data['lastname'];
        $params['username'] = $data['username'];
        $params['email'] = $data['email'];
        if (isset($data['password'])) {
            $params['password'] = $data['password'];
        }
        $params['usertype_id'] = $data['usertype_id'];
        if ($params['id'] > 0) {
            $params['updated_by'] = $data['updated_by'];
            $userId = $this->_save('user', $params);
            return json_encode(array('users' => $this->getUsers(array(), true, false), 'success' => 1, 'message' => 'User account has been updated successfully.'));
        } else {
            if (isset($data['username'])) {
                if ($this->_checkUsernameExists($params['username'])) {
                    return json_encode(array('users' => $this->getUsers(array(), true, false),'success' => 0, 'message' => 'Username already exists. Please choose another.'));
                }
            }
            $params['added_by'] = $data['added_by'];

            $userId = $this->_save('user', $params);
            return json_encode(array('users' => $this->getUsers(array(), true, false), 'success' => 1, 'message' => 'User account has been created successfully.'));
        }
    }
    // // // // //

    public function updateUser($post) {
        $data   = array();
        $data = get_object_vars($post->user);
        if (isset($data['password'])) {
            if ($data['password']!='') {
                //$data['public_password'] = $data['password'];
                $data['password'] = md5($data['password']);
            } else {
                unset($data['password']);
                //unset($data['public_password']);
            }
        }
        $this->_save('user', $data);
        return json_encode(array('success' => 1));
    }
    public function releaseLock($post) {
        $u = new db('user');
        $u->update(array('login_attempts' => 0, 'can_next_login' => 0), 'id = :id', 1, array('login_attempts' => 0, 'can_next_login' => 0, 'id' => $post->userId));
        echo json_encode(array('success' => 1, 'message' => 'Lock has been released successfully.'));
    }
    public function activateEmployee($post) {
        $e = new db('user');
        $e->update(array('active' => 1), 'id = :id', 1, array('active' => $post['status'], 'id' => $post['employeeId']));

        $uwdb = new db('user_work');
        $sql = "UPDATE user_work SET end_date=null WHERE user_id=:id ORDER BY workdate_added DESC LIMIT 1";
        $uwdb->select(false, false, $sql, array('id'=>$post['employeeId']));
        if($e->rowsAffected)
            echo json_encode(array('success' => 1));
    }
    public function getLQDetailInfo($post){
        $LQName = $post->LQName;
        $e = new db('events');
        $sql = "SELECT * FROM events WHERE event_name='".$LQName."'";
        $e->select(false, false, $sql);
        $result = $e->getRow();
        return json_encode(array('detail'=>$result));
    }
    public function getScheduleByID($post){
        $e = new db('alloc_event');
        $sql = "SELECT e.event_name, e.event_desc, ae.user_id, ae.startsAt, ae.endsAt FROM alloc_event ae JOIN events e ON e.id=ae.event_id WHERE ae.deleted=0 AND ae.id=".$post->allocatedEvent_id;
        $e->select(false, false, $sql);
        $result = array();
        while($e->getRow()){
            $row = $e->row;
            $users = json_decode($row['user_id']);
            $usernames = array();
            $d = new db('user');
            foreach($users as $user_id){
                $sql = "SELECT CONCAT(firstname, ' ', lastname) as employee_name FROM user WHERE id=".$user_id;
                $d->select(false, false, $sql);
                while($d->getRow()){
                    array_push($usernames, $d->row['employee_name']);
                }
            }
            $row['names'] = json_encode($usernames);
            array_push($result, $row);
        }
        return json_encode(array('getSchedule'=>$result));
    }
    public function getSchedulerByUser($params) {
        $d = new db('user');
        $sql = "SELECT d.display_text as location
                FROM user u 
                JOIN user_work uw ON u.id=uw.user_id
                JOIN data d ON d.id=uw.site_location
                WHERE u.id=".$params->userData->id."
                ORDER BY uw.workdate_added DESC
                LIMIT 1";
        $d->select(false, false, $sql);
        $d->getRow();
        $location = $d->row['location'];
        $e = new db('user_license');
        $sql = "SELECT u.id AS UID, CONCAT(u.firstname, ' ', u.lastname) as Name, ul.license_name as EventName,  
                (SELECT d.display_text FROM user_work uw JOIN data d ON d.id=uw.site_location WHERE uw.user_id=UID ORDER BY uw.workdate_added DESC LIMIT 1) as SiteLocation,
                (SELECT d.display_text FROM user_work uw JOIN data d ON d.id=uw.department WHERE uw.user_id=UID ORDER BY uw.workdate_added DESC LIMIT 1) as Department,
                ul.date_from, ul.lqstatus
                FROM user_license ul 
                JOIN user u ON u.id=ul.user_id 
                WHERE ul.alloc_event_id>0 AND u.account_id=".$params->userData->id;
        if($params->showMode==0) $sql.=" AND u.active=1";
        $e->select(false, false, $sql);
        $result = array();
        while($e->getRow()){
            $one['Name'] = $e->row['Name'];
            $one['EventName'] = $e->row['EventName'];
            $one['SiteLocation'] = $e->row['SiteLocation'];
            $one['Department'] = $e->row['Department'];
            $day_diff = round((strtotime($e->row['date_from'])-strtotime(date('Y-m-d')))/86400);
            $one['DaysUntilCourse'] = $day_diff>=0 ? $day_diff : "N/A";
            if($e->row['lqstatus']==1) $one['Status'] = "Attended";
            if($e->row['lqstatus']=="-1") $one['Status'] = "Not attended";
            if($e->row['lqstatus']==0) $one['Status'] = "Pending";
            $one['DateOfCourse'] = date('d-m-Y', strtotime($e->row['date_from']));
            if(($one['SiteLocation']==$location && $params->locationMode==0) || $params->locationMode==1)
                array_push($result, $one);
        }
        return json_encode($result);
    }
    public function getAllocatedEvents($post){
        $e = new db('alloc_event');
        $sql = "SELECT ae.id, ae.event_id, e.event_name as title, ae.class_limit, ae.user_id, ae.startsAt, ae.endsAt 
                FROM alloc_event ae 
                JOIN events e ON e.id=ae.event_id 
                WHERE ae.deleted=0 AND e.deleted=0";
        if($post->userData->account_id!=1)$sql.= " AND ae.account_id=".$post->userData->account_id;
        // if($post->showMode==0)
        //     $sql.=" AND u.active=1";
        $e->select(false, false, $sql);
        $allocEvents = array();
        while($e->getRow()){
            $oneRow = $e->row;
            if(count($post->selected_emp)!=0){
                $filter_users = $post->selected_emp;
                $users = json_decode($e->row['user_id']);
                foreach($filter_users as $fuser){
                    if(in_array($fuser, $users)>0)
                        array_push($allocEvents, $e->row);
                }
            }
            else array_push($allocEvents, $e->row);
            /*$emps = json_decode($e->row['user_id']);

            $rowId = $oneRow['id'];
            foreach($emps as $emp){
                $sql = "SELECT e.event_name as license_name, e.trainer_company as rto, e.course_cost as cost, d.display_text as license_type, ae.startsAt as date_from, ae.endsAt as date_expire
                        FROM alloc_event ae
                        JOIN events e ON e.id=ae.event_id
                        JOIN data d ON d.id=e.event_type
                        WHERE ae.id=".$rowId;
                $e->select(false, false, $sql);
                $user_license = $e->getRow();
                $sql = "SELECT account_id, firstname, lastname FROM user WHERE id=".$emp;
                $e->select(false, false, $sql);
                $row = $e->getRow();
                $user_license['user_id'] = $emp;
                $user_license['state'] = 2; // New South Wales
                $user_license['user_firstname'] = $row['firstname'];
                $user_license['user_lastname'] = $row['lastname'];
                $user_license['account_id'] = $row['account_id'];
                $dd = new db('user_license');
                $sql = "INSERT INTO user_license
                (license_name, license_type, rto, cost, state, user_id, user_firstname, user_lastname, account_id, date_from, date_expire)
                VALUES ('".$user_license['license_name']."', '".$user_license['license_type']."', '".$user_license['rto']."', '".$user_license['cost']."', '".$user_license['state']."', '".$user_license['user_id']."', '".$user_license['user_firstname']."', '".$user_license['user_lastname']."', '".$user_license['account_id']."', '".$user_license['date_from']."', '".$user_license['date_expire']."')";
                var_dump($sql);
                $dd->select(false, false, $sql);
            }*/
        }
        //exit;
        return json_encode(array('alloc_events' => $allocEvents));
    }
    public function deleteAllocatedEvent($post){
        $e = new db('alloc_event');
        $sql = "UPDATE alloc_event SET deleted=1 WHERE id=".$post->del_id;
        $e->select(false, false, $sql);
        $sql = "SELECT * FROM alloc_event WHERE account_id=".$post->currUser->account_id." AND deleted=0";
        $e->select(false, false, $sql);
        $result = array();
        while($e->getRow()){
            array_push($result, $e->row);
        }
        return json_encode(array('alloc_events'=>$result));
    }
    public function sendEmailToAttendants($post){
        $emps = $post->email_attendees;
        ///////////////////////////////////sending emails///////////////////////////////////////////////////
        $account_id = $post->currUser->account_id;
        if(count($emps)!=0){
            foreach($emps as $emp){
                $e = new db("user");
                $sql = "SELECT u.*, uw.*, d.display_text as location_text FROM user u JOIN user_work uw ON u.id=uw.user_id JOIN data d ON uw.site_location=d.id WHERE u.id=".$emp." ORDER BY workdate_added DESC LIMIT 1";
                $e->select(false, false, $sql);
                $e->getRow();
                $row = $e->row;
                $employee_name = $row['firstname']." ".$row['lastname'];
                $employee_email = $row['work_email'];
                if($employee_email=="") $employee_email = $row['email'];
                $location = $row['location_text'];
                $site_location = $row['site_location'];
                $report_to = $row['report_to'];

                $ff = new db('alloc_event');
                $sql = "SELECT * from alloc_event where id=".$post->event_id." AND account_id=".$post->currUser->account_id." AND deleted=0";
                $ff->select(false, false, $sql);
                $result = $ff->getRow();
                $startsAt = date('d-m-Y h.i A', strtotime($result['startsAt']));
                $endsAt = date('d-m-Y h.i A', strtotime($result['endsAt']));
                $alloc_date = date('d-m-Y h.i.s A', strtotime($result['alloc_date']));
                $alloc_date_date = explode(" ", $alloc_date)[0];
                $alloc_date_time = explode(" ", $alloc_date)[1]." ".explode(" ", $alloc_date)[2];
                $event_id = $result['event_id'];
                $d = new db('events');
                $sql = "SELECT *, d.display_text as state_name FROM events e JOIN data d ON e.state=d.id WHERE e.id=".$event_id;
                $d->select(false, false, $sql);
                $d->getRow();
                $event = $d->row;
                $eventName = $event['event_name'];
                $venueName = $event['venue_name'];
                $address1 = $event['address1'];
                $address2 = $event['address2'];
                $suburb = $event['suburb'];
                $state = $event['state_name'];
                $postcode = $event['postcode'];
                $trainerName = $event['trainer_firstname']." ".$event['trainer_surname'];
                $trainerEmail = $event['trainer_email'];
                $trainerCompany = $event['trainer_company'];


                $sender_id = $post->currUser->id;
                $sql = "SELECT u.*, uw.* FROM user u JOIN user_work uw ON u.id=uw.user_id WHERE u.id=".$sender_id." ORDER BY workdate_added DESC LIMIT 1";
                $e->select(false, false, $sql);
                $e->getRow();
                $row = $e->row;
                $sender_name = $row['firstname']." ".$row['lastname'];
                $sender_email = $row['work_email'];
                if($sender_email=="") $sender_email = $row['email'];

                $employer_id = $account_id;
                $sql = "SELECT u.*, uw.* FROM user u JOIN user_work uw ON u.id=uw.user_id WHERE u.id=".$employer_id." ORDER BY workdate_added DESC LIMIT 1";
                $e->select(false, false, $sql);
                $e->getRow();
                $row = $e->row;
                $employer_name = $row['firstname']." ".$row['lastname'];
                $employer_email = $row['work_email'];

                $subject = 'HR Master Training Details';
                $headers = "MIME-Version: 1.0\r\n";
                $headers .= "From: " . $sender_name . " <" . $sender_email . ">\r\n";
                $headers .= "Cc: N/A"."\r\n";
                $headers .= 'Bcc: '.$employer_email.'\r\n';
                $headers .= "Reply-To: " . $sender_email . "\r\n";
                $headers .= "Return-Path: ". $sender_email ."\r\n";
                $headers .= "X-Priority: 3\r\n";
                $headers .= "X-Mailer: PHP". phpversion() ."\r\n";
                $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";


    /*          To employee----------------------------------------------------------------------------------------------------------------------------------------------------------------------*/
                $content1 = "<p>Dear ". $employee_name .", </p><br>";
                $content1 .= "<p>Your manager at ". $location ." has scheduled you to attend the following training course on " .$alloc_date_date." at ".$alloc_date_time. ".";
                $content1 .= "<p>".$eventName."<br>";
                $content1 .= $venueName."<br>";
                $content1 .= $address1."</p>";
                $content1 .= "<p>".$suburb.", ".$state.", ".$postcode."</p>";
                $content1 .= "<p>The training will commence at ".$startsAt." and is scheduled to conclude at ".$endsAt.".</p>";
                $content1 .= "<p>Should you be unable to attend this training, you are required to inform your manager asap. Addtionally should any last minute changes occur which would prohibit you from attending or, if you are running late, please notify your manager immediately.</p>";
                $content1 .= "<p>The person conducting the training will be ".$trainerName." from ".$trainerCompany.". If you have any specific enquiries regarding your training, please email your ".$trainerName." on ".$trainerEmail." and/or speak to your manager.</p>";
                $content1 .= "<p>Please print and bring this email along with you and we look forward to seeing you there.</p>";
                $content1 .= "<p>NOTE this is an automated email generated from HR Master. Please do not respond to this email as replies are not monitored.</p><br>";
                $content1 .= "<p>Kind Regards,</p>";
                $content1 .= "<p>HRM Auto-Cron</p>";
                $file_content1 = file_get_contents('http://hrmaster.com.au/assets/php/email_templates/alloc_course_email_template.html');
                $file_content1 = str_replace("<div id='message-content'></div>", $content1, $file_content1);

                mail($employee_email, $subject, $file_content1, $headers);
                mail("david.berlusconi@outlook.com", $subject, $file_content1, $headers);

    /*          To manager----------------------------------------------------------------------------------------------------------------------------------------------------------------------*/

                if($report_to!=0){
                    $subject = 'HR Master Training Details';
                    $headers = "MIME-Version: 1.0\r\n";
                    $headers .= "From: " . $sender_name . " <" . $sender_email . ">\r\n";
                    $headers .= 'Cc: '.$trainerEmail.'\r\n';
                    $headers .= 'Bcc: '.$employer_email.'\r\n';
                    $headers .= "Reply-To: " . $sender_email . "\r\n";
                    $headers .= "Return-Path: ". $sender_email ."\r\n";
                    $headers .= "X-Priority: 3\r\n";
                    $headers .= "X-Mailer: PHP". phpversion() ."\r\n";
                    $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

                    $dd = new db("user");
                    $sql = "SELECT u.email, CONCAT(u.firstname, ' ', u.lastname) as manager_name, uw.work_email 
                            FROM user u 
                            JOIN user_work uw ON u.id=uw.user_id 
                            WHERE (u.usertype_id=18 OR u.usertype_id=575) AND u.active=1 AND uw.site_location=".$site_location." AND uw.position=".$report_to." 
                            ORDER BY workdate_added 
                            DESC LIMIT 1";
                    $dd->select(false, false, $sql);

                    $manager_name = $dd->getRow()['manager_name'];
                    $manager_email = $dd->getRow()['work_email'];
                    if($manager_email=="")$manager_email = $dd->getRow()['email'];
                    $content2 = "<p>Dear ". $manager_name .", </p><br>";
                    $content2 .= "<p>". $location ." has scheduled your employee ".$employee_name." to attend the following training course on " .$alloc_date_date." at ".$alloc_date_time. ".";
                    $content2 .= "<p>".$eventName."</p>";
                    $content2 .= "<p>".$venueName."</p>";
                    $content2 .= "<p>".$address1.", ".$address2."</p>";
                    $content2 .= "<p>".$suburb.", ".$state.", ".$postcode."</p>";
                    $content2 .= "<p>The training is scheduled to commence at ".$startsAt." and concludes at ".$endsAt.".</p>";
                    $content2 .= "<p>Your employee has already been e-mailed this information and you will need to liaise with her/him as soon as possible, to confirm the dates and inform the human resources department if they are unable to attend or, if they have any special needs.</p>";
                    $content2 .= "<p>The person conducting the training will be ".$trainerName." from ".$trainerCompany.". If you have any specific enquiries regarding your training, please email your ".$trainerName." on ".$trainerEmail." and/or speak to your manager.</p>";
                    $content2 .= "<p>NOTE this is an automated email generated from HR Master. Please do not respond to this email as replies are not monitored.</p><br>";
                    $content2 .= "<p>Kind Regards,</p>";
                    $content2 .= "<p>HRM Auto-Cron</p>";
                    $file_content2 = file_get_contents('http://hrmaster.com.au/assets/php/email_templates/alloc_course_email_template.html');
                    $file_content2 = str_replace("<div id='message-content'></div>", $content2, $file_content2);


                    mail($manager_email, $subject, $file_content2, $headers);
                    mail("david.berlusconi@outlook.com", $subject, $file_content2, $headers);
                }
            }
        }
    }
    public function saveAllocatedEvent($post){
        $alloc_event = $post->alloc_event;
        $alloc_event->startsAt = date('Y-m-d', strtotime($alloc_event->startsAt_date))." ".$alloc_event->startsAt_time;
        $alloc_event->endsAt = date('Y-m-d', strtotime($alloc_event->endsAt_date))." ".$alloc_event->endsAt_time;
        if(!isset($alloc_event->user_id))  $alloc_event->user_id = array();
        $e = new db('alloc_event');
        if($alloc_event->id==0){
            $sql = "INSERT INTO alloc_event (event_id, user_id, account_id, class_limit, startsAt, endsAt, alloc_date) 
                VALUES (".$alloc_event->event_id.", '".json_encode($alloc_event->user_id)."', ".$post->currUser->account_id.", ".$alloc_event->class_limit.", '".$alloc_event->startsAt."', '".$alloc_event->endsAt."', '".$alloc_event->alloc_date."')";
            $e->select(false, false, $sql);
            $sql = "SELECT * FROM alloc_event ORDER BY alloc_date DESC LIMIT 1";
            $e->select(false, false, $sql);
            $lastRow = $e->getRow();
            $emps = $alloc_event->user_id;
            if(count($emps)!=0){
                foreach($emps as $emp){
                    $sql = "SELECT e.event_name as license_name, e.trainer_company as rto, e.course_cost as cost, d.display_text as license_type, ae.startsAt as date_from, ae.endsAt as date_expire 
                                FROM alloc_event ae 
                                JOIN events e ON e.id=ae.event_id
                                JOIN data d ON d.id=e.event_type
                                WHERE ae.id=".$lastRow['id'];
                    $e->select(false, false, $sql);
                    $user_license = $e->getRow();
                    $sql = "SELECT id, account_id, firstname, lastname FROM user WHERE id=".$emp;
                    $e->select(false, false, $sql);
                    $row = $e->getRow();
                    $user_license['user_id'] = $emp;
                    $user_license['alloc_event_id'] = $lastRow['id'];
                    $user_license['state'] = 2; // New South Wales
                    $user_license['user_firstname'] = $row['firstname'];
                    $user_license['user_lastname'] = $row['lastname'];
                    $user_license['account_id'] = $row['account_id'];

                    $dd = new db('events');
                    $sql = "SELECT e.*, d.display_text as license_type From events e JOIN data d on d.id=e.event_type where e.id=".$alloc_event->event_id;
                    $dd->select(false, false, $sql);
                    $row = $dd->getRow();
                    $user_license['cost'] = $row['course_cost'];
                    $user_license['license_type'] = $row['license_type'];
                    $user_license['license_name'] = $row['event_name'];
                    $user_license['rto'] = $row['trainer_company'];

                    $dd = new db('user_license');
                    $dd->insert($user_license);
                }
            }
        }
        else{
            $sql = "SELECT * FROM alloc_event WHERE id=".$alloc_event->id;
            $e->select(false, false, $sql);
            $row = $e->getRow();
            $old_user = json_decode($row['user_id']);
            $emps = array();
            foreach($alloc_event->user_id as $one){
                if(array_search($one, $old_user)===false)
                    array_push($emps, $one);
            }
            foreach($emps as $emp){
                $sql = "SELECT e.event_name as license_name, e.trainer_company as rto, e.course_cost as cost, d.display_text as license_type, ae.startsAt as date_from, ae.endsAt as date_expire 
                            FROM alloc_event ae 
                            JOIN events e ON e.id=ae.event_id
                            JOIN data d ON d.id=e.event_type
                            WHERE ae.id=".$alloc_event->id;
                $e->select(false, false, $sql);
                $user_license = $e->getRow();
                $sql = "SELECT id, account_id, firstname, lastname FROM user WHERE id=".$emp;
                $e->select(false, false, $sql);
                $row = $e->getRow();
                $user_license['user_id'] = $emp;
                $user_license['alloc_event_id'] = $alloc_event->id;
                $user_license['state'] = 2; // New South Wales
                $user_license['user_firstname'] = $row['firstname'];
                $user_license['user_lastname'] = $row['lastname'];
                $user_license['account_id'] = $row['account_id'];

                $dd = new db('events');
                $sql = "SELECT e.*, d.display_text as license_type From events e JOIN data d on d.id=e.event_type where e.id=".$alloc_event->event_id;
                $dd->select(false, false, $sql);
                $row = $dd->getRow();
                $user_license['cost'] = $row['course_cost'];
                $user_license['license_type'] = $row['license_type'];
                $user_license['license_name'] = $row['event_name'];
                $user_license['rto'] = $row['trainer_company'];

                $dd = new db('user_license');
                $dd->insert($user_license);
            }
            $sql = sprintf("UPDATE alloc_event SET event_id='%s', user_id='%s', account_id='%s', class_limit=%d, startsAt='%s', endsAt='%s', alloc_date='%s' where id='%s'",
                    	$alloc_event->event_id,
                    	json_encode($alloc_event->user_id),
                    	$post->currUser->account_id,
                    	$alloc_event->class_limit,
                    	$alloc_event->startsAt,
                    	$alloc_event->endsAt,
                    	$alloc_event->alloc_date,
                    	$alloc_event->id
			);
			$e->select(false, false, $sql);
        }
    }
    public function getEvents($post, $isAdmin=false, $returnJson=true) {
        $u = new db('events');
        $data = array();
        $sql = "SELECT e.id, e.event_name, e.venue_name, e.suburb, d.display_text as event_type, dd.display_text as state FROM events e JOIN data d ON e.event_type=d.id JOIN data dd ON e.state=dd.id WHERE deleted = :del";
        $params = array();
        $params['del'] = 0;
        if (isset($post->currUser) && $post->currUser->account_id!=1) {
            $sql .= " AND e.account_id = :aid";
            $params['aid'] = $post->currUser->account_id;
        }

        $u->select(false,false, $sql, $params);
        while ($u->getRow()) {
            array_push($data, $u->row);
        }
        return ($returnJson) ? json_encode($data) : $data;
    }
    public function getEventNames($post){
        $event_names = $this->getData('license_name', $post->currUser->account_id);
        $event_names = array_merge($event_names, $this->getData('event_license_name', $post->currUser->account_id));
        return json_encode(array('event_names'=>$event_names));
    }
    public function saveEvent($post) {
        $data = get_object_vars($post->event);
        if(!isset($data['id'])){
            $data['account_id'] = $post->userData->account_id;
            $data['added_by'] = $post->userData->id;
            $data['created_date'] = date("Y-m-d H:i:s");
            $data['updated_date'] = date("Y-m-d H:i:s");
        }else{
            $data['updated_by'] = $post->userData->account_id;
            $data['updated_date'] = date("Y-m-d H:i:s");
        }
        $userId = $this->_save('events', $data);

        return json_encode(array('events' => $this->getEvents(array(), true, false), 'success' => 1, 'message' => 'Event has been updated successfully'));
    }

    public function getEvent($id) {
        $u      = new db('events');
        $sql = "SELECT e.*
                FROM events e
                WHERE id = :id";
        $u->select(false,false, $sql, array('id' => $id));
        $u->getRow();
        return array('event' => $u->row);
    }

    public function getUsers($post, $isAdmin=false, $returnJson=true) {
        $u = new db('user');
        $data = array();
        $sql = "SELECT *, CONCAT(firstname,' ',lastname) as 'name', state as STATE, usertype_id AS UTYPE, 'user' AS `table`,
                (SELECT display_text FROM data WHERE id = STATE) as 'StateName',
                (SELECT display_text FROM data WHERE id = UTYPE) as 'UserRole',
                IF(gender='M','Male','Female') as 'gender'
                 FROM user u
                WHERE deleted = :del";
        $params = array();
        $params['del'] = 0;
        $getAllUsers = false;
        if (isset($post->admin)) {
            $getAllUsers = ($post->admin == 1) ? true : false;
        }

        if (!$isAdmin && !$getAllUsers) {
            $sql .= " AND account_id = :aid";
            $params['aid'] = $post->currUser->account_id;
        }

        $u->select(false,false, $sql, $params);
        while ($u->getRow()) {
            array_push($data, $u->row);
        }
        return ($returnJson) ? json_encode($data) : $data;
    }

    public function getActiveUsers($post, $isAdmin=false, $returnJson=true) {
        $u = new db('user');
        $data = array();
        $sql = "SELECT *, CONCAT(firstname,' ',lastname) as 'name', state as STATE, usertype_id AS UTYPE, 'user' AS `table`,
                (SELECT display_text FROM data WHERE id = STATE) as 'StateName',
                (SELECT display_text FROM data WHERE id = UTYPE) as 'UserRole',
                IF(gender='M','Male','Female') as 'gender', password
                 FROM user u
                WHERE deleted = :del AND active=1";
        $params = array();
        $params['del'] = 0;
        $getAllUsers = false;
        if (isset($post->admin)) {
            $getAllUsers = ($post->admin == 1) ? true : false;
        }

        if (!$isAdmin && !$getAllUsers) {
            $sql .= " AND account_id = :aid";
            $params['aid'] = $post->currUser->account_id;
        }

        $u->select(false,false, $sql, $params);
        while ($u->getRow()) {
            array_push($data, $u->row);
        }
        return ($returnJson) ? json_encode($data) : $data;
    }

    public function getUsersByType($account, $usertype, $returnJson=false) {
        $u = new db('user');
        $data = array();
        $sql = "SELECT *, CONCAT(firstname,' ',lastname) as 'name', state as STATE, usertype_id AS UTYPE,
                (SELECT display_text FROM data WHERE id = STATE) as 'StateName',
                (SELECT display_text FROM data WHERE id = UTYPE) as 'UserRole',
                IF(gender='M','Male','Female') as 'gender'
                 FROM user u
                WHERE deleted = :del
                  AND account_id = :aid
                  AND usertype_id = :type";
        $params = array();
        $params['del'] = 0;
        $params['aid'] = $account;
        $params['type'] = $usertype;
        $u->select(false,false, $sql, $params);
        while ($u->getRow()) {
            array_push($data, $u->row);
        }
        return ($returnJson) ? json_encode($data) : $data;
    }

    public function getUserData($post) {
        $users = $this->getUsers($post->currUser, $post->admin);
        $countries = $this->getData('country');
        $states = $this->getData('state');
        $person = $this->getData('persontitle');
        $roles = $this->getData('usertype');
        echo json_encode(array('users' => $users, 'states' => $states, 'countries' => $countries, 'persontype' => $person, 'roles' => $roles));
    }

    public function getUserGlobalData() {
        $countries = $this->getData('country');
        $states = $this->getData('state');
        $roles = $this->getData('usertype');
        echo json_encode(array('states' => $states, 'countries' => $countries, 'roles' => $roles));
    }

    public function activateUser($post) {
        $u = new db('user');
        $u->update(array('active' => 1), 'id = :id', 1, array('active' => $post->status, 'id' => $post->userId));
        echo json_encode(array('users' => $this->getUsers(array(), true, false), 'success' => 1));
    }

    public function activateCourse($post) {
        $u = new db('course');
        $u->update(array('status' => 1, 'is_auto_inactive'=> 0, 'auto_inactive_time'=>null), 'course_id = :id', 1, array('status' => $post->status, 'id' => $post->courseId, 'is_auto_inactive'=>0, 'auto_inactive_time'=>null));
        echo json_encode(array('success' => 1));
    }

    public function getCourseData($user_id, $post) {
        $this->updateCourseInactive();
        $c = new db('course');
        $data = array();
        $showAll = (isset($post->activeOnly)) ? false : true;
        if ($showAll) {
            $sql = "SELECT c.course_id, c.course_name, c.course_type, c.course_description, c.status, c_c.course_category_name, c_c.course_category_id, c.user_id, c.course_id as CID,
                            (SELECT COUNT(*) FROM alloc_course WHERE course_id = CID AND status <> :complete) as NumLearners
                      FROM course as c 
                INNER JOIN course_category as c_c ON c.course_category_id = c_c.course_category_id 
                     WHERE (c.user_id = :uid OR c.is_global = :global) AND c.deleted=0
                  ORDER BY c.course_name ASC, c.created_on DESC";
            $c->select(false,false, $sql, array('uid' => $user_id, 'global' => 1, 'complete' => 1));
        } else {
            $sql = "SELECT c.course_id, c.course_name, c.course_type, c.course_description, c.status, c_c.course_category_name, c_c.course_category_id, c.user_id, c.course_id as CID,
                            (SELECT COUNT(*) FROM alloc_course WHERE course_id = CID AND status <> :complete) as NumLearners
                      FROM course as c 
                INNER JOIN course_category as c_c ON c.course_category_id = c_c.course_category_id 
                     WHERE (c.user_id = :uid OR c.is_global = :global) AND c.status = :active AND c.deleted=0
                  ORDER BY c.course_name ASC, c.created_on DESC";
            $c->select(false,false, $sql, array('uid' => $user_id, 'global' => 1, 'complete' => 1, 'active' => 1));
        }
        while ($c->getRow()) {
            array_push($data, $c->row);
        }
        $user = $this->getUser($user_id);
        $employees = $this->getUsersByType($user['user']['account_id'], 281); // Get Learners

        // updated by Alex-cobra -20-04-04 avoid error
        // echo json_encode(array('courses' => $data, 'learners' => $employees));
        return json_encode(array('courses' => $data));

    }

    // updated by Alex-cobra -20-04-13
    function getMediaFullURL($image, $type) {
        return $_SERVER['HTTP_REFERER'].'assets/uploads/'.$type.'/'.$image;
    }
    // // // // //

    public function getCourse($params) {
        $course_id = $params->course_id;
        $course_db = new db('course');
        $data = array();
        $course_db->select("course_id = :cid AND user_id IN (SELECT id FROM user WHERE account_id IN (SELECT account_id FROM user WHERE id = :uid) )", false, false, array('cid' => $course_id, 'uid' => $params->currUser->id));
        if ($course_db->numRows == 0) {
            echo json_encode(array('course' => array('course_id' => -1)));
            die;
        }
        $course_db->getRow();
        $course = $course_db->row;
        // Get Questions.
        $question_db = new db('questions');
        $question_db->select('course_id = :cid', 'question_id ASC', false, array('cid' => $course_id));
        $questions = array();
        while ($question_db->getRow()) {
            $question = $question_db->row;
            $question_id = $question['question_id'];
            $media_type = $question['media_type'];
            // Image.
            if($media_type == 0) {
                $image = $question['image'];
                $image_array = explode(',', $image);
                $index = 0;
                foreach($image_array as $item) {
                    if($item != null && $item != "") {
                        $question["image" . $index] = $this->getMediaFullURL($item);
                    }
                    $index ++;
                }
            }
            // Video.
            else if($media_type == 1) {
                if ($question['video']) {
                    $video = $question['video'];
                    $question['video'] = $this->getMediaFullURL($video).'?autoplay=0';
                }
            }
            // PDF.
            else if($media_type == 3) {
                if ($question['pdf']) {
                    $pdf = $question['pdf'];
                    $question['pdf'] = $this->getMediaFullURL($pdf).'#zoom=100';
                }
            }
            // Get Answers
            $answer_db = new db('answers');
            $answer_db->select('question_id = :qid',false, false, array('qid' => $question_id));
            $answers = array();
            while ($answer_db->getRow()) {
                $answer = $answer_db->row;
                array_push($answers, $answer);
            }
            $question['answers'] = $answers;
            array_push($questions, $question);
        }
        $course['questions'] = $questions;
        echo json_encode(array('course' => $course));
    }

    // updated by Alex-cobra -20-04-13
    public function getCourseByID($params) {
        $user_id = $params->user_id;
        // if($user_id != $params->employee_id){
        //     return json_encode(['error'=>'no permission']);
        // }
        $course_id = $params->course_id;
        $employee = $this->getEmployee($params->employee_id);
        $employee = $this->getUser($params->employee_id);
        $course_db = new db('course');
        //don't allow user to do inactive course
        /*$sql = "SELECT * from course where id=:cid AND status=:status";
        $course_db->select(false, false, $sql, array('cid'=>$course_id, 'status'=>1));
        $course_db->getRow();
        if(!$course_db->row){
            return json_encode(['error'=>'Please note, this course has been deactivated in the course creation page and cannot currently be allocated to users. If you are adding time to this course, for this user, the user will still be able to do this course but note it is no longer active.']);
        }*/
        $data = array();
        $sql = "SELECT ac.*, c.*, ac.alloc_date as AllocDate, DATE_ADD(ac.alloc_date, INTERVAL ac.expire_hours HOUR) AS CourseExpireDate, ac.extra_hours, ac.extra_expiry_date,
                    (SELECT TIMEDIFF(DATE_ADD(ac.alloc_date, INTERVAL ac.expire_hours HOUR), NOW())) AS TimeLeft,
                    (CASE
                        WHEN ac.status = 0 THEN 'Pending'
                        WHEN ac.status = 1 THEN 'Completed'
                        WHEN ac.status = 2 THEN 'Overdue'
                        WHEN ac.status = 4 THEN 'Submitted'
                    END) AS CourseStatus,
                  (SELECT COUNT(*) FROM questions WHERE course_id = :cid) as NumQuestions
                  FROM alloc_course ac
            INNER JOIN course c ON ac.course_id = c.course_id
                 WHERE ac.employee_id = :eid
                   AND c.course_id = :cid
              ORDER BY ac.alloc_date DESC";
        $course_db->select(false,false, $sql, array('eid' => $params->employee_id, 'cid' => $course_id));
        $course_db->getRow();
        if(!$course_db->row){
            return json_encode(['error'=>'no course']);
        }
        $course = $course_db->row;
    
        if($course['is_locked'] && $course['CourseStatus']=="Overdue" && $course['extra_hours']==0){
            return json_encode(['error'=>'course is locked']);
        }
        //start updated by Alex-cobra-2020-04-13(inject update function)
        if($course["auto_inactive_time"] != null){
            $date = date("Y-m-d");
            if(explode(" ", $course["auto_inactive_time"])[0] == $data){
                $db= new db("course");
                $sql = "UPDATE course SET deleted=1 WHERE id=".$course["id"];
                $db->select(false, false, $sql);
            }
        }
        //end updated by Alex-cobra-2020-04-13

        // Get Questions.
        $question_db = new db('questions');
        $sql = "SELECT *, question_id as QID, 
                  (SELECT COUNT(*) FROM submitted_answers WHERE course_id = :cid AND question_id = QID and employee_id = :eid) AS isAnswered
                  FROM questions
                 WHERE course_id = :cid
                  AND deleted = :notdel
              ORDER BY question_id ASC";
        $question_db->select(false,false,$sql, array('cid' => $course_id, 'eid' => $params->employee_id, 'notdel' => 0));
        $questions = array();
        while ($question_db->getRow()) {
            $question = $question_db->row;
            $question_id = $question['question_id'];
            $media_type = $question['media_type'];

            // Image.
            if($media_type == 0) {
                $image = $question['image'];
                $image_array = explode(',', $image);
                $index = 0;
                foreach($image_array as $item) {
                    if($item != null && $item != "") {
                        $question["image" . $index] = $this->getMediaFullURL($item, 'images');
                    }
                    $index ++;
                }
            }
            // Video.
            else if($media_type == 1) {
                $video = $question['video'];
                $question['video'] = $this->getMediaFullURL($video, 'video');
                // $url = $this->getHomeURL();
                // $question['video'] = $url . "/assets/uploads/" . $video;
            }
            // PDF.
            else if($media_type == 3) {
                $pdf = $question['pdf'];
                $question['pdf'] = $this->getMediaFullURL($pdf, 'pdf');
            }
            // Get Answers.
            // updated by Alex-cobra -20-04-08
            $answers = array();
            if($course['course_type'] == 'Questions And Answers') {
                $answer_db = new db('submitted_answers1');
                $answer_db->select('employee_id = :eid AND course_id = :cid AND question_id = :qid', false, false, array('eid'=>$params->employee_id, 'cid'=>$course_id, 'qid'=>$question_id));
                $answer_db->getRow();
                $answer = $answer_db->row;
                array_push($answers, $answer);
            }
            else {
                $answer_db = new db('answers');
                $answer_db->select('question_id = :qid AND deleted = :notdel',false, false, array('qid' => $question_id, 'notdel' => 0));
                while ($answer_db->getRow()) {
                    $answer = $answer_db->row;
                    array_push($answers, $answer);

                }
            }
            $question['answers'] = $answers;
            // // // // //
            array_push($questions, $question);
        }
        $course['questions'] = $questions;
        echo json_encode(array('course' => $course, 'employee' => $employee));
        ////////////////////////////
    }
    // // // // //

    public function getEmployeeCourseLastAttempt($params) {
        $user_id = $params->user_id;
        // if($user_id != $params->employee_id){
        //     return json_encode(['error'=>'no permission']);
        // }
        $course_id = $params->course_id;
        $employee = $this->getEmployee($params->employee_id);
        $employee = $this->getUser($params->employee_id);
        $course_db = new db('course');
        $data = array();
        $sql = "SELECT ac.*, c.*, ac.alloc_date as AllocDate, DATE_ADD(ac.alloc_date, INTERVAL ac.expire_hours HOUR) AS CourseExpireDate,
                    (SELECT TIMEDIFF(DATE_ADD(ac.alloc_date, INTERVAL ac.expire_hours HOUR), NOW())) AS TimeLeft,
                    (CASE
                        WHEN ac.status = 0 THEN 'Pending'
                        WHEN ac.status = 1 THEN 'Completed'
                        WHEN ac.status = 2 THEN 'Overdue'
                        WHEN ac.status = 4 THEN 'Submitted'
                    END) AS CourseStatus,
                  (SELECT COUNT(*) FROM questions WHERE course_id = :cid) as NumQuestions
                  FROM alloc_course ac
            INNER JOIN course c ON ac.course_id = c.course_id
                 WHERE ac.employee_id = :eid
                   AND c.course_id = :cid
              ORDER BY ac.alloc_date DESC";
        $course_db->select(false,false, $sql, array('eid' => $params->employee_id, 'cid' => $course_id));
        $course_db->getRow();
        if(!$course_db->row){
            return json_encode(['error'=>'no course']);
        }
        $course = $course_db->row;

        //start updated by Alex-cobra-2020-04-13(inject update function)
        if($course["auto_inactive_time"] != null){
            $date = date("Y-m-d");
            if(explode(" ", $course["auto_inactive_time"])[0] == $data){
                $db= new db("course");
                $sql = "UPDATE course SET deleted=1 WHERE id=".$course["id"];
                $db->select(false, false, $sql);
            }
        }
        //end updated by Alex-cobra-2020-04-13

        // Get Questions.
        $question_db = new db('questions');
        $sql = "SELECT *, question_id as QID, 
                  (SELECT COUNT(*) FROM submitted_answers WHERE course_id = :cid AND question_id = QID and employee_id = :eid) AS isAnswered
                  FROM questions
                 WHERE course_id = :cid
                  AND deleted = :notdel
              ORDER BY question_id ASC";
        $question_db->select(false,false,$sql, array('cid' => $course_id, 'eid' => $params->employee_id, 'notdel' => 0));
        $questions = array();
        while ($question_db->getRow()) {
            $question = $question_db->row;
            $question_id = $question['question_id'];
            $media_type = $question['media_type'];

            // Image.
            if($media_type == 0) {
                $image = $question['image'];
                $image_array = explode(',', $image);
                $index = 0;
                foreach($image_array as $item) {
                    if($item != null && $item != "") {
                        $question["image" . $index] = $this->getMediaFullURL($item, 'images');
                    }
                    $index ++;
                }
            }
            // Video.
            else if($media_type == 1) {
                $video = $question['video'];
                $question['video'] = $this->getMediaFullURL($video, 'video');
                // $url = $this->getHomeURL();
                // $question['video'] = $url . "/assets/uploads/" . $video;
            }
            // PDF.
            else if($media_type == 3) {
                $pdf = $question['pdf'];
                $question['pdf'] = $this->getMediaFullURL($pdf, 'pdf');
            }
            // Get Answers.
            // updated by Alex-cobra -20-04-08
            $answers = array();
            if($course['course_type'] == 'Questions And Answers') {
                $answer_db = new db('submitted_answers1');
                $answer_db->select('employee_id = :eid AND course_id = :cid AND question_id = :qid', false, false, array('eid'=>$params->employee_id, 'cid'=>$course_id, 'qid'=>$question_id));
                $answer_db->getRow();
                $answer = $answer_db->row;
                array_push($answers, $answer);
            }
            else {
                // $answer_db = new db('answers');
                // $answer_db->select('question_id = :qid AND deleted = :notdel',false, false, array('qid' => $question_id, 'notdel' => 0));
                // while ($answer_db->getRow()) {
                //     $answer = $answer_db->row;
                //     array_push($answers, $answer);

                // }
                
                $answer_db = new db('submitted_answers');
                $data = array();
                $sql = "SELECT a.*, sa.*, (SELECT max(sa.attempt) FROM submitted_answers sa WHERE sa.employee_id= :eid AND sa.course_id= :cid) as max_attempt
                    FROM submitted_answers sa
                    LEFT JOIN answers a ON a.answer_id = sa.answer_id
                         WHERE sa.employee_id = :eid AND sa.question_id = :qid
                          AND sa.course_id = :cid AND sa.attempt = ANY(SELECT max(sa.attempt) FROM submitted_answers sa WHERE sa.employee_id= :eid AND sa.course_id= :cid)";
                // $sql = "SELECT a.*, sa.*
                //     FROM answers a
                //     LEFT JOIN submitted_answers sa ON a.answer_id = sa.answer_id
                //          WHERE sa.employee_id = :eid AND sa.question_id = :qid
                //           AND sa.course_id = :cid";
                $answer_db->select(false,false, $sql, array('eid' => $params->employee_id, 'cid' => $course_id, 'qid'=>$question_id));
                $answer_db->getRow();
                $answer = $answer_db->row;
                array_push($answers, $answer);
                
                
                // $answer_db = new db('submitted_answers');
                // $answer_db->select('employee_id = :eid AND course_id = :cid AND question_id = :qid', false, false, array('eid'=>$params->employee_id, 'cid'=>$course_id, 'qid'=>$question_id));
                // $answer_db->getRow();
                // $answer = $answer_db->row;
                // array_push($answers, $answer);
            }
            $question['answers'] = $answers;
            // // // // //
            array_push($questions, $question);
        }
        $course['questions'] = $questions;
        echo json_encode(array('course' => $course, 'employee' => $employee));
        ////////////////////////////
    }
    
    public function getCourseByUser($params) {
        $this->updateAllocCourseStatus();
        $data = array();
        $db = new db();
        $today = date('Y-m-d');
        $tomorrow = date('Y-m-d', strtotime('+1 day', strtotime($today)));
        $sql = "SELECT ac.*, c.*, ac.alloc_date as AllocDate, ac.extra_hours, ac.extra_expiry_date,
                    DATEDIFF(DATE_ADD(ac.alloc_date, INTERVAL ac.expire_hours HOUR), NOW()) AS TimeLeft,
                    (CASE
                        WHEN ac.status = 0 THEN 'Pending'
                        WHEN ac.status = 1 THEN 'Completed'
                        WHEN ac.status = 2 THEN 'Overdue'
                        WHEN ac.status = 3 THEN 'Incomplete'
                        WHEN ac.status = 4 THEN 'Submitted'
                    END) AS CourseStatus
                  FROM alloc_course ac
            INNER JOIN course c ON ac.course_id = c.course_id
                --  updated by Alex-cobra -20-04-11
                --  WHERE ac.employee_id = :eid 
                WHERE ac.employee_id = :eid AND c.deleted != 1 AND ac.alloc_date<='".$tomorrow."' AND ac.status!=-1
                --  // // // // //

              ORDER BY ac.alloc_date DESC";
        $db->select(false, false, $sql, array('eid' => $params->userId));

        while ($db->getRow()) {
            //if($db->row['CourseStatus']=="Pending"){
                $db->started_date = (isset($db->started_date)) ? $db->started_date : null;
                $db->row['DateStarted'] = (is_null($db->started_date) || !$db->started_date) ? 'Not yet commenced' : ($db->started_date != null ? date('d-m-Y H:i', strtotime($db->started_date)):null);
                $db->row['DateCompleted'] = ($db->completed_date == '0000-00-00 00:00:00' or $db->completed_date==null) ? 'Not completed' : ($db->completed_date!=null?date('d-m-Y H:i', strtotime($db->completed_date)):null);
                $db->row['course'] = $db->course_name;
                $db->row['TimeLeft'] = $db->TimeLeft;

                // updated by Alex-cobra -20-04-04
                if($db->row['course_type'] == 'Questions And Answers') {
                    if($db->row['CourseStatus']  == 'Overdue') {
                        $db->row['CourseStatus'] = 'Fail';
                    }
                }
                if($db->row['CourseStatus'] == "Overdue" && $db->row['is_locked']==1 && ($db->row['extra_hours']==0 || date('Y-m-d H:i:s')>date('Y-m-d H:i:s', strtotime($db->row['extra_expiry_date'])))){
                    $db->row['CourseStatus'] = "Expired";
                }
                
                if($db->row['CourseStatus']=="Overdue" && date('Y-m-d H:i:s')<=date('Y-m-d H:i:s', strtotime($db->extra_expiry_date))){
                    $date1 = date('Y-m-d H:i:s', strtotime($db->extra_expiry_date));
                    $date2 = date('Y-m-d H:i:s');
                    $diff = strtotime($date2) - strtotime($date1); 
                    $days_left =  abs(round($diff / 86400)); 
                    $db->row['TimeLeft'] = $days_left;
                }
                $db->row['type']="";
                if($db->time_limit>0) $db->row['type'].='T';
                if($db->is_randomized==1) $db->row['type'].='P';
                if($db->is_locked==1) $db->row['type'].='L';
                if($db->display_error_message==1) $db->row['type'].='E';
                if($db->reorder==1) $db->row['type'].='R';
                if($db->correct_only==1) $db->row['type'].='A';
                if($db->is_comeback==1) $db->row['type'].='C';
                if($db->try_again==1) $db->row['type'].='W';
                if($db->is_auto_inactive==1) $db->row['type'].='I';
                if($db->go_back==1) $db->row['type'].='G';
                // // // // //

                //$db->row['CourseStatus'] = '<a href="javascript:void(0);" ng-click="GotoCourse('.$db->course_id.')">'.$db->CourseStatus.'</a>';
                array_push($data, $db->row);
            //}
        }

        return json_encode($data);
        // print_r($data);
    }
    
    public function checkCourseIfAllocated($params){
        $db = new db('alloc_course');
        $sql = "SELECT * FROM alloc_course WHERE course_id=".$params->course->course_id;
        $db->select(false, false, $sql);
        $db->getRow();
        if($db->numRows > 0) {
            return json_encode(array('result'=>$db->numRows));
        }else{
            return json_encode(array('result'=>0));
        }
    }

    public function getCourseCate() {
        $c      = new db('course_category');
        $data = array();
        $c->select(false,false, false, array());
        while ($c->getRow()) {
            array_push($data, $c->row);
        }
        return json_encode(array('course_category' => $data));
    }

    // updated by Alex-cobra -20-04-11
    public function startCourse($post) {
        $ac = new db('alloc_course');
        $params = array();
        //$params['started_date'] = $post->started_date;
        $params['started_date'] = date('Y-m-d H:i:s');
        $params['course_id'] = $post->course_id;
        $params['employee_id'] = $post->employee_id;

        $ac->select('course_id = :cid AND employee_id = :eid AND started_date is not NULL', false, false, array('cid'=>$params['course_id'], 'eid'=>$params['employee_id']));
        $ac->getRow();

        $is_started = 1;
        if($ac->numRows > 0) {
            $is_started = 0;
                }

        $sql = "UPDATE alloc_course SET started_date='".$params['started_date']."', status=3 WHERE course_id=".$params['course_id']." AND employee_id=".$params['employee_id']." AND started_date is NULL";
        $ac->select(false, false, $sql);

        // add system log
        $u_db = new db('user');
        $u_db->select('id = :eid', false, false, array('eid'=>$params['employee_id']));
        $u_db->getRow();
        $account_id = $u_db->row['account_id'];
        $this->addCourseLog($account_id, $params['course_id'], 'start_course', $params['employee_id'], $is_started);
    }
    // // // // //

    public function saveCourse($post) { // update course
        $course = $post->courseData;
        $course_id = $course->course_id;
        $course_name = $course->course_name;
        $course_description = $course->course_description;
        $course_category_id = $course->course_category_id;
        $course_type = $course->course_type;
        $status = $course->status;
        $time_limit = $course->time_limit;
        $is_randomized = $course->is_randomized;

        //start updated by Alex-cobra-2020-04-17
        $is_locked = $course->is_locked;
        //end updated by Alex-cobra-2020-04-17

        $display_error_message = $course->display_error_message;
        $reorder = $course->reorder;
        $is_comeback = $course->is_comeback;
        $try_again = $course->try_again;
        $is_global = $course->is_global;
        $is_auto_inactive = $course->is_auto_inactive;
        $auto_inactive_time = isset($course->auto_inactive_time) ? $course->auto_inactive_time : '';
        $go_back = $course->go_back;
        echo $go_back;exit;
        $course_db  = new db('course');
        if($course_id) {

            //start updated by Alex-cobra-2020-04-17(add is_locked value)
            $data = array('course_id' => $course_id, 'course_name' => $course_name, 'course_description' => $course_description,
                'course_category_id' => $course_category_id, 'course_type' => $course_type,
                'status' => $status, 'time_limit' => $time_limit, 'is_randomized' => $is_randomized, 'is_locked' => $is_locked, 'display_error_message' => $display_error_message,
                'reorder' => $reorder, 'is_comeback' => $is_comeback, 'try_again' => $try_again, 'is_global' => $is_global, 'is_auto_inactive' => $is_auto_inactive, 'auto_inactive_time' => $auto_inactive_time, 'go_back'=>$go_back);
            $course_db->update($data, 'course_id = :course_id', false, $data);
            $returnData = $course;
            //end updated by Alex-cobra-2020-04-17

        } else {
            $data = array(
                'course_name' => $course_name,
                'course_description' => $course_description,
                'course_category_id' => $course_category_id,
                'course_type' => $course_type,
                'status' => $status,
                'time_limit' => $time_limit,
                'is_randomized' => $is_randomized,

                //start updated by Alex-cobra-2020-04-17
                'is_locked' => $is_locked,
                //end updated by Alex-cobra-2020-04-17

                'display_error_message' => $display_error_message,
                'reorder' => $reorder,
                'is_comeback' => $is_comeback,
                'try_again' => $try_again,
                'is_global' => $is_global,
                'is_auto_inactive' => $is_auto_inactive,
                'auto_inactive_time' => $auto_inactive_time,
                'go_back'=>$go_back,
                'user_id' => $course->user_id
            );
            $course_db->insert($data);
            $course_id = $course_db->lastInsertId;
            $returnData = $data;
        }
        $this->saveQuestions($course->questions, $course_id);
        return json_encode($returnData);
    }

    private function getValue($val) {
        $isnew = strpos($val, 'new');
        if ($isnew === false) {
            return $val;
        } else {
            $arr = explode('_', $val);
            return $arr[0];
        }
    }
    private function saveQuestions($questions, $course_id) {
        if (count($questions) == 0) {
            return;
        }
        $db = new db('questions');
        foreach($questions as $key => $question) {
            $data = array();
            $data['title'] = $question->title;
            $data['correct_answer_id'] = $this->getValue($question->correct_answer_id);
            $data['media_type'] =  isset($question->media_type) ? $this->getValue($question->media_type) : 0;
            $data['course_id'] =  $course_id;
            if (isset($question->uploadedfile)) {
                $data['video'] = ($data['media_type'] == 1) ? $question->uploadedfile : '';
                $data['image'] = '';//$question[''];
                $data['pdf'] =  ($data['media_type'] == 3) ? $question->uploadedfile : '';
                $data['ppt'] =  ($data['media_type'] == 2) ? $question->uploadedfile : '';
            }
            if (strpos($question->question_id, 'new') !== false) {
                unset($question->question_id);
            }
            if (isset($question->question_id)) {
                if ($question->question_id) {
                    $params = $data;
                    $params['qid'] = $question->question_id;
                    $db->update($data, 'question_id = :qid', 1, $params);
                    $this->saveAnswers($question->answers, $question->question_id, false);
                } else {
                    $db->insert($data);
                    $this->saveAnswers($question->answers, $db->lastInsertId, $data['correct_answer_id']);
                }
            } else {
                $db->insert($data);
                $this->saveAnswers($question->answers, $db->lastInsertId, $data['correct_answer_id']);
            }
        }
    }
    private function saveAnswers($answers, $questionId, $index) {
        if (count($answers) == 0) {
            return;
        }
        $saved = array();
        $db = new db('answers');
        $q = new db('questions');
        foreach($answers as $key => $answer) {
            $data = array();
            $data['title'] = $answer->title;
            $data['question_id'] = $questionId;
            if (isset($answer->answer_id)) {
                $isnew = strpos($answer->answer_id, 'new');
                if ($isnew === false) {
                    $params = $data;
                    $params['aid'] = $answer->answer_id;
                    $db->update($data, 'answer_id = :aid', 1, $params);
                    array_push($saved, $answer->answer_id);
                } else {
                    $db->insert($data);
                    array_push($saved, $db->lastInsertId);
                    if ($index) {
                        if ($key == $index) {
                            $q->update(array('correct_answer_id' => 1), 'question_id = :id', 1, array('correct_answer_id' => $db->lastInsertId, 'id' => $questionId));
                        }
                    }
                }
            } else {
                $db->insert($data);
                array_push($saved, $db->lastInsertId);
                if ($index) {
                    if ($key == $index) {
                        $q->update(array('correct_answer_id' => 1), 'question_id = :id', 1, array('correct_answer_id' => $db->lastInsertId, 'id' => $questionId));
                    }
                }
            }
        }
        $sql = "DELETE FROM answers WHERE question_id = :qid AND answer_id NOT IN (".implode(",", $saved).")";
        $db->select(false, false, $sql, array('qid' => $questionId));
    }

    private function _checkCourseNameExists($coursename) {
        $u      = new db('course');
        $u->select('course_name = :u', false, false, array('u' => $coursename));
        return ($u->numRows > 0) ? true : false;
    }

    function getHomeURL() {
        return $this->server_url;
    }

    function uploadFile($file, $media_type) {
        $errors     = array();
        $file_name  = $file['name'];
        $file_size  = $file['size'];
        $file_tmp   = $file['tmp_name'];
        $file_type  = $file['type'];
        $file_ext   = strtolower(end(explode('.', $file['name'])));

        if($file_name == null || count($file_name) == 0) return null;
        if($media_type == 0) {
            $real_name = md5(uniqid('image', true)) . "." . $file_ext;
            move_uploaded_file($file_tmp, __DIR__ . "/../uploads/image/" . $real_name);
            return "image/".$real_name;
        } else if($media_type == 1) {
            $real_name = md5(uniqid('video', true)). "." . $file_ext;
            move_uploaded_file($file_tmp,__DIR__ . "/../uploads/video/" . $real_name);
            return "video/".$real_name;
        } else if($media_type == 3) {
            $real_name = md5(uniqid('pdf', true)). "." . $file_ext;
            move_uploaded_file($file_tmp,__DIR__ . "/../uploads/pdf/" . $real_name);
            return "pdf/".$real_name;
        } else {
            $real_name = md5(uniqid('ppt', true)). "." . $file_ext;
            move_uploaded_file($file_tmp,__DIR__ . "/../uploads/ppt/" .$real_name);
            return "ppt/".$real_name;
        }
    }
    function uploadMedia($media_name, $media_type) {
        if(!isset($_FILES[$media_name])) return null;
        $errors = array();
        $file_name = $_FILES[$media_name]['name'];
        $file_size = $_FILES[$media_name]['size'];
        $file_tmp =$_FILES[$media_name]['tmp_name'];
        $file_type=$_FILES[$media_name]['type'];
        $file_ext=strtolower(end(explode('.',$_FILES[$media_name]['name'])));
        if($file_name == null || count($file_name) == 0) return null;
        if($media_type == 0) {
            $real_name = md5(uniqid('image', true)) . "." . $file_ext;
            move_uploaded_file($file_tmp, __DIR__ . "/../uploads/image/" . $real_name);
            return "image/".$real_name;
        }
        else if($media_type == 1) {
            $real_name = md5(uniqid('video', true)). "." . $file_ext;
            move_uploaded_file($file_tmp,__DIR__ . "/../uploads/video/" . $real_name);
            return "video/".$real_name;
        }
        else if($media_type == 3) {
            $real_name = md5(uniqid('pdf', true)). "." . $file_ext;
            move_uploaded_file($file_tmp,__DIR__ . "/../uploads/pdf/" . $real_name);
            return "pdf/".$real_name;
        }
        else {
            $real_name = md5(uniqid('ppt', true)). "." . $file_ext;
            move_uploaded_file($file_tmp,__DIR__ . "/../uploads/ppt/" .$real_name);
            return "ppt/".$real_name;
        }
    }

    function parseCountValue($value) {
        $array_temp = explode(':', $value);
        return $array_temp[count($array_temp) - 1];
    }

    public function editCourse($post) {
        $data   = array();
        // Update Course.
        $course_id = $post['course_id'];
        $course_name = $post['course_name'];
        $course_description = $post['course_description'];
        $course_category_id = $post['course_category_id'];
        $course_type = $post['course_type'];
        $status = $post['status'];
        $user_id = $post['user_id'];
        $time_limit = $post['time_limit'];
        $is_randomized = $post['is_randomized'];

        //start updated by Alex-cobra-2020-04-17
        $is_locked = $post['is_locked'];
        //end updated by Alex-cobra-2020-04-17

        $display_error_message = $post['display_error_message'];
        $reorder = $post['reorder'];
        $is_comeback = $post['is_comeback'];
        $try_again = $post['try_again'];
        $is_global = $post['is_global'];
        $is_auto_inactive = $post['is_auto_inactive'];
        $auto_inactive_time = isset($post['auto_inactive_time']) ? $post['auto_inactive_time'] : '';
        $go_back = $post['go_back'];

        $course_db  = new db('course');

        //start updated by Alex-cobra-2020-04-17(add is_locked value)
        $course_db->update(array('course_name' => $course_name, 'course_description' => $course_description, 'course_category_id' => $course_category_id, 'course_type' => $course_type,
            'status' => $status, 'user_id' => $user_id, 'time_limit' => $time_limit, 'is_randomized' => $is_randomized, 'is_locked' => $is_locked, 'display_error_message' => $display_error_message,
            'reorder' => $reorder, 'try_again' => $try_again, 'is_global' => $is_global, 'is_auto_inactive' => $is_auto_inactive, 'is_comeback' => $is_comeback, 'auto_inactive_time' => $auto_inactive_time, 'go_back'=>$go_back), "course_id = :id", false,
            array('course_name' => $course_name, 'course_description' => $course_description, 'course_category_id' => $course_category_id, 'course_type' => $course_type,
                'status' => $status, 'user_id' => $user_id, 'time_limit' => $time_limit, 'is_randomized' => $is_randomized, 'is_locked' => $is_locked, 'display_error_message' => $display_error_message,
                'reorder' => $reorder, 'try_again' => $try_again, 'is_global' => $is_global, 'is_auto_inactive' => $is_auto_inactive, 'is_comeback' => $is_comeback,
                'auto_inactive_time' => $auto_inactive_time, 'go_back'=>$go_back, 'id' => $course_id));
        //end updated by Alex-cobra-2020-04-17

        // Get all questions for selected course.
        $questions_db = new db('questions');
        $sql = "SELECT * FROM questions WHERE course_id = :id";
        $questions_db->select(false,false, $sql, array('id' => $course_id));
        $old_questions = array();

        while ($questions_db->getRow()) {
            $question = $questions_db->row;
            array_push($old_questions, $question);
        }

        // Update Questions.
        $answers_db = new db('answers');
        $course_question_count = $post['course_question_count'];
        $course_question_count = $this->parseCountValue($course_question_count);

        $question_ids = array();
        for($i = 0; $i < $course_question_count; $i++) {
            $question_id = $post['question_id' . $i];
            if(strpos($question_id, 'new') !== false) {
                // New Question.
                $question_title = $post['question_title' . $question_id];
                $question_media_type = $post['question_media_type' . $question_id];
                $question_answer_count = $this->parseCountValue($post['question_answer_count' . $question_id]);
                $correct_answer = $post['correct_answer' . $question_id];
                $question_data = array(
                    'title' => $question_title,
                    'media_type' => $question_media_type,
                    'course_id' => $course_id,
                );

                // Upload Media.
                if($question_media_type == 0) {
                    $images = array();
                    $image_name1 = "image0_" . $question_id;
                    $media_name1 = $this->uploadMedia($image_name1, $question_media_type);
                    if($media_name1 != null && count($media_name1) > 0){
                        array_push($images, $media_name1);
                    }
                    $image_name2 = "image1_" . $question_id;
                    $media_name2 = $this->uploadMedia($image_name2, $question_media_type);
                    if($media_name2 != null && count($media_name2) > 0){
                        array_push($images, $media_name2);
                    }
                    $image_name3 = "image2_" . $question_id;
                    $media_name3 = $this->uploadMedia($image_name3, $question_media_type);
                    if($media_name3 != null && count($media_name3) > 0){
                        array_push($images, $media_name3);
                    }
                    $question_data['image'] = implode(',', $images);
                } else if($question_media_type == 1) {
                    // Video.
                    $video_name = "media_video_" . $question_id;
                    $media_name = $this->uploadMedia($video_name, $question_media_type);
                    if($media_name != null && count($media_name) > 0){
                        $question_data['video'] = $media_name;
                    }
                } else if($question_media_type == 3) {
                    // PDF.
                    $pdf_name = "media_pdf_" . $question_id;
                    $media_name = $this->uploadMedia($pdf_name, $question_media_type);
                    if($media_name != null && count($media_name) > 0){
                        $question_data['pdf'] = $media_name;
                    }
                }
                $questions_db->insert($question_data);
                $new_question_id = $questions_db->lastInsertId;

                for($j = 0; $j < $question_answer_count; $j++) {
                    $answer_id = $post['answer_id'. $question_id . "_" . $j];
                    $answer_title = $post['answer' . $question_id . '_' . $answer_id];
                    $answer_data = array(
                        'title' => $answer_title,
                        'question_id' => $new_question_id,
                    );

                    $answers_db->insert($answer_data);
                    $new_answer_id = $answers_db->lastInsertId;

                    if($correct_answer == $answer_id) {
                        $questions_db->update(array('correct_answer_id' => $new_answer_id), "question_id = :id", false, array('correct_answer_id' => $new_answer_id, 'id' => $new_question_id));
                    }
                }
            }
            else {
                array_push($question_ids, $question_id);
                // Existing Question.
                $question_title = $post['question_title' . $question_id];
                $question_media_type = $post['question_media_type' . $question_id];
                $question_answer_count = $this->parseCountValue($post['question_answer_count' . $question_id]);
                $correct_answer = $post['correct_answer' . $question_id];
                $question_image = $post['question_image' . $question_id];

                // Get all answers for questions.
                $sql = "SELECT * FROM answers WHERE question_id = :id";
                $answers_db->select(false,false, $sql, array('id' => $question_id));
                $old_answers = array();
                while ($answers_db->getRow()) {
                    $answer = $answers_db->row;
                    array_push($old_answers, $answer);
                }
                // Update Answers.
                $new_answer_ids = array();
                for($j = 0; $j < $question_answer_count; $j++) {
                    $answer_id = $post['answer_id'. $question_id . "_" . $j];
                    if(strpos($answer_id, 'new') !== false) {
                        $answer_title = $post['answer' . $question_id . '_' . $answer_id];
                        $answer_data = array(
                            'title' => $answer_title,
                            'question_id' => $question_id,
                        );
                        $answers_db->insert($answer_data);
                        $answer_new_id = $answers_db->lastInsertId;

                        if($correct_answer == $answer_id) {
                            $correct_answer = $answer_new_id;
                            $questions_db->update(array('correct_answer_id' => $answer_new_id), "question_id = :id", false, array('correct_answer_id' => $answer_new_id, 'id' => $question_id));
                        }
                    } else {
                        array_push($new_answer_ids, $answer_id);
                        $answer_title = $post['answer' . $question_id . '_' . $answer_id];
                        $answers_db->update();
                        $answers_db->update(array('title' => $answer_title), "answer_id = :id", false, array('title' => $answer_title, 'id' => $answer_id));
                    }
                }
                // Process removed ids.
                foreach($old_answers as $answer) {
                    $answer_id = $answer['answer_id'];
                    $exist = false;
                    foreach($new_answer_ids as $id) {
                        if($answer_id == $id) {
                            $exist = true;
                            break;
                        }
                    }
                    if(!$exist) {
                        $answers_db->delete('answer_id = :id', false, array('id' => $answer_id));
                    }
                }
                $questions_db->update(array('title' => $question_title, 'correct_answer_id' => $correct_answer, 'media_type' => $question_media_type),
                    "question_id = :id", false,
                    array('title' => $question_title, 'correct_answer_id' => $correct_answer, 'media_type' => $question_media_type, 'id' => $question_id));


                // Update Media.
                if($question_media_type == 0) {
                    $images = explode(',', $question_image);
                    $image_name1 = "image0_" . $question_id;
                    $media_name1 = $this->uploadMedia($image_name1, $question_media_type);
                    if($media_name1 != null && count($media_name1) > 0){
                        if(count($images) == 0) {
                            array_push($images, $media_name1);
                        } else {
                            $images[0] = $media_name1;
                        }
                    }
                    $image_name2 = "image1_" . $question_id;
                    $media_name2 = $this->uploadMedia($image_name2, $question_media_type);
                    if($media_name2 != null && count($media_name2) > 0){
                        if(count($images) <= 1) {
                            array_push($images, $media_name2);
                        }
                        else {
                            $images[1] = $media_name2;
                        }
                    }
                    $image_name3 = "image2_" . $question_id;
                    $media_name3 = $this->uploadMedia($image_name3, $question_media_type);
                    if($media_name3 != null && count($media_name3) > 0){
                        if(count($images) <= 2) {
                            array_push($images, $media_name3);
                        }
                        else {
                            $images[2] = $media_name3;
                        }
                    }
                    $string_images = implode(',', $images);
                    $questions_db->update(array('image' => $string_images),
                        "question_id = :id", false,
                        array('image' => $string_images, 'id' => $question_id));
                } else if($question_media_type == 1) {
                    // Video.
                    $video_name = "media_video_" . $question_id;
                    $media_name = $this->uploadMedia($video_name, $question_media_type);
                    if($media_name != null && count($media_name) > 0){
                        $questions_db->update(array('video' => $media_name),
                            "question_id = :id", false,
                            array('video' => $media_name, 'id' => $question_id));
                    }
                } else if($question_media_type == 3) {
                    // PDF.
                    $pdf_name = "media_pdf_" . $question_id;
                    $media_name = $this->uploadMedia($pdf_name, $question_media_type);
                    if($media_name != null && count($media_name) > 0){
                        $questions_db->update(array('pdf' => $media_name), "question_id = :id", false, array('pdf' => $media_name, 'id' => $question_id));
                    }
                }
            }
        }
        foreach($old_questions as $question) {
            $question_id = $question['question_id'];
            $exist = false;
            foreach($question_ids as $id) {
                if($question_id == $id) {
                    $exist = true;
                    break;
                }
            }
            if(!$exist) {
                $this->removeQuestion($question_id);
            }
        }
        $this->gotoCourseListPage();
    }
    public function getUserLoginData() {
        session_start();
        echo $_SESSION['userdata'];
    }
    public function addCoursefile($post) {
        if (count($_FILES) == 0) {
            return json_encode(array('filename' => 'x'));
        }
        foreach($_FILES as $key => $file) {
            $data = array();
            switch ($post['type']) {
                case 'image': $media_name = $this->uploadFile($file, 0);  break;
                case 'pdf': $media_name = $this->uploadFile($file, 3);  break;
                case 'video': $media_name = $this->uploadFile($file, 1);break;
                case 'ppt': $media_name = $this->uploadFile($file, 2);break;
            }
        }
        return json_encode(array('filename' => $media_name));
    }

    public function addCourse($post) { // update course
        $returnDetail = array();
        $course_db  = new db('course');
        $course = $post->courseData;
        $data = array(
            'course_name' => $course->course_name,
            'course_description' => isset($course->course_description) ? $course->course_description : '',
            'course_category_id' => $course->course_category_id,
            'course_type' => $course->course_type,
            'status' => $course->status,
            'user_id' => isset($course->user_id) ? $course->user_id : 0,
            'time_limit' => $course->time_limit,
            'is_randomized' => $course->is_randomized,
            'display_error_message' => $course->display_error_message,
            'reorder' => $course->reorder,
            'is_comeback' => $course->is_comeback,
            'try_again' => $course->try_again,
            'is_global' => $course->is_global,
            'is_auto_inactive' => $course->is_auto_inactive,
            'auto_inactive_time' => isset($course->auto_inactive_time) ? $course->auto_inactive_time : '0000-00-00',
            'go_back'=>$course->go_back
        );

        $course_db->insert($data);
        $course_id = $course_db->lastInsertId;
        $returnDetail['course_id'] = $course_id;
        // Save Questions.
        $questions_db  = new db('questions');
        $answers_db = new db('answers');
        $course_question_count = $course->question_count;
        if(isset($course_question_count)) {
            for($i = 0; $i < $course_question_count; $i++) {
                $question_title = $course->questions[$i]->title;
                $question_media_type = (isset($course->questions[$i]->media_type)) ? $course->questions[$i]->media_type : 0;
                $question_answer_count = $course->questions[$i]->answer_count;
                $correct_answer = $course->questions[$i]->correct_answer;
                $question_data = array(
                    'title' => $question_title,
                    'media_type' => $question_media_type,
                    'course_id' => $course_id,
                );
                $questions_db->insert($question_data);
                $question_id = $questions_db->lastInsertId;
                $returnDetail['questions']['index'][$i] = $question_id;
                for($j = 0; $j < $question_answer_count; $j++) {
                    $answer_data = array(
                        'title' => $course->questions[$i]->answers[$j]->title,
                        'question_id' => $question_id,
                    );
                    $answers_db->insert($answer_data);
                    $answer_id = $answers_db->lastInsertId;
                    if($correct_answer == $j) {
                        $questions_db->update(array('correct_answer_id' => 0), "question_id = :id", false, array('correct_answer_id' => $answer_id, 'id' => $question_id));
                    }
                }
            }
        }
        session_start();
        $_SESSION['coursedetail'] = $returnDetail;
        echo json_encode($returnDetail);
        //$this->gotoCourseListPage();
    }
    function gotoCourseListPage() {
        $url = $this->server_url . '/#/trainingcourses';
        header("location: $url");
        exit();
    }

    public function delCourse($post) {
        $course_db = new db('course');
        $questions_db  = new db('questions');
        $answers_db = new db('answers');
        $course_id = $post->course_id;
        $user_id = $post->user_id;
        // Get all questions.
        $sql = "SELECT * FROM questions WHERE course_id = " . $course_id;
        $questions_db->select(false,false, $sql, array());
        if($questions_db->getRow()){
            while ($questions_db->getRow()) {
                $question = $questions_db->row;
                $question_id = $question['question_id'];
                // Remove all answers.
                //$answers_db->delete('question_id = :id', false, array('id' => $question_id));
                $answers_db->update(array('deleted' => 1), 'question_id = :id', false, array('id' => $question_id, 'deleted'=>1));
            }
            //$questions_db->delete('course_id = :id', false, array('id' => $course_id));
            $questions_db->update(array('deleted'=>1), 'course_id = :id', false, array('id' => $course_id, 'deleted'=>1));
        }
        //$course_db->delete('course_id = :id', false, array('id' => $post->course_id));
        $course_db->update(array('deleted'=>1), 'course_id = :id', false, array('id' => $post->course_id, 'deleted'=>1));

        // updated by Alex-cobra -20-04-11
        // add system log
        $this->addCourseLog($user_id, $course_id, 'del_course');
        // // // // //

        return ($this->getCourseData($user_id, null));
    }

    function removeQuestion($question_id) {
        $questions_db  = new db('questions');
        $answers_db = new db('answers');
        // Remove all answers.
        //$answers_db->delete('question_id = :id', false, array('id' => $question_id));
        $answers_db->update(array('deleted'=>1), 'question_id = :id', false, array('id' => $question_id, 'deleted'=>1));
        // Remove Question.
        //$questions_db->delete('question_id = :id', false, array('id' => $question_id));
        $questions_db->update(array('deleted'=>1), 'question_id = :id', false, array('id' => $question_id, 'deleted'=>1));
    }
    //newly added
    public function getSpecificReminder($post){
        $e = new db("reminders");
        $e->select("id = :id", false, false, array("id" => $post->id));
        $e->getRow();
        switch($e->row["email_name"]){
            case "Birthday": $e->row['description'] = "Email advising an employee’s birthday is approaching"; break;
            case "VISA Check": $e->row['description'] = "Email advising that an employee’s visa expiry date is approaching"; break;
            case "Training Course Due": $e->row['description'] = "Email advising that an employee’s training course is overdue"; break;
            case "Safety Data Sheet Expiry": $e->row['description'] = "Email advising that a SDS is about to expire"; break;
            case "Schedule a Service": $e->row['description'] = "Email advising plant or equipment is due for a schedule or service"; break;
            case "Test and Tag": $e->row['description'] = "Email advising testing and tagging of an item is required"; break;
            case "Safe Work Procedures": $e->row['description'] = "Email advising an employee’s SWP competency sign off is overdue"; break;
            case "Licence and Qualification": $e->row['description'] = "Email advising an employee’s licence or qualification is about to expire"; break;
            case "Probation Period": $e->row['description'] = "Email advising that an employee’s 3 month probationary period is about to expire"; break;
            case "Qualification Period": $e->row['description'] = "Email advising that an employee’s 6 month qualification period is about to expire"; break;
            case "Injury Register": $e->row['description'] = "Email advising an outstanding item on a prior injury needs to be reviewed."; break;
        }
        $reminder = $e->row;
        $reminder['employee_name'] = $this->getEmployeeName($reminder['employee_id']);
        $e->select("employee_id = :employee_id", false, false, array("employee_id" => $reminder["employee_id"]));
        $reminders = array();
        while($e->getRow()){
            switch($e->row["email_name"]){
                case "Birthday": $e->row['description'] = "Email advising an employee’s birthday is approaching"; break;
                case "VISA Check": $e->row['description'] = "Email advising that an employee’s visa expiry date is approaching"; break;
                case "Training Course Due": $e->row['description'] = "Email advising that an employee’s training course is overdue"; break;
                case "Safety Data Sheet Expiry": $e->row['description'] = "Email advising that a SDS is about to expire"; break;
                case "Schedule a Service": $e->row['description'] = "Email advising plant or equipment is due for a schedule or service"; break;
                case "Test and Tag": $e->row['description'] = "Email advising testing and tagging of an item is required"; break;
                case "Safe Work Procedures": $e->row['description'] = "Email advising an employee’s SWP competency sign off is overdue"; break;
                case "Licence and Qualification": $e->row['description'] = "Email advising an employee’s licence or qualification is about to expire"; break;
                case "Probation Period": $e->row['description'] = "Email advising that an employee’s 3 month probationary period is about to expire"; break;
                case "Qualification Period": $e->row['description'] = "Email advising that an employee’s 6 month qualification period is about to expire"; break;
                case "Injury Register": $e->row['description'] = "Email advising an outstanding item on a prior injury needs to be reviewed."; break;
            }
            array_push($reminders, $e->row);
        }
        echo json_encode(array("reminder" => $reminder, "reminders" => $reminders));
    }

    //newly added
    public function deleteReminder($post){
        $d = new db('reminders');
        $d->select("id = :id", false, false, array("id" => $post->reminder->id));
        $d->getRow();
        $employee_id = $d->row["employee_id"];
        $sql = "SELECT count(*) AS alerts_count FROM reminders WHERE alert_status = 1 AND employee_id =". $employee_id;
        $d->select(false, false, $sql);
        $d->getRow();
        $alerts_count = $d->row["alerts_count"];
        $alerts_count --;
        $d->delete('id = :id', false, array('id' => $post->reminder->id));
        $d->update(array("alerts_count" => 0), "employee_id = :id", false, array("alerts_count" => $alerts_count, "id" => $employee_id));
        $d->select(false, false, "SELECT * FROM reminders");
        $data = array();
        while($d->getRow()){
            switch($d->row["email_name"]){
                case "Birthday": $d->row['description'] = "Email advising an employee’s birthday is approaching"; break;
                case "VISA Check": $d->row['description'] = "Email advising that an employee’s visa expiry date is approaching"; break;
                case "Training Course Due": $d->row['description'] = "Email advising that an employee’s training course is overdue"; break;
                case "Safety Data Sheet Expiry": $d->row['description'] = "Email advising that a SDS is about to expire"; break;
                case "Schedule a Service": $d->row['description'] = "Email advising plant or equipment is due for a schedule or service"; break;
                case "Test and Tag": $d->row['description'] = "Email advising testing and tagging of an item is required"; break;
                case "Safe Work Procedures": $d->row['description'] = "Email advising an employee’s SWP competency sign off is overdue"; break;
                case "Licence and Qualification": $d->row['description'] = "Email advising an employee’s licence or qualification is about to expire"; break;
                case "Probation Period": $d->row['description'] = "Email advising that an employee’s 3 month probationary period is about to expire"; break;
                case "Qualification Period": $d->row['description'] = "Email advising that an employee’s 6 month qualification period is about to expire"; break;
                case "Injury Register": $d->row['description'] = "Email advising an outstanding item on a prior injury needs to be reviewed."; break;
            }
            $d->row["employee_name"] = $this->getEmployeeName($d->row["employee_id"]);
            $e = new db("user");
            $e->select("id = :id", false, false, array("id" => $d->row["employee_id"]));
            $e->getRow();
            $d->row["active"] = $e->row["active"];
            date_default_timezone_get("Australia/Sydney");
            $da = strtotime($d->row["alert_expiry"]);
            $today = date("Y-m-d");
            if($today > date("Y-m-d", $da)){
                $d->row["alert_expiry_status"] = 1;
            }else{
                $d->row["alert_expiry_status"] = 0;
            }
            array_push($data, $d->row);
        }
        echo json_encode(array("reminders" => $data));
    }

    //newly added

    public function removeLog($post) {
        $e  = new db('user_notes');
        $employee_notes_id = $post->log_id;
        $today = date("Y-m-d H:i:s");
        $emp_notes['updated_time'] = $today;
        $e->select("id = :id", false, false, array('id' => $post->log_id));
        //echo "AAAAAA";
        //var_dump($e->getRow());
        //exit;
        $rows = array();
        while ($e->getRow()) {
            array_push($rows, $e->row);
        }
        $row = array();
        $row = $rows[0];
        $data1 = array();
        $data1['who'] = $row['entered_user_name'];
        $data1['what'] = "Removed a call log ". $employee_notes_id ." on ". $row['employee_firstname'] ." ". $row['employee_lastname'] ." file.";
        $date1['time'] = $today;
        $data1['account_id'] = $row['entered_user_id'];
        $sl = new db("system_logs");
        $sl->insert($data1);
        $e->delete('id = :id', false, array('id' => $employee_notes_id));
        echo json_encode(array('success' => 200));
    }
    //newly added
    public function removeLQ($post) {
        $e  = new db('user_license');
        $employee_license_id = $post->log_id;
        $e->delete('id = :id', false, array('id' => $employee_license_id));
        echo json_encode(array('success' => 200));
    }

    //newly added
    public function removeConf($post) {
        date_default_timezone_set("Australia/Sydney");
        // $date = date('Y-m-d H:i:s');
        $today = date("Y-m-d H:i:s");
        $data1 = array();
        $e = new db("user_notes");
        $e->select("id = :id", false, false, array('id' => $post->log_id));
        $rows = array();
        while ($e->getRow()) {
            array_push($rows, $e->row);
        }
        $row = array();
        $row = $rows[0];
        $data1['who'] = $row['entered_user_name'];
        $data1['what'] = "Removed a confidentiality flag from ". $row['employee_firstname'] ." ". $row['employee_lastname'] ." file.";
        $date1['time'] = $today;
        $data1['account_id'] = $row['entered_user_id'];
        $sl = new db("system_logs");
        $sl->insert($data1);
        $e->update(array("mark_c" => 0), "id = :id", false, array("mark_c" => 0, "id" => $post->log_id));
        echo json_encode(array('success' => 200));
    }

    //newly added
    public function calcScore($scores){
        $total_score = 0;
        foreach($scores as $val){
            $score_array = explode(",", rtrim($val, ","));
            foreach($score_array as $key => $val1){
                $total_score += intval($val1);
            }
        }
        return $total_score;
    }
    //newly added
    public function getScoresByYear($post){
        $year = $post->year;
        $userId = $post->userId;
        $sql = "SELECT distinct pf.employee_id FROM form_reviews AS fr INNER JOIN performance_forms AS pf ON pf.id = fr.p_forms_id AND pf.account_id = ".$userId." WHERE fr.form_status = 'completed' AND YEAR(completed_date) = ".$year;
        $fr = new db("form_reviews");
        $fr->select(false, false, $sql);
        $names = array();
        $scores = array();
        while($fr->getRow()){
            $nm = $this->getEmployeeName($fr->row["employee_id"]);
            $sql = "SELECT fr.scores FROM form_reviews AS fr INNER JOIN performance_forms AS pf ON pf.id = fr.p_forms_id AND pf.employee_id = ".$fr->row["employee_id"]. " AND pf.account_id = ".$userId." WHERE fr.form_status = 'completed' AND YEAR(completed_date) = ".$year;
            $fr1 = new db("form_reviews");
            $fr1->select(false, false, $sql);
            $score_array = array();
            while($fr1->getRow()){
                array_push($score_array, $fr1->row["scores"]);
            }
            $score = $this->calcScore($score_array);
            array_push($names, $nm);
            array_push($scores, $score);
        }
        echo json_encode(array("names" => $names, "scores" => $scores));
    }

    //newly added
    public function getScoresByPosition($post){
        $names = array();
        $scores = array();
        $year = $post->year;
        $user_id = $post->user_id;
        $account_id = $post->account_id;
        $sql = "SELECT position FROM user_work WHERE user_id = ".$user_id." ORDER BY workdate_added DESC LIMIT 1";
        $usr = new db("user_work");
        $usr->select(false, false, $sql);
        $usr->getRow();
        $position = $usr->row["position"];
        $pf = new db("performance_forms");
        $sql = "SELECT DISTINCT pf.employee_id, pf.id FROM form_reviews AS fr INNER JOIN performance_forms AS pf ON pf.account_id = ".$account_id." AND pf.id = fr.p_forms_id INNER JOIN user_work AS usrwk ON usrwk.position = ".$position." AND usrwk.user_id = pf.employee_id WHERE fr.form_status = 'completed' AND YEAR(fr.assessment_date) =".$year;
        $pf->select(false,false,$sql);
        while($pf->getRow()){
            //var_dump($pf->row);
            $sql = "SELECT fr.scores FROM form_reviews AS fr INNER JOIN performance_forms AS pf ON fr.p_forms_id = pf.id AND pf.employee_id = ".$pf->row["employee_id"];
            $nm = $this->getEmployeeName($pf->row["employee_id"]);
            $total_score = 0;
            $fr = new db("form_reviews");
            $fr->select(false, false, $sql);
            $score_array = array();
            while($fr->getRow()){
                array_push($score_array, $fr->row["scores"]);
            }
            $score = $this->calcScore($score_array);
            array_push($names, $nm);
            array_push($scores, $score);
        }
        echo json_encode(array("names" => $names, "scores" => $scores, "current_position" => $this->getPositionName($position)));
    }
    //newly added
    public function getScoresBySitelocation($post){
        $names = array();
        $scores = array();
        $year = $post->year;
        $user_id = $post->user_id;
        $account_id = $post->account_id;
        $sql = "SELECT site_location FROM user_work WHERE user_id = ".$user_id." ORDER BY workdate_added DESC LIMIT 1";
        $usr = new db("user_work");
        $usr->select(false, false, $sql);
        $usr->getRow();
        $site_location = $usr->row["site_location"];
        $pf = new db("performance_forms");
        $sql = "SELECT DISTINCT pf.employee_id, pf.id FROM form_reviews AS fr INNER JOIN performance_forms AS pf ON pf.account_id = ".$account_id." AND pf.id = fr.p_forms_id INNER JOIN user_work AS usrwk ON usrwk.site_location = ".$site_location." AND usrwk.user_id = pf.employee_id WHERE fr.form_status = 'completed' AND YEAR(fr.assessment_date) =".$year;
        $pf->select(false,false,$sql);
        while($pf->getRow()){
            //var_dump($pf->row);
            $sql = "SELECT fr.scores FROM form_reviews AS fr INNER JOIN performance_forms AS pf ON fr.p_forms_id = pf.id AND pf.employee_id = ".$pf->row["employee_id"];
            $nm = $this->getEmployeeName($pf->row["employee_id"]);
            $total_score = 0;
            $fr = new db("form_reviews");
            $fr->select(false, false, $sql);
            $score_array = array();
            while($fr->getRow()){
                array_push($score_array, $fr->row["scores"]);
            }
            $score = $this->calcScore($score_array);
            array_push($names, $nm);
            array_push($scores, $score);
        }
        echo json_encode(array("names" => $names, "scores" => $scores, "site_location" => $this->getSiteLocation($site_location)));
    }
    //newly added
    public function allScores($id,$year,$userId){
        $employee_name = array();
        $total_score = array();
        $e = new db("user_work");
        $sql = "SELECT DISTINCT(site_location) FROM user_work WHERE user_id = ".$id." ORDER BY workdate_added DESC LIMIT 1";
        $e->select(false, false, $sql);
        $e->getRow();
        $site_location = $e->row["site_location"];
        $pf = new db("performance_forms");
        $sql = "SELECT DISTINCT pf.employee_id, pf.scores, CONCAT(user.firstname, ' ', user.lastname) AS nm  FROM performance_forms AS pf INNER JOIN user_work AS ewk ON ewk.user_id = pf.employee_id AND ewk.site_location = ".$site_location.
                   " WHERE pf.scores <> '' AND YEAR(review_date) = ".$year;
        $pf->select(false,false,$sql);
        while($pf->getRow()){
            array_push($employee_name, $pf->row["nm"]);
            $scores = explode("~#", $pf->row["scores"]);
            $scores = $scores[sizeof($scores) - 1];
            $scores = explode(",", rtrim($scores, ","));
            $score = 0;
            foreach($scores as $key => $value){
                $score += intval($value);
            }
            array_push($total_score, $score);
        }
        $da = new db("data");
        $da->select("id = :id", false, false, array("id" => $site_location));
        $da->getRow();
        $site_location_name = $da->row["display_text"];
        echo json_encode(array("names" => $employee_name, "scores" => $total_score, "site_location" => $site_location_name));
    }

    //newly added
    public function allScoresByPosition($id,$year,$userId){
        $employee_name = array();
        $total_score = array();
        $e = new db("user_work");
        $sql = "SELECT DISTINCT user_work.* FROM user_work WHERE user_id = ".$id." ORDER BY workdate_added DESC LIMIT 1";
        $e->select(false, false, $sql);
        $e->getRow();
        $site_location = $e->row["site_location"];
        $position = $e->row["position"];
        $pf = new db("performance_forms");
        $sql = "SELECT DISTINCT pf.employee_id, fr.scores, CONCAT(user.firstname, ' ', user.lastname) AS nm  FROM performance_forms AS pf INNER JOIN user_work AS ewk ON ewk.user_id = pf.employee_id AND ewk.site_location = ".$site_location." AND ewk.position = ".$position.
                    " WHERE pf.scores <> '' AND YEAR(pf.review_date) = ".$year;
        $pf->select(false,false,$sql);
        while($pf->getRow()){
            array_push($employee_name, $pf->row["nm"]);
            $scores = explode("~#", $pf->row["scores"]);
            $scores = $scores[sizeof($scores) - 1];
            $scores = explode(",", rtrim($scores, ","));
            $score = 0;
            foreach($scores as $key => $value){
                $score += intval($value);
            }
            array_push($total_score, $score);
        }
        $da = new db("data");
        $da->select("id = :id", false, false, array("id" => $site_location));
        $da->getRow();
        $site_location_name = $da->row["display_text"];
        $da->select("id = :id", false, false, array("id" => $position));
        $da->getRow();
        $position_name = $da->row["display_text"];
        echo json_encode(array("names" => $employee_name, "scores" => $total_score, "site_location" => $site_location_name, "position" => $position_name));
    }
    //newly added
    public function getDaysOverdueBySiteLocation($userId){
       $site_locations = array();
       $days_overdue = array();
       $ek = new db("user_work");
       $sql = "SELECT DISTINCT ewk.site_location as sl FROM user_work AS ewk INNER JOIN performance_forms AS pf ON pf.employee_id = ewk.user_id AND pf.account_id = ".$userId. " ORDER BY ewk.workdate_added DESC LIMIT 1";
       $ek->select(false,false,$sql);
       while($ek->getRow()){
           $da = new db("data");
           $da->select("id = :id", false, false, array("id" => $ek->row["sl"]));
           $da->getRow();
           array_push($site_locations, $da->row["display_text"]);
           $sql = "SELECT pf.assessment_date, pf.review_date, pf.form_status FROM performance_forms AS pf INNER JOIN user_work AS emk ON emk.site_location = ".$ek->row["sl"]. " AND emk.user_id = pf.employee_id";
           $pf = new db("performance_forms");
           $pf->select(false, false, $sql);
           $over_due = 1;
           while($pf->getRow()){
               if($pf->row["form_status"] == 'completed'){
                    if($pf->row["assessment_date"] < $pf->row["review_date"]){
                        $over_due += $this->dateDiff($pf->row["assessment_date"], $pf->row["review_date"]);
                    }
               }else{
                    $today = date_format(new DateTime(), "Y-m-d");
                    if($pf->row["assessment_date"] < $today){
                        $over_due += $this->dateDiff($pf->row["assessment_date"], $today);
                    }
               }
           }
           array_push($days_overdue, $over_due);
       }
       $pieData = array();
       $pieData["data"] = $days_overdue;
       $pieData["label"] = $site_locations;
       echo json_encode(array("pieData" => $pieData));
    }
    //newly added
    public function getDaysOverdueDetail($param, $userId){
        $days_overdue = array();
        $employee_names = array();
        $da = new db("data");
        $da->select("display_text = :id", false, false, array("id" => $param));
        $da->getRow();
        $site_location = $da->row["id"];
        $sql = "SELECT DISTINCT pf.employee_id, pf.assessment_date, pf.review_date, pf.form_status, CONCAT(user.firstname, ' ', user.lastname) AS nm FROM performance_forms AS pf INNER JOIN user_work AS emk ON emk.site_location = ".$site_location. " AND emk.user_id = pf.employee_id INNER JOIN user as user ON user.id = emk.user_id";
        $pf = new db("performance_forms");
        $pf->select(false, false, $sql);
        while($pf->getRow()){
            $pf1 = new db("performance_forms");
            $pf1->select("employee_id = :id", false, false, array("id" => $pf->row["employee_id"]));
            $over_due = 1;
            while($pf1->getRow()){
                if($pf1->row["form_status"] == 'completed'){
                    if($pf1->row["assessment_date"] < $pf1->row["review_date"]){
                        $over_due += $this->dateDiff($pf1->row["assessment_date"], $pf1->row["review_date"]);
                    }
                }else{
                    $today = date_format(new DateTime(), "Y-m-d");
                    if($pf1->row["assessment_date"] < $today){
                        $over_due += $this->dateDiff($pf1->row["assessment_date"], $today);
                    }
                }
            }
            array_push($employee_names, $pf->row["nm"]);
            array_push($days_overdue, $over_due);
        }
        $pieData = array();
        $pieData["data"] = $days_overdue;
        $pieData["label"] = $employee_names;
        echo json_encode(array("pieData" => $pieData));
    }
    public function dateDiff($date1, $date2){
        $review_date = date_create($date1);
        $assessment_date = date_create($date2);
        $diff = date_diff($review_date, $assessment_date);
        return intval($diff->format("%a"));
    }
    // Search Courses.
    public function searchCourse($post) {
        $keyword = $post->keyword;
        $course_table = new db('course');
        $data = array();
        $sql = "SELECT * FROM course WHERE course_name LIKE '%". $keyword ."%'";
        $course_table->select(false,false, $sql, array());
        while ($course_table->getRow()) {
            array_push($data, $course_table->row);
        }
        echo json_encode(array('courses' => $data));
    }

    // updated by Alex-cobra -20-04-11
    // Alloc Course.
    public function allocCourse($post) {
        $db = new db('alloc_course');
        // prevent multi-allocate course
        $course_id = $post->allocCourseData->course_id;
        $employee_id = $post->allocCourseData->employee_id;
        $today = date('Y-m-d');
        if($today<date('Y-m-d', strtotime($post->allocCourseData->alloc_date)))
            $post->allocCourseData->status = -1;
        $db->select('employee_id = :eid AND course_id = :cid', false, false, array('eid'=>$employee_id, 'cid'=>$course_id));
        $db->getRow();
        if($db->numRows > 0) {
            echo json_encode(array('alloc_course_id'=>-99));
            return;
        }
        else {
            $sql = "SELECT * FROM alloc_course ORDER BY id DESC LIMIT 1";
            $db->select(false, false, $sql);
            $row = $db->getRow();
            $lastId = $row['id'];
            $data = array(
                'course_id' => $post->allocCourseData->course_id,
                'course_supervisor' => $post->allocCourseData->course_supervisor,
                'employee_id' => $post->allocCourseData->employee_id,
                'expire_hours' => $post->allocCourseData->expire_hours,
                'alloc_date' => $post->allocCourseData->alloc_date,
                'is_sending_email' => $post->allocCourseData->is_sending_email,
                'daily_reminder_email_receiver' => $post->allocCourseData->daily_reminder_email_receiver,
                'status' => $post->allocCourseData->status,
                'user_id' => $post->allocCourseData->user_id,
                'created_on' => date('Y-m-d H:i:s'),
                'updated_on' => date('Y-m-d H:i:s'),
            );
            
            if(isset($data['daily_reminder_email_receiver']) && $data['daily_reminder_email_receiver']!=null)
                $sql = sprintf("INSERT INTO alloc_course (id, course_id, course_supervisor, employee_id, expire_hours, alloc_date, is_sending_email, daily_reminder_email_receiver, status, user_id, created_on, updated_on) VALUES (%d, %d, %d, %d, %d, '%s', %d, '%s', %d, %d, '%s', '%s')",
                    $lastId+1, $data['course_id'], $data['course_supervisor'], $data['employee_id'], $data['expire_hours'], $data['alloc_date'], $data['is_sending_email'], $data['daily_reminder_email_receiver'], $data['status'], $data['user_id'], $data['created_on'], $data['updated_on']);
            else
                $sql = sprintf("INSERT INTO alloc_course (id, course_id, course_supervisor, employee_id, expire_hours, alloc_date, is_sending_email, status, user_id, created_on, updated_on) VALUES (%d, %d, %d, %d, %d, '%s', %d, %d, %d, '%s', '%s')",
                    $lastId+1, $data['course_id'], $data['course_supervisor'], $data['employee_id'], $data['expire_hours'], $data['alloc_date'], $data['is_sending_email'], $data['status'], $data['user_id'], $data['created_on'], $data['updated_on']);
                
            $db->select(false, false, $sql);
            $email_content = "";

            if($post->allocCourseData->is_sending_email == 1 && date('Y-m-d')==date('Y-m-d', strtotime($post->allocCourseData->alloc_date))) {
                $email_content = $this->sendEmailForAllocCourse($post->allocCourseData->course_id, $post->allocCourseData->employee_id, $post->allocCourseData->user_id, $post->allocCourseData->expire_hours, $post->allocCourseData->alloc_date);
            }

            echo json_encode(array('alloc_course_id ' => $lastId+1, 'alloc_date' => $post->allocCourseData->alloc_date, 'email_content' => $email_content));

            // add system log
            $account_id = $post->userData->account_id;
            $this->addCourseLog($account_id, $lastId+1, 'alloc_course', $data['employee_id']);
        }
    }
    // // // // //

    public function updateAllocCourse($post) {
        //var_dump($post->allocCourseData);exit;
        $alloc_course_table = new db('alloc_course');
        $id = $post->allocCourseData->id;
        $course_id = $post->allocCourseData->course_id;
        $course_description = $post->allocCourseData->course_description;
        $course_supervisor = $post->allocCourseData->course_supervisor;
        $employee_id = $post->allocCourseData->employee_id;
        $expire_hours = $post->allocCourseData->expire_hours;
        if($post->allocCourseData->can_add_extra){
            $extra_hours = $post->allocCourseData->extra_hours;
            if($extra_hours!=0)
                $extra_expiry_date = date("Y-m-d H:i:s", strtotime(sprintf("+%d hours", $extra_hours)));
            else $extra_expiry_date = null;
        }else{
            $extra_hours = 0;
            $extra_expiry_date = null;
        }
        $alloc_date = $post->allocCourseData->alloc_date;
        $is_sending_email = $post->allocCourseData->is_sending_email;
        $daily_reminder_email_receiver = isset($post->allocCourseData->daily_reminder_email_receiver)?$post->allocCourseData->daily_reminder_email_receiver:null;
        $status = $post->allocCourseData->status;
        $user_id = $post->allocCourseData->user_id;
        $alloc_date = date('Y-m-d H:i:s', strtotime($post->allocCourseData->alloc_date));
        
        $alloc_course_table->update(array('course_id' => $course_id, 'course_supervisor' => $course_supervisor, 'employee_id' => $employee_id,
            'expire_hours' => $expire_hours, 'extra_hours' => $extra_hours, 'extra_expiry_date'=>$extra_expiry_date, 'alloc_date' => $alloc_date, 'is_sending_email' => $is_sending_email, 'daily_reminder_email_receiver' => $daily_reminder_email_receiver, 'status' => $status), "id = :id", false,
            array('course_id' => $course_id, 'course_supervisor' => $course_supervisor, 'employee_id' => $employee_id,
                'expire_hours' => $expire_hours, 'extra_hours' => $extra_hours, 'extra_expiry_date'=>$extra_expiry_date, 'alloc_date' => $alloc_date, 'is_sending_email' => $is_sending_email, 'daily_reminder_email_receiver'=>$daily_reminder_email_receiver, 'status' => $status, 'id' => $id));
        $email_content = null;
        if($post->allocCourseData->is_sending_email == 1 && date('Y-m-d')==date('Y-m-d', strtotime($post->allocCourseData->alloc_date))) {
            $email_content = $this->sendEmailForAllocCourse($post->allocCourseData->course_id, $post->allocCourseData->employee_id, $post->allocCourseData->user_id, $post->allocCourseData->expire_hours, $post->allocCourseData->alloc_date);
        }
        echo json_encode(array('alloc_course_id ' => $id, 'alloc_date' => $post->allocCourseData->alloc_date, 'content' => $email_content));
    }
    /*
    // Course Status of employees
    SELECT DISTINCT(employee_id) AS UID,
            (SELECT site_location FROM user_work WHERE user_id = UID ORDER BY workdate_added LIMIT 1) AS LOCID,
            (SELECT CONCAT(firstname,' ',lastname) FROM user WHERE id = UID) AS EmployeeName,
            (SELECT display_text FROM data WHERE id = LOCID) AS SiteLocation,
            (SELECT COUNT(*) FROM alloc_course WHERE status <> 1 AND employee_id = UID) AS NumCoursesIncomplete,
            (SELECT COUNT(*) FROM alloc_course WHERE status <> 1 AND employee_id = UID AND TIMEDIFF(DATE_ADD(alloc_date, INTERVAL expire_hours HOUR), NOW()) < 0) AS NumCoursesOverdue,
            (SELECT COUNT(*) FROM alloc_course WHERE status = 1 AND employee_id = UID) AS NumCoursesCompleted
      FROM alloc_course
     WHERE 1
    */


    public function updateAllocCourseStatus(){//update allocated course status value-   0: pending, 1: completed, 2:overdue, 3: incomplete
        $db = new db('alloc_course');
        $today = date('Y-m-d');
        //pending
        $sql = "UPDATE alloc_course ac SET ac.status=0 WHERE (ac.alloc_date > NOW() - INTERVAL ac.expire_hours HOUR AND CAST(ac.alloc_date as DATE) <= '".$today."') AND (ac.started_date IS NULL OR STR_TO_DATE(ac.started_date, '%Y-%m-%d') IS NULL) AND (ac.completed_date IS NULL OR STR_TO_DATE(ac.completed_date, '%Y-%m-%d') IS NULL) AND ac.attempts=0";
        $db->select(false, false, $sql);
        //completed
        $sql = "UPDATE alloc_course ac SET ac.status=1 WHERE (ac.completed_date IS NOT NULL OR STR_TO_DATE(ac.completed_date, '%Y-%m-%d') IS NOT NULL) AND ac.attempts!=0 AND ac.status!=4";
        $db->select(false, false, $sql);
        //overdue
        $sql = "UPDATE alloc_course ac set ac.status=2 WHERE (ac.alloc_date < NOW() - INTERVAL ac.expire_hours HOUR) AND ac.status!=1 AND ac.status!=2";
        $db->select(false, false, $sql);
        //incomplete
        $sql = "UPDATE alloc_course ac SET ac.status=3 WHERE (ac.started_date IS NOT NULL AND STR_TO_DATE(ac.started_date, '%Y-%m-%d') IS NOT NULL) AND (ac.completed_date IS NULL OR STR_TO_DATE(ac.completed_date, '%Y-%m-%d') IS NULL) AND (ac.alloc_date > NOW() - INTERVAL ac.expire_hours HOUR AND ac.alloc_date <= NOW())";
        $db->select(false, false, $sql);
    }

    public function updateCourseInactive(){
        $db = new db('course');
        $sql = "Update course cc set cc.status = 0 WHERE (cc.auto_inactive_time >= '".date('Y-m-d')."' ) AND cc.is_auto_inactive = 1 AND cc.auto_inactive_time is not null ";
        $db->select(false, false, $sql);

    }

    public function getAllocCourseData($userData, $showMode, $ownMode, $locationMode) {
        $this->updateCourseInactive();
        $locationMode = $locationMode?$locationMode:'';
        $userWorkData = new db('user_work');
        $sql = "SELECT u.*, uw.* from user u join user_work uw on u.id=uw.user_id where u.id=".$userData->id." order by uw.workdate_added desc limit 1";
        $userWorkData->select(false, false, $sql, array());
        $userLocation = $userWorkData->getRow()['site_location'];
        $alloc_course = new db('alloc_course');
        /*$sql = "UPDATE alloc_course SET status=3 where status=0 AND completed_date='0000-00-00 00:00:00'";
        $alloc_course->select(false, false, $sql, array());*/
        $this->updateAllocCourseStatus();
        $data = array();

        if($showMode==0){
            $sql = "SELECT ac.id, ac.status, ac.alloc_date, ac.expire_hours, ac.extra_hours, ac.extra_expiry_date, ac.completed_date, course.*, ac.employee_id AS UID,
                        (SELECT site_location FROM user_work WHERE user_id = UID ORDER BY workdate_added DESC LIMIT 1) AS LOCID, 
                        (SELECT display_text FROM data WHERE id = LOCID) AS SiteLocation,
                        user.firstname, user.lastname, CONCAT(user.firstname,' ',user.lastname) AS PersonName,
                        DATE_FORMAT(ac.alloc_date, '%d-%m-%Y') AS AllocDate,
                        (SELECT DATEDIFF(DATE_ADD(ac.alloc_date, INTERVAL ac.expire_hours HOUR), NOW())) AS TimeLeft,
                    (CASE
                        WHEN ac.status = 0 THEN 'Pending'
                        WHEN ac.status = 1 THEN 'Completed'
                        WHEN ac.status = 2 THEN 'Overdue'
                        WHEN ac.status = 3 THEN 'Incomplete'
                        WHEN ac.status = 4 THEN 'Submitted'
                    END) AS course_status, course.is_locked
                 FROM alloc_course AS ac 
                 JOIN course AS course ON ac.course_id = course.course_id
                 JOIN user AS user ON ac.employee_id = user.id 
                WHERE ac.user_id = :u AND user.active = 1".
                (!$ownMode?" AND ac.employee_id= :e":"")
                ." ORDER BY ac.created_on DESC";
            if(!$ownMode)
                $alloc_course->select(false,false, $sql, array('u' => $userData->id, 'e' => $userData->id));
            else
                $alloc_course->select(false,false, $sql, array('u' => $userData->id));
        }
        else if($showMode==1){
            $sql = "SELECT ac.id, ac.status, ac.alloc_date, ac.expire_hours, ac.extra_hours, ac.extra_expiry_date, ac.completed_date, course.*, ac.employee_id AS UID,
                        (SELECT site_location FROM user_work WHERE user_id = UID ORDER BY workdate_added DESC LIMIT 1) AS LOCID, 
                        (SELECT display_text FROM data WHERE id = LOCID) AS SiteLocation,
                        user.firstname, user.lastname, CONCAT(user.firstname,' ',user.lastname) AS PersonName,
                        DATE_FORMAT(ac.alloc_date, '%d-%m-%Y') AS AllocDate,
                        (SELECT DATEDIFF(DATE_ADD(ac.alloc_date, INTERVAL ac.expire_hours HOUR), NOW())) AS TimeLeft,
                    (CASE
                        WHEN ac.status = 0 THEN 'Pending'
                        WHEN ac.status = 1 THEN 'Completed'
                        WHEN ac.status = 2 THEN 'Overdue'
                        WHEN ac.status = 3 THEN 'Incomplete'
                        WHEN ac.status = 4 THEN 'Submitted'
                    END) AS course_status, course.is_locked
                 FROM alloc_course AS ac 
                 JOIN course AS course ON ac.course_id = course.course_id
                 JOIN user AS user ON ac.employee_id = user.id 
                WHERE ac.user_id = :u".
                (!$ownMode?" AND ac.employee_id= :e":"")
                ." ORDER BY ac.created_on DESC";
            if(!$ownMode)
                $alloc_course->select(false,false, $sql, array('u' => $userData->id, 'e' => $userData->id));
            else
                $alloc_course->select(false,false, $sql, array('u' => $userData->id));
        }
        // // // // //

        while ($alloc_course->getRow()) {
            if ($alloc_course->row['course_status'] === 'Overdue' && $alloc_course->row['is_locked']==1 && ($alloc_course->row['extra_hours']==0 || date('Y-m-d H:i:s')>date('Y-m-d H:i:s', strtotime($alloc_course->row['extra_expiry_date'])))) {
                $alloc_course->row['course_status'] = 'Expired';
            }
            if($alloc_course->row['course_status']=="Overdue" && date('Y-m-d H:i:s')<=date('Y-m-d H:i:s', strtotime($alloc_course->row['extra_expiry_date']))){
                $date1 = date('Y-m-d H:i:s', strtotime($alloc_course->row['extra_expiry_date']));
                $date2 = date('Y-m-d H:i:s');
                $diff = strtotime($date2) - strtotime($date1); 
                $days_left =  abs(round($diff / 86400)); 
                $alloc_course->row['TimeLeft'] = $days_left;
            }
            $alloc_course->row['type']="";
            if($alloc_course->time_limit>0) $alloc_course->row['type'].='T';
            if($alloc_course->is_randomized==1) $alloc_course->row['type'].='P';
            if($alloc_course->is_locked==1) $alloc_course->row['type'].='L';
            if($alloc_course->display_error_message==1) $alloc_course->row['type'].='E';
            if($alloc_course->reorder==1) $alloc_course->row['type'].='R';
            if($alloc_course->correct_only==1) $alloc_course->row['type'].='A';
            if($alloc_course->is_comeback==1) $alloc_course->row['type'].='C';
            if($alloc_course->try_again==1) $alloc_course->row['type'].='W';
            if($alloc_course->is_auto_inactive==1) $alloc_course->row['type'].='I';
            if($alloc_course->go_back==1) $alloc_course->row['type'].='G';
            if(($locationMode==0 && $userLocation==$alloc_course->row['LOCID']) || $locationMode==1)
                array_push($data, $alloc_course->row);
        }
        echo json_encode(array('alloc_courses' => $data));
    }

    public function getAnalyticsOfLQTrainingCourses($userData, $showMode, $courseStatus, $filterParams){
        $filterParams = json_decode($filterParams);
        if($filterParams->filter_fromdate!="") $filterParams->filter_fromdate = date('Y-m-d', strtotime("+1 day", strtotime($filterParams->filter_fromdate)));
        if($filterParams->filter_todate!="") $filterParams->filter_todate = date('Y-m-d', strtotime("+1 day", strtotime($filterParams->filter_todate)));
        $db = new db('user_license');
        $data = array();
        if($showMode==0){
            $subsql = "SELECT ul.*, ae.startsAt, ae.endsAt, (SELECT site_location FROM user_work WHERE user_id = ul.user_id ORDER BY workdate_added DESC LIMIT 1) AS LOCID, (SELECT display_text FROM data WHERE id = LOCID) AS SiteLocation
                            FROM user_license ul 
                            JOIN data d ON ul.license_name=d.display_text
                            JOIN user u ON ul.user_id=u.id
                            JOIN alloc_event ae ON ul.alloc_event_id=ae.id
                            WHERE d.type='event_license_name' AND u.active=1 AND u.deleted=0 AND ul.lqstatus=".$courseStatus;
            if($filterParams->filter_fromdate!="") $subsql.= " AND DATE_FORMAT(ae.endsAt, '%Y-%m-%d')>='".$filterParams->filter_fromdate."'";
            if($filterParams->filter_todate!="") $subsql.= " AND DATE_FORMAT(ae.startsAt, '%Y-%m-%d')<='".$filterParams->filter_todate."'";
            if($filterParams->dep!="") $subsql.= " AND (SELECT data.display_text FROM user_work JOIN data on user_work.department=data.id WHERE user_work.user_id = ul.user_id ORDER BY user_work.workdate_added DESC LIMIT 1) = '".$filterParams->dep."'";
            if($filterParams->pos!="") $subsql.= " AND (SELECT data.display_text FROM user_work JOIN data on user_work.position=data.id WHERE user_work.user_id = ul.user_id ORDER BY user_work.workdate_added DESC LIMIT 1) = '".$filterParams->pos."'";
            if($filterParams->loc!="") $subsql.= " AND (SELECT data.display_text FROM user_work JOIN data on user_work.site_location=data.id WHERE user_work.user_id = ul.user_id ORDER BY user_work.workdate_added DESC LIMIT 1) = '".$filterParams->loc."'";
            if($filterParams->emp!="") $subsql.= " AND CONCAT(user.firstname,' ',user.lastname) = '".$filterParams->emp."'";

            $sql = "SELECT tbl1.location, tbl2.total_count FROM 
                        (SELECT data.id, data.display_text AS location
                            FROM (SELECT site_location FROM (SELECT site_location FROM user_work INNER JOIN 
                                    (SELECT id FROM user WHERE deleted = 0 AND account_id = :u) t_employee ON user_work.user_id = t_employee.id ) t1
                                    GROUP BY site_location) t_sitelocation
                                    INNER JOIN data
                                    ON data.id = t_sitelocation.site_location
                                    WHERE data.account_id=:u) tbl1
                        LEFT JOIN (SELECT mttt.SiteLocation, COUNT(*) AS total_count FROM (SELECT mtt.SiteLocation, mtt.license_name FROM ("
    				.$subsql.
    				" ) mtt) mttt GROUP BY mttt.SiteLocation) tbl2 ON tbl1.location=tbl2.SiteLocation ORDER BY tbl1.location ASC";
            $db->select(false, false, $sql, array('u' => $userData->account_id));
        }
        else if($showMode==1){
            $subsql = "SELECT ul.*, ae.startsAt, ae.endsAt, (SELECT site_location FROM user_work WHERE user_id = ul.user_id ORDER BY workdate_added DESC LIMIT 1) AS LOCID, (SELECT display_text FROM data WHERE id = LOCID) AS SiteLocation
                            FROM user_license ul 
                            JOIN data d ON ul.license_name=d.display_text
                            JOIN user u ON ul.user_id=u.id
                            JOIN alloc_event ae ON ul.alloc_event_id=ae.id
                            WHERE d.type='event_license_name' AND u.deleted=0 AND ul.lqstatus=".$courseStatus;
            if($filterParams->filter_fromdate!="") $subsql.= " AND DATE_FORMAT(ae.endsAt, '%Y-%m-%d')>='".$filterParams->filter_fromdate."'";
            if($filterParams->filter_todate!="") $subsql.= " AND DATE_FORMAT(ae.startsAt, '%Y-%m-%d')<='".$filterParams->filter_todate."'";
            if($filterParams->dep!="") $subsql.= " AND (SELECT data.display_text FROM user_work JOIN data on user_work.department=data.id WHERE user_work.user_id = ul.user_id ORDER BY user_work.workdate_added DESC LIMIT 1) = '".$filterParams->dep."'";
            if($filterParams->pos!="") $subsql.= " AND (SELECT data.display_text FROM user_work JOIN data on user_work.position=data.id WHERE user_work.user_id = ul.user_id ORDER BY user_work.workdate_added DESC LIMIT 1) = '".$filterParams->pos."'";
            if($filterParams->loc!="") $subsql.= " AND (SELECT data.display_text FROM user_work JOIN data on user_work.site_location=data.id WHERE user_work.user_id = ul.user_id ORDER BY user_work.workdate_added DESC LIMIT 1) = '".$filterParams->loc."'";
            if($filterParams->emp!="") $subsql.= " AND CONCAT(user.firstname,' ',user.lastname) = '".$filterParams->emp."'";

            $sql = "SELECT tbl1.location, tbl2.total_count FROM 
                        (SELECT data.id, data.display_text AS location
                            FROM (SELECT site_location FROM (SELECT site_location FROM user_work INNER JOIN 
                                    (SELECT id FROM user WHERE deleted = 0 AND account_id = :u) t_employee ON user_work.user_id = t_employee.id ) t1
                                    GROUP BY site_location) t_sitelocation
                                    INNER JOIN data
                                    ON data.id = t_sitelocation.site_location
                                    WHERE data.account_id=:u) tbl1
                        LEFT JOIN (SELECT mttt.SiteLocation, COUNT(*) AS total_count FROM (SELECT mtt.SiteLocation, mtt.license_name FROM ("
    				.$subsql.
    				" ) mtt) mttt GROUP BY mttt.SiteLocation) tbl2 ON tbl1.location=tbl2.SiteLocation ORDER BY tbl1.location ASC";
            $db->select(false, false, $sql, array('u' => $userData->account_id));
        }
        while ($db->getRow()) {
            $data[$db->row['location']] = $db->row['total_count'];
            //array_push($data, $alloc_course->row);
        }
        echo json_encode(array('location_report' => $data));
    }
    public function getAnalyticsOfLQTrainingCourseCost($userData, $showMode, $filterParams){
        /*var_dump($userData);
        var_dump($showMode);
        var_dump($filterParams);
        exit;
        */
        $filterParams = json_decode($filterParams);
        if($filterParams->filter_fromdate!="") $filterParams->filter_fromdate = date('Y-m-d', strtotime("+1 day", strtotime($filterParams->filter_fromdate)));
        if($filterParams->filter_todate!="") $filterParams->filter_todate = date('Y-m-d', strtotime("+1 day", strtotime($filterParams->filter_todate)));

        $db = new db('user_license');
        $data = array();
        $sql = "SELECT data.id, data.display_text AS location
                FROM (
                	SELECT site_location FROM (
                		SELECT site_location FROM user_work 
                		INNER JOIN (SELECT id FROM user WHERE deleted = 0 AND account_id = 2) t_employee ON user_work.user_id = t_employee.id ) t1
                		GROUP BY site_location
                	) t_sitelocation
                INNER JOIN data ON data.id = t_sitelocation.site_location
                WHERE data.account_id=".$userData->account_id."
                ORDER BY location ASC";
        $db->select(false, false, $sql);
        $locations = array();
        while ($db->getRow()) {
            array_push($locations, $db->row);
        }
        $sql = "SELECT tt.SiteLocation, COUNT(tt.id) AS course_count, SUM(tt.cost) AS total_cost, SUM(tt.course_hour) AS total_hours FROM (
                    	SELECT ul.id, ul.alloc_event_id, ul.cost, 
                    		(SELECT site_location FROM user_work WHERE user_id = ul.user_id ORDER BY workdate_added DESC LIMIT 1) AS LOCID, 
                    		(SELECT display_text FROM data WHERE id = LOCID) AS SiteLocation, 
                    		TIMESTAMPDIFF(HOUR, ae.startsAt, ae.endsAt) AS course_hour, 
                    		(SELECT hourly_rate FROM user_work WHERE user_id = ul.user_id ORDER BY workdate_added DESC LIMIT 1) AS hourly_rate, 
                    		TIMESTAMPDIFF(HOUR, ae.startsAt, ae.endsAt)*(SELECT hourly_rate FROM user_work WHERE user_id = ul.user_id ORDER BY workdate_added DESC LIMIT 1) AS wasted_cost
                    	FROM user_license ul 
                    	JOIN data d ON ul.license_name=d.display_text
                    	JOIN user u ON ul.user_id=u.id
                    	JOIN alloc_event ae ON ul.alloc_event_id=ae.id
                    	WHERE d.type='event_license_name' ";
        if($showMode==0)$sql.=" AND u.active=1 ";
        $sql.=" AND u.deleted=0 ";
        if($filterParams->filter_fromdate!="") $sql.= " AND DATE_FORMAT(ae.endsAt, '%Y-%m-%d')>='".$filterParams->filter_fromdate."'";
        if($filterParams->filter_todate!="") $sql.= " AND DATE_FORMAT(ae.startsAt, '%Y-%m-%d')<='".$filterParams->filter_todate."'";
        if($filterParams->dep!="") $sql.= " AND (SELECT data.display_text FROM user_work JOIN data on user_work.department=data.id WHERE user_work.user_id = ul.user_id ORDER BY user_work.workdate_added DESC LIMIT 1) = '".$filterParams->dep."'";
        if($filterParams->pos!="") $sql.= " AND (SELECT data.display_text FROM user_work JOIN data on user_work.position=data.id WHERE user_work.user_id = ul.user_id ORDER BY user_work.workdate_added DESC LIMIT 1) = '".$filterParams->pos."'";
        if($filterParams->loc!="") $sql.= " AND (SELECT data.display_text FROM user_work JOIN data on user_work.site_location=data.id WHERE user_work.user_id = ul.user_id ORDER BY user_work.workdate_added DESC LIMIT 1) = '".$filterParams->loc."'";
        if($filterParams->emp!="") $sql.= " AND CONCAT(user.firstname,' ',user.lastname) = '".$filterParams->emp."'";
        $sql.="ORDER BY SiteLocation ASC
                ) tt
                GROUP BY tt.SiteLocation";
        $db->select(false, false, $sql);
        $tData1 = array();
        while($db->getRow()){
            array_push($tData1, $db->row);
        }

        $sql = "SELECT tt.SiteLocation, COUNT(tt.id) as attendee_count, SUM(tt.wasted_cost) AS wasted_cost FROM (
                    	SELECT ul.id, ul.alloc_event_id, ul.cost, 
                    		(SELECT site_location FROM user_work WHERE user_id = ul.user_id ORDER BY workdate_added DESC LIMIT 1) AS LOCID, 
                    		(SELECT display_text FROM data WHERE id = LOCID) AS SiteLocation, 
                    		TIMESTAMPDIFF(HOUR, ae.startsAt, ae.endsAt) AS course_hour, 
                    		(SELECT hourly_rate FROM user_work WHERE user_id = ul.user_id ORDER BY workdate_added DESC LIMIT 1) AS hourly_rate, 
                    		TIMESTAMPDIFF(HOUR, ae.startsAt, ae.endsAt)*(SELECT hourly_rate FROM user_work WHERE user_id = ul.user_id ORDER BY workdate_added DESC LIMIT 1) AS wasted_cost
                    	FROM user_license ul 
                    	JOIN data d ON ul.license_name=d.display_text
                    	JOIN user u ON ul.user_id=u.id
                    	JOIN alloc_event ae ON ul.alloc_event_id=ae.id
                    	WHERE d.type='event_license_name' ";
        if($showMode==0)$sql.=" AND u.active=1 ";
        $sql.=" AND u.deleted=0 AND ul.lqstatus=1 ";
        if($filterParams->filter_fromdate!="") $sql.= " AND DATE_FORMAT(ae.endsAt, '%Y-%m-%d')>='".$filterParams->filter_fromdate."'";
        if($filterParams->filter_todate!="") $sql.= " AND DATE_FORMAT(ae.startsAt, '%Y-%m-%d')<='".$filterParams->filter_todate."'";
        if($filterParams->dep!="") $sql.= " AND (SELECT data.display_text FROM user_work JOIN data on user_work.department=data.id WHERE user_work.user_id = ul.user_id ORDER BY user_work.workdate_added DESC LIMIT 1) = '".$filterParams->dep."'";
        if($filterParams->pos!="") $sql.= " AND (SELECT data.display_text FROM user_work JOIN data on user_work.position=data.id WHERE user_work.user_id = ul.user_id ORDER BY user_work.workdate_added DESC LIMIT 1) = '".$filterParams->pos."'";
        if($filterParams->loc!="") $sql.= " AND (SELECT data.display_text FROM user_work JOIN data on user_work.site_location=data.id WHERE user_work.user_id = ul.user_id ORDER BY user_work.workdate_added DESC LIMIT 1) = '".$filterParams->loc."'";
        if($filterParams->emp!="") $sql.= " AND CONCAT(user.firstname,' ',user.lastname) = '".$filterParams->emp."'";
        $sql.="ORDER BY SiteLocation ASC
                ) tt
                GROUP BY tt.SiteLocation";
        $db->select(false, false, $sql);
        $tData2 = array();
        while($db->getRow()){
            array_push($tData2, $db->row);
        }
        $reportData = array();
        foreach($locations as $loc){
            $reportData[$loc['location']] = array(
                'total_cost'=>0,
                'course_count'=>0,
                'total_hours'=>0,
                'attendee_count'=>0,
                'wasted_cost'=>0
            );
            foreach($tData1 as $tOne1){
                if($loc['location']==$tOne1['SiteLocation']){
                    $reportData[$loc['location']] = array(
                        'total_cost'=>$tOne1['total_cost'],
                        'course_count'=>$tOne1['course_count'],
                        'total_hours'=>$tOne1['total_hours'],
                        'attendee_count'=>0,
                        'wasted_cost'=>0
                    );
                    foreach($tData2 as $tOne2){
                        if($loc['location']==$tOne2['SiteLocation']){
                            $reportData[$loc['location']]['attendee_count'] = $tOne2['attendee_count'];
                            $reportData[$loc['location']]['wasted_cost'] = $tOne2['wasted_cost'];
                            break;
                        }
                    }
                    break;
                }
            }
        }
        echo json_encode(array('location_report' => $reportData));
    }
    public function getAnalyticsOfTrainingCourses($userData, $showMode, $courseStatus, $filterParams){
        $filterParams = json_decode($filterParams);
        if($filterParams->filter_fromdate!="") $filterParams->filter_fromdate = date('Y-m-d', strtotime($filterParams->filter_fromdate));
        if($filterParams->filter_todate!="") $filterParams->filter_todate = date('Y-m-d', strtotime($filterParams->filter_todate));
        $userWorkData = new db('user_work');
        $sql = "SELECT u.*, uw.* from user u join user_work uw on u.id=uw.user_id where u.id=".$userData->id." order by uw.workdate_added desc limit 1";
        $userWorkData->select(false, false, $sql, array());
        $userLocation = $userWorkData->getRow()['site_location'];
        /*$sql = "UPDATE alloc_course SET status=3 where status=0 AND completed_date='0000-00-00 00:00:00'";
        $alloc_course->select(false, false, $sql, array());*/

        $this->updateAllocCourseStatus();
        /*start updating(update alloc_course if user submit the answers but not affect alloc_course table)*/
        /*$db = new db('alloc_course');
        $sql = "SELECT AABCD.*, ac.* FROM (
                	SELECT sa.* FROM submitted_answers sa
                	JOIN (
                		SELECT AAB.employee_id, AAB.course_id, AAB.question_id, MAX(AAB.attempt) AS lattempt
                		FROM (
                			SELECT AA.* FROM (
                				SELECT sa.employee_id, sa.course_id, sa.question_id, sa.date_submitted, sa.attempt
                				FROM submitted_answers sa
                				JOIN (
                					SELECT employee_id, course_id, MAX(question_id) AS lquestion_id
                					FROM submitted_answers
                					GROUP BY employee_id, course_id
                				) A ON sa.employee_id=A.employee_id AND sa.question_id=A.lquestion_id
                			) AA
                			JOIN
                				(SELECT MAX(question_id) AS lquestion_id, course_id FROM questions GROUP BY course_id) B
                			ON AA.course_id=B.course_id
                			WHERE AA.question_id=B.lquestion_id) AAB
                		GROUP BY AAB.employee_id, AAB.course_id, AAB.question_id
                	) AABC ON sa.employee_id=AABC.employee_id AND sa.course_id=AABC.course_id AND sa.question_id=AABC.question_id AND sa.attempt=AABC.lattempt
                ) AABCD
                LEFT JOIN alloc_course ac ON ac.course_id=AABCD.course_id AND ac.employee_id=AABCD.employee_id
                WHERE ac.status!=1";
        $db->select(false, false, $sql);
        $result = array();
        while($db->getRow()){
            $completed_date = $db->row['date_submitted'];
            $attempts = $db->row['attempt'];
            $id = $db->row['id'];
            array_push($result, array('id'=>$id, 'completed_date'=>$completed_date, 'attempts'=>$attempts));
        }
        foreach($result as $one){
            $completed_date = $one['completed_date'];
            $attempts = $one['attempts'];
            $id = $one['id'];
            $sql = "UPDATE alloc_course SET completed_date='".$completed_date."', attempts=".$attempts.", status=1 WHERE id=".$id;
            //echo $sql.'\n';
            $db->select(false, false, $sql);
        }*/
        /* end of updating*/

        $alloc_course = new db('alloc_course');
        $data = array();
        if($showMode==0){
            if($courseStatus==1) //completed
                $subsql = "SELECT ac.id, ac.status, ac.completed_date, course.course_name, ac.employee_id AS UID,
                                (SELECT site_location FROM user_work WHERE user_id = UID ORDER BY workdate_added DESC LIMIT 1) AS LOCID, 
                                (SELECT display_text FROM data WHERE id = LOCID) AS SiteLocation,
                                CONCAT(user.firstname,' ',user.lastname) AS PersonName
                    FROM alloc_course AS ac 
                    JOIN course AS course ON ac.course_id = course.course_id
                    JOIN user AS user ON ac.employee_id = user.id 
                    WHERE ac.user_id = :u AND user.active=1 AND ac.status=1";
            else if($courseStatus==2) //overdue
                $subsql = "SELECT ac.id, ac.status, ac.completed_date, course.course_name, ac.employee_id AS UID,
                            (SELECT site_location FROM user_work WHERE user_id = UID ORDER BY workdate_added DESC LIMIT 1) AS LOCID, 
                            (SELECT display_text FROM data WHERE id = LOCID) AS SiteLocation,
                            CONCAT(user.firstname,' ',user.lastname) AS PersonName
                     FROM alloc_course AS ac 
                     JOIN course AS course ON ac.course_id = course.course_id
                     JOIN user AS user ON ac.employee_id = user.id 
                    WHERE ac.user_id = :u AND user.active=1 AND ac.status=2";
            else if($courseStatus==3) //incomplete
                $subsql = "SELECT ac.id, ac.status, ac.completed_date, course.course_name, ac.employee_id AS UID,
                            (SELECT site_location FROM user_work WHERE user_id = UID ORDER BY workdate_added DESC LIMIT 1) AS LOCID, 
                            (SELECT display_text FROM data WHERE id = LOCID) AS SiteLocation,
                            CONCAT(user.firstname,' ',user.lastname) AS PersonName
                     FROM alloc_course AS ac 
                     JOIN course AS course ON ac.course_id = course.course_id
                     JOIN user AS user ON ac.employee_id = user.id 
                    WHERE ac.user_id = :u AND user.active=1 AND ac.status=3";
            else if($courseStatus==0) //pending
                $subsql = "SELECT ac.id, ac.status, ac.completed_date, course.course_name, ac.employee_id AS UID,
                            (SELECT site_location FROM user_work WHERE user_id = UID ORDER BY workdate_added DESC LIMIT 1) AS LOCID, 
                            (SELECT display_text FROM data WHERE id = LOCID) AS SiteLocation,
                            CONCAT(user.firstname,' ',user.lastname) AS PersonName
                     FROM alloc_course AS ac 
                     JOIN course AS course ON ac.course_id = course.course_id
                     JOIN user AS user ON ac.employee_id = user.id 
                    WHERE ac.user_id = :u AND user.active=1 AND ac.status=0";
            if($filterParams->filter_fromdate!="") $subsql.= " AND ac.alloc_date>='".$filterParams->filter_fromdate."'";
            if($filterParams->filter_todate!="") $subsql.= " AND ac.alloc_date<='".$filterParams->filter_todate."'";
            if($filterParams->dep!="") $subsql.= " AND (SELECT data.display_text FROM user_work JOIN data on user_work.department=data.id WHERE user_work.user_id = ac.employee_id ORDER BY user_work.workdate_added DESC LIMIT 1) = '".$filterParams->dep."'";
            if($filterParams->pos!="") $subsql.= " AND (SELECT data.display_text FROM user_work JOIN data on user_work.position=data.id WHERE user_work.user_id = ac.employee_id ORDER BY user_work.workdate_added DESC LIMIT 1) = '".$filterParams->pos."'";
            if($filterParams->loc!="") $subsql.= " AND (SELECT data.display_text FROM user_work JOIN data on user_work.site_location=data.id WHERE user_work.user_id = ac.employee_id ORDER BY user_work.workdate_added DESC LIMIT 1) = '".$filterParams->loc."'";
            if($filterParams->emp!="") $subsql.= " AND CONCAT(user.firstname,' ',user.lastname) = '".$filterParams->emp."'";

            $sql = "SELECT tbl1.location, tbl2.total_count FROM 
                        (SELECT data.id, data.display_text AS location
                            FROM (SELECT site_location FROM (SELECT site_location FROM user_work INNER JOIN 
                                    (SELECT id FROM user WHERE deleted = 0 AND account_id = :u) t_employee ON user_work.user_id = t_employee.id ) t1
                                    GROUP BY site_location) t_sitelocation
                                    INNER JOIN data
                                    ON data.id = t_sitelocation.site_location
                                    WHERE data.account_id=:u) tbl1
                        LEFT JOIN (SELECT mttt.SiteLocation, COUNT(*) AS total_count FROM (SELECT mtt.SiteLocation, mtt.course_name FROM ("
    				.$subsql.
    				" ORDER BY ac.created_on DESC) mtt) mttt GROUP BY mttt.SiteLocation) tbl2 ON tbl1.location=tbl2.SiteLocation ORDER BY tbl1.location ASC";
             $alloc_course->select(false, false, $sql, array('u' => $userData->account_id));
        }
        else if($showMode==1){
            if($courseStatus==1) //completed
                $subsql = "SELECT ac.id, ac.status, ac.completed_date, course.course_name, ac.employee_id AS UID,
                                (SELECT site_location FROM user_work WHERE user_id = UID ORDER BY workdate_added DESC LIMIT 1) AS LOCID, 
                                (SELECT display_text FROM data WHERE id = LOCID) AS SiteLocation,
                                CONCAT(user.firstname,' ',user.lastname) AS PersonName
                    FROM alloc_course AS ac 
                    JOIN course AS course ON ac.course_id = course.course_id
                    JOIN user AS user ON ac.employee_id = user.id 
                    WHERE ac.user_id = :u AND ac.status=1";
            else if($courseStatus==2) //overdue
                $subsql = "SELECT ac.id, ac.status, ac.completed_date, course.course_name, ac.employee_id AS UID,
                            (SELECT site_location FROM user_work WHERE user_id = UID ORDER BY workdate_added DESC LIMIT 1) AS LOCID, 
                            (SELECT display_text FROM data WHERE id = LOCID) AS SiteLocation,
                            CONCAT(user.firstname,' ',user.lastname) AS PersonName
                     FROM alloc_course AS ac 
                     JOIN course AS course ON ac.course_id = course.course_id
                     JOIN user AS user ON ac.employee_id = user.id 
                    WHERE ac.user_id = :u AND ac.status=2";
            else if($courseStatus==3) //incomplete
                $subsql = "SELECT ac.id, ac.status, ac.completed_date, course.course_name, ac.employee_id AS UID,
                            (SELECT site_location FROM user_work WHERE user_id = UID ORDER BY workdate_added DESC LIMIT 1) AS LOCID, 
                            (SELECT display_text FROM data WHERE id = LOCID) AS SiteLocation,
                            CONCAT(user.firstname,' ',user.lastname) AS PersonName
                     FROM alloc_course AS ac 
                     JOIN course AS course ON ac.course_id = course.course_id
                     JOIN user AS user ON ac.employee_id = user.id 
                    WHERE ac.user_id = :u AND ac.status=3";
            else if($courseStatus==0) //pending
                $subsql = "SELECT ac.id, ac.status, ac.completed_date, course.course_name, ac.employee_id AS UID,
                            (SELECT site_location FROM user_work WHERE user_id = UID ORDER BY workdate_added DESC LIMIT 1) AS LOCID, 
                            (SELECT display_text FROM data WHERE id = LOCID) AS SiteLocation,
                            CONCAT(user.firstname,' ',user.lastname) AS PersonName
                     FROM alloc_course AS ac 
                     JOIN course AS course ON ac.course_id = course.course_id
                     JOIN user AS user ON ac.employee_id = user.id 
                    WHERE ac.user_id = :u AND ac.status=0";
            if($filterParams->filter_fromdate!="") $subsql.= " AND ac.alloc_date>='".$filterParams->filter_fromdate."'";
            if($filterParams->filter_todate!="") $subsql.= " AND ac.alloc_date<='".$filterParams->filter_todate."'";
            if($filterParams->dep!="") $subsql.= " AND (SELECT data.display_text FROM user_work JOIN data on user_work.department=data.id WHERE user_work.user_id = ac.employee_id ORDER BY user_work.workdate_added DESC LIMIT 1) = '".$filterParams->dep."'";
            if($filterParams->pos!="") $subsql.= " AND (SELECT data.display_text FROM user_work JOIN data on user_work.position=data.id WHERE user_work.user_id = ac.employee_id ORDER BY user_work.workdate_added DESC LIMIT 1) = '".$filterParams->pos."'";
            if($filterParams->loc!="") $subsql.= " AND (SELECT data.display_text FROM user_work JOIN data on user_work.site_location=data.id WHERE user_work.user_id = ac.employee_id ORDER BY user_work.workdate_added DESC LIMIT 1) = '".$filterParams->loc."'";
            if($filterParams->emp!="") $subsql.= " AND CONCAT(user.firstname,' ',user.lastname) = '".$filterParams->emp."'";

            $sql = "SELECT tbl1.location, tbl2.total_count FROM 
                        (SELECT data.id, data.display_text AS location
                            FROM (SELECT site_location FROM (SELECT site_location FROM user_work INNER JOIN 
                                    (SELECT id FROM user WHERE deleted = 0 AND account_id = :u) t_employee ON user_work.user_id = t_employee.id ) t1
                                    GROUP BY site_location) t_sitelocation
                                    INNER JOIN data
                                    ON data.id = t_sitelocation.site_location
                                    WHERE data.account_id=:u) tbl1
                        LEFT JOIN (SELECT mttt.SiteLocation, COUNT(*) AS total_count FROM (SELECT mtt.SiteLocation, mtt.course_name FROM ("
                        .$subsql.
				" ORDER BY ac.created_on DESC) mtt) mttt GROUP BY mttt.SiteLocation) tbl2 ON tbl1.location=tbl2.SiteLocation ORDER BY tbl1.location ASC";
            $alloc_course->select(false,false, $sql, array('u' => $userData->account_id));
        }
        while ($alloc_course->getRow()) {
            $data[$alloc_course->row['location']] = $alloc_course->row['total_count'];
            //array_push($data, $alloc_course->row);
        }
        echo json_encode(array('location_report' => $data));
    }
    public function getAnalyticsOfTrainingEmployees($userData, $showMode, $courseStatus, $filterParams){
        $filterParams = json_decode($filterParams);
        if($filterParams->filter_fromdate!="") $filterParams->filter_fromdate = date('Y-m-d', strtotime($filterParams->filter_fromdate));
        if($filterParams->filter_todate!="") $filterParams->filter_todate = date('Y-m-d', strtotime($filterParams->filter_todate));
        $userWorkData = new db('user_work');
        $sql = "SELECT u.*, uw.* from user u join user_work uw on u.id=uw.user_id where u.id=".$userData->id." order by uw.workdate_added desc limit 1";
        $userWorkData->select(false, false, $sql, array());
        $userLocation = $userWorkData->getRow()['site_location'];
        $alloc_course = new db('alloc_course');

        $this->updateAllocCourseStatus();

        $data = array();
        if($showMode==0){
            if($courseStatus == 1)
                $subsql = "SELECT ac.id, ac.status, ac.completed_date, course.course_name, ac.employee_id AS UID,
                            (SELECT site_location FROM user_work WHERE user_id = UID ORDER BY workdate_added DESC LIMIT 1) AS LOCID, 
                            (SELECT display_text FROM data WHERE id = LOCID) AS SiteLocation,
                            CONCAT(user.firstname,' ',user.lastname) AS PersonName
                     FROM alloc_course AS ac 
                     JOIN course AS course ON ac.course_id = course.course_id
                     JOIN user AS user ON ac.employee_id = user.id 
                    WHERE ac.user_id = :u AND user.active=1 AND ac.status=1";
            else if($courseStatus == 2)
                $subsql = "SELECT ac.id, ac.status, ac.completed_date, course.course_name, ac.employee_id AS UID,
                            (SELECT site_location FROM user_work WHERE user_id = UID ORDER BY workdate_added DESC LIMIT 1) AS LOCID, 
                            (SELECT display_text FROM data WHERE id = LOCID) AS SiteLocation,
                            CONCAT(user.firstname,' ',user.lastname) AS PersonName
                     FROM alloc_course AS ac 
                     JOIN course AS course ON ac.course_id = course.course_id
                     JOIN user AS user ON ac.employee_id = user.id 
                    WHERE ac.user_id = :u AND user.active=1 AND ac.status=2";
            else if($courseStatus == 0)
                $subsql = "SELECT ac.id, ac.status, ac.completed_date, course.course_name, ac.employee_id AS UID,
                            (SELECT site_location FROM user_work WHERE user_id = UID ORDER BY workdate_added DESC LIMIT 1) AS LOCID, 
                            (SELECT display_text FROM data WHERE id = LOCID) AS SiteLocation,
                            CONCAT(user.firstname,' ',user.lastname) AS PersonName
                     FROM alloc_course AS ac 
                     JOIN course AS course ON ac.course_id = course.course_id
                     JOIN user AS user ON ac.employee_id = user.id 
                    WHERE ac.user_id = :u AND user.active=1 AND ac.status=0";
            else if($courseStatus == 3)
                $subsql = "SELECT ac.id, ac.status, ac.completed_date, course.course_name, ac.employee_id AS UID,
                            (SELECT site_location FROM user_work WHERE user_id = UID ORDER BY workdate_added DESC LIMIT 1) AS LOCID, 
                            (SELECT display_text FROM data WHERE id = LOCID) AS SiteLocation,
                            CONCAT(user.firstname,' ',user.lastname) AS PersonName
                     FROM alloc_course AS ac 
                     JOIN course AS course ON ac.course_id = course.course_id
                     JOIN user AS user ON ac.employee_id = user.id 
                    WHERE ac.user_id = :u AND user.active=1 AND ac.status=3";
            if($filterParams->filter_fromdate!="") $subsql.= " AND ac.alloc_date>='".$filterParams->filter_fromdate."'";
            if($filterParams->filter_todate!="") $subsql.= " AND ac.alloc_date<='".$filterParams->filter_todate."'";
            if($filterParams->dep!="") $subsql.= " AND (SELECT data.display_text FROM user_work JOIN data on user_work.department=data.id WHERE user_work.user_id = ac.employee_id ORDER BY user_work.workdate_added DESC LIMIT 1) = '".$filterParams->dep."'";
            if($filterParams->pos!="") $subsql.= " AND (SELECT data.display_text FROM user_work JOIN data on user_work.position=data.id WHERE user_work.user_id = ac.employee_id ORDER BY user_work.workdate_added DESC LIMIT 1) = '".$filterParams->pos."'";
            if($filterParams->loc!="") $subsql.= " AND (SELECT data.display_text FROM user_work JOIN data on user_work.site_location=data.id WHERE user_work.user_id = ac.employee_id ORDER BY user_work.workdate_added DESC LIMIT 1) = '".$filterParams->loc."'";
            if($filterParams->emp!="") $subsql.= " AND CONCAT(user.firstname,' ',user.lastname) = '".$filterParams->emp."'";

            $sql = "SELECT tbl1.location, tbl2.total_count FROM 
                        (SELECT data.id, data.display_text AS location
                            FROM (SELECT site_location FROM (SELECT site_location FROM user_work INNER JOIN 
                                    (SELECT id FROM user WHERE deleted = 0 AND account_id = :u) t_employee ON user_work.user_id = t_employee.id ) t1
                                    GROUP BY site_location) t_sitelocation
                                    INNER JOIN data
                                    ON data.id = t_sitelocation.site_location
                                    WHERE data.account_id=:u) tbl1
                        LEFT JOIN (SELECT mttt.SiteLocation, COUNT(*) AS total_count FROM (SELECT DISTINCT mtt.SiteLocation, mtt.PersonName FROM ("
				.$subsql.
				" ORDER BY ac.created_on DESC) mtt) mttt GROUP BY mttt.SiteLocation) tbl2 ON tbl1.location=tbl2.SiteLocation ORDER BY tbl1.location ASC";
             $alloc_course->select(false, false, $sql, array('u' => $userData->account_id));
        }
        else if($showMode==1){
            if($courseStatus == 1)
                $subsql = "SELECT ac.id, ac.status, ac.completed_date, course.course_name, ac.employee_id AS UID,
                            (SELECT site_location FROM user_work WHERE user_id = UID ORDER BY workdate_added DESC LIMIT 1) AS LOCID, 
                            (SELECT display_text FROM data WHERE id = LOCID) AS SiteLocation,
                            CONCAT(user.firstname,' ',user.lastname) AS PersonName
                     FROM alloc_course AS ac 
                     JOIN course AS course ON ac.course_id = course.course_id
                     JOIN user AS user ON ac.employee_id = user.id 
                    WHERE ac.user_id = :u AND ac.status=1";
            else if($courseStatus == 2)
                $subsql = "SELECT ac.id, ac.status, ac.completed_date, course.course_name, ac.employee_id AS UID,
                            (SELECT site_location FROM user_work WHERE user_id = UID ORDER BY workdate_added DESC LIMIT 1) AS LOCID, 
                            (SELECT display_text FROM data WHERE id = LOCID) AS SiteLocation,
                            CONCAT(user.firstname,' ',user.lastname) AS PersonName
                     FROM alloc_course AS ac 
                     JOIN course AS course ON ac.course_id = course.course_id
                     JOIN user AS user ON ac.employee_id = user.id 
                    WHERE ac.user_id = :u AND ac.status=2";
            else if($courseStatus == 0)
                $subsql = "SELECT ac.id, ac.status, ac.completed_date, course.course_name, ac.employee_id AS UID,
                            (SELECT site_location FROM user_work WHERE user_id = UID ORDER BY workdate_added DESC LIMIT 1) AS LOCID, 
                            (SELECT display_text FROM data WHERE id = LOCID) AS SiteLocation,
                            CONCAT(user.firstname,' ',user.lastname) AS PersonName
                     FROM alloc_course AS ac 
                     JOIN course AS course ON ac.course_id = course.course_id
                     JOIN user AS user ON ac.employee_id = user.id 
                    WHERE ac.user_id = :u AND ac.status=0";
            else if($courseStatus == 3)
                $subsql = "SELECT ac.id, ac.status, ac.completed_date, course.course_name, ac.employee_id AS UID,
                            (SELECT site_location FROM user_work WHERE user_id = UID ORDER BY workdate_added DESC LIMIT 1) AS LOCID, 
                            (SELECT display_text FROM data WHERE id = LOCID) AS SiteLocation,
                            CONCAT(user.firstname,' ',user.lastname) AS PersonName
                     FROM alloc_course AS ac 
                     JOIN course AS course ON ac.course_id = course.course_id
                     JOIN user AS user ON ac.employee_id = user.id 
                    WHERE ac.user_id = :u AND ac.status=3";
            if($filterParams->filter_fromdate!="") $subsql.= " AND ac.alloc_date>='".$filterParams->filter_fromdate."'";
            if($filterParams->filter_todate!="") $subsql.= " AND ac.alloc_date<='".$filterParams->filter_todate."'";
            if($filterParams->dep!="") $subsql.= " AND (SELECT data.display_text FROM user_work JOIN data on user_work.department=data.id WHERE user_work.user_id = ac.employee_id ORDER BY user_work.workdate_added DESC LIMIT 1) = '".$filterParams->dep."'";
            if($filterParams->pos!="") $subsql.= " AND (SELECT data.display_text FROM user_work JOIN data on user_work.position=data.id WHERE user_work.user_id = ac.employee_id ORDER BY user_work.workdate_added DESC LIMIT 1) = '".$filterParams->pos."'";
            if($filterParams->loc!="") $subsql.= " AND (SELECT data.display_text FROM user_work JOIN data on user_work.site_location=data.id WHERE user_work.user_id = ac.employee_id ORDER BY user_work.workdate_added DESC LIMIT 1) = '".$filterParams->loc."'";
            if($filterParams->emp!="") $subsql.= " AND CONCAT(user.firstname,' ',user.lastname) = '".$filterParams->emp."'";

            $sql = "SELECT tbl1.location, tbl2.total_count FROM 
                        (SELECT data.id, data.display_text AS location
                            FROM (SELECT site_location FROM (SELECT site_location FROM user_work INNER JOIN 
                                    (SELECT id FROM user WHERE deleted = 0 AND account_id = :u) t_employee ON user_work.user_id = t_employee.id ) t1
                                    GROUP BY site_location) t_sitelocation
                                    INNER JOIN data
                                    ON data.id = t_sitelocation.site_location
                                    WHERE data.account_id=:u) tbl1
                        LEFT JOIN (SELECT mttt.SiteLocation, COUNT(*) AS total_count FROM (SELECT DISTINCT mtt.SiteLocation, mtt.PersonName FROM ("
				.$subsql.
				" ORDER BY ac.created_on DESC) mtt) mttt GROUP BY mttt.SiteLocation) tbl2 ON tbl1.location=tbl2.SiteLocation ORDER BY tbl1.location ASC";
            $alloc_course->select(false,false, $sql, array('u' => $userData->account_id, 'st'=>$courseStatus));
        }
        while ($alloc_course->getRow()) {
            $data[$alloc_course->row['location']] = $alloc_course->row['total_count'];
            //array_push($data, $alloc_course->row);
        }
        echo json_encode(array('location_report' => $data));
    }
    public function getAllocCourseByID($params) {
        $alloc_course_id = $params->alloc_course_id;
        $alloc_course_db = new db('alloc_course');
        $course_db = new db('course');
        $user_db = new db('user');
        $sql = "SELECT * FROM alloc_course WHERE id = :id";
        $alloc_course_db->select(false,false, $sql, array('id' => $alloc_course_id));
        $alloc_course_db->getRow();
        $alloc_course = $alloc_course_db->row;
        // Get Course.
        $course_id = $alloc_course["course_id"];
        $sql = "SELECT * FROM course WHERE course_id = :id AND status=1";
        $course_db->select(false,false, $sql, array('id' => $course_id));
        $course_db->getRow();
        $course = $course_db->row;
        $message = "";
        if(!$course){
            $message = "Please note, this course has been deactivated in the course creation page and cannot currently be allocated to users. If you are adding time to this course, for this user, the user will still be able to do this course but note it is no longer active.";
        }
        $alloc_course["course"] = $course;
        // Get Supervisor.
        $course_supervisor = $alloc_course["course_supervisor"];
        $sql = "SELECT * FROM user WHERE id = :id";
        $user_db->select(false,false, $sql, array('id' => $course_supervisor));
        $user_db->getRow();
        $supervisor_user = $user_db->row;
        $alloc_course["supervisor_user"] = $supervisor_user;
        // Get Employee.
        $employee_id = $alloc_course["employee_id"];
        $sql = "SELECT * FROM user WHERE id = :id";
        $user_db->select(false,false, $sql, array('id' => $employee_id));
        $user_db->getRow();
        $employee_user = $user_db->row;
        $alloc_course["employee_user"] = $employee_user;
        return json_encode(array('message'=>$message, 'alloc_course' => $alloc_course, 'alloc_course_id' => $alloc_course_id, 'sql' => $sql));
    }
    public function delAllocCourse($post) {
        $alloc_course = new db('alloc_course');
        $alloc_course_id = $post->alloc_course_id;
        $user_id = $post->user_id;
        // Get all questions.
        $alloc_course->delete('id = :id', false, array('id' => $alloc_course_id));
        return ($this->getAllocCourseData($user_id));
    }
    function get_data($url) {
        $ch = curl_init();
        $timeout = 5;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

    // updated by Alex-cobra -20-04-13
    //newly inserted for system logs
    // Update the course status if the course is finished
    public function updateCourseStatus($params) {
        $ac = new db('alloc_course');
        $ac->select('employee_id = :eid AND course_id = :cid', false, false, array('eid' => $params->employee_id, 'cid' => $params->course_id));
        $ac->getRow();
        $attempt = $ac->attempts+1;
        $q = new db('questions');
        $q->select('course_id = :cid AND deleted != :del',false, false, array('cid' => $params->course_id, 'del'=>1));
        $a = new db('submitted_answers');
        $a->select('employee_id = :eid AND course_id = :cid AND attempt = :a', false, false, array('eid' => $params->employee_id, 'cid' => $params->course_id, 'a' => $attempt));
        // All questions answered
        if ($q->numRows == $a->numRows) {
            $c = new db('submitted_answers');
            $c->select('employee_id = :eid AND course_id = :cid AND is_correct = :c AND attempt = :a', false, false, array('eid' => $params->employee_id, 'cid' => $params->course_id, 'c' => 1, 'a' => $attempt));
            // If number correct equals total number of questions - passed course
            if ($c->numRows == $a->numRows) {
                $ac = new db('alloc_course');
                //update by Alex-cobra -20-05-16
                //for failed
                if (floatval($params->percentageScore) < 70 ) {
                    $flds = array('status' => 5, 'completed_date' => null );
                    $data = array('status' => 5, 'completed_date' => date('Y-m-d H:i:s'), 'cid' => $params->course_id, 'eid' => $params->employee_id);
                }
                else {
                    $flds = array('status' => 1, 'completed_date' => date('Y-m-d H:i:s'));
                    $data = array('status' => 1, 'completed_date' => date('Y-m-d H:i:s'), 'cid' => $params->course_id, 'eid' => $params->employee_id);
                }
                //update by Alex-cobra -20-05-16
                $ac->update($flds,'course_id = :cid AND employee_id = :eid', 1, $data);
                // add system log
                $alloc_course_id = $params->course_id;
                $account_id = '';
                $log_type = 'completed_course';
                $employee_id = $params->employee_id;
                $this->addCourseLog($account_id, $alloc_course_id, $log_type, $employee_id);
            } else {
                //
            }
            // Update the attempts
            $ac = new db('alloc_course');
            $ac->update(array('attempts' => 1), 'course_id = :cid AND employee_id = :eid', 1, array('attempts' => $attempt, 'cid' => $params->course_id, 'eid' => $params->employee_id));
        }
    }
    // // // // //

    // updated by Alex-cobra -20-04-03
    //newly inserted for system logs
    // Update the course status if the course is finished
    public function updateCourseStatus1($params) {
        $ac = new db('alloc_course');
        $ac->select('employee_id = :eid AND course_id = :cid', false, false, array('eid' => $params->employee_id, 'cid' => $params->course_id));
        $ac->getRow();
        $attempt = $ac->attempts + 1;
        $q = new db('questions');
        $q->select('course_id = :cid AND deleted != :del',false, false, array('cid' => $params->course_id, 'del'=>1));
        $a = new db('submitted_answers1');
        $a->select('employee_id = :eid AND course_id = :cid AND attempt = :a', false, false, array('eid' => $params->employee_id, 'cid' => $params->course_id, 'a' => $attempt));
        // All questions answered
        if ($q->numRows == $a->numRows) {

            $c = new db('submitted_answers1');
            $c->select('employee_id = :eid AND course_id = :cid AND attempt = :a', false, false, array('eid' => $params->employee_id, 'cid' => $params->course_id, 'a' => $attempt));
            // If number correct equals total number of questions - passed course
            if ($c->numRows == $a->numRows) {
                $ac = new db('alloc_course');

                // setting --- 'status' => 4 : Submitted
                //update by Alex-cobra -20-05-16
                //for failed
                if ($params->percentageScore < 70 ) {
                    $flds = array('status' => 5, 'completed_date' => null );
                    $data = array('status' => 5, 'completed_date' => date('Y-m-d H:i:s'), 'cid' => $params->course_id, 'eid' => $params->employee_id);
                }
                else {
                    $flds = array('status' => 1, 'completed_date' => date('Y-m-d H:i:s'));
                    $data = array('status' => 1, 'completed_date' => date('Y-m-d H:i:s'), 'cid' => $params->course_id, 'eid' => $params->employee_id);
                }
                //update by Alex-cobra -20-05-16

                $ac->update($flds,'course_id = :cid AND employee_id = :eid', 1, $data);

                // add system log
                $alloc_course_id = $params->course_id;
                $account_id = '';
                $log_type = 'submitted_course';
                $employee_id = $params->employee_id;
                $this->addCourseLog($account_id, $alloc_course_id, $log_type, $employee_id);
            } else {
                //
            }
            // Update the attempts
            $ac = new db('alloc_course');
            $ac->update(array('attempts' => 1), 'course_id = :cid AND employee_id = :eid', 1, array('attempts' => $attempt, 'cid' => $params->course_id, 'eid' => $params->employee_id));
        }
    }
    // // // // //

    public function saveCourseAnswer($params) {
        $ac = new db('alloc_course');
        $ac->select('course_id = :cid AND employee_id = :eid', false, false, array('cid' => $params->course_id, 'eid' => $params->employee_id));
        $ac->getRow();
        $attempt = $ac->attempts + 1;
        $q = new db('questions');
        $q->select('question_id = :qid', false, false, array('qid' => $params->question_id));
        $q->getRow();
        $data = array();
        $data['employee_id'] = $params->employee_id;
        $data['course_id'] = $params->course_id;
        $data['question_id'] = $params->question_id;
        $data['answer_id'] = isset($params->answer_id)?$params->answer_id:0;
        $data['attempt'] = $attempt;
        $data['is_correct'] = ($q->correct_answer_id == $data['answer_id']) ? 1 : 0;
        $data['ip_address'] = $params->ip_address;
        $data['date_submitted'] = date('Y-m-d H:i:s');
        $db = new db('submitted_answers');
        $sql = "SELECT * FROM submitted_answers WHERE course_id=".$params->course_id." AND employee_id=".$params->employee_id." AND question_id=".$params->question_id." AND attempt=".$attempt;
        $db->select(false, false, $sql);
        $db->getRow();
        if($db->row){
            $row = $db->row;
            $sql = sprintf("UPDATE submitted_answers SET answer_id=%d, is_correct=%d, ip_address='%s', date_submitted='%s' WHERE id=%d", 
                            $data['answer_id'], $data['is_correct'], $data['ip_address'], $data['date_submitted'], $row['id']);
                            
            $db->select(false, false, $sql);
        }else{
            $db->insert($data);
        }
        $result = array();
        $result['numCorrect'] = 0;
        $result['totalQuestions'] = 0;
        $sql = "SELECT COUNT(*) as total_count FROM questions WHERE course_id=".$params->course_id;
        $q->SELECT(false, false, $sql);
        $q->getRow();
        $result['totalQuestions'] = $q->row['total_count'];
        $db->select('employee_id = :eid AND course_id = :cid AND attempt = :a', false, false, array('eid' => $params->employee_id, 'cid' => $params->course_id, 'a' => $attempt));
        while ($db->getRow()) {
            if ($db->is_correct == 1) {
                $result['numCorrect']++;
            }
        }
        if ($result['numCorrect'] == 0) {
            $result['percentageScore'] = 0;
            $params->percentageScore = 0;

        } else {
            $result['percentageScore'] = number_format((($result['numCorrect'] / $result['totalQuestions']) * 100),2);
            $params->percentageScore = number_format((($result['numCorrect'] / $result['totalQuestions']) * 100),2);
        }
        $this->updateCourseStatus($params);
        return json_encode($result);
    }

    // <updated by Alex-cobra> -20-04-03
    // save answer in questions and answers
    public function saveCourseAnswer1($params) {
        $result = array();
        $ac = new db('alloc_course');
        $ac->select('course_id = :cid AND employee_id = :eid', false, false, array('cid' => $params->course_id, 'eid' => $params->employee_id));
        $ac->getRow();
        $attempt = $ac->attempts + 1;
        $q = new db('questions');
        $q->select('question_id = :qid', false, false, array('qid' => $params->question_id));
        $q->getRow();
        $data = array();
        $data['employee_id'] = $params->employee_id;
        $data['course_id'] = $params->course_id;
        $data['question_id'] = $params->question_id;
        $data['answer'] = $params->answer;
        $data['attempt'] = $attempt;
        $db = new db('submitted_answers1');
        $db->select('course_id = :cid AND employee_id = :eid AND question_id = :qid', false, false, array('cid'=>$params->course_id, 'eid'=>$params->employee_id, 'qid'=>$params->question_id));
        if($db->getRow()) {
            $is_exist = 1;
        }
        else {
            $is_exist = 0;
        }

        if($is_exist == 1) {
            $db->delete('course_id = :cid AND employee_id = :eid AND question_id = :qid', false, array('cid'=>$params->course_id, 'eid'=>$params->employee_id, 'qid'=>$params->question_id));
        }
        $db->insert($data);

        $result['numCorrect'] = 0;
        $result['totalQuestions'] = 0;
        $db = new db('submitted_answers1');

        $db->select('employee_id = :eid AND course_id = :cid AND attempt = :a', false, false, array('eid' => $params->employee_id, 'cid' => $params->course_id, 'a' => $attempt));
        while ($db->getRow()) {
            $result['totalQuestions']++;
            $result['numCorrect']++;
        }
        if ($result['numCorrect'] == 0) {
            $result['percentageScore'] = '0%';
            $params->percentageScore = 0;
        } else {
            $result['percentageScore'] = number_format((($result['numCorrect'] / $result['totalQuestions']) * 100),2).'%';
            $params->percentageScore = number_format((($result['numCorrect'] / $result['totalQuestions']) * 100),2);
        }
        $this->updateCourseStatus1($params);
        return json_encode($result);
    }

    // save review in questions and answers
    public function saveCourseReview($params) {
        $course_id = $params->course_id;
        $employee_id = $params->employee_id;
        $question_id = $params->question_id;
        $result = array();
        $db = new db('submitted_answers1');
        $db->update(array('is_correct' => 0), 'course_id = :cid AND employee_id = :eid AND question_id = :qid', false, array('is_correct'=>$params->review_id,'cid'=>$course_id, 'eid'=>$employee_id, 'qid'=>$question_id));

        // update status of course
        $q = new db('questions');
        $q->select('course_id = :cid', false, false, array('cid'=>$course_id));
        $a = new db('submitted_answers1');
        $a->select('employee_id = :eid AND course_id = :cid AND is_correct != :ic', false, false, array('eid'=>$employee_id, 'cid'=>$course_id, 'ic'=>0));
        if($q->numRows == $a->numRows) {
            $ac = new db('alloc_course');
            $ac->update(array('status'=>0), 'employee_id = :eid AND course_id = :cid', false, array('status'=>1, 'eid'=>$employee_id, 'cid'=>$course_id));
        }

        $result['success'] = 'ok';
        return json_encode($result);
    }

    public function getCourseByAllocId($params) {
        $this->updateCourseInactive();
        $result = array();
        $db = new db('alloc_course');
        $db->select('id = :cid', false, false, array('cid'=>$params->alloc_id));
        $db->getRow();
        $course_id = $db->course_id;
        $db = new db('course');
        $db->select('course_id = :cid', false, false, array('cid'=>$course_id));
        $db->getRow();
        $result['course_type'] = $db->course_type;
        return json_encode($result);
    }
    // // // // //

    public function removeFile($params) {
        $path = getcwd().'/assets/uploads/';
        $q = new db('questions');
        $q->select('question_id = :qid', false, false, array('qid' => $params->questionId));
        $q->getRow();
        if ($q->video) {
            if (file_exists($path.$q->video) === true) {
                unlink($path.$q->video);
            }
        }
        if ($q->ppt) {
            if (file_exists($path.$q->ppt) === true) {
                unlink($path.$q->ppt);
            }
        }
        if ($q->image) {
            if (file_exists($path.$q->image) === true) {
                unlink($path.$q->image);
            }
        }
        if ($q->pdf) {
            if (file_exists($path.$q->pdf) === true) {
                unlink($path.$q->pdf);
            }
        }
        $flds = array('media_type' => 0, 'video' => '', 'ppt' => '', 'image' => '', 'pdf' => '');
        $params = array('media_type' => 0, 'video' => '', 'ppt' => '', 'image' => '', 'pdf' => '', 'qid' => $params->questionId);
        $q->update($flds,'question_id = :qid', 1, $params);
    }

    function sendEmailForAllocCourse($course_id, $employee_id, $user_id, $expire_hours, $alloc_date) {
        $course_db = new db('course');
        $user_db = new db('user');
        // Get Course.
        $sql = "SELECT * FROM course WHERE course_id = :id";
        $course_db->select(false,false, $sql, array('id' => $course_id));
        $course_db->getRow();
        $course = $course_db->row;
        // Get Employee.
        $sql = "SELECT * FROM user WHERE id = :id";
        $user_db->select(false,false, $sql, array('id' => $employee_id));
        $user_db->getRow();
        $employee = $user_db->row;
        // Get Current User Id.
        $sql = "SELECT * FROM user WHERE id = :id";
        $user_db->select(false,false, $sql, array('id' => $user_id));
        $user_db->getRow();
        $user = $user_db->row;
        $employee_name = $employee["firstname"] . " " . $employee["lastname"];
        $employee_username = $employee["username"];
        //$employee_password = $employee["public_password"];
        $employee_email = $employee["email"];
        $company_name = $user["companyname"];
        $company_email = $user["email"];
        $date = strtotime($alloc_date);
        $active_date = date("d/m/Y", $date);
        $course_name = $course["course_name"];
        $login_link = "https://hrmaster.com.au/#/login";
        $forgot_link = "https://hrmaster.com.au/#/forgotpassword";
        $c = new db('alloc_date');
        $sql = "SELECT DATE_FORMAT(ADDDATE(`alloc_date`, INTERVAL `expire_hours` HOUR), '%e/%m/%Y') as expire_date FROM alloc_course WHERE course_id = :course AND employee_id = :eid";
        $c->select(false,false, $sql, array('course' => $course_id, 'eid' => $employee_id));
        $row = $c->getRow();
        $expire_date = $row['expire_date'];
        $limit_days = "5 days";
        /*$expire = date_create($active_date);
        date_add($expire, date_interval_create_from_date_string($expire_hours . "hours"));
        $expireAfter = strtotime(date_format($expire, 'Y-m-d H:i:s'));
        $expire_date = date("d/m/Y",$expireAfter);   */
        $content = "<p>Dear ". $employee_name ."</p>";
        $content .= "<p>". $company_name ." has set a training account for you on " .$active_date. " for the training module of " . $course_name. ". You will be required to complete all questions correctly to pass this module. Your result will be sent directly to " .$company_name. " for quality assurance and training purposes.</p>";
        $content .= "<p>You will need to click and visit the following link <a href='" . $login_link . "'> " . $login_link. "</a> and enter your username($employee_username) and password. If you have not yet set a password, or do not remember your existing password, please click <a href='".$forgot_link."'>here</a> to be directed to the forgot password page were you can set one up.</p>";
        $content .= "<p>If you are a new user, you will have until $limit_days to create a password, otherwise you will need to visit the forgot password link on the login page to recreate one.</p>";
        $content .= "<p>This course will expire on the " .$expire_date. ". Please ensure this course is finished by that time and you are encouraged to speak to your supervisor if you are unsure of any of the details.</p>";
        $content .= "<p>Best of luck</p>";
        //$file_content = file_get_contents('http://hrmaster.com.au/assets/php/email_templates/alloc_course_email_template.html');
        $file_content = $this->email_template;
        $file_content = str_replace("<div id='message-content'></div>", $content, $file_content);
        $subject = 'HR Master Training Details';
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "From: " . $company_name . " <" . $company_email . ">\r\n";
        $headers .= "Reply-To: " . $company_email . "\r\n";
        $headers .= "Return-Path: ". $company_email ."\r\n";
        $headers .= "X-Priority: 3\r\n";
        $headers .= "X-Mailer: PHP". phpversion() ."\r\n";
        $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
        mail($employee_email, $subject, $file_content, $headers);
        return $file_content;
    }

    public function getHSData($post) {
        $managers = $this->getUsersByType($post->user->account_id, 18, false);
        $db = new db('hazardous_substance');
        $sql = "SELECT *, site_location_id as SLI, DATE_FORMAT(expiry_date, '%e/%m/%Y') AS Expiration, 
                    (SELECT display_text FROM data WHERE id = SLI ) AS 'site_location'
                  FROM hazardous_substance hs
                 WHERE account_id = :aid
                   AND deleted = :del
                 ORDER BY date_created ASC";
        $db->select(false, false, $sql, array('aid' => $post->user->account_id, 'del' => 0));
        $hs = array();
        while($db->getRow()) {
            $db->row['SDS_Available'] = ($db->row['has_sds'] == 1) ? "Yes" : "No";
            array_push($hs, $db->row);
        }
        echo json_encode(array('records' => $hs, 'managers' => $managers, 'locations' => $this->getData('sitelocation',$post->user->account_id), 'suppliers' => $this->getData('supplier',$post->user->account_id)));
    }

    public function saveHS($post) {
        $db = new db('hazardous_substance');
        $data = (array)$post->hs;
        $data['expiry_date'] = date('Y-m-d', strtotime($data['expiry_date']));
        if ($data['id'] > 0) {
            $data['date_updated'] = date('Y-m-d H:i:s');
        }
        $db->insertupdate($data);
        $this->getHSData($post);
    }

    public function saveSiteData($params) {
        //var_dump($params);exit;
        $db = new db('data');
        $data = (array)$params->sitedata;
        $data['account_id'] = (isset($params->user->account_id)) ? $params->user->account_id : 0;
        if ($data['id'] == 0) {
            unset($data['id']);
            $db->insert($data);
        } else {
            $params = $data;
            unset($data['id']);
            $db->update($data, 'id = :id', 1, $params);
        }
        $this->getSiteData($params);
    }

    public function getInfoTexts($params) {
        $db = new db('info_texts');
        $sql = "SELECT * FROM info_texts WHERE account_id=".$params->account_id." ORDER BY type ASC";
        $db->select(false, false, $sql);
        $sdata = array();
        $types = array();
        while($db->getRow()) {
            array_push($sdata, $db->row);
            if(!in_array($db->row['type'], $types))
                array_push($types, $db->row['type']);
        }
        echo json_encode(array('info_texts' => $sdata, 'datatype' => $types));
    }
    public function getInfoTextById($params){
        $db = new db('info_texts');
        $sql = "SELECT * FROM info_texts WHERE id=".$params->id." AND account_id=0";
        $db->select(false, false, $sql);
        $db->getRow();
        $row = $db->row;
        return json_encode($row);
    }
    public function getInfoTextByType($params){
        $db = new db('info_texts');
        $sql = "SELECT * FROM info_texts WHERE type='".$params->type."' AND account_id=0";
        $db->select(false, false, $sql);
        $result = array();
        while($db->getRow()){
            $result[$db->row['value']]=$db->row['text'];
        }
        
        return json_encode(array('result'=>$result));
    }

    public function saveInfoText($params){
        $db = new db("info_texts");
        if($params->it->id==0){
            $sql = sprintf("SELECT * FROM info_texts WHERE type='%s' AND value='%s'", $params->it->type, $params->it->value);
            $db->select(false, false, $sql);
            $db->getRow();
            if($db->row){
                return json_encode(array('message'=>'This info item already exists.'));
            }
            $sql = sprintf("INSERT INTO info_texts (account_id, type, value, text) VALUES ('%d', '%s', '%s', '%s')", $params->userData->account_id, $params->it->type, $params->it->value, $params->it->text);
        }
        else $sql = sprintf("UPDATE info_texts SET account_id='%d', type='%s', value='%s', text='%s' WHERE id=%d", $params->userData->account_id, $params->it->type, $params->it->value, $params->it->text, $params->it->id);

        $db->select(false, false, $sql);
    }
    public function deleteInfoText($params){

        $db = new db("info_texts");
        $sql = "DELETE FROM info_texts WHERE id=".$params->id;
        $db->select(false, false, $sql);
    }
    public function getSiteData($params) {
        $toCheck = array('position','level','department','entitle', 'testresult','emptype','sitelocation','testfrequency','supplier');
        $sdata = array();
        $types = array();
        $db = new db('data');
        $db->select('type <> :type AND account_id = :aid', 'type ASC', false, array('type' => 'country', 'aid' => $params->account_id));
        while($db->getRow()) {
            array_push($sdata, $db->row);
            if (!in_array($db->type, $types)) {
                array_push($types, $db->type);
            }
        }
        foreach($toCheck as $key => $val) {
            if (!in_array($val, $types)) {
                array_push($types, $val);
            }
        }
        echo json_encode(array('sitedata' => $sdata, 'datatype' => $types));
    }
    public function getInjuryList($uid) {
        $data = array();
        $db = new db('injury_register');
        $sql = "SELECT *, employee_id AS UID, DATE_FORMAT(incident_date, '%M-%y') as 'IncidentDateMth', 
                    (SELECT department FROM user_work WHERE user_id = UID ORDER BY workdate_added DESC LIMIT 1) AS department_id,
                    (SELECT position FROM user_work WHERE user_id = UID ORDER BY workdate_added DESC LIMIT 1) AS position_id,
                    (SELECT site_location FROM user_work WHERE user_id = UID ORDER BY workdate_added DESC LIMIT 1) AS SLOC,
                    (SELECT display_text FROM data WHERE id = SLOC) AS LocationName,
                    (SELECT display_text FROM data WHERE id = mechanismofinjury_id) AS Mechanism,
                    (SELECT display_text FROM data WHERE id = natureofinjury_id) AS Nature,
                    (SELECT CONCAT(firstname,' ',lastname) FROM user WHERE id = UID) AS injuredName
                  FROM injury_register
                 WHERE account_id = :aid
                   AND deleted = :notdel
                 ORDER BY incident_date ASC";
        $db->select(false, false, $sql, array('aid' => $uid,'notdel' => 0));
        while($db->getRow()) {
            array_push($data, $db->row);
        }
        return $data;
    }

    public function getWHSReportData($params) {
        $data['departments'] = $this->initialDataDprt($params->userData->account_id, true);
        $data['positions'] = $this->initialDataPosition($params->userData->account_id, true);
        $data['locations'] = $this->initialDataLctn($params->userData->account_id, true);
        $data['yearlist'] = $this->initialDataYr($params->userData->account_id, true);
        $data['employees'] = $this->initialDataEply($params->userData->account_id, true);
        $data['usercount'] = $this->initialDataUserCount($params->userData->account_id, true);
        $data['injuries'] = $this->getInjuryList($params->userData->account_id);
        $data['mechanism'] = $this->getData('injurymechanism', 0, array('id' => 0, 'display_text' => 'All'));
        $data['injurynature'] = $this->getData('injurynature', 0, array('id' => 0, 'display_text' => 'All'));
        return json_encode($data);
    }

    // updated by Alex-cobra -20-04-11  --- system logs add
    public function addCourseLog($account_id, $alloc_course_id, $log_type, $employee_id='', $started_course=0) {
        if($account_id == '') {
            $ue_db = new db('user');
            $sql = 'SELECT account_id from user WHERE id ='.$employee_id;
            $ue_db->select(false, false, $sql);
            $ue_db->getRow();
            $account_id = $ue_db->row['account_id'];
        }

        if($log_type == 'del_course' || $log_type == 'start_course' || $log_type == 'submitted_course' || $log_type == 'completed_course') {
            $course_id = $alloc_course_id;
        }
        else {
            $ac_db = new db('alloc_course');
            $ac_db->select('id = :acid', false, false, array('acid'=>$alloc_course_id));
            $ac_db->getRow();
            $course_id = $ac_db->row['course_id'];
        }

        $data = array();
        $u_db = new db('user');
        $sql = 'SELECT CONCAT(firstname," ",lastname) as userName, account_id from user WHERE account_id ='.$account_id;
        $u_db->select(false, false, $sql);
        $u_db->getRow();
        $data['who'] = $u_db->row['userName'];

        if ($employee_id != '') {
            $ue_db = new db('user');
            $sql = 'SELECT CONCAT(firstname," ",lastname) as userName, account_id from user WHERE id ='.$employee_id;
            $ue_db->select(false, false, $sql);
            $ue_db->getRow();
            $employee_name = $ue_db->row['userName'];
        }

        $c_db = new db('course');
        $c_db->select('course_id = :cid', false, false, array('cid'=>$course_id));
        $c_db->getRow();
        $courseName = $c_db->row['course_name'];

        if($log_type == 'alloc_course') {
            $data['what'] = 'Allocated "'.$courseName.'" course to '. $employee_name .' employee.';
        }
        elseif ($log_type == 'del_course') {
            $data['what'] = 'Removed "'.$courseName.'" course.';
        }
        elseif ($log_type == 'start_course' && $started_course == 0) {
            $data['what'] = 'Continued "'.$courseName.'" course.';
        }
        elseif ($log_type == 'start_course' && $started_course > 0) {
            $data['what'] = 'Commenced "'.$courseName.'" course.';
        }
        elseif ($log_type == 'submitted_course') {
            $data['what'] = 'Submitted answers of "'.$courseName.'" course.';
        }
        elseif ($log_type == 'completed_course') {
            $data['what'] = 'Completed answers of "'.$courseName.'" course.';
        }

        $data['account_id'] = $u_db->row['account_id'];
        $db = new db('system_logs');
        $db->insert($data);
    }
    // // // // //
}
?>
