<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('trims'))
{
	function trims($a)
	{
		$a=str_replace("\n", '', $a);
	    $a=str_replace("\t", '', $a);
	    $a=str_replace("  ", '', $a);
	    return $a;
	}
}


if ( ! function_exists('n'))
{
	function n($a)
	{
		return str_replace(',', '', $a);
	}
}


if ( ! function_exists('rexjsfunction'))
{
	function rexjsfunction()
	{
		return trims("function N(a){a=String(a).replace(/,/g,'');return Number(a);}
		function Format(amount)
		{
			amount=String(amount).replace(/,/g,'');
			amount=String(Number(amount).toFixed(2));
			var delimiter=',';
			var a=amount.split('.',2);
			var d=a[1];
			var i=parseInt(a[0]);
			if(isNaN(i))
			{ return '';}
			var minus='';
			if(i<0)
			{
				minus='-';
			}
			i=Math.abs(i);
			var n=new String(i);
			var a=[];
			while(n.length>3)
			{
				var nn=n.substr(n.length-3);
				a.unshift(nn);
				n=n.substr(0,n.length-3);
			}
			if(n.length>0)
			{
				a.unshift(n);
			}
			n=a.join(delimiter);
			if(d.length<1)
			{
				amount=n;
			}else{
				amount=n+'.'+d;
			}
			amount=minus+amount;
			return amount;
		}");
	}
}