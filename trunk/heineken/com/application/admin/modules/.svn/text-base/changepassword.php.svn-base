<?php
class changepassword extends App{	
	
	function beforeFilter(){
		global $CONFIG;
		$this->loginHelper = $this->useHelper('loginHelper');
	
		$this->assign('basedomain',$CONFIG['BASE_DOMAIN']);
	}
	
	function main(){
		
		return $this->View->toString(TEMPLATE_DOMAIN_WEB .'/apps/changepassword.html');		
	}
	
	function changeit(){
		global $CONFIG;
		$data = $this->userHelper->changepassword();
		if($data){
				$this->log('login','welcome');
				$this->assign("msg","login-in.. please wait");
				$this->assign("link","{$CONFIG['BASE_DOMAIN']}{$CONFIG['DINAMIC_MODULE']}");
				sendRedirect("{$CONFIG['BASE_DOMAIN']}home");
				// pr('masuk');
				return $this->out(TEMPLATE_DOMAIN_WEB . '/login_message.html');
				exit;
		}else{
		
			sendRedirect("{$CONFIG['LOGIN_PAGE']}");
			return $this->out(TEMPLATE_DOMAIN_WEB . '/login_message.html');
			exit;
		}
	}
	
}
?>