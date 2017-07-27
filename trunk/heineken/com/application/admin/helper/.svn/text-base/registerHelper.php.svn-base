<?php
class registerHelper {
	
	var $_mainLayout="";
	
	var $login = false;
	
	function __construct($apps=false){
		global $logger,$CONFIG;
		$this->logger = $logger;
		$this->apps = $apps;
		
		$this->config = $CONFIG;
		
	
	}
	
	
	function registerPhase(){
		$ok = false;
		global $CONFIG;
		
		if($this->apps->_p('register')==1){
		
			$this->logger->log('can register');
			$reg = $this->doRegister();
		
			return $reg;
			
			
			
		}
		$this->logger->log('can not register');
		return false;
	}
	
	function doRegister(){
		global $CONFIG;
		$this->logger->log('do register');
		
		
		 $notsaved = "not save";
		 $saved = "saved";
		// user stat
		$name = $this->apps->_p('name');
		$last_name = $this->apps->_p('lastname');
		$nickname = $this->apps->_p('nickname');
		$username = $this->apps->_p('username');
		$password = trim($this->apps->_p('password'));
		// $repassword = trim($this->apps->_p('repassword'));
		$nickname = $this->apps->_p('name');
		$email = trim($this->apps->_p('email'));
		
		//page stat
		// 	ownerid 	name 	description 	type 	img 	otherid	brandid 	brandsubid 	areaid 	city 	created_date 	closed_date 	n_statu
		$role = explode("_",$this->apps->_p('role'));
		$ownerid = $this->apps->_p('ownerid');
		$rolesname = $role[0].' '.$this->apps->_p('area');
		$description = $this->apps->_p('description');
		$type =$role[1];
		$img = $this->apps->_p('img');
		$otherid = $this->apps->_p('otherid');
		$brandid = $this->apps->_p('brandid');
		$brandsubid = $this->apps->_p('brandsubid');
		$areaid = $this->apps->_p('areaid');
		$city = $this->apps->_p('city');
		$created_date = date("Y-m-d H:i:s");
		$closed_date = date("Y-m-d H:i:s");
		$n_status = 1;
		
		if($name==''||$name==null){
			$this->logger->log('name is null');
			return  $notsaved;
		}
		// if($password!=$repassword) {
			// $this->logger->log('pass and re pass not match');
			// return false;
		// }
			
		$hashPass = sha1($password.$CONFIG['salt']);
		$sql = "SELECT * FROM social_member WHERE email='{$email}' LIMIT 1 ";
		$qData = $this->apps->fetch($sql);
	
		if($qData){
			$sql = "
				UPDATE social_member SET name='{$name}',nickname='{$nickname}',last_name='{$last_name}' , password='{$hashPass}', n_status = 1,try_to_login=0
					WHERE id = {$qData['id']} LIMIT 1				
			";
			// pr($sql);
			$this->apps->query($sql);
			$sql = "SELECT ownerid FROM my_pages WHERE ownerid={$qData['id']} LIMIT 1 ";
			$rqData = $this->apps->fetch($sql);
			
			if($rqData){
				$dataupdate = false;
				if($rolesname!='') $dataupdate[] = "name='{$rolesname}'";
				if($type!='') $dataupdate[] = "type='{$type}'";
				if($img!='') $dataupdate[] = "img='{$img}'";
				if($otherid!='') $dataupdate[] = "otherid='{$otherid}'";
				if($brandid!='') $dataupdate[] = "brandid='{$brandid}'";
				if($brandsubid!='') $dataupdate[] = "brandsubid='{$brandsubid}'";
				if($areaid!='') $dataupdate[] = "areaid='{$areaid}'";
				if($city!='') $dataupdate[] = "city='{$city}'";
				$qUpdateData = "";
				if($dataupdate){
					$qUpdateData = implode(',',$dataupdate);
				}else return $saved;
				
				$sql = "
						UPDATE my_pages SET 
						{$qUpdateData} 
						WHERE ownerid = {$qData['id']} LIMIT 1				
				";
				$this->logger->log($sql);
				$this->apps->query($sql);
				$this->createrolefriends($qData['id'],$otherid,$brandid,$brandsubid,$areaid);
				return $saved;
			}else{
				$sql = "
					INSERT INTO my_pages (ownerid ,	name, 	description ,	type 	,img ,	otherid,	brandid 	,brandsubid ,	areaid ,	city 	,created_date ,	closed_date,n_status) 
					VALUES ('{$qData['id']}','{$rolesname}','',{$type},'{$img}',{$otherid},{$brandid},{$brandsubid},{$areaid},'{$city}',NOW(),DATE_ADD(NOW(),INTERVAL 5 YEAR),1)
				";
				// pr($sql);
				 $this->apps->query($sql);
					if($this->apps->getLastInsertId()>0)  {
							$this->createrolefriends($qData['id'],$otherid,$brandid,$brandsubid,$areaid);
						return $saved;
					}
			
			}
		}else{
			$sql = "
				INSERT INTO social_member (password,email,register_date,salt,n_status,name,nickname,username,last_name) 
				VALUES ('{$hashPass}','{$email}',NOW(),'{$CONFIG['salt']}',1,'{$name}','{$nickname}','{$username}','{$last_name}')			
			";
			$this->apps->query($sql);
			$lastID = $this->apps->getLastInsertId();
			if($lastID>0) {
				$sql = "
					INSERT INTO my_pages (ownerid ,	name, 	description ,	type 	,img ,	otherid,	brandid 	,brandsubid ,	areaid ,	city 	,created_date ,	closed_date,n_status) 
					VALUES ('{$lastID}','{$rolesname}','',{$type},'{$img}',{$otherid},{$brandid},{$brandsubid},{$areaid},'{$city}',NOW(),DATE_ADD(NOW(),INTERVAL 5 YEAR),1)
				";
				// pr($sql);exit;
				 $this->apps->query($sql);
					if($this->apps->getLastInsertId()>0)  {
						$this->createrolefriends($lastID,$otherid,$brandid,$brandsubid,$areaid);
						return  $saved;
					}
			}		
		}
 		return  $notsaved;
	
	}
	

