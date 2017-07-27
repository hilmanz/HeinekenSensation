<?php
class account extends App{
	
	function beforeFilter(){
		global $LOCALE,$CONFIG;
		$this->assign('basedomain',$CONFIG['BASE_DOMAIN']);
		$this->assign('assets_domain', $CONFIG['ASSETS_DOMAIN_WEB']);
		$this->widgetHelper = $this->useHelper('widgetHelper');
	}
	
	function main(){
		
		//$this->View->assign('shorter',$this->setWidgets('shorter'));
		$this->View->assign('account_info',$this->setWidgets('account_info'));
		$this->View->assign('user_detail',$this->user);
		
		if(strip_tags($this->_g('page'))=='account') $this->log('surf','account');
		return $this->View->toString(TEMPLATE_DOMAIN_WEB .'/account.html');		
	}

	function setting(){
		//$this->View->assign('shorter',$this->setWidgets('shorter'));
		$this->View->assign('account_info',$this->setWidgets('account_info'));
		$this->View->assign('user_detail',$this->user);
		//pr($this->user);exit;
		
		if(strip_tags($this->_g('page'))=='account-setting') $this->log('surf','account-setting');
		return $this->View->toString(TEMPLATE_DOMAIN_WEB .'/account-setting.html');	
	}
}
?>