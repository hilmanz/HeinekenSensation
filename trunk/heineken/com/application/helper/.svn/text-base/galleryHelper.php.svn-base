<?php 

class galleryHelper {
	function __construct($apps=null){
		global $logger;
		$this->logger = $logger;
		$this->apps = $apps;

		if($this->apps->isUserOnline())  {
			if(is_object($this->apps->user)){
				$this->fbID = $this->apps->user->id;	
			}
		}
	}

	function loadGallery($start=0,$limit=8,$page=0,$widget=null){
		$email=$this->apps->user->email;
		if($email){
			$sql = "SELECT id FROM hnk_event_gallery WHERE email LIKE '%{$email}%' LIMIT 1";
			$rs=$this->apps->fetch($sql);
		}
		if($widget){
			$next = $start+9;
		}else{
			$next = $start+8;
		}
		
		$prev = $start;
		if($rs){
			if($page==0){
				if($start!=0){
					$sql3="SELECT *,part, DATE_FORMAT(date_photo,'%d/%m/%Y') AS date_photo FROM (
						(SELECT *,1 AS part FROM hnk_event_gallery
						WHERE n_status = 1 AND email LIKE '%{$email}%'
						ORDER BY date_photo DESC
						)UNION ALL
						(SELECT *,2 AS part FROM hnk_event_gallery
						WHERE n_status = 1 AND email NOT LIKE '%{$email}%'
						ORDER BY date_photo DESC)) AS A
						ORDER BY part ASC,date_photo DESC
						LIMIT 0,{$prev}";
				}
				$sql="SELECT *,part, DATE_FORMAT(date_photo,'%d/%m/%Y') AS date_photo FROM (
					(SELECT *,1 AS part FROM hnk_event_gallery
					WHERE n_status = 1 AND email LIKE '%{$email}%'
					ORDER BY date_photo DESC
					)UNION ALL
					(SELECT *,2 AS part FROM hnk_event_gallery
					WHERE n_status = 1 AND email NOT LIKE '%{$email}%'
					ORDER BY date_photo DESC)) AS A
				ORDER BY part ASC,date_photo DESC
				LIMIT {$start},{$limit}";

				$sql2="SELECT *,part, DATE_FORMAT(date_photo,'%d/%m/%Y') AS date_photo FROM (
					(SELECT *,1 AS part FROM hnk_event_gallery
					WHERE n_status = 1 AND email LIKE '%{$email}%'
					ORDER BY date_photo DESC
					)UNION ALL
					(SELECT *,2 AS part FROM hnk_event_gallery
					WHERE n_status = 1 AND email NOT LIKE '%{$email}%'
					ORDER BY date_photo DESC)) AS A
				ORDER BY part ASC,date_photo DESC
				LIMIT {$next},1000";
		
			}else{
				$sql="SELECT *, DATE_FORMAT(date_photo,'%d/%m/%Y') AS date_photo FROM hnk_event_gallery
					WHERE n_status = 1 AND email LIKE '%{$email}%'
					ORDER BY date_photo DESC 
					LIMIT {$start},{$limit}";
					
			}
			$rs=$this->apps->fetch($sql,1);
			$rs2=$this->apps->fetch($sql2,1);
			$rs3=$this->apps->fetch($sql3,1);
		}else{
			if($page==0){
				if($start!=0){
					$sql3="SELECT *, DATE_FORMAT(date_photo,'%d/%m/%Y') AS date_photo FROM hnk_event_gallery 
					WHERE n_status = 1
					ORDER BY date_photo DESC
					LIMIT 0,{$prev}";
				}
				$sql="SELECT *, DATE_FORMAT(date_photo,'%d/%m/%Y') AS date_photo FROM hnk_event_gallery 
					WHERE n_status = 1
					ORDER BY date_photo DESC
					LIMIT {$start},{$limit}";
				$sql2="SELECT *, DATE_FORMAT(date_photo,'%d/%m/%Y') AS date_photo FROM hnk_event_gallery 
					WHERE n_status = 1
					ORDER BY date_photo DESC
					LIMIT {$next},1000";
				$rs=$this->apps->fetch($sql,1);
				$rs2=$this->apps->fetch($sql2,1);
				$rs3=$this->apps->fetch($sql3,1);
			}else{
				$sql="SELECT *, DATE_FORMAT(date_photo,'%d/%m/%Y') AS date_photo FROM hnk_event_gallery 
					WHERE n_status = 1 AND email LIKE '%{$email}%'
					ORDER BY date_photo DESC
					LIMIT {$start},{$limit}";
				$rs=$this->apps->fetch($sql,1);
			}
		}

		if($page==0){
			$sql="SELECT COUNT(id) AS total FROM hnk_event_gallery 
					WHERE n_status = 1 LIMIT 1";
			$total=$this->apps->fetch($sql);

			return array('data'=>$rs,'data_total'=>$rs2,'data_prev'=>$rs3,'total'=>$total['total']);
		}else{
			return array('data'=>$rs);
		}
	}
}

?>

