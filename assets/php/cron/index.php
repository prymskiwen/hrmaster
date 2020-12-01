<?php
date_default_timezone_set("Australia/Sydney");

class email{
	var $address_valid = true;
	/*
	list of To addresses
	@var	array
	*/
	var $sendto = array();
	/*
	@var	array
	*/
	var $acc = array();
	/*
	@var	array
	*/
	var $abcc = array();
	/*
	paths of attached files
	@var array
	*/
	var $aattach = array();
	/*
	list of message headers
	@var array
	*/
	var $xheaders = array();
	/*
	message priorities referential
	@var array
	*/
	var $priorities = array( '1 (Highest)', '2 (High)', '3 (Normal)', '4 (Low)', '5 (Lowest)' );
	/*
	character set of message
	@var string
	*/
	var $charset = "us-ascii";
	var $ctencoding = "7bit";
	var $receipt = 0;
	var $sendResult		= false;
    /*
    	Mail contructor
    
    */
    function email()
    {
    	$this->autoCheck( true );
    	$this->boundary= "--" . md5( uniqid("myboundary") );
    }
    /*
    activate or desactivate the email addresses validator
    ex: autoCheck( true ) turn the validator on
    by default autoCheck feature is on
    
    @param boolean	$bool set to true to turn on the auto validation
    @access public
    */
    function autoCheck( $bool )
    {
    	if( $bool )
    		$this->checkAddress = true;
    	else
    		$this->checkAddress = false;
    }
    /*
    Define the subject line of the email
    @param string $subject any monoline string
    
    */
    function Subject( $subject )
    {
    	$this->xheaders['Subject'] = strtr( $subject, "\r\n" , "  " );
    }
    /*
    
    set the sender of the mail
    @param string $from should be an email address
    
    */
    function From( $from )
    {
    	if( ! is_string($from) ) {
    		echo "Class Mail: error, From is not a string";
    		exit;
    	}
    	$this->xheaders['From'] = $from;
    }
    
    /*
     set the Reply-to header
     @param string $email should be an email address
    
    */
    function ReplyTo( $address )
    {
    	if( ! is_string($address) )
    		return false;
    	$this->xheaders["Reply-To"] = $address;
    }
    /*
    add a receipt to the mail ie.  a confirmation is returned to the "From" address (or "ReplyTo" if defined)
    when the receiver opens the message.
    
    @warning this functionality is *not* a standard, thus only some mail clients are compliants.
    
    */
    function Receipt()
    {
    	$this->receipt = 1;
    }
    /*
    set the mail recipient
    @param string $to email address, accept both a single address or an array of addresses
    
    */
    function To( $to )
    {
    	// TODO : test validit� sur to
    	if( is_array( $to ) )
    		$this->sendto= $to;
    	else
    		$this->sendto[] = $to;
    	if( $this->checkAddress == true )
    		$this->CheckAdresses( $this->sendto );
    }
    /*		Cc()
     *		set the CC headers ( carbon copy )
     *		$cc : email address(es), accept both array and string
     */
    function Cc( $cc )
    {
    	if( is_array($cc) )
    		$this->acc= $cc;
    	else
    		$this->acc[]= $cc;
    	if( $this->checkAddress == true )
    		$this->CheckAdresses( $this->acc );
    }
    /*		Bcc()
     *		set the Bcc headers ( blank carbon copy ).
     *		$bcc : email address(es), accept both array and string
     */
    
