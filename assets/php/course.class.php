<?php
ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);


if (!class_exists('db')) {
    require('db.class.php');
}

if(!ini_get('date.timezone')){
    date_default_timezone_set('GMT');
}

class course {

    var $lockoutLengthMins = 15;
    var $database = '';
    var $user = array();
    var $server_url = "http://www.hrmaster.com.au";
    var $courseTable = 'course';
    var $questionsTable = 'questions';
    var $answersTable = 'answers';

    function __construct() {

    }

    // Get the id of the first deleted question for an answer - 0 if none
    private function getExistingAnswer($questionId) {
        $db = new db($this->answersTable);
        $db->select('question_id = :qid AND deleted = :del', 'answer_id ASC', false, array('qid' => $questionId, 'del' => 1));
        if ($db->numRows == 0) {
            return 0;
        } else {
            $db->getRow();
            return $db->answer_id;
        }
    }

    public function saveAnswers($questionId, $answers, $correctAnswerIndex) {
        $correctId = 0;
        $db = new db($this->answersTable);
        $counter = -1;
        foreach($answers as $index => $answer) {
            $counter++;
            if (trim($answer->title) == '') {
                continue;  // ignore answers with no name/label
            }

            $existingAnswer = $this->getExistingAnswer($questionId);
            $data = array();
            $data['title'] = $answer->title;
            $data['question_id'] = $questionId;

            $cIndex = 0;
            if ($existingAnswer == 0) {
                $db->insert($data);
                $cIndex = $db->lastInsertId;
            } else {
                $data['deleted'] = 0;
                $params = $data;
                $params['aid'] = $existingAnswer;
                $db->update($data, 'answer_id = :aid', 1, $params);
                $cIndex = $existingAnswer;
            }

            if ($counter == $correctAnswerIndex) {
                $correctId = $cIndex;
            }
        }
        return $correctId;
    }

    // Get the id of the first deleted question for an answer - 0 if none
    private function getExistingQuestion($courseId) {
        $db = new db($this->questionsTable);
        $db->select('course_id = :cid AND deleted = :del', 'question_id ASC', false, array('cid' => $courseId, 'del' => 1));
        if ($db->numRows == 0) {
            return 0;
        } else {
            $db->getRow();
            return $db->question_id;
        }
    }

    // updated by Alex-cobra -20-04-11
    public function saveQuestions($courseId, $questions) {


        foreach($questions as $obj) {
            if(!isset($obj->question_id))
                continue;
            $q = new db($this->questionsTable);
            $q->update(array('deleted' => 1), 'course_id = :cid', false, array('deleted' => 1, 'cid' => $courseId));
            $a = new db('answers');
            $a->update(array('deleted' => 1), 'question_id = :qid', false, array('deleted' => 1, 'qid' => $obj->question_id));
        }

        $db = new db($this->questionsTable);
        $db->bindParams = true;

        foreach($questions as $index => $obj) {

            if(!isset($obj->title)) {  // ignore questions with no title
                continue;
            }

            if (trim($obj->title) == '') {  // ignore questions with no title
                continue;
            }
            $data = array();

            $data['title'] = $obj->title;
            $data['media_type'] = (isset($obj->media_type)) ? $obj->media_type : 99;
            $data['course_id'] = $courseId;
            $data['image'] = '';
            $data['video'] = '';
            $data['pdf'] = '';
            switch($data['media_type']) {
                case 0: $data['image'] = $obj->image?$obj->image:''; break;
                case 1: $data['video'] = $obj->video?$obj->video:''; break;
                case 3: $data['pdf'] = $obj->pdf?$obj->pdf:''; break;
            }

            $existingQuestion = $this->getExistingQuestion($courseId);

//            $recordId = 0;

            if ($existingQuestion == 0) {
                if ($data['image'] == '') {
                    unset($data['image']);
                }
                if ($data['video'] == '') {
                    unset($data['video']);
                }
                if ($data['pdf'] == '') {
                    unset($data['pdf']);
                }
                $db->insert($data);
                $recordId = $db->lastInsertId;
            } else {
                $data['deleted'] = 0;
                $params = $data;
                $params['qid'] = $existingQuestion;
                $db->update($data, 'question_id = :qid', 1, $params);
                $recordId = $existingQuestion;
            }
            if(isset($obj->correct_answer_index)) {
                $correctAnswerId = $this->saveAnswers($recordId, $obj->answers, $obj->correct_answer_index);
                $db->update(array('correct_answer_id' => 1), 'question_id = :qid', 1, array('correct_answer_id' => $correctAnswerId, 'qid' => $recordId));
            }
        }
    }
    // // // // //

