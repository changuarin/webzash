/*
 * Rexter S Matic 0.13.15 - JavaScript Vector Library
 *
 * Copyright (c) 2015 Rexter S. Matic
 * Licensed under the MIT (http://www.opensource.org/licenses/mit-license.php) license.
 */
function Format(amount)
{
	amount=String(amount).replace(/,/g,'');
	amount=String(Number(amount).toFixed(2));
	var delimiter=',';
	var a=amount.split('.',2);
	var d=a[1];
	var i=parseInt(a[0]);
	if(isNaN(i)){return '';}
	var minus='';
	if(i<0){minus='-';}
	i=Math.abs(i);
	var n=new String(i);
	var a=[];
	while(n.length>3){
		var nn=n.substr(n.length-3);
		a.unshift(nn);
		n=n.substr(0,n.length-3);
	}
	if(n.length>0){
		a.unshift(n);
	}
	n=a.join(delimiter);
	if(d.length<1){
		amount=n;
	}else{
		amount=n+'.'+d;
	}amount=minus+amount;
	return amount;
}

function N(v)
{
	return parseFloat(String(v).replace(/,/g,''));
}

function revstr(s){ var t="";var u=N(s.length)-1;for(i=0;i<=u;i++){t+=s[u-i];}return t;}

function toWords(s){
	var th=['','thousand','million','billion','trillion'];
	var dg=['zero','one','two','three','four','five','six','seven','eight','nine'];
	var tn=['ten','eleven','twelve','thirteen','fourteen','fifteen','sixteen','seventeen','eighteen','nineteen'];
	var tw=['twenty','thirty','forty','fifty','sixty','seventy','eighty','ninety'];
	s=String(s).replace(/[\, ]/g,'');
	if(s!=parseFloat(s))return 'not a number';
	var x=s.indexOf('.');
	if(x==-1)x=s.length;
	if(x>15)return'too big';
	var n=s.split('');
	var str='';
	var sk=0;
	for(var i=0;i<x;i++)
	{
		if((x-i)%3==2)
		{
			if(n[i]=='1')
			{
				str+=tn[Number(n[i+1])]+' ';
				i++;
				sk=1;
			}else if(n[i]!=0){str+=tw[n[i]-2]+' ';sk=1;
		}
	}else if(n[i]!=0)
	{
		str+=dg[n[i]]+' ';
		if((x-i)%3==0)str+='hundred ';
		sk=1;
	}if((x-i)%3==1)
	{
		if(sk)str+=th[(x-i-1)/3]+' ';sk=0;
	}
}if(x!=s.length){var y=s.length;var cent='';for(var i=x+1;i<y;i++)cent+=n[i];if(cent.length==1)cent+='0';str+='& '+cent+'/100 ';}if(str)str=str.replace(/\s+/g,' ')+'PESOS ONLY';return str.toUpperCase();
}