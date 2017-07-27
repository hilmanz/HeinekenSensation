<?php
class balatest extends App{

	function beforeFilter(){
		$this->contentHelper = $this->useHelper('contentHelper');
		$this->searchHelper = $this->useHelper('searchHelper');
		$this->uploadHelper = $this->useHelper('uploadHelper');
		$this->userHelper = $this->useHelper('userHelper');
		global $LOCALE,$CONFIG;
		$this->assign('basedomain', $CONFIG['BASE_DOMAIN']);
		$this->assign('assets_domain', $CONFIG['ASSETS_DOMAIN_WEB']);
		$this->assign('locale', $LOCALE[1]);		
		$this->assign('pages', strip_tags($this->_g('page')));
		$this->assign('acts', strip_tags($this->_g('act')));
		$this->assign('search', strip_tags($this->_p('search')));
		$this->assign('brand', strip_tags($this->_p('brand')));
		
		$this->assign('startdate', strip_tags($this->_p('startdate')));
		$this->assign('enddate', strip_tags($this->_p('enddate')));
		
		$this->assign('user',$this->user);
		
		$this->assign('badetail',$this->userHelper->getrecepient('badetail'));
		
		$branduser = false;
		$areauser = false;
		$pluser = false;
		$bauser = false;
		
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
		$articlelist = $this->contentHelper->getDetailArticle();
		// pr($articlelist);exit;
		if($articlelist){
			foreach($articlelist['result'] as $plandata){
				foreach($plandata as $key => $val){
					$this->assign($key,$val);
				}
			}
			// pr($articlelist);
			$this->assign('plandata',true);
		}
			return $this->View->toString(TEMPLATE_DOMAIN_WEB .'widgets/balatest-detail.html');
		
	}
	
	
}
?>