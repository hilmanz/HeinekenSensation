<?php 

class userHelper {

	function __construct($apps){
		global $logger;
		$this->logger = $logger;
		$this->apps = $apps;
		if(is_object($this->apps->user)) {
				$uid = intval($this->apps->_request('uid'));
				if($uid==0) $this->uid = intval($this->apps->user->id);
				else $this->uid = $uid;
		}

		$this->dbshema = "beat";	
		$this->topclass = array(100,4,6);
	}

	function getUserProfileEdit(){
		global $CONFIG;
		$uid = intval($this->apps->_request('uid'));
		if(!$uid) $uid = intval($this->uid);
		
		if($uid!=0 || $uid!=null) {
		$sql = "
			SELECT sm.id,sm.name,sm.last_name,sm.img,sm.sex,sm.username,sm.nickname,sm.register_date,sm.StreetName,sm.phone_number,sm.email,sm.last_login,sm.n_status,sm.sex,sm.birthday,cityref.city as cityname FROM social_member sm
			LEFT JOIN {$this->dbshema}_city_reference cityref ON sm.city = cityref.id
			WHERE sm.id = {$uid} LIMIT 1";
		$qData = $this->apps->fetch($sql);
		
		
		
			
	}
		// pr($qData);
		return $qData;
	}
	
