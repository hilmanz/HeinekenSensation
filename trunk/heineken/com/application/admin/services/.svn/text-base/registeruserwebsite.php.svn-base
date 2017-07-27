<?php
class registeruserwebsite extends ServiceAPI{
		
	function beforeFilter(){
		$this->registerHelper = $this->useHelper('registerHelper');
	}
	
	function register(){
	
		$data = $this->registerHelper->registerPhase();
		return $data;
	}
	
	function getleader(){
		$type= $this->_p('leadertype');
		$data = $this->registerHelper->getLeader($type);
		print json_encode($data);exit;
	}
		
		
	
}
?>