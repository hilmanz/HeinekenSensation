<?php
class plan extends App{

	function beforeFilter(){
		global $LOCALE,$CONFIG;
		$this->contentHelper = $this->useHelper('contentHelper');
		$this->searchHelper = $this->useHelper('searchHelper');
		$this->entourageHelper = $this->useHelper('entourageHelper');
		$this->uploadHelper = $this->useHelper('uploadHelper');
		$this->messageHelper = $this->useHelper('messageHelper');
		$this->activityHelper = $this->useHelper('activityHelper');
		
		$this->assign('basedomain', $CONFIG['BASE_DOMAIN']);
		$this->assign('assets_domain', $CONFIG['ASSETS_DOMAIN_WEB']);
		$this->assign('locale', $LOCALE[1]);		
		$this->assign('pages', strip_tags($this->_g('page')));
		$this->assign('user', $this->user);
		$this->assign('users', $this->user);
		// pr($this->user);
		$this->assign('social',$this->userHelper->getrecepient());
		$this->assign('branddetail',$this->userHelper->getrecepient('branddetail'));
		$this->assign('areadetail',$this->userHelper->getrecepient('areadetail'));
		$this->assign('pldetail',$this->userHelper->getrecepient('pldetail'));
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
		
		
		$data = $this->userHelper->getUserProfile(); 		
		$this->View->assign('userprofile',$data);
		$brandusing = true;
		if($this->user->leaderdetail->type==4) $brandusing = false;
		$articlelist = $this->contentHelper->getArticleContent(null,4,4,array(0,3),"plan",false,false,false,true,false,$brandusing);
		$this->View->assign('notification',$articlelist);
		// pr($articlelist);
		
		$this->executor=array(1,2);
		$this->planner=array(1,2,3,4,5);
		$this->approver=array(4);
		
		$this->assign('search',strip_tags($this->_p('search')));
		$this->assign('category',strip_tags($this->_p('category')));
		
		$this->assign('plantypes',$this->user->plantypes);

		if(in_array($this->user->leaderdetail->type,$this->approver)) $this->assign('approver',true);
		if(in_array($this->user->leaderdetail->type,$this->planner)) $this->assign('planner',true);
		
		// pr($this->user);
	}
	
	function main(){
		global $CONFIG;
		//exit;
		
		$articlelist = $this->contentHelper->getArticleContent(null,50,4,array(0,3),"plan");
		// $brands = strip_tags($this->_p('brand'));
		// $arrBrand = array(3,4,5,6,100);
		// $arrCocreation = array(1,2);
		
		if($articlelist){
		
				// $baplan = array(1);
				// $cocreation = array(2);
				// $brands = array(3,4,5,6);
				// $otherplan = array(100);
				
			$colorpicker[0] = "#FF0000";
			$colorpicker[1] = "#FF9900";
			$colorpicker[2] = "#00FF33";
			$colorpicker[3] = "#FFFF00"; 
			$colorpicker[4] = "#FFFF00";
			$colorpicker[5] = "#FFFF00";
			$colorpicker[5] = "#FFFF00";
			
			$colorpicker[1111] = "#FF0000";
			
			$data['plan']['total'] =intval($articlelist['total']);
			$plan = false;
			// pr($articlelist);
			if ($articlelist['result']) {
				$key=0;
				// pr($articlelist);
				foreach($articlelist['result'] as $nkey => $val){
					
					$plan[$key]['title'] =$val['title']; 
					$plan[$key]['start'] = $val['posted_date'];
					
					$plan[$key]['approver'] = false;
					$plan[$key]['planner'] = true;
					
					if($val['expired_date']!='0000-00-00') $plan[$key]['end'] = $val['expired_date'];
					else $plan[$key]['end'] = $val['posted_date'];
					
					$plan[$key]['color'] = @$colorpicker[$val['author']['pagesdetail']['type']];
					
					if(!in_array($this->user->leaderdetail->type,$this->approver)){
						if($val['authorid']==$this->user->id) {
							if($val['n_status']==0) $plan[$key]['url'] =$CONFIG['BASE_DOMAIN']."plan/edit/".$val['id'];
							else $plan[$key]['url'] =$CONFIG['BASE_DOMAIN']."plan/invite/".$val['id'];
						}else $plan[$key]['url'] =$CONFIG['BASE_DOMAIN']."plan/detail/".$val['id'];
					}else  {
						if($val['authorid']==$this->user->id) $plan[$key]['url'] =$CONFIG['BASE_DOMAIN']."plan/edit/".$val['id'];
						else $plan[$key]['url'] =$CONFIG['BASE_DOMAIN']."plan/approve/".$val['id'];
						
						$plan[$key]['approver'] = true;
					}
					
					if( $val['posted_date']< date("Y-m-d" ) ) {
						$plan[$key]['url'] =$CONFIG['BASE_DOMAIN']."plan/detail/".$val['id'];
						$plan[$key]['color'] = $colorpicker[1111];
					}
					
					if( $val['n_status'] == 1 ) {
						$plan[$key]['url']=$CONFIG['BASE_DOMAIN']."plan/detail/".$val['id'];
						
					}
					$key++;
				}
			}
			$data['plan']['posts'] = $plan;
		}else{
			$data['plan']['total'] = 0;
			$data['plan']['posts']= false;
		}
		// pr($plan);
		// pr($articlelist);exit;
		$this->assign('plandata',$data);
	
		$this->log('surf','plan');
		return $this->View->toString(TEMPLATE_DOMAIN_WEB .'/apps/plan-pages.html');
	}
	
