<?php 

class entourageHelper {

	function __construct($apps){
		global $logger,$CONFIG;
		$this->logger = $logger;
		$this->apps = $apps;
		if($this->apps->isUserOnline())  {
			if(is_object($this->apps->user)) {
				$uid = intval($this->apps->_request('uid'));
				if($uid==0) $this->uid = intval($this->apps->user->id);
				else $this->uid = $uid;
			}
			
			
		}
		
		$this->config = $CONFIG;
		$this->dbshema = "beat";	
	}

	 function getDob( ){
		global $CONFIG;
		
		$sql = " SELECT name, birthday
				 FROM my_entourage ";
		$age = $this->apps->fetch($sql);	
		
		// $age = list($day,$month,$year) = explode("/",$birthday);
		// $year_diff  = date("Y") - $year;
		// $month_diff = date("m") - $month;
		// $day_diff   = date("d") - $day;
		// if ($day_diff < 0 || $month_diff < 0)
		  // $year_diff--;
		  
		return $age;
  }
  
  function getBrand(){
	global $CONFIG;
	$sql = " SELECT brandid
			 FROM my_pages ";
	$brand = $this->apps->fetch($sql);
	// pr($brand);
	return $brand;
  }
	
	function getGender(){
	global $CONFIG;
	
	$sql = " SELECT COUNT(*) male FROM my_entourage WHERE sex='Male' ";
	$data['male'] = $this->apps->fetch($sql);			
	
	$sql = " SELECT COUNT(*) Female FROM my_entourage WHERE sex='Female' ";
	$data['female'] = $this->apps->fetch($sql);		
	
	$sql = " SELECT COUNT(*) sex FROM my_entourage ";
	$data['all'] = $this->apps->fetch($sql);		
	
	return $data;
	
	// pr($data);
	
	}
	
