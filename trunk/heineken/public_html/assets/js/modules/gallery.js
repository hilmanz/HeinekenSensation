var params={};
params.start=0;
params.limit=8;
function loadGallery(nothing,start){
	params.start=start;
	$('#galleryList').html('<div style="min-height:600px" class="loaders"><img src="'+assets_domain+'images/loader.gif" /></div>');
	$.post(basedomain+'gallery/loadGallery',params,function(response){
		try{
			if(response.status==1){
				var htm='';
				if(params.start!=0){
					try{
						htm+='<div style="display:none;">';
						$.each(response.data.data_prev,function(k,v){
							htm+='<div class="imgBox">';
		                    	htm+='<div class="imgBox-entry">';
		                            htm+='<a class="showPopup" href="#gal-'+v.id+'" rel="group"  title="'+v.event+'">';
		                                htm+='<img src="'+assets_domain+'content/gallery/medium/'+v.src+'" alt="'+v.event+'" />';
		                            htm+='</a>';
		                            if(v.part==1){
		                            	var img = assets_domain+'content/gallery/large/'+v.src;
			                            htm+='<a class="greenBtn" href="javascript:;" onclick="shareFB(\''+v.event+'\',\'http://preview.kanadigital.com/Heineken_Micro/public_html/\',\''+img+'\',\''+v.date_photo+'\',\''+v.desc+'\')"><img src="'+assets_domain+'images/facebook-share-button1.png"></a>';
			                        }
									htm+='<div class="popupImg">';
										htm+='<div id="gal-'+v.id+'">';
												 htm+='<img src="'+assets_domain+'content/gallery/large/'+v.src+'" />';
										htm+='</div>';
									htm+='</div>';
		                        htm+='</div>';
		                    htm+='</div>';
						});
						htm+='</div>';
					}catch(e){}
				}
				$.each(response.data.data,function(k,v){
					htm+='<div class="imgBox">';
                    	htm+='<div class="imgBox-entry">';
                            htm+='<a class="showPopup" href="#gal-'+v.id+'" rel="group"  title="'+v.event+'">';
                                htm+='<img src="'+assets_domain+'content/gallery/medium/'+v.src+'" alt="'+v.event+'" />';
                            htm+='</a>';
                            if(v.part==1){
                            	var img = assets_domain+'content/gallery/large/'+v.src;
	                            htm+='<a data-log="shareFB_photo" class="greenBtn" href="javascript:;" onclick="shareFB(\''+v.event+'\',\'\',\''+img+'\',\''+v.date_photo+'\',\'Check out the wicked style I did when I took a peek into the wonderland! #HNKLiveAccess www.liveaccess.co\')"><img src="'+assets_domain+'images/facebook-share-button1.png"></a>';
	                        }
							htm+='<div class="popupImg">';
								htm+='<div id="gal-'+v.id+'">';
										 htm+='<img src="'+assets_domain+'content/gallery/large/'+v.src+'" />';
								htm+='</div>';
							htm+='</div>';
                        htm+='</div>';
                    htm+='</div>';
				});	
				var total = parseInt(response.data.total);
				if(total>1){
					try{
						htm+='<div style="display:none;">';
						$.each(response.data.data_total,function(k,v){
							htm+='<div class="imgBox">';
		                    	htm+='<div class="imgBox-entry">';
			                    	htm+='<a class="showPopup" href="#gal-'+v.id+'" rel="group"  title="'+v.event+'">';
		                                htm+='<img src="'+assets_domain+'content/gallery/medium/'+v.src+'" alt="'+v.event+'" />';
		                            htm+='</a>';
		                            if(v.part==1){
		                            	var img = assets_domain+'content/gallery/large/'+v.src;
			                            htm+='<a class="greenBtn" href="javascript:;" onclick="shareFB(\''+v.event+'\',\'http://preview.kanadigital.com/Heineken_Micro/public_html/\',\''+img+'\',\''+v.date_photo+'\',\''+v.desc+'\')"><img src="'+assets_domain+'images/facebook-share-button1.png"></a>';
			                        }
									htm+='<div class="popupImg">';
										htm+='<div id="gal-'+v.id+'">';
												 htm+='<img src="'+assets_domain+'content/gallery/large/'+v.src+'" />';
										htm+='</div>';
									htm+='</div>';
		                        htm+='</div>';
		                    htm+='</div>';
						});
						htm+='</div>';
					}catch(e){}
				}
				$('#galleryList').html(htm);
				if(start==0){
					params.start=1;

					kiPagination(total, params.start, 'galleryListPaging', nothing, 'loadGallery', params.limit);
				}
			}
		}catch(e){
			console.log(e);
			$('#galleryList').html("No Images data yet.");
		}
	},"json").error(function() { 
					window.location=basedomain+'login/age_check';
				 });
}

$(document).ready(function(){
	loadGallery({},0);
});