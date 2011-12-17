$(document).ready(function(e) {
    
	$("#nav_about").navigate ("nav_about");
	$("#nav_features").navigate("nav_features");
	$("#nav_documentation").navigate("nav_documentation");
	$("#nav_faq").navigate("nav_faq");
	$("#nav_support").navigate("nav_support");
	
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