<?php
class friends  extends ServiceAPI{
	
	function beforeFilter(){
		$this->contentHelper = $this->useHelper('contentHelper');
		$this->userHelper  = $this->useHelper('userHelper');
		$this->searchHelper  = $this->useHelper('searchHelper');
		$this->activityHelper   = $this->useHelper('activityHelper');
		$this->entourageHelper    = $this->useHelper('entourageHelper');
		$this->messageHelper     = $this->useHelper('messageHelper');

		global $LOCALE,$CONFIG;
		$this->assign('basedomain', $CONFIG['BASE_DOMAIN']);
		$this->assign('assets_domain', $CONFIG['ASSETS_DOMAIN_WEB']);		
		$this->assign('locale', $LOCALE[1]);		
		$this->assign('pages', strip_tags($this->_g('page')));
		$this->assign('tokenize',gettokenize(5000*60,$this->user->id));
	}
	
	function profile(){
			$result = $this->userHelper->getUserProfile();
			
			return $result;
	}
	
	function add(){
			$result = $this->userHelper->addCircleUser();
			if($result)  $this->notif(" {$this->user->name} {$this->user->last_name} added you as a friends ",false,intval($this->_request('fid')));
			return $result;
	}
	
	
	function unadd(){
			$result = $this->userHelper->unAddCircleUser();
			
			return $result;
	}
	
	function search(){
			$result = $this->userHelper->getSearchFriends();
			
			return $result;
	}
	
}
?>