	function getEntourage($streid=null,$start=0,$limit=10,$all=false){
		global $CONFIG;
		
		$sql = "SELECT code,brandtype FROM tbl_brand_preferences_references ";
		$qData = $this->apps->fetch($sql,1);
		if(!$qData)return false;
		$competitorarr = array();
		$pmiarr = array();
		foreach($qData as $val){
			if($val['brandtype']==0) $competitorarr[(string)$val['code']] =(string)$val['code'];
			if($val['brandtype']==1) $pmiarr[(string)$val['code']] =(string)$val['code'];
		}
		
		if(intval($this->apps->_request('start'))!=0) $start = intval($this->apps->_request('start'));
		if($streid){			
			$qEntourage = " AND id IN ({$streid}) ";
			$limit = 50;
		}else{
			$qEntourage = "";
		}
		if($all){
			$qLimit = "";
		}else $qLimit = " LIMIT {$start},{$limit} ";
		
		$filter = strip_tags($this->apps->_p('filter'));
		$cityid = strip_tags($this->apps->_p('cityid'));
		$brandid = strip_tags($this->apps->_p('brandid'));
		$totalengagement = intval($this->apps->_p('totalengagement'));
		
		$qFilter ="";
		if($filter=="pending") $qFilter = " AND n_status = 0 ";
		if($filter=="accept") $qFilter = " AND n_status = 1 ";
		if($filter=="reject") $qFilter = " AND n_status = 2 ";
		if($filter=="engagement") 	{
			if($totalengagement==0) $qFilter = " AND stat.total<>{$totalengagement} ";
			else  $qFilter = " AND stat.total ={$totalengagement} ";
		}
		
		$qCityid = "";
		if($cityid) $qCityid = " AND entou.city='{$cityid}' ";
		
		
		$qBrandid = "";
		if($brandid) $qBrandid = " AND entou.Brand1_ID='{$brandid}' ";
		
		$qDate = "";
		$startdate = strip_tags($this->apps->_p('startdate'));
		$enddate = strip_tags($this->apps->_p('enddate'));
		
		if(!$enddate) if($startdate)  $enddate = date("Y-m-d",strtotime($startdate. "+7 day"));
		
		if($startdate&&$enddate){
			$qDate = " AND DATE(entou.register_date) >= DATE('{$startdate}') AND DATE(entou.register_date) <= DATE('{$enddate}') ";
		}
		$data['result'] = false;
		$data['total'] = 0;
		if($this->apps->user->leaderdetail->type!=1) {
							$uid = intval($this->apps->_request('uid'));
							$auhtorarrid = false;
							if($uid==0)	{
								$uid = $this->uid;								
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
							}
							if(is_array($auhtorarrid)) 	{
								// pr($minionarr);
								$authorids = implode(',',$auhtorarrid);
							}else $authorids = $uid;
							
					 $qUserid = " AND entou.referrerbybrand IN ({$authorids}) ";		
		}else $qUserid = " AND  entou.referrerbybrand ={$this->uid} ";
		
		$sql = "	
		SELECT COUNT(*) total FROM my_entourage entou 
		LEFT JOIN ( SELECT count(*) total,friendid
		FROM {$this->dbshema}_news_content_tags
		WHERE   n_status=1 AND friendtype = 0 GROUP BY friendid ) stat ON stat.friendid= entou.id
		WHERE  1 {$qUserid} {$qEntourage} {$qFilter} {$qDate} {$qCityid} {$qBrandid} ";	
		$total = $this->apps->fetch($sql);		
		// pr($total);
		if(!$total)return false;
		$sql = "
		SELECT entou.*, IF(stat.total IS NULL,0,stat.total) total FROM my_entourage entou 
		LEFT JOIN ( SELECT count(*) total,id,friendid
		FROM {$this->dbshema}_news_content_tags
		WHERE   n_status=1 AND friendtype = 0 GROUP BY friendid ) stat ON stat.friendid= entou.id 
		WHERE 1 {$qUserid} {$qEntourage} {$qFilter} {$qDate} {$qCityid} {$qBrandid} ORDER BY entou.register_date DESC  {$qLimit} ";		
		
		$qData = $this->apps->fetch($sql,1);
		// pr($sql);
		$this->logger->log($sql);
		// pr();

		
		if($qData) {
		
			$arrentourage = false;
			$strentourage = false;
			$entouragedata = false;
			foreach($qData as $val){
				$arrentourage[$val['id']] = $val['id'];
			}
			if($arrentourage){
				$strentourage = implode(',',$arrentourage);
				$entouragedata = $this->entouragestatistic($strentourage);
			}
			if($arrentourage){
				$strentourage = implode(',',$arrentourage);
				$latestengagement = $this->getlatestengagement($strentourage);
			}			
			foreach($qData as $key => $val){
			
					if(is_file($CONFIG['LOCAL_PUBLIC_ASSET']."entourage/photo/small_".$val['img'])) {
						$qData[$key]['image_full_path']= $CONFIG['BASE_DOMAIN_PATH']."public_assets/entourage/photo/small_".$val['img'];
					}else  $qData[$key]['image_full_path'] =  $CONFIG['BASE_DOMAIN_PATH']."public_assets/entourage/photo/default.jpg";
					
					$qData[$key]['entouragetype'] = "Our";
					if(in_array($val['Brand1_ID'],$competitorarr)) $qData[$key]['entouragetype'] = "Competitor";				
					if(in_array($val['Brand1_ID'],$pmiarr)) $qData[$key]['entouragetype'] = "PMI";
					
					if($latestengagement){					
						if(array_key_exists($val['id'],$latestengagement))  $qData[$key]['latestengagament'] = $latestengagement[$val['id']];
						else  $qData[$key]['latestengagament'] = false;
					}else  $qData[$key]['latestengagament'] = false;
					if($entouragedata){
						if(array_key_exists($val['id'],$entouragedata))  $qData[$key]['entouragedata']= $entouragedata[$val['id']];
						else  $qData[$key]['entouragedata']= false;
					}else  $qData[$key]['entouragedata']= false;
					
				
			}
			$data['result'] = $qData;
			
			$data['total'] = $total['total'];
		}
		// pr($data);exit;
		
		return $data;
		// return $list;
		
		
	}
	
