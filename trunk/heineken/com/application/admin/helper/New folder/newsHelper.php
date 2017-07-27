<?php

class newsHelper {
	
	
	function __construct($apps){
	global $logger,$CONFIG;
	$this->logger = $logger;
	$this->apps = $apps;
	if($this->apps->isUserOnline())  {
			if(is_object($this->apps->user)) $this->uid = intval($this->apps->user->id);
	}
	else $this->uid = 0;
	$this->dbshema = "athreesix";
	}
	
	function getInboxUser($start=0,$limit=10){
		global $LOCALE;
		
		/*
			$LOCALE[1]['add friends']['inbox'] = " sekarang follow kamu";
			$LOCALE[1]['add comments']['inbox'] = " telah memberikan komentar di postinganmu";
			$LOCALE[1]['add favorite']['inbox'] = " telah menambahkan postinganmu sebagai favoritnya";
			$LOCALE[1]['add new thread']['inbox'] = " telah memberikan komentar di thread kamu";
		*/
		$activityIDarr = false;
		$theactivity = false;
		$qData = false;
		$inboxActivity = "'add comments','add favorite','add new thread'";
		$socialActivity = "'add friends'";
		

		$theactivity = $this->apps->activityHelper->getactivitytype($inboxActivity);		
		if($theactivity) {
			/* get article of user */
			$inboxActivityID = implode(',',$theactivity['id']);
		}else $inboxActivityID = false;
		
		/* get activity social id */	
		$socialactivitydata = $this->apps->activityHelper->getactivitytype($socialActivity);
		if($socialactivitydata) {
			$socialActivityID = implode(',',$socialactivitydata['id']);
		}else $socialActivityID = false;
		
		$activityIDarr = array($inboxActivityID,$socialActivityID);
		
		if($activityIDarr) {
		
				$activityId = implode(',',$activityIDarr);
		
				/* get article of user */
				$sql = "SELECT id FROM {$this->dbshema}_news_content WHERE authorid = {$this->uid} AND n_status = 1 AND fromwho = 1 LIMIT 100 ";
				$qData = $this->apps->fetch($sql,1);
				
				if($qData){
					foreach($qData as $val){
						$articleData[] = $val['id'];
					}
					if($articleData){
						$thearticles = implode(',',$articleData);
					}else  $thearticles = false ;
				}else $thearticles = false ;
				
				/* if not user notif, to prevent un-categories */
				if($socialActivityID) $appendConditionalQ = " AND IF (action_id IN {$socialActivityID},action_value={$this->uid},action_value IN ({$thearticles}) ) ";
				else $appendConditionalQ = " AND action_value IN ({$thearticles}) ";
				
				/* query all notif for this use */
				$sql = " 
					SELECT * , IF ( action_id IN {$socialActivityID} , 'social' , 'content' ) typeofnotification
					FROM tbl_activity_log 
					WHERE 
						action_id IN ({$activityId}) 
					{$appendConditionalQ}
					AND n_status = 0
					LIMIT {$start},{$limit}";
				// pr($sql);
				$qData = $this->apps->fetch($sql,1);
				if(!$qData) return false;
				
				$socialData = false;
				$contentData = false;
				
				foreach($qData as $key => $val){
					$actionid[$val['action_id']] = $val['action_id'];
					/* parse between id article and user */
					if($val['typeofnotification'] == 'social' )$socialData[] = $val['action_value'];
					else $contentData[] = $val['action_value'];
					
					$subjectData[] =  $val['user_id'];
				}
				if($socialData)$arrUserid= $this->apps->activityHelper->getsocialData($socialData);
				else $arrUserid = false;
				if($contentData) $arrContent = $this->apps->activityHelper->getContentData($contentData);
				else $arrContent = false;
				if($subjectData) $arrSubject = $this->apps->activityHelper->getsocialData($subjectData);
				else $arrSubject = false;
				
				if($actionid){
					$actions = implode(',',$actionid);
					$actionnamedata =  $this->apps->activityHelper->getactivitytype($actions,true);			
				}else $actionnamedata = false;
				// pr($actionnamedata);
				if($actionnamedata){
					foreach($qData as $key => $val){
						if(array_key_exists($val['action_id'],$actionnamedata['name'])) $qData[$key]['activityName'] = $LOCALE[1][$actionnamedata['name'][$val['action_id']]]['inbox'];
						else  $qData[$key]['activityName'] = false;
						if($val['typeofnotification'] == 'social' ){
							if($arrUserid){	
								if(array_key_exists($val['action_value'],$arrUserid))$qData[$key]['userdetail'] = $arrUserid[$val['action_value']];	
								else $qData[$key]['userdetail'] = false;
							}else $qData[$key]['userdetail'] = false;
						}else{
							if($arrContent ){
								if(array_key_exists($val['action_value'],$arrContent)) $qData[$key]['contentdetail'] = $arrContent[$val['action_value']];	
								else $qData[$key]['contentdetail'] = false;
							}else $qData[$key]['contentdetail'] = false;
						}
						if($arrSubject){	
								if(array_key_exists($val['user_id'],$arrSubject))$qData[$key]['userdetail'] = $arrSubject[$val['user_id']];	
								else $qData[$key]['userdetail'] = false;
						}else $qData[$key]['userdetail'] = false;
						
					}
				}
				return $qData;
		}
		return false;
		
		
	}
	
