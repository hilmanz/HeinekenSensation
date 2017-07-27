<?php
class slider{
	
	function __construct($apps=null){
		$this->apps = $apps;
		global $LOCALE,$CONFIG;
		$this->apps->assign('basedomain', $CONFIG['BASE_DOMAIN']);
		$this->apps->assign('assets_domain', $CONFIG['ASSETS_DOMAIN_WEB']);
		$this->apps->assign('locale',$LOCALE[$this->apps->lid]);
		$this->apps->assign('user',$this->apps->user);
	}

	function main(){

		//pr($this->apps->user);exit;
		$loadProfile = $this->apps->userHelper->getUserProfile();
		if($loadProfile['user_type']==2){
			$loadGallery = $this->apps->galleryHelper->loadGallery(0,10,1);
			$this->apps->assign('slider',$loadGallery['data']);
		}
		return $this->apps->View->toString(TEMPLATE_DOMAIN_WEB ."widgets/slider.html");	
	}
}
?>