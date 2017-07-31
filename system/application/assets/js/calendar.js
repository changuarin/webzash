function showCal(obj,tgtEl)
		{
			targetEl=tgtEl;
			createCalendar(obj);
			var calTable=obj.getElementById('calenderTable');
			var positions=[0,0];
			var positions=getParentOffset(tgtEl,positions);
			calTable.style.left=positions[0]+'px';
			calTable.style.top=(Number(positions[1])+Number(tgtEl.offsetParent.offsetHeight))+'px';
			calTable.style.display='block';var matchDate=new RegExp('^([0-9]{2})-([0-9]{2})-([0-9]{4})$');
			var m=matchDate.exec(targetEl.value);
			if(m==null)
			{
				if(String(targetEl.value).length>0){var m=String(targetEl.value).split(' ');
				m[0]=getMonthValue(m[0]);
				m[1]=Number(m[1]);
				trs=createCalender(obj,m[2],m[0],m[1]);
				showCalenderBody(obj,trs);
				} else {
					trs=createCalender(obj,false,false,false);
					showCalenderBody(obj,trs);
				}
			} else {
				m[1]=Number(m[1]);
				m[2]=Number(m[2]);
				m[2]=m[2]-1;
				trs=createCalender(obj,m[3],m[2],m[1]);
				showCalenderBody(obj,trs)
				;
			}
		}
		function getRealYear(dateObj){return(dateObj.getYear()%100)+(((dateObj.getYear()%100)<39)?2000:1900);}function getDaysPerMonth(month,year){if((year%4)==0){if((year%100)==0&&(year%400)!=0)return monthMaxDays[month];return monthMaxDaysLeap[month];}else return monthMaxDays[month];}function createCalender(obj,year,month,day){var curDate=new Date();var curDay=curDate.getDate();var curMonth=curDate.getMonth();var curYear=getRealYear(curDate);if(!year){var year=curYear;var month=curMonth;}var yearFound=0;for (var i=0;i<obj.getElementById('selectYear').options.length;i++){if(obj.getElementById('selectYear').options[i].value==year){obj.getElementById('selectYear').selectedIndex=i;yearFound=true;break;}}if(!yearFound){obj.getElementById('selectYear').selectedIndex=0;year=obj.getElementById('selectYear').options[0].value;}obj.getElementById('selectMonth').selectedIndex=month;var fristDayOfMonthObj=new Date(year,month,1);var firstDayOfMonth=fristDayOfMonthObj.getDay();continu=true;firstRow=true;var x=0;var d=0;var trs=[];var ti=0;while(d<=getDaysPerMonth(month,year)){if(firstRow){trs[ti]=obj.createElement("TR");if (firstDayOfMonth>0){while(x<firstDayOfMonth){trs[ti].appendChild(obj.createElement("TD"));x++;}}firstRow=false;var d=1;}if (x%7==0){ti++;trs[ti]=obj.createElement("TR");}if (day&&d==day){var setID='calenderChoosenDay';var styleClass='choosenDay';var setTitle='this day is currently selected';}else if(d==curDay&&month==curMonth&&year==curYear){var setID='calenderToDay';var styleClass='toDay';var setTitle='this day today';}else{var setID=false;var styleClass='normalDay';var setTitle=false;}var td=obj.createElement("TD");td.className=styleClass;if(setID){td.id=setID;}if(setTitle){td.title=setTitle;}td.align='center';td.onmouseover=function(){highLiteDay(this);};td.onmouseout=function(){deHighLiteDay(this);};if(targetEl)td.onclick=function(){pickDate(year,month,this.innerHTML);closeCalender(obj);};else td.style.cursor='default';td.appendChild(obj.createTextNode(d));trs[ti].appendChild(td);x++;d++;}return trs;}function showCalenderBody(obj,trs){var calTBody=obj.getElementById('calender');while(calTBody.childNodes[0]){calTBody.removeChild(calTBody.childNodes[0]);}for(var i in trs){calTBody.appendChild(trs[i]);}}function setYears(obj){var t=new Date();var sy=1947;var ey=t.getFullYear()+2;var curDate=new Date();var curYear=getRealYear(curDate);if(sy)startYear=curYear;if(ey)endYear=curYear;obj.getElementById('selectYear').options.length=0;var j=0;for(y=ey;y>=sy;y--){obj.getElementById('selectYear')[j++]=new Option(y,y);}}function closeCalender(obj){obj.getElementById('calenderTable').style.display='none';}function highLiteDay(el){el.className='hlDay';}function deHighLiteDay(el){if(el.id=='calenderToDay')el.className='toDay';else if(el.id=='calenderChoosenDay')el.className='choosenDay';else el.className='normalDay';}function pickDate(year,month,day){month++;day=day<10?'0'+day:day;month=month<10?'0'+month:month;if(!targetEl){alert('target for date is not set yet');}else{targetEl.value=monthName[Number(month)]+' '+day+' '+year;targetEl.focus();}}function grayOut(vis, options){var options=options||{};var zindex=options.zindex||1;var opacity=options.opacity||60;var opaque=(opacity/100);var bgcolor=options.bgcolor||'#000000';var dark=document.getElementById('darkenScreenObject');if(!dark){var tbody=document.getElementsByTagName("body")[0];var tnode=document.createElement('div');tnode.style.position='absolute';tnode.style.margin='0px';tnode.style.top='0px';tnode.style.left='0px';tnode.style.overflow='hidden';tnode.style.display='none';tnode.id='darkenScreenObject';tbody.appendChild(tnode);dark=document.getElementById('darkenScreenObject');}if(vis){if(document.body&&(document.body.scrollWidth||document.body.scrollHeight)){var pageWidth=document.body.scrollWidth+'px';var pageHeight=document.body.scrollHeight+'px';}else if(document.body.offsetWidth){var pageWidth=document.body.offsetWidth+'px';var pageHeight=document.body.offsetHeight+'px';}else{var pageWidth='100%';var pageHeight='100%';}dark.style.opacity=opaque;dark.style.MozOpacity=opaque;dark.style.filter='alpha(opacity='+opacity+')';dark.style.zIndex=zindex;dark.style.backgroundColor=bgcolor;dark.style.width= pageWidth;dark.style.height= pageHeight;dark.style.display='block';}else{dark.style.display='none';}}}catch(err){alert(err);}