<?php

class messageHelper {
	
	function __construct($apps){
		global $logger,$CONFIG;
		$this->logger = $logger;
		$this->apps = $apps;
		if($this->apps->isUserOnline()) {
			if(is_object($this->apps->user)) $this->uid = intval($this->apps->user->id);
		}
		else $this->uid = 0;
		$this->dbshema = "athreesix";
	}	
	
	function getCount(){
	$sql =  "
			SELECT COUNT( * ) hitung
			FROM my_message msg 
			WHERE id = {$this->uid} AND n_status = 1 ";

	$qData = $this->apps->fetch($sql);
	return $qData;
	}
	
	function getMessage($start=0,$limit=10,$notification=true){
		$result['result'] = false;
		$result['total'] = 0;
		
		if($start==0) $start = intval($this->apps->_p('start'));
		if(!$notification) $qnotif = "AND msg.message NOT LIKE '%tag on%' ";
		else $qnotif = "";
		$search = "";
		$startdate = "";
		$enddate = "";
		if ($this->apps->_p('search')) {
			if ($this->apps->_p('search')!="Search...") {
				$search = rtrim($this->apps->_p('search'));
				$search = ltrim($search);
				$realsearch = $search;
				if(strpos($search,' ')) $parseSearch = explode(' ', $search);
				else $parseSearch = false;
				
				if(is_array($parseSearch)) $search = $search.'|'.trim(implode('|',$parseSearch));
				else  $search = trim($search);
				$search = "AND (
							msg.message REGEXP  '{$search}' 
							OR smsend.name LIKE '{$realsearch}%' 
							OR smsend.last_name LIKE '{$realsearch}%' 
							OR smreci.name LIKE '{$realsearch}%' 
							OR smreci.last_name LIKE '{$realsearch}%' 
							
							) ";
			}
		}
		if ($this->apps->_p('startdate')) {
			$start_date = $this->apps->_p('startdate');
			$startdate = "AND DATE(msg.datetime) >= DATE('{$start_date}') ";
		}
		if ($this->apps->_p('enddate')) {
			$end_date = $this->apps->_p('enddate');
			$enddate = "AND DATE(msg.datetime) <= DATE('{$end_date}') ";
		}
		
		$sql_total = "
			select count(a.id) total
			FROM (
				SELECT msg.*
				FROM my_message msg	
				LEFT JOIN social_member smsend	ON smsend.id = msg.fromid
				LEFT JOIN social_member smreci	ON smreci.id = msg.recipientid
				WHERE msg.recipientid ={$this->uid}  {$search} {$startdate} {$enddate} {$qnotif} AND msg.n_status IN (1,2)  
				AND smsend.n_status = 1
				AND smreci.n_status = 1
				GROUP BY msg.parentid ORDER BY msg.datetime DESC
			) a
		";
		$total = $this->apps->fetch($sql_total);
		
		if(intval($total['total'])<=$limit) $start = 0;
		
		$sql =  "
			SELECT msg.* , IF(msg.message LIKE '%been tag on%','notification','direct' ) messagetype
			FROM my_message msg	
			LEFT JOIN social_member smsend	ON smsend.id = msg.fromid
			LEFT JOIN social_member smreci	ON smreci.id = msg.recipientid
			WHERE   msg.recipientid ={$this->uid}  {$search} {$startdate} {$enddate}  {$qnotif}
			AND msg.n_status IN (1,2) 
			AND smsend.n_status = 1
			AND smreci.n_status = 1
			GROUP BY msg.parentid ORDER BY msg.datetime DESC LIMIT {$start},{$limit} 
		";
		// pr($sql);
		$qData = $this->apps->fetch($sql,1);
		
