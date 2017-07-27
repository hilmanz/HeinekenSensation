<?php
class brand  extends ServiceAPI{
			

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
		$featarticle = $this->contentHelper->getArticleFeatured();
		$data['brand'] = $featarticle[0];
		$articlelist = $this->contentHelper->getArticleContent();
		$data['brand']['total'] =intval($articlelist['total']);
		$data['brand']['posts'] =$articlelist['result'];
	
		return $data;
	}
	
	function detail(){		
		$this->setWidgets('popup_article_detail');
		exit;	
	}
	
		
}
?>
