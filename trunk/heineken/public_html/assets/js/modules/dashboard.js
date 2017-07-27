var data = {};

$(document).ready(function(){
	
	/*initial load data*/
	load_dashboard();
	
	$(".selectkeyword input[type='checkbox']").on('change',function(){
		var arr = new Array();
		$("input[type='checkbox'].ck").each(function(){
		    if ($(this).prop('checked')==true){ 
		        arr.push($(this).val());
		    }
		});
		data.cid=arr;
		load_dashboard(data);
	});

	$("input[type='submit']#dateFilter").on('click',function(event){
		event.preventDefault();
		data.fromDate = $("#datefrom").val();
		data.toDate = $("#dateto").val();
		load_dashboard(data);
	});
});


function load_dashboard(post){
	$.post(basedomain+'../service/dashboard/load',post,function(response){
		try{
			if(response.dailybuzz.status==1){
				var chart_req = {};
				
				chart_req.container = "#chart1";
				chart_req.categories = response.dailybuzz.data.categories;
				chart_req.chart_data = response.dailybuzz.data.series;
				
				line_chart(chart_req);
			}
		}catch(e){}

		try{
			if(response.engagement.status==1){
				var chart_req = {};
				var chart_data = {type: 'pie',name: 'Engagement'};
				var pie_series = new Array();

				$.each(response.engagement.data,function(k,v){
					pie_series.push([v.campaign_id, parseInt(v.num)]);
				});

				chart_data.data=pie_series;
				chart_req.container = "#chart2";
				chart_req.chart_data = [chart_data];
				pie_chart(chart_req);
			}
		}catch(e){}
		try{
			if(response.shareofvoice.status==1){
				var chart_req = {};
				var chart_data = {type: 'pie',name: 'Post'};
				var pie_series = new Array();

				$.each(response.shareofvoice.data,function(k,v){
					pie_series.push([v.campaign_id, parseInt(v.num)]);
				});

				chart_data.data=pie_series;
				chart_req.container = "#chart3";
				chart_req.chart_data = [chart_data];
				pie_chart(chart_req);
			}
		}catch(e){}

		try{
			if(response.postofchannel.status==1){
				var chart_req = {};
				var categories = new Array();
				var chart_data = new Array();
				
			
				$.each(response.postofchannel.data.categories,function(k,v){
					categories.push(v);
				});
				$.each(response.postofchannel.data.series,function(k,v){				
					var tmp = {};
					tmp.name = k;
					tmp.data = v;
					chart_data.push(tmp);
				});
				
				chart_req.container = "#chart4";
				chart_req.categories = categories;
				chart_req.chart_data = chart_data;

				column_chart(chart_req);
			}
		}catch(e){}
		try{
			if(response.gettopengagement.status==1){
				var str='';
				//media
				var data = response.gettopengagement.data.news;
				if(data){
					str+='<div class="row media-key">';
                        str+='<div class="row-head">';
                            str+='<h3 class="fl"><span class="icon-cloud">&nbsp;</span>Media</h3>';
                            str+='<h3 class="fr f12"><a href="'+data.link+'" target="_blank">'+data.media_name+'</a></h3>';
                        str+='</div><!-- end .row-head -->';
                        str+='<div class="row-entry">';
                            str+='<div class="small-thumb fl">';
                                str+='<a href="'+data.link+'" target="_blank"><img src="'+assets_domain+'images/icon/iconWeb.png" /></a>';
                            str+='</div><!-- end .small-thumb -->';
                            str+='<div class="row-content fl">';
                                str+='<p><a href="'+data.link+'" target="_blank">'+data.title+'</a></p>';
                                str+='<div class="post-info">';
                                    str+='<a class="comments-count" href="#"><span class="icon-comment">&nbsp;</span>'+data.comments_count+' Comments</a>';
                                    str+='<a class="post-date" >'+data.published_date+'</a>';
                                str+='</div><!-- end .post-info -->';
                            str+='</div><!-- end .row-content -->';
                        str+='</div><!-- end .row-entry -->';
                    str+='</div><!-- end .row -->';
                }
                //twitter
				var v = response.gettopengagement.data.twitter;
				if(v){
					str+='<div class="row twitter-key">';
                       str+='<div class="row-head">';
                            str+='<h3 class="fl"><span class="icon-twitter">&nbsp;</span>Twitter</h3>';
                            str+='<h3 class="fr f12"><a href="'+v.author_link+'" target="_blank">@'+v.author_name+'</a></h3>';
                        str+='</div><!-- end .row-head -->';
                        str+='<div class="row-entry">';
                            str+='<div class="small-thumb fl">';
                                str+='<a href="'+v.link+'" target="_blank"><img src="'+v.author_avatar+'" /></a>';
                            str+='</div><!-- end .small-thumb -->';
                            str+='<div class="row-content fl">';
                                str+='<p>'+v.content+'</p>';
                                str+='<div class="post-info">';
                                    str+='<a class="comments-count" href="#"><span class="icon-comment">&nbsp;</span>'+v.rt+' RTs</a>';
                                    str+='<a class="post-date" >'+v.published_datetime+'</a>';
                                str+='</div><!-- end .post-info -->';
                            str+='</div><!-- end .row-content -->';
                        str+='</div><!-- end .row-entry -->';
                    str+='</div><!-- end .row -->';
                }

                //forum
				var v = response.gettopengagement.data.forum;
				if(v){
					str+='<div class="row forums-key">';
                        str+='<div class="row-head">';
                            str+='<h3 class="fl"><span class="icon-group">&nbsp;</span>Forums</h3>';
                            str+='<h3 class="fr f12"><a href="#">kaskus</a></h3>';
                        str+='</div><!-- end .row-head -->';
                        str+='<div class="row-entry">';
                            str+='<div class="small-thumb fl">';
                               str+='<a href="#"><img src="'+assets_domain+'images/icon/iconForum.png" /></a>';
                            str+='</div><!-- end .small-thumb -->';
                            str+='<div class="row-content fl">';
                                str+='<p>Tentang Launch Toyota AGYA & Daihatsu AYLA semuanya ada disini!!</p>';
                                str+='<div class="post-info">';
                                    str+='<a class="comments-count" href="#"><span class="icon-comment">&nbsp;</span>35 Comments</a>';
                                    str+='<a class="post-date" >28/08/2013</a>';
                                str+='</div><!-- end .post-info -->';
                            str+='</div><!-- end .row-content -->';
                        str+='</div><!-- end .row-entry -->';
                    str+='</div><!-- end .row -->';
                }

                //facebook
				var v = response.gettopengagement.data.facebook;
				if(v){
					var object_id = v.object_id;
                    var split = object_id.split("_");
					str+='<div class="row facebook-key">';
                        str+='<div class="row-head">';
                            str+='<h3 class="fl"><span class="icon-facebook">&nbsp;</span>Facebook</h3>';
                            str+='<h3 class="fr f12"><a href="https://www.facebook.com/'+split[0]+'" target="_blank">'+v.author_name+'</a></h3>';
                        str+='</div><!-- end .row-head -->';
                       str+='<div class="row-entry">';
                            str+='<div class="small-thumb fl">';
                                str+='<a href="https://www.facebook.com/'+split[0]+'" target="_blank"><img src="https://graph.facebook.com/'+v.author_id+'/picture" /></a>';
                            str+='</div><!-- end .small-thumb -->';
                           str+='<div class="row-content fl">';
                           
                                str+='<p><a href="https://www.facebook.com/'+split[0]+'/posts/'+split[1]+'" target="_blank">'+v.message+'</a></</p>';
                                str+='<div class="post-info">';
                                    str+='<a class="comments-count" href="#"><span class="icon-comment">&nbsp;</span>'+v.num_comments+' Comments</a>';
                                    str+='<a class="post-date" >'+v.publised_datetime+'</a>';
                                str+='</div><!-- end .post-info -->';
                            str+='</div><!-- end .row-content -->';
                        str+='</div><!-- end .row-entry -->';
                    str+='</div><!-- end .row -->';
                }

                $('.top-engagements').html(str);
				
			}
		}catch(e){}
		try{
			if(response.getsetimentonchannel.status==1){
				var chart_req = {};
				
				chart_req.container = "#chart5";
				chart_req.categories = response.getsetimentonchannel.data.categories;
				chart_req.chart_data = response.getsetimentonchannel.data.series;

				stack_column_chart(chart_req);
			}
		}catch(e){}


    },'json');
}