    public function saveCourse($course) {
        
        $db = new db($this->courseTable);

        $data = array();
        $data['course_id'] = isset($course->data->course_id) ? $course->data->course_id : 0;
        $data['course_type'] = $course->data->course_type;
        $data['status'] = $course->data->status;
        $data['time_limit'] = $course->data->time_limit;
        $data['is_randomized'] = $course->data->is_randomized;
        //        start updated by Alex-cobra-2020-04-17
        $data['is_locked'] = $course->data->is_locked;
        //        end updated by Alex-cobra-2020-04-17
        $data['display_error_message'] = $course->data->display_error_message;
        $data['reorder'] = $course->data->reorder;
        $data['is_comeback'] = $course->data->is_comeback;
        $data['try_again'] = $course->data->try_again;
        $data['is_global'] = $course->data->is_global;
        $data['correct_only'] = $course->data->correct_only;
        $data['is_auto_inactive'] = $course->data->is_auto_inactive;
        $data['go_back'] = $course->data->go_back;


        $data['course_category_id'] = $course->data->course_category_id;
        $data['course_name'] = (isset($course->data->course_name)) ? $course->data->course_name : '';
        $data['course_description'] = (isset($course->data->course_description)) ? $course->data->course_description : '';
        $data['user_id'] = $course->userData->id;
        $data['deleted'] = 0;
        $db->bindParams = true;
        // var_dump($data['course_description']);
        // var_dump(htmlspecialchars($data['course_description'], ENT_QUOTES));exit;
        $is_newCourse = 1;
        if($data['course_id'] != 0) {
            $is_newCourse = 0;


            //start updated by Alex-cobra-2020-04-17(add is_locked value)
            $sql = sprintf('UPDATE course SET course_type="%s", status=%d, time_limit="%s", is_randomized=%d, is_locked=%d, display_error_message=%d, reorder=%d, is_comeback=%d, try_again=%d, is_global=%d, correct_only=%d, is_auto_inactive=%d, go_back=%d, course_category_id=%d, course_name="%s", course_description="%s", user_id=%d, deleted=%d WHERE course_id=%d',
                $data['course_type'],
                $data['status'],
                $data['time_limit'],
                $data['is_randomized'],
                $data['is_locked'],
                $data['display_error_message'],
                $data['reorder'],
                $data['is_comeback'],
                $data['try_again'],
                $data['is_global'],
                $data['correct_only'],
                $data['is_auto_inactive'],
                $data['go_back'],
                $data['course_category_id'],
                $data['course_name'],
                htmlspecialchars($data['course_description'], ENT_QUOTES),
                $data['user_id'],
                $data['deleted'],
                $data['course_id']);
        }
        else
        {
            $sql = sprintf('INSERT INTO course (
course_id, course_type, status, time_limit, is_randomized, 
is_locked, display_error_message, reorder, is_comeback, try_again, 
is_global, correct_only, is_auto_inactive, course_category_id, course_name, course_description, user_id, deleted) 
                            VALUES (%d, "%s", %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, "%s", "%s", %d, %d)',
                $data['course_id'],
                $data['course_type'],
                $data['status'],
                $data['time_limit'],
                $data['is_randomized'],
                $data['is_locked'],
                $data['display_error_message'],
                $data['reorder'],
                $data['is_comeback'],
                $data['try_again'],
                $data['is_global'],
                $data['correct_only'],
                $data['is_auto_inactive'],
                $data['go_back'],
                $data['course_category_id'],
                $data['course_name'],
                htmlspecialchars($data['course_description'], ENT_QUOTES),
                $data['user_id'],
                $data['deleted']);
        }
        //echo $sql;exit;

        //end updated by Alex-2020-04-17
        $db->select(false, false, $sql);
        // updated by Alex-cobra -20-04-03
        $course_id = $data['course_id']!=0? $data['course_id']: $db->connection->lastInsertId();
        // // // // //
        if($course->data->is_auto_inactive == 1 && $course->data->auto_inactive_time)
        {
            $data['auto_inactive_time'] = date('Y-m-d H:i:s', strtotime($course->data->auto_inactive_time));
        }
        else
        {
            $data['auto_inactive_time'] = NULL;
        }
        $db->update(array('auto_inactive_time'=>$data['auto_inactive_time']),'course_id = :cid', false,
            array('auto_inactive_time'=>$data['auto_inactive_time'], 'cid'=>$course_id));
        $this->saveQuestions($course_id, $course->data->questions);
        if($is_newCourse == 1) {
            $this->addCourseLog($data['user_id'], $data['course_name'], 'create_course');
        }
        else {
            $this->addCourseLog($data['user_id'], $data['course_name'], 'modify_course');
        }

