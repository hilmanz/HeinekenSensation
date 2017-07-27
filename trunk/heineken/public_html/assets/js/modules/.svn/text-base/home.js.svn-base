var params={};
params.start=0;
params.limit=3;

function openFancybox() {
  $.fancybox({
     'href' : '#popup-quiz'
  });
}
function loadCalendar(nothing,start){
	params.start=start;
	$('#eventContainer').html('<div style="min-height:600px" class="loaders"><img src="'+assets_domain+'images/loader.gif" /></div>');
	$.post(basedomain+'home/loadCalendar',params,function(response){
		console.log(response);
		try{
			if(response.status==1){
				var htm='';
				var data = response.data.data;
				$.each(data,function(k,v){
					if(v.n_status==1){
						htm+='<div class="eventRow">';
					}else{
						htm+='<div class="eventRow" style="position:relative">';
						htm+='<div style="display: block; width: 100%; position: absolute; height: 130px; background: none repeat scroll 0px 0px rgba(0, 42, 0, 0.6);"></div>';
					}
                    	htm+='<a class="thumbEvent">';
							  htm+='<img src="'+assets_domain+'content/event/'+v.src+'" alt="" />';
                        htm+='</a>';
                        htm+='<div class="eventEntry">';
                            htm+='<div class="boxTitle">';
                                htm+='<h3>'+v.name+'</h3>';
                            htm+='</div>';
                            htm+='<p class="date">'+v.tgl+'</p>';
                            htm+='<p class="venue">'+v.address+'<br />'+v.city+'</p>';
                        htm+='</div>';
                    htm+='</div>';
				});
				$('#eventContainer').html(htm);

				if(start==0){
					params.start=1;
					var total = parseInt(response.data.total);
					kiPagination(total, params.start, 'eventContainerPaging', nothing, 'loadCalendar', params.limit);
				}
			}else{
				$('#eventContainer').html('Your session is expire due to inactivity. Please re-login...');
				location.reload();
			}
		}catch(e){
			$('#eventContainer').html('Your session is expire due to inactivity. Please re-login...');
			location.reload();
		}
	},"json").error(function() { 
		window.location=basedomain+'login/age_check';
	 });
}

$(document).ready(function(){
	loadCalendar({},0);
});