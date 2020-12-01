<?php
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

$servername = "localhost";
$username = "hrmaster_admin";
$password = "Password123";
$database = "hrmaster_hrmaster";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

date_default_timezone_set("Australia/Sydney");

$data = array();
$sql = "SELECT * FROM alloc_event WHERE DATEDIFF(startsAt, NOW())=3";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        array_push($data, $row);
    }
}

if(count($data)>0){
    foreach($data as $one){
        $emps = json_decode($one['user_id']);
        foreach($emps as $emp){
            $sql = "SELECT u.*, uw.*, d.display_text as location_text FROM user u JOIN user_work uw ON u.id=uw.user_id JOIN data d ON uw.site_location=d.id WHERE u.id=".$emp." ORDER BY workdate_added DESC LIMIT 1";
            $result = $conn->query($sql);
            $row = $result->fetch_assoc();
            $employee_name = $row['firstname']." ".$row['lastname'];
            $employee_email = $row['work_email'];
            if($employee_email=="") $employee_email = $row['email'];
            $location = $row['location_text'];
            $site_location = $row['site_location'];
            $report_to = $row['report_to'];
            $startsAt = date('d-m-Y h.i A', strtotime($one['startsAt']));
            $endsAt = date('d-m-Y h.i A', strtotime($one['endsAt']));
            $alloc_date = date('d-m-Y h.i.s A', strtotime($one['alloc_date']));
            $alloc_date_date = explode(" ", $alloc_date)[0];
            $alloc_date_time = explode(" ", $alloc_date)[1]." ".explode(" ", $alloc_date)[2];
            $event_id = $one['event_id'];
            
            $sql = "SELECT *, d.display_text as state_name FROM events e JOIN data d ON e.state=d.id WHERE e.id=".$event_id;
            $result = $conn->query($sql);
            $event = $result->fetch_assoc();
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
            
            $subject = 'HR Master Training Details';
            $headers = "MIME-Version: 1.0\r\n";
            $headers .= "From: HR Master Support <support@hrmaster.com.au>\r\n";
            $headers .= "Reply-To: <support@hrmaster.com.au>\r\n";
            $headers .= "Return-Path: <support@hrmaster.com.au>\r\n";
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
            mail("dberlusconi@aag.com.au", $subject, $file_content1, $headers);
            
        /*          To manager----------------------------------------------------------------------------------------------------------------------------------------------------------------------*/
        
            if($report_to!=0){
                $sql = "SELECT u.email, CONCAT(u.firstname, ' ', u.lastname) as manager_name, uw.work_email 
                        FROM user u 
                        JOIN user_work uw ON u.id=uw.user_id 
                        WHERE (u.usertype_id=18 OR u.usertype_id=575) AND u.active=1 AND uw.site_location=".$site_location." AND uw.position=".$report_to." 
                        ORDER BY workdate_added 
                        DESC LIMIT 1";
                $result = $conn->query($sql);
                $row = $result->fetch_assoc();
                
                $manager_name = $row['manager_name'];
                $manager_email = $row['work_email'];
                if($manager_email=="")$manager_email = $row['email'];
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
                mail($trainerEmail, $subject, $file_content2, $headers);
            }   
        /*          ----------------------------------------------------------------------------------------------------------------------------------------------------------------------*/
        }
    }
}
?>