	function getLeader($type=2){
		
		$sql  = "
			SELECT sm.id,pages.name pagename,sm.name, sm.last_name 
			FROM social_member sm
			LEFT JOIN my_pages pages ON pages.ownerid = sm.id
			WHERE type={$type} ";
		
		$qData = $this->apps->fetch($sql,1);
		
		return $qData;
	}
	
	
	function userlists($limit=100){
		//create user list per hirarki
		$uid = intval($this->apps->_request('uid'));
		if($uid!=0) $qUsers = " AND sm.id = {$uid} ";
		else  $qUsers = "";
		$start = 0;
		$sql  = "
			SELECT 
			sm.id,sm.name, sm.last_name ,sm.email,sm.username,sm.password,sm.birthday,sm.phone_number,sm.nickname,
			ptype.name pagename,ptype.id pagetype
			FROM social_member sm
			LEFT JOIN my_pages pages ON pages.ownerid = sm.id
			LEFT JOIN my_pages_type ptype ON ptype.id = pages.type
			 WHERE sm.n_status NOT IN (3)
			{$qUsers}
			ORDER BY register_date DESC
			LIMIT {$start},{$limit} ";
		// pr($sql);
		if($uid!=0) $qData = $this->apps->fetch($sql);
		else $qData = $this->apps->fetch($sql,1);
		return $qData;
	}
	
	function unusers(){
		//create user list per hirarki
		$uid = intval($this->apps->_request('uid'));
		$sql = "
				UPDATE social_member SET n_status = 3
				WHERE id = {$uid} LIMIT 1				
			";
			
		$this->apps->query($sql);
		
		return true;
	}
	
	function createrolefriends($uid=false,$otherid=false,$brandid=false,$brandsubid=false,$areaid=false){
		if($uid == false) return false;
		
		$qSelect = false;
		if($otherid != false) $qSelect[] = " otherid={$otherid} ";
		if($brandid != false) $qSelect[] = " brandid={$brandid} ";
		if($brandsubid != false) $qSelect[] = " brandsubid={$brandsubid} ";
		if($areaid != false) $qSelect[] = " areaid={$areaid} ";
	
		if(!$qSelect) return false;
		$strQSelect  = implode(' OR ',$qSelect);
		$sql = " 
			SELECT ownerid 
			FROM my_pages
			WHERE 1
			AND ( {$strQSelect} )
			";
		
		$qData = $this->apps->fetch($sql,1);
		$friendid = false;
		if(!$qData) return false;
			$this->logger->log(json_encode($sql));
		foreach($qData as $val){
			$friendid[$val['ownerid']] = $val['ownerid'];
		}
		
		if(!$friendid)return false;
		$qCirlce = false;
		$this->logger->log(json_encode($friendid));
		foreach($friendid as $val){
			if($val!=false){
				$qCirlce[]= " ( {$uid},{$val},1,0,NOW(),1 ) ";
				$qCirlce[]= " ( {$val},{$uid},1,0,NOW(),1 ) ";		
			}
		}
		if(!$qCirlce) return false;
		$strQCircle = implode(',',$qCirlce);
		$sql = " 
		INSERT IGNORE INTO my_circle ( userid ,	friendid ,	ftype, 	groupid ,	date_time ,	n_status ) 
		VALUES {$strQCircle}
		";
		$this->logger->log($sql);
		// exit;
		$this->apps->query($sql);
		
		
	}
	
}
