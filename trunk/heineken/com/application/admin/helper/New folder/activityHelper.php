<?php 
global $ENGINE_PATH;
include_once $ENGINE_PATH."Utility/Mobile_Detect.php";
class activityHelper {

	var $uid;
	var $osDetect;
	
	function __construct($apps){
		global $logger,$CONFIG;
		$this->logger = $logger;

		$this->apps = $apps;
		if($this->apps->isUserOnline())  {
			if(is_object($this->apps->user)) $this->uid = intval($this->apps->user->id);
			
		}
		
		if(isset($_SESSION['lid'])) $this->lid = intval($_SESSION['lid']);
		else $this->lid = 1;
		if($this->lid=='')$this->lid=1;
		$this->server = intval($CONFIG['VIEW_ON']);
		$this->osDetect = new Mobile_Detect;
		
	}
	
	
	
	function getA360Pagesactivity($start=0,$limit=2,$user=false){
		GLOBAL $COPY;
		
		$socialinteractid = array(4,5);		
		$data['total'] = false;
		$data['content'] = false;
		
		$bandMember = $this->apps->bandHelper->getMember();
		$qUser = "";
		if($bandMember) {
			foreach ($bandMember['result'] as $k => $v) {
				$arrMemberId[] = $v['frienddetail']['id'];
			}
			$strMemberId = implode(',',$arrMemberId);
			$qUser = " AND user_id IN ({$strMemberId}) ";
		} else return false;
		$sql = "SELECT count(*) total FROM tbl_activity_log WHERE action_id NOT IN (1,6,50) {$qUser} ORDER BY id DESC";
		$total = $this->apps->fetch($sql);
		
		if(!$total) return false;
		
		$sql = "SELECT * FROM tbl_activity_log WHERE action_id NOT IN (1,6,50) {$qUser} ORDER BY id DESC LIMIT {$start},{$limit}";
		$qData = $this->apps->fetch($sql,1);
		if(!$qData) return false;
		foreach($qData as $key => $val){
			//get userid
			$userid[] = $val['user_id'] ;
			//get action value
			$actionid[] = $val['action_id'];
			//get content id
			$arrActionValue = false;
			
			if(in_array($val['action_id'],$socialinteractid)) {
				$qData[$key]['otheruserid'] = intval($val['action_value']);
				$qData[$key]['contentid'] = false;
			}else {
				$qData[$key]['otheruserid'] = false;
				$qData[$key]['contentid'] = intval($val['action_value']);
			}
			
			$contentid[] = $qData[$key]['contentid'];
			$otheruserid[] = $qData[$key]['otheruserid'];
			
		}
		
		if($contentid){
			
			$strcontentid = implode(',',$contentid);
			
			//get content
			$sql = "SELECT * FROM athreesix_news_content WHERE id IN ({$strcontentid}) LIMIT {$limit}";
		
			$qSData = $this->apps->fetch($sql,1);
			if($qSData){
				foreach($qSData as $val){
					$arrContent[$val['id']] = $val;
				}
			}else $arrContent = false;
			if($arrContent){
				$arrContent = $this->apps->contentHelper->getStatistictArticle($arrContent);
			}			
		}
		
		if($otheruserid){
			 $strotheruserid = implode(',',$otheruserid);
			 //get content
			 $sql = "SELECT id,name,nickname,img,small_img FROM social_member WHERE id IN ({$strotheruserid}) LIMIT {$limit}";
			 $qSData = $this->apps->fetch($sql,1);
			if($qSData){
					foreach($qSData as $val){
					$arrOtherUser[$val['id']] = $val;
					}
			}else $arrOtherUser = false;
		} 
		

		
		if($userid){
			$struserid = implode(',',$userid);
			
			//get content
			$sql = "SELECT id,name,nickname,img,small_img FROM social_member WHERE id IN ({$struserid}) LIMIT {$limit}";
			$qSData = $this->apps->fetch($sql,1);
			if($qSData){
				foreach($qSData as $val){
					$arrUserid[$val['id']] = $val;
				}
			}else $arrUserid = false;			
		}
		
		if($actionid){
			$stractionid = implode(',',$actionid);
			
			//get content
			$sql = "SELECT * FROM tbl_activity_actions WHERE id IN ({$stractionid}) LIMIT {$limit}";
			$qSData = $this->apps->fetch($sql,1);
			if($qSData){
				foreach($qSData as $val){
					$arrActionid[$val['id']] = $val;
				}
			}else $arrActionid = false;			
		}
		//merge it

		foreach($qData as $key => $val){
			
				if(in_array($val['action_id'],$socialinteractid)) {
					if($arrOtherUser ){
						if(array_key_exists($val['otheruserid'],$arrOtherUser)) $qData[$key]['contentdetail'] = $arrOtherUser[$val['otheruserid']];	
						else $qData[$key]['contentdetail'] = false;
					}else $qData[$key]['contentdetail'] = false;
					$qData[$key]['contentType'] = 'social';
				}else{
					if($arrContent ){
						if(array_key_exists($val['contentid'],$arrContent)) $qData[$key]['contentdetail'] = $arrContent[$val['contentid']];	
						else $qData[$key]['contentdetail'] = false;
					}else $qData[$key]['contentdetail'] = false;
					$qData[$key]['contentType'] = 'content';
				}
				if($arrUserid){	
					if(array_key_exists($val['user_id'],$arrUserid))$qData[$key]['userdetail'] = $arrUserid[$val['user_id']];	
					else $qData[$key]['userdetail'] = false;
				}else $qData[$key]['userdetail'] = false;
				if($arrActionid){
					if(array_key_exists($val['action_id'],$arrActionid))$qData[$key]['actiondetail'] = $arrActionid[$val['action_id']];	
					else $qData[$key]['actiondetail'] = false;
				}else $qData[$key]['actiondetail'] = false;
			
			
		}
		
		//can be used on views
		foreach($qData as $key => $val){
			// if($val['userdetail']) $notification[] =  $val['userdetail']['name'];
			// else  $notification[] = 'some one';
			if($val['actiondetail']) $notification[] = $val['actiondetail']['activityName'];
			else  $notification[] = 'has do something with';
			if($val['contentdetail']) {
				if($val['contentType']=='social') $notification[] = $val['contentdetail']['name'];
				else $notification[] = $val['contentdetail']['title'];
			}else  $notification[] = str_replace('_',' ',$val['action_value']);
			$qData[$key]['notification'] = implode(' ',$notification);
			$notification = null;
		}
		
		if($qData){
			$data['total'] = intval($total['total']);
			$data['content'] = $qData;
			return $data;
		}else return false;		
	}
	
