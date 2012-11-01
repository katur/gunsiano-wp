$(function(){
	filterByAuthor();
	filterByDate();
	selectDefaultPublications();
})

selectDefaultPublications = function(){
	$("[data-author]").trigger("click");
	$("[data-date]").slice(0,2).trigger("click");
}

filterByAuthor = function(){
	$("[data-author]").click(function(e){
		e.preventDefault(); // keep page stationary after click
		if ($(this).hasClass("active")) { // if the link was previously active
			$(this).removeClass("active"); // unset active
		} else {
			$(this).addClass("active"); // set active
		}
		displayFilteredPublications();
	});
}

filterByDate = function(){
	$("[data-date]").click(function(e){
		e.preventDefault(); // keep page stationary after click
		if ($(this).hasClass("active")) { // if the link was previously active
			$(this).removeClass("active"); // unset active
		} else {
			$(this).addClass("active"); // set active
		}
		displayFilteredPublications();
	});
}

displayFilteredPublications = function(){
	$(".publication").hide();
	$(".active[data-author]").each(function(){
		author = $(this).attr("data-author");
		$(".active[data-date]").each(function(){
			date = $(this).attr("data-date");
			$(".publication").each(function(){
				if ($(this).find(".authors").text().match(author) &&
				$(this).find(".date").text().match(date)) {
					$(this).show();
				}
			});
		});
	});
}