		if($qData) {
			$sdata = false;
			$rdata = false;
			$socialData = false;
			$rsocialData = false;
			foreach($qData as $key => $val){
					if($val['messagetype']!='direct') $qData[$key]['message']  =  strtoupper(substr($val['message'],1,1)).strtolower(substr($val['message'],2));
					$qData[$key]['totalreply']  = 0;
					$qData[$key]['totalreply']  = false;
				$sdata[$val['fromid']] = $val['fromid'];
				$rdata[$val['recipientid']] = $val['recipientid'];
				 $reply = $this->replymessage(0,100,$val['parentid'],false);
				if($reply){
					$qData[$key]['totalreply'] = count($reply);
					$qData[$key]['lastreply'] = max($reply);
				}
				
			}
			if($sdata){
				$strsdata = implode(',',$sdata);
				$socialData= $this->getSocialData($strsdata);
				
			}
			if($rdata){
				$strsdata = implode(',',$rdata);
				$rsocialData= $this->getSocialData($strsdata);
				
			}
			if($socialData){
				foreach($qData as $key => $val){
					if(array_key_exists($val['fromid'],$socialData)) $qData[$key]['userdetail'] = $socialData[$val['fromid']];
					else  $qData[$key]['userdetail'] = false;
				}
			
			}
			if($rsocialData){
				foreach($qData as $key => $val){
					if(array_key_exists($val['recipientid'],$rsocialData)) $qData[$key]['recepientdetail'] = $rsocialData[$val['recipientid']];
					else  $qData[$key]['recepientdetail'] = false;
				}
			
			}
			
			// pr($qData);
			$result['result'] = $qData;
			$result['total'] = intval($total['total']);
			$totalnotif = $this->getCount();
			$result['totalnotif'] = intval($totalnotif['hitung']);
			return $result;
		}
		return false;
	}
	
	function readmessage(){
		
		$id = intval($this->apps->_request('id'));
		if($id==0) return false;
			
		$sql =  "
		SELECT * , IF(msg.message LIKE '%been tag on%','notification','direct' ) messagetype
		FROM my_message msg	
		WHERE id={$id} AND n_status IN (1,2) 
		ORDER BY datetime DESC LIMIT 1";
		$qData = $this->apps->fetch($sql,1);
		$data = false;
		if($qData) {
	
			$sdata = false;
			$socialData = false;
			foreach($qData as $val){
				$sdata[$val['fromid']] = $val['fromid'];
				$messageid = $val['id'];
			}
			
			$sql ="	UPDATE my_message set n_status=2
					WHERE
					id={$messageid} AND recipientid={$this->uid} LIMIT 1	
					";
			// pr($sql);
			$this->apps->query($sql);
			
			if($sdata){
				$strsdata = implode(',',$sdata);
				$socialData= $this->getSocialData($strsdata);
			}
			
			if($socialData){
				foreach($qData as $key => $val){
					if($val['messagetype']!='direct')  $qData[$key]['message']  =  strtoupper(substr($val['message'],1,1)).strtolower(substr($val['message'],2));
					if(array_key_exists($val['fromid'],$socialData)) $qData[$key]['userdetail'] = $socialData[$val['fromid']];
					else  $qData[$key]['userdetail'] = false;
				}
			
			}
			$data['message'] = $qData;
			$data['reply'] = $this->replymessage();
			// pr($qData);exit;
			return $data;		
		}
		
		return false;
	}

	function replymessage($start=0,$limit=10,$id=false,$noread=true){	
		if($id==false)$id = intval($this->apps->_request('id'));
		if($start==0) $start = intval($this->apps->_p('start'));

		$sql =  "
		SELECT *
		FROM my_message msg	
		WHERE parentid={$id} AND id<>{$id} AND n_status IN (1,2) 
		ORDER BY datetime DESC LIMIT {$start},{$limit} ";
		$qData = $this->apps->fetch($sql,1);
	
		if($qData) {	
			$sdata = false;
			$socialData = false;
			foreach($qData as $val){
				$sdata[$val['fromid']] = $val['fromid'];
				if($noread){
					$sql ="	UPDATE my_message set n_status=2
					WHERE
					id={$val['id']} AND recipientid={$this->uid} LIMIT 1	
					";
					$this->apps->query($sql);
				}
			}
			if($sdata){
				$strsdata = implode(',',$sdata);
				$socialData= $this->getSocialData($strsdata);
			}
			
			if($socialData){
				foreach($qData as $key => $val){
					//$qData[$key]['message']  =  strtoupper(substr($val['message'],1,1)).strtolower(substr($val['message'],2));
					if(array_key_exists($val['fromid'],$socialData)) $qData[$key]['userdetail'] = $socialData[$val['fromid']];
					else  $qData[$key]['userdetail'] = false;
				}
			
			}
			// pr($qData);exit;
			return $qData;		
		}
		
		return false;	
	}
	
	function createMessage($recipientid=false,$message=false,$fromwho=1){
		$datetime = date("Y-m-d H:i:s");
		
		$parentid = intval($this->apps->_p('parentid'));
		if(!$recipientid)$recipientid = intval($this->apps->_p('recipientid'));
		if(!$message)$message = strip_tags($this->apps->_p('message'));
		
		$sql ="
			INSERT INTO my_message (fromid,recipientid,message,datetime,n_status,fromwho,parentid) 
			VALUES ({$this->uid},{$recipientid},'{$message}','{$datetime}',1,'{$fromwho}',{$parentid})
			";
		// pr($sql);exit;
		$this->logger->log($sql);
		$this->apps->query($sql);
			
		if($this->apps->getLastInsertId()>0) {
			if($parentid==0) {
				$parentid = $this->apps->getLastInsertId();
				$sql ="
					UPDATE my_message set parentid={$parentid}
					WHERE
					id={$parentid} LIMIT 1					
					";
				$this->apps->query($sql);
				
				
			}
			$sql ="
					UPDATE my_message set datetime=NOW()
					WHERE
					id={$parentid} LIMIT 1					
					";
				$this->apps->query($sql);
			return $parentid;
		}
		return false;
	
	}
	
	function getSocialData($strsdata=false){
		global $CONFIG;
		if($strsdata==false) return false;
		$data =false;
	
		
		
		$sql ="
		SELECT sm.id,sm.name,sm.last_name,sm.img, ptype.id pagetypeid
		FROM social_member sm
		LEFT JOIN my_pages pages ON pages.ownerid = sm.id
		LEFT JOIN my_pages_type ptype ON ptype.id = pages.type		
		WHERE sm.id IN ({$strsdata}) ";		
		// pr($sql);
			$sQdata = $this->apps->fetch($sql,1);	
			// pr($sQdata);	
			if($sQdata){
				$arrmessagetype[1] = "direct";
				$arrmessagetype[2] = "direct";
				$arrmessagetype[3] = "brand";
				$arrmessagetype[4] = "brand";
				$arrmessagetype[5] = "brand";
				$arrmessagetype[100] = "challenge";
				foreach($sQdata as $key => $val){
				
							$sQdata[$key]['messagetype'] = @$arrmessagetype[$val['pagetypeid']];
							
							if(!is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}user/photo/{$val['img']}")) $val['img'] = false;
							if($val['img']) $sQdata[$key]['image_full_path'] = $CONFIG['BASE_DOMAIN_PATH']."public_assets/user/photo/".$val['img'];
							else $sQdata[$key]['image_full_path'] = $CONFIG['BASE_DOMAIN_PATH']."public_assets/user/photo/default.jpg";
							$data[$val['id']]=$sQdata[$key];			
				}
			}
			
		return $data;
	
	}	
	
	function uninboxmessage(){
			$cid = $this->apps->_p('cid');
			$sql ="
					UPDATE my_message set n_status=3
					WHERE
					parentid={$cid} AND ( fromid={$this->uid} OR  recipientid ={$this->uid} )
					
					";
				// pr($sql);exit;
			if($this->apps->query($sql)) return true;
			else return false;
	
	}
	
	
	function getinboxcount($notification=true){
		
		$total = 0;
		if(!$notification) $qnotif = "AND msg.message NOT LIKE '%tag on%' ";
		else $qnotif = "";
		$sql_total = "
			select count(a.id) total
			FROM (
				SELECT msg.*
				FROM my_message msg	
				LEFT JOIN social_member smsend	ON smsend.id = msg.fromid
				LEFT JOIN social_member smreci	ON smreci.id = msg.recipientid
				WHERE msg.recipientid ={$this->uid}  AND msg.n_status IN (1)  
				AND smsend.n_status = 1
				AND smreci.n_status = 1
				{$qnotif}
				GROUP BY msg.parentid ORDER BY msg.datetime DESC
			) a
		";
		$qData = $this->apps->fetch($sql_total);
		if($qData)$total = intval($qData['total']);
		
		return $total;
	}
	
}