$(document).ready(function()
{    
    $('#button_remove').prop('disabled', true);
    $('input#tos_ok').prop('checked', false);
    
    $('input#tos_ok').click(function()
    {
        if($('input#tos_ok').is(":checked"))
        {
            $('#button_remove').prop('class', 'button_submit');
            $('#button_remove').prop('disabled', false);
        }
        else
        {
            $('#button_remove').prop('class', 'button_submit_disabled');
            $('#button_remove').prop('disabled', true);
        }
    });
    
    $('#takedown_form #instructions').click(function()
    {
        if($('#takedown_form #message').is(":visible")) {
            $('#takedown_form #message').fadeOut();
            $('#takedown_form #instructions').text('Show instructions');
        } else {
            $('#takedown_form #message').fadeIn();
            $('#takedown_form #instructions').text('Hide instructions');
        }
    });

});