<?php
class post extends ServiceAPI{
	
	function beforeFilter(){
		$this->contentHelper = $this->useHelper('contentHelper');
		$this->searchHelper = $this->useHelper('searchHelper');
		$this->entourageHelper = $this->useHelper('entourageHelper');
		$this->uploadHelper = $this->useHelper('uploadHelper');
		$this->messageHelper = $this->useHelper('messageHelper');
		$this->activityHelper = $this->useHelper('activityHelper');
		$this->loginHelper = $this->useHelper('loginHelper');
		global $LOCALE,$CONFIG;
		
		$this->assign('basedomain', $CONFIG['BASE_DOMAIN']);
		$this->assign('assets_domain', $CONFIG['ASSETS_DOMAIN_WEB']);
		$this->assign('user',$this->user);
		$this->assign('tokenize',gettokenize(5000*60,$this->user->id));
		$this->assign('pages', strip_tags($this->_g('page')));
		$this->assign('locale', $LOCALE[1]);		
		$this->dbshema = "athreesix";
	}
	
	function main(){
		return false;
	}
	
	function upload(){
		global $CONFIG,$LOCALE,$logger;
		$username = ucwords($this->user->name);
		$type = intval($this->_p('type'));
		$logger->log(" type of post : ".$type);
		$logger->log(" type of uploads : ".@$this->_p('upload'));
		$logger->log(" files mime  : ".json_encode(@$_FILES['image']));
		
		if(strip_tags($this->_p('upload'))=='timeline') {
				$logger->log(" uses uploads : timeline");
				$result = $this->contentHelper->addUploadImage(false,$type);
						if($result) {
							$this->log('uploads',$this->getLastInsertId());
							$data = true;
						} else {
							$data = false;
						}					
		} else {
			$data = false;
		}
	
		
		
		if(strip_tags($this->_p('upload'))=='image') {
			$logger->log(" uses uploads : image");
			$path = $CONFIG['LOCAL_PUBLIC_ASSET']."article/";
			
			if (isset($_FILES['image'])&&$_FILES['image']['name']!=NULL) {
				if (isset($_FILES['image'])&&$_FILES['image']['size'] <= 20000000) {
					$data = $this->uploadHelper->uploadThisImage($_FILES['image'],$path);
						$logger->log(json_encode($data));
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
			
			$result = $this->contentHelper->addUploadImage($data,$type);
			if($result) {
				$this->log('uploads',$this->getLastInsertId());
				$data = true;
			} else {
				$data = false;
			}
			
		} 
		
		if(strip_tags($this->_p('upload'))=='video') {
			$logger->log(" uses uploads : video");
			$path = $CONFIG['LOCAL_PUBLIC_ASSET']."article/video/";
			if (isset($_FILES['video'])&&$_FILES['video']['name']!=NULL) {
				if (isset($_FILES['video'])&&$_FILES['video']['size'] <= 2000000) {
					$data = $this->uploadHelper->uploadThisVideo($_FILES['video'],$path);
					if ($data['arrVideo']!=NULL) {
						
					} else {
						$data = false;
					}
				} else {
					$data = false;
				}
			} else {
				$data = false;			
			}
						
			if ($type) {
		
				$data = $this->contentHelper->addUploadImage($data,$type);
				$this->log('uploads',$this->getLastInsertId());
							
			} else $data = false;			
		}
		
		print json_encode($data);exit;
	}

		
	function getJenis(){
		$valJenis = intval($this->_p('jenis_info'));
		print json_encode($valJenis);exit;
	}
	
	function ajaxpost($data){
		print json_encode($data);exit;
	}
	
	function plan(){
		global $CONFIG,$LOCALE;
		$edit = $this->_p('edit');
		if($edit=='do'){
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
				return true;
			}else return false;
		}
		return false;
	}
	
}
?>