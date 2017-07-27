<?php
class baperformance extends App{

	function beforeFilter(){
		$this->contentHelper = $this->useHelper('contentHelper');
		$this->entourageHelper = $this->useHelper('entourageHelper');
		$this->contentHelper = $this->useHelper('contentHelper');
		$this->activityHelper = $this->useHelper('activityHelper');
		$this->searchHelper  = $this->useHelper('searchHelper');
		$this->messageHelper = $this->useHelper('messageHelper');
		$this->userHelper = $this->useHelper('userHelper');
		
		global $LOCALE,$CONFIG;
		$this->assign('basedomain', $CONFIG['BASE_DOMAIN']);
		$this->assign('assets_domain', $CONFIG['ASSETS_DOMAIN_WEB']);
		$this->assign('locale', $LOCALE[1]);		
		$this->assign('pages', strip_tags($this->_g('page')));
		
		$this->assign('social',$this->userHelper->getrecepient());
		$this->assign('badetail',$this->userHelper->getrecepient('badetail'));
		
		$branduser = false;
		$areauser = false;
		$pluser = false;
		$bauser = false;
		
		$this->assign('social',$this->userHelper->getrecepient());
		$this->assign('branddetail',$this->userHelper->getrecepient('branddetail'));
		$this->assign('areadetail',$this->userHelper->getrecepient('areadetail'));
		$this->assign('pldetail',$this->userHelper->getrecepient('pldetail'));
		$this->assign('badetail',$this->userHelper->getrecepient('badetail'));
		
		
		if(in_array($this->user->leaderdetail->type,array(2,4))) $branduser = true;
		if(in_array($this->user->leaderdetail->type,array(3,4))) $areauser = true;
		if(in_array($this->user->leaderdetail->type,array(3,4,5))) $pluser = true;
		if(in_array($this->user->leaderdetail->type,array(2))) $bauser = true;
		
		$this->assign('branduser',$branduser);
		$this->assign('areauser',$areauser);
		$this->assign('pluser',$pluser);
		$this->assign('bauser',$bauser);
	}
	
	function main(){
		$this->log('surf','baperformance');
		$this->assign('startdate',$this->_p('startdate'));
		$this->assign('enddate',$this->_p('enddate'));
		$this->assign('search',$this->_p('search'));
		$this->assign('uid',$this->_p('uid'));
		$data = $this->userHelper->getUserProfile(); 
		$this->View->assign('userprofile',$data);
		
		$this->View->assign('acquisition',$this->setWidgets('acquisition'));
		$this->View->assign('age_relevancy',$this->setWidgets('age_relevancy'));
		$this->View->assign('brand_preference_relevancy',$this->setWidgets('brand_preference_relevancy'));
		$this->View->assign('gender',$this->setWidgets('gender'));
		return $this->View->toString(TEMPLATE_DOMAIN_WEB .'/apps/baperformance-pages.html');
	}
}
?>