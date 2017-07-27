<?php
class timeline  extends ServiceAPI{
			

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
	
	function feeds(){
		global $logger;
		$featarticle = $this->contentHelper->getArticleFeatured();
		$data['brand'] = $featarticle[0];
		$articlelist = $this->contentHelper->getArticleContent(null,9);
		
		$data['timeline']['total'] =intval($articlelist['total']);
		$data['timeline']['posts'] =$articlelist['result'];
		$data['timeline']['pages'] =$articlelist['pages'];
		
		// $logger->log(json_encode($data));
		return $data;
	}
	
	function detail(){		
		$this->setWidgets('popup_article_detail');
		exit;	
	}
	
		
}
?>
