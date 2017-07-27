<?php
class sendInboxMail_service  extends ServiceAPI{
			

	function beforeFilter(){
		
		$this->contentHelper = $this->useHelper('contentHelper');
		$this->activityHelper = $this->useHelper('activityHelper');
		$this->newsHelper = $this->useHelper('newsHelper');
	}
	
	function doit(){
		global $CONFIG;	
		$uidArr = $this->newsHelper->getNewsLetterScheadule();
		if(!$uidArr) return false;
		
		foreach($uidArr as $val){
			$data[] = $this->newsHelper->setMessageForMail(0,10,false,true,$val);			
			sleep(3);
		}
		pr($data);
		exit;
		return $data;
	
	}
	
	function getAccessToken(){
		global $CONFIG;
		
		$at = get_access_token($this->user->id,$CONFIG['SERVICE_KEY']);
		return  $this->apps->toJson(1,'access',$at);
	}
	
		
}
?>
