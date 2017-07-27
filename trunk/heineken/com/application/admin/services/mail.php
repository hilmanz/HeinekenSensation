<?php
class mail  extends ServiceAPI{
			

	function beforeFilter(){
	
		$this->newsHelper = $this->useHelper('newsHelper');
	
		global $LOCALE,$CONFIG;
		$this->assign('basedomain', $CONFIG['BASE_DOMAIN']);
		$this->assign('assets_domain', $CONFIG['ASSETS_DOMAIN_WEB']);
		$this->assign('locale', $LOCALE[1]);		
		$this->assign('pages', strip_tags($this->_g('page')));		
		
	}
	
	function pin(){

		return $this->newsHelper->sendpinmail();
	
	}
	
	function forgotpassword(){

		return $this->newsHelper->forgotpassword();
	
	}
	
		
}
?>
