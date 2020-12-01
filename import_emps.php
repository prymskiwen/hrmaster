<?php

require('/home/hrmaster/public_html/assets/php/db.class.php');





    $today		= date("Y-m-d");
    $uploaded_file	= $filename;
    $uploaded_by	= 25;

    $timestamp		= mktime(date("H"),date("i"),date("s"), date("n"),date("j"),date("Y"));

    $lineNo = 0;
    $new = 0;
    $updated = 0;
    $invalid = 0;
    
    
    function fixArray($temp, $locations) {
       
        $data = array();
        foreach ($temp as $k => $v) {
            switch($k) {
                case 0: $key = 'full_name'; break;
                case 1: $key = 'start_date'; break;
                case 2: $key = 'site_location'; break;
                case 3: $key = 'workstate'; break;
                case 4: $key = 'end_date'; break;
                case 5: $key = 'employment_type_id'; break;
                case 6: $key = 'year'; break;
                case 7: $key = 'annual_rate'; break;
                case 8: $key = 'bonus'; break;
                case 9: $key = 'overtime'; break;
                case 10: $key = 'commission'; break;
                case 11: $key = 'compensation'; break;
                case 12: $key = 'department'; break;
                case 13: $key = 'personal_days'; break;
                case 14: $key = 'sick_days'; break;
                case 15: $key = 'performance_score'; break;
            }
            
            if ($key == 'site_location') {
                $lid = 0;
                foreach($locations as $l => $loc) {
                    if ($v == $loc) {
                        $lid = $l;
                    }
                }
               // $v = $lid;
            }
            
            if ($key == 'employment_type_id') {
                if ($v == 'Full Time') {
                    $v = 335;
                } elseif ($v =='Part Time') {
                    $v = 336;
                } elseif ($v == 'Casual') {
                    $v = 337;
                }
            }
            
            
            
            $data[$key] = $v;
            
        } 
        return $data;
    }
    
    
    
    $locations = array();
    $e = new db('employee');
    $ew = new db('employee_work');
    $d = new db('data');
    $d->select('type = :t', false, false, array('t' => 'sitelocation'));
    while ($d->getRow()) {
        $locations[$d->id] = $d->display_text;
    }
    
    $handle = false;
    
    $handle = fopen('/home/hrmaster/public_html/assets/files/emps.txt', "r");

    echo "<pre>";
    
    $headers = array();
    $fields = array();
    $temp = array();
    
    $i = 0;
    if ($handle !== FALSE) {
        $data = fgetcsv($handle, 0, "\t");
        
        foreach ($data as $key => $val) {
            if ($key <= 15) {
                if ($key == 15) {
                   array_push($headers, 'Performance Score');
                   $t = str_replace ('Performance Score', '', $val);
                   $t = trim($t);
                   $t = trim($t, '"');
                   
                   array_push($temp, $t);
                   $i++;
                   continue;
                } 
                array_push($headers, $val);
            }
            
            
            if ($i == 1 || $i == 4) {  // Hire date
                if  ($val) {
                    $dt = explode('/', $val);
                    $yr = ($dt[2] > date('y')) ? '19'.$dt[2] : '20'.$dt[2];
                    $mth = (strlen($dt[1]) < 2) ? '0'.$dt[1] : $dt[1];
                    $day = (strlen($dt[0]) < 2) ? '0'.$dt[0] : $dt[0];                
                    $val = $yr . '-' . $mth.'-'.$day;
                }
            } elseif ($i == 11 || $i == 7 || $i == 8 || $i == 9 || $i == 10) {
                $val = trim($val);
                $val = trim($val,'$');
                $val = str_replace (',', '' ,$val);
            }
            array_push($temp, $val);
            
            
            
            $i++;
            if ($i == 15) {
                
                $dat = fixArray($temp,$locations);
                
                
                array_push($fields, $dat);
                $temp = array();
                $i = 0;
            }
            
        }
        print_r($headers);
        print_r($fields);
        
        //$e = new db('employee');
        //$ew = new db('employee_work');
        $empName = '';
        $startDate = '';
        foreach ($fields as $key => $emp) {
            if ($key == 0) {
                continue;
            }
            
            $nameArr = explode(',', $emp['full_name']);
            $edata = array('account_id' => 2, 'firstname' => $nameArr[1], 'lastname' => $nameArr[0]);
            if (($emp['full_name'] !== $empName)) {   // && $emp['start_date'] === $startDate)) {
                $e->insert($edata);
                $empName = $emp['full_name'];
                $startDate = $emp['start_date'];
            }          
            
            $e->select('firstname = :f AND lastname = :l', false, false, array('f' => $nameArr[1], 'l' => $nameArr[0]));
            $e->getRow();
            $eid = $e->id;
            
            $ew->update(array('active' => 1), 'employee_id = :eid', false, array('active' => 0, 'eid' => $eid));
            
            $dat = array();
            $dat['employee_id'] = $eid;
            $dat['start_date'] = $emp['start_date'];
            $dat['site_location_name'] = $emp['site_location'];
            $dat['workstate'] = 2;
            $dat['end_date'] = $emp['end_date'];
            $dat['employment_type_id'] = $emp['employment_type_id'];
            $dat['year'] = $emp['year'];
            $dat['annual_rate'] = $emp['annual_rate'];
            $dat['bonus'] = $emp['bonus'];
            $dat['overtime'] = $emp['overtime'];
            $dat['commission'] = $emp['commission'];
            $dat['compensation'] = $emp['compensation'];
            $dat['department_name'] = $emp['department'];
            $dat['personal_days'] = $emp['personal_days']; 
            $dat['sick_days'] = $emp['sick_days'];  
            $dat['active'] = 1;
            
            $input = array();
            foreach($dat as $k => $val) {
                if ($val == '') {
                    continue;
                }
                
                $input[$k] = $val;
                
            }
            
            $ew->insert($input);
                                   
        }
        
        
/*            print_r($data); die;
            continue;
         
            if ($lineNo == 1) {  
                $headers = getHeaders($data);
                continue;
            }


            $record = array();
            $record = getData($headers, $data);
            if (!isset($record['memid'])) {
                $invalid++;
                continue;
            }
            
            $record['subs_paid_amount'] = str_replace('$','',$record['subs_paid_amount']);
            $record['subs_paid_amount'] = str_replace(',','',$record['subs_paid_amount']);
         
            
            $db->select('memid = :m', false, false, array('m' => $record['memid']));
            if ($db->numRows == 0) {
                $db->insert($record);                    
                $new++;
            } else {
                $db->getRow();
                $params = $record;
                $params['id'] = $db->id;
                $db->update($record, 'id = :id', 1, $params); 
                $updated++;
            }

        }
        fclose($handle);
        unlink($_FILES['actfile']['tmp_name']);
 * */

    }

    $usermessage = "<b>Done</b>. &nbsp;".$lineNo." records processed. ".$new." added : ".$updated." updated : $invalid invalid.";

?>



