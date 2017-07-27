<?php
class login extends App{
		
	function beforeFilter(){
		$this->loginHelper = $this->useHelper('loginHelper');
		$this->messageHelper  = $this->useHelper('messageHelper');
	
	}
	
	function main(){
		global $CONFIG,$logger;
		$basedomain = $CONFIG['BASE_DOMAIN'];
		$this->assign('basedomain',$basedomain);
		
		$sessionid = strip_tags($this->_g('id'));
		
		if($sessionid!=0){
	
			$res = $this->loginHelper->mopsessionlogin($sessionid);
			
			
			if($res){
					$this->log('login','welcome');
					$this->assign("msg","login-in.. please wait");
					$this->assign("link","{$CONFIG['BASE_DOMAIN']}{$CONFIG['DINAMIC_MODULE']}");
					sendRedirect("{$CONFIG['BASE_DOMAIN']}{$CONFIG['DINAMIC_MODULE']}");
					return $this->out(TEMPLATE_DOMAIN_WEB . '/login_message.html');
					die();
			}
		}

		sendRedirect("{$CONFIG['LOGIN_PAGE']}");
		//return $this->out(TEMPLATE_DOMAIN_WEB . '/login_message.html');
		die();
	}
	
	function local(){

		global $CONFIG,$logger;
		
		$basedomain = $CONFIG['BASE_DOMAIN'];
		$this->assign('basedomain',$basedomain);
		
			$res = $this->loginHelper->loginSession(true);
			
			if($res['result']){
				if(@$this->session->getSession($CONFIG['SESSION_NAME'],"WEB")->login_count==0){
						$this->log('login','welcome');
						$this->assign("msg","login-in.. please wait");
						$this->assign("link","{$CONFIG['BASE_DOMAIN']}{$CONFIG['DINAMIC_MODULE']}");
						sendRedirect("{$CONFIG['BASE_DOMAIN']}changepassword");
						return $this->out(TEMPLATE_DOMAIN_WEB . '/login_message.html');
						die();

				
				}
				$this->log('login','welcome');
				$this->assign("msg","login-in.. please wait");
				$this->assign("link","{$CONFIG['BASE_DOMAIN']}{$CONFIG['DINAMIC_MODULE']}");
				sendRedirect("{$CONFIG['BASE_DOMAIN']}{$CONFIG['DINAMIC_MODULE']}");
				return $this->out(TEMPLATE_DOMAIN_WEB . '/login_message.html');
				die();
			}			
	
		$this->assign("msg","failed to login..");
		return $this->View->toString(TEMPLATE_DOMAIN_WEB .'/login.html');
	}
}
?>