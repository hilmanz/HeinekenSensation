<?php
class register extends App{
		
	function beforeFilter(){
		global $LOCALE,$CONFIG;
		$this->assign('basedomain', $CONFIG['BASE_DOMAIN']);
		$this->assign('assets_domain', $CONFIG['ASSETS_DOMAIN_WEB']);
		$this->contentHelper = $this->useHelper('contentHelper');
		$this->permissionHelper = $this->useHelper('permissionHelper');

		$this->searchHelper = $this->useHelper('searchHelper');
		$this->registerHelper = $this->useHelper('registerHelper');
	
	}
	
	function main(){
	
		$res = $this->registerHelper->userlists();
		$this->assign('users',$res);
		return $this->View->toString(TEMPLATE_DOMAIN_WEB .'widgets/register-user-lists.html');
	}
	
	function hirarkies(){
	
		$res = $this->registerHelper->getalluserhirarkies();
		$this->assign('users',$res);
		return $this->View->toString(TEMPLATE_DOMAIN_WEB .'widgets/register-user-hirarkies.html');
	
	}
	
	function doregister(){
		
		$res = $this->registerHelper->userlists();
		return $this->View->toString(TEMPLATE_DOMAIN_WEB .'widgets/create-user.html');
	}
	
	function edit(){
		
		$res = $this->registerHelper->userlists();
		if($res){
			foreach($res as $key => $val){
				$this->assign($key,$val);
			}
		}
		return $this->View->toString(TEMPLATE_DOMAIN_WEB .'widgets/edit-user.html');
	}
	
	function unusers(){
		global $CONFIG;
	
		$res = $this->registerHelper->unusers();
		sendRedirect( $CONFIG['BASE_DOMAIN']."register");
		exit;
	}
	
	function permission(){
		$res = $this->permissionHelper->getUserListPermission();
		$this->assign('users',$res);
		return $this->View->toString(TEMPLATE_DOMAIN_WEB .'widgets/register-user-permission-lists.html');
	}
	
	function seepermission(){
		$pagetype = $this->_request('uid');
		$this->assign('pagetype',$pagetype);
		$res = $this->permissionHelper->getPermissionList();
		$this->assign('users',$res);
		return $this->View->toString(TEMPLATE_DOMAIN_WEB .'widgets/register-user-permission.html');
	}
	
	
	
	function addpermissionuser(){
	
		$modules = $this->permissionHelper->getModules();
		$this->assign('modules',$modules);
		$pagetype = $this->_request('uid');
		$this->assign('pagetype',$pagetype);
		
		return $this->View->toString(TEMPLATE_DOMAIN_WEB .'widgets/add-permission-user.html');
	}
	

	
	function modulepermissionlist(){
	
		$modules = $this->permissionHelper->getModules();
		$this->assign('modules',$modules);
		
		return $this->View->toString(TEMPLATE_DOMAIN_WEB .'widgets/module-permission-list.html');
	}
	
	function modulespermissionform(){
	
		return $this->View->toString(TEMPLATE_DOMAIN_WEB .'widgets/add-modules.html');
	}
	
	function addmodulespermission(){
		global $CONFIG;
		$res = $this->permissionHelper->addModules();
		sendRedirect( $CONFIG['BASE_DOMAIN']."register/modulepermissionlist");
		exit;
	}
		
	function addpermission(){
		global $CONFIG;
		$res = $this->permissionHelper->addPermission();
		$pagetype = $this->_request('pagetype');
		// pr($pagetype);exit;
		sendRedirect( $CONFIG['BASE_DOMAIN']."register/seepermission/{$pagetype}");
		exit;
	}
	
	function addthispermission(){
		
		$res = $this->permissionHelper->addPermission();
		print json_encode($res);
		exit;
	}
}
?>