<?php 
global $ENGINE_PATH;
include_once $ENGINE_PATH."Utility/Mobile_Detect.php";
class contentHelper {

	var $uid;
	var $osDetect;

	
	function __construct($apps){
		global $logger,$CONFIG;
		$this->logger = $logger;

		$this->apps = $apps;
		if($this->apps->isUserOnline())  {
			if(is_object($this->apps->user)) $this->uid = intval($this->apps->user->id);
			// if(is_object($this->apps->page)) $this->pageid = intval($this->apps->page->id);
			
		}else $this->uid = 0;
		
		if(isset($_SESSION['lid'])) $this->lid = intval($_SESSION['lid']);
		else $this->lid = 1;
		if($this->lid=='')$this->lid=1;
		$this->server = intval($CONFIG['VIEW_ON']);
		$this->osDetect = new Mobile_Detect;
		
		
		$this->dbshema = "beat";
		
		$this->executor=array(1,2);
		$this->planner=array(2,3,4);
		$this->approver=array(4,6,100);
		$this->agency=array(100);
		
		$this->modzeropage=array('plan');	
		
		$this->moderation = 1;
		$this->plantype[0]="";
		$this->plantype[1]="BA";
		$this->plantype[2]="Co-Creation";
		$this->plantype[3]="Brand";
		$this->plantype[4]="Brand";
		$this->plantype[5]="Brand";
		$this->plantype[100]="Brand";
		
		$this->cocreationtypeid = "2";
		$this->brandtypeid = "3,4,5";
		
	}
	
		

	
	function getAuthorProfile($otherid=null,$typeAuthor='admin'){
		if($otherid==null) return false;
		global $CONFIG;
		$sql = "SELECT name, id AS authorid, '' as last_name,'' as pagestype,image as img FROM gm_member WHERE id IN ({$otherid}) LIMIT 10 ";
		if($typeAuthor == 'social' ) $sql = "SELECT name, id AS authorid, last_name,'' as pagestype,img  FROM social_member WHERE id IN ({$otherid}) ";
		if($typeAuthor == 'page' ) $sql = "SELECT name, id AS authorid , '' as last_name,type as pagestype,img FROM my_pages WHERE id IN ({$otherid}) ";
		// pr($sql);
		$data = $this->apps->fetch($sql,1);
		if(!$data) return false;
		
		foreach($data as $key => $val){
		
			if(!is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}user/photo/tiny_{$val['img']}")) $val['img'] = false;
			if($val['img']) $data[$key]['image_full_path'] = $CONFIG['BASE_DOMAIN_PATH']."public_assets/user/photo/tiny_".$val['img'];
			else $data[$key]['image_full_path'] = $CONFIG['BASE_DOMAIN_PATH']."public_assets/user/photo/default.jpg";
					
			$data[$key]['name'] =  $data[$key]['name']." ".$data[$key]['last_name'];
			$arrData[$val['authorid']] = $data[$key];
			
		}
		
		$sql = "
		SELECT 
		pages.name, pages.id ,
		pages.type ,pages.img,
		pages.ownerid ,pagetype.name pagetypename,
		CONCAT(smbrand.name,' ',smbrand.last_name) brandid ,pages.brandsubid,
		CONCAT(smarea.name,' ',smarea.last_name) areaid,pages.otherid
		FROM my_pages pages
		LEFT JOIN my_pages_type pagetype ON pagetype.id=pages.type
		LEFT JOIN social_member smbrand ON smbrand.id=pages.brandid
		LEFT JOIN social_member smarea ON smarea.id=pages.areaid
		WHERE ownerid IN ({$otherid}) ";
	
		$data = $this->apps->fetch($sql,1);
		
		foreach($data as $key => $val){
			if(array_key_exists($val['type'],$this->plantype))$data[$key]['plantype'] = $this->plantype[$val['type']];
			else $data[$key]['plantype'] =  0;
			$pagedata[$val['ownerid']] = $data[$key];
		}
		
		foreach($arrData as $key => $val){
			if(array_key_exists($val['authorid'],$pagedata)) $arrData[$key]['pagesdetail'] = $pagedata[$val['authorid']];
			else $arrData[$key]['pagesdetail'] = false;
			
		}
		
		if(!isset($arrData)) return false;
		return $arrData;
	}
	
	
	
	function getFavoriteUrl($cid=null,$content=2){
		if($cid==null) return false;
		$sql="
		SELECT count(*) total, contentid,url FROM social_bookmark sb 
		WHERE content={$content}
		AND contentid IN ({$cid}) 
		GROUP BY contentid ";
		$qData = $this->apps->fetch($sql,1);
		if(!$qData) return false;
		foreach($qData as $val){
			$arrData[$val['contentid']] = $val['total'];
		}	
		if($arrData) return $arrData;
		return false;
	}
	
	
	function saveFavorite(){
	
		$cid = intval($this->apps->_p('cid'));
		$likes =1;
		$uid = intval($this->uid);
		if($cid!=0 && $uid!=0){
			$sql ="
					INSERT INTO {$this->dbshema}_news_content_favorite (userid,contentid,likes,date,n_status) 
					VALUES ({$uid},{$cid},{$likes},NOW(),1)
					";
			$this->apps->query($sql);
			$this->logger->log($sql);
			if($this->apps->getLastInsertId()>0) return true;
			
		}
		return false;
	}
	
	
	
	
	function getFavorite($cid=null){
		global $CONFIG;
		if($cid==null) $cid = $this->apps->_p('cid');
		if($cid){
			$cidin = " AND contentid IN ({$cid}) ";
		}
			$sql ="
					SELECT  contentid,userid,likes FROM {$this->dbshema}_news_content_favorite WHERE n_status=  1 {$cidin}  
					";
		
				$qData = $this->apps->fetch($sql,1);
				
				if($qData) {
					$this->logger->log("have favorite");
					foreach($qData as $val){
						
						$arrUserid[$val['userid']] = $val['userid'];	
					}
									
					$users = implode(",",$arrUserid);
					
					
					$sql = "SELECT * FROM social_member WHERE id IN ({$users})  AND n_status = 1 ";
					$qDataUser = $this->apps->fetch($sql,1);
					if($qDataUser){
								
						foreach($qDataUser as $val){
							$userDetail[$val['id']]['name'] = $val['name'];
							$userDetail[$val['id']]['img'] = $val['img'];
							if(!is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}user/photo/tiny_{$val['img']}")) $val['img'] = false;
							if($val['img']) $userDetail[$val['id']]['image_full_path'] = $CONFIG['BASE_DOMAIN_PATH']."public_assets/user/photo/tiny_".$val['img'];
							else $userDetail[$val['id']]['image_full_path'] = $CONFIG['BASE_DOMAIN_PATH']."public_assets/user/photo/default.jpg";
				
							
						}
						
						foreach($qData as $key => $val ){						
							if(array_key_exists($val['userid'],$userDetail)) $qData[$key]['userdetail'] = $userDetail[$val['userid']];
							else $qData[$key]['userdetail'] = false;
							$data[$val['contentid']][$val['userid']]= $qData[$key];
						}
						
					
						if($data){
							foreach($data as $key => $val){
								$favoriteData[$key]['total']=count($val);								
								$favoriteData[$key]['users']=$val;
								if(array_key_exists($this->uid,$data[$key])) $favoriteData[$key]['mylikes']=$data[$key][$this->uid];
								else $favoriteData[$key]['mylikes']=false;
								
								
							}
						}
						
							
							return $favoriteData;
						}
				}
		return false;
			
			
	}	
	
	function addComment($cid=null,$comment=null){
	
		if($cid==null) $cid = intval($this->apps->_p('cid'));
		if($comment==null) $comment = $this->apps->_p('comment');
		// $this->logger->log('pre comment '.$cid.' comment: '.$comment );
		if(!$this->uid) return false;
		$uid = intval($this->uid);
			// $this->logger->log('pre comment found uid '.$uid );
		if($cid&&$comment){
			// $this->logger->log('do comment ' );
			if($comment=="") return false;
			// $this->logger->log('save comment '.$comment);
			global $CONFIG;
				//bot system halt
				$sql = "SELECT id,date,count(id) total FROM {$this->dbshema}_news_content_comment WHERE userid={$uid} ORDER BY id DESC LIMIT 1";
				$lastInsert = $this->apps->fetch($sql);
				// $this->logger->log($lastInsert['total']);
				if($lastInsert['total']==0) $divTime = $CONFIG['DELAYTIME']+1;
				else $divTime = strtotime(date("Y-m-d H:i:s")) - strtotime($lastInsert['date']); 
				if($CONFIG['DELAYTIME']==0) $divTime = $CONFIG['DELAYTIME']+1;
				// $this->logger->log(date("Y-m-d H:i:s").' .... '.$lastInsert['date']);
				if($divTime<=$CONFIG['DELAYTIME']) return false; 
				
				$sql ="
						INSERT INTO {$this->dbshema}_news_content_comment (userid,contentid,comment,date,n_status) 
						VALUES ({$uid},{$cid},'{$comment}',NOW(),1)
						";
				$this->apps->query($sql);
				$this->logger->log($sql);
				if($this->apps->getLastInsertId()>0) {
					
					
					return true;
				}
				
		} else $this->logger->log($cid.'|'.$comment);
		// $this->logger->log('failed do comment ');
		return false;	
	}
	
	function setWinnerChallenge(){
		global $CONFIG;
		
		if(intval($this->apps->_p('cid'))==null) return false;
		if(intval($this->apps->_p('cid_user'))==null) return false;
		if(intval($this->apps->_p('userid'))==null) return false;
		$cid = intval($this->apps->_p('cid'));
		$cid_user = intval($this->apps->_p('cid_user'));
		$userid = intval($this->apps->_p('userid'));
		
		if(!$this->uid) return false;
		$uid = intval($this->uid);	
		
		if($cid_user && $userid){
			// GET NILAI POINT
			$point = $this->PointChallenge(null,$cid,"getpoint");
			
			//bot system halt
			$sql = "SELECT id,datetimes,count(id) total FROM my_challenge WHERE userid = {$userid} ORDER BY id DESC LIMIT 1";
			$lastInsert = $this->apps->fetch($sql);
			$this->logger->log($lastInsert['total']);
			if($lastInsert['total']==0) $divTime = $CONFIG['DELAYTIME']+1;
			else $divTime = strtotime(date("Y-m-d H:i:s")) - strtotime($lastInsert['datetimes']);
			if($CONFIG['DELAYTIME']==0) $divTime = $CONFIG['DELAYTIME']+1;
			$this->logger->log(date("Y-m-d H:i:s").' .... '.$lastInsert['datetimes']);
			if($divTime<=$CONFIG['DELAYTIME']) return false;
			
			$sql ="
				INSERT INTO my_challenge (userid,contentid,brandid,datetimes,point,n_status) 
				VALUES ({$userid},{$cid_user},'{$uid}',NOW(),'{$point['prize']}',1)
			";
			// pr($sql);exit;
			$this->apps->query($sql);
			$this->logger->log($sql);
			if($this->apps->getLastInsertId()>0) return true;
			
		} else $this->logger->log($cid.'|'.$uid);
		return false;
	}
	
	function cekChallengeWinner($userid=null,$cid=null){
		global $CONFIG;
		
		if($userid==null) return false;
		if($cid==null) return false;
		if(!$this->uid) return false;
		$uid = intval($this->uid);
		
		$data = false;
		if($userid && $cid){
			$sql = "SELECT * FROM my_challenge WHERE userid = {$userid} AND contentid = {$cid} AND n_status = 1 ORDER BY id DESC LIMIT 1";			
			$data = $this->apps->fetch($sql);
			if ($data) $data = true;
			else $data = false;
		}
		
		return $data;
	}
	
	function getComment($cid=null,$all=false,$start=0,$limit=5,$summary=false){
		// return $cid;
		global $CONFIG;
		if($cid==null) $cid = $this->apps->_request('id');
		
		if(!$summary) if(intval($this->apps->_request('start'))>=0)$start = intval($this->apps->_request('start'));
	
		if($cid){			
			if($all==true) $qAllRecord = "";
			else  $qAllRecord = " LIMIT {$start},{$limit} ";
			if($all==true) $qFieldRecord = " count(*) total , contentid ";
			else  $qFieldRecord = " * ";
			if($all==true) $qGroupRecord = " GROUP BY contentid ";
			else  $qGroupRecord = "  ";
			
			$sql ="	SELECT {$qFieldRecord} FROM {$this->dbshema}_news_content_comment 
					WHERE contentid IN ({$cid}) AND n_status = 1
					{$qGroupRecord}
					ORDER BY date DESC {$qAllRecord}
					";
			// pr($sql);
			$qData = $this->apps->fetch($sql,1);
			
			$this->logger->log($sql);
			if($qData) {
			
				if($all==true) {
					foreach($qData as $val){
						$arrComment[$val['contentid']] = $val['total'];
					}
					return $arrComment;
				}
				
				
				foreach($qData as $val){
					$arrUserid[] = $val['userid'];				
				}
				
				$users = implode(",",$arrUserid);
				
				
				$sql = "SELECT * FROM social_member WHERE id IN ({$users})  AND n_status = 1 ";
				$qDataUser = $this->apps->fetch($sql,1);
				if($qDataUser){
					// $userRate = $this->getUserFavorite($cid,$users);
					$userRate = false;
					
					foreach($qDataUser as $val){
						$userDetail[$val['id']]['name'] = $val['name']." ".$val['last_name'];
						
						$userDetail[$val['id']]['img'] = $val['img'];
						if(!is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}user/photo/{$val['img']}")) $val['img'] = false;
						if($val['img']) $userDetail[$val['id']]['image_full_path'] = $CONFIG['BASE_DOMAIN_PATH']."public_assets/user/photo/".$val['img'];
						else $userDetail[$val['id']]['image_full_path'] = $CONFIG['BASE_DOMAIN_PATH']."public_assets/user/photo/default.jpg";
			
						
					}
					foreach($qData as $key => $val){
						$arrComment[$val['contentid']][$key] = $val;
						if(array_key_exists($val['userid'],$userDetail)){
							$arrComment[$val['contentid']][$key]['name'] = $userDetail[$val['userid']]['name'] ;
							$arrComment[$val['contentid']][$key]['img'] = $userDetail[$val['userid']]['img'] ;
							$arrComment[$val['contentid']][$key]['image_full_path'] = $userDetail[$val['userid']]['image_full_path'] ;
							
							if($userRate){
								if(array_key_exists($val['contentid'],$userRate)) {
									if(array_key_exists($val['userid'],$userRate[$val['contentid']]))$arrComment[$val['contentid']][$key]['favorite'] = $userRate[$val['contentid']][$val['userid']] ; 
									else $arrComment[$val['contentid']][$key]['favorite'] = 0;
								}else $arrComment[$val['contentid']][$key]['favorite'] = 0;
							}else  $arrComment[$val['contentid']][$key]['favorite'] = 0;
						}
					}
				
					$qData = null;
					// pr($arrComment);exit;
					return $arrComment;
				}
			}			
		}
		return false;	
	}
	
