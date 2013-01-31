$(function(){
	hoverClone();
	cloneCycle();
	commentPopup();
});

// on ahringer-library page, when hovered over a well, fade in popup div with clone
function hoverClone(){
	$('.well, .well-small').mouseover(function(){
		wellPosition = $(this).attr('id');
		cloneInfo = $(this).next('.invisible').html();
		if (cloneInfo == '<br>')
			cloneInfo = 'no clone';
		$('#hover-clone').html(wellPosition + ':<br>' + cloneInfo).fadeIn();
	});

	$('.plate, .plate-small').mouseleave(function(){
		$('#hover-clone').fadeOut();
	});
}

// on add-culture page, after clicking on a well, cycle through the statuses/colors
function cloneCycle(){
	$('.well-medium').click(function(){
		status = $(this).attr('class').replace(/well-medium status/, '');
		$(this).removeClass('status' + status);
		switch(status) {
			case '0': 
				status = 2;
				break;
			case '1':
				status = 0;
				break;
			case '2':
				status = 1;
				break;
		}
		$(this).addClass('status' + status);
		$(this).next('input').val(status);
	});
}

// on the new_stamp page, after clicking a well's comment button, fade in a div with the comment textarea.
function commentPopup() {
		$('.comment-button').click(function(){
			well_position = $(this).attr('id');
			position = $(this).position();
			$('.comment-popup#' + well_position).css('top', position.top + 20).css('left', position.left + 20).fadeIn();
		});
		
		//click on the spanned 'X' (closeButton) to fade out the div
		$('.close-button').click(function(){
			$('.comment-popup').fadeOut();
		});
}
