$(function(){
	filterPublications();
	selectDefaultPublications();
})

selectDefaultPublications = function(){
	$("[data-author]").trigger("click");
	$("[data-date]").slice(0,1).trigger("click");
}

filterPublications = function(){
	$("[data-date], [data-author]").click(function(e){
		e.preventDefault(); // keep page stationary after click
		$(this).toggleClass("active"); // activate or unactivate the link
		$(".publication").hide(); // hide all publications
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
	});
}

/*
toggleDate = function(){
	$("[data-date]").click(function(e){
		e.preventDefault(); // keeps page stationary after click
		$("[data-data]").removeClass("active"); // unset previous active link
		$(this).addClass("active"); // set this link to active
		updateAddress();
	});
}

filterPublications = function(){
	$("[data-author]").click(function(e){
		e.preventDefault(); // keep page stationary after click
		$(this).toggleClass("active"); // activate or unactivate the link
		$(".publication").hide(); // hide all publications
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
	});
}
*/