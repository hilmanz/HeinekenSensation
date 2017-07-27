<?php 

class apnsHelper  {

	function __construct($apps){
		global $logger;
		$this->logger = $logger;
		$this->apps = $apps;
		if(is_object($this->apps->user)) $this->uid = intval($this->apps->user->id);

		$this->dbschema = "beat";	
		$this->radius = 100 / 10000;
		$this->dbshema = "beat";
	}
	
	function curlJSON($url,$data,$uname,$pwd){
	$data_string = json_encode($data);
	// pr($data_string);exit;
	$ch = curl_init($url);                                                                      
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                                                  
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);    
	curl_setopt($ch, CURLOPT_USERPWD, "$uname:$pwd");		
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
	    'Content-Type: application/json',                                                                                
	    'Content-Length: ' . strlen($data_string))                                                                       
	);                                                                                                                   
	$result = curl_exec($ch);
	$info = curl_getinfo($ch);
	curl_close($ch);
	return array("response"=>$result,"info"=>$info);
	}

	function pushnotification(){
			// '{"device_tokens": ["<token>"], "aps": {"alert": "Hello!"}}' \
			// App Key : ZitLOOKcQKC3xhaMiMM-kA
			// App Secret : HPrj3kNLQnu0PJleNwppEQ
			// App Master Secret : Hlv0MWdDSDuzxPYhPyShpw
			
					
			$uname = "ZitLOOKcQKC3xhaMiMM-kA";
			$pwd = "Hlv0MWdDSDuzxPYhPyShpw";
			$notification = $this->apps->activityHelper->getA360activity(0,10,false,false,false,'3',true);	
			$data['device_tokens'] = strip_tags($this->apps->_request("devicetoken"));
			$data['aps'] = $notification['content'];
			$url = "https://go.urbanairship.com/api/push/";
			
			$sql ="INSERT INTO tbl_device_token_apns (device_token,userid, datetimes) VALUES ('{$data['device_tokens'] }',{$this->uid},NOW()) ";
			$this->apps->query($sql);
			return $this->curlJSON($url,$data,$uname,$pwd);
			
			
	}
	
	function pushnotification_bot(){
			$uname = "ZitLOOKcQKC3xhaMiMM-kA";
			$pwd = "Hlv0MWdDSDuzxPYhPyShpw";
			$url = "https://go.urbanairship.com/api/push/";
			$sql =" 
			SELECT device_token,userid FROM tbl_device_token_apns 
			WHERE NOT EXISTS 
			( 	SELECT * FROM my_news_letter 
				WHERE my_news_letter.userid=tbl_device_token_apns.userid 
				AND n_status = 1 
			) ";
			
			$qData = $this->apps->fetch($sql,1);
			
			if(!$qData)return false;
			$result = false;
			foreach($qData as $val){
				
				$this->apps->Request->setParam('uid',$val['userid']);
				$notification = $this->apps->activityHelper->getA360activity(0,10,false,false,false,'3',true);	
				$data['device_tokens'] =$val['device_token'];
				$data['aps'] = $notification['content'];
				
				$res= $this->curlJSON($url,$data,$uname,$pwd);
				$result[] = $res;
			}		
			pr($result);
			exit;
			
			
	}
	
	
	function blocknotification(){
			$sql ="INSERT INTO my_news_letter (userid,n_status) VALUES ({$this->uid},1) 
			ON DUPCLICATE KEY UPDATE n_status = 1;
			";
			$qData = $this->apps->query($sql);
			return true;
	}
	
	function unblocknotification(){
			$sql ="
			INSERT INTO my_news_letter (userid,n_status) VALUES ({$this->uid},0) 
			ON DUPCLICATE KEY UPDATE n_status = 0;
			";
			$qData = $this->apps->query($sql);
			return true;
	}
	
}

?>

