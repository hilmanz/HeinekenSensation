<?php
class track  extends ServiceAPI{
			

	function beforeFilter(){
	
		$this->trackingGPSHelper = $this->useHelper('trackingGPSHelper');
	
		global $LOCALE,$CONFIG;
		$this->assign('basedomain', $CONFIG['BASE_DOMAIN']);
		$this->assign('assets_domain', $CONFIG['ASSETS_DOMAIN_WEB']);
		$this->assign('locale', $LOCALE[1]);		
		$this->assign('pages', strip_tags($this->_g('page')));		
		
	}
	
	function gps(){

		return $this->trackingGPSHelper->senddata();
	
	}
	

		
}
?>