	function create(){
		global $CONFIG;
		$this->assign('social',$this->userHelper->getrecepient());
		
		if(strip_tags($this->_p('upload'))=='simpan') {
			
		
			$path = $CONFIG['LOCAL_PUBLIC_ASSET']."article/";
			$data = false;
			$image = false;
			if (isset($_FILES['image']) && $_FILES['image']['name']!=NULL) {
				if (isset($_FILES['image']) && $_FILES['image']['size'] <= 20000000) {
					$image = $this->uploadHelper->uploadThisImage($_FILES['image'],$path);
					
				}
			}
			
			$path = $CONFIG['LOCAL_PUBLIC_ASSET']."attachment/";
			$file = false;
			if (isset($_FILES['attachment']) && $_FILES['attachment']['name']!=NULL) {
				if (isset($_FILES['attachment']) && $_FILES['attachment']['size'] <= 20000000) {
					$file = $this->uploadHelper->uploadThisFile($_FILES['attachment'],$path);
					
				}
			}
			
			if($image&&$file) $data = array_merge($image,$file);
			else{
				if($image) $data = $image;
				if($file) $data = $file;
			}
			
			$result = $this->contentHelper->addUploadImage($data);
				
			if($result) {
				$this->log('uploads',$this->getLastInsertId());
				sendRedirect($CONFIG['BASE_DOMAIN']."plan");
					return $this->out(TEMPLATE_DOMAIN_WEB . '/login_message.html');
				exit;
			}
		}
		return $this->View->toString(TEMPLATE_DOMAIN_WEB .'/widgets/create-plan.html');
	}
	
	function edit(){
		global $CONFIG;
		$do = $this->_p('do');
		$id = $this->_request('id');
		$this->assign('id',$id);
		
		$articlelist = $this->contentHelper->getDetailArticle();
		// pr($articlelist);exit;
		if($articlelist){
			$datetimes = explode(' ',@$articlelist['result'][0]['posted_date']);
				if(is_array($datetimes)){
					$this->assign('dates',$datetimes[0]);
					$this->assign('times',$datetimes[1]);
				}
			foreach($articlelist['result'][0] as $key => $val){
		
				$this->assign($key,$val);
			}
		}
		
		if($do=='edit'){
			$path = $CONFIG['LOCAL_PUBLIC_ASSET']."article/";
			$data = false;
			$image = false;
			if (isset($_FILES['image']) && $_FILES['image']['name']!=NULL) {
				if (isset($_FILES['image']) && $_FILES['image']['size'] <= 20000000) {
					$image = $this->uploadHelper->uploadThisImage($_FILES['image'],$path);
					}
			}
				
			$path = $CONFIG['LOCAL_PUBLIC_ASSET']."attachment/";
			$file = false;
			if (isset($_FILES['attachment']) && $_FILES['attachment']['name']!=NULL) {
				if (isset($_FILES['attachment']) && $_FILES['attachment']['size'] <= 20000000) {
					$file = $this->uploadHelper->uploadThisFile($_FILES['attachment'],$path);
					
				}
			}
			
			if($image&&$file) $data = array_merge($image,$file);
			else{
				if($image) $data = $image;
				if($file) $data = $file;
			}
			
			$result = $this->contentHelper->editContentArticle($data);
			if($result) {
				$this->log('uploads',$this->getLastInsertId());
				sendRedirect($CONFIG['BASE_DOMAIN']."plan");
					return $this->out(TEMPLATE_DOMAIN_WEB . '/login_message.html');
				exit;
			}
		}
		// pr($articlelist);
		return $this->View->toString(TEMPLATE_DOMAIN_WEB .'/widgets/edit-plan.html');
	}
	
	
	function invite(){
		global $CONFIG;
		$do = $this->_p('do');
		$id = $this->_request('id');
		$this->assign('id',$id);
		
		$articlelist = $this->contentHelper->getDetailArticle();
		// pr($articlelist);exit;
		if($articlelist){
			$datetimes = explode(' ',@$articlelist['result'][0]['posted_date']);
				if(is_array($datetimes)){
					$this->assign('dates',$datetimes[0]);
					$this->assign('times',$datetimes[1]);
				}
			foreach($articlelist['result'][0] as $key => $val){
		
				$this->assign($key,$val);
			}
		}
		
		if($do=='edit'){
			$path = $CONFIG['LOCAL_PUBLIC_ASSET']."article/";
			$data = false;
			if (isset($_FILES['image']) && $_FILES['image']['name']!=NULL) {
				if (isset($_FILES['image']) && $_FILES['image']['size'] <= 20000000) {
					$data = $this->uploadHelper->uploadThisImage($_FILES['image'],$path);
					}
			}
			$result = $this->contentHelper->editContentArticle($data);
			if($result) {
				$this->log('uploads',$this->getLastInsertId());
				sendRedirect($CONFIG['BASE_DOMAIN']."plan");
					return $this->out(TEMPLATE_DOMAIN_WEB . '/login_message.html');
				exit;
			}
		}
		return $this->View->toString(TEMPLATE_DOMAIN_WEB .'/widgets/invite-plan.html');
	}
	
	
	function detail(){
		global $CONFIG;
		$do = $this->_p('do');
		$id = $this->_request('id');
		$this->assign('id',$id);
		
		$articlelist = $this->contentHelper->getDetailArticle();
		// pr($articlelist);exit;
		if($articlelist){
			$datetimes = explode(' ',@$articlelist['result'][0]['posted_date']);
				if(is_array($datetimes)){
					$this->assign('dates',$datetimes[0]);
					$this->assign('times',$datetimes[1]);
				}
			foreach($articlelist['result'][0] as $key => $val){
		
				$this->assign($key,$val);
				
			}
		}
		// pr($articlelist);
		
		return $this->View->toString(TEMPLATE_DOMAIN_WEB .'/widgets/detail-plan.html');
	}
	
