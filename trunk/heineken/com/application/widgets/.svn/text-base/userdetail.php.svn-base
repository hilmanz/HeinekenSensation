<?php
class userdetail{
	
	function __construct($apps=null){
		$this->apps = $apps;
		global $LOCALE,$CONFIG;
		$this->apps->assign('basedomain', $CONFIG['BASE_DOMAIN']);
		$this->apps->assign('assets_domain', $CONFIG['ASSETS_DOMAIN_WEB']);
		$this->apps->assign('user',$this->apps->user);
	}

	function main(){

		$loadProfile = $this->apps->userHelper->getUserProfile();

		$this->triviaHelper = $this->apps->useHelper('triviaHelper');
		$notSubmitTheForm = $this->triviaHelper->notSubmitTheForm();
		
		$this->apps->assign('notSubmitTheForm', $notSubmitTheForm);
		
		$this->apps->assign('profile', $loadProfile);
		return $this->apps->View->toString(TEMPLATE_DOMAIN_WEB ."widgets/userdetail.html");	
	}
}
?>