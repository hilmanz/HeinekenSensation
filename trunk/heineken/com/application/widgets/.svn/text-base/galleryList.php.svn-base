<?php
class galleryList{
	
	function __construct($apps=null){
		$this->apps = $apps;
		global $LOCALE,$CONFIG;
		$this->apps->assign('basedomain', $CONFIG['BASE_DOMAIN']);
		$this->apps->assign('assets_domain', $CONFIG['ASSETS_DOMAIN_WEB']);
		$this->apps->assign('locale',$LOCALE[$this->apps->lid]);
		$this->apps->assign('user',$this->apps->user);
	}

	function main(){

		$load = $this->apps->galleryHelper->loadGallery(0,9,0,true);

		$this->apps->assign('gallery', $load['data']);
		$this->apps->assign('galleryNext', $load['data_total']);
		return $this->apps->View->toString(TEMPLATE_DOMAIN_WEB ."widgets/galleryList.html");	
	}
}
?>