	function getA360activity($start=0,$limit=2,$user=false){
		GLOBAL $LOCALE;
		
		/*
		$LOCALE[1]['add friends']['news'] = " telah menjadi follower ";
		$LOCALE[1]['add comments']['news'] = " berkomentar di ";
		$LOCALE[1]['add favorite']['news'] = " telah memfavoritkan ";
		$LOCALE[1]['uploads']['news'] = " telah mem-upload karya barunya";
		$LOCALE[1]['attending']['news'] = " berencana untuk datang ke ";
		$LOCALE[1]['playmusic']['news'] = " menyimak ";
		$LOCALE[1]['add cover']['news'] = " telah memperbaharui foto cover-nya ";
		$LOCALE[1]['update profile']['news'] = " telah memperbaharui foto profilnya ";
		$LOCALE[1]['addplaylist']['news'] = " punya playlist baru! ";
		$LOCALE[1]['join contest']['news'] = " telah ikut berpartisipasi di ";
		$LOCALE[1]['add new thread']['news'] = " telah memulai sebuah thread baru di Forum A360 ";
		$LOCALE[1]['read article']['news'] = " telah membaca sebuah article ";
		*/
		
		$data['total'] = false;
		$data['content'] = false;
		
		$activityIDarr = false;
		$theactivity = false;
		$qData = false;
		$articleActivity = "'add comments','add favorite','add new thread','uploads','attending','add cover','addplaylist','join contest','read article'";
		$socialActivity = "'add friends','playmusic','update profile'";
		
		$theactivity = $this->apps->activityHelper->getactivitytype($articleActivity);	
		/* get article of user */
		if($theactivity) {		
			$articleActivitID = implode(',',$theactivity['id']);
		}else $articleActivitID = false;
		/* get activity social id */	
		$socialactivitydata = $this->apps->activityHelper->getactivitytype($socialActivity);
		if($socialactivitydata) {
			$socialActivityID = implode(',',$socialactivitydata['id']);
		}else $socialActivityID = false;
		
		$activityIDarr = array($articleActivitID,$socialActivityID);
		
		if($activityIDarr) {
		
			$activityId = implode(',',$activityIDarr);
			
			if($user) {
				if(strip_tags($this->apps->_g('page'))=='my') $qUser = " AND user_id = {$this->uid} ";
				else {
					$uid = intval($this->apps->_g('uid'));
					if($uid!=0) $qUser = " AND user_id = {$uid} ";
					else $qUser = " ";
				}
					// pr($qUser);
			}else $qUser = " ";
			
			$sql = "SELECT count(*) total FROM tbl_activity_log WHERE action_id IN ({$activityId})  {$qUser} ORDER BY id DESC ";		
			$total = $this->apps->fetch($sql);	
			if(!$total) return false;
			
			$sql = "SELECT *,IF ( action_id IN ({$socialActivityID}) , 'social' , 'content' ) typeofnotification FROM tbl_activity_log WHERE action_id IN ({$activityId})  {$qUser} ORDER BY id DESC LIMIT {$start},{$limit}";		
			$qData = $this->apps->fetch($sql,1);
			
			// pr($sql);
			if(!$qData) return false;
			
			$contentid = false;
			$otheruserid = false;
			
			foreach($qData as $key => $val){
				//get userid
				$userid[] = $val['user_id'] ;
				//get action value
				$actionid[] = $val['action_id'];
				//get content id
				$arrActionValue = false;
				
				if($val['typeofnotification'] == 'social' )	$otheruserid[] = intval($val['action_value']);
				else $contentid[] = intval($val['action_value']);
			}
			
			if($contentid) $arrContent = $this->getContentData($contentid);
			else $arrContent = false;
			
			if($otheruserid) $arrOtherUser = $this->getsocialData($otheruserid);
			else $arrOtherUser = false;
			
			if($userid) $arrUserid = $this->getsocialData($userid);
			else $arrUserid = false;
					
			if($actionid){
				$stractionid = implode(',',$actionid);
				
				//get content
				$sql = "SELECT * FROM tbl_activity_actions WHERE id IN ({$stractionid}) LIMIT {$limit}";
				$qSData = $this->apps->fetch($sql,1);
				if($qSData){
					foreach($qSData as $val){
						$arrActionid[$val['id']] = $val;
					}
				}else $arrActionid = false;			
			}
			//merge it

			foreach($qData as $key => $val){
				
					if($val['typeofnotification'] == 'social' ){
						if($arrOtherUser ){
							if(array_key_exists($val['action_value'],$arrOtherUser)) $qData[$key]['contentdetail'] = $arrOtherUser[$val['action_value']];	
							else $qData[$key]['contentdetail'] = false;
						}else $qData[$key]['contentdetail'] = false;
						$qData[$key]['contentType'] = 'social';
					}else{
						if($arrContent ){
							if(array_key_exists($val['action_value'],$arrContent)) $qData[$key]['contentdetail'] = $arrContent[$val['action_value']];	
							else $qData[$key]['contentdetail'] = false;
						}else $qData[$key]['contentdetail'] = false;
						$qData[$key]['contentType'] = 'content';
					}
					if($arrUserid){	
						if(array_key_exists($val['user_id'],$arrUserid))$qData[$key]['userdetail'] = $arrUserid[$val['user_id']];	
						else $qData[$key]['userdetail'] = false;
					}else $qData[$key]['userdetail'] = false;
					
					if($arrActionid){
						if(array_key_exists($val['action_id'],$arrActionid))$qData[$key]['actiondetail'] = $arrActionid[$val['action_id']];	
						else $qData[$key]['actiondetail'] = false;
					}else $qData[$key]['actiondetail'] = false;
				
				
			}
			
			//can be used on views
			foreach($qData as $key => $val){
				// if($val['userdetail']) $notification[] =  $val['userdetail']['name'];
				// else  $notification[] = 'some one';
				if($val['actiondetail']) {
					if(array_key_exists($val['actiondetail']['activityName'],$LOCALE[$this->apps->lid])) $activities = $LOCALE[$this->apps->lid][$val['actiondetail']['activityName']]['news'];
					else $activities = false;
					$notification['activities'] = $activities;
					
				}else  $notification[] = 'has do something with';
				
				if($val['contentdetail']) {
					if($val['contentType']=='social') $notification['subjective'] = $val['contentdetail']['name'];
					else $notification['subjective'] = $val['contentdetail']['title'];
				}else  $notification['subjective'] = str_replace('_',' ',$val['action_value']);
				
				// $qData[$key]['notification'] = implode(' ',$notification);
				$qData[$key]['notification'] = $notification;
				$notification = null;
			}
				// pr($qData);
			if($qData){
				$data['total'] = intval($total['total']);
				$data['content'] = $qData;
				return $data;
			}else return false;	
		}else return false;	
	}
	
	
	
