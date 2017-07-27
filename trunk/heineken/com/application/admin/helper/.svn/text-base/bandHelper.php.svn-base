<?php 

class bandHelper {
	
	function __construct($apps){
		global $logger;
		$this->logger = $logger;
		$this->apps = $apps;
		if(is_object($this->apps->user)) $this->uid = intval($this->apps->user->id);
		$this->dbshema= 'beat';
	}
	
	function getBandProfile(){
		global $CONFIG;
		
		$pid = intval($this->apps->_request('pid'));
		if(!$pid) $pid = intval($this->apps->user->pageid);
		if($pid!=0 || $pid!=null) {
		
			$sql = "SELECT mp.*,'' as influencer,mpc.cityid,mpg.genreid,cg.category as genre,sm.email
			FROM my_pages mp
			LEFT JOIN  social_member sm ON mp.ownerid = sm.id
			LEFT JOIN my_pages_city mpc ON mp.id = mpc.mypagesid
			LEFT JOIN my_pages_genre mpg ON mp.id = mpg.mypagesid
			LEFT JOIN athreesix_news_content_category cg ON mpg.genreid = cg.id
			WHERE mp.id = {$pid} AND mp.n_status = 1 LIMIT 1";
			$this->logger->log($sql);
			$qdata = $this->apps->fetch($sql);
			
			if(!$qdata) return false;
			if ($qdata['cityid']) {
				$getcity = $this->apps->contentHelper->getCity($province=NULL,$type=NULL,$qdata['cityid']);
				if($getcity) {
					foreach($getcity as $key => $val) {
						$valcity = $val['city'];
					}
				}else $valcity = false;
				$qdata['cityname'] = $valcity;
			}
			
			/* if($qdata['genreid']) {
				$genre = $this->apps->bandHelper->genre();
				foreach ($genre as $k => $v) {
					if(array_key_exists('genreid',$qdata)) {
						$category[$v['id']] = $v['category'];
						$qdata['genre'] = $category[$qdata['genreid']];
					} else {
						$qdata['genre'] ="";
					}
				}
			} */
			$qdata['influencer'] .= $this->getPageInfluence($qdata['id']);
			if(!is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}pages/{$qdata['img']}")) $qdata['img']="";
			//pr($qdata);
			if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}pages/original_{$qdata['img']}")) $qdata['imgoriginal']= "original_{$qdata['img']}";
			else $qdata['imgoriginal'] = false;
			
			return $qdata;
		}
		return false;
	}
	
	function updateBandProfile($pages){
		$imtheowner = $this->getownerband();		
		if(!$imtheowner) return false;
		
		$loginHelper = $this->apps->useHelper('loginHelper');
		$this->logger->log('can update band');	
		
		//get band
		$sql = "SELECT * FROM my_pages WHERE n_status=1 AND ownerid={$this->uid} LIMIT 1";
		$this->logger->log($sql);
		$rs = $this->apps->fetch($sql);
		if(!$rs)return false;
		$rs = null;
		
		$name = strip_tags($this->apps->_p('name'));
		$idcity = intval($this->apps->_p('city'));
		$idgenre = intval($this->apps->_p('genre'));
		$influencer = strip_tags($this->apps->_p('influence'));
		$description = strip_tags($this->apps->_p('description'));
		$type = $pages=='myband' ? 1 : 4;
		
		if (isset($_POST['person'])) {
			$persons = $_POST['person'];
		} else {
			$persons = "";
		}
		
		if (isset($_POST['instrument'])) {
			$instrument = $_POST['instrument'];
		} else {
			$instrument = "";
		}
		
		if (isset($_POST['new_instrument'])) {
			$new_instrument = $_POST['new_instrument'];
		} else {
			$new_instrument = "";
		}
		
		if($name!='') $arrQuery[] = " name='{$name}' ";
		if($description!='') $arrQuery[] = " description='{$description}' ";
		
		$strQuery = implode(',',$arrQuery);
		if(!$strQuery) return false;
		$this->logger->log($strQuery);
		$pageid = $this->apps->user->pageid;
		if($pageid) $pageid; else return false;
		
		$sql = " UPDATE my_pages SET {$strQuery} WHERE id={$pageid} AND n_status=1 LIMIT 1";
		$this->logger->log($sql);
		$qData = $this->apps->query($sql);
		if($qData) {
			if ($idcity!='') {
				$sql_city = "UPDATE my_pages_city SET cityid = {$idcity} WHERE mypagesid = {$pageid}";
				$this->apps->query($sql_city);
			} else return false;
			
			if ($idgenre!='') {
				$sql_genre = "UPDATE my_pages_genre SET genreid = {$idgenre} WHERE mypagesid = {$pageid}";
				$this->apps->query($sql_genre);
			} else return false;
			
			if ($influencer!='') {
				$sql_influence = "UPDATE my_pages_influencer SET influencer = \"{$influencer}\" WHERE mypagesid = {$pageid}";
				$this->apps->query($sql_influence);
			} else return false;
			
			if ($instrument!='') {
				foreach ($instrument as $key => $val) {
					$id_member = $key;
					$instrument = $val;
					$sql_member = "UPDATE my_pages_member SET instrument = \"{$instrument}\" WHERE mymember = {$id_member}";
					$this->apps->query($sql_member);
				}
			} else {
				$sql_member = "DELETE FROM my_pages_member WHERE myid = {$pageid}";
				$this->apps->query($sql_member);
			}
			if ($new_instrument!=''){
				foreach ($new_instrument as $key => $val) {
					$id_member = $key;
					$new_instrument = $val;
					$sql_member = "INSERT INTO my_pages_member (myid,mymember,instrument,mypagestype,created_date) VALUES ('{$pageid}','{$id_member}',\"{$new_instrument}\",'{$type}',NOW())";
					$this->apps->query($sql_member);
				}
			} else return false;
			
			$sql = "
			SELECT *
			FROM social_member 
			WHERE 
			n_status=1 AND id={$this->uid} LIMIT 1";
			$this->logger->log($sql);
			$rs = $this->apps->fetch($sql);
			if($rs) $loginHelper->setdatasessionuser($rs); 
			else return false;
			return true;
		}else return false;
	}
	
	function getownerband(){
		$ownerid = $this->uid;
		$sql="SELECT COUNT(*) as total FROM my_pages WHERE ownerid={$ownerid} AND n_status = 1 LIMIT 1;";
		$qData = $this->apps->fetch($sql);
		if($qData) {
			$total = intval($qData['total']);
			if($total>0) return true;			
		}
		return false;
		
	}

	function haveBand($id){
		$sql="SELECT COUNT(ownerid) as total FROM my_pages WHERE ownerid={$id} AND n_status = 1 LIMIT 1;";
		$r = $this->apps->fetch($sql);
		return $r;
	}
	
	function genre(){
		$sql_band = "SELECT * FROM athreesix_news_content_category WHERE pageid=21 ORDER BY category;";
		$sql_dj = "SELECT * FROM athreesix_news_content_category WHERE pageid=101 ORDER BY category;";
		$r['band'] = $this->apps->fetch($sql_band,1);
		$r['dj'] = $this->apps->fetch($sql_dj,1);
		return $r;
	}
	
	function insertMyPage($files=NULL,$path=NULL){
		$ownerid = intval($this->uid);
		$name = strip_tags($this->apps->_p('name'));
		$description = strip_tags($this->apps->_p('description'));
		$type = intval($this->apps->_p('typemusic'))==3 ? 1 : 4;
		
		$influence = strip_tags($this->apps->_p('influence'));
		if ($type==1) {
			$genreid = intval($this->apps->_p('genre'));
			$cityid = intval($this->apps->_p('city'));
		} else {
			$genreid = intval($this->apps->_p('genredj'));
			$cityid = intval($this->apps->_p('citydj'));
		}
		
		$content = strip_tags($this->apps->_p('content'));
		if (isset($_POST['person'])) {
			$persons = $_POST['person'];
		} else {
			$persons = "";
		}
		
		if (isset($_POST['instrument'])) {
			$instrument = $_POST['instrument'];
		} else {
			$instrument = "";
		}
		
		$description = $description=='Description' ? "" : $description;
		if (!empty($files['name'])) {
			if($files['size']<= 200000) {
				$data = $this->apps->uploadHelper->uploadThisImage($files,$path);
				if ($data['arrImage']!=NULL) {
					$image = $data['arrImage']['filename'];
					$sql = "INSERT INTO my_pages (ownerid,name,description,type,img,created_date,n_status) VALUES ('{$ownerid}','{$name}',\"{$description}\",'{$type}','{$image}',NOW(),1)";		
				}
			} else return false;
		} else {
			$sql = "INSERT INTO my_pages (ownerid,name,description,type,created_date,n_status) VALUES ('{$ownerid}','{$name}',\"{$description}\",'{$type}',NOW(),1)";
		}
		$this->logger->log($sql);
		if ($this->apps->query($sql)) {
			$idband = $this->apps->getLastInsertId();
			$sql = "
				SELECT *
				FROM social_member 
				WHERE 
				n_status=1 AND 
				id={$this->uid}
				LIMIT 1";
			$this->logger->log($sql);
			$rs = $this->apps->fetch($sql);
			$this->apps->loginHelper->setdatasessionuser($rs);
			if ($influence!='') {
				if ($influence!='Influence Music') $this->insertinfluence($idband,$influence);
			}
			if ($cityid!='') $this->insertCity($idband,$cityid);
			if ($genreid!='') $this->insertGenre($idband,$genreid); 
			if ($persons!='') {
				foreach ($instrument as $key => $val) {
					$id_member = $key;
					$instrument = $val;
					$this->insertMember($idband,$id_member,$type,$instrument);
				}
			}
			return true;
		} else return false;
	}
	
	function getPageInfluence($idband){
		$sql = "SELECT * FROM my_pages_influencer WHERE mypagesid = {$idband} LIMIT 1";
		$r = $this->apps->fetch($sql);
		$influence = $r['influencer'];
		return $influence;
	}
	
	function insertInfluence($idband,$influence){
		$sql = "INSERT INTO my_pages_influencer (mypagesid,influencer,n_status) VALUES ('{$idband}',\"{$influence}\",1)";
		$this->apps->query($sql);
	}
	
	function insertGenre($idband,$genreid){
		$sql = "INSERT INTO my_pages_genre (mypagesid,genreid,n_status) VALUES ('{$idband}','{$genreid}',1)";
		$this->apps->query($sql);
	}
	
	function insertCity($idband,$cityid){
		$sql = "INSERT INTO my_pages_city (mypagesid,cityid,n_status) VALUES ('{$idband}','{$cityid}',1)";
		$this->apps->query($sql);
	}
	
	function getMember($type=1,$limit=8,$start=0){
		//pr($this->apps->_g('page'));
		$page = $this->apps->_g('page');
		if($page) {
			$type = $page=="myband" ? 1 : 4;
		} else return false;
		$pid = intval($this->apps->_request('pid'));
		if(!$pid) $pid = intval($this->apps->user->pageid);
		if($pid!=0 || $pid!=null) {
			$memberdata = false;
			$arrMemberId = false;
			// get all member of this band
			$sql =	" SELECT count(*) total FROM my_pages_member WHERE myid = {$pid} AND mypagestype = {$type}";
			$members = $this->apps->fetch($sql);
			if($members){
				//get members
				$sql =	" SELECT * FROM my_pages_member WHERE myid = {$pid} AND mypagestype = {$type} LIMIT {$start},{$limit}";			
				$memberdata = $this->apps->fetch($sql,1);
				if($memberdata){
					foreach($memberdata as $val){
						$arrMemberId[$val['mymember']] = $val['mymember'];
						$memberdata[]= $val;
					}
				}
			}
			
			//get pages
			$sql = "SELECT * FROM my_pages WHERE id IN ({$pid}) AND type = {$type} LIMIT 1";			
			$pagedata = $this->apps->fetch($sql);			
			
			if(!$pagedata) return false;
			
			if($arrMemberId) array_push($arrMemberId,$pagedata['ownerid']);
			else $arrMemberId[$pagedata['ownerid']] = $pagedata['ownerid'];
			
			if($arrMemberId) $strMemberId = implode(',',$arrMemberId);
			else return false;
			
			//get friend detail
			$sql =	" SELECT sm.id,sm.name,sm.img,sm.sex,pm.instrument 
				FROM social_member sm
				LEFT JOIN my_pages_member pm ON sm.id = pm.mymember
				WHERE sm.id IN ({$strMemberId}) AND  sm.n_status = 1";			
			
			$qData = $this->apps->fetch($sql,1);
			if(!$qData) return false;
			foreach($qData as $val){
				$memberuser[$val['id']] = $val;
			}
			
			if(!$memberuser) return false;	
			$data['result'] = $memberuser;
			$data['total'] = count($memberuser);
			return $data;
		}
		return false;	
	}
	
	function insertMember($idband,$id_member,$type,$instrument){
		$sql = "INSERT INTO my_pages_member (myid,mymember,instrument,mypagestype,created_date) VALUES ('{$idband}','{$id_member}',\"{$instrument}\",'{$type}',NOW())";
		if ($this->apps->query($sql)) {
			if ($type==1) {
				$this->apps->log('add member band','my band');
			} else {
				$this->apps->log('add member dj','my dj');
			}
		}
	}
	
	function saveImage(){
		global $CONFIG;
		$loginHelper = $this->apps->useHelper('loginHelper');
		
		$filename="";
		if($_FILES['myImage']['error']==0)	{
			$path = "public_assets/pages/";
			$data = $this->apps->uploadHelper->uploadThisImage(@$_FILES['myImage'],$path);
			$filename = @$data['arrImage']['filename'];
			if ($data) {
				$sql = "UPDATE my_pages SET img = '{$filename}' WHERE ownerid={$this->uid} AND n_status=1 LIMIT 1";
				$this->logger->log($sql);
				$qData = $this->apps->query($sql);
				
				$sql = "
				SELECT *
				FROM social_member 
				WHERE n_status=1 AND id={$this->uid} LIMIT 1";
				$this->logger->log($sql);
				$rs = $this->apps->fetch($sql);
				if($rs) $loginHelper->setdatasessionuser($rs); 
				else return false;
			}
		}
		return $filename;
	}
	
	function saveCropImage(){
		global $CONFIG;
		
		$loginHelper = $this->apps->useHelper('loginHelper');
		
		$files['source_file'] = $this->apps->_p("imageFilename");
		$files['url'] = "{$CONFIG['LOCAL_PUBLIC_ASSET']}pages/";
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
		
		if(file_exists($files['url'].$files['source_file'])){
			//saveit
			$sql = "UPDATE my_pages SET  img = '{$files['source_file']}' WHERE id={$this->apps->user->pageid} LIMIT 1";
			$this->logger->log($sql);
			
			$qData = $this->apps->query($sql);
			if($qData){
				$sql = "
				SELECT *
				FROM social_member 
				WHERE n_status=1 AND id={$this->uid} LIMIT 1";
				$this->logger->log($sql);
				$rs = $this->apps->fetch($sql);
				if($rs) $loginHelper->setdatasessionuser($rs); 
				else return false;
				
				//$rs = $this->apps->fetch($sql);	
				//if(!$rs)return false;
				//$rs['img'] = $files['source_file'];
				//how to update the session on on fly
				//$this->apps->session->set($CONFIG['SESSION_NAME'],urlencode64(json_encode($rs)));
				return $files['source_file'];
			}else return false;
			
		}else return false;
				
	}
	
	function saveImageCover($type){
		global $CONFIG;
		$loginHelper = $this->apps->useHelper('loginHelper');
		
		$filename="";
		
		if($_FILES['myImageCover']['error']==0)	{
			$path = "public_assets/pages/cover/";
			$data = $this->apps->uploadHelper->uploadThisImage(@$_FILES['myImageCover'],$path);
			$filename = @$data['arrImage']['filename'];
			if ($data) {
				$sql_cek = "SELECT * FROM my_wallpaper WHERE myid = '{$this->uid}' AND type = {$type} AND n_status = 1 ORDER BY datetime DESC LIMIT 1";
				$cekCover = $this->apps->fetch($sql_cek);
				if($cekCover) {
					$sql = "UPDATE my_wallpaper SET n_status = 0 WHERE id = {$cekCover['id']} AND type = {$type}";
					$qData = $this->apps->query($sql);
					$sql = "INSERT INTO my_wallpaper (myid,image,type,datetime,n_status) VALUES ('{$this->uid}','{$filename}',{$type},NOW(),1)";
					$this->logger->log($sql);
					$qData = $this->apps->query($sql);
				} else {
					$sql = "INSERT INTO my_wallpaper (myid,image,type,datetime,n_status) VALUES ('{$this->uid}','{$filename}',{$type},NOW(),1)";
					$this->logger->log($sql);
					$qData = $this->apps->query($sql);
				}
				
				$sql = "
				SELECT *
				FROM social_member 
				WHERE n_status=1 AND id={$this->uid} LIMIT 1";
				$this->logger->log($sql);
				$rs = $this->apps->fetch($sql);
				if($rs) $loginHelper->setdatasessionuser($rs); 
				else return false;
			}
		}
		return $filename;
	}
	
	function saveCropImageCover($type){
		global $CONFIG;
		
		$loginHelper = $this->apps->useHelper('loginHelper');
		
		$files['source_file'] = $this->apps->_p("imageFilenameCover");
		$files['url'] = "{$CONFIG['LOCAL_PUBLIC_ASSET']}pages/cover/";
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
		
		if(file_exists($files['url'].$files['source_file'])){
			//saveit
			$sql = "UPDATE my_wallpaper SET image = '{$files['source_file']}' WHERE myid={$this->uid} AND n_status = 1 AND type = {$type} LIMIT 1";
			$this->logger->log($sql);
			
			$qData = $this->apps->query($sql);
			if($qData){
				$sql = "
				SELECT *
				FROM social_member 
				WHERE n_status=1 AND id={$this->uid} LIMIT 1";
				$this->logger->log($sql);
				$rs = $this->apps->fetch($sql);
				if($rs) $loginHelper->setdatasessionuser($rs); 
				else return false;
				return $files['source_file'];
			}else return false;
			
		}else return false;
				
	}
	
	function getPageGallery($start=0,$limit=10) {
		global $CONFIG;			
		$bandMember = $this->getMember();
		
		$qUser = "";
		if($bandMember) {
			foreach ($bandMember['result'] as $k => $v) {
				$arrMemberId[$v['id']] = $v['id'];
			}
			$strMemberId = implode(',',$arrMemberId);
			$qUser = " AND userid IN ({$strMemberId}) ";
		} else return false;
		
		if(!$this->apps->user->pageid) return false;
		// CEK REQUEST PID & DAPATKAN OWNERID
		$arrOwner = false;
		if (intval($this->apps->_request('pid'))) {
			$pid = intval($this->apps->_request('pid'));
			$sql_owner = "SELECT id,ownerid FROM my_pages WHERE id = '{$pid}' LIMIT 1";
			$arrOwner = $this->apps->fetch($sql_owner);
		}
		
		$pid = $arrOwner['ownerid'];
		if(!$pid) $pid = intval($this->apps->user->pageid);
		if($pid!=0 || $pid!=null) {
			$start = intval($this->apps->_request('start'));
			$sql_pages = "SELECT img,name FROM my_pages WHERE id={$pid}";
			$arrPages = $this->apps->fetch($sql_pages);
			
			//GET CONTENT
			$sql = "
			SELECT anc.id,anc.title,anc.brief,anc.image,'' as img_pages,'' as name_pages,anc.thumbnail_image,anc.slider_image,anc.posted_date,anc.file,anc.url,anc.fromwho,anc.tags,anc.authorid,anc.topcontent,anc.cityid,anct.type pagesname 
			FROM athreesix_news_content anc
			LEFT JOIN {$this->dbshema}_news_content_type anct ON anct.id = anc.articleType
			WHERE anc.authorid = {$pid} AND anc.fromwho = 2 ORDER BY anc.posted_date DESC LIMIT {$start},{$limit}";
			
			$qData = $this->apps->fetch($sql,1);
			if($qData){
				foreach($qData as $val){
					$arrContent[] = $val;
				}
			}else $arrContent = false;
			
			if($arrContent){
				//$arrContent = $this->apps->contentHelper->getStatistictArticle($arrContent);
				foreach($arrContent as $key => $val){
					$arrContent[$key]['img_pages'] = $arrPages['img'];
					$arrContent[$key]['name_pages'] = $arrPages['name'];
					$arrContent[$key]['imagepath'] = false;
						
					if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}article/{$val['image']}")) 	$arrContent[$key]['imagepath'] = "article";
					if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}article/small_{$val['image']}"))  	$arrContent[$key]['image'] = "small_{$val['image']}";
					
					$video_thumbnail = false;
					if($val['url']!='')	{
						//PARSER URL AND GET PARAM DATA
						$parseUrl = parse_url($val['url']);
						if(array_key_exists('query',$parseUrl)) parse_str($parseUrl['query'],$parseQuery);
						else $parseQuery = false;
						if($parseQuery) {
							if(array_key_exists('v',$parseQuery))$video_thumbnail = $parseQuery['v'];
						} 
						$arrContent[$key]['video_thumbnail'] = $video_thumbnail;
					}else $arrContent[$key]['video_thumbnail'] = false;		
				}
				//pr($arrContent);
				return $arrContent;
			} else return false;
		}
		return false;
	}
	
	function getPageCalender($start=null,$limit=3,$contenttype=0,$topcontent=array(0)) {
		global $CONFIG;
		
		$result['result'] = false;
		$result['total'] = 0;
		
		if($start==null)$start = intval($this->apps->_request('start'));
		$contenttype = intval($contenttype);
		$limit = intval($limit);
		$topcontent = implode(',',$topcontent);
		$typeid = strip_tags($this->apps->contentHelper->checkPage($contenttype));
		
		$pid = intval($this->apps->_request('pid'));
		if(!$pid) $pid = intval($this->apps->user->pageid);
		if($pid!=0 || $pid!=null) {
			//GET TOTAL ARTICLE
			$sql = "SELECT count(*) total FROM {$this->dbshema}_news_content  WHERE articleType IN ({$typeid}) AND topcontent IN ({$topcontent}) AND n_status<>3 AND authorid = {$pid} ";
			$total = $this->apps->fetch($sql);
			
			if(intval($total['total'])<=$limit) $start = 0;
			$sql = "
				SELECT id,title,brief,image,thumbnail_image,slider_image,posted_date,file,url,fromwho,tags,authorid,topcontent,cityid ,articleType
				FROM {$this->dbshema}_news_content
				WHERE articleType IN ({$typeid}) AND topcontent IN ({$topcontent}) AND n_status<>3 AND fromwho = 2 AND authorid = {$pid}
				ORDER BY posted_date DESC , id DESC
				LIMIT {$start},{$limit}
			";
			//pr($sql);
			$rqData = $this->apps->fetch($sql,1);
			if($rqData) {
				//cek detail image from folder
					//if is article, image banner do not shown
				foreach($rqData as $key => $val){
					$rqData[$key]['imagepath'] = false;
					if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}event/{$val['image']}")) 	$rqData[$key]['imagepath'] = "event";
					if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}banner/{$val['image']}")) 	$rqData[$key]['imagepath'] = "banner";
					if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}article/{$val['image']}"))  	$rqData[$key]['imagepath'] = "article";					
					
					if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}article/{$val['image']}")) $rqData[$key]['banner'] = false;
					else $rqData[$key]['banner'] = true;
					
					//check file
					if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}music/mp3/{$val['file']}")) $rqData[$key]['hasfile'] = true;
					else $rqData[$key]['hasfile'] = false;	
					
					//check file small
					if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}{$rqData[$key]['imagepath']}/small_{$val['image']}")) $rqData[$key]['image'] = "small_{$val['image']}";
				
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
				}
				
				if($rqData) $qData=	$this->apps->contentHelper->getStatistictArticle($rqData);
				else $qData = false;
			}else $qData = false;

			$result['result'] = $qData;
			$result['total'] = intval($total['total']);
			return $result;
		}
		return false;
	}
	
	function unMember(){
		$loginHelper = $this->apps->useHelper('loginHelper');
		$pid = intval($this->apps->_request('pid'));
		//if found, use update to move friend
		$sql = "DELETE FROM my_pages_member	WHERE mymember = {$this->uid} AND myid={$pid} ";
		$result = $this->apps->query($sql);	
		if($result) {
			$sql = "SELECT * FROM social_member WHERE n_status=1 AND id={$this->uid} LIMIT 1";
			$this->logger->log($sql);
			$rs = $this->apps->fetch($sql);
			if($rs) $loginHelper->setdatasessionuser($rs); 
			else return false;
			return true;
		} else return false;
	}
	
}
?>