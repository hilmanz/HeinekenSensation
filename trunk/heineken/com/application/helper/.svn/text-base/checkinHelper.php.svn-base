<?php 

class checkinHelper {

	function __construct($apps){
		global $logger,$CONFIG;
		$this->logger = $logger;
		$this->apps = $apps;
		if(is_object($this->apps->user)) $this->uid = intval($this->apps->user->id);

		$this->dbschema = "beat";	
		$this->radius = intval($CONFIG['radius']) / 10000;
		$this->pradius = 500 / 10000;
		$this->dbshema = "beat";
	}


	function searchvenue($coor=null,$keywords=null){
		// search vennue
		$limit = 30;
		$start= intval($this->apps->_request('start'));
		
		if (strip_tags($this->apps->_request('coor'))) {
			$coor = strip_tags($this->apps->_request('coor'));
		} else {
			if ($coor) $coor;
		}
		/* testing */
		
		$searchKeyOn = array("latitude","longitude");
		
		if (strip_tags($this->apps->_p('keywords'))) {
			$keywords = strip_tags($this->apps->_p('keywords'));
		} else {
			if ($keywords) $keywords;
		}
		
		$keywords = rtrim($keywords);
		$keywords = ltrim($keywords);
		if($coor=='') return false;
		$realkeywords = $keywords;
		$newcoor =false;
		$lon = 0;
		$lonmax = 0;
		$lat = 0;
		$latmax = 0;
		
		
			/* radius calc */
			$arrcoor = explode(',',$coor);
			if(is_array($arrcoor)){
				$lat = $arrcoor[0] + $this->radius;
				$latmax = $arrcoor[0] - $this->radius;
				$lon = $arrcoor[1] - $this->radius;
				$lonmax = $arrcoor[1] + $this->radius;
		
			}
			
			/*
			
				SELECT SUBSTR(coor,1, LOCATE(',',coor)-1) lon, SUBSTR(coor,LOCATE(',',coor)+1) lat FROM `beat_city_reference` WHERE 1
			*/
			foreach($searchKeyOn as $key => $val){
				$searchKeyOn[$key] = " vm.{$val} like '%{$realkeywords}%' ";
				if($val=="city") $searchKeyOn[$key] = " vm.{$val} like '%{$realkeywords}%' ";
				
				if($val=="latitude"&&$lat!=0&&$latmax!=0) {
					$searchKeyOn[$key] = " 	vm.{$val} >= '{$lat}' AND vm.{$val} <= '{$latmax}' ";					
				}
				
				if($val=="longitude"&&$lon!=0&&$lonmax!=0) {
					$searchKeyOn[$key] = " vm.{$val} >= '{$lon}' AND vm.{$val} <= '{$lonmax}' ";					
									
				}
			}
		$keywordsearch = "";
		if($keywords) $keywordsearch = " AND ( vm.venuename like '%{$realkeywords}%' OR vm.city like '%{$realkeywords}%' OR vm.provinceName like '%{$realkeywords}%' OR vr.keywords  like '%{$realkeywords}%' ) ";
		$strSearchKeyOn = implode(" AND ",$searchKeyOn);
		$qKeywords = " 	AND  ( {$strSearchKeyOn} {$keywordsearch} )";

		$sql = "
		SELECT vm.*,vr.keywords venue
		FROM {$this->dbschema}_venue_master vm
		LEFT JOIN {$this->dbschema}_venue_reference vr ON vr.venueid=vm.id
		WHERE 1 {$qKeywords} AND vr.keywords <> '' AND vr.keywords IS NOT NULL
		ORDER BY abs(vm.latitude - {$lat}) ASC, abs(vm.longitude - {$lon}) ASC, vm.venuename ASC LIMIT {$start},{$limit}";
		//pr($sql);
		$this->logger->log($coor);
		$this->logger->log($sql);
		$qData = $this->apps->fetch($sql,1);
		if($qData){
			$venueid = false;
			$datacheckin = false;
			foreach($qData as $val){
					$venueid[$val['id']] = $val['id'];
			}
			if($venueid){
				$strvenue = implode(',',$venueid);
			}
			if($strvenue){
				$sql ="SELECT COUNT(*) total,venueid FROM my_checkin WHERE userid={$this->uid} AND venueid IN  ({$strvenue}) GROUP BY venueid  ";
				$this->logger->log($sql);
				$mycheckin = $this->apps->fetch($sql,1);
				if($mycheckin){
				
					foreach($mycheckin as $val){
						$datacheckin[$val['venueid']] = $val['total'];
					}
					
				}
			}
			
			foreach( $qData as $key => $val ){
				$qData[$key]['mycheckin'] = 0;
				if($datacheckin){
					if(array_key_exists($val['id'],$datacheckin)) $qData[$key]['mycheckin'] = $datacheckin[$val['id']];
				}
			}
			
			
			return $qData;
		}			
		
		return false;
	}
	
