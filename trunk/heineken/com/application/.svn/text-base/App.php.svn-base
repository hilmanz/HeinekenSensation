<?php
global $APP_PATH;

class App extends Application{
	
	var $_mainLayout=""; 
	var $user = array();
	var $ACL;
	var $userHelper;
	var $permissionHelper=false;
	var $contentHelper;
	var $triviaHelper;
	var $userpage;
	function __construct(){
		parent::__construct();
		
		$this->setVar();
	
	}
	/**
	 * warning : do not put db query here.
	 */
	function setVar(){
		global $CONFIG;
		if(isset($_SESSION['lid'])) $this->lid = intval($_SESSION['lid']);
		else $this->lid = 1;
		if($this->lid=='')$this->lid=1;
		
		$this->userpage = $CONFIG['DINAMIC_MODULE'];
		$this->assign('userpage',$this->userpage);
		
		$page= strip_tags($this->_g('page'));	
		$act= strip_tags($this->_g('act'));
		//if($this->isUserOnline()) $this->user = $this->getUserOnline();
		if($page!='login'&&$page!='age_check'){
			if($this->isUserOnline()){
				$this->user = $this->getUserOnline();
			}else{			
				//var_dump($this->isUserOnline());exit;
				sendRedirect("{$CONFIG['AGE_CHECK']}");
				exit;
			}
		}
		
	}
	
	function main(){
		global $CONFIG,$LOCALE;
		global $FB;	
		
		
		if($CONFIG['MAINTENANCE']==true){
			$this->assign('meta',$this->View->toString(TEMPLATE_DOMAIN_WEB . "/meta.html"));
			$this->assign('mainContent', $this->View->toString(TEMPLATE_DOMAIN_WEB . '/under-maintenance.html'));
			$this->mainLayout(TEMPLATE_DOMAIN_WEB . '/master.html');
		} else {
			
			$str = $this->run();
			
			$this->afterFilter();		
			//pr($this->user);exit;
			$this->assign('basedomain', $CONFIG['BASE_DOMAIN']);
			$this->assign('pages',  strip_tags($this->_g('page')));

			$this->assign('assets_domain', $CONFIG['ASSETS_DOMAIN_WEB']);
			$this->assign('fb_app', $FB['appID']);
			if($this->isUserOnline()){
				$this->assign('logoutUrl', $_SESSION['logoutUrl']);
				$this->userHelper = $this->useHelper('userHelper');
				$this->triviaHelper = $this->useHelper('triviaHelper');
				$notSubmitTheForm = $this->triviaHelper->notSubmitTheForm();

				$loadProfile = $this->userHelper->getUserProfile();
				//pr($loadProfile);exit;
				$this->assign('profile',$loadProfile);

				
				$this->assign('notSubmitTheForm', $notSubmitTheForm);
				$this->assign('trivia',$this->setWidgets('trivia'));
			}
			$this->assign('meta',$this->View->toString(TEMPLATE_DOMAIN_WEB . "/meta.html"));
			$this->assign('header',$this->View->toString(TEMPLATE_DOMAIN_WEB . "/header.html"));
			$this->assign('footer',$this->View->toString(TEMPLATE_DOMAIN_WEB . "/footer.html"));
			
			

			
			$this->assign('mainContent',$str);

			$this->beforeRender();
			$this->mainLayout(TEMPLATE_DOMAIN_WEB . '/master.html');				
		}
	}
	
	function getMessageUser()
	{
	
		$message = $this->messageHelper->getinboxcount();
		$this->View->assign('messageCount',$message);
		// pr($message);
		
	}

	
	function setWidgets($class=null,$path=null){
		GLOBAL $APP_PATH;
		
		if($class==null) return false;
			if( !is_file( $APP_PATH .WIDGET_DOMAIN_WEB. $path . $class .'.php' ) ){
			
				if( is_file( '../templates/'. WIDGET_DOMAIN_WEB . $path  . $class .'.html' ) ){
					return $this->View->toString(WIDGET_DOMAIN_WEB .$path. $class .'.html');
				}
			}else{
			// pr($class);
				include_once $APP_PATH . WIDGET_DOMAIN_WEB . $path. $class .'.php';
				$widgetsContent = new $class($this);
				return $widgetsContent->main();
			}
	}
	
	
	function useHelper($class=null,$path=null){
		GLOBAL $APP_PATH,$DEVELOPMENT_MODE;
		if($class==null) return false;
		if(file_exists($APP_PATH . HELPER_DOMAIN_WEB. $path. $class .'.php')){
			include_once $APP_PATH . HELPER_DOMAIN_WEB. $path. $class .'.php';
			$helper = new $class($this);
			return $helper;
		}else{
			
				print "please define : ".$APP_PATH . HELPER_DOMAIN_WEB. $path. $class .'.php';
				die();
		
		}
	}
	
