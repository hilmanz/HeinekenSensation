<?php
class entourage extends ServiceAPI{

	
	function beforeFilter(){
	
		$this->contentHelper = $this->useHelper('contentHelper');
		$this->entourageHelper = $this->useHelper('entourageHelper');
		$this->searchHelper = $this->useHelper('searchHelper');
		$this->deviceMopHelper = $this->useHelper('deviceMopHelper');
		$this->uploadHelper = $this->useHelper('uploadHelper');
		global $LOCALE,$CONFIG;
		$this->assign('basedomain', $CONFIG['BASE_DOMAIN']);
		$this->assign('assets_domain', $CONFIG['ASSETS_DOMAIN_WEB']);
		$this->assign('locale', $LOCALE[1]);		
		$this->assign('pages', strip_tags($this->_g('page')));		
	}
	
	function lists(){
		
		return $this->entourageHelper->getEntourage(null,0,5,true);

	}
	
	function profile(){		
			$userprofile =  $this->entourageHelper->entourageProfile();	
			return $userprofile;

	}
	
		
	function register(){
		GLOBAL $CONFIG;
		$success = false;
		
			
			$path = $CONFIG['LOCAL_PUBLIC_ASSET']."entourage/photo/";
			$img = false;
			if (isset($_FILES['img'])&&$_FILES['img']['name']!=NULL) {
				if (isset($_FILES['img'])&&$_FILES['img']['size'] <= 20000000) {
					$img = $this->uploadHelper->uploadThisImage($_FILES['img'],$path);
						
				} else {
					$success = false;
				}
			} else {
				$success = false;
			}
	
		if($img)$filename = $img['arrImage']['filename'];
		else $filename = false;
		
		$data = $this->entourageHelper->addEntourage($filename);
		if($data) $success = true;		
		else $success = false;
		
		return $success;
	}
	
	function search(){
		$data = $this->entourageHelper->getSearchEntourage();
		return $data;
	}
	
	function checkemail(){
			$result = false;
			$data = $this->deviceMopHelper->searchProfileUser();
			if($data) {
				if($data['result']) $result = $data;
				else {
					// $data = $this->entourageHelper->checkentourage();	
					// if($data['result']) $result = $data;
					
				}
			}else {
				// $data = $this->entourageHelper->checkentourage();	
				// if($data['result']) $result = $data;
				
			}
			return $result;
	}
	
	function checkgiid(){
			
			$result = false;
			$data = $this->deviceMopHelper->AdminGetProfileonGiid();
			if($data) {
				if($data['result']) $result = $data;
				else {
					// $data = $this->entourageHelper->checkentourage();	
					// if($data['result']) $result = $data;
				}
			}else {
					// $data = $this->entourageHelper->checkentourage();	
					// if($data['result']) $result = $data;
			}
			return $result;
	}
	
	function chart(){
	
		$entourage = $this->entourageHelper->getEntourage(null,0,1000,true);
		$dataentourage = false;
		$rangedate = false;
		if($entourage){
			if($entourage['result']){
				foreach($entourage['result'] as $val){			
					$datetimes = (string)date("Y-m-d",strtotime($val['register_date']));
					$rangedate[$datetimes] = (string)date("Y-m-d",strtotime($val['register_date']));
					$dataentourage['data'][$val['n_status']][$datetimes]=@$dataentourage['data'][$val['n_status']][$datetimes]+1;					
					$dataentourage['gender'][$val['sex']]=@$dataentourage['gender'][$val['sex']]+1;					
					$dataentourage['brand'][$val['entouragetype']]=@$dataentourage['brand'][$val['entouragetype']]+1;
					$birthday = $this->entourageHelper->getAge($val['birthday']);
					$dataentourage['age'][$birthday]=@$dataentourage['age'][$birthday]+1;					
				}
				$newdata = false;
				sort($rangedate);
				$mindate = strtotime(min($rangedate));
				$maxdate = strtotime(max($rangedate));
				
				$startdate = strip_tags($this->_p('startdate'));
				$enddate = strip_tags($this->_p('enddate'));
				if($startdate){
					if(!$enddate) if($startdate)  $enddate = date("Y-m-d",strtotime($startdate. "+7 day"));
					$mindate = strtotime($startdate);
					$maxdate = strtotime($enddate);
				}
				$totaldate = ($maxdate - $mindate) / (60*60*24);
				
				
				for($i=0;$i<=$totaldate;$i++){
				// pr($totaldate);
					$dates = date("Y-m-d",$mindate);
					$val = date("Y-m-d" , strtotime("{$dates} +{$i} day"));
					// pr($val);
					foreach($dataentourage['data'] as $key => $valve) {					
						if(array_key_exists($val,$dataentourage['data'][$key])) $newdata[$key][$val] = $dataentourage['data'][$key][$val];
						else $newdata[$key][$val] = 0;
					}
					
				}
				$dataentourage['data'] = $newdata;
				
			}
		}
		
		// return $dataentourage;
	
		ob_start();
		header('Cache-Control: no-cache, must-revalidate');
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Content-type: application/json');		
	
		print json_encode($dataentourage,JSON_FORCE_OBJECT);
		exit;
	}
	
	function synchenturage(){
		
		$data = $this->entourageHelper->synchenturage();
	
		
		return true;
	}
}
?>