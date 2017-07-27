<?php
class age_check extends App{
	
	function beforeFilter(){
		global $LOCALE,$CONFIG;
		$this->assign('basedomain',$CONFIG['BASE_DOMAIN']);
		$this->assign('assets_domain', $CONFIG['ASSETS_DOMAIN_WEB']);
	}
	
	function main(){

		if(strip_tags($this->_g('page'))=='age_check') $this->log('surf','age_check');
		return $this->View->toString(TEMPLATE_DOMAIN_WEB .'/age_gateway.html');		
	}
}
?>