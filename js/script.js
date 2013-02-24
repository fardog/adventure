/* Author: Far Dog LLC
 * This is the javascript for Adventure.
 */

$(document).ready(function() {
	// Add Confirmation dialogs for all Deletes
	$("a.delete-content").click(function(event) {
		return ConfirmDelete('Content');
	});
	$("a.delete-links").click(function(event) {
		return ConfirmDelete('Action');
	});

	$("a.deny-suggestion").click(function(event) {
		return ConfirmDeny('Suggestion');
	});

	$("a.denyall-suggestions").click(function(event) {
		return confirm('Do you really want to deny all suggestions?');
	});

	$("a.closeall-suggestions").click(function(event) {
		return confirm('You are about to close all suggestions, this cannot be undone!');
	});

	//timeout to dismiss flashdata if it exists
	var flashTimeout = setTimeout(function(){
		$("#flashdata").fadeOut(300, function () {
			$("#flashdata").remove();
		});
	}, 5000);

	//close button for flashdata
	$("a#flashclose").click(function(event) {
		clearTimeout(flashTimeout);
		$("#flashdata").fadeOut(300, function () {
			$("#flashdata").remove();
		});
	});

	//bit for hiding edit links from search engines so they don't inadvertently delete my shit, and they have
	$("a.editLink").click(function(event) {
		var editId = $(this).attr('id');
		if($(this).hasClass('adventure')) window.location.href = "/adventure/edit/"+editId+"/";
		else if($(this).hasClass('page')) window.location.href = "/page/edit/"+editId+"/";
	});

	//Bit for getting history
	$("a#getHistory").click(function(event){
		if($('#history').hasClass('open')) {
			$('#history').removeClass('open');
			$('#history ul').remove();
			return;
		}
		$('#history ul').remove();
		$('#history').addClass('open').append('<ul></ul>');
		var advId = $('a#getHistory').attr('class');
		$.getJSON('/history/get/'+advId, function(jd) {
			$.each(jd, function(i, object) {
				var isSave = '';
				if(object.save == true) isSave = " class=\"strong\"";
				$('#history ul').append('<li'+isSave+'><span class="date">'+object.date+'</span><a href="'+object.url+'">'+object.description+'</a></li>');
			});
		});
	});

	//Bit for showing suggestions
	$("a#suggestAction").click(function(event) {
		if($('#suggestDiv').hasClass('suggestOpen')) {
			$('#suggestDiv').removeClass('suggestOpen');
			$('#suggestDiv').hide();
			$('#history').removeClass('open');
			return;
		}
		$('#suggestDiv').addClass('suggestOpen');
		$('#suggestDiv').show();
		$('#history').addClass('open')
	});

	//Bit for saving games
	$("a#saveGame").click(function(event){
		var saveId = $('a#saveGame').attr('class');
		$.getJSON('/history/save/'+saveId, function(jd) {
			var result = jd.success;
			if(result == true) result = "Success!";
			else result = "Failure!";
			$("a#saveGame").text(result);
		});
	});

	//Show/Hide for a bunch of junk
	$("a.showHide").click(function(event) {
		if(this.text.toString().search(/show/i) > -1) {
			this.innerHTML = this.text.toString().replace(/show/i, 'Hide');
			$(this).next('div').show();
		}
		else {
			this.innerHTML = this.text.toString().replace(/hide/i, 'Show');
			$(this).next('div').hide();
		}
	});

	//Date picker for suggestion timeout junk
	$(".datepicker").datepicker({
			dateFormat: 'yy-mm-dd',
			defaultDate: +7,
			minDate: 0,
			showOtherMonths: true,
			selectOtherMonths: true
		});
	//Link to add "no limit" for date
	$("a#timeoutNone").click(function(){ $("#suggestTimeout").val('Forever'); });


	//Event tracking. I promise this is because I'm learning, not because I'm trying to "big brother" you.
	$("a#getHistory").click(function(){ _gaq.push(['_trackEvent', 'Bookmark', 'Click', 'View History']); });
	$("a#saveGame").click(function(){ _gaq.push(['_trackEvent', 'Bookmark', 'Click', 'Save']); });
	$("a#loadGame").click(function(){ _gaq.push(['_trackEvent', 'Bookmark', 'Click', 'Load']); });
	$("#addContent").click(function(){ _gaq.push(['_trackEvent', 'Content', 'Click', 'Add']); });
	$("a.contentMoveUp").click(function(){ _gaq.push(['_trackEvent', 'Content', 'Click', 'Move Up']); });
	$("a.contentMoveDown").click(function(){ _gaq.push(['_trackEvent', 'Content', 'Click', 'Move Down']); });
	$("a.edit-content").click(function(){ _gaq.push(['_trackEvent', 'Content', 'Click', 'Edit']); });
	$("a.delete-content").click(function(){ _gaq.push(['_trackEvent', 'Content', 'Click', 'Delete']); });
	$("#deleteAdventure").click(function(){ _gaq.push(['_trackEvent', 'Adventure', 'Click', 'Delete']); });
	$("#addLink").click(function(){ _gaq.push(['_trackEvent', 'Link', 'Click', 'Add New']); });
	$("#addExistingLink").click(function(){ _gaq.push(['_trackEvent', 'Link', 'Click', 'Add Existing']); });
	$("a.linkMoveUp").click(function(){ _gaq.push(['_trackEvent', 'Link', 'Click', 'Move up']); });
	$("a.linkMoveDown").click(function(){ _gaq.push(['_trackEvent', 'Link', 'Click', 'Move Down']); });
	$("a.edit-links").click(function(){ _gaq.push(['_trackEvent', 'Link', 'Click', 'Edit']); });
	$("a.delete-links").click(function(){ _gaq.push(['_trackEvent', 'Link', 'Click', 'Delete']); });
});

function ConfirmDelete( deleteObj ) {
	return confirm('Are you sure you wish to delete this ' + deleteObj + '?');
} 

function ConfirmDeny( denyObj ) {
	return confirm('Are you sure you wish to deny this ' + denyObj + '?');
}