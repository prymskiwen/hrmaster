<?php

date_default_timezone_set('Australia/Sydney');

if (!class_exists('db')) {

    require('db.class.php');

}

if (!class_exists('transaction')) {

    require('transaction.class.php');

}

if(!ini_get('date.timezone')) {

    date_default_timezone_set('GMT');

}

function getEmployeeName($emp_id){
    $e = new db("user");
    $e->select("id = :id", false, false, array("id" => $emp_id));
    $e->getRow();
    return array("firstname" => $e->row["firstname"], "lastname" => $e->row["lastname"]);
}


class injuryregister {



    function __construct() {



    }



    public function getData($type, $account=0) {

        $data   = array();

        $d      = new db('data');

        $d->select("type = :type AND account_id = :account", 'display_text ASC', false, array('type' => $type, 'account' => $account));

        while ($d->getRow()) {

            array_push($data, $d->row);

        }

        return $data;

    }



    public function getEmployees($account_id) {

        $data = array();

        $db = new db('user');

        $db->select('account_id = :aid AND deleted = :notdel', false, false, array('aid' => $account_id, 'notdel' => 0));

        while ($db->getRow()) {

            array_push($data, $db->row);

        }

        return $data;

    }





    public function delete($table, $id) {

        $db = new db($table);

        $db->update(array('deleted' => 1), 'id = :id', 1, array('deleted' => 1, 'id' => $id));

    }



    public function getInjury($id) {

        $data = array();

        $db = new db('injury_register');

        $db->select('id = :aid', false, false, array('aid' => $id));

        $db->getRow();
        if(!$db->row['lost_time']) $db->row['lost_time'] =  '00:00';
        if(!$db->row['workcover_date']) $db->row['workcover_date'] =  '0000-00-00';
        if(!$db->row['closed_date']) $db->row['closed_date'] =  '0000-00-00';
        if(!$db->row['safeworknotified_date']) $db->row['safeworknotified_date'] =  '0000-00-00';

        return json_encode($db->row);

    }



    public function getInjuryList($account) {

        $data = array();

        $db = new db('injury_register');

        $sql = "SELECT *, DATE_FORMAT(incident_date,'%d-%m-%Y') AS dateOfIncident, investigated_by AS investigatedBy, remedial_priority AS remedialPriority,

                            natureofinjury_id AS NID, site_location_id AS SID,

                            (SELECT display_text FROM data WHERE id = SID) AS siteLocation, 

                            (SELECT display_text FROM data WHERE id = NID) AS natureOfInjury,

                        employee_id AS EID, (SELECT CONCAT(firstname,' ',lastname) FROM user WHERE id = EID) AS injuredName

                 FROM injury_register 

                WHERE account_id = :aid 

                AND deleted = :del

                AND is_complete = :complete";

        $db->select(false, false, $sql, array('aid' => $account, 'del' => 0, 'complete' => 0));

        while ($db->getRow()) {
            if(!$db->row['lost_time']) $db->row['lost_time'] =  '00:00';
//            if(!$db->row['workcover_date']) $db->row['workcover_date'] =  date('Y-m-d');
//            if(!$db->row['closed_date']) $db->row['closed_date'] =  date('Y-m-d');
            $status = 'Active';
//            $workcover_date = date('Y-m-d', strtotime($db->row['workcover_date']));
//            $closed_date = date('Y-m-d', strtotime($db->row['closed_date']));
            $workcover_date = $db->row['workcover_date'];
            $closed_date = $db->row['closed_date'];
            $today = date('Y-m-d');
            if($closed_date != '' && $closed_date <= $today) $status = 'Closed';
            else{
                if($workcover_date != '' && $workcover_date < $today) $status = "Overdue";
            }
            $db->row['status'] = $status;
            array_push($data, $db->row);

        }

        return $data;

    }