	function getVenue($start=null,$limit=10,$filter=null){
		global $CONFIG;
		
		$result['result'] = false;
		$result['total'] = 0;
	
		if($start==null)$start = intval($this->apps->_request('start'));		
		$limit = intval($limit);
		$start= intval($this->apps->_request('start'));		
		
		$search = "";
		$startdate = "";
		$enddate = "";
		if ($this->apps->_p('search')) {
			if ($this->apps->_p('search')!="Search...") {
				$search = rtrim($this->apps->_p('search'));
				$search = ltrim($search);
				
				if(strpos($search,' ')) $parseSearch = explode(' ', $search);
				else $parseSearch = false;
				
				if(is_array($parseSearch)) $search = $search.'|'.trim(implode('|',$parseSearch));
				else  $search = trim($search);
				
				$search = "AND (vr.keywords REGEXP  '{$search}' OR vm.venuename REGEXP  '{$search}' OR vm.city REGEXP  '{$search}') ";
			}
		}
		if ($this->apps->_p('startdate')) {
			$start_date = $this->apps->_p('startdate');
			$startdate = "AND vr.datetime >= '{$start_date}' ";
		}
		if ($this->apps->_p('enddate')) {
			$end_date = $this->apps->_p('enddate');
			$enddate = "AND vr.datetime < '{$end_date}' ";
		}
		
		//GET TOTAL VENUE
		$sql_total = "SELECT count(*) as total
			FROM {$this->dbschema}_venue_reference vr
			LEFT JOIN {$this->dbschema}_venue_master vm ON vr.venueid = vm.id
			WHERE 1 {$search} 
			AND vr.keywords <> '' AND vr.keywords IS NOT NULL 
			AND vm.venuename <> '' AND vm.venuename IS NOT NULL 			
			{$startdate} {$enddate} AND vr.n_status = 1";
		$total = $this->apps->fetch($sql_total);
		if(intval($total['total'])<=$limit) $start = 0;
		
		$cid = $this->apps->_request('id');		
		if ($filter) {
			$cid = "AND vr.id = {$cid}";
			$limit = 1;
		}
		
		// GET VENUE
		$sql = "
			SELECT vr.*,vm.provinceName,vm.venuename,vm.city 
			FROM {$this->dbschema}_venue_reference vr
			LEFT JOIN {$this->dbschema}_venue_master vm ON vr.venueid = vm.id
			WHERE 1 {$cid} {$search} 
			AND vr.keywords <> '' AND vr.keywords IS NOT NULL 
			AND vm.venuename <> '' AND vm.venuename IS NOT NULL 			
			{$startdate} {$enddate} AND vr.n_status = 1 ORDER BY vr.datetime DESC LIMIT {$start},{$limit}
		";
		//pr($sql);
		$qData = $this->apps->fetch($sql,1);
		
		$result['result'] = $qData;
		$result['total'] = $total['total'];
		return $result;
	}
	
