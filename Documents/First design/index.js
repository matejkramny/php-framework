$(document).ready(function(e) {
    
	$("#nav_about").navigate ("nav_about");
	$("#nav_features").navigate("nav_features");
	$("#nav_documentation").navigate("nav_documentation");
	$("#nav_faq").navigate("nav_faq");
	$("#nav_support").navigate("nav_support");
	
	$("#test_button").click(function () {
		$("#messages").insertMessage ("You have been logged out.", 0 , "");
	});
	
	$("#container").updateContainerHeights();
	$().moveSlider(sliderOrigin);
});

$.fn.updateContainerHeights = function () 
{
	$("#cont_left").css ("height", $("#cont_content").innerHeight());
	$("#cont_right").css ("height", $("#cont_content").innerHeight());
}

$.fn.navigate = function (name)
{
	$(this, "#" + name + " a").mouseover(function (e) {
		$("#nav_hover").moveSlider(name);
	}).mouseout(function(e) {
        $("#nav_hover").moveSlider(null);
    }).click (function () {
		$("#nav_hover").moveSlider(name);
		sliderOrigin = sliderAt = name;
		
		$("#cont_content").fetchPage (name);
	});
}

$.fn.fetchPage = function (id)
{
	var href = $("#" + id + " a").attr("href");
	if (href.length < 1)
		return false;
	
	href = href.substr(0, 1);
	
	var message = null;
	$.ajax({
		url: "/" + href,
		data: "?sendPageContent=true",
		dataType: "html",
		type: "POST",
		success: function (data)
		{
			$("#cont_content").html(data);
			$().updateContainerHeights();
		},
		error: function ()
		{
			$().insertMessage("Sorry, but the page could not be loaded asynchronously. Redirecting...", 2000, "_red");
			setTimeout (function () {
				window.location = "/" + href;
			}, 2000);
		},
		beforeSend: function ()
		{
			message = $().insertMessage("Loading requested page asynchronously. Please wait.", 0);
		},
		complete: function ()
		{
			$(message).removeMessage();
		}
	});
};

$.fn.moveSlider = function(moveto) {
	var speed = 'fast';
	
	if (moveto == null)
	{
		speed = 'slow';
		moveto = sliderOrigin;
	}
	
	var pos = 0;
	var newWidth = 0;
	
	$("#navigation ul li").each(function(index, element) {
        var s = $(element).css ("width");
		s.substr(0, s.length - 2);
		
		if ($(element).attr ("id") == moveto)
		{
			newWidth = parseInt(s);
			return false;
		}
		
		pos += parseInt (s);
    });
	
	$("#nav_hover").stop(true).animate({
		left: pos,
		width: newWidth
	}, speed);
	$("#nav_hover_bg").stop(true).animate({
		width: newWidth - 40
	}, speed);
};

var messageCount = 0;
$.fn.insertMessage = function (message, expire, color)
{
	function removeMessages ()
	{
		$("#messages").html ("");
	}
	function addSpacer ()
	{
		$("#messages").prepend('<div class="message_divider"></div>');
	}
	function addMessage (msg)
	{
		if (typeof color === "undefined" || color != "_red")
		{
			color = "";
		}
		$("#messages").prepend('<div id="easing_temp" class="message' + color + '">'
			+ '<p>' + msg + '</p>'
			+ '<div class="message_close_button"></div>'
			+ '</div>');
		
		var mID = $("#easing_temp");
		var timer = null;
		if (typeof expire === "undefined" || expire != 0)
		{	timer = setTimeout (function () {
				$(mID).removeMessage();
			}, expire);
		}
		
		$(mID).find ("div").click (function () {
			$(mID).removeMessage();
		}).mouseout (function () {
			$(this).stop(true).animate({ opacity: 0.3 }, 100, function () {
				$(this).css("background", "url(images/no_repeat.png) 0px -494px");
				$(this).animate({ opacity: 1 }, "fast");
			});
		}).mouseover (function () {
			$(this).stop(true).animate({ opacity: 0.3 }, 100, function () {
				$(this).css("background", "url(images/no_repeat.png) 0px -450px");
				$(this).animate({ opacity: 1 }, 'fast');
			});
		});
				
		$("#easing_temp").hide ().show ('slow').removeAttr ("id");
		
		return mID;
	}
	
	if (messageCount == 0)
	{
		removeMessages ();
		var m = addMessage (message);
	}
	else
	{
		addSpacer ();
		var m = addMessage (message);
	}
	
	messageCount++;
	
	return m;
};

$.fn.removeMessage = function ()
{
	if ($(this).next().attr("class") == "message_divider")
	{
		$(this).next().remove();
	}
	$(this).hide('fast', function () { $(this).remove(); });
				
	if (messageCount > 0)
		messageCount--;
}