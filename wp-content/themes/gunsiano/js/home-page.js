$(function(){
	toggleThumb();
	stayHere();
	selectDefaultThumbnail();
})

selectDefaultThumbnail = function(){
	$("[data-research-area]").first().trigger("mouseenter");
}

toggleThumb = function(){
	$("[data-research-area]").hover(function(e){
		$("[data-research-area]").removeClass("active-thumbnail"); // unset previous active link
		$(this).addClass("active-thumbnail"); // set this link to active
		$("#research-area").text($(this).attr("data-research-area"));
		$("#theater").empty();
		$("#theater").append($(this).attr("data-visual"));
	});
}

stayHere = function(){
	$("[data-research-area]").click(function(e){
		e.preventDefault(); // keeps page stationary after click
	});
}