<?php
class my  extends ServiceAPI{
	
	function beforeFilter(){
		$this->contentHelper = $this->useHelper('contentHelper');
		$this->userHelper  = $this->useHelper('userHelper');
		$this->wallpaperHelper = $this->useHelper('wallpaperHelper');
		$this->uploadHelper = $this->useHelper('uploadHelper');
		$this->activityHelper = $this->useHelper('activityHelper');
		$this->newsHelper = $this->useHelper('newsHelper');
		$this->messageHelper = $this->useHelper('messageHelper');
		$this->searchHelper = $this->useHelper('searchHelper');
		$this->entourageHelper = $this->useHelper('entourageHelper');
		global $LOCALE,$CONFIG;
		$this->assign('basedomain', $CONFIG['BASE_DOMAIN']);
		$this->assign('assets_domain', $CONFIG['ASSETS_DOMAIN_WEB']);		
		$this->assign('locale', $LOCALE[1]);		
		$this->assign('pages', strip_tags($this->_g('page')));
		$this->assign('tokenize',gettokenize(5000*60,$this->user->id));
	}
	
	function profile(){
		$user = $this->userHelper->getUserProfile(); 
		$plan = $this->contentHelper->getArticleContent(null,10,4,array(0,3),"plan",false,false,false,true,true,true,false);
		$data['profile'] = $user;
		$data['role'] = $this->user->leaderdetail->pagetypename;
		$data['badetail'] = @$this->user->badetail;
		$data['branddetail'] =  @$this->user->branddetail;
		$data['pldetail'] =  @$this->user->pldetail;
		$data['areadetail'] =  @$this->user->areadetail;
		$data['role'] = $this->user->leaderdetail->pagetypename;
		$data['notification'] = $this->activityHelper->getA360activity(0,5,false,false,false,'3',false);	
		$data['plan']['total'] = $plan['total'];
		$data['plan']['lists'] = $plan['result'];
		$data['challenge'] = false;
		$data['entourage'] = $this->entourageHelper->getEntourage();
		return $data;		
		
	}
	
	function changepassword(){
			
			$data = $this->userHelper->changepassword();
			return $data;	
	}
	
	function inbox(){
		$data = $this->messageHelper->getMessage();

		return $data;		
	}

	
	function friends(){
				
		$data = $this->userHelper->getFriends(true,50);

		return $data;
		
	}
	
	function album(){
		global $CONFIG;
		$typegallery = strip_tags($this->_request('type'));
		
		$plan = $this->contentHelper->getGalleryTypeContent(null,10);
	
		$data['album']['total'] = $plan['total'];
		$data['album']['lists'] = $plan['result'];
		$data['album']['pages'] =$plan['pages'];
		return $data;
	}
	
	function circle(){
		global $CONFIG;
		
		if(strip_tags($this->_g('do'))=='create') {
			$data = $this->userHelper->createCircleUser();
			$name = strip_tags($this->_request('name'));
			if($data) $this->log('create group',"{$name}");
			return $data;
		}
		if(strip_tags($this->_g('do'))=='loss') {
			$data = $this->userHelper->uncreateCircleUser();
			$name = strip_tags($this->_request('name'));
			if($data) $this->log('destroy group',"{$name}");
			return $data;
		}
		
		exit;			
	}
	
	function cover(){
		$this->log('add cover',$this->user->id);
		global $CONFIG;
		$data = array('status'=>false,'message'=>'there is no actions');
		if(strip_tags($this->_p('action'))=='set') {
		
			$path = $CONFIG['LOCAL_PUBLIC_ASSET']."user/cover/";
			
			if (isset($_FILES['image'])&&$_FILES['image']['name']!=NULL) {
				if (isset($_FILES['image'])&&$_FILES['image']['size'] <= 20000000) {
					$data = $this->uploadHelper->uploadThisImage($_FILES['image'],$path);
					if ($data['arrImage']!=NULL) {
						
					} else {
						$data = false;
					}
				} else {
					$data = false;
				}
			} else {
				$data = false;
			}
			
			$result = $this->userHelper->saveImageCover($data);
			if($result) {			
				$data = true;
			} else {
				$data = false;
			}
			
		} 
		return $data;
	}
	
	function ajax(){
		$needs = $this->_request("needs");
		$contentid = intval($this->_request("contentid"));		
		$start = intval($this->_request("start"));
		$noteid = intval($this->_p("noteid"));

		if($needs=="contentgallery") $data =  $this->contentHelper->getMygallery($start,$limit=9);
		if($needs=="content") $data =  $this->contentHelper->getListSongs($start);
		if($needs=="comment") $data =  $this->contentHelper->getComment($contentid,false,$start);
		if($needs=="hapusmygallery") $data =  $this->contentHelper->hapusmygallery();
		if($needs=="inbox-trash") $data = $this->newsHelper->trashInbox($noteid);
		if($needs=="inbox-news-letter") $data = $this->newsHelper->saveinboxtime();	
		
		if($needs=="inbox-read") $data = $this->newsHelper->inboxread($noteid);	
		if($needs=="inbox-data-json") $data = $this->newsHelper->getInboxUser($start);	
		if($needs=="friends-list") 	$data = $this->userHelper->getFriends(true,16);	
		
		return $data;
	}
}
?>