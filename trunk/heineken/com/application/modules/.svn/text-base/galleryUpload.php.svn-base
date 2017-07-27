<?php
class galleryUpload extends App{
	
	function beforeFilter(){
		global $LOCALE,$CONFIG;
		$this->assign('basedomain',$CONFIG['BASE_DOMAIN']);
		$this->assign('assets_domain', $CONFIG['ASSETS_DOMAIN_WEB']);
		$this->uploadHelper = $this->useHelper('uploadHelper');
		$this->userHelper = $this->useHelper('userHelper');		
		$this->loadProfile = $this->userHelper->getUserProfile();
		if($this->loadProfile['isAdmin']!=1){
			sendRedirect("{$CONFIG['BASE_DOMAIN']}home");
			$this->assign("msg","Please wait...");
			return $this->View->toString(TEMPLATE_DOMAIN_WEB .'/login_message.html');
			die();
		}
	}
	
	function main(){
		if($this->loadProfile['isAdmin']==1){
			if ($_FILES[csv][size] > 0) { 

			    //get the csv file 
			    $file = $_FILES[csv][tmp_name]; 
			   
			    $handle = fopen($file,"r");
			     
			    //loop through the csv file and insert into database 
			    do { 
			       $rs[]=$data;
			    } while ($data = fgetcsv($handle,1000,",","'")); 
			  
			    foreach ($rs as $key => $value) {
			    	$sql="SELECT * FROM hnk_member WHERE email='{$value[1]}' LIMIT 1";
			    	
			    	if($this->fetch($sql)){
			    		$sql="UPDATE hnk_member
							SET phone={$value[2]},user_type=4
							WHERE email='{$value[1]}'";
						$this->query($sql);
			    	}else{
			    		$sql="INSERT INTO hnk_member (email,phone,user_type)
								VALUES ('{$value[1]}',{$value[2]},4)";
						$this->query($sql);
			    	}
			    	
			    	$upload = $this->uploadHelper->resizeThisImage($value[3]);

			    	$this->View->assign('msg',$upload);
			    }
			} 
			
			if(strip_tags($this->_g('page'))=='galleryUpload') $this->log('surf','gallery_upload');
			return $this->View->toString(TEMPLATE_DOMAIN_WEB .'/galleryUpload.html');	
		}	

	}

	function custom(){
		if($this->loadProfile['isAdmin']==1){

			$upload = $this->uploadHelper->manualResizeImage();
			$this->View->assign('msg',$upload);
			if(strip_tags($this->_g('page'))=='galleryUpload') $this->log('surf','gallery_manual_resize');
			return $this->View->toString(TEMPLATE_DOMAIN_WEB .'/galleryUpload.html');
		}
	}
}
?>