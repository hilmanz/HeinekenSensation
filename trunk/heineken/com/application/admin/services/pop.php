<?php
class pop extends ServiceAPI{
	
	function beforeFilter(){
		$this->contentHelper = $this->useHelper('contentHelper');
		$this->checkinHelper = $this->useHelper('checkinHelper');
		global $LOCALE,$CONFIG;
		
		$this->assign('basedomain', $CONFIG['BASE_DOMAIN']);
		$this->assign('assets_domain', $CONFIG['ASSETS_DOMAIN_WEB']);
		$this->assign('user',$this->user);
		$this->assign('tokenize',gettokenize(5000*60,$this->user->id));
		$this->assign('pages', strip_tags($this->_g('page')));
		$this->assign('locale', $LOCALE[1]);		
	
	}
	
	function main(){
		return false;
	}
	
	function comment(){
		/*
		if($cid==null) $cid = intval($this->apps->_p('cid'));
		*/
		$data = $this->contentHelper->unComment();
		
		return $data;
	}
	
	function content(){
		/*
		if($cid==null) $cid = intval($this->apps->_p('cid'));
		*/
		$data = $this->contentHelper->unContentPost();
		
		return $data;
	}

		
	function checkin($data){
		/*
				$cid = intval($this->apps->_p('cid'));
		*/
		$data = $this->checkinHelper->uncheckin();
		return $data;
	}
	
}
?>