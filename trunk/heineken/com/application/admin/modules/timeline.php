<?php
class timeline extends App{

	
	function beforeFilter(){
	
		$this->entourageHelper = $this->useHelper('entourageHelper');
		$this->contentHelper = $this->useHelper('contentHelper');
		$this->searchHelper = $this->useHelper('searchHelper');
		global $LOCALE,$CONFIG;
		$this->assign('basedomain', $CONFIG['BASE_DOMAIN']);
		$this->assign('assets_domain', $CONFIG['ASSETS_DOMAIN_WEB']);
		$this->assign('locale', $LOCALE[1]);		
		$this->assign('pages', strip_tags($this->_g('page')));		
	}
	
	function main(){
		
		$this->View->assign('popular_tags',$this->setWidgets('popular_tags'));
		$this->View->assign('weekly_popular',$this->setWidgets('weekly_popular'));
		$this->View->assign('side_banner',$this->setWidgets('side_banner'));
		$this->View->assign('shorter_filter',$this->setWidgets('shorter_filter'));
		$this->View->assign('article_images_list',$this->setWidgets('article_images_list'));
		$this->log('surf','timeline');
		return $this->View->toString(TEMPLATE_DOMAIN_WEB .'/apps/timeline-pages.html');

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
		$this->log('surf','timeline');
		return $this->View->toString(TEMPLATE_DOMAIN_WEB .'/apps/timeline-pages.html');

	}
	
	function highlight(){
		
		$this->View->assign('popular_tags',$this->setWidgets('popular_tags'));
		$this->View->assign('weekly_popular',$this->setWidgets('weekly_popular'));
		$this->View->assign('side_banner',$this->setWidgets('side_banner'));
		$this->View->assign('shorter_filter',$this->setWidgets('shorter_filter'));
		$this->View->assign('article_images_list',$this->setWidgets('article_list'));
		$this->log('surf','timeline');
		return $this->View->toString(TEMPLATE_DOMAIN_WEB .'/apps/timeline-pages.html');

	}
}
?>