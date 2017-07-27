<?php
class brand extends App{
	
	function beforeFilter(){	
		$this->contentHelper = $this->useHelper('contentHelper');
		$this->searchHelper = $this->useHelper('searchHelper');
		global $LOCALE,$CONFIG;
		$this->assign('basedomain', $CONFIG['BASE_DOMAIN']);
		$this->assign('assets_domain', $CONFIG['ASSETS_DOMAIN_WEB']);
		$this->assign('locale', $LOCALE[1]);		
		$this->assign('pages', strip_tags($this->_g('page')));		
		$this->assign('branddetail',$this->userHelper->getrecepient('branddetail'));
		$this->assign('areadetail',$this->userHelper->getrecepient('areadetail'));
		$this->assign('pldetail',$this->userHelper->getrecepient('pldetail'));
		$this->assign('badetail',$this->userHelper->getrecepient('badetail'));
	}
	
	function main(){
		$this->View->assign('popular_tags',$this->setWidgets('popular_tags'));
		$this->View->assign('weekly_popular',$this->setWidgets('weekly_popular'));
		$this->View->assign('side_banner',$this->setWidgets('side_banner'));
		$this->View->assign('shorter_filter',$this->setWidgets('shorter_filter'));
		$this->View->assign('article_images_list',$this->setWidgets('article_images_list'));
		$this->log('surf','brand');
		return $this->View->toString(TEMPLATE_DOMAIN_WEB .'/apps/brand-pages.html');
	}
	
	function create(){
		global $CONFIG;
		
		if(strip_tags($this->_p('upload'))=='simpan') {
			
		
			$path = $CONFIG['LOCAL_PUBLIC_ASSET']."article/";
			$data = false;
			if (isset($_FILES['image']) && $_FILES['image']['name']!=NULL) {
				if (isset($_FILES['image']) && $_FILES['image']['size'] <= 20000000) {
					$data = $this->uploadHelper->uploadThisImage($_FILES['image'],$path);
					
				}
			}
			$result = $this->contentHelper->addUploadImage($data);
				
			if($result) {
				$this->log('uploads',$this->getLastInsertId());
				sendRedirect($CONFIG['BASE_DOMAIN']."plan");
					return $this->out(TEMPLATE_DOMAIN_WEB . '/login_message.html');
				exit;
			}
		}
		return $this->View->toString(TEMPLATE_DOMAIN_WEB .'/widgets/create-brand.html');
	}
	
	function detail(){
		$this->setWidgets('popup_article_detail');
		exit;	
	}
	
	function editContent(){
		global $CONFIG,$LOCALE;
		if ($this->_p('authorid')==$this->user->id || $this->_p('authorid')==$this->user->pageid) {
			$data = $this->contentHelper->setEditContent();			
			if ($data) {
				$data;
			} else {
				$data= false;
			}
		} else {
			$data= false;
		}
		print json_encode($data);exit;
	}
	
	function ajax(){
		$needs = $this->_request("needs");
		$contentid = intval($this->_request("contentid"));
		$start = intval($this->_request("start"));
		if($needs=="content")  $data =  $this->contentHelper->getArticleContent($start);		
		if($needs=="comment") $data =  $this->contentHelper->getComment($contentid,false,$start);
		print json_encode($data);exit;
	}
	
	function article(){
		$this->View->assign('popular_tags',$this->setWidgets('popular_tags'));
		$this->View->assign('weekly_popular',$this->setWidgets('weekly_popular'));
		$this->View->assign('side_banner',$this->setWidgets('side_banner'));
		$this->View->assign('shorter_filter',$this->setWidgets('shorter_filter'));
		$this->View->assign('article_images_list',$this->setWidgets('article_list'));
		$this->log('surf','brand');
		return $this->View->toString(TEMPLATE_DOMAIN_WEB .'/apps/brand-pages.html');
	}
	
	function highlight(){
		$this->View->assign('popular_tags',$this->setWidgets('popular_tags'));
		$this->View->assign('weekly_popular',$this->setWidgets('weekly_popular'));
		$this->View->assign('side_banner',$this->setWidgets('side_banner'));
		$this->View->assign('shorter_filter',$this->setWidgets('shorter_filter'));
		$this->View->assign('article_images_list',$this->setWidgets('article_list'));
		$this->log('surf','brand');
		return $this->View->toString(TEMPLATE_DOMAIN_WEB .'/apps/brand-pages.html');
	}
}
?>