    public function getInjuryData($post) {
        $data = $this->getInjuryList($post->userData->account_id);

        $employees = array();

        $db = new db('user');

        $db->select('account_id = :aid AND deleted = :notdel AND active = :isactive', false, false, array('aid' => $post->userData->account_id, 'notdel' => 0, 'isactive'=>'1'));

        while ($db->getRow()) {

            array_push($employees, $db->row);

        }

        $locations = $this->getData('injurylocation');

        $nature = $this->getData('injurynature');

        $bodypart = $this->getData('injuredbodypart');

        $mechanism = $this->getData('injurymechanism');

        $sites = $this->getData('sitelocation', $post->userData->account_id);



        $rq = array();

        array_push($rq, array('id'=> '', 'name' => 'Please select to give a recommendation'));

        array_push($rq, array('id'=> 'Kill', 'name' => 'Kill or cause permanent disability/ill health'));

        array_push($rq, array('id'=> '!!!', 'name' => 'Long term illness or serious injury'));

        array_push($rq, array('id'=> '!!', 'name' => 'Medical attention & several days off work'));

        array_push($rq, array('id'=> '!', 'name' => 'First aid needed'));



        $rl = array();

        array_push($rl, array('id'=> '', 'name' => 'Please select to give a recommendation'));

        array_push($rl, array('id'=> '++', 'name' => 'Very likely'));

        array_push($rl, array('id'=> '+', 'name' => 'Likely'));

        array_push($rl, array('id'=> '-', 'name' => 'Unlikely'));

        array_push($rl, array('id'=> '--', 'name' => 'Very unlikely'));



        // Reminder frequency

        $rf = array();

        array_push($rf, array('id'=> '0', 'name' => 'Never'));

        array_push($rf, array('id'=> '1', 'name' => 'Every day'));

        array_push($rf, array('id'=> '3', 'name' => 'Every 3 days'));

        array_push($rf, array('id'=> '5', 'name' => 'Every 5 days'));

        array_push($rf, array('id'=> '7', 'name' => 'Every 7 days'));

        array_push($rf, array('id'=> '14', 'name' => 'Every 14 days'));

        array_push($rf, array('id'=> '28', 'name' => 'Every 28 days'));



        // Remedial priority

        $rp = array();

        for($i=0; $i<=6; $i++) {

            array_push($rp, array('id' => $i, 'name' => $i));

        }


        return json_encode(array('injuries' => $data, 'sites' => $sites, 'employees' => $employees, 'locations' => $locations, 'nature' => $nature, 'bodypart' => $bodypart, 'mechanism' => $mechanism, 'rq' => $rq, 'rl' => $rl, 'rf' => $rf, 'rp' => $rp));
//        return json_encode(array('injuries' => $data, 'sites' => $sites, 'employees' => $employees, 'locations' => $locations, 'nature' => $nature, 'bodypart' => $bodypart, 'mechanism' => $mechanism, 'rq' => $rq, 'rl' => $rl, 'rf' => $rf, 'rp' => $rp));



    }
    public function deleteInjury($post){
        $db= new db("injury_register");
        $sql = "UPDATE injury_register SET deleted=1 WHERE id=".$post->data->id;
        $db->select(false, false, $sql);
    }
    public function getLastInjuryId(){
        $db= new db("injury_register");
        $sql = "SELECT * FROM injury_register ORDER BY id DESC LIMIT 1";
        $db->select(false, false, $sql);
        $db->getRow();
        $row = $db->row;
        return $row['id'];
    }

