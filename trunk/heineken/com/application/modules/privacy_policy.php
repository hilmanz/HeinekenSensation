<?php
class privacy_policy extends App{
	
	function beforeFilter(){
		global $LOCALE,$CONFIG;
		$this->assign('basedomain',$CONFIG['BASE_DOMAIN']);
		$this->assign('assets_domain', $CONFIG['ASSETS_DOMAIN_WEB']);
		$this->triviaHelper = $this->useHelper('triviaHelper');
		$this->userHelper = $this->useHelper('userHelper');
	}
	
	function main(){
		global $CONFIG;
		if($_SESSION['user_stats']['isFan']==NULL){
				sendRedirect("{$CONFIG['BASE_DOMAIN']}notFan");
				$this->assign("msg","Please wait...");
				return $this->View->toString(TEMPLATE_DOMAIN_WEB .'/login_message.html');
				die();
			
		}
		
		$this->View->assign('user_detail',$this->setWidgets('userdetail'));
		$this->View->assign('trivia',$this->setWidgets('trivia'));
		
		if(strip_tags($this->_g('page'))=='privacy_policy') $this->log('surf','privacy_policy');
		return $this->View->toString(TEMPLATE_DOMAIN_WEB .'/privacy_policy.html');		
	}
}
?>