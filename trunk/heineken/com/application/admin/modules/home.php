<?php
class home extends App{
	
	function beforeFilter(){
		global $LOCALE,$CONFIG;
		$this->contentHelper = $this->useHelper('contentHelper');
		$this->entourageHelper = $this->useHelper('entourageHelper');
		$this->contentHelper = $this->useHelper('contentHelper');
		$this->activityHelper = $this->useHelper('activityHelper');
		$this->searchHelper  = $this->useHelper('searchHelper');
		$this->messageHelper = $this->useHelper('messageHelper');
		$this->userHelper = $this->useHelper('userHelper');
		$this->uploadHelper = $this->useHelper('uploadHelper');
		$this->assign('basedomain', $CONFIG['BASE_DOMAIN']);
		$this->assign('assets_domain', $CONFIG['ASSETS_DOMAIN_WEB']);		
		$this->assign('locale', $LOCALE[1]);
		$this->assign('user', $this->user);
		$data = $this->userHelper->getUserProfile(); 	
		$this->View->assign('userprofile',$data);
		
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
		$this->assign('startdate',$this->_p('startdate'));
		$this->assign('enddate',$this->_p('enddate'));
		$this->assign('search',$this->_p('search'));
		$this->View->assign('my_profile_box',$this->setWidgets('my_profile_box'));
		$this->View->assign('lates_engagement_box',$this->setWidgets('lates_engagement_box'));
		$this->View->assign('inbox_box',$this->setWidgets('inbox_box'));
		$this->View->assign('line_chart',$this->setWidgets('line_chart'));
		$this->View->assign('medium_banner',$this->setWidgets('medium_banner'));		
		if(strip_tags($this->_g('page'))=='home') $this->log('surf','home');
		return $this->View->toString(TEMPLATE_DOMAIN_WEB .'/home.html');		
	}
	
	
	function profileDetail(){
	$this->View->assign('profile_detail',$this->setWidgets('profile_detail'));
	return $this->View->toString(TEMPLATE_DOMAIN_WEB .'widgets/profile-detail.html');
	}
	
	function profileDetailEdit(){
		global $CONFIG;
		if($this->_p('token')){
			$data = $this->uploadHelper->uploadThisImage($files=$_FILES['img'],$path=$CONFIG['LOCAL_PUBLIC_ASSET'].'user/photo/');
			if ($data){
				$saved = $this->userHelper->updateUserImageProfile($data['arrImage']['filename']);
				if ($saved)$this->View->assign('status', 1);
			}
			
			// return false;
		}
		
		
		$this->View->assign('profile_edit',$this->setWidgets('profile_edit'));
		return $this->View->toString(TEMPLATE_DOMAIN_WEB ."widgets/profile-edit.html");
	}
	
	function entourageList(){
		$this->View->assign('entourage_list',$this->setWidgets('entourage_list'));
		return $this->View->toString(TEMPLATE_DOMAIN_WEB .'widgets/entourage-list.html');
	}
	
	function entourageDetail(){
		$this->View->assign('entourage_detail',$this->setWidgets('entourage_detail'));
		return $this->View->toString(TEMPLATE_DOMAIN_WEB .'widgets/entourage-detail.html');
	}
	
	function ajax(){
		if($this->_p('action')=="a360activity") {
			$maxrecord = 2;
			$start = intval($this->_p('start'));
			$data = $this->activityHelper->getA360activity($start,$maxrecord);
			print json_encode($data['content']); exit;
		}
	}	
}
?>