var data = {};
var sortable="";

$(document).ready(function(){
	
	/*initial load data*/
	load_twitter();
	
	$(".selectkeyword input[type='checkbox']").on('change',function(){
		var arr = new Array();
		$("input[type='checkbox'].ck").each(function(){
		    if ($(this).prop('checked')==true){ 
		        arr.push($(this).val());
		    }
		});
		data.cid=arr;
		load_twitter(data);
	});

	$("input[type='submit']#dateFilter").on('click',function(event){
		event.preventDefault();
		data.fromDate = $("#datefrom").val();
		data.toDate = $("#dateto").val();
		load_twitter(data);
	});
	$("a.arrowshort").on('click',function(event){
		event.preventDefault();
		var sort={};
		sort.type=$(this).data('log');
		if($(this).hasClass('icon-arrow-up')){
			$(this).removeClass('icon-arrow-up');
			$(this).addClass('icon-arrow-down');
			sort.status=1;
			gettwitfeed(0,10,sort)
		}else{
			$(this).removeClass('icon-arrow-down');
			$(this).addClass('icon-arrow-up');
			sort.status=0;
			gettwitfeed(0,10,sort)
		}
	});
});


function load_twitter(post){
	$.post(basedomain+'../service/twitter/load',post,function(response){
		try{
			if(response.twitterbuzz.status==1){
				var chart_req = {};
				
				chart_req.container = "#chart1";
				chart_req.categories = response.twitterbuzz.data.categories;
				chart_req.chart_data = response.twitterbuzz.data.series;

				line_chart(chart_req);
			}
		}catch(e){}
		try{
			if(response.totalretweet.status==1){
				var chart_req = {};
				var chart_data = {type: 'pie',name: 'Tweets'};
				var pie_series = new Array();

				$.each(response.totalretweet.data,function(k,v){
					pie_series.push([v.campaign_id, parseInt(v.num)]);
				});

				chart_data.data=pie_series;
				chart_req.container = "#chartpie1";
				chart_req.chart_data = [chart_data];
				pie_chart(chart_req);
			}
		}catch(e){}
		try{
			if(response.intractions.status==1){
				var chart_req = {};
				var chart_data = {type: 'pie',name: 'Intraction'};
				var pie_series = new Array();

				$.each(response.intractions.data,function(k,v){
					pie_series.push([v.campaign_id, parseInt(v.num)]);
				});

				chart_data.data=pie_series;
				chart_req.container = "#chartpie2";
				chart_req.chart_data = [chart_data];
				pie_chart(chart_req);
			}
		}catch(e){}
		try{
			if(response.potentialreach.status==1){
				var chart_req = {};
				var chart_data = {type: 'pie',name: 'Reach'};
				var pie_series = new Array();

				$.each(response.potentialreach.data,function(k,v){
					pie_series.push([v.campaign_id, parseInt(v.num)]);
				});

				chart_data.data=pie_series;
				chart_req.container = "#chartpie3";
				chart_req.chart_data = [chart_data];
				pie_chart(chart_req);
			}
		}catch(e){}

		try{
			if(response.generalsentiment.status==1){
				var chart_req = {};
				var categories = response.generalsentiment.data.categories;
				var chart_data = new Array();
				
				$.each(response.generalsentiment.data.series,function(k,v){				
					var tmp = {};
					tmp.name = k;
					tmp.data = v;
					switch(k){
						case 'Positive':
							tmp.color = '#64bf0a';
							break;
						case 'Negative':
							tmp.color = '#e12626';
							break;
						default:
							tmp.color = '#999999';
					}
					chart_data.push(tmp);
				});
				
				chart_req.container = "#bar1";
				chart_req.categories = categories;
				chart_req.chart_data = chart_data;

				bar_chart(chart_req);
			}
		}catch(e){}
		try{
			if(response.viralsentiment.status==1){
				var chart_req = {};
				var categories = response.viralsentiment.data.categories;
				var chart_data = new Array();
				
				$.each(response.viralsentiment.data.series,function(k,v){				
					var tmp = {};
					tmp.name = k;
					tmp.data = v;
					switch(k){
						case 'Positive':
							tmp.color = '#64bf0a';
							break;
						case 'Negative':
							tmp.color = '#e12626';
							break;
						default:
							tmp.color = '#999999';
					}
					chart_data.push(tmp);
				});
				
				chart_req.container = "#bar2";
				chart_req.categories = categories;
				chart_req.chart_data = chart_data;

				bar_chart(chart_req);
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
			load_twit_list(response);
		}catch(e){}

    },'json');
}

function gettwitfeed(start,limit,sort){
	if(sort){
		sortable = "/"+sort.type+"/"+sort.status;
	}
	$.post(basedomain+'../service/twitter/tweets/'+start+'/'+limit+''+sortable,data,function(response){
		try{
			load_twit_list(response);
		}catch(e){}
	});
}

function load_twit_list(response){
	$('.data_table2 tbody').html("");
	if(response.tweets.status==1){
		var str='';
		$.each(response.tweets.data.list,function(k,v){
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
               str+='<td valign="top"><a target="_blank" href="https://twitter.com/'+v.author_id+'"><img src="'+v.author_avatar+'" /></a></td>';
               str+='<td valign="top">';
               str+='<div class="twitter-text">';
                  str+='<p><span class="tweetprofilelink"><strong><a target="_blank" href="https://twitter.com/'+v.author_id+'">'+v.author_name+'</a></strong>';
                  	str+='<a target="_blank" href="https://twitter.com/'+v.author_id+'">@'+v.author_id+'</a></span><span class="tweet-time"><a target="_blank" href="#">'+v.published_datetime+'</a></span><br>'+v.content+'</p>';
                  str+='<div id="twitter-actions" style="display: none; opacity: 0; margin-top: -20px;">';
                    str+='<div class="intent" id="intent-reply"><a href="https://twitter.com/intent/tweet?in_reply_to=373468538431741952" title="Reply"></a></div>';
                    str+='<div class="intent" id="intent-retweet"><a href="https://twitter.com/intent/retweet?tweet_id=373468538431741952" title="Retweet"></a></div>';
                    str+='<div class="intent" id="intent-fave"><a href="https://twitter.com/intent/favorite?tweet_id=373468538431741952" title="Favourite"></a></div>';
                  str+='</div>';
                str+='</div>';
                str+='</td>';
                str+='<td valign="top" class="center">'+number_format(v.posts_reach)+'</td>';
                str+='<td valign="top" class="center">'+number_format(v.engagement)+'</td>';
                str+='<td valign="top" class="center"><span class="'+sentiment_color+'">'+sentiment+'</span></td>';
            str+='</tr>';
		});
		var detail = response.tweets.data.details;
		$('.data_table2 tbody').html(str);
		$('#totalTwit').html(number_format(detail.total_rows)+' Tweets <span class="netip"><a href="#" class="exceldownload" title="Download Raw">&nbsp;</a></span>');
		$('.dataTables_info').html("Showing "+detail.current_page+" to "+detail.current_limit+" of "+number_format(detail.total_rows)+" entries");
		


		var pgn='';
		if(detail.prev==false){
			pgn+='<a class="paginate_disabled_previous">Previous</a>';
		}else{
			var prev = parseInt(detail.index)-2;
			pgn+='<a class="paginate_enabled_previous" onclick="gettwitfeed('+prev+','+detail.limit+')">Previous</a>';
		}
		if(detail.next==false){
			pgn+='<a class="paginate_disabled_next">Next</a>';
		}else{
			var prev = parseInt(detail.index)-1;
			pgn+='<a class="paginate_enabled_next" onclick="gettwitfeed('+detail.index+','+detail.limit+')">Next</a>';
		}
        $('.dataTables_paginate').html(pgn);
	}
}