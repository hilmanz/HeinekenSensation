$(document).ready(function(){
	
	load_keyword_group();
});

function load_keyword_group(post){
	$.post(basedomain+'../service/account/load',post,function(response){
		//try{
			if(response.accountNkeyword.data){
				var arr = new Array();
				var head='';
				var body='';
				$.each(response.accountNkeyword.data,function(k,v){
					var evenOdd = ((k%2==0)?'even':'odd');
					body+='<tr class="'+evenOdd+'">';
					var dummy=false;
					
					if(Object.keys(v).length < arr.length && k!=0){
						dummy=true;
					}

					$.each(v, function(kk,vv){
						var th = false;
						if(k==0){
							arr.push(kk);
							head+='<th><h3 class="th_wrapp">'+kk+'</h3></th>';
						}
						$.map(arr, function(elementOfArray, indexInArray) {
							if(elementOfArray == kk){
								th=true;
								body+='<td><a href="#">'+((vv=="")?"":vv)+'</a></td>';
							}else{
								if(dummy==true){
									body+='<td><a href="#"></a></td>';
								}
							}

						});
						if(th==false && k!=0){
							arr.push(kk);
							head+='<th><h3 class="th_wrapp">'+kk+'</h3></th>';
							//body+='<td><a href="#">'+((vv=="")?"":vv)+'</a></td>';
						}
						
					});
					body+='</tr>';
				});
				head='<tr>'+head+'</tr>';
				$('#topicNkeyword thead').html(head);
				$('#topicNkeyword tbody').html(body);
			}
		//}catch(e){}
	});
}