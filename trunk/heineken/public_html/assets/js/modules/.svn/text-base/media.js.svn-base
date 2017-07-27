var data = {};
var sortable="";

$(document).ready(function(){
	
	/*initial load data*/
	load_media();
	
	$(".selectkeyword input[type='checkbox']").on('change',function(){
		var arr = new Array();
		$("input[type='checkbox'].ck").each(function(){
		    if ($(this).prop('checked')==true){ 
		        arr.push($(this).val());
		    }
		});
		data.cid=arr;
		load_media(data);
	});

	$("input[type='submit']#dateFilter").on('click',function(event){
		event.preventDefault();
		data.fromDate = $("#datefrom").val();
		data.toDate = $("#dateto").val();
		load_media(data);
	});

	$("a.arrowshort").on('click',function(event){
		event.preventDefault();
		var sort={};
		sort.type=$(this).data('log');
		if($(this).hasClass('icon-arrow-up')){
			$(this).removeClass('icon-arrow-up');
			$(this).addClass('icon-arrow-down');
			sort.status=1;
			getmediafeed(0,10,sort)
		}else{
			$(this).removeClass('icon-arrow-down');
			$(this).addClass('icon-arrow-up');
			sort.status=0;
			getmediafeed(0,10,sort)
		}
	});
});


function load_media(post){
	$.post(basedomain+'../service/media/load',post,function(response){
		try{
			if(response.articlesonmedia.status==1){
				var chart_req = {};
				
				chart_req.container = "#chart1";
				chart_req.categories = response.articlesonmedia.data.categories;
				chart_req.chart_data = response.articlesonmedia.data.series;

				line_chart(chart_req);
			}
		}catch(e){}
		try{
			if(response.articlepost.status==1){
				var chart_req = {};
				var chart_data = {type: 'pie',name: 'Article Posts'};
				var pie_series = new Array();

				$.each(response.articlepost.data,function(k,v){
					pie_series.push([v.campaign_id, parseInt(v.num)]);
				});

				chart_data.data=pie_series;
				chart_req.container = "#chartpie1";
				chart_req.chart_data = [chart_data];
				pie_chart(chart_req);
			}
		}catch(e){}
		try{
			if(response.articlecomment.status==1){
				var chart_req = {};
				var chart_data = {type: 'pie',name: 'Article Comments'};
				var pie_series = new Array();

				$.each(response.articlecomment.data,function(k,v){
					pie_series.push([v.campaign_id, parseInt(v.num)]);
				});

				chart_data.data=pie_series;
				chart_req.container = "#chart3";
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
			if(response.mediasentiment.status==1){
				var chart_req = {};
				var chart_req = {};
				
				chart_req.container = "#chart4";
				chart_req.categories = response.mediasentiment.data.categories;
				chart_req.chart_data = response.mediasentiment.data.series;

				stack_column_chart(chart_req);
			}
		}catch(e){}
		try{
			load_feed_list(response);
		}catch(e){}

    },'json');
}

function getmediafeed(start,limit,sort){
	if(sort){
		sortable = "/"+sort.type+"/"+sort.status;
	}
	$.post(basedomain+'../service/media/feeds/'+start+'/'+limit+''+sortable,data,function(response){
		try{
			load_feed_list(response);
		}catch(e){}
	});
}

function load_feed_list(response){
	$('.data_table2 tbody').html("");
	if(response.mediapost.status==1){
		var str='';
		$.each(response.mediapost.data.list,function(k,v){
			v.sentiment_value = parseFloat(v.sentiment_value);
			var sentiment = 'Negative';
			var sentiment_color = 'red';
			if(v.sentiment_value>0){
				sentiment = 'Positive';
				sentiment_color = 'green';
			}
			k=k+1;
			var evenOdd = ((k%2==0)?'even':'odd');
			
            str+='<tr class="'+evenOdd+'">';
                        str+='<td><a href="'+v.link+'" target="_blank">'+v.media_name+'</a><br><span style="font-size:10px;color: #0086AB !important;text-decoration: none;">'+v.published_date+'</span></td>';
                        str+='<td><a href="'+v.link+'" target="_blank">'+v.title+'</a></td>';
                        str+='<td class="center">'+number_format(v.comments_count)+'</td>';
                        str+='<td>'+v.summary+'</td>';
                        str+='<td class="center"><span class="'+sentiment_color+'">'+sentiment+'</span></td>';
                    str+='</tr>';
		});
		var detail = response.mediapost.data.details;
		$('#mediaPost tbody').html(str);
		$('#totalMediaPost').html(number_format(detail.total_rows)+' Articles <span class="netip"><a href="#" class="exceldownload" title="Download Raw">&nbsp;</a></span>');
		$('.dataTables_info').html("Showing "+detail.current_page+" to "+detail.current_limit+" of "+number_format(detail.total_rows)+" entries");
		


		var pgn='';
		if(detail.prev==false){
			pgn+='<a class="paginate_disabled_previous">Previous</a>';
		}else{
			var prev = parseInt(detail.index)-2;
			pgn+='<a class="paginate_enabled_previous" onclick="getmediafeed('+prev+','+detail.limit+')">Previous</a>';
		}
		if(detail.next==false){
			pgn+='<a class="paginate_disabled_next">Next</a>';
		}else{
			var prev = parseInt(detail.index)-1;
			pgn+='<a class="paginate_enabled_next" onclick="getmediafeed('+detail.index+','+detail.limit+')">Next</a>';
		}
        $('.dataTables_paginate').html(pgn);
	}
}