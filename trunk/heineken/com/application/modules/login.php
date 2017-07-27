<?php
class login extends App{
		
	function beforeFilter(){
		$this->FacebookHelper = $this->useHelper('FacebookHelper');
		$this->loginHelper = $this->useHelper('loginHelper');
		$this->session = $this->useHelper('SessionHelper');
	}
	
	function main(){
		global $CONFIG,$logger;
		$basedomain = $CONFIG['BASE_DOMAIN'];
		$assetdomain = $CONFIG['ASSETS_DOMAIN_WEB'];
		$this->assign('basedomain',$basedomain);
		$this->assign('assets_domain',$assetdomain);
		
		return $this->View->toString(TEMPLATE_DOMAIN_WEB .'/login.html');
	}
	function fb(){
		global $CONFIG,$logger;
		$basedomain = $CONFIG['BASE_DOMAIN'];
		$assetdomain = $CONFIG['ASSETS_DOMAIN_WEB'];
		$this->assign('basedomain',$basedomain);
		$this->assign('assets_domain',$assetdomain);
		//var_dump($this->session->getSession($this->config['SESSION_NAME'],"WEB"));
		if(!$this->session->getSession($this->config['SESSION_NAME'],"WEB")){
			$fbLogin = $this->FacebookHelper->init();

			$this->assign('fbURL',$fbLogin['return']['urlConnect']);
			
			if($fbLogin['status']==1){
				$user_status = $this->FacebookHelper->checkUser($fbLogin['return']['userProfile'],$fbLogin['return']['isLiked']);
				/*pr($user_status);exit;*/
				$_SESSION['user_stats']=$user_status;
				//pr($_SESSION['user_stats']);exit;
				$_SESSION['logoutUrl']=$fbLogin['return']['urlConnect'];
				$this->session->setSession($this->config['SESSION_NAME'],"WEB",$fbLogin['return']['userProfile']);
				$this->log('surf','login');
				if($this->session->getSession($this->config['SESSION_NAME'],"WEB")){
					sendRedirect("{$CONFIG['BASE_DOMAIN']}{$CONFIG['DINAMIC_MODULE']}");
					$this->assign("msg","Please wait...");
					return $this->View->toString(TEMPLATE_DOMAIN_WEB .'/login_message.html');
					die();
				}
			}
		}else{
			sendRedirect("{$CONFIG['BASE_DOMAIN']}{$CONFIG['DINAMIC_MODULE']}");
			$this->assign("msg","Please wait...");
			return $this->View->toString(TEMPLATE_DOMAIN_WEB .'/login_message.html');
			die();
		}

		
		return $this->View->toString(TEMPLATE_DOMAIN_WEB .'/login.html');
	}

	function setUser(){

	}

	function age_check(){
		global $CONFIG,$logger;
		$basedomain = $CONFIG['BASE_DOMAIN'];
		$assetdomain = $CONFIG['ASSETS_DOMAIN_WEB'];
		$this->assign('basedomain',$basedomain);
		$this->assign('assets_domain',$assetdomain);
		//var_dump($this->session->getSession($this->config['SESSION_NAME'],"WEB"));
		if($this->session->getSession($this->config['SESSION_NAME'],"WEB")){
			sendRedirect("{$CONFIG['BASE_DOMAIN']}{$CONFIG['DINAMIC_MODULE']}");
			$this->assign("msg","Please wait...");
			return $this->View->toString(TEMPLATE_DOMAIN_WEB .'/login_message.html');
			die();
		}else{
			$age_check = intval($this->_p('age_check'));
			if($age_check==1){
				//age check
				$day = intval($this->_p('dday'));
				$month = intval($this->_p('dmonth'));
				$year = "19".intval($this->_p('dyear'));
				
				$age = intval(date("md", date("U", mktime(0, 0, 0, $month, $day, $year))) > date("md") ? ((date("Y")-$year)-1):(date("Y")-$year));
				if($age<21||$day==0||$month==0||$age>60){ 
					$this->assign("msg","Sorry, you have to be at least 21 years old to enter this site");
				}else{
					sendRedirect("{$CONFIG['BASE_DOMAIN']}login/fb");
					$this->assign("msg","Please wait...");
					return $this->View->toString(TEMPLATE_DOMAIN_WEB .'/login_message.html');
					die();
				}
			}
		}

		//var_dump($basedomain);exit;
		return $this->View->toString(TEMPLATE_DOMAIN_WEB .'/age_gateway.html');
	}

	function tou_web(){
		return $this->View->toString(TEMPLATE_DOMAIN_WEB .'/touWeb.html');
	}

	function tou_quiz(){
		return $this->View->toString(TEMPLATE_DOMAIN_WEB .'/touQuiz.html');
	}

	
}
?>