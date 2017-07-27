<?php 

class userHelper {

	function __construct($apps){
		global $logger;
		$this->logger = $logger;
		$this->apps = $apps;
		$this->fbID = 0;
		
		if($this->apps->isUserOnline())  {
			if(is_object($this->apps->user)){
				$this->fbID = $this->apps->user->id;	
			}
		}
	}

	function getUserProfile(){
		if($this->fbID==0){
			return false;
		}else{
			$sql="SELECT * FROM hnk_member WHERE fb_id={$this->fbID} LIMIT 1";
			$rs = $this->apps->fetch($sql);

			$sql="SELECT * FROM hnk_admin WHERE admin='{$rs['email']}' AND n_status=1 LIMIT 1";
			$admin= $this->apps->fetch($sql);
			
			if($rs['personal_type']!=0){
				$sql="SELECT * FROM hnk_user_playing_logs
						WHERE fb_id={$this->fbID} ORDER BY submit_date DESC LIMIT 1";
				$upl=$this->apps->fetch($sql);
				if($upl){
					$sql="SELECT * FROM hnk_user_type
							WHERE id={$upl['answer_type']} LIMIT 1";
					$pt = $this->apps->fetch($sql);
					if($pt) {
						$rs['personal_type'] = strtoupper($pt['personality_type']);
						$rs['personal_desc'] = utf8_encode($pt['personality_desc']);
					}
				}
			}

			if($admin){
				$rs['isAdmin'] = 1;
			}

			return $rs;
		}
	}
	
	function changeProfile($phone=null,$email=null){
		if($phone) $set = "SET phone={$phone}";
		if($email) $set = "SET active_email='{$email}'";
		$sql = "UPDATE hnk_member 
				{$set}
				WHERE fb_id={$this->fbID} LIMIT 1";
				$qData = $this->apps->query($sql);
		if ($qData) return array('status'=>1,'message'=>'Updated');
		return array('status'=>0,'message'=>'Failed');
	}

}

?>