	function getCategoryVenue(){
		global $CONFIG;				
		
		$sql = "SELECT * FROM {$this->dbshema}_venue_category ORDER BY name ";
		$qData = $this->apps->fetch($sql,1);
		if($qData){
			return $qData;
		}
		return false;
	}
	
	function getChallengeHashtag($start=0,$limit=5,$tags=null,$type=null){
		global $CONFIG;
		
		if($tags==null) return false;
		if($type==null) return false;
		if(intval($this->apps->_request('start'))>=0)$start = intval($this->apps->_request('start'));
		$tags = strip_tags($tags);
		$limit = intval($limit);
		
		$result['result'] = false;
		$result['total'] = 0;
		
		$tags = rtrim($tags);
		$tags = ltrim($tags);
		
		if(strpos($tags,' ')) $parseTags = explode(' ', $tags);
		else $parseTags = false;
		
		if(is_array($parseTags)) $tags = $tags.'|'.trim(implode('|',$parseTags));
		else  $tags = trim($tags);
	
		if($tags){
			//GET TOTAL ARTICLE BY HASHTAG
			$sql = "SELECT count(*) total FROM {$this->dbshema}_news_content anc  WHERE articleType = {$type} AND tags REGEXP  '{$tags}' AND n_status=1";
			$total = $this->apps->fetch($sql);
			if(intval($total['total'])<=$limit) $start = 0;
			
			//GET ARTICLE BY HASHTAG
			$sql = "
				SELECT * FROM {$this->dbshema}_news_content anc	WHERE articleType = {$type} AND tags REGEXP  '{$tags}' AND n_status=1 ORDER BY posted_date DESC , id DESC 
				LIMIT {$start},{$limit}
			";
			
			$rqData = $this->apps->fetch($sql,1);
		
			if($rqData) {
				//CEK DETAIL IMAGE FROM FOLDER
				//IF IS ARTICLE, IMAGE BANNER DO NOT SHOWN
				foreach($rqData as $key => $val){
					$rqData[$key]['imagepath'] = false;
					if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}event/{$val['image']}")) 	$rqData[$key]['imagepath'] = "event";
					if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}banner/{$val['image']}")) 	$rqData[$key]['imagepath'] = "banner";
					if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}article/{$val['image']}"))  	$rqData[$key]['imagepath'] = "article";					
					
					if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}article/{$val['image']}")) 	$rqData[$key]['banner'] = false;
					else $rqData[$key]['banner'] = true;
				