	function getlatestengagement($strentourage=false){
		if($strentourage==false) return false;
		global $CONFIG;
		//get enggement of entourage
			$sql ="SELECT *
			FROM {$this->dbshema}_news_content_tags
			WHERE friendid IN ({$strentourage})   AND n_status=1 AND friendtype = 0
			GROUP BY friendid , userid ORDER BY date DESC
			";	
			// pr($sql);exit;
			$qData = $this->apps->fetch($sql,1);
			$arrfid = false;
			if(!$qData) return false;
			foreach($qData as $key => $val){
					
				$contentid[$val['contentid']] = $val['contentid'];				
			}
				$contentarr = false;
			if($contentid){
		
				$strcid = implode(',',$contentid);
				$sql="
				SELECT anc.id,anc.title,anc.brief,anc.image,anc.posted_date,tpages.name pagetypes
				FROM {$this->dbshema}_news_content anc			
				LEFT JOIN my_pages pages ON anc.authorid=pages.ownerid		
				LEFT JOIN my_pages_type tpages ON tpages.id=pages.type 				
				WHERE anc.id IN ({$strcid})   ";
					// pr($sql);
				$rqData = $this->apps->fetch($sql,1);
				foreach($rqData as $key => $val){
					$rqData[$key]['engagementtype'] = "Personal";
					if($val['pagetypes']=='SBA') $rqData[$key]['engagementtype'] = "Personal";
					if($val['pagetypes']=='PL') $rqData[$key]['engagementtype'] = "Co-Creation";
					if($val['pagetypes']=='Brand') $rqData[$key]['engagementtype'] = "BRAND";
					if($val['pagetypes']=='121') $rqData[$key]['engagementtype'] = "Co-Creation";
					if($val['pagetypes']=='IS') $rqData[$key]['engagementtype'] = "BRAND";
					
					if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}article/{$val['image']}"))   $rqData[$key]['image_full_path'] = $CONFIG['BASE_DOMAIN_PATH']."public_assets/article/".$val['image'];
					else $rqData[$key]['image_full_path'] = $CONFIG['BASE_DOMAIN_PATH']."public_assets/article/default.jpg";	
					
					$contentarr[$val['id']] = $rqData[$key];				
				}
			}
			if(!$qData) return false;
			foreach($qData as $key => $val){						
				$qData[$key]['engagementtype'] = "Personal";
				if($contentarr){			
				
						if(array_key_exists($val['contentid'],$contentarr))  {
							$qData[$key]['contentdetail'] = $contentarr[$val['contentid']];
							$qData[$key]['engagementtype'] =  $contentarr[$val['contentid']]['engagementtype'];
						}else  $qData[$key]['contentdetail'] = false;
				}	
				
				$arrfid[$val['friendid']] = $qData[$key];	
			}
			// pr($arrfid);exit;
			return $arrfid;
	}
	
	function entourageDetail(){
		global $CONFIG;
		$id=$this->apps->_g('id');
		
		$sql = "SELECT * FROM my_entourage WHERE id={$id} LIMIT 1 ";		
		$qData = $this->apps->fetch($sql);
		
		// pr($qData);
		
		if($qData)	{
			if(is_file( $CONFIG['LOCAL_PUBLIC_ASSET']."entourage/photo/".$qData['img'])) $qData['image_full_path']= $CONFIG['BASE_DOMAIN_PATH']."public_assets/entourage/photo/".$qData['img'];
			else $qData['image_full_path']=  $CONFIG['BASE_DOMAIN_PATH']."public_assets/entourage/photo/default.jpg";
			
			$qData['entouragedata'] = $this->entouragestatistic($qData['id']);
			
			 
		}
		// pr($qData);
		
		if($qData) 	return $qData;
		return false;
		
		
	}
	
	function entourageProfile(){
		global $CONFIG;
		$id=$this->apps->_request('id');
		$uid = intval($this->uid);
		
		$sql = "
		SELECT  entou.*, IF(stat.total IS NULL,0,stat.total) total 
		FROM my_entourage entou 
		LEFT JOIN ( SELECT count(*) total,id,friendid
		FROM {$this->dbshema}_news_content_tags
		WHERE n_status=1 AND friendtype = 0 GROUP BY friendid ) stat ON stat.friendid= entou.id 
		WHERE  entou.id={$id} LIMIT 1 ";		
		$qData = $this->apps->fetch($sql);
		
		// pr($qData);
		
		if($qData)	{
			if(is_file( $CONFIG['LOCAL_PUBLIC_ASSET']."entourage/photo/small_".$qData['img'])) $qData['image_full_path']= $CONFIG['BASE_DOMAIN_PATH']."public_assets/entourage/photo/small_".$qData['img'];
			else $qData['image_full_path']=  $CONFIG['BASE_DOMAIN_PATH']."public_assets/entourage/photo/default.jpg";
			
			$qData['entouragedata'] = $this->entouragestatistic($qData['id']);
			$qData['latestengagament'] = $this->getlatestengagement($qData['id']);
			 
		}
		// pr($qData);
		
		if($qData) 	return $qData;
		return false;
		
		
	}
	
	function addEntourage($img=false){
		
		if($this->apps->user->leaderdetail->type!=1) return false;
		
		$this->logger->log(" update or register phase 1 : ");
		$firstname=$this->apps->_request("name");
		$lastname=$this->apps->_request("lastname");
		$nickname=$this->apps->_request("nickname");
		$email=$this->apps->_request("email");
	
		$city=intval($this->apps->_request("city"));
		$state=$this->apps->_request("state");
		$giidnumber=$this->apps->_request("giidnumber");
		$giidtype=$this->apps->_request("giidtype");
		$companymobile=$this->apps->_request("companymobile");
		$phone_number=$this->apps->_request("phone_number");
		$sex=$this->apps->_request("sex");
		$birthday=$this->apps->_request("birthday");
		$description=$this->apps->_request("description");
		$StreetName=$this->apps->_request("StreetName");
		if($companymobile!='ST1')$phone_number=$companymobile;		
	
		$brand1=$this->apps->_request("Brand1_ID");
		if($brand1=='') {
				$brand1 = "0004";
				$brand1ref = "F/M/Z";
		}
		// $brand1 = "0004";
		$brandsub1=$this->apps->_request("Brand1SUB_ID");
		if($brandsub1==''){
			$brandsub1 = "0004"; 
			$brandsub1ref = "F/M/Z"; 			
		}
		
		$socialaccount=$this->apps->_request("socialaccount");
		$socialaccount_sub=$this->apps->_request("socialaccount_sub");
		if($birthday!=''){
			$this->logger->log($birthday);
			$birthdayarr = explode('/',$birthday);
			if(is_array($birthdayarr)&&(count($birthdayarr)==3)){
				$weekslen = strlen($birthdayarr[1]);
				
				if($weekslen<2) $birthdayarr[1] = "0".$birthdayarr[1];
				$datelen = strlen($birthdayarr[0]);
				if($datelen<2) $birthdayarr[0] = "0".$birthdayarr[0];
				
				$newbirth= "{$birthdayarr[2]}-{$birthdayarr[0]}-{$birthdayarr[1]}";
				if($newbirth!='')$birthday = $newbirth;
			}
		
		}
		
		
		$updatedatavalidation=false;
		
		if($phone_number!='') $updatedatavalidation[] = "phone_number='{$phone_number}'";
		if($sex!='') $updatedatavalidation[] = "sex='{$sex}'";
		if($birthday!='') $updatedatavalidation[] = "birthday='{$birthday}'";
		if($brand1!='') $updatedatavalidation[] = "Brand1_ID='{$brand1}'";
		if($brandsub1!='') $updatedatavalidation[] = "Brand1U_ID='{$brandsub1}'";
		if($giidnumber!='') $updatedatavalidation[] = "giidnumber='{$giidnumber}'";
		if($giidtype!='') $updatedatavalidation[] = "giidtype='{$giidtype}'";
		if($socialaccount!='') $updatedatavalidation[] = "socialaccount='{$socialaccount}'";
		if($firstname!='') $updatedatavalidation[] = "name='{$firstname}'";
		if($nickname!='') $updatedatavalidation[] = "nickname='{$nickname}'";
		if($lastname!='') $updatedatavalidation[] = "last_name='{$lastname}'";
		if($img) $updatedatavalidation[] = "img='{$img}'";
		
		
		if($updatedatavalidation){
			$qInsertOnUpdateVerified = implode(',',$updatedatavalidation);
		}

		$usertype=intval(@$this->apps->session->getSession($this->config['SESSION_NAME'],'USERTYPE')->users);
		
		$referrerbybrand = intval($this->uid); /* use on segment 8  */
		if($referrerbybrand==0) return false;
		if(!$email) return false;
		if(!$giidnumber) return false;
		
		$confirm18=1;
		$receiveinfo=1;
		$n_status=0;
		$verified = 1;
		// pr($img);exit;
		$sql ="
		INSERT INTO my_entourage 
		(registerid ,name ,	nickname ,	email ,	register_date ,	img ,	small_img ,	city 	,sex ,	birthday ,	description, 	last_name ,	StreetName, 	phone_number ,	n_status ,	Brand1_ID ,	referrerbybrand ,verified, usertype,giidnumber,giidtype,socialaccount,socialaccount_sub,Brand1U_ID) 
		VALUES
		('',\"{$firstname}\",\"{$nickname}\",\"{$email}\",NOW(),\"{$img}\",\"{$img}\",\"{$city}\",\"{$sex}\",\"{$birthday}\",\"{$description}\",\"{$lastname}\",\"{$StreetName}\",\"{$phone_number}\",{$n_status},\"{$brand1}\",{$referrerbybrand},{$verified},{$usertype},\"{$giidnumber}\",\"{$giidtype}\",\"{$socialaccount}\",\"{$socialaccount_sub}\",\"{$brandsub1}\")	
		ON DUPLICATE KEY UPDATE {$qInsertOnUpdateVerified}
		";
		
		// pr($sql);exit;
		$this->logger->log($sql);
		// $this->logger->log($qInsertOnUpdateVerified);
		$qData = $this->apps->query($sql);
		$data['result'] = false;
		$entourageid = false;
		if($this->apps->getLastInsertId())	$entourageid = $this->apps->getLastInsertId();
		else{
			$sql =" SELECT * FROM my_entourage WHERE email=\"{$email}\" LIMIT 1";
			// $this->logger->log($sql);
			$entorourage = $this->apps->fetch($sql);
			
			if($entorourage) $entourageid = $entorourage['id'];
			else return false;
		}
				
		$data['result'] = true;
		$data['savedb'] = true;
		$data['savefriends'] = false;
		$data['savemop'] = false;
		
	
			$sql = "
			INSERT INTO my_circle (friendid,userid,ftype,groupid,date_time,n_status)
			VALUES ('{$entourageid}','{$referrerbybrand}',0,0,NOW(),1)
			ON DUPLICATE KEY UPDATE n_status=1
			";
		
			$this->apps->query($sql);
			// $this->logger->log($sql);
			if($this->apps->getLastInsertId()) 	$data['savefriends'] = true;
			
		
			$mop = $this->apps->deviceMopHelper->syncAdminUserRegistrant("AdminRegisterProfileDeDuplication");
			// $mop['result'] = 0;
			// pr($mop);
			$this->logger->log(json_encode($mop));
			if($mop['result']==1) {
				$sql = "UPDATE my_entourage SET registerid='{$mop['data'][0]['RegistrationID']}',n_status=1 WHERE id={$entourageid} LIMIT 1 ";
				$qData = $this->apps->query($sql);
				if($qData) $data['savemop'] = true;
			
			}
			// pr($data);
			return $data;	
		
				
	}
	
	function synchenturage(){
		
			$this->logger->log("sync entourage : ");
		$sql = "SELECT * FROM my_entourage WHERE n_status = 0 ORDER BY register_date DESC LIMIT 1 ";
		$val = $this->apps->fetch($sql);
		
		if(!$val) 
		{
		pr('no data');
		exit;
		}
		
		// pr($val);exit;
		
			$this->apps->Request->setParamPost("name",$val['name']);
			$this->apps->Request->setParamPost("lastname",$val['last_name']);
			$this->apps->Request->setParamPost("nickname",$val['nickname']);
			$this->apps->Request->setParamPost("email",$val['email']);
			
			
			$sql = "SELECT * FROM beat_city_reference WHERE cityidmop='{$val['city']}' LIMIT 1";
			$city = $this->apps->fetch($sql);		
			
			$this->apps->Request->setParamPost("state",$city['provinceid']);
			$this->apps->Request->setParamPost("city",$city['id']);
			$this->apps->Request->setParamPost("giidnumber",$val['giidnumber']);
			$this->apps->Request->setParamPost("giidtype",$val['giidtype']);
			$this->apps->Request->setParamPost("sex",$val['sex']);
			$this->apps->Request->setParamPost("birthday",$val['birthday']);
			$this->apps->Request->setParamPost("phone_number",$val['phone_number']);
			$this->apps->Request->setParamPost("Brand1_ID",$val['Brand1_ID']);
			$this->apps->Request->setParamPost("Brand1SUB_ID",$val['Brand1U_ID']);
			$this->apps->Request->setParamPost("companymobile","ST1");
						
			// pr($this->apps->_request("giidtype"));exit;
				
			$mop = $this->apps->deviceMopHelper->syncAdminUserRegistrant("AdminRegisterProfileDeDuplication");
			
			// pr($mop);exit;
			if($mop['result']==1) {
				$sql = "UPDATE my_entourage SET registerid='{$mop['data'][0]['RegistrationID']}',n_status=1 WHERE id={$val['id']} LIMIT 1 ";
				$qData = $this->apps->query($sql);
				if($qData) $data['savemop'] = true;		
			}else{
				$sql = "UPDATE my_entourage SET n_status=2 WHERE id={$val['id']} LIMIT 1 ";
				$qData = $this->apps->query($sql);
			}
			
		
		pr($mop);exit;
	}
	
	
	function getSearchEntourage(){
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
	
	
	function entouragestatistic($strentourage=null){
	
		// pr($this->apps->user);exit;
			if($strentourage==null) return false;
			global $CONFIG;
			
			//get enggement of entourage
			$sql ="SELECT *
			FROM {$this->dbshema}_news_content_tags
			WHERE friendid IN ({$strentourage})  AND n_status=1 AND friendtype = 0 GROUP BY friendid,friendtype
			
			";	
			$rqData = $this->apps->fetch($sql,1);
			$strcid = false;
			// pr($rqData);
			if(!$rqData) return false;
				$arrfid = false;
			foreach($rqData as $val){
				$arrcid[$val['contentid']] = $val['contentid'];
			}
			if($arrcid) $strcid = implode(',',$arrcid);
			
			//get contentid detail
			$sql="SELECT id,title,brief,image,thumbnail_image,slider_image,posted_date,file,url,fromwho,tags,authorid,topcontent,cityid ,articleType,can_save
			FROM {$this->dbshema}_news_content anc
			WHERE id IN ({$strcid}) ";
			// pr($sql);
			$qData = $this->apps->fetch($sql,1);
			if(!$qData) return false;
			
			foreach($qData as $key => $val){
				$qData[$key]['imagepath'] = false;
				if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}event/{$val['image']}")) 	$qData[$key]['imagepath'] = "event";
				if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}banner/{$val['image']}")) 	$qData[$key]['imagepath'] = "banner";
				if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}article/{$val['image']}"))  	$qData[$key]['imagepath'] = "article";					
				
				if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}article/{$val['image']}")) 	$qData[$key]['banner'] = false;
				else $qData[$key]['banner'] = true;
								
				//CHECK FILE SMALL
				if(is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}{$qData[$key]['imagepath']}/small_{$val['image']}")) $qData[$key]['image'] = "small_{$val['image']}";
				
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
					$qData[$key]['video_thumbnail'] = $video_thumbnail;
				}else $qData[$key]['video_thumbnail'] = false;
				
				if($qData[$key]['imagepath']) $qData[$key]['image_full_path'] = $CONFIG['BASE_DOMAIN_PATH']."public_assets/".$qData[$key]['imagepath']."/".$qData[$key]['image'];
				else $qData[$key]['image_full_path'] = $CONFIG['BASE_DOMAIN_PATH']."public_assets/article/default.jpg";
				$contentdata[$val['id']] =  $qData[$key];
				
			}
			
			
			foreach($rqData as $key => $val){
				$arrfid[$val['friendid']][$key] = $val;
				if(array_key_exists($val['contentid'],$contentdata)) $arrfid[$val['friendid']][$key]['contentdetail'] = $contentdata[$val['contentid']];
				else  $arrfid[$val['friendid']][$key]['contentdetail']  = false;
			}
			if($arrfid) return $arrfid;
			
			return false;
	
			
		// i need check how many entourage of this BA
		// check how many times the entourage has engagement
	}
	
	function checkentourage(){
		global $CONFIG;
		$email= $this->apps->_request('email');
		$giid= $this->apps->_request('giidnumber');
		$filter = false;
		
		if($email) $filter[] = " email =\"{$email}\" ";
		if($giid) $filter[] = " giidnumber = \"{$giid}\" ";
		
		if($filter) $qFilter =	implode(" AND ",$filter);
		else $qFilter="";
		
		if($qFilter=="") return false;
		
		$sql = "SELECT * FROM my_entourage WHERE {$qFilter} LIMIT 1 ";		
				// pr($sql);
		$qData = $this->apps->fetch($sql);
		if($qData)	{
			if(is_file( $CONFIG['LOCAL_PUBLIC_ASSET']."entourage/photo/small_".$qData['img'])) $qData['image_full_path']= $CONFIG['BASE_DOMAIN_PATH']."public_assets/entourage/photo/small_".$qData['img'];
			else $qData['image_full_path']=  $CONFIG['BASE_DOMAIN_PATH']."public_assets/entourage/photo/default.jpg";
			
			$qData['entouragedata'] = $this->entouragestatistic($qData['id']);
		}
		// pr($qData);
		
		if($qData) 	return array('result'=>true,'data'=>$qData);
		return array('result'=>false,'data'=>false);
	}
	
	function getAge($birthDate){
		
         $birthDate = explode("-", $birthDate);
         $age = (date("md", date("U", mktime(0, 0, 0, $birthDate[2], $birthDate[1], $birthDate[0]))) > date("md") ? ((date("Y")-$birthDate[0])-1):(date("Y")-$birthDate[0]));
        return $age;
	}
	
	
	
	
	
}

?>