	/*
	 *	Mengatur setiap paramater di alihkan ke class yang mengaturnya
	 *
	 *	Urutan paramater:
	 *	- page			(nama class) 
	 *	- act				(nama method)
	 *	- optional		(paramater selanjutnya optional, tergantung kebutuhan)
	 */
	function run(){
		global $APP_PATH,$CONFIG;
		
		//ini me-return variable $page dan $act
		if($this->Request->getParam("req")) $this->Request->decrypt_params($this->Request->getParam("req"));
		$page = $this->Request->getParam('page');		
		$act = $this->Request->getParam('act');
		/* unverified go here */

		//var_dump($_SESSION['user_stats']);
		if($page!='login'&&$page!='notFan'){
			//var_dump($_SESSION['user_stats']);exit;
			/*sendRedirect("{$CONFIG['NOT_FAN_BASE_DOMAIN']}");
					$this->assign("msg","Please wait...");
					return $this->View->toString(TEMPLATE_DOMAIN_WEB .'/login_message.html');
					die();*/
			//var_dump($_SESSION['user_stats']['isFan']);exit;
			/*if($_SESSION['user_stats']['isFan']==NULL||$_SESSION['user_stats']['isFan']=='NULL'){
					sendRedirect("{$CONFIG['BASE_DOMAIN']}notFan");
					$this->assign("msg","Please wait...");
					return $this->View->toString(TEMPLATE_DOMAIN_WEB .'/login_message.html');
					die();
				
			}*/
		}
		/*if($this->isUserOnline()==false) {		
			return $this->View->toString(TEMPLATE_DOMAIN_WEB.'/age_gateway.html');
		}*/
		//pr($this->isUserOnline());exit;
		if( $page != '' ){
			if( !is_file( $APP_PATH . MODULES_DOMAIN_WEB . $page . '.php' ) ){
				
				if( is_file( '../templates/'. TEMPLATE_DOMAIN_WEB . '/'. $page . '.html' ) ){
						if($page!='login'&&$page!='age_check'){
							if($this->isUserOnline()){
								return $this->View->toString(TEMPLATE_DOMAIN_WEB.'/'.$page.'.html');
							}else{
								//var_dump('bar5');exit;
									sendRedirect("{$CONFIG['AGE_CHECK']}");
									exit;
								
							}
						}else{
								//var_dump('bar6');exit;
									sendRedirect("{$CONFIG['AGE_CHECK']}");
									exit;
							
						}
						
				}else{
					sendRedirect($CONFIG['BASE_DOMAIN']."index.php");
					die();
				}
			}else{
					//var_dump($page);exit;
				include_once MODULES_DOMAIN_WEB. $page.'.php';
				
				$content = new $page();
				
				$content->beforeFilter();
				
				if( $act != '' ){
					if( method_exists($content, $act) )	return $content->$act();
					else {
						if($page!='login'&&$page!='age_check'){
							if($this->isUserOnline()){
								return $content->main();
							}else{
								//var_dump('bar');exit;
									sendRedirect("{$CONFIG['AGE_CHECK']}");
									exit;
								
							}
						}else{
								//var_dump('bar3');exit;
									sendRedirect("{$CONFIG['AGE_CHECK']}");
									exit;
							
						}
					}
				}else {
						if($page!='login'&&$page!='age_check'){
							if($this->isUserOnline()){
								return $content->main();
							}else{
								
								//var_dump('bar2');exit;
									sendRedirect("{$CONFIG['AGE_CHECK']}");
									exit;
								
							}
						}else{
									//var_dump('bar4');exit;
									sendRedirect("{$CONFIG['AGE_CHECK']}");
									exit;
								
						}
				}
			}			
		} else {			
			include_once MODULES_DOMAIN_WEB . $CONFIG['DEFAULT_MODULES'];
			$content = new home();
			$content->beforeFilter();
			return $content->main();
		}
	}
	
	function birthday($birthday){
		$birth = explode(' ',$birthday);
		list($year,$month,$day) = explode("-",$birth[0]);
		$year_diff  = date("Y") - $year;
		$month_diff = date("m") - $month;
		$day_diff   = date("d") - $day;
		if ($day_diff < 0 || $month_diff < 0)
		  $year_diff--;
		return $year_diff;
	}
	
	function is_valid_email($email) {
	  $result = TRUE;
	  if(!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$", $email)) {
		$result = FALSE;
	  }
	  return $result;
	}
	
	function is_admin(){
	
		$sql = "SELECT count(*) as total 
			FROM tbl_front_admin
			WHERE
			user_id='".mysql_escape_string(intval($_SESSION['user_id']))."' 
			AND fb_id='".mysql_escape_string(intval($_SESSION['user_login_id']))."'
			LIMIT 1
			;";
		
		$this->open(0);
		$checkAdmin = $this->fetch($sql);
		$this->close();	
		// print_r($sql);			
		if($checkAdmin) {
		$is_admin = ($checkAdmin['total']>=1) ? true : false ;
		}else $is_admin = false;
		
		return $is_admin;
	}
	function objectToArray($object) {
		//print_r($object);exit;
		
		 if (is_object($object)) {
		    foreach ($object as $key => $value) {
		        $array[$key] = $value;
		    }
		}
		else {
		    $array = $object;
		}
		return $array;
		
	}
	
}
?>