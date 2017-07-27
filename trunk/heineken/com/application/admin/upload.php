<?php

global $ENGINE_PATH;
include_once $ENGINE_PATH."Utility/Paginate.php";

class upload extends Admin{
	var $category;
	var $type;
	function __construct(){
		parent::__construct();	
		
		$this->folder =  'upload';
		$this->total_per_page = 20;
	}
	
	function admin(){
		global $CONFIG;
	
		//get admin role
		foreach($this->roler as $key => $val){
			$this->View->assign($key,$val);
		}
		//get specified admin role if true
		if($this->specified_role){
			foreach($this->specified_role as $val){
				$type[] = $val['type'];
				$category[] = $val['category'];
			}
			if($type) $this->type = implode(',',$type);
			else return false;
			if($category) $this->category = implode(',',$category);
			else return false;
		}
		
		$this->View->assign('folder',$this->folder);
		$this->View->assign('baseurl',$CONFIG['BASE_DOMAIN_PATH']);
		$act = $this->_g('act');
		if($act){
			return $this->$act();
		} else {
			return $this->home();
		}
	}

	function home(){
		//filter box
		$filter = "";
		$search = $this->_g("search") == NULL ? '' : $this->_g("search");
		$startdate = $this->_g("startdate") == NULL ? '' : $this->_g("startdate");
		$enddate = $this->_g("enddate") == NULL ? '' : $this->_g("enddate");
		
		$filter .= $startdate=='' ? "" : "AND created_date >= '{$startdate}' ";
		$filter .= $enddate=='' ? "" : "AND created_date < '{$enddate}' ";		
		$filter .= $search=='' ? "" : "AND (nama_file LIKE '%{$search}%' OR email LIKE '%{$search}%' OR nama_event LIKE '%{$search}%') ";
		
		$this->View->assign('search',$search);
		$this->View->assign('startdate',$startdate);
		$this->View->assign('enddate',$enddate);
	
		$start = intval($this->_g('st'));
		
		/* Hitung banyak record data */
		$sql = "SELECT count(*) total FROM hnk_upload WHERE n_status <> 3 {$filter}";
		$totalList = $this->fetch($sql);
		
		if($totalList){
			$total = intval($totalList['total']);
		} else $total = 0;
		
		/* list article */
		$sql = "
			SELECT * FROM hnk_upload WHERE n_status <> 3 {$filter}
			ORDER BY created_date DESC
			LIMIT {$start},{$this->total_per_page}
		";
		
		$list = $this->fetch($sql,1);
		
		$n = $start+1;
		if ($list) {
			foreach($list as $key => $val){
				$id = $val['id'];
				$val['no'] = $n++;
				$list[$key] = $val;
			}
		}
		
		$this->View->assign('list',$list);
		$this->Paging = new Paginate();	
		$this->View->assign("paging",$this->Paging->getAdminPaging($start, $this->total_per_page, $total, "?s={$this->folder}&search={$search}&startdate={$startdate}&enddate={$enddate}"));
		return $this->View->toString("application/admin/{$this->folder}/{$this->folder}_list.html");
	}
	