        if ($course_id) {
            return json_encode(array('success' => 1));
        } else {
            return json_encode(array('success' => 0));
        }
        // // // // //
    }

    public function saveFile() {
        // updated by Alex-cobra -20-04-10
        $type = $_POST['type'];
        $directory = 'other/';
        switch($type) {
            case 'image': $directory = 'images/'; break;
            case 'video': $directory = 'video/'; break;
            case 'pdf': $directory = 'pdf/'; break;
        }

        // setting in real server
        $uploadsDir = "./assets/uploads/".$directory;

        $file = $_FILES['fileToUpload'];

        if ($file['error']) {
            echo json_encode(array('success' => 0, 'message' => 'Error uploading file', 'error'=>$file['error'], $_FILES['fileToUpload']));
            die;
        }

        $arr = explode('.', $file['name']);
        $extn = array_pop($arr);
        if($type=='pdf') {
            $extn = 'pdd';
        }
        $newName = implode('',$arr).'--'.time().".".$extn;

        move_uploaded_file($file['tmp_name'], $uploadsDir.$newName);
        $url_prefix = $_SERVER['HTTP_ORIGIN'];
//        $url_prefix = 'https://hrmaster.com.au';
        if ($type=='pdf')
            return json_encode(array('success' => 1, 'message' => 'Success! File uploaded successfully.',
                'filename' => $newName, 'href' => $url_prefix.'/assets/uploads/'.$directory.$newName));
        else
            return json_encode(array('success' => 1, 'message' => 'Success! File uploaded successfully.',
                'filename' => $newName, 'href' => $url_prefix.'/assets/uploads/'.$directory.$newName));
        // // // // //
    }

    public function getAnswers($cid, $qid, $correct_id) {
        $data = array();
        $a = new db($this->answersTable);
        $sql = "SELECT a.* FROM answers a JOIN questions q ON q.question_id=a.question_id WHERE q.course_id=".$cid." AND q.question_id=".$qid." AND q.deleted=0 AND a.deleted=0";
        $a->select(false, false, $sql);
        $i = 0;
        $correctIndex = '';
        while($a->getRow()) {
            $a->row['index'] = $i;
            if ($correct_id == $a->answer_id) {
                $correctIndex = $i;
            }
            array_push($data, $a->row);
            $i++;
        }
        return array('answers' => $data, 'correctIndex' => $correctIndex);
    }

    //start updated by Alex-cobra -2020-04-04(change $uploadsDir and echo "href")
    public function getCourse($params, $url_prefix) {
        $detail = array();
        $db = new db($this->courseTable);
        $user_id = $params->userData->id;
        $db->select('course_id = :cid AND user_id = :uid AND deleted=:del', false, false, array('cid' => $params->course_id, 'uid' => $user_id, 'del'=>0));
        if ($db->numRows > 0) {
            $db->getRow();
            $detail['detail'] = $db->row;
            $detail['questions'] = array();
            $q = new db($this->questionsTable);
            $q->select('course_id = :cid AND deleted = :notdel', 'question_id ASC', false, array('cid' => $params->course_id, 'notdel' => 0));
            $idx = 0;
            while ($q->getRow()) {
                $q->row['index'] = $idx;
                $qNum = $idx + 1;
                $q->row['name'] = "Question ".$qNum;
                $q->row['media_type'] = (int)$q->row['media_type'];

                switch($q->row['media_type']) {
                    case '0': $q->row['image_href'] = $q->image?$url_prefix.'/assets/uploads/images/'.$q->row['image']:""; break;
                    case '1': $q->row['video_href'] = $q->video?$url_prefix.'/assets/uploads/video/'.$q->row['video']:""; break;
                    case '3': $q->row['pdf_href'] = $q->pdf?$url_prefix.'/assets/uploads/pdf/'.$q->row['pdf']:""; break;
                }
                $aList = $this->getAnswers($params->course_id, $q->question_id, $q->correct_answer_id);

                $answerList = $aList['answers'];
                $q->row['answer_count'] = count($answerList);
                $q->row['correct_answer_index'] = $aList['correctIndex'];
                $qArr = $q->row;
                $qArr['answers'] = $answerList;
                array_push($detail['questions'], $qArr);
                $idx++;
            }
        }

        return json_encode($detail);
    }
    // end update

    public function removeFile($params) {
        //
    }

    //start updated by Alex-cobra-2020-04-02(change $delDir)
    public function removeFiles($params) {
        // $type = $_POST['type'];
        $index = $params->index;
        $course = $params->course;

        $url_pdf = $course[$index]->pdf;
        $url_image = $course[$index]->image;
        $url_video = $course[$index]->video;

        $delDir = "assets/uploads/pdf/";
        if (file_exists($delDir.$url_pdf) === true) {

            unlink($delDir.$url_pdf);
        }
        $delDir = "assets/uploads/images/";
        if (file_exists($delDir.$url_image) === true) {
            unlink($delDir.$url_image);
        }
        $delDir = "assets/uploads/video/";
        if (file_exists($delDir.$url_video) === true) {
            unlink($delDir.$url_video);
        }
        return  json_encode(array('success' => 1, 'message' => 'Success! File deleted successfully.'));
    }
    //end updated by Alex-cobra-2020-04-02(change $delDir)

    // updated by Alex-cobra -20-04-11  --- system logs add
    public function addCourseLog($accountID, $courseName, $log_type) {
        $data = array();
        $u_db = new db('user');
        $sql = 'SELECT CONCAT(firstname," ",lastname) as userName, account_id from user WHERE account_id ='.$accountID;
        $u_db->select(false, false, $sql);
        $u_db->getRow();
        $data['who'] = $u_db->row['userName'];
        if($log_type == 'create_course') {
            $data['what'] = 'Created "'.$courseName.'" course.';
        }
        else if($log_type == 'modify_course') {
            $data['what'] = 'Modified "'.$courseName.'" course.';
        }
        $data['account_id'] = $u_db->row['account_id'];
        $db = new db('system_logs');
        $db->insert($data);
    }
    // // // // //

}

?>