    function Bcc( $bcc )
    {
    	if( is_array($bcc) ) {
    		$this->abcc = $bcc;
    	} else {
    		$this->abcc[]= $bcc;
    	}
    	if( $this->checkAddress == true )
    		$this->CheckAdresses( $this->abcc );
    }
    /*		Body( text [, charset] )
     *		set the body (message) of the mail
     *		define the charset if the message contains extended characters (accents)
     *		default to us-ascii
     *		$mail->Body( "m�l en fran�ais avec des accents", "iso-8859-1" );
     */
    function Body( $body, $charset="" )
    {
    	$this->body = $body;
    	if( $charset != "" ) {
    		$this->charset = strtolower($charset);
    		if( $this->charset != "us-ascii" )
    			$this->ctencoding = "8bit";
    	}
    }
    /*		Organization( $org )
     *		set the Organization header
     */
    function Organization( $org )
    {
    	if( trim( $org != "" )  )
    		$this->xheaders['Organization'] = $org;
    }
    /*		Priority( $priority )
     *		set the mail priority
     *		$priority : integer taken between 1 (highest) and 5 ( lowest )
     *		ex: $mail->Priority(1) ; => Highest
     */
    function Priority( $priority )
    {
    	if( ! intval( $priority ) )
    		return false;
    	if( ! isset( $this->priorities[$priority-1]) )
    		return false;
    	$this->xheaders["X-Priority"] = $this->priorities[$priority-1];
    	return true;
    }
    /*
     Attach a file to the mail
     @param string $filename : path of the file to attach
     @param string $filetype : MIME-type of the file. default to 'application/x-unknown-content-type'
     @param string $disposition : instruct the Mailclient to display the file if possible ("inline") or always as a link ("attachment") possible values are "inline", "attachment"
     */
    function Attach( $filename, $filetype = "", $disposition = "inline" )
    {
    	// TODO : si filetype="", alors chercher dans un tablo de MT connus / extension du fichier
    	if( $filetype == "" )
    		$filetype = "application/x-unknown-content-type";
    	$this->aattach[] = $filename;
    	$this->actype[] = $filetype;
    	$this->adispo[] = $disposition;
    }
    /*
    Build the email message
    @access protected
    */
    function BuildMail()
    {
    	// build the headers
    	$this->headers = "";
    //	$this->xheaders['To'] = implode( ", ", $this->sendto );
    	if( count($this->acc) > 0 )
    		$this->xheaders['CC'] = implode( ", ", $this->acc );
    	if( count($this->abcc) > 0 )
    		$this->xheaders['BCC'] = implode( ", ", $this->abcc );
    	if( $this->receipt ) {
    		if( isset($this->xheaders["Reply-To"] ) )
    			$this->xheaders["Disposition-Notification-To"] = $this->xheaders["Reply-To"];
    		else
    			$this->xheaders["Disposition-Notification-To"] = $this->xheaders['From'];
    	}
    	if( $this->charset != "" ) {
    		$this->xheaders["Mime-Version"] = "1.0";
    		$this->xheaders["Content-Type"] = "text/html; charset=$this->charset";
    		$this->xheaders["Content-Transfer-Encoding"] = $this->ctencoding;
    	}
    	$this->xheaders["X-Mailer"] = "Php/libMailv1.3";
    
    	// include attached files
    	if( count( $this->aattach ) > 0 ) {
    		$this->_build_attachement();
    	} else {
    		$this->fullBody = $this->body;
    	}
    	reset($this->xheaders);
    	while( list( $hdr,$value ) = each( $this->xheaders )  ) {
    		if( $hdr != "Subject" )
    			$this->headers .= "$hdr: $value\n";
    	}
    }
    /*
    	fornat and send the mail
    	@access public
    */
    function Send() {
    	if ($this->address_valid) {   // Only send if the address is valid!
    		$this->BuildMail();
    		$this->strTo = implode( ", ", $this->sendto );
    		// envoie du mail
    		$this->sendResult = @mail( $this->strTo, $this->xheaders['Subject'], $this->fullBody, $this->headers );
    	}
    }
    /*
     *		return the whole e-mail , headers + message
     *		can be used for displaying the message in plain text or logging it
     */
    function Get()
    {
    	$this->BuildMail();
    	$mail = "To: " . $this->strTo . "\n";
    	$mail .= $this->headers . "\n";
    	$mail .= $this->fullBody;
    	return $mail;
    }
    /*
    	check an email address validity
    	@access public
    	@param string $address : email address to check
    	@return true if email adress is ok
     */
    function ValidEmail($address)
    {
    	if( ereg( ".*<(.+)>", $address, $regs ) ) {
    		$address = $regs[1];
    	}
     	if(ereg( "^[^@  ]+@([a-zA-Z0-9\-]+\.)+([a-zA-Z0-9\-]{2}|net|com|gov|mil|org|edu|int)\$",$address) )
     		return true;
     	else
     		return false;
    }
    /*
    	check validity of email addresses
    	@param	array $aad -
    	@return if unvalid, output an error message and exit, this may -should- be customized
    
     */
    function CheckAdresses( $aad )
    {
            return true;   // Don't validate for now.
    	for($i=0;$i< count( $aad); $i++ ) {
    		if( ! $this->ValidEmail( $aad[$i]) ) {
    			echo "Class Mail, method Mail : invalid address $aad[$i]";
    			$this->address_valid	= false;
    			//exit;
    		}
    	}
    }
    /*
     check and encode attach file(s) . internal use only
     @access private
    */
    function _build_attachement()
    {
    	$this->xheaders["Content-Type"] = "multipart/mixed;\n boundary=\"$this->boundary\"";
    	$this->fullBody = "This is a multi-part message in MIME format.\n--$this->boundary\n";
    	$this->fullBody .= "Content-Type: text/html; charset=$this->charset\nContent-Transfer-Encoding: $this->ctencoding\n\n" . $this->body ."\n";
    	$sep= chr(13) . chr(10);
    	$ata= array();
    	$k=0;
    	// for each attached file, do...
    	for( $i=0; $i < count( $this->aattach); $i++ ) {
    		$filename = $this->aattach[$i];
    		$basename = basename($filename);
    		$ctype = $this->actype[$i];	// content-type
    		$disposition = $this->adispo[$i];
    		if( ! file_exists( $filename) ) {
    			echo "Class Mail, method attach : file $filename can't be found"; exit;
    		}
    		$subhdr= "--$this->boundary\nContent-type: $ctype;\n name=\"$basename\"\nContent-Transfer-Encoding: base64\nContent-Disposition: $disposition;\n  filename=\"$basename\"\n";
    		$ata[$k++] = $subhdr;
    		// non encoded line length
    		$linesz= filesize( $filename)+1;
    		$fp= fopen( $filename, 'r' );
    		$ata[$k++] = chunk_split(base64_encode(fread( $fp, $linesz)));
    		fclose($fp);
    	}
    	$this->fullBody .= implode($sep, $ata);
    }
}
class db {
    var $table          = "";
    var $dbArr          = array();
    var $connection	= null;
    var $result         = null;
    var $lastInsertId   = 0;
    var $numRows        = 0;
    var $row            = array();
    var $errorInfo      = array();
    var $rowsAffected   = 0;
	public function __construct($table=false, $db=false) {
        // Contructor that will automatically connect to the database upon instantiation
        $server 	= "localhost";
        $username 	= "hrmaster_admin";
        $password	= "Password123";
        $database   = 'hrmaster_hrmaster';
        //$database = "hrmaster_dev";
        try {
            $dsn                =   "mysql:host=$server;dbname=$database;charset=utf8";
            $this->connection   = new PDO($dsn, $username, $password);
        }  catch(PDOException $e) {
            echo 'ERROR: '. $e->getMessage();
        }
        $this->table        = $table;
	}
    private function execute($params=null) {
        $this->result       = $this->connection->prepare($this->query);
        $this->result->execute($params);
        $this->errorInfo    = $this->connection->errorInfo();
    }
    public function getInsertId() {
        return $this->lastInsertId;
    }
    /** Function to SELECT records from the database and tables */
    function select($where=false, $order=false, $sql=false, $params=array(), $limit=false) {
        if ($sql) {
            $this->query    = $sql;
        } else {
            $this->query	= "SELECT ".$this->table.".* FROM ".$this->table;
            $this->query    .= ($where == false) ? "" : " WHERE ".$where;
            $this->query    .= ($order == false) ? "" : " ORDER BY ".$order;
            $this->query    .= ($limit == false) ? "" : " LIMIT ".$limit;
        }
        //echo $sql;
        $this->execute($params);
        $this->numRows      = $this->result->rowCount();
    }
    /** Function to Insert records into a database
      * Param $arr contains the database field names as the Array Key and the value of the field is the value of the particular element in the array
    **/
    function insert($params=array()) {
        if (count($params) == 0) {
            return;
        }
        $values		= "";
        $keys		= "";
        foreach ($params as $key => $val) {
                $keys	.= $key.", ";
                $values	.= ":$key, ";
        }
        // Trim these fields to remove the right ","
        $keys			= rtrim($keys,", ");
        $values			= rtrim($values,", ");
        $this->query	= "INSERT INTO ".$this->table." (".$keys.") VALUES (".$values.")";
        $this->execute($params);
        // echo "\nPDOStatement::errorInfo():\n";
        // $arr = $this->result->errorInfo();
        // print_r($arr);
        $this->lastInsertId = $this->connection->lastInsertId();
    }
    /** Function to Replace records into a database
      * Param $arr contains the database field names as the Array Key and the value of the field is the value of the particular element in the array
    **/
    function replace($params=array()) {
        if (count($params) == 0) {
            return;
        }
        $values		= "";
        $keys		= "";
        foreach ($params as $key => $val) {
                $keys	.= $key.", ";
                $values	.= ":$key, ";
        }
        // Trim these fields to remove the right ","
        $keys			= rtrim($keys,", ");
        $values			= rtrim($values,", ");
        $this->query	= "REPLACE INTO ".$this->table." (".$keys.") VALUES (".$values.")";
        $this->execute($params);
        $this->lastInsertId = $this->connection->lastInsertId();
    }
	/** Function to UPDATE record(s) in a database
	  * Param $arr contains the database field names as the Array Key and the value of the field is the value of the particular element in the array
	**/
	function update($fields=array(), $where=false, $limit=false, $params=array()) {
        if (count($fields) == 0) {
            return;
        }
        $qStr	= "";
        foreach ($fields as $key => $val) {
            $qStr .= "$key = :$key, ";
        }
        $qStr			= rtrim($qStr, ", ");
        $this->query	= "UPDATE ".$this->table." SET ".$qStr;
        if ($where) {
            $this->query	.= " WHERE ".$where;
        }
        if ($limit) {
            $this->query      .= " LIMIT $limit";
        }
        //echo $this->query;
        $this->execute($params);
        $this->rowsAffected = $this->result->rowCount();
	}
	function delete($where=false, $limit=false, $params=array()) {
		$this->query         = "DELETE FROM ".$this->table;
		if ($where) {
            $this->query    .= " WHERE ".$where;
		}
        if ($limit) {
            $this->query    .= " LIMIT 1";
        }
		$this->execute($params);
	}
    private function _getPrimaryKeyField() {
        $this->query = "SHOW INDEX FROM ".$this->table." WHERE Key_name = :kType";
        $this->execute(array('kType' => 'PRIMARY'));
        if ($this->result->rowCount() > 0) {
            $this->getRow();
            return $this->Column_name;
        }
        return '';
    }
    function insertupdate($params) {
        if (count($params) == 0) {
            return;
        }
        $keyFld = $this->_getPrimaryKeyField();
        $qStr = "";
        foreach ($params as $key => $val) {
            $qStr .= "$key = :$key, ";
        }
        // Trim these fields to remove the right ","
        $qStr	= rtrim($qStr,", ");
        $this->query	= "INSERT INTO ".$this->table." SET $qStr ON DUPLICATE KEY UPDATE $qStr";
        $this->execute($params);
        $this->lastInsertId = ($this->connection->lastInsertId()) ? $this->connection->lastInsertId() : $keyFld ? $params[$keyFld] : 0;
    }           
    /** Get the next rows from the result resource   */
    function getRow() {
        $this->row      = array();
        $this->row      = $this->result->fetch(PDO::FETCH_ASSOC);
        if ($this->row !== false) {
            foreach($this->row as $key => $val) {
                $this->$key		= $val;
                if($this->$key == "hr_issue")
                    error_log($this->$key.": ". $val);
            }
        }
        return $this->row;
    }
    private function format_date($date, $separator="/") {
        if ($date == "") {
            return "0000-00-00";
        }
        $a      = explode($separator, $date);
        return $a[2].'-'.$a[1].'-'.$a[0];
    }
    public function format_display_date($date, $separator="-") {
        if ($date == "" || $date == "0000-00-00") {
            return "-";
        }
        $a      = explode($separator, $date);
        return $a[2].'-'.$a[1].'-'.$a[0];
    }
    public function __destruct() {
        unset($this->dbConn);
    }
}
function datediff($date1, $date2){
    $d1 = strtotime($date1);
    $d2 = strtotime($date2);
    return ($d2-$d1)/60/60/24;
}

