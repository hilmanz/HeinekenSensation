<?php
class gallery extends App{

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
		// $start=null,$limit=10,$contenttype=0,$topcontent=array(0,3),$articletype=false,$groupby=false,$author=false,$allcontent=false
		// pr($this->_request('category'));
		$articlelist = $this->contentHelper->getGalleryTypeContent(null,12);		
		$this->assign('plandata',$articlelist);
		$this->assign('category',strip_tags($this->_request('category')));
		$this->assign('total',$articlelist['total']);
		$this->log('surf','gallery');
			// pr($articlelist);
		return $this->View->toString(TEMPLATE_DOMAIN_WEB .'apps/gallery-pages.html');
	}
	
	function plan(){
			
		$articlelist = $this->contentHelper->getArticleContent(null,10,4,array(0,3),"plan",false,false,true);
		
		$this->assign('plandata',$articlelist);
		$this->assign('total',$articlelist['total']);
		return $this->View->toString(TEMPLATE_DOMAIN_WEB .'apps/gallery-plan-pages.html');
	}
	
	
	function detail(){
		$articlelist = $this->contentHelper->getDetailArticle();
		// pr($articlelist);exit;
		$this->assign('plandata',$articlelist);
		return $this->View->toString(TEMPLATE_DOMAIN_WEB .'apps/gallery-detail-pages.html');
	}
	
	function shows(){
		$articlelist = $this->contentHelper->getDetailArticle();
		// pr($articlelist);exit;
		$this->assign('plandata',$articlelist);
		return $this->View->toString(TEMPLATE_DOMAIN_WEB .'apps/gallery-show-pages.html');
	}
	
	function addphoto(){
		$this->assign('cid',$this->_request('cid'));
		return $this->View->toString(TEMPLATE_DOMAIN_WEB .'apps/add-photo-pages.html');
	}
	
	function upload(){
		global $CONFIG;
			$path = $CONFIG['LOCAL_PUBLIC_ASSET']."article/";
			
			if (isset($_FILES['image'])&&$_FILES['image']['name']!=NULL) {
				if (isset($_FILES['image'])&&$_FILES['image']['size'] <= 20000000) {
					$data = $this->uploadHelper->uploadThisImage($_FILES['image'],$path);
					if ($data['arrImage']!=NULL) {
						$result = $this->contentHelper->addUploadImageGallery($data);
						if($result) {
						
							$data = true;
						} else {
							$data = false;
						}
					} else {
						$data = false;
					}
				} else {
					$data = false;
				}
			} else {
				$data = false;
			}
			
		
		$url = "gallery";
	
		sendRedirect($CONFIG['BASE_DOMAIN'].$url);
		return $this->out(TEMPLATE_DOMAIN_WEB . 'login_message.html');
		exit;
	}
		
	function clearphoto(){
		global $CONFIG;
		$this->contentHelper->clearphotogallery();
		$url = "gallery";
		sendRedirect($CONFIG['BASE_DOMAIN'].$url);
		return $this->out(TEMPLATE_DOMAIN_WEB . 'login_message.html');
		exit;
	}
	
	function clearphotocover(){
		global $CONFIG;
		$this->contentHelper->clearphotocovergallery();
		$url = "gallery";
		sendRedirect($CONFIG['BASE_DOMAIN'].$url);
		return $this->out(TEMPLATE_DOMAIN_WEB . 'login_message.html');
		exit;
	}
	
	function ajax(){
		// pr('masukkkk');exit;
		$needs = $this->_request("needs");
		$start = intval($this->_request("start"));
		if($needs=="gallery") $data =  $this->contentHelper->getGalleryTypeContent($start,12);
		print json_encode($data);exit;
	}
}
?>