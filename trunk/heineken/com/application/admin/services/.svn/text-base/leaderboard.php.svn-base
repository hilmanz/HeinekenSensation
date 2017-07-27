<?php
class leaderboard  extends ServiceAPI{
	
	function beforeFilter(){
		$this->contentHelper = $this->useHelper('contentHelper');
		$this->userHelper  = $this->useHelper('userHelper');
		$this->searchHelper  = $this->useHelper('searchHelper');
		$this->activityHelper   = $this->useHelper('activityHelper');
		$this->entourageHelper    = $this->useHelper('entourageHelper');
		$this->messageHelper     = $this->useHelper('messageHelper');
		$this->leaderboardHelper     = $this->useHelper('leaderboardHelper');

		global $LOCALE,$CONFIG;
		$this->assign('basedomain', $CONFIG['BASE_DOMAIN']);
		$this->assign('assets_domain', $CONFIG['ASSETS_DOMAIN_WEB']);		
		$this->assign('locale', $LOCALE[1]);		
		$this->assign('pages', strip_tags($this->_g('page')));
		$this->assign('tokenize',gettokenize(5000*60,$this->user->id));
	}
	
	function sba(){
			$result = $this->leaderboardHelper->getEntourageList();
			return $result;
	}
	
	function topplace(){
			$result = $this->leaderboardHelper->topplacelist();
			return $result;		
	}

	
}
?>