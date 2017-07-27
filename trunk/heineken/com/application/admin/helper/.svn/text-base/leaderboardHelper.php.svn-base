<?php 

class leaderboardHelper {

	function __construct($apps){
		global $logger;
		$this->logger = $logger;
		$this->apps = $apps;
		if($this->apps->isUserOnline())  {
			if(is_object($this->apps->user)) $this->uid = intval($this->apps->user->id);
			
			
		}

		$this->dbshema = "beat";	
	}

	
	
	function getEntourageList(){
		global $CONFIG;
			
		$sql = "SELECT count(*) totalentourage, sm.id,sm.name, sm.last_name, sm.img
				FROM `my_entourage` entourage
				LEFT JOIN social_member sm ON sm.id=entourage.referrerbybrand 
				LEFT JOIN my_pages pages ON sm.id=pages.ownerid
				WHERE pages.type = 1  AND entourage.n_status = 1
				GROUP BY `referrerbybrand` ORDER BY totalentourage DESC ";	
		$qData = $this->apps->fetch($sql,1);		
		if(!$qData)return false;
		foreach($qData as $key => $val ){
			if(!is_file("{$CONFIG['LOCAL_PUBLIC_ASSET']}user/photo/{$qData[$key]['img']}")) $qData[$key]['img'] = false;
			if($qData[$key]['img']) $qData[$key]['image_full_path'] = $CONFIG['BASE_DOMAIN_PATH']."public_assets/user/photo/".$qData[$key]['img'];
			else $qData[$key]['image_full_path'] = $CONFIG['BASE_DOMAIN_PATH']."public_assets/user/photo/default.jpg";
		}
		// pr($qData);exit;
		return $qData;
		
		
	}
	
	
	function topplacelist(){
		global $CONFIG;
			$limit = 10;
		$sql = "SELECT count(*) total, checkin.venueid, master.venuename, master.id
				FROM my_checkin checkin
				LEFT JOIN beat_venue_master master ON checkin.venueid=master.id
				WHERE checkin.venueid<>0
				GROUP BY checkin.venueid
				ORDER BY total DESC LIMIT {$limit} ";	
		$qData = $this->apps->fetch($sql,1);		
		
		return $qData;
		
		
	}
	
}

?>

