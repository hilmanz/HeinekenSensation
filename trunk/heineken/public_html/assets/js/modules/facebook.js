var data = {};
var sortable="";
var fb_post_type = "feeds";

$(document).ready(function(){
	
	/*initial load data*/
	load_facebook();
	
	$(".selectkeyword input[type='checkbox']").on('change',function(){
		var arr = new Array();
		$("input[type='checkbox'].ck").each(function(){
		    if ($(this).prop('checked')==true){ 
		        arr.push($(this).val());
		    }
		});
		data.cid=arr;
		load_facebook(data);
	});

	$("input[type='submit']#dateFilter").on('click',function(event){
		event.preventDefault();
		data.fromDate = $("#datefrom").val();
		data.toDate = $("#dateto").val();
		load_facebook(data);
	});
	$("a.arrowshort").on('click',function(event){
		event.preventDefault();
		var sort={};
		sort.type=$(this).data('log');
		if($(this).hasClass('icon-arrow-up')){
			$(this).removeClass('icon-arrow-up');
			$(this).addClass('icon-arrow-down');
			sort.status=1;
			getfbfeed(0,10,sort);
		}else{
			$(this).removeClass('icon-arrow-down');
			$(this).addClass('icon-arrow-up');
			sort.status=0;
			getfbfeed(0,10,sort);
		}
	});
	$('a.feed_type').on('click',function(event){
		event.preventDefault();
		if($(this).hasClass('active')){
		}else{
			if($(this).data('type')=='feeds'){
				$('a.personals').removeClass('active');
				$(this).addClass('active');
				fb_post_type='feeds';
				getfbfeed(0,10);
			}else if($(this).data('type')=='personals'){
				$('a.feeds').removeClass('active');
				$(this).addClass('active');
				fb_post_type='personals';
				getfbfeed(0,10);
			}
		}

	});
});


function load_facebook(post){
	$.post(basedomain+'../service/facebook/load',post,function(response){
		try{
			if(response.fbdaily.status==1){
				var chart_req = {};
				
				chart_req.container = "#chart1";
				chart_req.categories = response.fbdaily.data.categories;
				chart_req.chart_data = response.fbdaily.data.series;

				line_chart(chart_req);
			}
		}catch(e){}
		try{
			if(response.fbposts.status==1){
				var chart_req = {};
				var chart_data = {type: 'pie',name: 'Posts'};
				var pie_series = new Array();

				$.each(response.fbposts.data,function(k,v){
					pie_series.push([v.campaign_id, parseInt(v.num)]);
				});

				chart_data.data=pie_series;
				chart_req.container = "#chartpie1";
				chart_req.chart_data = [chart_data];
				pie_chart(chart_req);
			}
		}catch(e){}

		try{
			if(response.fbcomments.status==1){
				var chart_req = {};
				var chart_data = {type: 'pie',name: 'Comments'};
				var pie_series = new Array();

				$.each(response.fbcomments.data,function(k,v){
					pie_series.push([v.campaign_id, parseInt(v.num)]);
				});

				chart_data.data=pie_series;
				chart_req.container = "#chartpie3";
				chart_req.chart_data = [chart_data];
				pie_chart(chart_req);
			}
		}catch(e){}
		try{
			if(response.tagcommonthemes.status==1){
				var str='';
				$.each(response.tagcommonthemes.data,function(k,v){
					str+='<a href="#" rel="'+v.nums+'">'+v.keyword+'</a> ';
				});
				$('#tagcloud').html(str);
				$("#tagcloud a").tagcloud();  
			}
		}catch(e){}
		try{
			if(response.fblikes.status==1){
				var chart_req = {};
				var chart_data = {type: 'pie',name: 'Likes'};
				var pie_series = new Array();

				$.each(response.fblikes.data,function(k,v){
					pie_series.push([v.campaign_id, parseInt(v.num)]);
				});

				chart_data.data=pie_series;
				chart_req.container = "#line1";
				chart_req.chart_data = [chart_data];
				pie_chart(chart_req);
			}
		}catch(e){}

		try{
			if(response.fbshares.status==1){
				var chart_req = {};
				var chart_data = {type: 'pie',name: 'Shares'};
				var pie_series = new Array();

				$.each(response.fbshares.data,function(k,v){
					pie_series.push([v.campaign_id, parseInt(v.num)]);
				});

				chart_data.data=pie_series;
				chart_req.container = "#line2";
				chart_req.chart_data = [chart_data];
				pie_chart(chart_req);
			}
		}catch(e){}
		
		try{
			if(response.fbcontentinteraction.status==1){
				var chart_req = {};
				var categories = response.fbcontentinteraction.data.categories;
				var chart_data = new Array();
				
				$.each(response.fbcontentinteraction.data.series,function(k,v){				
					var tmp = {};
					tmp.name = k;
					tmp.data = v;
					chart_data.push(tmp);
				});
				
				chart_req.container = "#contenttype";
				chart_req.categories = categories;
				chart_req.chart_data = chart_data;

				bar_chart(chart_req);
			}
		}catch(e){}
		//try{
			load_feed_list(response);
		//}catch(e){}
    },'json');
}

