<?php
class notFan extends App{
	
	function beforeFilter(){
		global $LOCALE,$CONFIG,$FB;
		$this->assign('basedomain',$CONFIG['BASE_DOMAIN']);
		$this->assign('assets_domain', $CONFIG['ASSETS_DOMAIN_WEB']);
		$this->assign('HeinekenPageID',$FB['heinekenPageID']);

		$this->facebookHelper = $this->useHelper('FacebookHelper');
	}
	
	function main(){
		
		if(strip_tags($this->_g('page'))=='notFan') $this->log('surf','notFan');
		return $this->View->toString(TEMPLATE_DOMAIN_WEB .'/notFan.html');		
	}

	function reload(){
		global $CONFIG;
		$isFan = $this->facebookHelper->isFan($this->user->id);
		if($isFan){
			$_SESSION['user_stats']['isFan']=$isFan;
			sendRedirect("{$CONFIG['BASE_DOMAIN']}home");
			$this->assign("msg","Please wait...");
			return $this->View->toString(TEMPLATE_DOMAIN_WEB .'/login_message.html');	
			die();
		}

		if(strip_tags($this->_g('page'))=='notFan') $this->log('surf','reload notFan');
		return $this->View->toString(TEMPLATE_DOMAIN_WEB .'/notFan.html');		
	}
}
?>