	function approve(){
		// pr('masuk');
		if(!in_array($this->user->leaderdetail->type,$this->approver)) return false;
		
		global $CONFIG;
		$do = $this->_p('do');
		$id = $this->_request('id');
		$this->assign('id',$id);
		
		$articlelist = $this->contentHelper->getDetailArticle();
		// pr($articlelist);
		if($articlelist){
			$datetimes = explode(' ',@$articlelist['result'][0]['posted_date']);
				if(is_array($datetimes)){
					$this->assign('dates',$datetimes[0]);
					$this->assign('times',$datetimes[1]);
				}
			foreach($articlelist['result'][0] as $key => $val){
		
				$this->assign($key,$val);
			}
		}
		
		if($do=='edit'){
			$path = $CONFIG['LOCAL_PUBLIC_ASSET']."article/";
			$data = false;
			if (isset($_FILES['image']) && $_FILES['image']['name']!=NULL) {
				if (isset($_FILES['image']) && $_FILES['image']['size'] <= 20000000) {
					$data = $this->uploadHelper->uploadThisImage($_FILES['image'],$path);
					}
			}
			$result = $this->contentHelper->editContentArticle($data);
			if($result) {
				$this->log('uploads',$this->getLastInsertId());
				sendRedirect($CONFIG['BASE_DOMAIN']."plan");
					return $this->out(TEMPLATE_DOMAIN_WEB . '/login_message.html');
				exit;
			}
		}
		return $this->View->toString(TEMPLATE_DOMAIN_WEB .'/widgets/approve-plan.html');
	}
	
	function reason(){
	
		if(!in_array($this->user->leaderdetail->type,$this->approver)) return false;
		
		
		global $CONFIG;
		$do = $this->_p('do');
		$id = $this->_request('id');
		$this->assign('id',$id);
		
		$articlelist = $this->contentHelper->getDetailArticle();
		
		if($articlelist){
			$datetimes = explode(' ',@$articlelist['result'][0]['posted_date']);
				if(is_array($datetimes)){
					$this->assign('dates',$datetimes[0]);
					$this->assign('times',$datetimes[1]);
				}
			foreach($articlelist['result'][0] as $key => $val){
		
				$this->assign($key,$val);
			}
		}
		
		if($do=='edit'){
			$path = $CONFIG['LOCAL_PUBLIC_ASSET']."article/";
			$data = false;
			if (isset($_FILES['image']) && $_FILES['image']['name']!=NULL) {
				if (isset($_FILES['image']) && $_FILES['image']['size'] <= 20000000) {
					$data = $this->uploadHelper->uploadThisImage($_FILES['image'],$path);
					}
			}
			$result = $this->contentHelper->editContentArticle($data);
			if($result) {
				$this->log('uploads',$this->getLastInsertId());
				sendRedirect($CONFIG['BASE_DOMAIN']."plan");
					return $this->out(TEMPLATE_DOMAIN_WEB . '/login_message.html');
				exit;
			}
		}
		return $this->View->toString(TEMPLATE_DOMAIN_WEB .'/widgets/reject-reason-plan.html');
	}
	
}
?>