function getfbfeed(start,limit,sort){
	if(sort){
		sortable = "/"+sort.type+"/"+sort.status;
	}
	$.post(basedomain+'../service/facebook/'+fb_post_type+'/'+start+'/'+limit+''+sortable,data,function(response){
		try{
			load_feed_list(response);
		}catch(e){}
	});
}

function load_feed_list(response){
	$('.data_table2 tbody').html("");
	if(response.fbfeeds.status==1){
		var str='';
		$.each(response.fbfeeds.data.list,function(k,v){
			v.sentiment_value = parseFloat(v.sentiment_value);
			var sentiment = 'Negative';
			var sentiment_color = 'red';
			if(v.sentiment_value>0){
				sentiment = 'Positive';
				sentiment_color = 'green';
			}
			k=k+1;
			var evenOdd = ((k%2==0)?'even':'odd');
			var split_object_id = v.object_id;
			
			var split = split_object_id.split("_");
            str+='<tr class="'+evenOdd+'">';
                str+='<td><a href="https://www.facebook.com/'+v.author_id+'" target="_blank">'+v.author_name+'</a><br><span style="font-size:10px;color: #0086AB !important;text-decoration: none;">'+v.created_time+'</span></td>';
                str+='<td><a href="#">'+v.message+'</a></td>';
                str+='<td class="center"><a href="https://www.facebook.com/'+split[0]+'/posts/'+split[1]+'" target="_blank">'+number_format(v.num_comments)+'</a></td>';
                str+='<td>'+v.post_type+'</td>';
                str+='<td class="center"><span class="'+sentiment_color+'">'+sentiment+'</span></td>';
            str+='</tr>';


                         
                   
		});
		var detail = response.fbfeeds.data.details;
		$('.data_table2 tbody').html(str);
		$('#totalfbfeeds').html(number_format(detail.total_rows)+' Posts <span class="netip"><a href="#" class="exceldownload" title="Download Raw">&nbsp;</a></span>');
		$('.dataTables_info').html("Showing "+detail.current_page+" to "+detail.current_limit+" of "+number_format(detail.total_rows)+" entries");
		


		var pgn='';
		if(detail.prev==false){
			pgn+='<a class="paginate_disabled_previous">Previous</a>';
		}else{
			var prev = parseInt(detail.index)-2;
			pgn+='<a class="paginate_enabled_previous" onclick="getfbfeed('+prev+','+detail.limit+')">Previous</a>';
		}
		if(detail.next==false){
			pgn+='<a class="paginate_disabled_next">Next</a>';
		}else{
			var prev = parseInt(detail.index)-1;
			pgn+='<a class="paginate_enabled_next" onclick="getfbfeed('+detail.index+','+detail.limit+')">Next</a>';
		}
        $('.dataTables_paginate').html(pgn);
	}
}
