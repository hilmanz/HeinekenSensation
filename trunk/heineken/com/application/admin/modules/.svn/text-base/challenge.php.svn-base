<?php
class challenge extends App{

	function beforeFilter(){
		global $LOCALE,$CONFIG;
		
		$this->contentHelper = $this->useHelper('contentHelper');
		$this->searchHelper = $this->useHelper('searchHelper');
		$this->messageHelper  = $this->useHelper('messageHelper');
		$this->userHelper = $this->useHelper('userHelper');
		
		$this->assign('basedomain', $CONFIG['BASE_DOMAIN']);
		$this->assign('assets_domain', $CONFIG['ASSETS_DOMAIN_WEB']);
		$this->assign('locale', $LOCALE[1]);		
		$this->assign('pages', strip_tags($this->_g('page')));
		$this->assign('user',$this->user);
		$this->assign('branddetail',$this->userHelper->getrecepient('branddetail'));
		$this->assign('areadetail',$this->userHelper->getrecepient('areadetail'));
		$this->assign('pldetail',$this->userHelper->getrecepient('pldetail'));
		$this->assign('badetail',$this->userHelper->getrecepient('badetail'));
		
		$social = $this->user->miniondetail;	
		$this->assign('social',$this->userHelper->getrecepient());
		
	}
	
	function main(){
		$articlelist = $this->contentHelper->getArticleContent(null,10,3);
		
		$this->assign('startdate',$this->_p('startdate'));
		$this->assign('enddate',$this->_p('enddate'));
		$this->assign('search',$this->_p('search'));
		$this->assign('total',intval($articlelist['total']));
		$this->assign('challenge',$articlelist['result']);
		$this->log('surf','challenge');
		// pr($articlelist);
		return $this->View->toString(TEMPLATE_DOMAIN_WEB .'/apps/challenge-pages.html');
	}
	
	function create(){
		global $CONFIG;
		
		$this->assign('branddetail',$this->userHelper->getrecepient('branddetail'));
		$this->assign('areadetail',$this->userHelper->getrecepient('areadetail'));
		$this->assign('pldetail',$this->userHelper->getrecepient('pldetail'));
		$this->assign('badetail',$this->userHelper->getrecepient('badetail'));
		
		$social = $this->user->miniondetail;	
		$this->assign('social',$this->userHelper->getrecepient());
		
		if (strip_tags($this->_p('upload'))=="simpan") {
			$data['arrImage']['filename']='';
			$result = $this->contentHelper->addUploadImage($data);
			if ($result) {
				sendRedirect($CONFIG['BASE_DOMAIN']."challenge");
				return $this->out(TEMPLATE_DOMAIN_WEB . '/login_message.html');
				exit;
			}
		}
	
		$social = $this->userHelper->getFriends(true,16);
		$this->assign('social',$social['data']);
		
		
		$social = $this->user->miniondetail;	
		$this->assign('social',$this->userHelper->getrecepient());
		
		return $this->View->toString(TEMPLATE_DOMAIN_WEB .'/widgets/create-challenge.html');
	}
	
	function detail(){
		$cidStr = intval($this->_request('id'));	
		$article = $this->contentHelper->getDetailArticle();
		// pr($article);
		$this->assign('detail',$article['result']);
		$this->assign('cidStr',$cidStr);
		$this->View->assign('challenge_hashtag',$this->setWidgets('challenge_hashtag'));
		return $this->View->toString(TEMPLATE_DOMAIN_WEB .'/widgets/challenge-detail.html');
	}
	
	function detailHashtag(){
		$cid = intval($this->_request('challenge'));
		$cid_user = intval($this->_request('id'));
		
		$article = $this->contentHelper->getDetailArticle();
		
		if (strip_tags($this->_request('tags'))) $tags = strip_tags($this->_request('tags'));
		else $tags = $article['result'][0]['un_tags'];
		
		$challengeHashtag = $this->contentHelper->getChallengeHashtag(0,10,$tags,3);
		$cekWinner = $this->contentHelper->cekChallengeWinner($article['result'][0]['authorid'],$cid_user);
		
		$this->assign('detailhashtag',$article['result']);
		$this->assign('cekwinner',$cekWinner);
		$this->assign('challengeHashtag',$challengeHashtag['result']);
		$this->assign('total_hashtag',$challengeHashtag['total']);
		$this->assign('cidStr',$cid);
		$this->assign('cid_user',$cid_user);
		$this->assign('tags',$tags);
		// pr($challengeHashtag);
		return $this->View->toString(TEMPLATE_DOMAIN_WEB .'/widgets/challengehashtag-detail.html');
	}
	
	function addComment(){
		global $CONFIG;
		
		$cid = intval($this->_p('cid'));
		$data = $this->contentHelper->addComment();
		sendRedirect($CONFIG['BASE_DOMAIN']."challenge/detail/{$cid}");
		return $this->out(TEMPLATE_DOMAIN_WEB . '/login_message.html');
		exit;
	}
	
	function setwinner(){
		global $CONFIG;
		if (strip_tags($this->_p('set'))=="winner") {
			$cid = intval($this->_p('cid'));
			$cid_user = intval($this->_p('cid_user'));			
			$tags = strip_tags($this->_p('tags'));
			
			$data = $this->contentHelper->setWinnerChallenge();
			sendRedirect($CONFIG['BASE_DOMAIN']."challenge/detailHashtag/{$cid_user}/{$cid}/{$tags}");
			return $this->out(TEMPLATE_DOMAIN_WEB . '/login_message.html');
			exit;
		}
	}
	
	function unpublish(){
		global $CONFIG;
		
		$this->Request->setParamPost('cid',intval($this->_request('id')));
		$this->Request->setParamPost('type',6);
		
		$data = $this->contentHelper->unContentPost();
		sendRedirect($CONFIG['BASE_DOMAIN']."challenge");
		return $this->out(TEMPLATE_DOMAIN_WEB . '/login_message.html');
		exit;
	}
	
	function ajax(){
		$needs = $this->_request("needs");
		$tags = $this->_request("tags");
		$contentid = intval($this->_request("contentid"));
		$start = intval($this->_request("start"));
		if ($this->_request("startdate") || $this->_request("enddate") || $this->_request("search")) {
			$this->Request->setParamPost('startdate',$this->_request('startdate'));
			$this->Request->setParamPost('enddate',$this->_request('enddate'));
			$this->Request->setParamPost('search',$this->_request('search'));
		}
		
		if($needs=="challenge") $data =  $this->contentHelper->getArticleContent($start,10,3);
		if($needs=="challenge_hashtag") $data =  $this->contentHelper->getChallengeHashtag(0,3,$tags,3);
		print json_encode($data);exit;
	}
}
?>