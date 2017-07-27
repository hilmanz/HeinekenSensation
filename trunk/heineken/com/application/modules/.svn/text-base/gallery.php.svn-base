<?php
class gallery extends App{
	
	function beforeFilter(){
		global $LOCALE,$CONFIG;
		$this->assign('basedomain',$CONFIG['BASE_DOMAIN']);
		$this->assign('assets_domain', $CONFIG['ASSETS_DOMAIN_WEB']);
		$this->triviaHelper = $this->useHelper('triviaHelper');
		$this->userHelper = $this->useHelper('userHelper');
		$this->galleryHelper = $this->useHelper('galleryHelper');
	}
	
	function main(){
		global $CONFIG;
		if($_SESSION['user_stats']['isFan']==NULL){
				sendRedirect("{$CONFIG['BASE_DOMAIN']}notFan");
				$this->assign("msg","Please wait...");
				return $this->View->toString(TEMPLATE_DOMAIN_WEB .'/login_message.html');
				die();
			
		}
		
		$this->View->assign('user_detail',$this->setWidgets('userdetail'));
		$this->View->assign('trivia',$this->setWidgets('trivia'));
		$this->View->assign('gallery',$this->galleryHelper->loadGallery());

		if(strip_tags($this->_g('page'))=='gallery') $this->log('surf','gallery');
		return $this->View->toString(TEMPLATE_DOMAIN_WEB .'/gallery.html');		
	}

	function loadGallery(){

		$start = intval($this->_p('start'));
		$limit = intval($this->_p('limit'));

		$load = $this->galleryHelper->loadGallery($start,$limit);

		if($load){
			print_r(json_encode(array('status'=>1,'message'=>'success','data'=>$load)));exit;
		}else{
			print_r(json_encode(array('status'=>0,'message'=>'No data')));exit;
		}
	}
}
?>