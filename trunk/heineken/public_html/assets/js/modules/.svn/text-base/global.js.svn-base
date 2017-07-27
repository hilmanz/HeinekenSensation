var triv_idx=0;
var triv_pattern='';

$(document).ready(function(){
	$('a').on('click',function(){
		var log = $(this).data('log');
		if(log!=''||log!='undefined'){
			$.post(basedomain+'home/logAjax',{'log':log},function(response){
			});
		}
	});
	$('#galleryList').on('click','a',function(){
		var log = $(this).data('log');
		if(log!=''||log!='undefined'){
			$.post(basedomain+'home/logAjax',{'log':log},function(response){
			});
		}
	});

	$('.quizPopup').on('click',function(event){
		event.preventDefault();
		$('#quizList')[0].reset();
		var id = $(this).attr('href');
		$.fancybox({
            href: id
        });
	});

	$('#emailBox,#phoneBox').on('click','.clickable',function(event){
		event.preventDefault();
		var div=$(this);
		var div2=$(this).closest('div').find('p');
		var current_value = div2.html();
		if(div.data('change')=='phone'){
			div2.closest('div').html('<input id="phone_number" type="text" name="phone_number" data-value="'+current_value+'" placeholder="'+current_value+'">');
			$("#phoneBox input").focus().select();
		}else if(div.data('change')=='email'){
			div2.closest('div').html('<input type="text" name="email" data-value="'+current_value+'" placeholder="'+current_value+'">');
			$("#emailBox input").focus().select();
		}

	});


	//change email
	$("#emailBox").on('keypress blur','input',function(e) {
		var div = $(this);
		var update={};
	    if(e.which == 13 || e.type=='focusout') {
	    	if(div.val()!=''){
		    	update.email = div.val();
		    	var findLabel = div.closest('div').find('label');
		    	if(findLabel.length>0){
		    		findLabel.html('<label> updating...</label>');
		    	}else{
		    		div.closest('div').append('<label> updating...</label>')
		        }
		        $.post(basedomain+'home/changeProfile',update,function(response){
		        	try{
		        		if(response.status==1){
		        			div.closest('div').html('<p class="email">'+update.email+'</p><a href="#" class="clickable" data-change="email">[edit]</a>');
		        		}else{
		        			div.closest('div').find('label').html(response.message);
		        		}
		        	}catch(e){}
		        },'json').error(function() { 
					window.location=basedomain+'login/age_check';
				 });
		    }else{
		    	div.closest('div').html('<p class="email">'+div.data('value')+'</p><a href="#" class="clickable" data-change="email">[edit]</a>');
		    }
	    }
	});

	//change phone number
	$("#phoneBox").on('keypress blur','input',function(e) {
		var div = $(this);
		var update={};
	    if(e.which == 13 || e.type=='focusout') {
	    	if(div.val()!=''){
		    	update.phone = div.val();
		    	var findLabel = div.closest('div').find('label');
		    	if(findLabel.length>0){
		    		findLabel.html('<label> updating...</label>');
		    	}else{
		    		div.closest('div').append('<label> updating...</label>')
		        }
		        $.post(basedomain+'home/changeProfile',update,function(response){
		        	try{
		        		if(response.status==1){
		        			//div.closest('div').html('<p class="phone">'+update.phone+'</p><a href="#" class="clickable" data-change="phone">[edit]</a>');
		        			div.closest('div').html('<p class="phone" data-change="phone">'+update.phone+'</p><a href="#" class="clickable iconEdit" data-change="phone">&nbsp;</a>');
		        		}
		        	}catch(e){}
		        },'json').error(function() { 
					window.location=basedomain+'login/age_check';
				 });
		    }else{
		    	if(div.data('value')=='Input your phone number'){
		    		div.closest('div').html('<input id="phone_number" type="text" placeholder="'+div.data('value')+'" data-value="'+div.data('value')+'" name="phone_number">');
		    	}else{
		    		div.closest('div').html('<p class="phone" data-change="phone">'+div.data('value')+'</p><a href="#" class="clickable iconEdit" data-change="phone">&nbsp;</a>');
		    	}

		    }
	    }
	});


	$('#closeTrivia').on('click',function(event){
		event.preventDefault();
		location.reload();
	});
	
	$('#submitTrivia').on('click',function(event){
		event.preventDefault();
		var opt = {};
		var allow=1;
		var emptydiv='';
		opt.pattern = triv_pattern;
		opt.reasonToWin = $("textarea[name='reasonToWin']").val();
		if(opt.reasonToWin==''){
			allow = 0;
			emptydiv +="textarea[name='reasonToWin']";
		}else{
			$("textarea[name='reasonToWin']").closest('div').find('.asterix').html('');
		}
		opt.hobby = $("select[name='hobby']").val();
		if(opt.hobby==0){
			allow = 0;
			if(emptydiv!=''){
				emptydiv+=',';
			}
			emptydiv +="select[name='hobby']";
		}else{
			$("select[name='hobby']").closest('div').find('.asterix').html('');
		}
		opt.weekend = $("select[name='weekend']").val();
		if(opt.weekend==0){
			allow = 0;
			if(emptydiv!=''){
				emptydiv+=',';
			}
			emptydiv +="select[name='weekend']";
		}else{
			$("select[name='weekend']").closest('div').find('.asterix').html('');
		}
		opt.genre = $("select[name='genre']").val();
		if(opt.genre==0){
			allow = 0;
			if(emptydiv!=''){
				emptydiv+=',';
			}
			emptydiv +="select[name='genre']";
		}else{
			$("select[name='genre']").closest('div').find('.asterix').html('');
		} 
		var htm='';
		
		if(allow==1){
			$('.asterix').html('');
			$('#finishTrivia').html('<div class="loaders"><img src="'+assets_domain+'images/loader.gif"></div>');
			$.post(basedomain+'home/submit',opt,function(response){
				try{
					if(response.status==1){
						htm+="<h1>THANKS </h1>";
			            htm+="<p>Tetap datang ke event - event Heineken lainnya untuk meningkatkan kesempatan lo menjadi salah satu orang yang terpilih untuk pergi ke Korea & menikmati Sensation Wicked Wonderland. <br />";
			            htm+="(and don't forget to keep spreading the words on social media).<br />";
			            htm+="Siap - buat bikin impian lo menjadi kenyataan!</p>";
					}else{
						htm+="<h1>Oops! </h1>";
			            htm+="<p>"+response.message+"</p>";
					}
					$('#finishTrivia').html(htm);
					$.fancybox({
			            href: '#popup-quiz4'
			        });
					/*$(document).scrollTop(0);
					$.fancybox.close();
					$('#popup-quiz4').show();*/
					setTimeout(function(){
	                         location.reload();
	                    }, 20000);
					
				}catch(e){}
			},'json').error(function() { 
						window.location=basedomain+'login/age_check';
					 });
		}else{
			$(emptydiv).closest('div').find('.asterix').html('<img style="width:6%" src="'+assets_domain+'images/icon_warning.png" />');
		}
	});

	
	$('a.nextTrivia').on('click',function(event){
		event.preventDefault();
		console.log(triv_idx);
		if(triv_idx<list_length){
			var value = $("input[name='triv_"+triv_idx+"']:checked").val();
			console.log(value);
			if(value>0&&value!='undefined'){
				triv_idx++;
				$.fancybox({
		            href: '#triv_'+triv_idx
		        });
				if(triv_idx==list_length){
					loadPersonality();
				}
			}
		}	
	})
});

