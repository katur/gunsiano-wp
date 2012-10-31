$(function(){
	togglePhone();
	toggleLab();
	toggleRoom();
	selectDefaults();
})

selectDefaults = function(){
	$("[data-phone]").first().trigger("click");
	$("[data-lab]").first().trigger("click");
	$("[data-address-type]").first().trigger("click");
}

togglePhone = function(){
	$("[data-phone]").click(function(e){
		e.preventDefault(); // keeps page stationary after click
		$("[data-phone]").removeClass("active"); // unset previous active link
		$(this).addClass("active"); // set this link to active
		phoneNumber = $(this).attr("data-phone");
		$("#phone").text(phoneNumber).attr("href", "tel:" + phoneNumber);
	});
}

toggleLab = function(){
	$("[data-lab]").click(function(e){
		e.preventDefault(); // keeps page stationary after click
		$("[data-lab]").removeClass("active"); // unset previous active link
		$(this).addClass("active"); // set this link to active
		updateAddress();
	});
}

toggleRoom = function(){
	$("[data-address-type]").click(function(e){
		e.preventDefault(); // keeps page stationary after click
		$("[data-address-type]").removeClass("active"); // unset previous active link
		$(this).addClass("active"); // set this link to active
		updateAddress();
	});
}

updateAddress = function(){
	labName = $(".active[data-lab]").attr("data-lab");
	mailType = $(".active[data-address-type]").attr("data-address-type");
	if (mailType == "packages") {
		room = "LL2-117";
	} else if (mailType == "regular") {
		room = "8th floor";
	} else if (mailType == "visiting" && labName == "Kris Gunsalus") {
		room = "6th floor";
	} else if (mailType == "visiting" && labName == "Fabio Piano") {
		room = "7th floor";
	} else {
		room = null;
	}
	if (labName != null) {
		$("#laboratory").text(labName);
	}
	if (room != null) {
		$("#floor-detail").text(room);
	}
}