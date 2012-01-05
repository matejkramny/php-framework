$(document).ready(function(e) {
    
	$("#nav_about").navigate ("nav_about");
	$("#nav_features").navigate("nav_features");
	$("#nav_documentation").navigate("nav_documentation");
	$("#nav_faq").navigate("nav_faq");
	$("#nav_support").navigate("nav_support");
	
	$("#test_button").click(function () {
		$("#messages").insertMessage ("Hello to framework website. it is great", 4000 , "_red");
	});
	
});

$.fn.navigate = function (name)
{
	$(this, "#" + name + " a").mouseover(function (e) {
		$("#nav_hover").moveSlider(name);
	}).mouseout(function(e) {
        $("#nav_hover").moveSlider(null);
    });
}

var sliderAt = "nav_about";
var sliderOrigin = "nav_about";
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
		if (expire != 0)
		{	var timer = setTimeout (function () {
				if ($(mID).next().attr("class") == "message_divider")
				{
					$(mID).next().remove();
				}
				$(mID).hide('fast', function () { $(this).remove(); });
			}, expire);
		}
		
		$("#easing_temp").hide ().show ('slow').removeAttr ("id");
	}
	
	if (messageCount == 0)
	{
		removeMessages ();
		addMessage (message);
	}
	else
	{
		addSpacer ();
		addMessage (message);
	}
	
	messageCount++;
};