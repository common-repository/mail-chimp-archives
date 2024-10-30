jQuery(document).ready(function($) {
	
    $('h3.time_interval:first').before('<p>Click the Date to reveal the archives for that period.</p>');
    $('h3.time_interval').css('cursor','pointer');
    $('ul.mailing_group').hide();
    $('ul.mailing_group:first').show();
	
    $('h3.time_interval').toggle(function(){
        var h3_group_id = $(this).attr("id");
        var group_id = h3_group_id.replace("title","group");
        $("#"+group_id+"").show("normal");
    },
    function(){
        var h3_group_id = $(this).attr("id");
        var group_id = h3_group_id.replace("title","group");
        $("#"+group_id+"").hide("normal");
    });
});