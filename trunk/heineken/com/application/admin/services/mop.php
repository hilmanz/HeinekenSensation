<?php
class mop  extends ServiceAPI{
	
	function beforeFilter(){
		$this->deviceMopHelper= $this->useHelper('deviceMopHelper');
		
		global $LOCALE,$CONFIG;
		$this->assign('basedomain', $CONFIG['BASE_DOMAIN']);
		$this->assign('assets_domain', $CONFIG['ASSETS_DOMAIN_WEB']);		

	}
	
	function login(){
		
		$user = $this->deviceMopHelper->loginAdminMop(); 
		return $user;
		
	}
	
	function endsessionmop(){
		$user = $this->deviceMopHelper->AdminEndSession(); 
		return $user;
		
	}
	
	function checkreferral(){
		$user = $this->deviceMopHelper->checkReferralMop(); 
		return $user;
		
	}
	
	function registeruser(){
		$user = $this->deviceMopHelper->syncAdminUserRegistrant("AdminRegisterProfile"); 
		return $user;
		
	}
	
	function changeuserprofile(){
		$user = $this->deviceMopHelper->syncAdminUserRegistrant("AdminUpdateProfile"); 
		return $user;
		
	}
	
	function registeruserdeduplicate(){
		$user = $this->deviceMopHelper->syncAdminUserRegistrant("AdminRegisterProfileDeDuplication"); 
		return $user;
		
	}
	
	function searchuser(){
		$user = $this->deviceMopHelper->searchProfileUser(); 
		return $user;
		
	}
	
	function searchusergiid(){
		$user = $this->deviceMopHelper->AdminGetProfileonGiid(); 
		return $user;
		
	}
	function getuser(){
		$user = $this->deviceMopHelper->getProfileUser(); 
		return $user;
		
	}
	
	function registerdevice(){
		$user = $this->deviceMopHelper->registerDeviceAdmin(); 
		return $user;
		
	}
	
	function secrettoken(){
	
			$headers = $this->deviceMopHelper->gettokensecret();
			return array('usethis' => $headers);
	}
	
	
}
?>