	function getsocialData($userid=null){
			if($userid==null) return false;
			$struserid = implode(',',$userid);
			
			//get content
			$sql = "SELECT id,name,nickname,img,small_img FROM social_member WHERE id IN ({$struserid}) LIMIT 10";
			// pr($sql);
			$qSData = $this->apps->fetch($sql,1);
			if($qSData){
				foreach($qSData as $val){
					$arrUserid[$val['id']] = $val;
				}
			}else $arrUserid = false;			
			return $arrUserid;
	}
	
	function getContentData($contentid){
			if($contentid==null) return false;
			$strcontentid = implode(',',$contentid);
			
			//get content
			$sql = "SELECT anc.*,anct.type FROM athreesix_news_content anc 
			LEFT JOIN athreesix_news_content_type anct ON anct.id=anc.articleType
			WHERE anc.id IN ({$strcontentid}) LIMIT 10";
			// pr($sql);
			$qSData = $this->apps->fetch($sql,1);
			if($qSData){
				foreach($qSData as $val){
					$arrContent[$val['id']] = $val;
				}
			}else $arrContent = false;
			if($arrContent){
				$arrContent = $this->apps->contentHelper->getStatistictArticle($arrContent);
			}
			
			return $arrContent;
	}
	
	
	function getactivitytype($dataactivity=null,$id=false){
		if($dataactivity==null)return false;
		if($id) $qAppender = " id IN ({$dataactivity}) ";
		else $qAppender = " activityName IN ({$dataactivity})  ";
		$theactivity = false;
		/* get activity  id */	
		$sql = " SELECT * FROM tbl_activity_actions WHERE {$qAppender} ";

		$qData = $this->apps->fetch($sql,1);
			
		if($qData) {
			foreach($qData as $val){
				$theactivity['id'][$val['id']]=$val['id'];
				$theactivity['name'][$val['id']]=$val['activityName'];
				
			}
		}
		return $theactivity;
	}
}
?>

