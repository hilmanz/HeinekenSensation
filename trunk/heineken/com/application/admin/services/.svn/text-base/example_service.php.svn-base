<?php
class example_service {
			
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
	
	function checkOnline(){
			
			print_r($this->apps->getUserOnline());exit;
	
	}
	
	function getAccessToken(){
		global $CONFIG;
		
		$at = get_access_token($this->user->id,$CONFIG['SERVICE_KEY']);
		return  $this->apps->toJson(1,'access',$at);
	}
	
		
}
?>