function email($due_date, $period, $days_prior, $email_con, $array_text_exchanged, $email_to){
    $today = new DateTime();
    if(strpos($email_con, 'birthday') == false){
        switch($period){
            case 0:{
                if($due_date!=null){
                    if($days_prior == 0){
                        $td = date_format($today, 'Y-m-d');
                        $dd = date_format(date_create($due_date), 'Y-m-d');
                        if($td == $dd){
                            sendEmail($email_to, $email_con, $array_text_exchanged);
                            return 1;
                        }
                    }else{
                        $td = date_format($today, 'Y-m-d');
                        $dd = date_format(date_create($due_date), 'Y-m-d');
                        $diff = datediff($td, $dd);
                        if($diff == $days_prior){
                            sendEmail($email_to, $email_con, $array_text_exchanged);
                            return 1;
                        }
                    }
                }
                break;
            }
            default: {
                if($due_date!=null){
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
        }
    }else {
        switch($period){
            case 0:{
                if($due_date!=null){
                    if($days_prior == 0){
                        $td = date_format($today, "m/d");
                        $dd = date_format(date_create($due_date), "m/d");
                        
                        if($td === $dd){
                            sendEmail($email_to, $email_con, $array_text_exchanged);
                            return 1;
                        }
                    }else {
                        $date1 = date_format(date_create($due_date), "m/d");
                        $date2 = date_format(date_add($today, date_interval_create_from_date_string($days_prior. " days")), "m/d");
                        if($date1 === $date2){
                            sendEmail($email_to, $email_con, $array_text_exchanged);
                            return 1;
                        }
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
        if($key == "<\$insertdate>" || $key == "<\$insertDOB>"){
            $value = formatDate($value);
        }
        $email_con = str_replace($key, $value, $email_con);
    }
    $array = explode("<p></p>", $email_con, 2);
    $subject = $array[0];
    $message = $array[1];
    //var_dump($array);
    /*var_dump($subject);
    var_dump($message);*/
    //exit;
    $m= new email(); // create the mail

    $m->From("HR Master Support <support@hrmaster.com.au>");

    $m->To($email_to);
    //$m->To("peterjackson0120@gmail.com");

    $m->Subject($subject);
    $m->Body($message);
    $m->Priority(3) ;
    $m->Send();
    echo "sent email successfully!";
}
function formatDate($date){
    $a = explode("-", $date);
    return $a[2]. "-" .$a[1]. "-" .$a[0]; 
}
function getEmployeeName($emp_id){
    $e = new db("user");
    $e->select("id = :id", false, false, array("id" => $emp_id));
    $e->getRow();
    return array("firstname" => $e->row["firstname"], "lastname" => $e->row["lastname"]);
}
function getUserEmail($emp_id){
    $e = new db("user");
    $e->select("id = :id", false, false, array("id" => $emp_id));
    $e->getRow();
    return $e->row["email"];
}
function getCourseName($course_id){
    $e = new db("course");
    $e->select("course_id = :id", false, false, array("id" => $course_id));
    $e->getRow();
    return $e->row["course_name"];
}
function getUserId($emp_id){
    $e = new db("user");
    $e->select("id = :id", false, false, array("id" => $emp_id));
    $e->getRow();
    return $e->row["id"];
}
function getSiteLocation($emp_id){
    $e = new db("user_work");
    $sql = "SELECT data.display_text FROM data INNER JOIN user_work AS empwk on empwk.site_location = data.id WHERE empwk.user_id = ". $emp_id." ORDER BY empwk.workdate_added DESC LIMIT 1";
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
function getTradingName($emp_id){
    $u = new db('user');
    $u->select('id=:id', false, false, array('id'=>$emp_id));
    $u->getRow();
    $account_id = $u->row['account_id'];
    $u->select('id=:aid', false, false, array('aid'=>$account_id));
    $u->getRow();
    return $u->row['tradingname'];
}

function getEmployer($emp_id){
    $u = new db('user');
    $u->select('id=:id', false, false, array('id'=>$emp_id));
    $u->getRow();
    $account_id = $u->row['account_id'];
    $u->select('id=:aid', false, false, array('aid'=>$account_id));
    $u->getRow();
    return $u->row;
}
function sendEmailForActiveCourse($course_id, $employee_id, $user_id, $expire_hours, $alloc_date) {
    $email_template = "
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
    $file_content = $email_template;
    //var_dump($file_content);
    $file_content = str_replace("<div id='message-content'></div>", $content, $file_content);
    $subject = 'HR Master Training Details';
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "From: " . $company_name . " <" . $company_email . ">\r\n";
    $headers .= "Reply-To: " . $company_email . "\r\n";
    $headers .= "Return-Path: ". $company_email ."\r\n";
    $headers .= "X-Priority: 3\r\n";
    $headers .= "X-Mailer: PHP". phpversion() ."\r\n";
    $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
    //var_dump($file_content);
    //mail($employee_email, $subject, $file_content, $headers);
    mail("peterjackson0120@gmail.com", $subject, $file_content, $headers);
    return $file_content;
}

function sendEmailForOverdueCourses($preferredEmailUsersFirstName, $preferredEmailUserLastname, $preferredEmailUserEmail, $row) {
    $email_template = "
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
        
    $employeeName = getEmployeeName($row["employee_id"]);
    
    $employeeFirstName = $employeeName['firstname'];
    $employeeLastname = $employeeName['lastname'];
    $insertSiteLocaiton = getSiteLocation($row["employee_id"]);
    $companyName = getCompanyName($row["employee_id"]);
    $employer = getEmployer($row["employee_id"]);
    $company_email = $employer['email'];
    $insertdate = $row['extra_hours']!=0 ? $row['extra_expiry_date'] : date_format(date_add(date_create($row["alloc_date"]),date_interval_create_from_date_string($row["expire_hours"]. " hours")), "Y-m-d");
    $insertAllocDate = $course_table_row['alloc_date'];
    $insertCourseName = getCourseName($row["course_id"]);
    $accountIdName = getTradingName($row["employee_id"]);
        
    $content = "<p>Dear ".$preferredEmailUsersFirstName." ".$preferredEmailUserLastname."</p>";
    $content .= "<p>This email is an automated reminder advising you that our human resources database (HRM), has identified that ". $employeeFirstName." ".$employeeLastname." who is working at ".$insertSiteLocaiton.", has a training course called ".$insertCourseName." that is overdue for completion.</p>";
    $content .= "<p>This course was allocated to the ".$employeeFirstName." on ".$insertAllocDate." by the human resources department at ".$accountIdName. " and was due for completion on ".$insertdate.".</p>";
    $content .= "<p>Please ensure you speak to ".$employeeFirstName." ".$employeeLastname." regarding this oversight and advise that their ongoing employment is conditional upon undertaking all company sanctioned training courses.</p><p></p>";
    $content .= "<p>Please do not reply email. This is an automated message and replies are not monitored. Should you wish to unsubscribe, please contact your human resources manager.</p><p></p><p></p>";
    $content .= "<p>Kind Regards.</p>";
    $content .= "<p>System Administrator</p>";
    $content .= "<p>".$companyName."</p>";
    //var_dump($content);
   
    //$file_content = file_get_contents('http://hrmaster.com.au/assets/php/email_templates/alloc_course_email_template.html');
    $file_content = $email_template;
    
    $file_content = str_replace("<div id='message-content'></div>", $content, $file_content);
    $subject = 'Email advising '.$employeeFirstName." ".$employeeLastname.' Training Course is overdue.';
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "From: HR Master Support\r\n";
    $headers .= "Reply-To: " . $company_email . "\r\n";
    $headers .= "Return-Path: ". $company_email ."\r\n";
    $headers .= "X-Priority: 3\r\n";
    $headers .= "X-Mailer: PHP". phpversion() ."\r\n";
    $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
    
    mail($preferredEmailUserEmail, $subject, $file_content, $headers);
}

$var = date("H");
$sql = "";

switch($var){
    case "07":{
        $sql = "SELECT reminders.* FROM reminders INNER JOIN user ON user.id = reminders.employee_id WHERE reminders.alert_status = 1 AND DATE(NOW()) < reminders.alert_expiry AND user.active = 1 AND reminders.email_name = 'WorkCover CoC Due'";
        break;
    }
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
    case "19":{
        $sql = "SELECT reminders.* FROM reminders INNER JOIN user ON user.id = reminders.employee_id WHERE reminders.alert_status = 1 AND DATE(NOW()) < reminders.alert_expiry AND user.active = 1 AND reminders.email_name = 'Performance Review'";
        break;
    }
}
if($sql!=""){
    $rm_table = new db("reminders");
    $rm_table->select(false, false, $sql);
    $reminders = array();
    while($rm_table->getRow()){
        $row = $rm_table->row;
        $preferred_employee_name = getEmployeeName($row["employee_id"]);
        switch($row["email_name"]){
            case "Birthday": {
                $bd_table = new db("user");
                $bd_table->select("account_id = :id AND deleted = :notdel AND active=:isactive", false, false, array("id" => $row["account_id"], "notdel" => 0, "isactive"=>1));
                while($bd_table->getRow()){
                    $employee_row = $bd_table->row;
                    if($employee_row['dob']!=NULL){
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
                }
                break;
            }
            case "VISA Check": {
                $bd_table = new db("user");
                $bd_table->select("account_id = :id  AND deleted = :notdel AND active=:isactive", false, false, array("id" => $row["account_id"], "notdel" => 0, "isactive"=>1));
                while($bd_table->getRow()){
                    $employee_row = $bd_table->row;
                    $array_text_exchanged = array();
                    $array_text_exchanged["<\$preferredEmailUsersFirstName>"] = $preferred_employee_name["firstname"];
                    $array_text_exchanged["<\$preferredEmailUserLastname>"] = $preferred_employee_name["lastname"];
                    $array_text_exchanged["<\$employeeFirstName>"] = $employee_row["firstname"];
                    $array_text_exchanged["<\$employeeLastname>"] = $employee_row["lastname"];
                    $array_text_exchanged["<\$insertSiteLocaiton>"] = getSiteLocation($employee_row["id"]);
                    $array_text_exchanged["<\$companyName>"] = getCompanyName($employee_row["id"]);
                    $array_text_exchanged["<\$insertdate>"] = $employee_row["visaexpiry"];
                    email($employee_row["visaexpiry"], $row["period"], $row["days_prior"], $row["email_con"], $array_text_exchanged, $row["reminder_email"]);
                }
                break;
            }
            case "Training Course Due": {
                $course_table = new db("alloc_course");
                $today = date('Y-m-d H:i:s');
                $tomorrow = date('Y-m-d H:i:s', strtotime('+1 day', strtotime($today)));
                $sql = "SELECT ac.* 
                        FROM alloc_course ac 
                        JOIN user u ON u.id=ac.employee_id 
                        WHERE u.active=1 AND 
                                u.deleted=0 AND ac.user_id=".$row['account_id'].
                                " AND (DATEDIFF(DATE_ADD(ac.alloc_date, INTERVAL ac.expire_hours HOUR), '".$today."')=".$row['days_prior']." OR ('".$today."'>ac.extra_expiry_date AND CAST(ac.extra_expiry_date AS DATE)='".date('Y-m-d', strtotime($today))."'))";
                $course_table->select(false, false, $sql);
                while($course_table->getRow()){
                    $course_table_row = $course_table->row;
                    $employee_name = getEmployeeName($course_table_row["employee_id"]);
                    $array_text_exchanged = array();
                    $array_text_exchanged["<\$preferredEmailUsersFirstName>"] = $preferred_employee_name["firstname"];
                    $array_text_exchanged["<\$preferredEmailUserLastname>"] = $preferred_employee_name["lastname"];
                    $array_text_exchanged["<\$employeeFirstName>"] = $employee_name["firstname"];
                    $array_text_exchanged["<\$employeeLastname>"] = $employee_name["lastname"];
                    $array_text_exchanged["<\$insertSiteLocaiton>"] = getSiteLocation($course_table_row["employee_id"]);
                    $array_text_exchanged["<\$companyName>"] = getCompanyName($course_table_row["employee_id"]);
                    $array_text_exchanged["<\$insertDueDate>"] = $course_table_row['extra_hours']!=0 ? $course_table_row['extra_expiry_date'] : date_format(date_add(date_create($course_table_row["alloc_date"]),date_interval_create_from_date_string($course_table_row["expire_hours"]. " hours")), "Y-m-d");
                    $array_text_exchanged["<\$insertAllocDate>"] = $course_table_row['alloc_date'];
                    
                    $array_text_exchanged["<\$insertCourseName>"] = getCourseName($course_table_row["course_id"]);
                    $array_text_exchanged["<\$accountIdName>"] = getTradingName($course_table_row["employee_id"]);
                    //var_dump($array_text_exchanged);
                    email($array_text_exchanged["<\$insertDueDate>"], $row["period"], $row["days_prior"], $row["email_con"], $array_text_exchanged, $row['reminder_email']);
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
                    $array_text_exchanged["<\$insertSiteLocaiton>"] = getSiteLocation($substance_row["supplier_id"]);
                    $array_text_exchanged["<\$companyName>"] = getCompanyName($substance_row["supplier_id"]);
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
                    $array_text_exchanged["<\$insertSiteLocaiton>"] = getSiteLocation($asset_row["site_location_id"]);
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
                    $array_text_exchanged["<\$insertSiteLocaiton>"] = getSiteLocation($asset_row["site_location_id"]);
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
                $sql = "SELECT ul.* FROM user_license ul JOIN user u ON u.id=ul.user_id WHERE u.active=1 AND u.deleted=0 AND ul.account_id".$row['account_id'];
                $table->select(false, false, $sql);
                
                while($table->getRow()){
                    $license_row = $table->row;
                    
                    $array_text_exchanged = array();
                    $array_text_exchanged["<\$preferredEmailUsersFirstName>"] = $preferred_employee_name["firstname"];
                    $array_text_exchanged["<\$preferredEmailUserLastname>"] = $preferred_employee_name["lastname"];
                    $array_text_exchanged["<\$employeeFirstName>"] = $license_row["user_firstname"];
                    $array_text_exchanged["<\$employeeLastname>"] = $license_row["user_lastname"];
                    $array_text_exchanged["<\$insertSiteLocaiton>"] = getSiteLocation($license_row["user_id"]);
                    $array_text_exchanged["<\$companyName>"] = getCompanyName($license_row["user_id"]);
                    $array_text_exchanged["<\$insertdate>"] = $license_row["date_expire"];
                    $array_text_exchanged["<\$licenseQualificationName>"] = $license_row["license_name"];
                    //var_dump($array_text_exchanged);
                    email($license_row["date_expire"], $row["period"], $row["days_prior"], $row["email_con"], $array_text_exchanged, $row["reminder_email"]);
                }
                break;
            }
            case "Performance Review": {
                $table = new db("performance_forms");
                $sql = "SELECT pf.*, fr.* FROM performance_forms pf JOIN form_reviews fr ON pf.id=fr.p_forms_id JOIN user u ON u.id=pf.employee_id WHERE u.active=1 AND u.deleted=0 AND pf.account_id=".$row['account_id']." AND fr.form_status='pending'";
                $table->select(false, false, $sql);
                
                while($table->getRow()){
                    $license_row = $table->row;
                    $employee_name = getEmployeeName($license_row["employee_id"]);
                    $array_text_exchanged = array();
                    $array_text_exchanged["<\$preferredEmailUsersFirstName>"] = $preferred_employee_name["firstname"];
                    $array_text_exchanged["<\$preferredEmailUserLastname>"] = $preferred_employee_name["lastname"];
                    $array_text_exchanged["<\$employeeFirstName>"] = $employee_name["firstname"];
                    $array_text_exchanged["<\$employeeLastname>"] = $employee_name["lastname"];
                    $array_text_exchanged["<\$insertSiteLocaiton>"] = getSiteLocation($license_row["employee_id"]);
                    $array_text_exchanged["<\$companyName>"] = getCompanyName($license_row["employee_id"]);
                    $array_text_exchanged["<\$insertdate>"] = $license_row["assessment_date"];
                    email($license_row["assessment_date"], $row["period"], $row["days_prior"], $row["email_con"], $array_text_exchanged, $row["reminder_email"]);
                }
                break;
            }
            case "Probation Period": {
                $table = new db("user");
                $table->select("account_id = :id AND deleted = :notdel AND active=:isactive", false, false, array("id" => $row["account_id"], "notdel" => 0, "isactive"=>1));
                
                while($table->getRow()){
                    $employee_row = $table->row;
                    $employee_work = new db("user_work");
                    $sql = "SELECT * from user_work where user_id=".$employee_row['id']." ORDER BY workdate_added DESC LIMIT 1";
                    $employee_work->select(false, false, $sql);
                    $employee_work->getRow();
                    $employee_work_row = $employee_work->row;
                    
                    $array_text_exchanged = array();
                    $array_text_exchanged["<\$preferredEmailUsersFirstName>"] = $preferred_employee_name["firstname"];
                    $array_text_exchanged["<\$preferredEmailUserLastname>"] = $preferred_employee_name["lastname"];
                    $array_text_exchanged["<\$employeeFirstName>"] = $employee_row["firstname"];
                    $array_text_exchanged["<\$employeeLastname>"] = $employee_row["lastname"];
                    $array_text_exchanged["<\$insertSiteLocaiton>"] = getSiteLocation($employee_work_row["user_id"]);
                    $array_text_exchanged["<\$companyName>"] = getCompanyName($employee_work_row["user_id"]);
                    $array_text_exchanged["<\$insertdate>"] = date_add(date_create($employee_work_row["start_date"]), date_interval_create_from_date_string("90 days"));// 3 months
                    
                    email($array_text_exchanged["<\$insertdate>"], $row["period"], $row["days_prior"], $row["email_con"], $array_text_exchanged, $row["reminder_email"]);
                }
                break;
            }
            case "Qualification Period": {
                $table = new db("user");
                $table->select("account_id = :id AND deleted = :notdel AND active=:isactive", false, false, array("id" => $row["account_id"], "notdel" => 0, "isactive"=>1));
                
                while($table->getRow()){
                    $employee_row = $table->row;
                    $employee_work = new db("user_work");
                   $sql = "SELECT * from user_work where user_id=".$employee_row['id']." ORDER BY workdate_added DESC LIMIT 1";
                    $employee_work->select(false, false, $sql);
                    $employee_work->getRow();
                    $employee_work_row = $employee_work->row;
                    
                    
                    $array_text_exchanged = array();
                    $array_text_exchanged["<\$preferredEmailUsersFirstName>"] = $preferred_employee_name["firstname"];
                    $array_text_exchanged["<\$preferredEmailUserLastname>"] = $preferred_employee_name["lastname"];
                    $array_text_exchanged["<\$employeeFirstName>"] = $employee_row["firstname"];
                    $array_text_exchanged["<\$employeeLastname>"] = $employee_row["lastname"];
                    $array_text_exchanged["<\$insertSiteLocaiton>"] = getSiteLocation($employee_work_row["user_id"]);
                    $array_text_exchanged["<\$companyName>"] = getCompanyName($employee_work_row["user_id"]);
                    $array_text_exchanged["<\$insertdate>"] = date_add(date_create($employee_work_row["start_date"]), date_interval_create_from_date_string("180 days"));// 6 months
                    
                    email($array_text_exchanged["<\$insertdate>"], $row["period"], $row["days_prior"], $row["email_con"], $array_text_exchanged, $row["reminder_email"]);
                }
                break;
            }
            case "Injury Register": {
                $table = new db("injury_register");
                $sql = "SELECT ir.* FROM injury_register ir JOIN user u ON u.id=ir.employee_id WHERE u.active=1 AND u.deleted=0 AND ir.deleted=0 AND ir.account_id=".$row['account_id'];
                $table->select(false, false, $sql);
                
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
                    $array_text_exchanged["<\$preferredEmailUsersLastname>"] = $preferred_employee_name["lastname"];
                    $array_text_exchanged["<\$employeeFirstName>"] = $employee_name["firstname"];
                    $array_text_exchanged["<\$employeeLastname>"] = $employee_name["lastname"];
                    $array_text_exchanged["<\$insertSiteLocaiton>"] = getSiteLocation($injury_register_row["employee_id"]);
                    $array_text_exchanged["<\$companyName>"] = getCompanyName($injury_register_row["employee_id"]);
                    $array_text_exchanged["<\$insertdate>"] = $injury_register_row["incident_date"]; //incident_date
                    $array_text_exchanged["<\$likelihood>"] = $likelihood_list[$injury_register_row["risk_likelihood"] / 1];
                    $array_text_exchanged["<\$severity>"] = $severity_list[$injury_register_row["level_of_risk"] / 1];
                    $period = explode(" ", $injury_register_row["email_frequency"]);
                    email($array_text_exchanged["<\$insertdate>"], $period[2], 0, $row["email_con"], $array_text_exchanged, $row["reminder_email"]);
                }
                break;
            }
            case "WorkCover CoC Due":{
                
                $table = new db("injury_register");
                $sql = "SELECT ir.* FROM injury_register ir JOIN user u ON u.id=ir.employee_id WHERE u.active=1 AND u.deleted=0 AND ir.deleted=0 AND ir.insurer_notified=1 AND ir.account_id=".$row['account_id'];
                $table->select(false, false, $sql);
                
                while($table->getRow()){
                    $injury_register_row = $table->row;
                    $employee_name = getEmployeeName($injury_register_row["employee_id"]);
                    
                    $array_text_exchanged = array();
                    $array_text_exchanged["<\$preferredEmailUsersFirstName>"] = $preferred_employee_name["firstname"];
                    $array_text_exchanged["<\$preferredEmailUsersLastName>"] = $preferred_employee_name["lastname"];
                    $array_text_exchanged["<\$employeeFirstName>"] = $employee_name["firstname"];
                    $array_text_exchanged["<\$employeeLastname>"] = $employee_name["lastname"];
                    $array_text_exchanged["<\$insertSiteLocaiton>"] = getSiteLocation($injury_register_row["employee_id"]);
                    $array_text_exchanged["<\$companyName>"] = getCompanyName($injury_register_row["employee_id"]);
                    $array_text_exchanged["<\$injurydate>"] = $injury_register_row["incident_date"]; //incident_date
                    $array_text_exchanged["<\$insertCoCdate>"] = $injury_register_row["workcover_date"]; //incident_date
                    $array_text_exchanged["<\$severity>"] = $severity_list[$injury_register_row["level_of_risk"] / 1];
                    email($array_text_exchanged["<\$insertCoCdate>"], $row['period'], $row["days_prior"], $row["email_con"], $array_text_exchanged, $row["reminder_email"]);
                }
                break;
            }
        }
        //array_push($reminders, $rm_table->row);
    }
}
/* send email when course is active(now==alloc_date)*/
if($var=="10"){
    $today = date('Y-m-d H:i:s');
    $tomorrow = date('Y-m-d H:i:s', strtotime('+1 day', strtotime($today)));
    $alloc_course = new db("alloc_course");
    $sql = "SELECT * FROM alloc_course ac JOIN course c ON c.course_id=ac.course_id WHERE '".date('Y-m-d', strtotime($today))."'=ac.alloc_date AND ac.is_sending_email=1 AND c.deleted=0";
    $alloc_course->select(false, false, $sql);
    $active_courses = array();
    while($alloc_course->getRow()){
        $row = $alloc_course->row;
        array_push($active_courses, $row);
        sendEmailForActiveCourse($row['course_id'], $row['employee_id'], $row['user_id'], $row['expire_hours'], $row['alloc_date']);
    }
    
    /* check overdue courses for alloc course reminder settings*/
    $sql = "SELECT ac.*, ac.course_supervisor, u.email, ac.daily_reminder_email_receiver
                        FROM alloc_course ac 
                        JOIN user u ON u.id=ac.employee_id 
                        WHERE u.active=1 AND 
                                u.deleted=0 AND 
                                (DATEDIFF(DATE_ADD(ac.alloc_date, INTERVAL ac.expire_hours HOUR), '".$today."')=0 OR ('".$today."'>ac.extra_expiry_date AND ac.daily_reminder_email_receiver IS NOT NULL))";

    $alloc_course->select(false, false, $sql);
    while($alloc_course->getRow()){
        $row = $alloc_course->row;
        
        $receiver = $row['daily_reminder_email_receiver'];
        $employee_name = getEmployeeName($row["employee_id"]);
        $supervisorId = $row['course_supervisor'];
        $supervisor_email = getUserEmail($supervisorId);
        $supervisor_name = getEmployeeName($supervisorId);
        $employer = getEmployer($row["employee_id"]);
        $employer_email = $employer['email'];
        //var_dump($receiver);
        if($receiver=="employee"){
            $preferredEmailUsersFirstName = $employee_name["firstname"];
            $preferredEmailUserLastname = $employee_name["lastname"];
            $preferredEmailUserEmail = $row['email'];
            sendEmailForOverdueCourses($preferredEmailUsersFirstName, $preferredEmailUserLastname, $preferredEmailUserEmail, $row);
        }else if($receiver=="supervisor"){
            $preferredEmailUsersFirstName = $supervisor_name["firstname"];
            $preferredEmailUserLastname = $supervisor_name["lastname"];
            $preferredEmailUserEmail = $supervisor_email;
            sendEmailForOverdueCourses($preferredEmailUsersFirstName, $preferredEmailUserLastname, $preferredEmailUserEmail, $row);
        }else if($receiver=="employee_supervisor"){
            $preferredEmailUsersFirstName = $employee_name["firstname"];
            $preferredEmailUserLastname = $employee_name["lastname"];
            $preferredEmailUserEmail = $row['email'];
            sendEmailForOverdueCourses($preferredEmailUsersFirstName, $preferredEmailUserLastname, $preferredEmailUserEmail, $row);
            $preferredEmailUsersFirstName = $supervisor_name["firstname"];
            $preferredEmailUserLastname = $supervisor_name["lastname"];
            $preferredEmailUserEmail = $supervisor_email;
            sendEmailForOverdueCourses($preferredEmailUsersFirstName, $preferredEmailUserLastname, $preferredEmailUserEmail, $row);
        }else if($receiver=="employee_employer"){
            $preferredEmailUsersFirstName = $employee_name["firstname"];
            $preferredEmailUserLastname = $employee_name["lastname"];
            $preferredEmailUserEmail = $row['email'];
            sendEmailForOverdueCourses($preferredEmailUsersFirstName, $preferredEmailUserLastname, $preferredEmailUserEmail, $row);
            $preferredEmailUsersFirstName = $employer["firstname"];
            $preferredEmailUserLastname = $employer["lastname"];
            $preferredEmailUserEmail = $employer_email;
            sendEmailForOverdueCourses($preferredEmailUsersFirstName, $preferredEmailUserLastname, $preferredEmailUserEmail, $row);
        }
    }
    exit;
}
?>