	function checkin(){
		global $CONFIG;
		$venueid = intval($this->apps->_p('venueid'));
		$contentid = intval($this->apps->_p('cid'));
		$this->logger->log("checkin by plan : id {$contentid}");
		$venue = $this->apps->_p('venue');
		$brief = $this->apps->_p('brief');
		$title = $this->apps->_p('title');
		$content = $this->apps->_p('content');
		$venuerefid = intval($this->apps->_p('venuerefid'));
		$coor = $this->apps->_p('coor');
		$mypagestype = $this->apps->_p('mypagestype');
		$friendtags = $this->apps->_p('fid');
		$friendtypetags = $this->apps->_p('ftype');
		$rating = intval($this->apps->_p('rating'));
		$prize = intval($this->apps->_p('prize'));
		$wifi = intval($this->apps->_p('wifi'));
		$smoking = intval($this->apps->_p('smoking'));
		$image = '';
		$type = 3;
		$fromwho = 1;
		if(!$this->uid) return false;
		
		
		
		$authorid = intval($this->uid);
		
		if($mypagestype==0) $mypagestype = 1;
		/* radius calc */
			$arrcoor = explode(',',$coor);
			if(is_array($arrcoor)){
				$lat = $arrcoor[0];				
				$lon = $arrcoor[1];
			
		
			}
		$path = $CONFIG['LOCAL_PUBLIC_ASSET']."article/";
		
			$this->logger->log(json_encode(@$_FILES['image']));
			
			if (isset($_FILES['image'])&&$_FILES['image']['name']!=NULL) {
				if (isset($_FILES['image'])&&$_FILES['image']['size'] <= 20000000) {
					$data = $this->apps->uploadHelper->uploadThisImage($_FILES['image'],$path);
				
						if ($data['arrImage']!=NULL) {
								$image = $data['arrImage']['filename'];
						}
				}
			}
			
		if(!$contentid){
			 	
		
		
			$posted_date = date('Y-m-d H:i:s');		
			
			$sql ="
				INSERT INTO {$this->dbshema}_news_content (cityid,brief,title,content,tags,image,articleType,created_date,posted_date,authorid,fromwho,n_status,url) 
				VALUES ('{$venueid}','{$brief}','{$title}',\"{$content}\",'','{$image}',{$type},NOW(),'{$posted_date}','{$authorid}','{$fromwho}',1,'')
				";
				
				// pr($sql);
			$this->logger->log($sql);
			$this->apps->query($sql);
			if($this->apps->getLastInsertId())  $contentid = $this->apps->getLastInsertId();
		}else{
			if($contentid!=0){
			
				$sql = "UPDATE {$this->dbshema}_news_content SET image='{$image}' WHERE id = {$contentid} LIMIT 1";
				$this->logger->log($sql);
				$this->apps->query($sql);
				
			}
		}
		
		 // Full texts 	id 	contentid 	userid	venue 	venueid 	venuerefid 	coor	mypagestype join_date 	n_status
		$sql = " INSERT INTO my_checkin(contentid ,	userid	,venue ,	venueid 	,venuerefid 	,latitude,longitude	,mypagestype ,rating ,prize ,wifi ,smoking ,join_date ,	n_status) VALUES 
		({$contentid},{$this->uid},\"{$venue}\",\"{$venueid}\",\"{$venuerefid}\",\"{$lat}\",\"{$lon}\",\"{$mypagestype}\",{$rating},{$prize},{$wifi},{$smoking},NOW(),1)
		";
		// pr($sql);
		$this->logger->log($sql);
		$this->apps->query($sql);
		if($this->apps->getLastInsertId()) {
			if($friendtags){
				$cid = $this->apps->getLastInsertId();
				$arrfid = explode(',',$friendtags);
				$arrftype = explode(',',$friendtypetags);
				$frienddata = false;
				if(is_array($arrfid)){
					foreach($arrfid as $key => $val){
						$frienddata[$key]['fid'] = $val;
						$frienddata[$key]['ftype'] = intval($arrftype[$key]);
						if(array_key_exists($key,$arrftype)) $frienddata[$key]['ftype'] = $arrftype[$key];
					}
					
					if($frienddata){
				
						foreach($frienddata as $val){
							$this->apps->contentHelper->addFriendTags($contentid,$val['fid'],$val['ftype']);
						}
					
					}
				}else{
					$this->apps->contentHelper->addFriendTags($contentid,$friendtags,$friendtypetags);
				}
			}
			return true;
		}
		else return false;
	}
	
	function addvenue(){
		$venueid = intval($this->apps->_p('venueid'));
		$keywords = $this->apps->_p('keywords');	
		$city = $this->apps->_p('city');	
		$venuename = $this->apps->_p('venuename');	
		$venuecategory = $this->apps->_p('category');	
		if($venuename=='') $venuename = $keywords;
		$coor = $this->apps->_p('coor');
			$searchKeyOn = array("latitude","longitude");
	
				/* radius calc */
			$arrcoor = explode(',',$coor);
			if(is_array($arrcoor)){
				$lati = $arrcoor[0];				
				$longi = $arrcoor[1];	
			}
		
		
	
		/* radius calc */
		$arrcoor = explode(',',$coor);
		if(is_array($arrcoor)){
			$lat = $arrcoor[0] + $this->pradius;
			$latmax = $arrcoor[0] - $this->pradius;
			$lon = $arrcoor[1] - $this->pradius;
			$lonmax = $arrcoor[1] + $this->pradius;
	
		}
		
		foreach($searchKeyOn as $key => $val){
				
			if($val=="latitude"&&$lat!=0&&$latmax!=0) {
				$searchKeyOn[$key] = " 	{$val} >= '{$lat}' AND {$val} <= '{$latmax}' ";					
			}
			
			if($val=="longitude"&&$lon!=0&&$lonmax!=0) {
				$searchKeyOn[$key] = " {$val} >= '{$lon}' AND {$val} <= '{$lonmax}' ";					
								
			}
		}
	
		$strSearchKeyOn = implode(" AND ",$searchKeyOn);
		$qKeywords = " 	AND  ( {$strSearchKeyOn} )";

		$sql = "
		SELECT * FROM {$this->dbschema}_venue_master 
		WHERE 1 {$qKeywords} AND venuename <> '' AND venuename IS NOT NULL ORDER BY id ASC LIMIT 1 ";
		//pr($sql);
		$this->logger->log($sql);
		$mastervenue = $this->apps->fetch($sql);
		if($mastervenue){
			$provinceName = $mastervenue['provinceName'];
			if($city=='') $city = $mastervenue['city'];
		}else{
			$provinceName = $keywords;
			if($city=='') $city = $keywords;
		}
		
		if($provinceName=='') return false;
		if($city=='') return false;
		if($venuename=='') return false;
		if($lati=='') return false;
		if($longi=='') return false;
		
		$sql ="INSERT INTO {$this->dbschema}_venue_master 
		( provinceName ,	city ,	venuename ,	latitude ,	longitude ,	venuecategory ,	n_status )
		VALUES(\"{$provinceName}\",\"{$city}\",\"{$venuename}\",\"{$lati}\",\"{$longi}\",'{$venuecategory}',1)
		";
			// pr($sql);
		$this->logger->log($sql);
		$this->apps->query($sql);
		if($this->apps->getLastInsertId()) {
			$venueid = $this->apps->getLastInsertId();
			$sql ="INSERT INTO {$this->dbschema}_venue_reference
			( venueid,	keywords ,latitude,longitude	, 	datetime ,	n_status )
			VALUES({$venueid},\"{$keywords}\",\"{$lati}\",\"{$longi}\",NOW(),1)
			";
			
			$this->apps->query($sql);
			if($this->apps->getLastInsertId()) 	{
					$data['result'] = true;
					$data['venueid'] = $venueid;
					$data['venuename'] = $venuename;
					$data['keywords'] = $keywords;
					$data['venuerefid'] = $this->apps->getLastInsertId();
					$data['coor'] = $coor;
					return $data;
			}
			else {
				$data['result'] = false;
				return $data;
			}
		}
		else return false;
		
		
	}
	
		
	function uncheckin(){
		$cid = intval($this->apps->_p('cid'));
		
		$sql = " UPDATE my_checkin SET n_status = 0 WHERE userid={$this->uid} AND id={$cid} LIMIT 1";
		// pr($sql);
		$qData = $this->apps->query($sql);
		if($qData) return true;
		else return false;
	}
	
	
}

?>

