<?php
class home extends App{
	
	function beforeFilter(){
		global $LOCALE,$CONFIG;
		$this->assign('basedomain',$CONFIG['BASE_DOMAIN']);
		$this->assign('assets_domain', $CONFIG['ASSETS_DOMAIN_WEB']);
		$this->triviaHelper = $this->useHelper('triviaHelper');
		$this->userHelper = $this->useHelper('userHelper');
		$this->galleryHelper = $this->useHelper('galleryHelper');
		$this->calendarHelper = $this->useHelper('calendarHelper');
	}
	
	function main(){
		global $CONFIG,$logger;
		
		if($_SESSION['user_stats']['isFan']==NULL){
				sendRedirect("{$CONFIG['BASE_DOMAIN']}notFan");
				$this->assign("msg","Please wait...");
				return $this->View->toString(TEMPLATE_DOMAIN_WEB .'/login_message.html');
				die();
			
		}
		//pr($_SESSION['user_stats']);exit;

		$this->View->assign('user_detail',$this->setWidgets('userdetail'));
		$this->View->assign('galleryList',$this->setWidgets('galleryList'));
		$this->View->assign('slider',$this->setWidgets('slider'));

		$loadProfile = $this->userHelper->getUserProfile();
		$this->View->assign('profile',$loadProfile);
		
		if(strip_tags($this->_g('page'))=='home') $this->log('surf','home');
		return $this->View->toString(TEMPLATE_DOMAIN_WEB .'/home.html');		
	}

	function loadCalendar(){

		$city = strip_tags($this->_p('city'));
		$start = intval($this->_p('start'));
		$limit = intval($this->_p('limit'));
		
		$calendar = $this->calendarHelper->loadCalendar($city,$start,$limit);

		if($calendar){
			print_r(json_encode(array('status'=>1,'message'=>'success','data'=>$calendar)));exit;
		}else{
			print_r(json_encode(array('status'=>0,'message'=>'No data')));exit;
		}
	}

	function submit(){
		global $CONFIG;

		$pattern = strip_tags($this->_p('pattern'));
		$reasonToWin = strip_tags($this->_p('reasonToWin'));
		$hobby = intval($this->_p('hobby'));
		$weekend = intval($this->_p('weekend'));
		$music = intval($this->_p('genre'));
		$rs = $this->triviaHelper->submitTrivia($pattern,$reasonToWin,$hobby,$weekend,$music);

		print_r(json_encode($rs));exit;
	}
	
	function load(){
		global $CONFIG;

		$pattern = strip_tags($this->_p('pattern'));
		$rs = $this->triviaHelper->loadResultTrivia($pattern);

		print_r(json_encode($rs));exit;
	}

	function changeProfile(){
		global $CONFIG;
		//phone update
		$phone = htmlspecialchars($this->_p('phone'));
		if(isset($phone)==false&&$phone=='') $phone = null;
		//email update
		$email = htmlspecialchars($this->_p('email'));
		if(isset($email)==true && $email!=''){
			if(!$this->is_valid_email($email)){
				$msg = "Your email is not valid.";
				print_r(json_encode(array('status'=>0,'message'=>$msg)));exit;
			}
		}else{
			$email=null;
		}

		$upt = $this->userHelper->changeProfile($phone,$email);

		print_r(json_encode($upt));exit;
	}

	function logAjax(){
		global $CONFIG,$logger;
		$log = htmlspecialchars($this->_p('log'));
		if(isset($log)==false&&$log=='') $log = null;

		var_dump($log);
		if($log){
			$this->log('surf',$log);exit;
		}
		exit;
	}

}
?>