					//CHECK FILE SMALL
					if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}{$rqData[$key]['imagepath']}/small_{$val['image']}")) $rqData[$key]['image'] = "small_{$val['image']}";
					
					//PARSEURL FOR VIDEO THUMB
					$video_thumbnail = false;
					if($val['url']!='')	{
						//PARSER URL AND GET PARAM DATA
						$parseUrl = parse_url($val['url']);
						if(array_key_exists('query',$parseUrl)) parse_str($parseUrl['query'],$parseQuery);
						else $parseQuery = false;
						if($parseQuery) {
							if(array_key_exists('v',$parseQuery))$video_thumbnail = $parseQuery['v'];
						} 
						$rqData[$key]['video_thumbnail'] = $video_thumbnail;
					}else $rqData[$key]['video_thumbnail'] = false;
					
					if($rqData[$key]['imagepath']) $rqData[$key]['image_full_path'] = $CONFIG['BASE_DOMAIN_PATH']."public_assets/".$rqData[$key]['imagepath']."/".$rqData[$key]['image'];
					else $rqData[$key]['image_full_path'] = $CONFIG['BASE_DOMAIN_PATH']."public_assets/article/default.jpg";
				}
				
				if($rqData) $qData=	$this->getStatistictArticle($rqData);
				else $qData = false;
			}else $qData = false;	
		}		
		
		$result['result'] = $qData;
		$result['total'] = intval($total['total']);
		return $result;
	}
	
	function getAttending($cid=null){
		if($cid==null) $cid = $this->apps->_p('cid');
		if($cid){
			$cidin = " AND contestid IN ({$cid}) ";
		}
			$sql ="
					SELECT count(*) total,contestid FROM my_contest WHERE n_status=  1 {$cidin}  GROUP BY contestid
					";
		
				$qData = $this->apps->fetch($sql,1);
				if($qData) {
					$this->logger->log("have attending");
					foreach($qData as $val){
						$attendingData[$val['contestid']]=$val['total'];
					}
					
						return $attendingData;
				}
		return false;
	}
	
	function getBanner($page="home",$type="slider_header",$featured=0,$limit=4){
		global $CONFIG;
		$sql ="SELECT * FROM {$this->dbshema}_news_content_banner_type WHERE type ='{$type}' AND n_status=1 LIMIT 1 "; 
		
		$this->logger->log($sql);
		$bannerType = $this->apps->fetch($sql);	
		if(!$bannerType) return false;
		$sql ="SELECT * FROM {$this->dbshema}_news_content_page WHERE pagename = '{$page}' AND n_status = 1 LIMIT 1";		
		$this->logger->log($sql);
		$bannerPage = $this->apps->fetch($sql);
		if(!$bannerPage) return false;	
	 
		$sql = "SELECT * FROM {$this->dbshema}_news_content_banner WHERE page LIKE '%{$bannerPage['id']}%' AND type IN ({$bannerType['id']}) AND n_status IN ({$this->server}) ";
		$this->logger->log($sql);
		$banner = $this->apps->fetch($sql,1);		
		
		if(!$banner) return false;
		foreach($banner as $val){
			$arrBannerID[] = $val['parentid'];
			$banners[$val['parentid']] = $val;
		}
	
		$bannerId = implode(",",$arrBannerID) ;
		
		$sql = "	
		SELECT anc.id,anc.content,anc.brief,anc.title,anc.image,anc.posted_date ,anc.categoryid,ancc.category,anc.articleType,anc.slider_image,anc.thumbnail_image,anct.content_name,anct.type typeofarticlename,anc.url
		FROM {$this->dbshema}_news_content anc
		LEFT JOIN {$this->dbshema}_news_content_category ancc ON ancc.id = anc.categoryid
		LEFT JOIN {$this->dbshema}_news_content_type anct ON anct.id = anc.articleType
		WHERE anc.id IN ({$bannerId}) AND anc.n_status IN ({$this->server})
		ORDER BY posted_date DESC  LIMIT {$limit}
		";
		
		$this->logger->log($sql);
		// pr($sql);
		$qData = $this->apps->fetch($sql,1);
		if(!$qData) return false;
		foreach($qData as $key => $val){
			if(array_key_exists($val['id'],$banners)) $qData[$key]['banner'] = $banners[$val['id']];			
			
			if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}banner/thumb_{$val['slider_image']}")) $qData[$key]['banner_thumb'] = true;
			else  $qData[$key]['banner_thumb'] = false;
				// pr("{$CONFIG['LOCAL_PUBLIC_ASSET']}banner/thumb_{$val['slider_image']}");
			//parseurl for video thumb
				$video_thumbnail = false;
				if($val['articleType']==3&&$val['url']!='')	{				
					//parser url and get param data
						$parseUrl = parse_url($val['url']);
						if(array_key_exists('query',$parseUrl)) parse_str($parseUrl['query'],$parseQuery);
						else $parseQuery = false;
						if($parseQuery) {
							if(array_key_exists('v',$parseQuery))$video_thumbnail = $parseQuery['v'];
						} 
						$qData[$key]['video_thumbnail'] = $video_thumbnail;
				}else $qData[$key]['video_thumbnail'] = false;		
			
		}
		
		return $qData;
	}	
	
	function getCity($province=NULL, $type=NULL, $cityID=NULL){
		if($cityID){
			$filter = 'AND id = '.$cityID;
			$default = "SELECT * FROM {$this->dbshema}_city_reference WHERE  provinceid<>0  AND city <> '(NOT SPECIFIED)' AND id ={$cityID} ORDER BY city";
			$qDefault = $this->apps->fetch($default);
		}else{
			$filter = '';
		}
		
		if ($province) {
			$filterprov = " provinceid = {$province}";
		} else {
			$filterprov = "";
		}
		
		$sql ="SELECT * FROM {$this->dbshema}_city_reference WHERE provinceid <> 0 AND city <> '(NOT SPECIFIED)' {$filterprov} {$filter}  ORDER BY city";
		// pr($sql);
		$qData = $this->apps->fetch($sql,1);
		$this->logger->log($sql);
		
		if($type=='topup'){
			array_unshift($qData, $qDefault);
		}		
		
		if(!$qData) return false;
		return $qData;
	}
	
	function getTypeContent(){
		$sql_type ="SELECT id,content_name type FROM {$this->dbshema}_news_content_type WHERE id IN ('3','4','5') ORDER BY id";
		$qData = $this->apps->fetch($sql_type,1);
		
		if(!$qData) return false;
		return $qData;
	}
	
	function getEventArticleType(){
		$sql_type ="SELECT * FROM {$this->dbshema}_news_content_type WHERE content = 4 ORDER BY id";
		$qData = $this->apps->fetch($sql_type,1);
		
		if(!$qData) return false;
		return $qData;
	}
	
	function getProvince($type=null,$id=null){
		if($id){
			$filter = 'WHERE id <> '.$id;
			$default = "SELECT * FROM {$this->dbshema}_province_reference WHERE id = ".$id;
			$qDefault = $this->apps->fetch($default);
		}else{
			$filter = '  ';
		}
		// pr($id);
		if($type=='topup'){$filterProvince = 'AND id IN (1,2,3,4,5,6,7,8,9,10,11,12)';}
		else if($type=='coverage'){$filterProvince = 'AND id IN (1,2,3,4,5,6,8,9,10,13,14,16,19,21,24,30)';}
		else if($type=='coveragemap'){$filterProvince = 'WHERE id IN (1,2,3,4,5,6,7,8,9,10,11,12,16,17,25,26,27,28,29,30)';}
		else{$filterProvince = '';}
	
		$sql ="SELECT * FROM {$this->dbshema}_province_reference {$filter} {$filterProvince}";
		$this->logger->log($sql);
		$qData = $this->apps->fetch($sql,1);
		
		if($type=='coverage' || $type=='topup'){
			array_unshift($qData, $qDefault);
		}
		
		
		if(!$qData) return false;
		return $qData;
	}
	
	function getProvinceVenue($type=null,$id=null){
		$sql ="SELECT provinceName FROM {$this->dbshema}_venue_master WHERE n_status = 1 AND provinceName <> '' GROUP BY provinceName ORDER BY provinceName";
		$this->logger->log($sql);
		$qData = $this->apps->fetch($sql,1);
		
		if(!$qData) return false;
		return $qData;
	}
	
	function getCityVenue($type=null,$id=null){
		$sql ="SELECT city FROM {$this->dbshema}_venue_master WHERE n_status = 1 AND city <> '' GROUP BY city ORDER BY city";
		$this->logger->log($sql);
		$qData = $this->apps->fetch($sql,1);
		
		if(!$qData) return false;
		return $qData;
	}
	
	// add by putra featured article
	function getArticleFeatured() {
		global $CONFIG;
		
		$sql = "SELECT id,title,brief,image,thumbnail_image,slider_image,posted_date,file,url,fromwho,tags,authorid,topcontent,cityid ,articleType,can_save,content
		FROM {$this->dbshema}_news_content WHERE articleType IN (4) AND n_status=1 ORDER BY posted_date DESC ,id DESC  LIMIT 1";
		
		$qData = $this->apps->fetch($sql,1);
		if(!$qData) return false;
		//CEK DETAIL IMAGE FROM FOLDER
		//IF IS ARTICLE, IMAGE BANNER DO NOT SHOWN
		foreach($qData as $key => $val){
			$qData[$key]['imagepath'] = false;
			
			if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}event/{$val['image']}")) 	$qData[$key]['imagepath'] = "event";
			if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}banner/{$val['image']}")) 	$qData[$key]['imagepath'] = "banner";
			if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}article/{$val['image']}"))  	$qData[$key]['imagepath'] = "article";	
		
			if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}article/{$val['image']}")) $qData[$key]['banner'] = false;
			else $qData[$key]['banner'] = true;	
			
			//CHECK FILE
			if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}music/mp3/{$val['file']}")) $qData[$key]['hasfile'] = true;
			else $qData[$key]['hasfile'] = false;				
			
			//PARSEURL FOR VIDEO THUMB
			if($val['articleType']==3&&$val['url']!='')	{
				//PARSER URL AND GET PARAM DATA
				$parseUrl = parse_url($val['url']);
				if(array_key_exists('query',$parseUrl)) parse_str($parseUrl['query'],$parseQuery);
				else $parseQuery = false;
				if($parseQuery) {
					if(array_key_exists('v',$parseQuery))$video_thumbnail = $parseQuery['v'];
					else $video_thumbnail= false;
				}else $video_thumbnail = false;
				$qData[$key]['video_thumbnail'] = $video_thumbnail;
			}else $qData[$key]['video_thumbnail'] = false;		
			
			if($qData[$key]['imagepath']) $qData[$key]['image_full_path'] = $CONFIG['BASE_DOMAIN_PATH']."public_assets/".$qData[$key]['imagepath']."/".$qData[$key]['image'];
			else $qData[$key]['image_full_path'] = $CONFIG['BASE_DOMAIN_PATH']."public_assets/article/default.jpg";
		}
		
		if($qData) $qData =	$this->getStatistictArticle($qData);
		else return false;
		return $qData;
	}
	
	function getArticleContent($start=null,$limit=10,$contenttype=0,$topcontent=array(0,3),$articletype=false,$groupby=false,$author=false,$allcontent=false,$datetimes=false,$usingbadetail=true,$usingbranddetail=true,$checkin=true) {
		global $CONFIG;
		
		$result['result'] = false;
		$result['total'] = 0;
		
		if($start==null)$start = intval($this->apps->_request('start'));
		$uid = intval($this->apps->_request('uid'));
		if($uid==0)	{
			$uidType = $this->uid;
			$uid = $this->uid;
		}
		
		$contenttype = intval($contenttype);
		$limit = intval($limit);
		$topcontent = implode(',',$topcontent);
		
		//RUN FILTER ENGINE, KEYWORDSEARCH , CONTENTSEARCH 
		$filter = $this->apps->searchHelper->filterEngine($limit,$groupby,$author);
		$typeid = strip_tags($this->checkPage($contenttype,$articletype));		
		
		if(!$typeid) return false;
		
		$search = "";
		$startdate = "";
		$enddate = "";
		$authorid = "";		
	
		if ($this->apps->_p('search')) {
			if ($this->apps->_p('search')!="Search...") {
				$search = rtrim($this->apps->_p('search'));
				$search = ltrim($search);
				
				if(strpos($search,' ')) $parseSearch = explode(' ', $search);
				else $parseSearch = false;
				
				if(is_array($parseSearch)) $search = $search.'|'.trim(implode('|',$parseSearch));
				else  $search = trim($search);
				
				$search = " AND (anc.title REGEXP  '{$search}' OR anc.brief REGEXP  '{$search}' OR anc.content REGEXP  '{$search}') ";
			}
		}
		if ($this->apps->_p('startdate')) {
			$start_date = $this->apps->_p('startdate');
			$startdate = " AND DATE(anc.posted_date) >= DATE('{$start_date}') ";
		}
		if ($this->apps->_p('enddate')) {
			$end_date = $this->apps->_p('enddate');
			$enddate = " AND DATE(anc.posted_date) <= DATE('{$end_date}') ";
		}
		$authorid = " AND anc.authorid = {$uid}";
		
		$qStatus = "  AND anc.n_status = 1 ";
		$qHirarki ="";
		if($filter['uidsearch']!='') $qHirarki = " {$filter['uidsearch']} ";
		else {
			$leadertype = intval($this->apps->user->leaderdetail->type);
			if($leadertype){
				
						if(in_array($articletype,$this->modzeropage)){
						
							$auhtorarrid[$uid] = $uid;
							
								$auhtorminion = @$this->apps->user->branddetail;
								if($auhtorminion){
									foreach($auhtorminion as $val){
											$auhtorarrid[$val->ownerid] = $val->ownerid;
									}
								}
							
							
							$auhtorminion = @$this->apps->user->areadetail;
							if($auhtorminion){
								foreach($auhtorminion as $val){
										$auhtorarrid[$val->ownerid] = $val->ownerid;
								}
							}		
							
							$auhtorminion = @$this->apps->user->pldetail;
							if($auhtorminion){
								foreach($auhtorminion as $val){
										$auhtorarrid[$val->ownerid] = $val->ownerid;
								}
							}	
							
							$auhtorminion = @$this->apps->user->badetail;
							// pr($auhtorminion);
							if($auhtorminion){
								foreach($auhtorminion as $val){
										$auhtorarrid[$val->ownerid] = $val->ownerid;
								}
							}	
							
						
							if(is_array($auhtorarrid)) 	{
								// pr($minionarr);
								$authorids = implode(',',$auhtorarrid);
							}else $authorids = $uid;
							
							$qHirarki = " AND ( anc.authorid IN ({$authorids}) OR EXISTS ( SELECT contentid FROM {$this->dbshema}_news_content_tags WHERE friendid IN ({$authorids}) AND friendtype=1  AND contentid=anc.id) ) ";
						// $qHirarki = "";
						}
				
				
				/*
				if(in_array($leadertype,$this->approver)){
					$minionarr[$this->uid] = $this->uid;
					$auhtorminion = $this->apps->user->miniondetail;
					if($auhtorminion){
						foreach($auhtorminion as $val){
								$minionarr[$val->ownerid] = $val->ownerid;
						}
					}		
					
					if(is_array($minionarr)) 	{
						// pr($minionarr);
						$authorids = implode(',',$minionarr);
					}else $authorids = $this->uid;
					
					$qHirarki = "  AND ( authorid IN ({$authorids}) OR EXISTS ( SELECT contentid FROM {$this->dbshema}_news_content_tags WHERE friendid IN ({$authorids}) AND friendtype=0  ) ) ";
				}
				*/
				
			}
		}
		
		$qCheckCheckin = "";
		
		$typeofsort = "DESC";
		$qCategories = " ";
		$qSort = " anc.posted_date DESC , anc.id DESC ";
		if($articletype=='plan'){
			$typeofsort = "ASC";
			$brands = strip_tags($this->apps->_request('category'));
			$month = intval($this->apps->_request('month'));
			$leadertype = intval($this->apps->user->leaderdetail->type);
			$qStatus = "  AND anc.n_status IN (0,1,2) ";		
			if($leadertype==4) {
				if($datetimes)  $qStatus = "  AND anc.n_status IN (0) ";		
			}
			if($datetimes) $qStatus .= "  AND anc.posted_date >= NOW() ";		
			else $qStatus .= "  AND DATE(anc.posted_date) >= DATE_SUB( DATE(NOW()), INTERVAL 15 DAY ) ";		
			if($month!=0)	$qStatus .= " AND MONTH(anc.posted_date) = {$month} ";
		
			if($brands=='cocreation') $qCategories = " AND pages.type IN ({$this->cocreationtypeid}) ";
			if($brands=='brands') $qCategories = " AND pages.type IN ({$this->brandtypeid}) ";
			if($brands=='ba') $qCategories = " AND pages.type IN ( 1 ) ";
			if(!$usingbadetail) $qCategories = " AND pages.type NOT IN ( 1,100 ) ";
			if(!$usingbadetail&&!$usingbranddetail) $qCategories = " AND pages.type NOT IN ( 1,100,{$this->brandtypeid} ) ";
			if($datetimes) $qSort = "  anc.posted_date {$typeofsort}, anc.id {$typeofsort} ";
			else  $qSort = " anc.posted_date {$typeofsort} , anc.id DESC ";
			
			if(!$checkin){
					$qCheckCheckin = " AND NOT EXISTS ( SELECT contentid FROM my_checkin mc WHERE mc.contentid=anc.id ) ";
			}
			// pr($leadertype);
			if($leadertype==1){
				$qHirarki = " AND ( anc.authorid IN ({$uid}) )";
			}
		}
		
		if($allcontent == true )$qArticleType = "";
		else $qArticleType = " anc.articleType IN ({$typeid}) AND ";
		if($typeid=='3') $qCheckCheckin = " OR EXISTS (SELECT contentid FROM my_checkin WHERE contentid=anc.id ) ";
		//GET TOTAL ARTICLE

		$sql = "SELECT count(*) total 
		FROM {$this->dbshema}_news_content anc 
		LEFT JOIN my_pages pages ON anc.authorid=pages.ownerid 
		WHERE  {$qArticleType} anc.topcontent IN ({$topcontent}) {$search} {$startdate} {$enddate} {$filter['keywordsearch']} {$filter['categorysearch']} {$filter['citysearch']} {$filter['postedsearch']} {$filter['fromwhosearch']} {$qStatus} {$qHirarki} {$qCategories} {$qCheckCheckin}
		{$filter['groupbyfilter']}		";
		$total = $this->apps->fetch($sql);
		
		if(intval($total['total'])<=$limit) $start = 0;
		
		$sql = "
			SELECT 			anc.id,anc.title,anc.brief,anc.image,anc.thumbnail_image,anc.slider_image,anc.posted_date,anc.expired_date,anc.file,anc.url,anc.fromwho,anc.tags,anc.authorid,anc.topcontent,anc.cityid ,anc.articleType,anc.can_save,anc.n_status,pages.type pagetypes
			FROM {$this->dbshema}_news_content anc
			LEFT JOIN my_pages pages ON anc.authorid=pages.ownerid 
			{$filter['subqueryfilter']} 
			WHERE {$qArticleType} anc.topcontent IN ({$topcontent}) {$search} {$startdate} {$enddate} {$filter['keywordsearch']} {$filter['categorysearch']} {$filter['citysearch']} {$filter['postedsearch']} {$filter['fromwhosearch']} {$qStatus} {$qHirarki} {$qCategories}  {$qCheckCheckin}
			{$filter['groupbyfilter']} ORDER BY {$filter['suborderfilter']}  {$qSort}
			LIMIT {$start},{$limit}";
		// pr($sql);
		// pr($usingbadetail);
		$rqData = $this->apps->fetch($sql,1);
		$this->logger->log($sql);
		if($rqData) {
			//CEK DETAIL IMAGE FROM FOLDER
			//IF IS ARTICLE, IMAGE BANNER DO NOT SHOWN
			foreach($rqData as $key => $val){
				$rqData[$key]['imagepath'] = false;
				if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}event/{$val['image']}")) 	$rqData[$key]['imagepath'] = "event";
				if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}banner/{$val['image']}")) 	$rqData[$key]['imagepath'] = "banner";
				if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}article/{$val['image']}"))  	$rqData[$key]['imagepath'] = "article";					
				
				if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}article/{$val['image']}")) 	$rqData[$key]['banner'] = false;
				else $rqData[$key]['banner'] = true;
			
				//CHECK FILE SMALL
				if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}{$rqData[$key]['imagepath']}/small_{$val['image']}")) $rqData[$key]['image'] = "small_{$val['image']}";
				
				//PARSEURL FOR VIDEO THUMB
				$video_thumbnail = false;
				if($val['url']!='')	{
					//PARSER URL AND GET PARAM DATA
					$parseUrl = parse_url($val['url']);
					if(array_key_exists('query',$parseUrl)) parse_str($parseUrl['query'],$parseQuery);
					else $parseQuery = false;
					if($parseQuery) {
						if(array_key_exists('v',$parseQuery))$video_thumbnail = $parseQuery['v'];
					} 
					$rqData[$key]['video_thumbnail'] = $video_thumbnail;
				}else $rqData[$key]['video_thumbnail'] = false;
				
				if($rqData[$key]['imagepath']) $rqData[$key]['image_full_path'] = $CONFIG['BASE_DOMAIN_PATH']."public_assets/".$rqData[$key]['imagepath']."/".$rqData[$key]['image'];
				else $rqData[$key]['image_full_path'] = $CONFIG['BASE_DOMAIN_PATH']."public_assets/article/default.jpg";
				
				
			}
			
			if($rqData) $qData=	$this->getStatistictArticle($rqData);
			else $qData = false;
		}else $qData = false;		
	
		$result['result'] = $qData;
		$result['total'] = intval($total['total']);
		$totals = intval($total['total']);

		
		if($totals>$start) $nextstart = $start;
		else $nextstart = 0;
		
				
		if($start<=0)$countstart = $limit;
		else $countstart = $limit+$nextstart;
		
		$thenextpage = intval($limit+$nextstart);
		if($totals<=$thenextpage)	$thenextpage = 0;	
		$result['pages']['nextpage'] = $thenextpage;
		$result['pages']['prevpage'] = $countstart-$limit;
		
		return $result;
	}
	
	function getCommentModeration($start=null,$limit=10,$type=null) {
		global $CONFIG;
		
		$result['result'] = false;
		$result['total'] = 0;
		
		if($start==null)$start = intval($this->apps->_request('start'));		
		$limit = intval($limit);
		$userid = $this->uid;
		
		// GET CONTENT POST
		$sql_content = "SELECT id,title FROM {$this->dbshema}_news_content WHERE  articleType = {$type} AND n_status = 1";
		$qData_content = $this->apps->fetch($sql_content,1);
		$arrPost = false;
		if ($qData_content) {
			foreach ($qData_content as $key => $val) {
				$arrPost[$key] = $val['id'];
			}
		}
		
		if(!$arrPost) return false;
		$cidStr = implode(",",$arrPost);
		
		$search = "";
		$startdate = "";
		$enddate = "";
		if ($this->apps->_g('page')=='moderation') {
			if ($this->apps->_p('search')) {
				if ($this->apps->_p('search')!="Search...") {
					$search = rtrim($this->apps->_p('search'));
					$search = ltrim($search);
					
					if(strpos($search,' ')) $parseSearch = explode(' ', $search);
					else $parseSearch = false;
					
					if(is_array($parseSearch)) $search = $search.'|'.trim(implode('|',$parseSearch));
					else  $search = trim($search);
					
					$search = "AND comment REGEXP '{$search}'";
				}
			}
			if ($this->apps->_p('startdate')) {
				$start_date = $this->apps->_p('startdate');
				$startdate = "AND DATE(date) >= DATE('{$start_date}') ";
			}
			if ($this->apps->_p('enddate')) {
				$end_date = $this->apps->_p('enddate');
				$enddate = "AND DATE(date) <= DATE('{$end_date}') ";
			}
		}
		
		//GET TOTAL LIST COMMENT
		$sql_total = "SELECT count(*) total FROM {$this->dbshema}_news_content_comment WHERE contentid IN ({$cidStr}) {$search} {$startdate} {$enddate} AND n_status = 1";
		$total = $this->apps->fetch($sql_total);
		
		if(intval($total['total'])<=$limit) $start = 0;
		
		// GET COMMENT POST
		$sql_comment = "SELECT * FROM {$this->dbshema}_news_content_comment WHERE contentid IN ({$cidStr}) {$search} {$startdate} {$enddate} AND n_status = 1 ORDER BY date DESC LIMIT {$start},{$limit}";
		
		$qData = $this->apps->fetch($sql_comment,1);
		$arrUser = false;
		if ($qData) {
			foreach ($qData as $key => $val) {
				$arrUser[$key] = $val['userid'];
			}
		}
		
		if(!$arrUser) return false;
		$users = implode(",",$arrUser);
		
		$sql = "SELECT * FROM social_member WHERE id IN ({$users})  AND n_status = 1 ";
		$qDataUser = $this->apps->fetch($sql,1);
		if($qDataUser){
			foreach($qDataUser as $val){
				$userDetail[$val['id']]['name'] = $val['name'];
				$userDetail[$val['id']]['img'] = $val['img'];
				if(!is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}user/photo/{$val['img']}")) $val['img'] = false;
				if($val['img']) $userDetail[$val['id']]['image_full_path'] = $CONFIG['BASE_DOMAIN_PATH']."public_assets/user/photo/".$val['img'];
				else $userDetail[$val['id']]['image_full_path'] = $CONFIG['BASE_DOMAIN_PATH']."public_assets/user/photo/default.jpg";
			}
			
			foreach($qData as $key => $val){
				$arrComment[$key] = $val;
				if(array_key_exists($val['userid'],$userDetail)){
					$arrComment[$key]['name'] = $userDetail[$val['userid']]['name'] ;
					$arrComment[$key]['img'] = $userDetail[$val['userid']]['img'] ;
					$arrComment[$key]['image_full_path'] = $userDetail[$val['userid']]['image_full_path'] ;
				}
			}
		}
		
		$result['result'] = $arrComment;
		$result['total'] = intval($total['total']);
		return $result;
	}
	
	function getStatistictArticle($rqData=null){
		
		if($rqData==null) return false;
		global $CONFIG;
		/* path to page */
				
		$socialArrAuthor = false;
	
		$adminProfile = false;
		$socialProfile = false;
		
		$qData = false;
		$cidArr = false;
		$cityData = false;
		$arrCityID = false;
		foreach($rqData as $key => $val){
		
			$cidArr[] = $val['id'];
			if(array_key_exists('cityid',$val)) $arrCityID[$val['cityid']] = intval($val['cityid']);
			
			//get profile array
			$socialArrAuthor[$val['authorid']] = $val['authorid'];
		
			
			$qData[$key] = $val;
			$qData[$key]['ts'] = strtotime($val['posted_date']);
			$qData[$key]['user'] = false;
			// $qData[$key]['comment'] = false;
			$qData[$key]['commentcount'] = 0;
			$qData[$key]['favorite'] = 0;
			$qData[$key]['views'] = 0;
			$qData[$key]['author'] = false;
			$qData[$key]['attending'] = 0;
			$qData[$key]['gallery'] = false;
			$qData[$key]['totalgallery'] = 1;
			$qData[$key]['profilepath'] = false;
			$qData[$key]['cityname'] = false;
			$qData[$key]['friendtags'] = false;
			$qData[$key]['commentlist'] = false;
			$qData[$key]['rating'] = false;
		}		
		
		if(!$cidArr) return false;
		$cidStr = implode(",",$cidArr);		
		
		if(!$arrCityID) return false;
		$cityArr = implode(",",$arrCityID);		
		
		//get profiler
		if($socialArrAuthor){
			$socialStr = implode(",",$socialArrAuthor);
			$socialProfile = $this->getAuthorProfile($socialStr,'social');
		}
		
		if($cityArr){
			$cityData = $this->checkCity($cityArr);
		}
		
		
		//merge profiler
		foreach($qData as $key => $val){
				//user profile
				if($socialProfile) if(array_key_exists($val['authorid'],$socialProfile)) $qData[$key]['author'] = $socialProfile[$val['authorid']];		
				//city data
				if($cityData)  if(array_key_exists($val['cityid'],$cityData)) $qData[$key]['cityname'] = $cityData[$val['cityid']];
				
				$qData[$key]['profilepath'] = "friends";	
				
		}
		
		// favorite or like data
		$favoriteData = $this->getFavorite($cidStr);
		if($favoriteData){
			
			foreach($qData as $key => $val){
				if(array_key_exists($val['id'],$favoriteData)) $qData[$key]['favorite'] = $favoriteData[$val['id']]['total'];			
				
				if(array_key_exists($val['id'],$favoriteData)) {
						foreach($favoriteData[$val['id']]['users'] as $valfav){
							$userfavorites[] = $valfav;
						}					
						$qData[$key]['users']['favorites'] = $userfavorites;
						$userfavorites = false;
				}
				
				// if(array_key_exists($val['id'],$favoriteData)) 	$qData[$key]['users']['favorites'] =$favoriteData[$val['id']]['users'] ;
				
				if(array_key_exists($val['id'],$favoriteData)) $qData[$key]['users']['mylikes'] = $favoriteData[$val['id']]['mylikes'];	
		
			}
			
		}
		
		//comment di list article 
		$commentsData = $this->getComment($cidStr,true);
		
		
		if($commentsData){
			// $commentlimits = count($cidArr);
		
			
			foreach($qData as $key => $val){
					$commentsDataComment = $this->getComment($val['id'],false,0,2,true);
						if(array_key_exists($val['id'],$commentsData)) $qData[$key]['commentcount'] = $commentsData[$val['id']];
						if($commentsDataComment) {							
							if(array_key_exists($val['id'],$commentsDataComment)) {
								foreach($commentsDataComment[$val['id']] as $valcom){
									$commentarray[] =$valcom;
								}
								$qData[$key]['commentlist']=$commentarray;
								$commentarray = false;
								// $qData[$key]['comment'] = $commentsData[$val['id']];
								// if(array_key_exists($val['id'],$commentsDataComment)) $qData[$key]['commentlist'] = $commentsDataComment[$val['id']];
								
						}
					}
				
			}
			// pr($qData);
		}
		
		// gallery or repository data
		$gallerydata = $this->getContentRepository($cidStr);
		if($gallerydata){
			
			foreach($qData as $key => $val){
				if(array_key_exists($val['id'],$gallerydata)) $qData[$key]['gallery'] = $gallerydata[$val['id']];
				if(array_key_exists($val['id'],$gallerydata)) $qData[$key]['totalgallery'] = count($gallerydata[$val['id']]) ;
				else $qData[$key]['totalgallery'] = 0;
				
			
			}
		
		}
		//get views
		$getTotalViewsArticle = $this->getTotalViewsArticle($cidStr);
		if($getTotalViewsArticle){
			
			foreach($qData as $key => $val){
				if(array_key_exists($val['id'],$getTotalViewsArticle)) $qData[$key]['views'] = $getTotalViewsArticle[$val['id']];
				
			
			}
		
		}
		
		//get views
		$getCheckin = $this->getCheckin($cidStr);
		if($getCheckin){
			
			foreach($qData as $key => $val){
				if(array_key_exists($val['id'],$getCheckin)) $qData[$key]['rating'] = $getCheckin[$val['id']];
				
			
			}
		
		}
		
		//get attending
		$attendingData = $this->getAttending($cidStr);
		if($attendingData){
			
			foreach($qData as $key => $val){
				if(array_key_exists($val['id'],$attendingData)) $qData[$key]['attending'] = $attendingData[$val['id']];
				
			
			}
		
		}	
		
		//get friend tags
		$friendtagsData = $this->getFriendTagged($cidStr,0,false);
		if($friendtagsData){
			
			foreach($qData as $key => $val){
				if(array_key_exists($val['id'],$friendtagsData)) $qData[$key]['friendtags'] = $friendtagsData[$val['id']];
				
			
			}
		
		}
		if($qData) {
			return $qData;
		} else {
		return false;
		}
	}
	
	function checkCity($strCity=null){
			if($strCity==null) return false;
			$sql ="SELECT * FROM {$this->dbshema}_city_reference WHERE id IN ({$strCity}) LIMIT 20 ";
			// pr($sql);
			$qData = $this->apps->fetch($sql,1);
			if(!$qData)return false;
			$rqData = false;
			foreach($qData as $val){
				$rqData[$val['id']] = $val['city'];
			}	
			
			return $rqData;
			
	}
	
	function checkPage($contenttype=0,$articletype=false){
	
		if($articletype==false) $articletype = strip_tags($this->apps->_g('page'));
		
		$sql = "SELECT * FROM {$this->dbshema}_news_content_type WHERE type = '{$articletype}' AND content={$contenttype} LIMIT 1";
		$arrType = $this->apps->fetch($sql,1);
		
		if(!$arrType) return false;
		foreach($arrType as $val){
			$arrtypeid[] = $val['id'];
		}
		
		$typeid = false;
		if($arrtypeid) $typeid = implode(',',$arrtypeid);
		else return false;
		return $typeid;
	}
	
	function getDetailArticle($start=null,$limit=1,$contenttype=false) {		
		global $CONFIG;
		
		if($start==null)$start = intval($this->apps->_request('start'));
		$category = intval($this->apps->_request('cid'));
		$id = intval($this->apps->_request('id'));
		$limit = intval($limit);
	
		if($category!=0) $qCategory = " AND categoryid={$category} ";
		else $qCategory = "";
		
		if($id!=0) $qid = " AND acontent.id={$id} ";
		else $qid = ""; 
		
		if($contenttype){
			$contenttype = intval($contenttype);
			$qType = " AND articleType = {$contenttype} ";
		}else $qType = "";
		// $typeid = strip_tags($this->checkPage($contenttype));
		//get total
		$sql = "
		SELECT count(*) total  
		FROM {$this->dbshema}_news_content acontent
		LEFT JOIN {$this->dbshema}_news_content_category acategory ON acontent.categoryid = acategory.id
		WHERE  n_status IN (0,1)  {$qid}  {$qCategory} {$qType} ";
	
		$totaldata = $this->apps->fetch($sql);
		if(!$totaldata) return false;
		if($totaldata['total']<=0) return false;
		
		$sql = "
		SELECT acontent.*, acategory.point ,acategory.category,anct.type  
		FROM {$this->dbshema}_news_content acontent
		LEFT JOIN {$this->dbshema}_news_content_type anct ON acontent.articleType = anct.id
		LEFT JOIN {$this->dbshema}_news_content_category acategory ON acontent.categoryid = acategory.id
		WHERE  n_status IN (0,1)  {$qid}  {$qCategory} {$qType} 
		ORDER BY posted_date DESC LIMIT {$start},{$limit}";
		// pr($sql);
		$rqData = $this->apps->fetch($sql,1);
		if($rqData){
			//cek detail image from folder
				//if is article, image banner do not shown
			foreach($rqData as $key => $val){
				$rqData[$key]['session_userid'] = intval($this->apps->user->id);
				$rqData[$key]['session_pageid'] = intval($this->apps->user->pageid);
				$untags = $val['tags'];
				$rqData[$key]['un_tags'] = $untags;	
				$rqData[$key]['imagepath'] = false;
				
				
				if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}event/{$val['image']}")) 	$rqData[$key]['imagepath'] = "event";
				if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}banner/{$val['image']}")) 	$rqData[$key]['imagepath'] = "banner";
				if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}article/{$val['image']}"))  	$rqData[$key]['imagepath'] = "article";	
				// pr(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}article/{$val['image']}"));
				//check file
				if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}music/mp3/{$val['file']}")) $rqData[$key]['hasfile'] = true;
				else $rqData[$key]['hasfile'] = false;	
				
				//parseurl for video thumb
				$video_thumbnail = false;
				if($val['url']!='')	{
					//parser url and get param data
						$parseUrl = parse_url($val['url']);
						if(array_key_exists('query',$parseUrl)) parse_str($parseUrl['query'],$parseQuery);
						else $parseQuery = false;
						if($parseQuery) {
							if(array_key_exists('v',$parseQuery))$video_thumbnail = $parseQuery['v'];
						} 
						$rqData[$key]['video_thumbnail'] = $video_thumbnail;
				}else $rqData[$key]['video_thumbnail'] = false;				

				if($rqData[$key]['imagepath']) $rqData[$key]['image_full_path'] = $CONFIG['BASE_DOMAIN_PATH']."public_assets/".$rqData[$key]['imagepath']."/".$rqData[$key]['image'];
				else $rqData[$key]['image_full_path'] = $CONFIG['BASE_DOMAIN_PATH']."public_assets/article/default.jpg";		
				
				$rqData[$key]['engagementtype'] = $this->getbaengagementstat($val['id'],$val['authorid']);
				
			}
		}
		if($rqData) $qData=	$this->getStatistictArticle($rqData);
		else $qData = false;
		
		if(!$qData) return false;
		if($this->uid && $qData){
		
				$sql ="
				INSERT INTO {$this->dbshema}_news_content_rank (categoryid ,	point, 	userid ,	date) 
				VALUES ({$qData[0]['categoryid']},{$qData[0]['point']},{$this->uid},NOW())
				
				";
				$this->apps->query($sql);
				
				// job buat bot tracking user preference
				// $sql ="
				// INSERT INTO job_content_preference (user_id ,	content_id, 	n_status) 
				// VALUES ({$this->uid},{$qData['id']},0)
				
				// ";
				
				// $this->apps->query($sql);
		
	
		}
		
		
		if(!$qData) return false;
		
		$result['result'] = $qData;
		$result['total'] = $totaldata['total'];		
		 //pr($result);
		return $result;
	}
	
	
	function getbaengagementstat($cid=false,$authorid=false){
		if(!$authorid) return false;
		$sql ="SELECT count(*) total FROM my_checkin checkin WHERE checkin.contentid={$cid} AND checkin.userid IN ({$authorid}) AND checkin.n_status=1";
		
		$qData = $this->apps->fetch($sql);
		// pr($sql);
		if($qData){
			if($qData['total']>0)	return true;
			else return false;
		}
		return false;
		
	}
	
	function getTotalViewsArticle($cid=null){
		if($cid==null) return false;
		
		$sql = "SELECT COUNT(*) total, action_value as cid FROM tbl_activity_log WHERE action_id=2 AND action_value IN ({$cid}) GROUP BY cid";
		// pr($sql);
		$qData = $this->apps->fetch($sql,1);
		if(!$qData) return false;
		$arrViewArticle = false;
		foreach($qData as $key => $val){
			$arrViewArticle[$val['cid']] = $val['total'];
		}
		if($arrViewArticle){
			return $arrViewArticle;
		}else return false;
		
	}
	
	function getContentRepository($strId=null,$gallerytype=0,$limit=10){
	
		if($strId==null) return false;
		global $CONFIG;
		$gallerytype = intval($gallerytype);
		$limit = intval($limit);
				
		$sql = "SELECT * FROM  {$this->dbshema}_news_content_repo WHERE otherid IN ({$strId}) AND n_status=1 ORDER BY created_date DESC LIMIT {$limit} ";
		// pr($sql);
		$rqData = $this->apps->fetch($sql,1);
		
		if(!$rqData) return false;
		$qData = false;
		foreach($rqData as $key => $val){
			if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}article/{$val['files']}"))  	$rqData[$key]['image_full_path'] = "{$CONFIG['BASE_DOMAIN_PATH']}public_assets/article/{$val['files']}";	
			else 	$rqData[$key]['image_full_path'] =  $CONFIG['BASE_DOMAIN_PATH']."public_assets/article/default.jpg";	
			$qData[$val['otherid']][$val['id']] = $rqData[$key];
		}
		// pr($qData);
		if(!$qData) return false;
		
		return $qData;
	
	}
	
	function getListSongs($start=null,$limit=9) {
		global $CONFIG;
		
		$pid = intval($this->apps->_request('pid'));
		if(!$pid) $pid = intval($this->apps->user->pageid);
		if($pid!=0 || $pid!=null) {		
			if($start==null)$start = intval($this->apps->_request('start'));
			$limit = intval($limit);
			$pages = $this->apps->_g('page');
			$userid = $this->uid;
			if ($pages=='my') {
				//GET IDCONTENT PLAYLIST
				$sql_playlist = "SELECT id,otherid FROM my_playlist where myid = {$userid} AND n_status=1 ORDER BY datetime DESC";
				$dataPlaylist = $this->apps->fetch($sql_playlist,1);
				if ($dataPlaylist) {
					foreach($dataPlaylist as $key => $val){
						$idcontent[] = $val['otherid'];
					}
					if(!$idcontent) return false;
					$arrIdContent = implode(",",$idcontent);
				} else return false;
				
				//GET TOTAL PLAYLIST
				$sql_total = "SELECT count(*) total FROM {$this->dbshema}_news_content WHERE id IN ({$arrIdContent}) AND n_status = 1";
				$total = $this->apps->fetch($sql_total);
				
				if(!$total) return false;
				if($start>intval($total['total'])) return false;
				if(intval($total['total'])<=$limit) $start = 0;
				
				//GET DATA PLAYLIST
				$sql = " SELECT * FROM {$this->dbshema}_news_content WHERE id IN ({$arrIdContent}) AND n_status = 1 LIMIT {$start},{$limit}";
			} elseif ($pages=='myband' || $pages=='mydj' ) {
				$type = $pages=='myband' ? 1 : 4;
				
				//GET TOTAL SONGS
				$sql_total = "SELECT count(*) total FROM {$this->dbshema}_news_content WHERE fromwho = 2 AND articleType = 3 AND file <> '' AND authorid = {$pid} AND n_status = 1";
				$total = $this->apps->fetch($sql_total);
				
				if(!$total) return false;
				if($start>intval($total['total'])) return false;
				if(intval($total['total'])<=$limit) $start = 0;
				
				//GET DATA SONGS
				$sql = "SELECT * FROM {$this->dbshema}_news_content WHERE fromwho = 2 AND articleType = 3 AND file <> '' AND authorid = {$pid} AND n_status = 1 ORDER BY posted_date DESC LIMIT {$start},{$limit}";
			} else return false;
			$rqData = $this->apps->fetch($sql,1);
			
			$no=1;
			if($rqData) {
				foreach ($rqData as $k => $v) {
					$v['no'] = $no++;
					if ($v['filesize']) {
						$durasi = $v['filesize']/1000;
						$v['filesize'] = sprintf("%02d:%02d", ($durasi /60), $durasi %60 );
					} else $v['filesize'] = "";
					$rqData[$k] = $v;
					
					if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}music/mp3/{$v['file']}")) $rqData[$k]['hasfile'] = true;
					else $rqData[$k]['hasfile'] = false;				
				}
			}
			
			if($rqData) $qData=	$this->getStatistictArticle($rqData);
			else $qData = false;
			
			if(!$qData) return false;
			$arrPlaylist['result'] = $qData;
			$arrPlaylist['total'] = $total['total'];
			return $arrPlaylist;
		}
		return false;
	}
	
	function getMygallery($start=null,$limit=3,$userid=NULL) {
		global $CONFIG;
		if($start==null) $start = intval($this->apps->_g('start'));
		
		if(strip_tags($this->apps->_g('page'))=='my') $userid = $this->uid;
		else $userid = intval($this->apps->_g('uid'));
				
		//get total gallery
		$sql_total = "
			SELECT count(*) total FROM `my_images` mm
			WHERE mm.userid = '{$userid}'
			AND mm.n_status = 1
		";
		$total = $this->apps->fetch($sql_total);
		
		if(!$total) return false;
		if($start>intval($total['total'])) return false;
		if(intval($total['total'])<=$limit) $start = 0;
		
		$sql = "
			SELECT mm.*,nc.title,nc.brief,nc.image,nc.file,nc.articleType,ct.content as typecontent,ct.type as typeofarticle,nc.fromwho,nc.authorid,nc.posted_date , nc.url
			FROM `my_images` mm
			LEFT JOIN {$this->dbshema}_news_content nc ON mm.contentid = nc.id
			LEFT JOIN {$this->dbshema}_news_content_type ct ON nc.articleType = ct.id
			WHERE mm.userid = '{$userid}' AND mm.type = 0
			AND mm.n_status = 1 ORDER BY mm.date DESC LIMIT {$start},{$limit}
		";
		
		$rqData = $this->apps->fetch($sql,1);
		if(!$rqData) return false;
		foreach ($rqData as $key => $val) {
			if ($val['typecontent']==0) {
				$val['typecontent'] = "article";
			} elseif($val['typecontent']==2) {
				$val['typecontent'] = "banner";
			} elseif($val['typecontent']==4) {
				$val['typecontent'] = "event";
			} else {
				$val['typecontent'] = "";
			}
			$val['id_image'] = $val['id'];
			$val['id'] = $val['contentid'];
			
			$rqData[$key] = $val;
			
			$rqData[$key]['imagepath'] = false;
								
			if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}event/{$val['image']}")) 	$rqData[$key]['imagepath'] = "event";
			if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}banner/{$val['image']}")) 	$rqData[$key]['imagepath'] = "banner";
			if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}article/{$val['image']}"))  	$rqData[$key]['imagepath'] = "article";	
			
			if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}event/small_{$val['image']}"))  	$rqData[$key]['image'] = "small_{$val['image']}";	
			if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}banner/small_{$val['image']}"))  	$rqData[$key]['image'] = "small_{$val['image']}";	
			if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}article/small_{$val['image']}"))  	$rqData[$key]['image'] = "small_{$val['image']}";	
			
			$video_thumbnail = false;
			if($val['url']!='')	{
				//parser url and get param data
				$parseUrl = parse_url($val['url']);
				if(array_key_exists('query',$parseUrl)) parse_str($parseUrl['query'],$parseQuery);
				else $parseQuery = false;
				if($parseQuery) {
					if(array_key_exists('v',$parseQuery))$video_thumbnail = $parseQuery['v'];
				} 
				$rqData[$key]['video_thumbnail'] = $video_thumbnail;
			}else $rqData[$key]['video_thumbnail'] = false;		
			
			if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}music/mp3/{$val['file']}")) $rqData[$key]['hasfile'] = true;
			else $rqData[$key]['hasfile'] = false;		
			
			$rqData[$key]['image_full_path'] = $CONFIG['BASE_DOMAIN_PATH'].$rqData[$key]['imagepath'];			
		}
		//pr($rqData);
		if($rqData) $qData=	$this->getStatistictArticle($rqData);		
		else $qData = false;
		
		if(!$qData) return false;
		$arrGallery['result'] = $qData;
		$arrGallery['total'] = $total['total'];
		return $arrGallery;
	}
	
	function hapusmygallery(){
		$cid = $this->apps->_p('cid');
		$sql = "UPDATE my_images set n_status = 0 WHERE id = {$cid}";
		if ($this->apps->query($sql)) {
			$data = array("status"=>1);
		} else {
			$data = array("status"=>0);
		}
		
		return $data;
	}
	
	function addUploadImage($data=false,$type=NULL){
		global $LOCALE;
		$fromwho =1;
		$type = intval($this->apps->_p('type'));
		$title = strip_tags($this->apps->_p('title'));
		// if($title=='') return false;
		$description = strip_tags($this->apps->_p('desc'));
		$tags = strip_tags($this->apps->_p('tags'));
		$brief = strip_tags($this->apps->_p('brief'));
		if($brief=='') $brief = $this->wordcut($description,10);
		$posted_date = strip_tags($this->apps->_p('posted_date'));
		$posted_date_times = strip_tags($this->apps->_p('times'));
		$expired_date = strip_tags($this->apps->_p('expired_date'));
		$city_event = intval($this->apps->_p('city_event'));
		$fid[] = $this->apps->_p('fid');
		$fid[] = $this->apps->_p('fidbrand');
		$fid[] = $this->apps->_p('fidarea');
		$fid[] = $this->apps->_p('fidpl');
		
		if($fid) $fid = implode(',',$fid);
		

		$prize = intval($this->apps->_p('prize'));
		$ftype[] = $this->apps->_p('ftype');
		$ftype[] = $this->apps->_p('ftypebrand');
		$ftype[] = $this->apps->_p('ftypearea');
		$ftype[] = $this->apps->_p('ftypepl');
		
		if($ftype) $ftype = implode(',',$ftype);
		
		$image = false;
		$file = false;
		if($data) {
		
			if(array_key_exists('arrImage',$data))	$image = $data['arrImage']['filename'];			
			if(array_key_exists('arrVideo',$data)) $image = $data['arrVideo']['filename'];
			if(array_key_exists('arrFile',$data)) $file = $data['arrFile']['filename'];
		}
		
		$url = strip_tags($this->apps->_p('url'));		
		
		if(!$this->uid) return false;
		$authorid = intval($this->uid);
		if(!$authorid) return false;

		
		if($posted_date=='') {
			$posted_date = date('Y-m-d H:i:s');
		} else {
			if($posted_date_times=='') {
				$posted_date = $posted_date." ".date('H:i:s');
			} else {
				$posted_date = $posted_date." ".$posted_date_times;
			}
		}
		if($expired_date=='') $expired_date = $posted_date;
		
		
		// plan  =  0	
		if(in_array($this->apps->_request('page'),$this->modzeropage)) $this->moderation = 0;
		
		$plantypes = $this->apps->user->plantypes;
		if($plantypes=='Brands') $this->moderation = 1;
		
		$leadertype = intval($this->apps->user->leaderdetail->type);
		if($leadertype)	if(in_array($leadertype,$this->approver)) $this->moderation = 1;
		
		if($leadertype==100) return false;
		$sql ="
			INSERT INTO {$this->dbshema}_news_content (cityid,brief,title,content,tags,image,articleType,created_date,posted_date,expired_date,authorid,fromwho,n_status,url,file) 
			VALUES ('{$city_event}','{$brief}','{$title}','{$description}','{$tags}','{$image}',{$type},NOW(),'{$posted_date}','{$expired_date}','{$authorid}','{$fromwho}',{$this->moderation},\"{$url}\",\"{$file}\")
			";
		// pr($sql);exit;
		$this->logger->log($sql);	
		
		if ($this->apps->query($sql)) {
			$this->logger->log($this->apps->getLastInsertId());	
			if($this->apps->getLastInsertId()>0) {
				$message = " {$this->apps->user->name} {$this->apps->user->last_name} {$LOCALE[1]['tagged']} {$title} ";
				$cid = $this->apps->getLastInsertId();
				$this->PointChallenge($prize,$cid);
			
			
				if($fid){	
				
					$arrfid = explode(',',$fid);
					$arrftype = explode(',',$ftype);
					$frienddata = false;
					if(is_array($arrfid)){
						foreach($arrfid as $key => $val){
							$frienddata[$key]['fid'] = $val;
							$frienddata[$key]['ftype'] = $arrftype[$key];
							
						}
						
						if($frienddata){
					
							foreach($frienddata as $val){
								if($val['fid']!=0){
								$this->addFriendTags($cid,$val['fid'],$val['ftype'],false,$message);
							
								}
							}
						
						}
					}else{
						$ftype = intval($ftype);
						if($fid!=0){
							$this->addFriendTags($cid,intval($fid),intval($ftype),false,$message);						
							
						}
					}
			}
						
				
				return true;
			}
		} else return false;
	}
	
	function getPagesCategory($pageid='photography',$checkpage=true){
		if($checkpage){
			$page = strip_tags($pageid);
			$sql ="SELECT * FROM {$this->dbshema}_news_content_page WHERE pagename='{$page}' LIMIT 1" ;
			// pr($sql);
			$pageData = $this->apps->fetch($sql); 
			if(!$pageData) return false;
			$pageid = intval($pageData['id']);
		}else $pageid = intval($pageid);
		
		$sql ="SELECT * FROM {$this->dbshema}_news_content_category WHERE pageid={$pageid} " ;
		
		$qData = $this->apps->fetch($sql,1);
		
		return $qData ; 
	}
	
	function PointChallenge($prize=null,$cid=null,$act=null){
		
		if ($act=="getpoint" && $cid){
			$sql ="SELECT * FROM {$this->dbshema}_news_content_challenge WHERE contentid = {$cid} AND n_status = 1 LIMIT 1";
			$qData = $this->apps->fetch($sql);
			if ($qData) {
				return $qData;
			} else return false;
		} else {
			if($prize && $cid){
				$sql ="INSERT INTO {$this->dbshema}_news_content_challenge (contentid,prize,date,n_status)
				VALUE ('{$cid}','{$prize}',NOW(),1)
				";			
				if ($this->apps->query($sql)) {
					return true;
				} else return false;
			}
		}
		return false;
	}
	
	function populartags($contenttype=0,$limit=5){
			
			$typeid = strip_tags($this->checkPage($contenttype));
			
			$limit = intval($limit);
			
			$sql ="	SELECT COUNT(*) total,content.id,content.tags
					FROM {$this->dbshema}_news_content content 
					LEFT JOIN tbl_activity_log log ON log.action_value = content.id
					WHERE log.action_id=2  AND content.n_status=1 AND content.articleType IN ({$typeid})
					GROUP BY content.id
					ORDER BY total DESC LIMIT {$limit}
					";
			// pr($sql);
			$qData = $this->apps->fetch($sql,1);
			
			if(!$qData) return false;
			$nametags = false;
			foreach($qData as $key => $val){
				if($val['tags']) $nametags[$val['id']] = $val['tags'];				
			}
			$qData = null;
						
			if($nametags)	return $nametags;
			return false;
	}
	
	function weeklyPopular ($contenttype=0,$limit=9){
			global $CONFIG;
			$typeid = strip_tags($this->checkPage($contenttype));
			
			$limit = intval($limit);
			//get between this week days
				//get monday of this week
					$mondaydate = date("Y-m-d",strtotime('last monday', strtotime('next sunday')));
				
			$sql ="	
					SELECT COUNT(*) total,content.*
					FROM {$this->dbshema}_news_content content 
					LEFT JOIN tbl_activity_log log ON log.action_value = content.id
					WHERE log.date_time BETWEEN '{$mondaydate}' AND DATE_ADD(NOW(),INTERVAL 1 DAY) AND content.n_status=1 AND articleType IN ({$typeid})
					GROUP BY content.id
					ORDER BY total DESC LIMIT {$limit}
					";
	
			$qData = $this->apps->fetch($sql,1);

			if(!$qData) return false;
			foreach($qData as $key => $val){
			$qData[$key]['imagepath'] = false;
								
			if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}event/{$val['image']}")) 	$qData[$key]['imagepath'] = "event";
			if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}banner/{$val['image']}")) 	$qData[$key]['imagepath'] = "banner";
			if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}article/{$val['image']}"))  	$qData[$key]['imagepath'] = "article";	
			
			if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}event/small_{$val['image']}"))  	$qData[$key]['image'] = "small_{$val['image']}";	
			if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}banner/small_{$val['image']}"))  	$qData[$key]['image'] = "small_{$val['image']}";	
			if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}article/small_{$val['image']}"))  	$qData[$key]['image'] = "small_{$val['image']}";	
			
			if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}event/thumbnail_{$val['thumbnail_image']}")) $qData[$key]['image'] = "thumbnail_{$val['image']}";	
			if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}banner/thumbnail_{$val['thumbnail_image']}")) $qData[$key]['image'] = "thumbnail_{$val['image']}";	
			if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}article/thumbnail_{$val['thumbnail_image']}")) $qData[$key]['image'] = "thumbnail_{$val['image']}";	
			
			if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}event/square{$val['image']}")) $qData[$key]['image'] = "square{$val['image']}";
			if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}banner/square{$val['image']}")) $qData[$key]['image'] = "square{$val['image']}";
			if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}article/square{$val['image']}")) $qData[$key]['image'] = "square{$val['image']}";
			
			
			//parseurl for video thumb
			$video_thumbnail = false;
			if($val['url']!='')	{
				//parser url and get param data
				$parseUrl = parse_url($val['url']);
				if(array_key_exists('query',$parseUrl)) parse_str($parseUrl['query'],$parseQuery);
				else $parseQuery = false;
				if($parseQuery) {
					if(array_key_exists('v',$parseQuery))$video_thumbnail = $parseQuery['v'];
				} 
				$qData[$key]['video_thumbnail'] = $video_thumbnail;
			}else $qData[$key]['video_thumbnail'] = false;		
			
			if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}music/mp3/{$val['file']}")) $qData[$key]['hasfile'] = true;
			else $qData[$key]['hasfile'] = false;			
		}
		if($qData) $qData=	$this->getStatistictArticle($qData);
		else $qData = false;
		return $qData;
	}
	
	
	function addFavorite(){
		
		if(!$this->uid) return false;
		$cid = intval($this->apps->_p("cid"));
		$emoid = intval($this->apps->_p("emoid"));
		if($cid==0) return false;
		
		/*
		$sql = "
		SELECT count(*) total 
		FROM {$this->dbshema}_news_content_favorite
		WHERE userid={$this->uid} AND contentid={$cid} LIMIT 1";
		
		$qData = $this->apps->fetch($sql);
		
		if(!$qData) return false;
		if($qData['total']>0) return false;		
		*/
		
		$sql=" 
				INSERT INTO 
				{$this->dbshema}_news_content_favorite 	(userid ,	contentid 	,likes, 	date ,	n_status  ) 
				VALUES ({$this->uid},{$cid},{$emoid},NOW(),1)
				ON DUPLICATE KEY UPDATE likes={$emoid} , date = NOW()
				";
		$this->apps->query($sql);
		if($this->apps->getLastInsertId()>0) return true;
		return false;
	
	}
	function getFriendTagged($strCid=null,$tagin=0,$authorid=true){
		if(!$this->uid) return false;
		if($strCid==null) return false;
		if($authorid)$quserid = " AND userid={$this->uid} ";
		else $quserid = "";
		$sql = "
		SELECT * 
		FROM {$this->dbshema}_news_content_tags
		WHERE contentid IN ({$strCid}) {$quserid} AND n_status = 1 AND tagin = {$tagin} ";
		
		// pr($sql);
		$this->logger->log($sql);
		$qData = $this->apps->fetch($sql,1);
		if(!$qData) return false;
		$data = false;
		$arrSocialFid = false;
		$arrEntourageFid = false;
				foreach($qData as $val){	
				 // friendid 	friendtype 	date 	tagin 	n_status 
					/* BA */	
					if($val['friendtype']==0) $arrEntourageFid[$val['friendid']] = $val['friendid'];
					/* entourage */
					if($val['friendtype']==1) $arrSocialFid[$val['friendid']] = $val['friendid'];
					
					$frienddata[$val['friendtype']][$val['friendid']]= $val;
				}
				$socialdata = false;
				$entouragedata = false;
				if($arrSocialFid) {
					$strsocialfid = implode(',',$arrSocialFid);
					$socialfid = $this->apps->userHelper->socialdata($strsocialfid);
					if($socialfid){
						foreach($socialfid as $key => $val){
							$socialfid[$key]['friendtype'] = 1;
							$socialdata[1][$val['id']]=$socialfid[$key];
						}
					}
				}
				
				if($arrEntourageFid) {
					$strentouragefid = implode(',',$arrEntourageFid);
					$this->logger->log($strentouragefid);
					$entouragefid = $this->apps->userHelper->entouragedata($strentouragefid,false);
					$this->logger->log(json_encode($entouragefid));
					if($entouragefid){
						foreach($entouragefid as $key => $val){
								$entouragefid[$key]['friendtype'] = 0;
								$entouragedata[0][$val['id']]=$entouragefid[$key];
						}
					}
				}
				
				if(!$frienddata) return false;
				
				//merge data
				foreach($frienddata as $keyftype => $ftype){
					foreach($ftype as $key => $val){
					if($socialdata)if(array_key_exists($keyftype,$socialdata)) if(array_key_exists($key,$socialdata[$keyftype]))  $data[$val['contentid']][] = $socialdata[$keyftype][$key];
					if($entouragedata) if(array_key_exists($keyftype,$entouragedata)) if(array_key_exists($key,$entouragedata[$keyftype]))  $data[$val['contentid']][] = $entouragedata[$keyftype][$key];
					 // pr($val);
					}
					
				}
		
		if($data) return $data;
		return false;
	} 
	
	function addFriendTags($cid=0,$fid=0,$ftype='pass',$gallery=false,$message=false){
		
		if(!$this->uid) return false;
		if( $cid==0 ) $cid = intval($this->apps->_p("cid"));
		if( $fid==0 ) $fid = intval($this->apps->_p("fid"));
		if( $ftype=='pass') $ftype = intval($this->apps->_p("ftype"));
			$gallery = intval($gallery);
		if( $cid==0) return false;
		global $LOCALE;
		
		if($message==false) {
			$sql=" SELECT title FROM {$this->dbshema}_news_content WHERE id={$cid} LIMIT 1";
			$qData = $this->apps->fetch($sql);
			if($qData) $message = " {$this->apps->user->name} {$this->apps->user->last_name} {$LOCALE[1]['tagged']} {$qData['title']} ";
			else $message =  " {$this->apps->user->name} {$this->apps->user->last_name} {$LOCALE[1]['tagged']} ";
		}
		$sql=" 
				INSERT INTO 
				{$this->dbshema}_news_content_tags 	(userid ,	contentid 	,friendid, 	friendtype, date ,	n_status, tagin ) 
				VALUES ({$this->uid},{$cid},{$fid},{$ftype},NOW(),1,{$gallery})
				ON DUPLICATE KEY UPDATE n_status = 1
				";
			// pr($sql);exit;
			$this->logger->log($sql);
			$this->logger->log($message);
		$this->apps->query($sql);
		if($this->apps->getLastInsertId()>0) {
			if($message){
				// $this->apps->messageHelper->createMessage($fid,$message,$ftype);
				$this->apps->notif($message,$cid);
			}			
			return true;
		}
		return false;
	
	}
	
	function unFriendTags($cid=0,$fid=0,$ftype='pass',$gallery=false){
		
		if(!$this->uid) return false;
		if( $cid==0 ) $cid = intval($this->apps->_p("cid"));
		if( $fid==0 ) $fid = intval($this->apps->_p("fid"));
		if( $ftype=='pass') $ftype = intval($this->apps->_p("ftype"));
			$gallery = intval($gallery);
		if( $cid==0) return false;
		
		
			
		$sql=" 	UPDATE 
				{$this->dbshema}_news_content_tags SET n_status = 0 WHERE
				userid={$this->uid} AND friendid ={$fid} AND contentid={$cid}  AND tagin = {$gallery} LIMIT 1
				";
		$data = $this->apps->query($sql);
		if($data) return true;
		return false;
	
	}
	
	function friendTagsSearch(){
		
		$limit = 16;
		$start= intval($this->apps->_request('start'));
		$searchKeyOn = array("name","email","last_name");
		$keywords = strip_tags($this->apps->_request('keywords'));	
		$keywords = rtrim($keywords);
		$keywords = ltrim($keywords);
		
		$realkeywords = $keywords;
		$keywords = '';
		
		if(strpos($keywords,' ')) $parseKeywords = explode(' ', $keywords);
		else $parseKeywords = false;
		
		if(is_array($parseKeywords)) $keywords = $keywords.'|'.trim(implode('|',$parseKeywords));
		else  $keywords = trim($keywords);
		
		if(!$realkeywords){
			if($keywords!=''){
				foreach($searchKeyOn as $key => $val){
					$searchKeyOn[$key] = " {$val} REGEXP '{$keywords}' ";
				}
				$strSearchKeyOn = implode(" OR ",$searchKeyOn);
				$qKeywords = " 	AND  ( {$strSearchKeyOn} )";
			}else $qKeywords = " ";
		}else{
			foreach($searchKeyOn as $key => $val){
				$searchKeyOn[$key] = " {$val} like '{$realkeywords}%' ";
				if($val=="email") $searchKeyOn[$key] = " {$val} = '{$realkeywords}' ";
				if($val=="last_name") $searchKeyOn[$key] = " {$val} like '%{$realkeywords}%' ";
				
			}
			$strSearchKeyOn = implode(" OR ",$searchKeyOn);
			$qKeywords = " 	AND  ( {$strSearchKeyOn} )";
		}
		$sql = "SELECT count(*) total FROM my_entourage WHERE n_status =1  {$qKeywords} ORDER BY name ASC ";
		$total = $this->apps->fetch($sql);
		if(!$total) return false;
		
		$sql = "SELECT id,name,img,email,IF(last_name IS NULL,'',last_name) last_name , referrerbybrand FROM my_entourage WHERE n_status =1  {$qKeywords} ORDER BY name ASC, last_name ASC LIMIT {$start},{$limit}";
	
		$qData = $this->apps->fetch($sql,1);
	
		if(!$qData) return false;
		foreach($qData as $key => $val){
			$arrFriends[$val['id']] = $val['id']; 
			if($val['referrerbybrand']==$this->uid) $qData[$key]['isFriends'] = true;
			else $qData[$key]['isFriends'] =false;
		}
		
		if($qData){
			$data['result'] = $qData;
			$data['total'] = $total['total'];
			$data['myid'] = intval($this->uid);
		}
		return $data;
	}
	
	function getnewestupload(){
		global $CONFIG;
		$sql = "
			SELECT anc.id,anc.title,anc.brief,image,thumbnail_image,slider_image,posted_date,file,url,fromwho,tags,authorid,topcontent,cityid , anct.type,anct.content,anc.articleType,anct.type pagesname
			FROM {$this->dbshema}_news_content  anc
			LEFT JOIN {$this->dbshema}_news_content_type anct ON anc.articleType = anct.id
			WHERE n_status = 1   AND anc.articleType <> 0
			ORDER BY created_date DESC , id DESC
			LIMIT 4";
			$qData = $this->apps->fetch($sql,1);
		if(!$qData) return false;
		//cek detail image from folder
			//if is article, image banner do not shown
		foreach($qData as $key => $val){
			$qData[$key]['imagepath'] = false;
								
			if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}event/{$val['image']}")) 	$qData[$key]['imagepath'] = "event";
			if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}banner/{$val['image']}")) 	$qData[$key]['imagepath'] = "banner";
			if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}article/{$val['image']}"))  	$qData[$key]['imagepath'] = "article";	
			
			if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}event/small_{$val['image']}"))  	$qData[$key]['image'] = "small_{$val['image']}";	
			if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}banner/small_{$val['image']}"))  	$qData[$key]['image'] = "small_{$val['image']}";	
			if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}article/small_{$val['image']}"))  	$qData[$key]['image'] = "small_{$val['image']}";	
			
			if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}event/thumbnail_{$val['thumbnail_image']}")) $qData[$key]['image'] = "thumbnail_{$val['image']}";	
			if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}banner/thumbnail_{$val['thumbnail_image']}")) $qData[$key]['image'] = "thumbnail_{$val['image']}";	
			if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}article/thumbnail_{$val['thumbnail_image']}")) $qData[$key]['image'] = "thumbnail_{$val['image']}";	
			
			if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}event/square{$val['image']}")) $qData[$key]['image'] = "square{$val['image']}";
			if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}banner/square{$val['image']}")) $qData[$key]['image'] = "square{$val['image']}";
			if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}article/square{$val['image']}")) $qData[$key]['image'] = "square{$val['image']}";
			
			
			//parseurl for video thumb
			$video_thumbnail = false;
			if($val['url']!='')	{
				//parser url and get param data
				$parseUrl = parse_url($val['url']);
				if(array_key_exists('query',$parseUrl)) parse_str($parseUrl['query'],$parseQuery);
				else $parseQuery = false;
				if($parseQuery) {
					if(array_key_exists('v',$parseQuery))$video_thumbnail = $parseQuery['v'];
				} 
				$qData[$key]['video_thumbnail'] = $video_thumbnail;
			}else $qData[$key]['video_thumbnail'] = false;		
			
			if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}music/mp3/{$val['file']}")) $qData[$key]['hasfile'] = true;
			else $qData[$key]['hasfile'] = false;			
		}
		if($qData) $qData=	$this->getStatistictArticle($qData);
		else $qData = false;
		//pr($qData);
		return $qData;
	}
	
	function setCover(){
		global $CONFIG;
		include_once '../engines/Utility/phpthumb/ThumbLib.inc.php';
		
		$cid = intval($this->apps->_request('cid'));
		$fromwhere = intval($this->apps->_request('fromwhere'));
		$typeofpage = intval($this->apps->_request('typeofpage')); //used by who
	
		//type of page , define user pages
		if($typeofpage!=0){
			$myid = $this->pageid; // wait for session
		}else $myid = $this->uid;
		$image = false;
		//if from content, get image content
		$sql ="SELECT id,image FROM {$this->dbshema}_news_content WHERE  id={$cid} AND fromwho = {$fromwhere} LIMIT 1;";
		$data = $this->apps->fetch($sql);
	
		if(!$data) return false;
		if($typeofpage==0) $coverfolder = "user/cover/";
		else $coverfolder = "pages/cover/";
		$folder = $CONFIG['LOCAL_PUBLIC_ASSET'];
		$image = $data['image'];	
		copy($folder."article/".$image,$folder.$coverfolder.$image);
	
		//userid 	image 	otherid 	fromwhere 0:news_content;1:my	type 0:user;1-n mypagestype	n_status 
		$sql =" 
		INSERT INTO my_wallpaper ( myid,	image ,	otherid ,	fromwhere ,type, n_status ,datetime )
		VALUES ({$myid},'{$image}',{$cid},{$fromwhere},{$typeofpage},1,NOW())
		ON DUPLICATE KEY UPDATE datetime=NOW()
		";
		//pr($sql);
		$this->apps->query($sql);
		
		if($this->apps->getLastInsertId()>0) {
			$sql =" 
			INSERT INTO my_images ( userid,	contentid ,	date ,	n_status )
			VALUES ({$myid},{$cid},NOW(),1)
			ON DUPLICATE KEY UPDATE date=NOW()
			";
			
			$this->apps->query($sql);
			return true;
		} else return false;
		
	}
	
	function setPlaylist(){
		$cid = intval($this->apps->_request('cid'));
		$fromwhere = intval($this->apps->_request('fromwhere'));
		$typeofpage = intval($this->apps->_request('typeofpage'));
		$authorid = intval($this->apps->_request('authorid'));
		
		//check user have myid relation		
		$sql ="SELECT id, ownerid FROM my_pages WHERE ownerid={$this->uid} AND n_status<> 3 LIMIT 1";
		$data = $this->apps->fetch($sql);
		if($data) {
			if($data['id']==$authorid) {
				return false;
			} else {
				$myid = $this->uid;
			}
		} else {
			$myid = $this->uid;
		}
		
		//get file content
		$file = false;
		$sql ="SELECT id,file FROM {$this->dbshema}_news_content WHERE  id={$cid} AND fromwho = {$fromwhere} LIMIT 1;";
		$data = $this->apps->fetch($sql);
	
		if(!$data) return false;
		$file = $data['file'];
		
		//get type of page
		$type = false;
		$sql ="SELECT id,type FROM my_pages WHERE  id={$authorid} LIMIT 1;";
		$arrtype = $this->apps->fetch($sql);
		if (!$arrtype) return false;
		$type = $arrtype['type'];
		
		//userid  image  otherid  fromwhere 0:news_content;1:my	type 0:user;1-n mypagestype	n_status 
		$sql =" 
			INSERT INTO my_playlist (myid,file,otherid,fromwhere,type,n_status,datetime) VALUES ({$myid},'{$file}',{$cid},{$fromwhere},{$type},1,NOW())
			ON DUPLICATE KEY UPDATE datetime=NOW()
		";
		
		$this->apps->query($sql);
		
		if($this->apps->getLastInsertId()>0) {
			/*
			$sql =" 
			INSERT INTO my_images ( userid,	contentid ,	date ,	n_status )
			VALUES ({$myid},{$cid},NOW(),1)
			ON DUPLICATE KEY UPDATE date=NOW()
			";
			
			$this->apps->query($sql);
			*/
			return true;
		} else return false;
	}
		
	function getMyFavorite($userid=0,$limit=10){
			global $CONFIG;
			
			if($userid==0) return false;
			$start = intval($this->apps->_request('start'));
			$sql ="
					SELECT contentid FROM {$this->dbshema}_news_content_favorite WHERE n_status=  1 AND userid = {$userid} ORDER BY date DESC  LIMIT {$start},{$limit}
					";

				$qData = $this->apps->fetch($sql,1);
				if($qData) {
					$this->logger->log("have favorite");
					foreach($qData as $val){
						$favoriteData[$val['contentid']]=$val['contentid'];
					}
				
					if(!$favoriteData) return false;
					$strcontentid = implode(',',$favoriteData);
					
					//get content
					$sql = "
					SELECT anc.id,anc.title,anc.brief,anc.image,anc.thumbnail_image,anc.slider_image,anc.posted_date,anc.file,anc.url,anc.fromwho,anc.tags,anc.authorid,anc.topcontent,anc.cityid,anct.type pagesname FROM athreesix_news_content anc
					LEFT JOIN {$this->dbshema}_news_content_type anct ON anct.id = anc.articleType					
					WHERE anc.id IN ({$strcontentid}) AND anc.n_status=1 LIMIT {$limit}";
			
					$qData = $this->apps->fetch($sql,1);
					if($qData){
						foreach($qData as $val){
							$arrContent[] = $val;
						}
					}else $arrContent = false;
					
					if($arrContent){
						
						foreach($arrContent as $key => $val){
							$arrContent[$key]['imagepath'] = false;
								
							if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}event/{$val['image']}")) 	$arrContent[$key]['imagepath'] = "event";
							if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}banner/{$val['image']}")) 	$arrContent[$key]['imagepath'] = "banner";
							if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}article/{$val['image']}"))  	$arrContent[$key]['imagepath'] = "article";	
							
							if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}event/small_{$val['image']}"))  	$arrContent[$key]['image'] = "small_{$val['image']}";	
							if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}banner/small_{$val['image']}"))  	$arrContent[$key]['image'] = "small_{$val['image']}";	
							if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}article/small_{$val['image']}"))  	$arrContent[$key]['image'] = "small_{$val['image']}";	
							
							$video_thumbnail = false;
							if($val['url']!='')	{
								//parser url and get param data
								$parseUrl = parse_url($val['url']);
								if(array_key_exists('query',$parseUrl)) parse_str($parseUrl['query'],$parseQuery);
								else $parseQuery = false;
								if($parseQuery) {
									if(array_key_exists('v',$parseQuery))$video_thumbnail = $parseQuery['v'];
								} 
								$arrContent[$key]['video_thumbnail'] = $video_thumbnail;
							}else $arrContent[$key]['video_thumbnail'] = false;		
							
							if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}music/mp3/{$val['file']}")) $arrContent[$key]['hasfile'] = true;
							else $arrContent[$key]['hasfile'] = false;		
						}
						
						$arrContent = $this->apps->contentHelper->getStatistictArticle($arrContent);
						return $arrContent;
					}else return false;
				}
		return false;
			
			
			
			
	}
	
	
	function getContestSubmission($userid=0,$mypagestype=0,$limit=10){
			
			global $CONFIG;
			if($userid==0) return false;
			$start = intval($this->apps->_request('start'));
			$sql ="
					SELECT contestid FROM my_contest WHERE n_status=  1 AND otherid = {$userid}  AND mypagestype={$mypagestype} LIMIT {$start},{$limit}
					";
		// pr($sql);
				$qData = $this->apps->fetch($sql,1);
				if($qData) {
					$this->logger->log("have contest");
					foreach($qData as $val){
						$contestData[$val['contestid']]=$val['contestid'];
					}

					if(!$contestData) return false;
					$strcontentid = implode(',',$contestData);
					
					//get content
					$sql = "
					SELECT anc.id,anc.title,anc.brief,anc.image,anc.thumbnail_image,anc.slider_image,anc.posted_date,anc.file,anc.url,anc.fromwho,anc.tags,anc.authorid,anc.topcontent,anc.cityid,anct.type pagesname FROM athreesix_news_content anc
					LEFT JOIN {$this->dbshema}_news_content_type anct ON anct.id = anc.articleType					
					WHERE anc.id IN ({$strcontentid}) AND anc.n_status=1 LIMIT {$limit}";
			
					$qData = $this->apps->fetch($sql,1);
					if($qData){
						foreach($qData as $key => $val){
							$qData[$key]['imagepath'] = false;
								
							if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}event/{$val['image']}")) 	$qData[$key]['imagepath'] = "event";
							if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}banner/{$val['image']}")) 	$qData[$key]['imagepath'] = "banner";
							if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}article/{$val['image']}"))  	$qData[$key]['imagepath'] = "article";	
							
							if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}event/small_{$val['image']}"))  	$qData[$key]['image'] = "small_{$val['image']}";	
							if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}banner/small_{$val['image']}"))  	$qData[$key]['image'] = "small_{$val['image']}";	
							if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}article/small_{$val['image']}"))  	$qData[$key]['image'] = "small_{$val['image']}";	
							
							$video_thumbnail = false;
							if($val['url']!='')	{
								//parser url and get param data
								$parseUrl = parse_url($val['url']);
								if(array_key_exists('query',$parseUrl)) parse_str($parseUrl['query'],$parseQuery);
								else $parseQuery = false;
								if($parseQuery) {
									if(array_key_exists('v',$parseQuery))$video_thumbnail = $parseQuery['v'];
								} 
								$qData[$key]['video_thumbnail'] = $video_thumbnail;
							}else $qData[$key]['video_thumbnail'] = false;	
							
							if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}music/mp3/{$val['file']}")) $qData[$key]['hasfile'] = true;
							else $qData[$key]['hasfile'] = false;		
							
							$arrContent[] = $qData[$key];
							
						}
					}else $arrContent = false;
					
					if($arrContent){
						$arrContent = $this->apps->contentHelper->getStatistictArticle($arrContent);
						return $arrContent;
					}else return false;
				}
		return false;			
	}
	
	
	function getMyCalendar($userid=0,$mypagestype=0,$limit=10){
			
			global $CONFIG;
			if($userid==0) return false;
			$contestData = false;
			$start = intval($this->apps->_request('start'));
			if($mypagestype>=2) $qFromWho = " AND anc.fromwho = {$mypagestype} ";
			else $qFromWho = "";
					$sql ="
					SELECT contestid FROM my_contest WHERE n_status=  1 AND otherid = {$userid}  AND mypagestype={$mypagestype} LIMIT {$start},{$limit}
					";
			
				$qData = $this->apps->fetch($sql,1);
				// pr($sql);
					if($qData) {
						$this->logger->log("have contest");
						foreach($qData as $val){
							$contestData[$val['contestid']]=$val['contestid'];
						}
					}
					
					if($contestData){
						$strcontentid = implode(',',$contestData);
						$qContentId = " anc.id IN ({$strcontentid}) OR  ";
					}else $qContentId = "";
					//get content
					$sql = "
					SELECT anc.id,anc.title,anc.brief,anc.image,anc.thumbnail_image,anc.slider_image,anc.posted_date,anc.file,anc.url,anc.fromwho,anc.tags,anc.authorid,anc.topcontent,anc.cityid,anct.type pagesname FROM {$this->dbshema}_news_content anc
					LEFT JOIN {$this->dbshema}_news_content_type anct ON anct.id = anc.articleType					
					WHERE {$qContentId} authorid={$userid}  AND anc.n_status=1 {$qFromWho} AND articleType= 5 ORDER BY anc.posted_date DESC LIMIT {$limit}";
						// pr($sql);exit;
					$qData = $this->apps->fetch($sql,1);
					
					if($qData){
						foreach($qData as $key => $val){
							$qData[$key]['imagepath'] = false;
								
							if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}event/{$val['image']}")) 	$qData[$key]['imagepath'] = "event";
							if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}banner/{$val['image']}")) 	$qData[$key]['imagepath'] = "banner";
							if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}article/{$val['image']}"))  	$qData[$key]['imagepath'] = "article";	
							
							if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}event/small_{$val['image']}"))  	$qData[$key]['image'] = "small_{$val['image']}";	
							if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}banner/small_{$val['image']}"))  	$qData[$key]['image'] = "small_{$val['image']}";	
							if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}article/small_{$val['image']}"))  	$qData[$key]['image'] = "small_{$val['image']}";	
							
							$video_thumbnail = false;
							if($val['url']!='')	{
								//parser url and get param data
								$parseUrl = parse_url($val['url']);
								if(array_key_exists('query',$parseUrl)) parse_str($parseUrl['query'],$parseQuery);
								else $parseQuery = false;
								if($parseQuery) {
									if(array_key_exists('v',$parseQuery))$video_thumbnail = $parseQuery['v'];
								} 
								$qData[$key]['video_thumbnail'] = $video_thumbnail;
							}else $qData[$key]['video_thumbnail'] = false;	
							
							if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}music/mp3/{$val['file']}")) $qData[$key]['hasfile'] = true;
							else $qData[$key]['hasfile'] = false;		
							
							$arrContent[] = $qData[$key];
							
						}
					}else $arrContent = false;
					
					if($arrContent){
						$arrContent = $this->apps->contentHelper->getStatistictArticle($arrContent);
						return $arrContent;
					}else return false;
			
		return false;			
	}
	
	
	function setEditContent(){
		global $CONFIG;
		$cid = intval($this->apps->_p('article_id'));
		$title = strip_tags($this->apps->_p('title'));
		$content = strip_tags($this->apps->_p('description'));
		$tags = strip_tags($this->apps->_p('tags'));		
		
		$sql = "UPDATE {$this->dbshema}_news_content SET title = \"{$title}\",content = \"{$content}\",tags = '{$tags}' WHERE id = '{$cid}'  AND authorid={$this->uid} AND fromwho=1 ";
		if ($this->apps->query($sql)) {
			return true;
		} else return false;
		return false;
	}
	
	function unContentPost(){
		$cid = intval($this->apps->_p('cid'));
		$type = intval($this->apps->_p('type'));
		
		if($cid==0) return false;
		if($type==0) return false;
		if($this->uid==0) return false;
		
		$sql = "UPDATE {$this->dbshema}_news_content SET n_status = 3 WHERE id = '{$cid}' AND fromwho=1 AND articleType={$type} LIMIT 1";
		if ($this->apps->query($sql)) {
			return true;
		} else return false;
		return false;
	}
	
	function unVenueReference($act=null) {
		
		if ($act=="venueid") {
			$cid = intval($this->apps->_p('cid'));
			$idvenue = intval($this->apps->_p('idvenue'));
		
			$sql = "UPDATE {$this->dbshema}_venue_reference SET venueid = {$idvenue} WHERE id = '{$cid}'";
			if ($this->apps->query($sql)) {
				return true;
			} else return false;		
		} else {
			$cid = intval($this->apps->_request('id'));
			
			if($cid==0) return false;
			if($this->uid==0) return false;
			
			$sql = "UPDATE {$this->dbshema}_venue_reference SET n_status = 3 WHERE id = '{$cid}'";
			if ($this->apps->query($sql)) {
				return true;
			} else return false;
		}		
		return false;
	}
	
	function unCommentPost(){
		$id = intval($this->apps->_p('id'));
		
		if($id==0) return false;
		if($this->uid==0) return false;
		
		$sql = "UPDATE {$this->dbshema}_news_content_comment SET n_status = 3 WHERE id = '{$id}' LIMIT 1";		
		if ($this->apps->query($sql)) {
			return true;
		} else return false;
		return false;
	}
	
	function unComment(){
		
		$cid = intval($this->apps->_p('cid'));
		if($this->uid==0) return false;
		
		if($cid==0) return false;
			
		$sql = "UPDATE {$this->dbshema}_news_content_comment SET n_status = 0 WHERE id = '{$cid}' AND userid={$this->uid} LIMIT 1";
		if ($this->apps->query($sql)) {
			return true;
		} else return false;
		return false;
	}
	
	
	function wordcut($str=null,$num=1){
			if($str==null) return false;
			
			$arrStr = explode(" ",$str);
			$arrNewStr = false;
			foreach($arrStr as $key => $val){
				if($key<=$num){
					$arrNewStr[] = $val;
				}else break;
			}
			if($arrNewStr==false) return false;
			$str = implode(" ",$arrNewStr);
			return $str;
	
	}
	function clearphotogallery(){
		$id = intval($this->apps->_request('id'));
		
		$sql ="UPDATE {$this->dbshema}_news_content_repo SET n_status = 3 WHERE id={$id} LIMIT 1 ";
		
		return $this->apps->query($sql);
		
	}
	function clearphotocovergallery(){
		$id = intval($this->apps->_request('id'));
		
		$sql ="UPDATE {$this->dbshema}_news_content SET image='' WHERE id={$id} LIMIT 1 ";
		
		return $this->apps->query($sql);
		
	}
	function addUploadImageGallery($data=false,$type=NULL){
		global $LOCALE;
		$fromwho =1;
		$type = intval($this->apps->_p('type'));
		$cid = intval($this->apps->_p('cid'));
		$title = strip_tags($this->apps->_p('title'));
		$brief = strip_tags($this->apps->_p('brief'));
		$content = strip_tags($this->apps->_p('desc'));
		$friendtags = $this->apps->_p('fid');
		$friendtypetags = $this->apps->_p('ftype');
		
		if($data) {
			 $image = false;
			if(array_key_exists('arrImage',$data))	$image = $data['arrImage']['filename'];			
			if(array_key_exists('arrVideo',$data)) $image = $data['arrVideo']['filename'];		
			
		}
		
		if(!$this->uid) return false;
		$authorid = intval($this->uid);
		if(!$authorid) return false;
		
		$sql ="
			INSERT INTO {$this->dbshema}_news_content_repo (`title`, `brief`, `content`, `typealbum`, `gallerytype`, `files`, `fromwho`, `otherid`, `created_date`, `n_status`,authorid) VALUES ( '{$title}', '{$brief}', '{$content}', '1', '0', '{$image}', '1', '{$cid}', NOW(), '1',{$authorid});
			";
			
			// pr($sql);exit;
	
			$this->apps->query($sql);
			if($this->apps->getLastInsertId()>0){
				$message = " {$this->apps->user->name} {$this->apps->user->last_name} {$LOCALE[1]['tagged']} {$title} ";
				$galleryid = $this->apps->getLastInsertId();
				if($friendtags){					
					$arrfid = explode(',',$friendtags);
					$arrftype = explode(',',$friendtypetags);
					$frienddata = false;
					if(is_array($arrfid)){
						foreach($arrfid as $key => $val){
							$frienddata[$key]['fid'] = $val;
							$frienddata[$key]['ftype'] = $arrftype[$key];
							
						}
						
						if($frienddata){
						
							foreach($frienddata as $val){
								$this->addFriendTags($galleryid,$val['fid'],$val['ftype'],true,$message);
							}
						
						}
					}else{
						$friendtypetags = intval($friendtypetags);
						$this->addFriendTags($galleryid,intval($friendtags),intval($friendtypetags),true,$message);
					}
				}
								
				return true;
			
			}else return false;
				
			
		
	}
	
	function editContentArticle($data=false){
		global $CONFIG,$LOCALE;
		$fromwho =1;
	
		$id = intval($this->apps->_p('id'));
		$qSet['title'] = strip_tags($this->apps->_p('title'));
		$qSet['content'] = strip_tags($this->apps->_p('desc'));
		$qSet['tags'] = strip_tags($this->apps->_p('tags'));
		$qSet['brief'] = strip_tags($this->apps->_p('brief'));
		$qSet['n_status'] = $this->apps->_p('n_status');
		$reason = strip_tags($this->apps->_p('reason'));
		if($qSet['brief']=='') $qSet['brief'] = $this->wordcut($qSet['content'],10);
		$qSet['posted_date'] = strip_tags($this->apps->_p('posted_date'));
		$times = strip_tags($this->apps->_p('times'));
		$qSet['expired_date'] = strip_tags($this->apps->_p('expired_date'));
	
		$fid = $this->apps->_p('fid');
		$ftype = $this->apps->_p('ftype');
	
		if($qSet['n_status']!='') {
			$n_status = intval($qSet['n_status']);
			$qSet['n_status'] = $n_status;
		}
		
		if($data) {
		
			if(array_key_exists('arrImage',$data))	$qSet['image'] = $data['arrImage']['filename'];			
			if(array_key_exists('arrVideo',$data)) $qSet['image'] = $data['arrVideo']['filename'];
			if(array_key_exists('arrFile',$data)) $qSet['file'] = $data['arrFile']['filename'];
		}
		
		
		$url = strip_tags($this->apps->_p('url'));		
		
		if(!$this->uid) return false;
			
		if($qSet['posted_date']=='') $qSet['posted_date'] = date('Y-m-d H:i:s');		
		if($qSet['expired_date'] =='') $qSet['expired_date'] = $qSet['posted_date'];	
		if($times!='') {
					$posted_date = explode(' ',$qSet['posted_date']);
					if(is_array($posted_date)){
						$datetimes[0] = $posted_date[0];
						$datetimes[1] = $times;
						$qSet['posted_date'] = implode(' ',$datetimes);
					}
		}
		$qUpdates = false;
		$qSetUpdate = false;
		foreach($qSet as $key => $val){
			if($val!='') $qSetUpdate[]="{$key}=\"{$val}\"";
		}
		if($qSetUpdate){
			$qUpdates = implode(',',$qSetUpdate);
		}
		if(!$qUpdates) return false;
		
		
			
		$qWhere = " AND authorid={$this->uid} AND fromwho=1  ";
		
		$leadertype = intval($this->apps->user->leaderdetail->type);
		if($leadertype){
			$approver = $this->approver;
			if(in_array($leadertype,$approver)){
					$qWhere = "   ";
			}
		}
		
		$sql = "UPDATE {$this->dbshema}_news_content 
		SET {$qUpdates}
		WHERE id = '{$id}' {$qWhere} ";
	
		if ($this->apps->query($sql)) {
			if($reason) $message = $reason;
			else $message = " {$this->apps->user->name} {$this->apps->user->last_name} {$LOCALE[1]['tagged']} {$qSet['title']} ";
			
			
			if($fid){					
					$arrfid = explode(',',$fid);
					$arrftype = explode(',',$ftype);
					$frienddata = false;
					if(is_array($arrfid)){
						foreach($arrfid as $key => $val){
							$frienddata[$key]['fid'] = $val;
							$frienddata[$key]['ftype'] = $arrftype[$key];
						
						}
						
						if($frienddata){
					
							foreach($frienddata as $val){
								
								$this->addFriendTags($id,$val['fid'],$val['ftype'],false,$message);
								
							}
						
						}
					}else{
						$ftype = intval($ftype);
					
						$this->addFriendTags($id,intval($fid),intval($ftype),false,$message);						
						
					}
			}
						
			return true;
		} else return false;
		return false;
	}
	
	
	function getCheckin($cidstr=false){
		if($cidstr==false) return false;
		$sql = " 
		SELECT checkin.*,master.provinceName,master.city cityname,master.venuecategory, master.venuename 
		FROM my_checkin checkin 
		LEFT JOIN {$this->dbshema}_venue_master master ON master.id = checkin.venueid
		WHERE contentid in ({$cidstr})  ";
		
		$qData = $this->apps->fetch($sql,1);
		if(!$qData) return false;
		$dataceckin = false;
		foreach($qData as $val){
			$dataceckin[$val['contentid']] = $val;		
		}
		
		return $dataceckin;
		
		
	}
	
	
	function getGalleryTypeContent($start=null,$limit=12) {
		global $CONFIG;
		
		$result['result'] = false;
		$result['total'] = 0;
		
		if($start==null)$start = intval($this->apps->_request('start'));
		$uid = intval($this->apps->_request('uid'));
		if($uid==0)	{
			$uidType = $this->uid;
			$uid = $this->uid;
		}
		
		
		$limit = intval($limit);
		
		
		//RUN FILTER ENGINE, KEYWORDSEARCH , CONTENTSEARCH 
		$filter = $this->apps->searchHelper->filterEngine($limit,false,false);
				
		$search = "";
		$startdate = "";
		$enddate = "";
		$authorid = "";		
	
		if ($this->apps->_p('search')) {
			if ($this->apps->_p('search')!="Search...") {
				$search = rtrim($this->apps->_p('search'));
				$search = ltrim($search);
				
				if(strpos($search,' ')) $parseSearch = explode(' ', $search);
				else $parseSearch = false;
				
				if(is_array($parseSearch)) $search = $search.'|'.trim(implode('|',$parseSearch));
				else  $search = trim($search);
				
				$search = " AND (anc.title REGEXP  '{$search}' OR anc.brief REGEXP  '{$search}' OR anc.content REGEXP  '{$search}') ";
			}
		}
		if ($this->apps->_p('startdate')) {
			$start_date = $this->apps->_p('startdate');
			$startdate = " AND DATE(anc.posted_date) >= DATE('{$start_date}') ";
		}
		if ($this->apps->_p('enddate')) {
			$end_date = $this->apps->_p('enddate');
			$enddate = " AND DATE(anc.posted_date) <= DATE('{$end_date}') ";
		}
		$authorid = " AND anc.authorid = {$uid}";
		
		$qStatus = "  AND anc.n_status = 1 ";
		$qHirarki ="";
		if($filter['uidsearch']!='') $qHirarki = " {$filter['uidsearch']} ";
		else {
			$leadertype = intval($this->apps->user->leaderdetail->type);
			if($leadertype){
				
					
							$auhtorarrid[$uid] = $uid;
							$auhtorminion = @$this->apps->user->branddetail;
							if($auhtorminion){
								foreach($auhtorminion as $val){
										$auhtorarrid[$val->ownerid] = $val->ownerid;
								}
							}
							
							$auhtorminion = @$this->apps->user->areadetail;
							if($auhtorminion){
								foreach($auhtorminion as $val){
										$auhtorarrid[$val->ownerid] = $val->ownerid;
								}
							}		
							
							$auhtorminion = @$this->apps->user->pldetail;
							if($auhtorminion){
								foreach($auhtorminion as $val){
										$auhtorarrid[$val->ownerid] = $val->ownerid;
								}
							}	
							
							$auhtorminion = @$this->apps->user->badetail;
							if($auhtorminion){
								foreach($auhtorminion as $val){
										$auhtorarrid[$val->ownerid] = $val->ownerid;
								}
							}	
							
							if(is_array($auhtorarrid)) 	{
								// pr($minionarr);
								$authorids = implode(',',$auhtorarrid);
							}else $authorids = $uid;
							
							$qHirarki = " AND ( authorid IN ({$authorids}) OR EXISTS ( SELECT contentid FROM {$this->dbshema}_news_content_tags WHERE friendid IN ({$authorids}) AND friendtype=1  AND contentid=anc.id) ) ";
						
					
			}
		}
		
		$qStatus = "  AND anc.n_status IN (1) ";				
		$qArticleType = " anc.image <> '' AND anc.image is not null  ";
		
		$brands = strip_tags($this->apps->_request('category'));
		$qCategories = "";
		if($brands=='cocreation') $qCategories = " AND pages.type IN ({$this->cocreationtypeid}) ";
		if($brands=='brands') $qCategories = " AND pages.type IN ({$this->brandtypeid}) ";
		if($brands=='timeline') $qCategories = " AND pages.type NOT IN ( {$this->cocreationtypeid},{$this->brandtypeid} ) ";
		// if($brands=='baengagement') $qCategories = " AND pages.type IN ( {$this->cocreationtypeid},{$this->brandtypeid} ) AND EXISTS ( SELECT contentid FROM my_checkin checkin WHERE checkin.contentid=anc.id AND checkin.userid IN ({$authorids}) AND checkin.n_status=1 ) ";
		if($brands=='baengagement') {
			$qCategories = " AND anc.articleType = 5 AND EXISTS ( SELECT contentid FROM my_checkin checkin WHERE checkin.contentid=anc.id AND checkin.userid
			IN ({$authorids}) AND checkin.n_status=1 ) ";
			$qArticleType = " 1 ";
			$qHirarki = " ";
		}
		
		//GET TOTAL ARTICLE

		$sql = "SELECT count(*) total 
		FROM {$this->dbshema}_news_content anc
		LEFT JOIN my_pages pages ON anc.authorid=pages.ownerid 
		WHERE  {$qArticleType} {$search} {$startdate} {$enddate} {$filter['keywordsearch']} {$filter['categorysearch']} {$filter['citysearch']} {$filter['postedsearch']} {$filter['fromwhosearch']} {$qStatus} {$qHirarki} {$qCategories}
		{$filter['groupbyfilter']}		";
		$total = $this->apps->fetch($sql);
		
		if(intval($total['total'])<=$limit) $start = 0;
		
		$sql = "
			SELECT anc.id,anc.title,anc.brief,anc.image,anc.thumbnail_image,anc.slider_image,anc.posted_date,anc.expired_date,anc.file,anc.url,anc.fromwho,anc.tags,anc.authorid,anc.topcontent,anc.cityid ,anc.articleType,anc.can_save,anc.n_status,pages.type pagetypes
			FROM {$this->dbshema}_news_content anc
			LEFT JOIN my_pages pages ON anc.authorid=pages.ownerid 
			{$filter['subqueryfilter']} 
			WHERE {$qArticleType} {$search} {$startdate} {$enddate} {$filter['keywordsearch']} {$filter['categorysearch']} {$filter['citysearch']} {$filter['postedsearch']} {$filter['fromwhosearch']} {$qStatus} {$qHirarki} {$qCategories}
			{$filter['groupbyfilter']} ORDER BY {$filter['suborderfilter']}  anc.posted_date DESC , anc.id DESC 
			LIMIT {$start},{$limit}";
		// pr($sql);
		
		$rqData = $this->apps->fetch($sql,1);
		$this->logger->log($sql);
		if($rqData) {
			//CEK DETAIL IMAGE FROM FOLDER
			//IF IS ARTICLE, IMAGE BANNER DO NOT SHOWN
			foreach($rqData as $key => $val){
				$rqData[$key]['imagepath'] = false;
				if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}event/{$val['image']}")) 	$rqData[$key]['imagepath'] = "event";
				if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}banner/{$val['image']}")) 	$rqData[$key]['imagepath'] = "banner";
				if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}article/{$val['image']}"))  	$rqData[$key]['imagepath'] = "article";					
				
				if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}article/{$val['image']}")) 	$rqData[$key]['banner'] = false;
				else $rqData[$key]['banner'] = true;
			
				//CHECK FILE SMALL
				if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}{$rqData[$key]['imagepath']}/small_{$val['image']}")) $rqData[$key]['image'] = "small_{$val['image']}";
				
				//PARSEURL FOR VIDEO THUMB
				$video_thumbnail = false;
				if($val['url']!='')	{
					//PARSER URL AND GET PARAM DATA
					$parseUrl = parse_url($val['url']);
					if(array_key_exists('query',$parseUrl)) parse_str($parseUrl['query'],$parseQuery);
					else $parseQuery = false;
					if($parseQuery) {
						if(array_key_exists('v',$parseQuery))$video_thumbnail = $parseQuery['v'];
					} 
					$rqData[$key]['video_thumbnail'] = $video_thumbnail;
				}else $rqData[$key]['video_thumbnail'] = false;
				
				if($rqData[$key]['imagepath']) $rqData[$key]['image_full_path'] = $CONFIG['BASE_DOMAIN_PATH']."public_assets/".$rqData[$key]['imagepath']."/".$rqData[$key]['image'];
				else $rqData[$key]['image_full_path'] = $CONFIG['BASE_DOMAIN_PATH']."public_assets/article/default.jpg";
				
				
			}
			
			if($rqData) $qData=	$this->getStatistictArticle($rqData);
			else $qData = false;
		}else $qData = false;		
	
		$result['result'] = $qData;
		$result['total'] = intval($total['total']);
		$totals = intval($total['total']);

		
		if($totals>$start) $nextstart = $start;
		else $nextstart = 0;
		
				
		if($start<=0)$countstart = $limit;
		else $countstart = $limit+$nextstart;
		
		$result['pages']['nextpage'] = $limit+$nextstart;
		$result['pages']['prevpage'] = $countstart-$limit;
		
		return $result;
	}
	
}
?>

