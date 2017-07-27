<?php
class entourage extends App{

	
	function beforeFilter(){
		global $LOCALE,$CONFIG;
		$this->contentHelper = $this->useHelper('contentHelper');
		$this->entourageHelper = $this->useHelper('entourageHelper');
		$this->contentHelper = $this->useHelper('contentHelper');
		$this->activityHelper = $this->useHelper('activityHelper');
		$this->searchHelper  = $this->useHelper('searchHelper');
		$this->messageHelper = $this->useHelper('messageHelper');
		$this->userHelper = $this->useHelper('userHelper');
		$this->assign('badetail',$this->userHelper->getrecepient('badetail'));
		$this->assign('basedomain', $CONFIG['BASE_DOMAIN']);
		$this->assign('assets_domain', $CONFIG['ASSETS_DOMAIN_WEB']);
		$this->assign('locale', $LOCALE[1]);		
		$this->assign('pages', strip_tags($this->_g('page')));		
	}
	
	function main(){
		$this->assign('filter',$this->_p('filter'));
		$this->assign('totalengagement',$this->_p('totalengagement'));
		$this->assign('search',$this->_p('search'));
		$this->assign('uid',$this->_p('uid'));
		$this->assign('brandid',$this->_p('brandid'));
		$this->assign('cityid',$this->_p('cityid'));
 	
		$this->log('surf','entourage');	
		 
		// $this->View->assign('userprofile',$data);
		$entourage = $this->entourageHelper->getEntourage();
		$dataentourage = false;
		if($entourage['result']){
			foreach($entourage['result'] as $val){
				$datetimes = (string)date("Y-m-d",strtotime($val['register_date']));
				$dataentourage['datetimes'][]=$datetimes;				
				$dataentourage['data'][$val['n_status']][$datetimes]=@$dataentourage['data'][$val['n_status']][$datetimes]+1;
				
			}	
		}
		$this->View->assign('entouragechart', json_encode($dataentourage));
		// pr($entourage);
		$this->assign('entourage',$entourage['result']);
		$this->assign('total',$entourage['total']);
		$this->View->assign('entourage_list',$this->setWidgets('entourage_list'));
		return $this->View->toString(TEMPLATE_DOMAIN_WEB .'/apps/entourage-pages.html');

	}
	
	function profile(){		
			$userprofile = $this->entourageHelper->entourageProfile();	
			$this->assign('userprofile',$userprofile);
			// pr($userprofile);
			$this->View->assign('entourage_detail',$this->setWidgets('entourage_detail'));
			return $this->View->toString(TEMPLATE_DOMAIN_WEB .'/apps/entourage-profile.html');

	}
	
	function editContent(){
		global $CONFIG,$LOCALE;
		if ($this->_p('authorid')==$this->user->id || $this->_p('authorid')==$this->user->pageid) {
			$data = $this->contentHelper->setEditContent();			
			if ($data) {
				$data;
			} else {
				$data= false;
			}
		} else {
			$data= false;
		}
		print json_encode($data);exit;
	}
	
	
	function register(){
		
		if($this->_g('register')==1){
			$data = $this->entourageHelper->addEntourage();
			$success = false;
			if($data) $succes = true;			
			$this->assign('success',$succes);
		}
		return $this->View->toString(TEMPLATE_DOMAIN_WEB .'/apps/entourage-register.html');
	}
	
	function search(){
		$data = $this->entourageHelper->getSearchEntourage();
		print json_encode($data);exit;
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
		
		print json_encode($data);exit;
		
	}
	
	function ajax(){
		
			
		$needs = strip_tags($this->_p('needs'));
		if($needs=='entourage') $data = $this->entourageHelper->getEntourage();
		 	
		print json_encode($data);exit;
	
		
	}
	
}
?>