<?php
global $ENGINE_PATH;
include_once $ENGINE_PATH."Utility/facebook/facebook.php";
class FacebookHelper {
	var $fb;
	var $user_id;
	var $me;
	var $_access_token;
	var $logger;
	function __construct($apps=null){
		global $FB,$logger;
		$this->apps = $apps;
		$this->logger = $logger;
		$this->fb = new Facebook(array(
			  'appId'  => $FB['appID'],
			  'secret' => $FB['appSecret'],
			));
		
	}
	
	function init(){
		global $FB,$CONFIG,$thisMobile;
			
	
			
			$this->logger->log('[FACEBOOK] : access API ');


			$loginReqUri = "{$CONFIG['BASE_DOMAIN']}login/fb";
			
			try{
				if($this->fb->getUser()){
					try{
						$isFan = $this->fb->api("/me/likes?target_id={$FB['heinekenPageID']}");

						if(sizeof($isFan['data'][0])>0){
							$data['isLiked']= $isFan['data'][0]['id'];
						}
					
						$data['userProfile']= $this->fb->api('/me');
			
						$params['next'] = "{$CONFIG['BASE_DOMAIN']}logout.php";
						
						$data['urlConnect'] =$this->fb->getLogoutUrl($params);

						return array('status'=>1,'return'=>$data);
					}catch (Exception $e){
					
						$this->logger->log('[FACEBOOK] [LOGIN ] : failed to login , get url login ');
							
						$params = array('scope' => 'email,user_status,user_activities,publish_actions,user_likes,read_friendlists,user_about_me,user_location,publish_stream,user_relationships,read_stream','redirect_uri'=>"{$loginReqUri}");
						$data['urlConnect'] =$this->fb->getLoginUrl($params);
						return array('status'=>0,'return'=>$data);
					}		
								
				}else {
				
					$this->logger->log('[FACEBOOK] : get login url ');
					
					$params = array('scope' => 'email,user_status,user_activities,publish_actions,user_likes,read_friendlists,user_about_me,user_location,publish_stream,user_relationships,read_stream','redirect_uri'=>"{$loginReqUri}");
					$data['urlConnect'] =$this->fb->getLoginUrl($params);
					return array('status'=>0,'return'=>$data);
				}
			}catch (Exception $e){
			
					$this->logger->log('[FACEBOOK] : get login url , failed authorize ');
					
						$params = array('scope' => 'email','redirect_uri'=>"{$loginReqUri}");
						$data['urlConnect'] =$this->fb->getLoginUrl($params);
						return array('status'=>0,'return'=>$data);
			}	
	}

	function checkUser($dataFB,$isLiked){
		global $FB,$CONFIG;
		$user=array();		
		//check if user did like the heineken fan page
		$user['isFan']=$isLiked;
		
		//pr($sql);exit;

		//check if user came the heineken shows
		$sql="SELECT * FROM hnk_member WHERE email = '{$dataFB['email']}' LIMIT 1";
		
		$rs = $this->apps->fetch($sql);
		
		if(!$rs){ //new user
			$gender=$dataFB['gender'];
			switch($gender){
				case 'male':
					$gender = 0;
					break;
				case 'female':
					$gender = 1;
					break;
				default:
					$gender = 2;
			}


				$qry="INSERT INTO hnk_member (fb_id,first_name,last_name,middle_name,gender,email,location,user_type)
					VALUES (
							{$dataFB['id']}, 
							'{$dataFB['first_name']}', 
							'{$dataFB['last_name']}', 
							'{$dataFB['middle_name']}', 
							{$gender}, 
							'{$dataFB['email']}', 
							'{$dataFB['location']['name']}',
							1
							)";
			
			
			
			if($this->apps->query($qry)){
				$user['status']=1;
			}
		}else{
			if($rs['user_type']==4){
				$gender=$dataFB['gender'];
				switch($gender){
					case 'male':
						$gender = 0;
						break;
					case 'female':
						$gender = 1;
						break;
					default:
						$gender = 2;
				}
				$qry="UPDATE hnk_member
						SET fb_id={$dataFB['id']},
							first_name='{$dataFB['first_name']}',
							middle_name='{$dataFB['middle_name']}',
							last_name='{$dataFB['last_name']}',
							gender={$gender},
							location='{$dataFB['location']['name']}',
							user_type=2
						WHERE email='{$dataFB['email']}'";
				//pr($qry);exit;
				if($this->apps->query($qry)){
					$user['status']=2;
				}
			}else{
				$user['status']=$rs['user_type'];
			}
		}

		if($user['isFan']){
			$sql="UPDATE hnk_member
					SET isLike=1
					WHERE fb_id={$dataFB['id']}";
			$this->apps->query($sql);
		}else{
			$sql="UPDATE hnk_member
					SET isLike=0
					WHERE fb_id={$dataFB['id']}";
			$this->apps->query($sql);
		}
		return $user;
	}

	function isFan($fbID){
		global $FB,$CONFIG;

		$isFan = $this->fb->api("/me/likes?target_id={$FB['heinekenPageID']}");

		
		
		//pr($this->fb);
		//check if user did like the heineken fan page
	
		
		if(sizeof($isFan['data'][0])>0){
			$sql="UPDATE hnk_member
					SET isLike=1
					WHERE fb_id={$fbID}";
			$this->apps->query($sql);
			return true;
		}else{
			$sql="UPDATE hnk_member
					SET isLike=0
					WHERE fb_id={$fbID}";
			$this->apps->query($sql);
			return 0;
		}
	}
	
}
?>