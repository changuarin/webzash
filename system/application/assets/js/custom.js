/* Confirm click */

$(document).ready(function() {
	$(".confirmClick").click( function() { 
	    if ($(this).attr('title')) {
		var question = 'Are you sure you want to ' + $(this).attr('title').toLowerCase() + '?';
	    } else {
		var question = 'Are you sure you want to do this action?';
	    }
	    if ( confirm( question ) ) {
		[removed].href = this.src;
	    } else {
		return false;
	    }
	});
	$("#tooltip-target-1").ezpz_tooltip();
	$("#tooltip-target-2").ezpz_tooltip();
	$("#tooltip-target-3").ezpz_tooltip();
});

function notiplug( options ) {
  /*options = {
        body: "This is the body of the notification",
        icon: "icon.jpg",
        dir : "ltr"
    };*/ 
  if (!("Notification" in window)) {
    alert("This browser does not support desktop notification");
  }

  else if (Notification.permission === "granted") {
  
  var notification = new Notification(options.title,options);
  }

  else if (Notification.permission !== 'denied') {
    Notification.requestPermission(function (permission) {

      if (!('permission' in Notification)) {
        Notification.permission = permission;
      }

      if (permission === "granted") {
        /*var options = {
              body: "This is the body of the notification",
              icon: "icon.jpg",
              dir : "ltr"
          };*/
        var notification = new Notification(options.title,options);
      }
    });
  }

}

function autocheck( z )
{
	var t=String(parent.document.location),url='';
	if(t.indexOf('.php/')>0)url='../../index.php/z/zz';
	else url='index.php/z/zz';
	$.post(url,{a:z},function(y)
	{
		if(y.a)
		{
			notiplug({
				body:y.c,
				icon:'',
				dir:'ltr',
				title:'1 FT request from '+y.d,
				
			});
		}
		t_=setTimeout( function(){ autocheck( t_ ) }, 3000 );
	},
	'json');
	clearTimeout( z );
}