	function add(){
		global $CONFIG;
		
		if($this->_p('simpan')){
			list($even_date,$nama_event,$email) = explode('#',$_FILES['image']['name']);
			$nama_file = $_FILES['image']['name'];
			$email = explode('.', $email,-1);
			$email = implode('.',$email);
			
			$sql = "
				INSERT INTO hnk_upload (nama_file,nama_event,email,event_date,created_date) VALUES (\"{$nama_file}\",\"{$nama_event}\",'{$email}','{$even_date}',NOW())
			";
			$this->query($sql);
			
			$last_id = $this->getLastInsertId();
			if(!$last_id){
				$this->View->assign("msg","Add process failure");
				return $this->View->toString("application/admin/{$this->folder}/{$this->folder}_new.html");
			}else{
					
				
				//create Image function
				$imagesfilename = $this->createImage($last_id,"{$CONFIG['LOCAL_ASSET']}content/gallery/",false);
				// $this->log->sendActivity("add {$this->folder}",$last_id);
				
				/* insert to gallery heineken live access */
				// $imagesfilename = $this->createImage($last_id,"{$CONFIG['LOCAL_ASSET']}content/gallery/",false);
				// pr(" filename : ".$imagesfilename);
				if($imagesfilename) {
					$sql = " 
					INSERT INTO hnk_event_gallery (event,email,date_photo,src) 
					VALUES (\"{$nama_event}\",'{$email}','{$even_date}','{$imagesfilename}')
					";
					// pr($sql);
					$this->query($sql);
					
					$sql = "UPDATE hnk_member SET user_type = 2 WHERE email=\"{$email}\" LIMIT 1";
					$this->query($sql);
			 
				}
				
				return $this->View->showMessage("Success Create {$this->folder} ", "index.php?s={$this->folder}&act=add");
			}
		}
		return $this->View->toString("application/admin/{$this->folder}/{$this->folder}_new.html");
	}
	
	function edit(){
		global $CONFIG;
		
		$id = $this->_g('id');
		
		if(! $this->_p('simpan')){
			$sql = "SELECT * FROM hnk_upload WHERE id={$id} LIMIT 1";
			$qData = $this->fetch($sql);
			// pr($qData);
			if($qData){
				foreach($qData as $key => $val){
					$this->View->assign($key,$val);
				}
			}
		} else {
			$id 			= $this->_p('id');
			$nama_event 	= $this->_p('nama_event');
			$email 			= $this->_p('email');
			$nama_file 		= $this->_p('nama_file');
			$status 		= $this->_p('n_status');
			
			if($nama_event=='' ){
				$this->View->assign('msg',"Please complete the form!");
				return $this->View->toString("application/admin/{$this->folder}/{$this->folder}_edit.html");
			}
			
			$sql = "UPDATE hnk_upload SET nama_event = '{$nama_event}',email=\"{$email}\",nama_file=\"{$nama_file}\",n_status='{$status}' 
					WHERE id={$id} LIMIT 1";
			$last_id = $id;
		
			if(!$this->query($sql)){
				$this->View->assign("msg","edit process failure");
				return $this->View->toString("application/admin/{$this->folder}/{$this->folder}_edit.html");
			}else{
				//create Image function
				$this->createImage($last_id);
				
				$sql = "UPDATE hnk_member SET user_type = 2 WHERE email=\"{$email}\" LIMIT 1";
				$this->query($sql);
			 
				return $this->View->showMessage('Berhasil', "index.php?s={$this->folder}");
			}
		}
		return $this->View->toString("application/admin/{$this->folder}/{$this->folder}_edit.html");
	}
	
	function hapus(){
		$id = $this->_g('id');
		if( !$this->query("UPDATE hnk_upload SET n_status = 3 WHERE id={$id}")){
			return $this->View->showMessage('Gagal',"index.php?s={$this->folder}");
		}else{
			return $this->View->showMessage('Berhasil',"index.php?s={$this->folder}");
		}
	}
	
	function createImage($last_id=null,$path=false,$savetolog=true){
		global $CONFIG;
		if($path==false){
			$path = "{$CONFIG['LOCAL_PUBLIC_ASSET']}{$this->folder}/";
		}
		if($last_id==null) return false;
	
		if ($_FILES['image']['name']!=NULL) {
			include_once '../../engines/Utility/phpthumb/ThumbLib.inc.php';
			
			//list($file_name,$ext) = explode('.',$_FILES['image']['name']);
			$type = explode('/',$_FILES['image']['type']);
			$img = md5($_FILES['image']['name'].rand(1000,9999)).".".$type[1];
			try{
				$thumb = PhpThumbFactory::create( $_FILES['image']['tmp_name']);
			}catch (Exception $e){
				pr("failed to save thumb factory {$e}");
				pr($path);
				pr($_FILES['image']);
				return false;
			}
			
			if(move_uploaded_file($_FILES['image']['tmp_name'],"{$path}{$img}")){
				
				list($width, $height, $type, $attr) = getimagesize("{$path}{$img}");
				$nama_file = $_FILES['image']['name'];
				$maxSize = 1000;
				if($width>=$maxSize){
					if($width>=$height) {
						$subs = $width - $maxSize;
						$percentageSubs = $subs/$width;
					}
				}
				if($height>=$maxSize) {
					if($height>=$width) {
						$subs = $height - $maxSize;
						$percentageSubs = $subs/$height;
					}
				}
				if(isset($percentageSubs)) {
				 $width = $width - ($width * $percentageSubs);
				 $height =  $height - ($height * $percentageSubs);
				}
				
				$w_small = $width - ($width * 0.5);
				$h_small = $height - ($height * 0.5);
				$w_tiny = $width - ($width * 0.7);
				$h_tiny = $height - ($height * 0.7);
				
				//resize the image
				if($savetolog){
					$thumb->adaptiveResize($width,$height);
					$big = $thumb->save( "{$path}big_".$img);
					$thumb->adaptiveResize($w_small,$h_small);
					$small = $thumb->save( "{$path}small_".$img );
					$thumb->adaptiveResize($w_tiny,$h_tiny);
					$tiny = $thumb->save( "{$path}tiny_".$img );
				}else{
					
					$thumb->adaptiveResize($width,$height);
					$big = $thumb->save( "{$path}original/".$img);
					$thumb->adaptiveResize($width,$height);
					$big = $thumb->save( "{$path}large/".$img);
					$thumb->adaptiveResize($width,$height);
					$big = $thumb->save( "{$path}slider/".$img);
					$thumb->adaptiveResize($width,$height);
					$big = $thumb->save( "{$path}ori/".$nama_file);
					$thumb->adaptiveResize($w_small,$h_small);
					$small = $thumb->save( "{$path}medium/".$img );
					$thumb->adaptiveResize($w_tiny,$h_tiny);
					$tiny = $thumb->save( "{$path}thumb/".$img );
				}
				//$this->autoCropCenterArea($img,"{$CONFIG['LOCAL_PUBLIC_ASSET']}{$this->folder}/",$width,$height);
				
			}else{
				pr("failed to save thumb factory {$e}");
			// pr($path);
			// pr($_FILES['image']);
			}
			// if($savetolog)
			$this->inputImage($last_id,$img);
			return $img;
		}
	}
	
	function inputImage($id,$img){
		$this->query("UPDATE hnk_upload SET hash_filename = '{$img}' WHERE id={$id}");
				
	}
}