	function trashInbox($id=null){
		if($id == null) return false;
		$id = strip_tags($this->apps->_p('noteid'));
		$sql = "UPDATE tbl_activity_log SET n_status = 1 WHERE id IN ({$id}) ;";

		$qData = $this->apps->query($sql);
		if($qData) return true;
		else return false;
	}
	
	
	function sendMessage(){
		global $CONFIG;
		
		$name 	 	= mysql_escape_string(cleanXSS(strip_tags($this->apps->_p('txtName'))));
		$email		= mysql_escape_string(cleanXSS(strip_tags($this->apps->_p('txtEmail'))));
		$propinsi	= mysql_escape_string(cleanXSS(strip_tags($this->apps->_p('txtPropinisi'))));
		$telepon	= mysql_escape_string(cleanXSS(strip_tags($this->apps->_p('txtTelepon'))));
		$kota		= mysql_escape_string(cleanXSS(strip_tags($this->apps->_p('txtKota'))));
		$tipe		= mysql_escape_string(cleanXSS(strip_tags($this->apps->_p('txtTipe'))));
		$message	= mysql_escape_string(cleanXSS(strip_tags($this->apps->_p('txtMessage'))));
		$checked 	= mysql_escape_string(cleanXSS(strip_tags($this->apps->_p('checkTXT'))));
		
		if($name == '') return array('message'=>'please fill form','result'=>false);
		if($email == '') return array('message'=>'please fill form','result'=>false);
		if($propinsi == '') return array('message'=>'please fill form','result'=>false);
		if($telepon == '') return array('message'=>'please fill form','result'=>false);
		if($kota == '') return array('message'=>'please fill form','result'=>false);
		if($tipe == '') return array('message'=>'please fill form','result'=>false);
		if($message == '') return array('message'=>'please fill form','result'=>false);
		// if($checked == '') return array('message'=>'please fill form','result'=>false);
		if($checked=='on')$checked=1;
		else $checked=0;
		
		$sql ="INSERT INTO axis_message (nama, 	userid ,	email 	,propinsi 	,kota ,	tipe ,telepon ,	message ,	date_time, 	n_status,syarat) 
		VALUES ('{$name}',{$this->uid},'{$email}','{$propinsi}','{$kota}','{$tipe}','{$telepon}','{$message}',NOW(),0,'{$checked}')
		" ;
			$this->logger->log($sql);
		$this->apps->query($sql);
		if($this->apps->getLastInsertId()){
			
			$to= $CONFIG['EMAIL_AXIS']['web'];
			$from = $email;
			$msg = $message;
			$result = $this->sendGlobalMail($name,$to,$from,$msg, $telepon, $checked,$propinsi,$kota,$tipe);
			$this->logger->log($result['message']);
			if($result['result']){
				$sql = "UPDATE axis_message SET n_status=1 WHERE id={$this->apps->getLastInsertId()} LIMIT 1";
				$this->apps->query($sql);
				return $result;
			}else return $result;
			
		}
		return false;
	}
	
	function sendGlobalMail($name,$to,$from,$msg,$telepon,$checked,$propinsi,$kota,$tipe){
		GLOBAL $ENGINE_PATH, $CONFIG, $LOCALE;
		require_once $ENGINE_PATH."Utility/PHPMailer/class.phpmailer.php";
		
		//get Province
		$prov = "SELECT province FROM axis_province_reference WHERE id={$propinsi} LIMIT 1";
		$province = $this->apps->fetch($prov);
		
		//get City
		$city = "SELECT city FROM axis_city_reference WHERE id={$kota} LIMIT 1";
		$cityName = $this->apps->fetch($city);
		
		$mail = new PHPMailer();
		
		$mail->IsSMTP();                                      // set mailer to use SMTP
		$mail->Host = $CONFIG['EMAIL_SMTP_HOST'];  // specify main and backup server
		$mail->SMTPAuth = true;     // turn on SMTP authentication
		$mail->Username = $CONFIG['EMAIL_SMTP_USER'];  // SMTP username
		$mail->Password = $CONFIG['EMAIL_SMTP_PASSWORD']; // SMTP password

		$mail->From = $from;
		$mail->FromName = $from;
		$mail->AddAddress($to);

		$mail->WordWrap = 50;                                 // set word wrap to 50 characters
		$mail->IsHTML(true);                                  // set email format to HTML

		$mail->Subject = '[Web Feedback] '.$telepon.' '.$from.' '.date("d/m/Y h:i:s A");
		if($checked==1){
			$mail->Body    = 'Name: '.$name.'<br />Email: '.$from.'<br /> Phone Number: '.$telepon.'<br /> Province: '.$province['province'].'<br /> City: '.$cityName['city'].'<br /> Question type: '.$tipe.'<br /> Message: '.$msg.'<br /><br />Saya bersedia dihubungi melalui telepon';
		}else{
			$mail->Body    = 'Name: '.$name.'<br />Email: '.$from.'<br /> Phone Number: '.$telepon.'<br /> Province: '.$province['province'].'<br /> City: '.$cityName['city'].'<br /> Question type: '.$tipe.'<br /> Message: '.$msg;
		}
		$mail->AltBody = $msg;
		$result = $mail->Send();
	
		if($result) return array('message'=>'success send mail','result'=>true);
		else return array('message'=>'error mail setting','result'=>false);
	}
	
	
	
}