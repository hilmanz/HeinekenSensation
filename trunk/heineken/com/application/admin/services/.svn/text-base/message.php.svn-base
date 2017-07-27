<?php
class message extends ServiceAPI{
	
	function beforeFilter(){
		$this->contentHelper = $this->useHelper('contentHelper');
		$this->messageHelper = $this->useHelper('messageHelper');
		$this->userHelper = $this->useHelper('userHelper');
		$this->searchHelper = $this->useHelper('searchHelper');
		
	
		global $LOCALE,$CONFIG;
		
		$this->assign('basedomain', $CONFIG['BASE_DOMAIN']);
		$this->assign('assets_domain', $CONFIG['ASSETS_DOMAIN_WEB']);
		$this->assign('user',$this->user);
		$this->assign('tokenize',gettokenize(5000*60,$this->user->id));
		$this->assign('pages', strip_tags($this->_g('page')));
		$this->assign('locale', $LOCALE[1]);		
	
	}
	
	function lists(){
		$data = $this->messageHelper->getMessage();
		return $data;
	}
	
	function create(){
	
		$res = false;
		$fid[] = $this->_p('recipientid');
		$ftype[] = $this->_p('ftype');
		
		if($fid) $fid = implode(',',$fid);
		if($ftype) $ftype = implode(',',$ftype);
		$msg = strip_tags($this->_p('message'));
		
		if($fid){	
				
			$arrfid = explode(',',$fid);
			$arrftype = explode(',',$ftype);
			$frienddata = false;
			if(is_array($arrfid)){
				foreach($arrfid as $key => $val){
					$frienddata[$key]['fid'] = $val;
					$frienddata[$key]['ftype'] = 1;
					
					
				}
				
				if($frienddata){
			
					foreach($frienddata as $val){
						
							$data = $this->messageHelper->createMessage($val['fid'],"{$msg}",$val['ftype']);	
					}
				
				}
			}else{
				$ftype = intval($ftype);
				$fid = intval($fid);
				
					$data = $this->messageHelper->createMessage($fid,"{$msg}",$ftype);	
			}
		
		}
		if($data) {
			$res['result'] = true;
			$res['parentid'] = $data;
		}else {
			$res['result'] = false;
			$res['parentid'] = 0;
		}
		return $res;
	}
	
	function readmessage(){
	
		$data = $this->messageHelper->readMessage();
		return $data;
		
	}
	
	function unlists(){
		$data = $this->messageHelper->uninboxmessage();
		return $data;
	}
	
}
?>