	function updateUserImageProfile($filename=null)
	{
		$sql = "
				UPDATE social_member 
				SET img='{$filename}'
				WHERE id={$this->apps->user->id} LIMIT 1
				";
				// pr($influencer);exit;
				$qData = $this->apps->query($sql);
		if ($qData) return true;
		
		return false;
		
	}
	
			
	
	
	
	
	function getUserProfile(){
		global $CONFIG;
	
		$uid = intval($this->apps->_request('uid'));
		if(!$uid) $uid = intval($this->uid);
		if($uid!=0 || $uid!=null) {
			$sql = "
			SELECT sm.id,sm.name,sm.last_name,sm.img,sm.sex,sm.username,sm.nickname,sm.register_date,sm.StreetName,sm.phone_number,sm.email,sm.last_login,sm.n_status,sm.sex,sm.birthday,cityref.city as cityname,sm.small_img ,sm.description 
			FROM social_member sm
			LEFT JOIN {$this->dbshema}_city_reference cityref ON sm.city = cityref.id
			WHERE sm.id = {$uid} LIMIT 1";
			// pr($sql);
			$this->logger->log($sql);
			$qData = $this->apps->fetch($sql);
			if(!$qData)return false;
			$qData['rank'] = " you rank not specified yet ";
			// $sql ="
			// SELECT mrank.*
			// FROM my_rank mrank
			// LEFT JOIN {$this->dbshema}_rank_table ranktable ON ranktable.id = mrank.rank
			// WHERE userid = {$uid} 
			// AND n_status = 1 LIMIT 1		
			// ";
			$sql = "
				SELECT count(*) total
				FROM `my_entourage` 
				WHERE referrerbybrand={$uid} AND n_status = 1
				";
				
			$entourages = 1;
			$myentourage = $this->apps->fetch($sql);
			if($myentourage){
				$entourages = intval($myentourage['total']);
				
			}
			$sql = "
				SELECT count(*) totalentourage, sm.id,sm.name, sm.last_name, sm.img
				FROM `my_entourage` entourage
				LEFT JOIN social_member sm ON sm.id=entourage.referrerbybrand 
				LEFT JOIN my_pages pages ON sm.id=pages.ownerid
				WHERE pages.type = 1  AND entourage.n_status = 1
				GROUP BY `referrerbybrand` ORDER BY totalentourage DESC 
			";
			$tuser = 0;
			$alluser = $this->apps->fetch($sql,1);
			if($alluser){
				$no = 1;
				foreach($alluser as $val){
					if($val['id']==$uid) $tuser = intval($no);
					$no++;
					
				}
			}
			// $qRankData = $this->apps->fetch($sql);	
			// pr($sql);
			if($tuser!=0){
						// $qData['rank'] = "{$qRankData['rank']}";
						if($entourages==0)$entourages =1;
						// $rankz = round($tuser / $entourages);
						$rankz = $tuser;
						$qData['rank'] = "{$rankz}";
			}else{
						$qData['rank'] = " you rank not specified yet ";
			}	
			
			$friendsData = $this->isFriends($qData['id'],true);
			$arrFriends = false;
			if($friendsData){
				foreach($friendsData as $val){
					$arrFriends[$val['friendid']] = $val['friendid'];
				}
			}
		
			$qData['isFriends'] =false;
			if($arrFriends) {
				if(array_key_exists($qData['id'],$arrFriends))$qData['isFriends'] = true;
			}
				
			
			if(!is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}user/photo/{$qData['img']}")) $qData['img'] = false;
			if(!is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}user/cover/{$qData['small_img']}")) $qData['small_img'] = false;
			// if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}user/photo/crop{$qData['img']}")) $qData['img'] = "crop{$qData['img']}";
			if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}user/photo/original_{$qData['img']}")) $qData['imgoriginal']= "original_{$qData['img']}";
			else $qData['imgoriginal'] = false;
			
			// $qData['image_full_path'] = $CONFIG['BASE_DOMAIN_PATH']."public_assets/user/photo/";
			
			if($qData['img']) $qData['image_full_path'] = $CONFIG['BASE_DOMAIN_PATH']."public_assets/user/photo/".$qData['img'];
			else $qData['image_full_path'] = $CONFIG['BASE_DOMAIN_PATH']."public_assets/user/photo/default.jpg";
			
			if($qData['small_img']) $qData['cover_full_path'] = $CONFIG['BASE_DOMAIN_PATH']."public_assets/user/cover/".$qData['small_img'];
			else $qData['cover_full_path'] = $CONFIG['BASE_DOMAIN_PATH']."public_assets/user/cover/default.jpg";
			
			$plan = $this->apps->contentHelper->getArticleContent(null,10,4,array(0,3),"plan",false,false,false,true,true,true,false);
			$baengagement = $this->getbaengagement(5);
			
			$data['notification'] = $this->apps->activityHelper->getA360activity(0,2,false,false,false,'3',false);	
			$data['plan']['total'] = $plan['total'];
			$data['plan']['lists'] = $plan['result'];
			if($baengagement) $data['baengagement'] = $baengagement;
			else $data['baengagement'] = 0;
			$data['challenge'] = false;
			$data['entourage'] = $this->apps->entourageHelper->getEntourage();
			$data['inbox'] = $this->apps->messageHelper->getMessage(0,2);
		
			if($data) $qData = array_merge($qData,$data);
			// pr($qData);
			return $qData;
		}
		return false;
	}
	
	function getTotalEngagement() {
	global $CONFIG;
	
		$uid = intval($this->apps->_request('uid'));
		if(!$uid) $uid = intval($this->uid);
		
		$sql = "SELECT COUNT(*) FROM my_rank WHERE userid ={$uid} LIMIT 1";
			// pr($sql);
			$this->logger->log($sql);
			$qData = $this->apps->fetch($sql);
	}
	
	function getUserAttribute(){		
		$sql = "
		SELECT sum(ancr.point) rank,categoryid ,category
		FROM axis_news_content_rank ancr
		LEFT JOIN axis_news_content_category ancc ON ancc.id= ancr.categoryid
		WHERE userid={$this->uid} 
		GROUP BY categoryid ORDER BY rank DESC LIMIT 5 ";
		$this->logger->log($sql);
		$qData = $this->apps->fetch($sql,1);
	
		if($qData){
			$mostLike = null;
			foreach($qData as $val){
				$mostLike[] = $val['category'];		
			}
			$userLikeCategory = implode(' , ',$mostLike);
		}
		$sql = "
			SELECT art.rank titleRank,art.id levelRank FROM my_rank sr
			LEFT JOIN social_media_account sma ON sma.userid=sr.userid
			LEFT JOIN axis_rank_table art ON art.id=sr.rank
			WHERE sr.userid = {$this->uid} AND sr.n_status = 1 limit 1		
		";
		$this->logger->log($sql);
		$qData = $this->apps->fetch($sql);
		if(isset($userLikeCategory)) $qData['userlike'] = $userLikeCategory;
		if($qData)	return $qData;
		else return false;
	
	}
	
	function getRankUser(){
		$sql ="
			SELECT * 
			FROM my_rank 
			WHERE userid = {$this->uid} 
			AND n_status = 1 LIMIT 1		
			";
		$this->logger->log($sql);
		$qData = $this->apps->fetch($sql);	
	
		if($qData){
			$lastPoint = $qData['point'];
			$lastDate  = $qData['date'];
	
			$qData = null;
			//cek new point // > tanggal
			$sql ="
				SELECT SUM(score) total 
				FROM tbl_exp_point 
				WHERE user_id = {$this->uid} AND date_time > '{$lastDate}'
				";
			$this->logger->log($sql);
			$qData = $this->apps->fetch($sql);	
			$point = $qData['total'];
			$qData = null;
					
			//klo ada point baru, setelah penginsert-an point sebelum nya , tambah point nya
			if($point==0)	return false;
				
			$newPoint = $lastPoint+$point;
					
			$sql = "
				SELECT id FROM {$this->dbshema}_rank_table 
				WHERE minPoint <= {$newPoint} AND maxPoint > {$newPoint} LIMIT 1";
			$this->logger->log($sql);
			$qData = $this->apps->fetch($sql);	
			$rank = $qData['id'];
			$qData = null;
			
			if($rank){
				$sql="INSERT INTO my_rank (userid,date,date_ts,rank,point,n_status) VALUES ({$this->uid},NOW(),".time().",{$rank},{$newPoint},1) ";
				$this->logger->log($sql);
				$qData = $this->apps->query($sql);
				$lastID = $this->apps->getLastInsertId();
				$qData = null;
				if($lastID!=0 || $lastID!=null){
				
					$sql="UPDATE my_rank SET n_status = 0 WHERE userid={$this->uid} AND id <> {$lastID}  ";
					$this->logger->log($sql);
					$qData = $this->apps->query($sql);
					$qData = null;
				}else {
					//cek data if n_status 1 have duplicate value
					$sql = "
						SELECT count(*) total, id FROM my_rank 
						WHERE n_status = 1 AND userid={$this->uid} ORDER BY id DESC LIMIT 2";
						$this->logger->log($sql);
					$qData = $this->apps->fetch($sql);	
					
					if($qData['total']>=2){
						$qData = null;
						$sql = "
						SELECT id FROM my_rank 
						WHERE n_status = 1 AND userid={$this->uid} ORDER BY id DESC LIMIT 1";
						$this->logger->log($sql);
						$qData = $this->apps->fetch($sql);	
						$usingIDRank = intval($qData['id']);
						$qData = null;
						if($usingIDRank!=0){
							$sql="UPDATE my_rank SET n_status = 0 WHERE id <> {$usingIDRank} AND userid={$this->uid} ";
							$this->logger->log($sql);
							$qData = $this->apps->query($sql);
							$qData = null;
						} 
					}else return true;
				
				
				}
			}
			return false;
			
		}else{
			
			//cek klo uda ada activity brarti rollback rank nya
			$sql ="
					SELECT count(*) total 
					FROM tbl_exp_point 
					WHERE user_id = {$this->uid} 
					LIMIT 1	
					";
				$this->logger->log($sql);
			$qData = $this->apps->fetch($sql);	
			
			if($qData['total']<=0){
				//klo ga ada. insert ke social rank newbie
				$sql="INSERT INTO my_rank (userid,date,date_ts,rank,point,n_status) VALUES ({$this->uid},NOW(),".time().",1,0,1) ";
				$this->logger->log($sql);
				$qData = $this->apps->query($sql);	
			}else{
				$qData = null;
				$sql ="
					SELECT SUM(score) total 
					FROM tbl_exp_point 
					WHERE user_id = {$this->uid} 
					";
					$this->logger->log($sql);
				$qData = $this->apps->fetch($sql);	
				$point = intval($qData['total']);
				$qData = null;
			
				$sql = "
					SELECT id FROM {$this->dbshema}_rank_table
					WHERE minPoint <= {$point} AND maxPoint >= {$point} LIMIT 1";
					$this->logger->log($sql);
				$qData = $this->apps->fetch($sql);	
				$rank = $qData['id'];
					
				if($rank!=0|| $rank!=null){
					$sql="INSERT INTO my_rank (userid,date,date_ts,rank,point,n_status) VALUES ({$this->uid},NOW(),".time().",{$rank},{$point},1) ";
					$this->logger->log($sql);
					$qData = $this->apps->query($sql);		
					return true;					
				}
			}
		return false;
		}
		
	
	}
	
	
	function getPreferenceThemeUser(){
		$sql =" SELECT * FROM social_preference_page WHERE userid={$this->uid} AND n_status=1 LIMIT 1";
		$this->logger->log($sql);
		$qData = $this->apps->fetch($sql);
		// print_r( unserialize($qData['apperances']));exit;
		if($qData) return unserialize($qData['apperances']);
		else return false;
	}
	
	function savePreferenceThemeUser(){
		$data = $this->getPreferenceThemeUser();
		if($this->apps->Request->getPost('bodyColor')) $data['body']['color'] = $this->apps->Request->getPost('bodyColor');
		// if($this->apps->Request->getPost('bodyImage')) $data['body']['image'] = $this->apps->Request->getPost('bodyImage');
		// $data['content']['color'] = $this->apps->Request->getPost('contentColor');
		// $data['border']['color'] = $this->apps->Request->getPost('borderColor');
		// $data['header']['font']['family'] = $this->apps->Request->getPost('headerFontFamily');
		// $data['header']['font']['size'] = $this->apps->Request->getPost('headerFontSize');
		// $data['header']['font']['color'] = $this->apps->Request->getPost('headerFontColor');
		if( $this->apps->Request->getPost('contentFontFamily')) $data['content']['font']['family'] = $this->apps->Request->getPost('contentFontFamily');
		if( $this->apps->Request->getPost('contentFontSize')) $data['content']['font']['size'] = $this->apps->Request->getPost('contentFontSize');
		if( $this->apps->Request->getPost('contentFontColor')) $data['content']['font']['color'] = $this->apps->Request->getPost('contentFontColor');
				
		$dataPreference = serialize($data);
		
		$sql="INSERT INTO 
		social_preference_page (userid,apperances,date,n_status) VALUES ({$this->uid},'{$dataPreference}',NOW(),1) 
		ON DUPLICATE KEY UPDATE
		apperances = VALUES(apperances)
		";
		$this->logger->log($sql);
		$qData = $this->apps->query($sql);	
		
		
	}
	
	
	function updateUserProfile(){
	
		$loginHelper = $this->apps->useHelper('loginHelper');
		
		$this->logger->log('can update profile');
		//cek token valid

		$tokenize = strip_tags($this->apps->_p('tokenize'));
		$accepttoken = cektokenize($tokenize,$this->uid);		
		if(!$accepttoken) return false;
		
		//get user
		$sql = "SELECT * FROM social_member WHERE n_status=1 AND id={$this->uid} LIMIT 1";
		$this->logger->log($sql);
		$rs = $this->apps->fetch($sql);
		if(!$rs)return false;
		$rs = null;
		$name = strip_tags($this->apps->_p('name'));
		$influencer = strip_tags($this->apps->_p('influencer'));
		$StreetName = strip_tags($this->apps->_p('StreetName'));
		$sex = strip_tags($this->apps->_p('sex'));
		$birthday = strip_tags($this->apps->_p('birthday'));
		$description = strip_tags($this->apps->_p('description'));
		if($name!='') $arrQuery[] = " name='{$name}' ";
		if($influencer!='') $arrQuery[] = " influencer='{$influencer}' ";
		if($StreetName!='') $arrQuery[] = " StreetName='{$StreetName}' ";
		if($sex!='') $arrQuery[] = " sex='{$sex}' ";
		if($birthday!='') $arrQuery[] = " birthday='{$birthday}' ";
		if($description!='') $arrQuery[] = " description='{$description}' ";

			$strQuery = implode(',',$arrQuery);
			if(!$strQuery) return false;
			$this->logger->log($strQuery);
			
			$sql = "
			UPDATE social_member 
			SET {$strQuery} 
			WHERE id={$this->uid} LIMIT 1
			";
			// pr($influencer);exit;
			$this->logger->log($sql);

			$qData = $this->apps->query($sql);
			if($qData) {
					$sql = "
					SELECT *
					FROM social_member 
					WHERE 
					n_status=1 AND 
					id={$this->uid}
					LIMIT 1";
				$this->logger->log($sql);
				$rs = $this->apps->fetch($sql);
				if($rs) $loginHelper->setdatasessionuser($rs); 
				else return false;
				return true;
			}else return false;
		
			
	
			
		}	
	
	function saveImage($widget){
		global $CONFIG,$LOCALE;
		$filename="";
		if($_FILES['myImage']['error']==0)	{
			if ($_FILES['myImage']['size'] <= 2560000) {
				$path = $widget=='photo_profile' ? $CONFIG['LOCAL_PUBLIC_ASSET']."user/photo/" : $CONFIG['LOCAL_PUBLIC_ASSET']."user/cover/";	
				$dataImage  = $this->apps->uploadHelper->uploadThisImage(@$_FILES['myImage'],$path,220,true);
				if($dataImage['result']){
					if ($widget=='photo_profile') {
						/* kata angga ga perlu otomatis ke update */
						/* 	$sql = "UPDATE social_member SET  img = '{$dataImage['arrImage']['filename']}' WHERE id={$this->uid} LIMIT 1";
							$this->logger->log($sql);
							
							$qData = $this->apps->query($sql);
							if($qData)	$filename = @$dataImage['arrImage']['filename'];
						*/
						$filename = @$dataImage['arrImage']['filename'];
					} elseif ($widget=='photo_cover') {
						$sql_cover = "INSERT INTO my_wallpaper (myid,image,type,datetime,n_status) 
							values ('{$this->uid}','{$dataImage['arrImage']['filename']}',0,NOW(),1)
						";
						$arrData = $this->apps->query($sql_cover);
						if($arrData) $filename = @$dataImage['arrImage']['filename'];
					}
				}
			} else {
				return false;
			}
		}
		return $filename;
	}
	
	function saveImageCover($data=false){
		global $CONFIG;
		$this->logger->log(json_encode($data));
		if(!$data) return false;
				$description = strip_tags($this->apps->_p('description'));
				$sql = "
				UPDATE social_member 
				SET  small_img = '{$data['arrImage']['filename']}', description = '{$description}'
				WHERE id={$this->uid} LIMIT 1
				";
				$this->logger->log($sql);
				
				$qData = $this->apps->query($sql);
				
		return true;
	}
	
	
	
	function saveCropImage(){
				global $CONFIG;
				
				$loginHelper = $this->apps->useHelper('loginHelper');
				
				$files['source_file'] = $this->apps->_p("imageFilename");
				$files['url'] = "{$CONFIG['LOCAL_PUBLIC_ASSET']}user/photo/";
				$arrFilename = explode('.',$files['source_file']);
				if($files==null) return false;
				$targ_w = $this->apps->_p('w');
				$targ_h =$this->apps->_p('h');
				$jpeg_quality = 90;
				
				if($files['source_file']=='') return false;
				
				//check is img have original char
						
				$arrOriginal = explode("_",$files['source_file']);
				if(is_array($arrOriginal)){
					if($arrOriginal[0]=='original') {						
						$files['source_file'] = $arrOriginal[1];
						unlink($files['url'].$files['source_file']);
						copy($files['url']."original_".$files['source_file'],$files['url'].$files['source_file']);
					}
					
				}				
			
				$src = 	$files['url'].$files['source_file'];
				copy($src, $files['url']."original_".$files['source_file']);
			
				try{
					
					$img_r = false;
					if($arrFilename[1]=='jpg' || $arrFilename[1]=='jpeg' ) $img_r = imagecreatefromjpeg($src);
					if($arrFilename[1]=='png' ) $img_r = imagecreatefrompng($src);
					if($arrFilename[1]=='gif' ) $img_r = imagecreatefromgif($src);
					if(!$img_r) return false;
					$dst_r = ImageCreateTrueColor( $targ_w, $targ_h );

					imagecopyresampled($dst_r,$img_r,0,0,$this->apps->_p('x'),$this->apps->_p('y'),	$targ_w,$targ_h,$this->apps->_p('w'),$this->apps->_p('h'));

					// header('Content-type: image/jpeg');
					if($arrFilename[1]=='jpg' || $arrFilename[1]=='jpeg' ) imagejpeg($dst_r,$files['url'].$files['source_file'],$jpeg_quality);
					if($arrFilename[1]=='png' ) imagepng($dst_r,$files['url'].$files['source_file']);
					if($arrFilename[1]=='gif' ) imagegif($dst_r,$files['url'].$files['source_file']);
					
				}catch (Exception $e){
					return false;
				}
				include_once '../engines/Utility/phpthumb/ThumbLib.inc.php';
					
				try{
					$thumb = PhpThumbFactory::create($files['url'].$files['source_file']);
				}catch (Exception $e){
					// handle error here however you'd like
				}
				list($width, $height, $type, $attr) = getimagesize($files['url'].$files['source_file']);
				$maxSize = 400;
				if($width>=$maxSize){
					if($width>=$height) {
						$subs = $width - $maxSize;
						$percentageSubs = $subs/$width;
					}
				}
				if($height>=$maxSize) {
					if($height>=$width) {
						$subs = $height - $maxSize;
						$percentageSubs = $subs/$height;
					}
				}
				if(isset($percentageSubs)) {
				 $width = $width - ($width * $percentageSubs);
				 $height =  $height - ($height * $percentageSubs);
				}
				
				$w_small = $width - ($width * 0.5);
				$h_small = $height - ($height * 0.5);
				$w_tiny = $width - ($width * 0.7);
				$h_tiny = $height - ($height * 0.7);
				
				//resize the image
				$thumb->adaptiveResize($width,$height);
				$big = $thumb->save(  "{$files['url']}".$files['source_file']);
				$thumb->adaptiveResize($width,$height);
				$prev = $thumb->save(  "{$files['url']}prev_".$files['source_file']);
				$thumb->adaptiveResize($w_small,$h_small);
				$small = $thumb->save( "{$files['url']}small_".$files['source_file'] );
				$thumb->adaptiveResize($w_tiny,$h_tiny);
				$tiny = $thumb->save( "{$files['url']}tiny_".$files['source_file']);
								
				if(is_file($files['url'].$files['source_file'])){
					//saveit
					$sql = "
					UPDATE social_member 
					SET  img = '{$files['source_file']}'
					WHERE id={$this->uid} LIMIT 1
					";
					$this->logger->log($sql);
					
					$qData = $this->apps->query($sql);
					if($qData){
							$sql = "
							SELECT *
							FROM social_member 
							WHERE 
							n_status=1 AND id={$this->uid} LIMIT 1 ";
						$rs = $this->apps->fetch($sql);	
						if(!$rs)return false;
						$rs['img'] = $files['source_file'];
						//how to update the session on on fly
						if($rs) $loginHelper->setdatasessionuser($rs); 
						else return false;
						return $files['source_file'];
					}else return false;
					
				}else return false;
				
	}
	
	function saveCropCoverImage(){
		global $CONFIG;
		
		$loginHelper = $this->apps->useHelper('loginHelper');
		
		$files['source_file'] = $this->apps->_p("imageFilename");
		$files['url'] = "{$CONFIG['LOCAL_PUBLIC_ASSET']}user/cover/";
		$arrFilename = explode('.',$files['source_file']);
		if($files==null) return false;
		$targ_w = $this->apps->_p('w');
		$targ_h =$this->apps->_p('h');
		$jpeg_quality = 90;
		
		if($files['source_file']=='') return false;		
		
		//check is img have original char						
		$arrOriginal = explode("_",$files['source_file']);
		if(is_array($arrOriginal)){
			if($arrOriginal[0]=='original') {						
				$files['source_file'] = $arrOriginal[1];
				unlink($files['url'].$files['source_file']);
				copy($files['url']."original_".$files['source_file'],$files['url'].$files['source_file']);
			}
			
		}				
	
		$src = 	$files['url'].$files['source_file'];
		copy($src, $files['url']."original_".$files['source_file']);
		
		try{
			$img_r = false;
			$arrFilename[1] = strtolower($arrFilename[1]);
			if($arrFilename[1]=='jpg' || $arrFilename[1]=='jpeg' ) $img_r = imagecreatefromjpeg($src);
			if($arrFilename[1]=='png' ) $img_r = imagecreatefrompng($src);
			if($arrFilename[1]=='gif' ) $img_r = imagecreatefromgif($src);
			if(!$img_r) return false;
			$dst_r = ImageCreateTrueColor( $targ_w, $targ_h );

			imagecopyresampled($dst_r,$img_r,0,0,$this->apps->_p('x'),$this->apps->_p('y'),	$targ_w,$targ_h,$this->apps->_p('w'),$this->apps->_p('h'));

			// header('Content-type: image/jpeg');
			if($arrFilename[1]=='jpg' || $arrFilename[1]=='jpeg' ) imagejpeg($dst_r,$files['url'].$files['source_file'],$jpeg_quality);
			if($arrFilename[1]=='png' ) imagepng($dst_r,$files['url'].$files['source_file']);
			if($arrFilename[1]=='gif' ) imagegif($dst_r,$files['url'].$files['source_file']);
			
		}catch (Exception $e){
	
			return false;
		}
		include_once '../engines/Utility/phpthumb/ThumbLib.inc.php';
			
		try{
			$thumb = PhpThumbFactory::create($files['url'].$files['source_file']);
		}catch (Exception $e){
			// handle error here however you'd like
		}
		list($width, $height, $type, $attr) = getimagesize($files['url'].$files['source_file']);
		$maxSize = 400;
		if($width>=$maxSize){
			if($width>=$height) {
				$subs = $width - $maxSize;
				$percentageSubs = $subs/$width;
			}
		}
		if($height>=$maxSize) {
			if($height>=$width) {
				$subs = $height - $maxSize;
				$percentageSubs = $subs/$height;
			}
		}
		if(isset($percentageSubs)) {
		 $width = $width - ($width * $percentageSubs);
		 $height =  $height - ($height * $percentageSubs);
		}
		
		$w_small = $width - ($width * 0.5);
		$h_small = $height - ($height * 0.5);
		$w_tiny = $width - ($width * 0.7);
		$h_tiny = $height - ($height * 0.7);
		
		//resize the image
		$thumb->adaptiveResize($width,$height);
		$big = $thumb->save(  "{$files['url']}".$files['source_file']);
		$thumb->adaptiveResize($width,$height);
		$prev = $thumb->save(  "{$files['url']}prev_".$files['source_file']);
		$thumb->adaptiveResize($w_small,$h_small);
		$small = $thumb->save( "{$files['url']}small_".$files['source_file'] );
		$thumb->adaptiveResize($w_tiny,$h_tiny);
		$tiny = $thumb->save( "{$files['url']}tiny_".$files['source_file']);
						
		if(is_file($files['url'].$files['source_file'])){
			$sql = "UPDATE my_wallpaper SET image = '{$files['source_file']}' WHERE myid={$this->uid} AND type=0 ORDER BY datetime DESC LIMIT 1";
			$this->logger->log($sql);			
			$qData = $this->apps->query($sql);
			if($qData){
				return $files['source_file'];
			} else return false;			
		} else return false;				
	}
	
	function isFriends($fid=null,$all=false){
		$fid = strip_tags($fid);
		if($fid=='') return false;
		if($this->uid==0) return false;
			
		$sql = "SELECT * FROM my_circle WHERE userid= {$this->apps->user->id} AND friendid IN ({$fid}) AND n_status<>0 ";

		$qData = $this->apps->fetch($sql,1);
		if(!$qData) return false;
		$data['total'] = count($qData);
		$data['result'] = $qData;
		
		if($data['total']>0) {
			if(!$all)return true;
			else return $data['result'];
		}else return false;
		
	}
	
	function getGroupUser(){
			$uid = strip_tags($this->apps->_request('uid'));
			if(!$uid) $uid = intval($this->uid);
			if($uid!=0 || $uid!=null) {
				$sql = "SELECT * FROM my_circle_group WHERE userid IN ({$uid}) AND n_status = 1 ORDER BY datetime DESC ";
				
				$qData = $this->apps->fetch($sql,1);
				if($qData) {
					foreach($qData as $val){
						$groupCircle[$val['id']] = $val['name'];
					}	
					if($groupCircle)	return $groupCircle;
					else return false;
				}
				
				
			}
			return false;
	}
	function getFriends($all=true,$limit=8,$start=0,$useGroup=true){
	//global user id, for list of friend of friend : 21,23,1,5,3
		$uid = strip_tags($this->apps->_request('uid'));
		$start = intval($this->apps->_p('start'));
		$group = intval($this->apps->_request('groupid'));
		$circle = false;
		if(!$uid) $uid = intval($this->uid);
		
		if($uid!=0 || $uid!=null) {
		
				
			//get circle group
			if($useGroup){
				$groupdata = $this->getGroupUser($uid);
				$arrGroupId = array();
				if($groupdata) {
					
					foreach($groupdata as $key => $val){			
						$arrGroupId[] = $key;										
					}
						
				}
				
				
				if($group!=0){			
					$strGroupid = $group;
				}else {	
					array_push($arrGroupId,0);
					$strGroupid = implode(',',$arrGroupId);
				}
			}else $strGroupid=0;
			
			$sql =" SELECT name FROM my_pages WHERE ownerid ={$this->uid} LIMIT 1";
			$pagesnames = $this->apps->fetch($sql);
			if($pagesnames)$groupname = $pagesnames['name'];
			else $groupname = $this->apps->user->leaderdetail->name;
			
			$groupname = rtrim($groupname);
			$groupname = ltrim($groupname);
			if(strpos($groupname,' ')) $parseKeywords = explode(' ', $groupname);
			else $parseKeywords = false;
			
			if(is_array($parseKeywords)) {
				$groupname = strtoupper("AREA {$parseKeywords[1]}|PL {$parseKeywords[1]}|SBA {$parseKeywords[1]}");
			}else  $groupname = trim($groupname);
			
			if(in_array($this->apps->user->leaderdetail->type,$this->topclass)) $groupname = " ";
			// if(is_array($parseKeywords)) $groupname = $groupname.'|'.trim(implode('|',$parseKeywords));
			// else  $groupname = trim($groupname);
			
			// get all friend of this user
			$sql =	" 
			
			SELECT count(*) total FROM 
			( 
				SELECT count(*) total FROM 
				( 
					(
					SELECT id,{$uid} userid,ownerid friendid,1 ftype,created_date date_time,n_status 
					FROM my_pages 
					WHERE name  REGEXP '{$groupname}'
					)
					UNION
					( 
					SELECT id,userid,friendid,ftype,date_time,n_status 
					FROM my_circle 
					WHERE groupid IN ({$strGroupid}) AND userid IN ({$uid}) AND  n_status IN (0,1) AND ftype=1 GROUP BY friendid,ftype  
					)
					UNION
					( 
					SELECT id,userid,friendid,ftype,date_time,n_status 
					FROM my_circle 
					WHERE groupid IN ({$strGroupid}) AND userid IN ({$uid}) AND  n_status IN (0,1) AND ftype=0 GROUP BY friendid,ftype  
					)
				) a
				GROUP BY friendid,ftype
			) b
			";
			// pr($sql);
			$friends = $this->apps->fetch($sql);
	// pr($friends);
			if(!$friends) return false;
			if($friends['total']==0) return false;
			
			if($all) $qAllQData = " LIMIT {$start},{$limit} ";
			else  $qAllQData = "";
			$circle =false;
			//get circle
			
			
			$sql =	"
			SELECT id,userid,friendid,ftype,date_time,n_status, TRIM(name) name,TRIM(last_name ) last_name
			FROM 
			( 
					(
					SELECT mp.id,{$uid} userid,mp.ownerid friendid,1 ftype,mp.created_date date_time,mp.n_status ,sm.name, sm.last_name
					FROM my_pages mp
					LEFT JOIN social_member sm ON sm.id=mp.ownerid
					WHERE mp.name REGEXP '{$groupname}'  AND  mp.ownerid <> {$uid}
					)
					UNION
					( 
					SELECT   mc.id, mc.userid, mc.friendid, mc.ftype, mc.date_time, mc.n_status ,sm.name, sm.last_name
					FROM my_circle mc
					LEFT JOIN social_member sm ON sm.id=mc.friendid
					WHERE  mc.groupid IN ({$strGroupid}) AND  mc.userid IN ({$uid}) AND  mc.friendid <> {$uid} AND  mc.n_status IN (0,1) AND  mc.ftype=1 
					GROUP BY  mc.friendid, mc.ftype 
					)
					UNION
					( 
					SELECT mc.id, mc.userid, mc.friendid, mc.ftype, mc.date_time, mc.n_status ,me.name, me.last_name
					FROM my_circle mc
					LEFT JOIN my_entourage me ON me.id=mc.friendid
					WHERE  mc.groupid IN ({$strGroupid}) AND  mc.userid IN ({$uid}) AND  mc.friendid <> {$uid} AND  mc.n_status IN (0,1) AND  mc.ftype=0 
					GROUP BY  mc.friendid, mc.ftype   
					)
			) a GROUP BY friendid,ftype  ORDER BY  name ASC , last_name ASC  {$qAllQData}
			";
		
			$qData = $this->apps->fetch($sql,1);
			// pr($sql);
			// $this->logger->log(json_encode($qData));
			if($qData) {
			$arrSocialFid = false;
			$arrEntourageFid = false;
				foreach($qData as $val){	
					/* BA */	
					if($val['ftype']==0) $arrEntourageFid[$val['friendid']] = $val['friendid'];
					/* entourage */
					if($val['ftype']==1) $arrSocialFid[$val['friendid']] = $val['friendid'];
					
					$circledata[$val['ftype']][$val['friendid']]= $val;
				}
				$socialdata = false;
				$entouragedata = false;
				if($arrSocialFid) {
					$strsocialfid = implode(',',$arrSocialFid);
					$socialfid = $this->socialdata($strsocialfid);
					if($socialfid){
						foreach($socialfid as $key => $val){
							$socialfid[$key]['ftype'] = 1;
							$socialdata[1][$val['id']]=$socialfid[$key];
						}
					}
				}
				
				if($arrEntourageFid) {
					$strentouragefid = implode(',',$arrEntourageFid);
					$this->logger->log($strentouragefid);
					$entouragefid = $this->entouragedata($strentouragefid);
					if($entouragefid){
						foreach($entouragefid as $key => $val){
								$entouragefid[$key]['ftype'] = 0;
								$entouragedata[0][$val['id']]=$entouragefid[$key];
						}
					}
				}
				

				
			
				if(!$circledata) return false;
				
				// pr($socialdata);
				// pr($entouragedata);
				// pr($circledata);
				//merge data
				foreach($circledata as $keyftype => $ftype){
					foreach($ftype as $key => $val){
					if($socialdata)if(array_key_exists($keyftype,$socialdata)) if(array_key_exists($key,$socialdata[$keyftype]))  $circle[] = $socialdata[$keyftype][$key];
					if($entouragedata) if(array_key_exists($keyftype,$entouragedata)) if(array_key_exists($key,$entouragedata[$keyftype]))  $circle[] = $entouragedata[$keyftype][$key];
					 // pr($val);
					}
					
				}
			
			
				
			
			}
			// pr($circle);
			if($circle) $data['result'] = true;
			else  $data['result'] = false;
			$data['data'] = $circle;
			
			
			// pr($data);
			return $data;
			
			
		}
		return false;
	}
	
	function getCircleUser($all=true,$limit=8,$start=0){
		//global user id, for list of friend of friend : 21,23,1,5,3
		$uid = strip_tags($this->apps->_request('uid'));
		$start = intval($this->apps->_request('start'));

		
		if(!$uid) $uid = intval($this->uid);
		if($uid!=0 || $uid!=null) {
		
				
			//get circle group
		
				$groupdata = $this->getGroupUser($uid);
				$arrGroupId = array();
				if($groupdata) {
						
					foreach($groupdata as $key => $val){			
						$arrGroupId[] = $key;										
					}
						
				}else array_push($arrGroupId,0);
		
				
				$strGroupid = implode(',',$arrGroupId);
			
			// get all friend of this user
			$sql =	" SELECT count(*) total FROM ( SELECT friendid FROM my_circle WHERE groupid IN ({$strGroupid}) AND userid IN ({$uid}) AND n_status = 1 GROUP BY friendid ) a";
		
			$friends = $this->apps->fetch($sql);
			if(!$friends) return false;
			
			//get circle
			$sql =	" SELECT * FROM my_circle WHERE groupid IN ({$strGroupid}) AND userid IN ({$uid}) AND n_status = 1 ORDER BY id DESC  ";

			$qData = $this->apps->fetch($sql,1);
			if(!$qData) return false;
			
			foreach($qData as $val){			
				$arrFriendId[ $val['friendid']] = $val['friendid'];
				$circledata[]= $val;
			}
		
			if(!$arrFriendId) return false;
			$strFriendId = implode(',',$arrFriendId);
			if($all) $qAllQData = " LIMIT {$limit} ";
			else  $qAllQData = "";
			//get friend detail
			$sql =	" SELECT id,name,img,sex,last_name FROM social_member WHERE id IN ({$strFriendId}) AND  n_status = 1 {$qAllQData} ";
			
			$qData = $this->apps->fetch($sql,1);
			if(!$qData) return false;
			foreach($qData as $val){
				$frienduser[$val['id']] = $val;
			}
			
			if(!$circledata&&!$frienduser) return false;
			
			//merge data
			foreach($circledata as $key => $val){
				if(array_key_exists($val['friendid'],$frienduser)) $circledata[$key]['frienddetail'] = $frienduser[$val['friendid']];
				else  $circledata[$key]['frienddetail'] = false;			
			}
			
			//create new array
			foreach($circledata as $key => $val){
				$circle[$val['userid']][$val['groupid']][] = $val;
			}
			
			if(!$circle) return false;
			
			// pr($circle);
			$data['result'] = $circle;
			$data['total'] = $friends['total'];	
			
		// pr($data);
			return $data;
			
			
		}
		return false;
	
	}
	
	function createCircleUser(){
		$name = preg_replace("/_/i"," ",strip_tags($this->apps->_request('name')));
		$groupid = intval($this->apps->_p('groupid'));
		if($name=='') return false;
		if($groupid!=0){
			$sql = "
			UPDATE my_circle_group SET name=\"{$name}\"
			WHERE id={$groupid} LIMIT 1;
			";
			// pr($sql);
			$this->apps->query($sql);
			return true;
		}else{
			$sql = "
			INSERT INTO my_circle_group (name,userid,datetime,n_status)
			VALUES ('{$name}',{$this->uid},NOW(),1)
			ON DUPLICATE KEY UPDATE n_status=1;
			";		
			$this->apps->query($sql);
			if($this->apps->getLastInsertId()) return array("result"=>true,"content"=>$this->apps->getLastInsertId());
			else return false;
		}

		
	
	}
	
	function uncreateCircleUser(){
		$circleid = strip_tags($this->apps->_p('circleid'));
		// $name = str_replace("_"," ",strip_tags($this->apps->_request('name')));
		$sql = "
		UPDATE my_circle_group SET n_status=0
		WHERE id= {$circleid} AND userid={$this->uid}
		LIMIT 1
		";
		
		$result = $this->apps->query($sql);
		if($result) {
			$sql = "
			UPDATE my_circle SET groupid = 0
			WHERE userid = {$this->uid} AND groupid={$circleid}
			";
			$result = $this->apps->query($sql);			
			if($result)return true;
			else {
				$sql = "
					DELETE FROM my_circle WHERE groupid <> 0 AND userid = {$this->uid} AND groupid={$circleid}
				";
				$result = $this->apps->query($sql);	
				if($result)return true;
				else return false;
			}
		}else return false;
	
	}
	
	function addCircleUser(){
		$uid = intval($this->apps->_request('fid'));
		$ftype = intval($this->apps->_request('ftype'));
		$groupid = intval($this->apps->_request('groupid'));

		//cek default circle , friends on circle
		if($this->uid==$uid) return false;
		$sql = "SELECT count(*) total, id FROM my_circle WHERE userid= {$this->uid} AND friendid={$uid} AND ftype={$ftype} AND groupid=0 LIMIT 1";
			
		$qData = $this->apps->fetch($sql);
		
		if(!$qData) return false;
		if($qData['total']>0){
		$oldid = $qData['id'];
		//if found, use update to move friend
			//check they have other group
				$sql = "SELECT count(*) total, id FROM my_circle WHERE userid= {$this->uid} AND friendid={$uid} AND ftype={$ftype} AND groupid = {$groupid} LIMIT 1";
				$qData = $this->apps->fetch($sql);
			
				if(!$qData) return false;
				if($qData['total']>0){
				//if found, update the status to true
					$sql = "
					UPDATE my_circle SET n_status = 1
					WHERE userid = {$this->uid} AND friendid={$uid} AND id={$qData['id']} AND ftype={$ftype} LIMIT 1
					";
					
					$result = $this->apps->query($sql);	
					if($result) return true;
					else return false;
				}else{
					//if really not found, then use insert
					$sql = "
					INSERT INTO my_circle (friendid,userid,ftype,groupid,date_time,n_status)
					VALUES ('{$uid}',{$this->uid},{$ftype},{$groupid},NOW(),1)
					ON DUPLICATE KEY UPDATE groupid = {$groupid}, n_status=1
					";
/* 					$sql = "
					UPDATE my_circle SET groupid = {$groupid} , n_status = 1
					WHERE userid = {$this->uid} AND friendid={$uid} AND id={$oldid} LIMIT 1
					";
 */					$result = $this->apps->query($sql);	
					if($result) return true;
				}
		}else{
		//if not found, re-check other id
			$sql = "SELECT count(*) total, id FROM my_circle WHERE userid= {$this->uid} AND friendid={$uid} AND groupid = {$groupid} AND ftype={$ftype} LIMIT 1";
			$qData = $this->apps->fetch($sql);
			if(!$qData) return false;
			if($qData['total']>0){
				//if found, update the status to true
				$sql = "
				UPDATE my_circle SET n_status = 1
				WHERE userid = {$this->uid} AND friendid={$uid} AND id={$qData['id']} AND ftype={$ftype} LIMIT 1
				";
				
				$result = $this->apps->query($sql);	
				if($result) return true;
				else return false;
				
			}else{
				//if really not found, then use insert
				$sql = "
				INSERT INTO my_circle (friendid,userid,ftype,groupid,date_time,n_status)
				VALUES ('{$uid}',{$this->uid},{$ftype},{$groupid},NOW(),1)
				ON DUPLICATE KEY UPDATE groupid = {$groupid}, n_status=1
				";
				
				$this->apps->query($sql);
				
				if($this->apps->getLastInsertId()) return true;
				else return false;
			}
		}		
		
		return false;
		
	
	}
	
	function deGroupCircleUser(){
		$uid = intval($this->apps->_request('uid'));
		$groupid = intval($this->apps->_request('groupid'));
		//cek friend on circle
		$sql = "SELECT count(*) total FROM my_circle WHERE userid= {$this->uid} AND friendid={$uid} AND groupid={$groupid} LIMIT 1";
		$qData = $this->apps->fetch($sql);
		if(!$qData) return false;
		if($qData['total']>0){
		//if found, use update to move friend
			$sql = "
			UPDATE my_circle SET n_status = 0
			WHERE userid = {$this->uid} AND friendid={$uid} AND groupid={$groupid} LIMIT 1
			";
			$result = $this->apps->query($sql);	
			if($result) return true;
			else return false;
		
		}else return false;
		
		
	
	}
	
	function unAddCircleUser(){
		$uid = intval($this->apps->_request('fid'));
		$groupid = intval($this->apps->_request('groupid'));
		$ftype = intval($this->apps->_request('ftype'));
		//cek friend on circle
		$sql = "SELECT count(*) total FROM my_circle WHERE userid= {$this->uid} AND friendid={$uid} AND ftype={$ftype} LIMIT 1";
		$qData = $this->apps->fetch($sql);
		if(!$qData) return false;
		if($qData['total']>0){
		//if found, use update to move friend
			$sql = "
			UPDATE my_circle SET n_status = 0
			WHERE userid = {$this->uid} AND friendid={$uid} AND ftype={$ftype}
			";
			$result = $this->apps->query($sql);	
			if($result) return true;
			else return false;
		}else return false;
		
		
	
	}
	
	function attending($attendartype=0){
		
		$contentid = intval($this->apps->_request('contentid'));
		if($contentid==0) return false;
		
		if($attendartype!=0) {
			
			//select to my_pages_type as what
			$otherid = 0;
		}else $otherid = $this->uid;
		if($otherid==0) return false;
	
		$sql = "SELECT count(*) total FROM my_contest WHERE otherid={$otherid}  AND  mypagestype={$attendartype} AND contestid={$contentid} LIMIT 1";
			// pr($sql);
		$qData = $this->apps->fetch($sql);
		if(!$qData) return false;
		if($qData['total']>0) return false;
			
		$sql = "INSERT INTO my_contest (contestid,otherid,mypagestype,join_date,n_status) VALUES ({$contentid},{$otherid},{$attendartype},NOW(),1)";

		$this->apps->query($sql);
		if($this->apps->getLastInsertId()) return true;
		return false;
		
	}
	
	function getUserFavorite(){
		
		$uid = strip_tags($this->apps->_request('uid'));
		$start = intval($this->apps->_request('start'));	
		$limit = 9;
		if(!$uid) $uid = intval($this->uid);
		if($uid!=0 || $uid!=null) {
				$sql ="
					SELECT contentid FROM {$this->dbshema}_news_content_favorite WHERE n_status=  1 AND userid IN ({$uid})  GROUP BY contentid
					";
		
				$qData = $this->apps->fetch($sql,1);
				if($qData) {
					foreach($qData as $val){
						$favoriteData[$val['contentid']]=$val['contentid'];
					}
					
				if(!$favoriteData) return false;
				$strContentId = implode(',',$favoriteData);
				
					$sql = "
						SELECT id,title,brief,image,thumbnail_image,slider_image,posted_date,file,url,fromwho,tags,authorid,topcontent,cityid 
						FROM {$this->dbshema}_news_content 
						WHERE AND n_status<>3  AND id IN ({$strContentId}) 
						ORDER BY posted_date DESC , id DESC
						LIMIT {$start},{$limit}";
					
					$rqData = $this->apps->fetch($sql,1);
					if(!$rqData) return false;
					//cek detail image from folder
						//if is article, image banner do not shown
					foreach($rqData as $key => $val){
						if(file_exists("{$CONFIG['LOCAL_PUBLIC_ASSET']}article/{$val['image']}")) $rqData[$key]['banner'] = false;
						else $rqData[$key]['banner'] = true;		
					}
				
				if($rqData) $qData=	$this->getStatistictArticle($rqData);
				
				return $qData;
				}
		}
		return false;
	}
	
	
	function getSearchFriends(){
		global $CONFIG;
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
		$sql = "
		SELECT count(*) total FROM
		(
		( SELECT id,n_status,name,last_name,email FROM social_member sm WHERE n_status =1  {$qKeywords} ORDER BY name ASC  )
		UNION 
		( SELECT id,n_status,name,last_name,email FROM my_entourage me WHERE n_status =1 AND referrerbybrand = {$this->apps->user->id}   {$qKeywords} ORDER BY name ASC  )
		) a ";
		$total = $this->apps->fetch($sql);
		// pr($sql);
		if(!$total) return false;
		
		$sql = "SELECT 
		id friendid,name,img,email,IF(last_name IS NULL,'',last_name) last_name ,ftype FROM
		(
		( SELECT id,n_status,name,last_name,email,img,1 ftype FROM social_member sm WHERE n_status =1  {$qKeywords}  )
		UNION 
		( SELECT id,n_status,name,last_name,email,img,0 ftype FROM my_entourage me WHERE n_status =1 AND referrerbybrand = {$this->apps->user->id}  {$qKeywords}  )
		) a
		ORDER BY name ASC, last_name ASC LIMIT {$start},{$limit}";
		$qData = $this->apps->fetch($sql,1);
		// pr($qData);
		$this->logger->log($sql);
		if(!$qData) return false;
		if($qData) {
			$arrSocialFid = false;
			$arrEntourageFid = false;
				foreach($qData as $val){	
					/* BA */	
					if($val['ftype']==0) $arrEntourageFid[$val['friendid']] = $val['friendid'];
					/* entourage */
					if($val['ftype']==1) $arrSocialFid[$val['friendid']] = $val['friendid'];
					
					$circledata[$val['ftype']][$val['friendid']]= $val;
				}
				$socialdata = false;
				$entouragedata = false;
				if($arrSocialFid) {
					$strsocialfid = implode(',',$arrSocialFid);
					$socialfid = $this->socialdata($strsocialfid);
					if($socialfid){
						foreach($socialfid as $key => $val){
							$socialfid[$key]['ftype'] = 1;
							$socialdata[1][$val['id']]=$socialfid[$key];
						}
					}
				}
				
				if($arrEntourageFid) {
					$strentouragefid = implode(',',$arrEntourageFid);
					$this->logger->log($strentouragefid);
					$entouragefid = $this->entouragedata($strentouragefid);
					if($entouragefid){
						foreach($entouragefid as $key => $val){
								$entouragefid[$key]['ftype'] = 0;
								$entouragedata[0][$val['id']]=$entouragefid[$key];
						}
					}
				}
				

				
			
				if(!$circledata) return false;
				
				// pr($socialdata);
				// pr($entouragedata);
				// pr($circledata);
				//merge data
				foreach($circledata as $keyftype => $ftype){
					foreach($ftype as $key => $val){
					if($socialdata)if(array_key_exists($keyftype,$socialdata)) if(array_key_exists($key,$socialdata[$keyftype]))  $circle[] = $socialdata[$keyftype][$key];
					if($entouragedata) if(array_key_exists($keyftype,$entouragedata)) if(array_key_exists($key,$entouragedata[$keyftype]))  $circle[] = $entouragedata[$keyftype][$key];
					 // pr($val);
					}
					
				}
			
			
				
			
			
			// pr($circle);
			if($circle) {
			$data['result'] = $circle;			 
			$data['total'] = $total['total'];
			$data['myid'] = intval($this->apps->user->id);
			}
		}
		return $data;
		
	}
	
	function socialdata($strsocialfid=false){
			
			if(!$strsocialfid)return false;
			global $CONFIG;
					//get friend detail
			$sql =	" 
			SELECT sm.id,concat(sm.name,' ',sm.last_name) name,sm.img,sm.sex,sm.last_name , pagetype.name role,cityref.city
			FROM social_member sm
			LEFT JOIN my_pages pages ON pages.ownerid=sm.id
			LEFT JOIN my_pages_type pagetype ON pages.type=pagetype.id
			LEFT JOIN beat_city_reference cityref ON cityref.id=pages.city
			WHERE sm.id IN ({$strsocialfid}) AND  sm.n_status = 1  ";
			
			$qData = $this->apps->fetch($sql,1);
			if(!$qData) return false;
			foreach($qData as $key => $val){
			
				if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}user/photo/{$val['img']}")) $qData[$key]['image_full_path'] = $CONFIG['BASE_DOMAIN_PATH']."public_assets/user/photo/".$val['img'];
				else $qData[$key]['image_full_path'] = $CONFIG['BASE_DOMAIN_PATH']."public_assets/user/photo/default.jpg";
				
				$qData[$key]['latestengagement'] =$this->getbaengagement(1,$val['id']);				
				$qData[$key]['persontype'] =false;
				 
			}
			return $qData;
			
	}
	
	function entouragedata($strentouragefid=false,$usingowner=true){
			
			if(!$strentouragefid) return false;
				global $CONFIG;
			$competitorarr = array('221','273','10','12','61');
			$pmiarr = array('00AM','Marlboro');
			
			if($usingowner) $ownerthisentourage = " AND entou.referrerbybrand = {$this->uid} ";
			else $ownerthisentourage = "";
			//get friend detail
			$sql =	" 
			SELECT entou.id,entou.name,entou.img,entou.sex,entou.last_name,'entourage' role,entou.Brand1_ID ,cityref.city
			FROM my_entourage entou
			LEFT JOIN beat_city_reference cityref ON cityref.id=entou.city
			WHERE entou.id IN ({$strentouragefid}) {$ownerthisentourage}  AND  entou.n_status = 1  ";
				$this->logger->log($sql);
				// pr($sql);
			$qData = $this->apps->fetch($sql,1);
			if(!$qData) return false;
			
			$latestengagement = $this->getlatestengagement($strentouragefid);
			
			foreach($qData as $key => $val){
					if(is_file($CONFIG['LOCAL_PUBLIC_ASSET']."entourage/photo/".$val['img'])) $qData[$key]['image_full_path']= $CONFIG['BASE_DOMAIN_PATH']."public_assets/entourage/photo/".$val['img'];
					else  $qData[$key]['image_full_path']=  $CONFIG['BASE_DOMAIN_PATH']."public_assets/entourage/photo/default.jpg";
					
					$qData[$key]['persontype'] = "Our";
					if(in_array($val['Brand1_ID'],$competitorarr)) $qData[$key]['persontype'] = "Competitor";				
					if(in_array($val['Brand1_ID'],$pmiarr)) $qData[$key]['persontype'] = "PMI";
					
					if($latestengagement){					
						if(array_key_exists($val['id'],$latestengagement))  $qData[$key]['latestengagement'] = $latestengagement[$val['id']];
						else  $qData[$key]['latestengagement'] = false;
					}
				
			}
			$this->logger->log($sql);
			return $qData;
			
	}
	
	function getlatestengagement($strentourage=false,$limit=1){
		if($strentourage==false) return false;
		//get enggement of entourage
			$sql ="SELECT *
			FROM {$this->dbshema}_news_content_tags
			WHERE friendid IN ({$strentourage}) AND userid={$this->apps->user->id} AND n_status=1 AND friendtype = 0
			GROUP BY friendid , userid ORDER BY date DESC 
			";	
			
			$qData = $this->apps->fetch($sql,1);
			$arrfid = false;
			if(!$qData) return false;
			foreach($qData as $key => $val){
					
				$contentid[$val['contentid']] = $val['contentid'];				
			}
				$contentarr = false;
			if($contentid){
		
				$strcid = implode(',',$contentid);
				$sql="SELECT id,title,brief,image,posted_date
				FROM {$this->dbshema}_news_content anc
				WHERE id IN ({$strcid}) ORDER BY posted_date DESC LIMIT {$limit} ";				
				$rqData = $this->apps->fetch($sql,1);
				foreach($rqData as $key => $val){
							
					$contentarr[$val['id']] = $val;				
				}
			}
			if(!$qData) return false;
			foreach($qData as $key => $val){
					
				if($contentarr){					
						if(array_key_exists($val['contentid'],$contentarr))  $qData[$key]['contentdetail'] = $contentarr[$val['contentid']];
						else  $qData[$key]['contentdetail'] = false;
				}	
				
				$arrfid[$val['friendid']] = $qData[$key];	
			}
			// pr($arrfid);exit;
			return $arrfid;
	}
	
	function changepassword(){
		
		$oldpass = strip_tags($this->apps->_p('oldpass'));
		$newpass = strip_tags($this->apps->_p('newpass'));
		$confirmnewpass = strip_tags($this->apps->_p('confirmnewpass'));
		$this->logger->log($oldpass.'-'.$newpass.'-'.$confirmnewpass);
		if($newpass!=$confirmnewpass) return false;
			$this->logger->log($oldpass.'-'.$newpass.'-'.$confirmnewpass);
		if(preg_match("/^(?=.{8,20})(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])/",$newpass)) {	
			$sql = "SELECT * FROM social_member WHERE id={$this->uid} LIMIT 1";
			$this->logger->log($oldpass.'-'.$newpass.'-'.$confirmnewpass);
			// pr($sql);exit;
			$rs = $this->apps->fetch($sql);
			if(!$rs) return false;
			
			$oldhashpass = sha1($oldpass.$rs['salt']);
			
			if($oldhashpass!=$rs['password']) return false;
				
			$hashpass = sha1($newpass.$rs['salt']);
					
			$sql ="UPDATE social_member SET password='{$hashpass}' WHERE id={$this->uid} LIMIT 1";
			$rs = $this->apps->query($sql);
			// pr($sql);exit;
			if($rs){
				$sql ="UPDATE social_member SET last_login=now(),login_count=login_count+1 WHERE id={$this->uid} LIMIT 1";
				$rs = $this->apps->query($sql);
				// pr($sql);exit;
				return true;
			}
		}
		$this->logger->log($oldpass.'-'.$newpass.'-'.$confirmnewpass.'-'.'not have secury password');
		return false;
		
	}
	
	function getbaengagement($limit=5,$authorids=false){
		/* must have checkin */
		if(!$authorids){
		$leadertype = intval($this->apps->user->leaderdetail->type);
			if($leadertype){
							$auhtorarrid[$this->uid] = $this->uid;
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
							}else $authorids = $this->uid;
							
			}
		}
		$sql ="
		SELECT cnt.id,contentid,userid,cnt.title, IF(cnt.brief IS NULL,cnt.title,cnt.brief) brief,join_date as posted_date
		FROM my_checkin checkin 
		LEFT JOIN social_member sm ON sm.id=checkin.userid
		LEFT JOIN {$this->dbshema}_news_content cnt ON cnt.id=checkin.contentid
		WHERE checkin.n_status = 1 AND contentid<>0 AND articleType = 5 AND checkin.userid IN ({$authorids})		
		GROUP BY contentid   ORDER BY join_date DESC LIMIT {$limit}";
		// pr($sql);
		$rs = $this->apps->fetch($sql,1);
		if(!$rs) return false;
		
		return $rs;
		
	}
	
	function getrecepient($type='all'){
		// pr($type);
			$socialData = false;
			if($type=='all'){
				$auhtorminion = @$this->apps->user->badetail;
				if($auhtorminion){
					foreach($auhtorminion as $key => $val){
						foreach($val as $keyval => $valval){
							$socialData[$key][$keyval] = $valval;
						}						
					}
				}
				$auhtorminion = @$this->apps->user->pldetail;
				if($auhtorminion){
					foreach($auhtorminion as $key => $val){
						foreach($val as $keyval => $valval){
							$socialData[$key][$keyval] = $valval;
						}						
					}
				}
				$auhtorminion = @$this->apps->user->branddetail;
				if($auhtorminion){
					foreach($auhtorminion as $key => $val){
						foreach($val as $keyval => $valval){
							$socialData[$key][$keyval] = $valval;
						}						
					}
				}
				$auhtorminion = @$this->apps->user->areadetail;
				if($auhtorminion){
					foreach($auhtorminion as $key => $val){
						foreach($val as $keyval => $valval){
							$socialData[$key][$keyval] = $valval;
						}						
					}
				}
			}else{
				$data = @$this->apps->user->$type;
				if($data){
					foreach($data as $key => $val){
						foreach($val as $keyval => $valval){
							$socialData[$key][$keyval] = $valval;
						}
					}
				}
			}
			
			return $socialData;
	
	}
	
	
	function gethirarkidata($type='sba',$leadid=false){
	
		$mesterdata = false;
		
		$topclass = @$this->apps->user->leaderdetail->topclass;
		$thisbrand = @$this->apps->user->leaderdetail->type;
		$thisarea = @$this->apps->user->leaderdetail->type;
		$thispl = @$this->apps->user->leaderdetail->type;
		
		if($type!='sba') if(!$leadid) return false;
		$qSearchUser = "";
		
		$brandid = "4";
		$brandsubid = "5";
		$brandallid = "{$brandid},{$brandsubid}";
		$areaallid = "6,7,8,9,10,11,12";
		$plalllid = "";
		
		$sbaalllid = "";
		
		/* brand user */
		$userbrand = @$this->apps->user->branddetail;
		$branduserarr = false;
		/* area user */
		$userarea = @$this->apps->user->areadetail;
		$areauserarr = false;
		/* sba user */
		$usersba = @$this->apps->user->badetail;
		$sbauserarr = false;
		/* PL user */
		$userpl = @$this->apps->user->pldetail;
		$pluserarr = false;
		
	
		
		 // pr($this->apps->user);
		/* brand */
		if($userbrand){
			foreach($userbrand as $val){
				 $branduserarr[$val->ownerid] = $val->ownerid;
			}
		}
		if(!$topclass) if($branduserarr) $brandallid = implode(',',$branduserarr);
		
		/* area */
		if($userarea){
			foreach($userarea as $val){
				 $areauserarr[$val->ownerid] = $val->ownerid;
			}
		}
		if(!$topclass) if($thisbrand!=3)  if($thisarea!=5)  if($areauserarr) $areaallid = implode(',',$areauserarr);
		
		/* sba */
		if($usersba){
			foreach($usersba as $val){
				 $sbauserarr[$val->ownerid] = $val->ownerid;
			}
		}
		if(!$topclass) if($sbauserarr) $sbaalllid = " AND ownerid IN (".implode(',',$sbauserarr).")";
		
		
		/* pl */
		if($userpl){
			foreach($userpl as $val){
				 $pluserarr[$val->ownerid] = $val->ownerid;
			}
		}
		if(!$topclass) if($thisbrand!=3)  if($thisarea!=5) if($pluserarr) $plalllid = " AND ownerid IN (".implode(',',$pluserarr).")";  
		
		// pr($userbrand);
		$areaid = "0";
		$plid = "0";
		
		$brandid = strip_tags($this->apps->_p('brandid'));
		$areaid = strip_tags($this->apps->_p('areaid'));
		$qbrandid = "";
		if($brandid){
				$qbrandid = " AND ( pages.brandid IN ({$brandid}) OR pages.brandsubid IN ({$brandid})) ";
		}
		
		$qareaid = "";
		if($areaid){
				$qareaid = " AND pages.areaid IN ({$areaid})   ";
		}
		
		
		if($type=='sba') {
				if($thispl!=2)		$qSearchUser = " AND otherid IN ({$leadid})  AND pages.type=1 {$sbaalllid} {$qareaid} {$qbrandid} ";
				else $qSearchUser = " AND   pages.type=1 {$sbaalllid} {$qareaid} {$qbrandid} ";
		}
		if($type=='area') $qSearchUser = " AND ( brandid IN ({$leadid}) OR brandsubid IN ({$leadid}) ) AND pages.type=5 AND pages.ownerid IN ({$areaallid}) ";
		if($type=='pl') {
			if($thisarea==5) {	
				$qareaid = " AND pages.city IN ({$this->apps->user->leaderdetail->city})   ";
				$qSearchUser = " 	AND   pages.type=2 {$plalllid} {$qbrandid}	{$qareaid} ";
			}else{
				$qSearchUser = " 	AND  areaid IN ({$leadid}) AND pages.type=2 {$plalllid} {$qbrandid}	{$qareaid} ";
			}
		}
		if($type=='brand') $qSearchUser = " AND pages.type=3 AND pages.ownerid IN ({$brandallid}) "; 
		
				
			$sql ="
					SELECT CONCAT(sm.name,' ',sm.last_name,' (',pages.name,') ') name, pages.id ,pages.type ,pages.img,pages.ownerid ,pagetype.name pagetypename
					FROM my_pages pages
					LEFT JOIN my_pages_type pagetype ON pagetype.id=pages.type
					LEFT JOIN social_member sm ON sm.id=pages.ownerid
					WHERE 1 
					{$qSearchUser} AND sm.n_status=1";
			
			// if($type=='sba')  pr($sql);
			// pr($sql);
			$qData = $this->apps->fetch($sql,1);
			if($qData){
				foreach($qData as $key => $val){
					$mesterdata[$val['id']] =  $val;
				}
			
			}
			
			
		 
		return $mesterdata;
		
	}
}

?>

