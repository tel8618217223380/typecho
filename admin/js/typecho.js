/*
 * You can add any Javascript here.
 */

$(document).ready(function() {
	$("table.latest tr").mouseover(function() {  
		 $(this).addClass("over"); }).mouseout(function() { 
			 $(this).removeClass("over"); });
	$("table.latest tr:even").addClass("alt");
	$("table.setting tr:odd").addClass("alt");
    
    $("table.latest tr:first th:first input").click(function() {
        if(true == $(this).attr('checked'))
        {
            $("table.latest tr td input").each(function() {
                $(this).attr('checked', true);
            }
            )
        }
        else
        {
            $("table.latest tr td input").each(function() {
                $(this).attr('checked', false);
            }
            )
        }
    }
    );

	$(":text").addClass("text");
	$(":password").addClass("password");
	$(":submit").addClass("submit");
	$(":button").addClass("button");
    
    $(".latest .publish").corner("5px");
    $(".latest .unpublish").corner("5px");
    $(".latest .spam").corner("5px");
    $(".latest .waiting").corner("5px");
    $(".latest .approved").corner("5px");
    $(".latest .activated").corner("5px");
    $(".latest .deactivated").corner("5px");
    $(".latest .config").corner("5px");
});
