<?php
class push extends ServiceAPI{
	
	function beforeFilter(){
		$this->contentHelper = $this->useHelper('contentHelper');
		$this->uploadHelper = $this->useHelper('uploadHelper');
		$this->loginHelper = $this->useHelper('loginHelper');
		$this->checkinHelper = $this->useHelper('checkinHelper');
		
		global $LOCALE,$CONFIG;
		
		$this->assign('basedomain', $CONFIG['BASE_DOMAIN']);
		$this->assign('assets_domain', $CONFIG['ASSETS_DOMAIN_WEB']);
		$this->assign('user',$this->user);
		$this->assign('tokenize',gettokenize(5000*60,$this->user->id));
		$this->assign('pages', strip_tags($this->_g('page')));
		$this->assign('locale', $LOCALE[1]);		
	
	}
	
	function main(){
		return false;
	}
	
	function comment(){
		/*
		if($cid==null) $cid = intval($this->_p('cid'));
		if($comment==null) $comment = $this->_p('comment');
		*/
		$data = $this->contentHelper->addComment();
		
		return $data;
	}

		
	function emoticon(){
	/*
		$cid = intval($this->_p("cid"));
		$emoid = intval($this->_p("emoid"));
	*/
		$data = $this->contentHelper->addFavorite();
		return $data;
	}
	
	function checkin(){
		/*
				$venueid = intval($this->_p('venueid'));
				$contentid = $this->_p('contentid');
				$venue = $this->_p('venue');
				$venuerefid = $this->_p('venuerefid');
				$coor = $this->_p('coor');
				$mypagestype = $this->_p('mypagestype');
				$friendtags = $this->_p('fid');
				$friendtypetags = $this->_p('ftype');
		*/
		$data = $this->checkinHelper->checkin();
		return $data;
	}
	
	function friendTags(){
		/*
			$cid = intval($this->_p("cid"));
			$fid = intval($this->_p("fid"));
			$ftype = intval($this->_p("ftype"));
		*/
		$cid = intval($this->_p("cid"));
		$fid = $this->_p('fid');
		$ftype = $this->_p('ftype');
		$arrfid = explode(',',$fid);
		$arrftype = explode(',',$ftype);
		$frienddata = false;
		$data = false;
		if(is_array($arrfid)){
			foreach($arrfid as $key => $val){
				$frienddata[$key]['fid'] = $val;
				$frienddata[$key]['ftype'] = $arrftype[$key];				
			}
				if($frienddata){
			
					foreach($frienddata as $val){
						$data =	$this->contentHelper->addFriendTags($cid,$val['fid'],$val['ftype'],false);					
					}
				
				}
			}else{
				$ftype = intval($ftype);
				$data =$this->contentHelper->addFriendTags($cid,intval($fid),intval($ftype),false);						
				
			}

			return $data;
	}
	
	function friendUnTags(){
	/*
		$cid = intval($this->_p("cid"));
		$fid = intval($this->_p("fid"));
		$ftype = intval($this->_p("ftype"));
	*/
		$cid = intval($this->_p("cid"));
		$fid = $this->_p('fid');
		$ftype = $this->_p('ftype');
		$arrfid = explode(',',$fid);
		$arrftype = explode(',',$ftype);
		$frienddata = false;
		$data = false;
		if(is_array($arrfid)){
			foreach($arrfid as $key => $val){
				$frienddata[$key]['fid'] = $val;
				$frienddata[$key]['ftype'] = $arrftype[$key];				
			}
				if($frienddata){
			
					foreach($frienddata as $val){
						$data =	$this->contentHelper->unFriendTags($cid,$val['fid'],$val['ftype'],false);					
					}
				
				}
			}else{
				$ftype = intval($ftype);
				$data =$this->contentHelper->unFriendTags($cid,intval($fid),intval($ftype),false);						
				
			}
	
		return $data;
	}
	
	
	function friendTagsSearch(){
	/*
		$keywords = intval($this->_p("keywords"));
	
	*/
		$data = $this->contentHelper->friendTagsSearch();
		return $data;;
	}
}
?>