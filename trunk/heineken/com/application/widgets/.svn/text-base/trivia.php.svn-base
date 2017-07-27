<?php
class trivia{
	
	function __construct($apps=null){
		$this->apps = $apps;
		global $LOCALE,$CONFIG;
		$this->apps->assign('basedomain', $CONFIG['BASE_DOMAIN']);
		$this->apps->assign('assets_domain', $CONFIG['ASSETS_DOMAIN_WEB']);
		$this->apps->assign('locale',$LOCALE[$this->apps->lid]);
		$this->apps->assign('user',$this->apps->user);
	}

	function main(){

		$this->triviaHelper = $this->apps->useHelper('triviaHelper');		
		$loadTrivia = $this->triviaHelper->loadTrivia();
		$loadFavoriteList = $this->triviaHelper->loadFavoriteList();
		$notSubmitTheForm = $this->triviaHelper->notSubmitTheForm();
		$total_trivia = sizeof($loadTrivia);
		//pr($loadFavoriteList);
		$this->apps->assign('fav_list', $loadFavoriteList);
		$this->apps->assign('trivia_list', $loadTrivia);
		$this->apps->assign('total_trivia', $total_trivia);
		$this->apps->assign('notSubmitTheForm', $notSubmitTheForm);
		return $this->apps->View->toString(TEMPLATE_DOMAIN_WEB ."widgets/trivia.html");	
	}
}
?>