    public function saveInjury($post) {
        $db = new db('injury_register');
        $params = array();

        $params['id'] = $post->data->id;
        $params['account_id'] = $post->userData->account_id;
        $params['natureofinjury_id'] = $post->data->natureofinjury_id;
        $params['mechanismofinjury_id'] = $post->data->mechanismofinjury_id;
        $params['location_id'] = $post->data->location_id;
        $params['injuredbodypart_id'] = $post->data->injuredbodypart_id;
        $params['insurer_notified'] = $post->data->insurer_notified;
        $params['safework_notified'] = $post->data->safework_notified;

//        $params['insurernotified_date'] = $post->data->insurernotified_date;
//        $params['insurernotified_date'] = isset($post->data->insurernotified_date) ? date('Y-m-d', strtotime($post->data->insurernotified_date)) : '0000-00-00';
        if (isset($post->data->insurernotified_date)) {
            if($post->data->insurernotified_date == '0000-00-00') {
                $params['insurernotified_date'] = '';
            } else {
                $params['insurernotified_date'] = date('Y-m-d', strtotime($post->data->insurernotified_date));
            }
        } else {
            $params['insurernotified_date'] = '';
        }
        if (isset($post->data->safeworknotified_date)) {
            $params['safeworknotified_date'] = ($post->data->safeworknotified_date == '0000-00-00') ? '' : date('Y-m-d', strtotime($post->data->safeworknotified_date));
        } else {
            $params['safeworknotified_date'] = '';
        }

        $params['incident_time'] = $post->data->incident_time;
        $params['created_by'] = $post->data->created_by;
        $params['employee_id'] = $post->data->employee_id;
        $params['site_location_id'] = $post->data->site_location_id;
        $params['email_frequency'] = $post->data->email_frequency;
        $params['risk_likelihood'] = $post->data->risk_likelihood;
        $params['level_of_risk'] = $post->data->level_of_risk;
        $params['upload_file_path'] = $post->data->upload_file_path;
        if (trim($params['upload_file_path']) == '') {
            unset($params['upload_file_path']);
        }

        $params['incident_date'] = date('Y-m-d', strtotime($post->data->incident_date));
        $params['investigate_date'] = date('Y-m-d', strtotime("now"));
        $params['risk_identification'] = isset($post->data->risk_identification) ? $post->data->risk_identification : '';
        $params['risk_assessment'] = isset($post->data->risk_assessment) ? $post->data->risk_assessment : '';
        $params['risk_controls'] = isset($post->data->risk_controls) ? $post->data->risk_controls : '';
        $params['eyewitness'] = isset($post->data->eyewitness) ? $post->data->eyewitness : '';
        $params['investigated_by'] = isset($post->data->investigated_by) ? $post->data->investigated_by : '';
        $params['lost_time'] = $post->data->lost_time;
        if (isset($post->data->closed_date)) {
            if($post->data->closed_date == '0000-00-00') {
                $params['closed_date'] = '';
            } else {
                $params['closed_date'] = date('Y-m-d', strtotime($post->data->closed_date));
            }
        } else {
            $params['closed_date'] = '';
        }
        if (isset($post->data->workcover_date)) {
            if($post->data->workcover_date == '0000-00-00') $params['workcover_date'] = '';
            else $params['workcover_date'] = date('Y-m-d', strtotime($post->data->workcover_date));
        } else {
            $params['workcover_date'] = '';
        }

        $db->bindParams = true;
        $db->insertupdate($params);

        $log = array();
        $employee_name = getEmployeeName($post->data->employee_id);
        $who = getEmployeeName($post->userData->id);
        $log['who'] = $who['firstname']." ".$who['lastname'];
        if($post->data->id==0)
            $log['what'] = "Created a new entry on the injury form for ".$employee_name['firstname']." ".$employee_name['lastname'];
        else $log['what'] = "Edited an entry on the injury form for ".$employee_name['firstname']." ".$employee_name['lastname'];
        $log['time'] = date('Y-m-d H:i:s');
        $log['account_id'] = $post->userData->account_id;
        $db = new db("system_logs");
        $db->insert($log);

        $note = array();
        $note['hr_issue'] = $post->data->insurer_notified==1? "Workers Compensation" : "Medical Issue";
        $note['hr_issue_note'] = isset($post->data->risk_identification) ? $post->data->risk_identification : "";
        $note['flag_id'] = $post->data->insurer_notified==1? "3": null;
        $note['injury_id'] = $post->data->id==0 ? $this->getLastInjuryId() : $post->data->id;
        $note['hr_action'] = "Matter Logged onto File";
        $note['hr_action_note'] = isset($post->data->risk_controls) ? $post->data->risk_controls : "";
        $note['tagged_user_id'] = null;
        $note['upload_file_path'] = $post->data->upload_file_path;
        $note['hour'] = null;
        $note['min'] = 5;
        $note['mark_c'] = 0;
        $note['updated_time'] = date("Y-m-d H:i:s");
        $note['entered_user_id'] = $post->userData->id;
        $note['account_id'] = $post->data->account_id;
        $note['user_id'] = $post->data->employee_id;
        $db = new db("user_note");
        if($post->data->id==0)
            $sql = sprintf(
                "INSERT INTO user_notes 
                (hr_issue, hr_issue_note, flag_id,  hr_action, hr_action_note, injury_id, tagged_user_id, upload_file_path, hour, min, mark_c, updated_time, entered_user_id, account_id, user_id) 
                VALUES 
                ('%s', '%s', '%d', '%s', '%s', '%d', '%d', '%s', '%d', '%d', '%d', '%s', '%d', '%d', '%d')",
                $note['hr_issue'],
                $note['hr_issue_note'],
                $note['flag_id'],
                $note['hr_action'],
                $note['hr_action_note'],
                $note['injury_id'],
                $note['tagged_user_id'],
                $note['upload_file_path'],
                $note['hour'],
                $note['min'],
                $note['mark_c'],
                $note['updated_time'],
                $note['entered_user_id'],
                $note['account_id'],
                $note['user_id']
            );
        else
            $sql=sprintf(
                "UPDATE user_notes SET hr_issue='%s', hr_issue_note='%s', flag_id=%d, hr_action='%s', hr_action_note='%s', tagged_user_id=%d, upload_file_path='%s', hour=%d, min=%d, mark_c=%d, updated_time='%s', entered_user_id=%d, account_id=%d, user_id=%d WHERE injury_id=%d",
                $note['hr_issue'],
                $note['hr_issue_note'],
                $note['flag_id'],
                $note['hr_action'],
                $note['hr_action_note'],
                $note['tagged_user_id'],
                $note['upload_file_path'],
                $note['hour'],
                $note['min'],
                $note['mark_c'],
                $note['updated_time'],
                $note['entered_user_id'],
                $note['account_id'],
                $note['user_id'],
                $note['injury_id']
            );
        $db->select(false, false, $sql);
        $data = array();
        $data['injuries'] = $this->getInjuryList($post->userData->account_id);
        return json_encode($data);

    }

}

?>

