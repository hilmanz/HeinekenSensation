<?php
class getaccess_service {	
			
	function __construct($apps){
		global $logger,$CONFIG;
		$this->logger = $logger;
		$this->apps = $apps;
		
		$this->dbshema = "athreesix";
		}
	
	function me(){
		global $CONFIG;
		
			// print_r('asdasd');exit;
			pr($CONFIG['SESSION_NAME']);exit;
	
	}
	
	function getAccessToken(){
		global $CONFIG;
		
		$at = get_access_token($this->user->id,$CONFIG['SERVICE_KEY']);
		return  $this->apps->toJson(1,'access',$at);
	}
	
		
}
?>