function loadPersonality(){
	var i=0;
	triv_pattern='';
	for(i;i<list_length;i++){
		if(i<list_length){
			if(i!=0){
				triv_pattern += ',';
			}
			triv_pattern += $("input[name='triv_"+i+"']:checked").val();
		}
	}
	console.log(triv_pattern);
	$('#triviaResult').html('<div class="loaders"><img src="'+assets_domain+'images/loader.gif"/></div>')
	$.post(basedomain+'home/load',{'pattern':triv_pattern},function(response){
		try{
			if(response.status==1){
				var htm='';
				var result = response.data;
				htm+='<h1>'+(result.personality_type).toUpperCase()+'</h1>';
				htm+='<p>'+result.personality_desc+'</p>';
				$('#triviaResult').html(htm);
				$('#personalityBox').html('<h2>'+(result.personality_type).toUpperCase()+'</h2>');
				if(notSubmitTheForm=='1'){
					
					setTimeout(function(){
                         location.reload();
                    }, 10000);    
				}
			}
		}catch(e){}
	},'JSON');
}

function shareFB(fb_name,fb_link,fb_img,fb_user,fb_post){
	FB.ui({
		method: 'feed',
		name: fb_name,
		link: fb_link,
		picture: fb_img,
		caption: fb_user,
		description: fb_post
	});						  
}





