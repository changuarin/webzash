function ddl(a,b)
{
	a.css('display','none');
	a.wrapAll('<label id="za-'+a.attr('id')+'">').parent();

	$('#za-'+a.attr('id')).append('<span class="ddmenu"status=""id="zb-'+a.attr('id')+'" '+
		'style="display: inline-block;cursor: default; overflow: hidden;">'+
		'<span tabindex="0" style="display: inline-block; overflow: hidden; white-space: nowrap; '+
		'border: 1px solid #d3d3d3; padding: 2px 3px 3px 3px; width: '+
		(b==undefined?'100px;':(b.width==undefined?'100px':b.width))+'">'+
			'<span id="ze-'+a.attr('id')+'" title="'+(b==undefined?'Please Select...':b.placeholder)+'" '+
			'style="display: inline-block; white-space: nowrap; '+
			'overflow: hidden;"value="">'+(b==undefined?'Please Select...':b.placeholder)+'</span>'+
		'</span>'+
	'</span>'+

	'<div class="ddmenu"id="zc-'+a.attr('id')+'" style="position: absolute; z-index: 1;">'+
		'<div style="overflow-y: auto;">'+
		'</div>'+
	'</div>');

	var v1='',v2='';
	a.find('option').each(function(i)
	{
		var v0=$(this).is(':selected')?'checked':'';
		if(v0)
		{
			v1+=$(this).val()+',';
			v2+=$(this).text()+', ';
		}
		$('#zc-'+a.attr('id')).append('<div class="ddms"style="white-space: nowrap; width:'+
		(b==undefined?'100px;':(b.width==undefined?'100px':b.width))+'">'+
			'<input type="checkbox" id="zd-'+a.attr('id')+'-i'+i+'" class="active" '+
			'tabindex="'+i+'" index="'+i+'" value="'+$(this).val()+'"'+v0+'>'+
			'<label for="zd-'+a.attr('id')+'-i'+i+'" style="cursor: default;">'+$(this).text()+'</label>'+
		'</div>');
	});
	if(v1)
	{
		v2=v2.substr(0,v2.length-2);
		v1=v1.substr(0,v1.length-1);
		$('#ze-'+a.attr('id')).attr('value',v1).attr('title',v2).html(v2);
	}

	$('.ddms').hover(function()
	{
		if($(this).hasClass('active'))
			$(this).removeClass('active');
		else $(this).addClass('active');
	}).find('input[type=checkbox]').change(function()
	{
		var z=0,y='',x='',w=null;
		$('.ddms').find('input[type=checkbox]').each(function(i)
		{
			if($(this).is(':checked'))
			{
				z++;
				y+=$(this).parent().find('label').html()+', ';
				x+=$(this).val()+',';
			}
		});
		if(z==0)$('#ze-'+a.attr('id')).attr('value','').attr('title',(b==undefined?'Please Select...':b.placeholder)).html((b==undefined?'Please Select...':b.placeholder));
		else
		{
			y=y.substr(0,y.length-2);
			x=x.substr(0,x.length-1);
			$('#ze-'+a.attr('id')).attr('value',x).attr('title',y).html(y);
		}
	});

	$(document).find('body').unbind('click').click(function()
	{
		if(!$('.ddms').hasClass('active')&&!$('#zb-'+a.attr('id')).hasClass('active'))
		{
			$('#zb-'+a.attr('id')).attr('status','');
			$('#zc-'+a.attr('id')).css('left','-33000px').css('top','-33000px');
		}
	});

	a.unwrap();
	
	$('#zc-'+a.attr('id')).css('left','-33000px').css('top','-33000px');
	$('#zb-'+a.attr('id')).unbind('click').click(function()
	{
		if($(this).attr('status')!='active')
		{
			var t=$(this).position().top+$(this).outerHeight()+'px';
			var l=$(this).position().left+'px';
			$('#zc-'+a.attr('id')).css('top',t);
			$('#zc-'+a.attr('id')).css('left',l);
			$(this).attr('status','active');
		}else{
			$(this).attr('status','');
			$('#zc-'+a.attr('id')).css('left','-33000px').css('top','-33000px');
		}
	}).hover(function()
	{
		if($(this).hasClass('active'))
			$(this).removeClass('active');
		else $(this).addClass('active');
	});
	
}