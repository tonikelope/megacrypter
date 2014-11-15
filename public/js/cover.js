$(document).ready(function()
{    
        $('#button_crypt').prop('disabled', true);
        
        $('input#advanced_mode').prop('checked', false);
        
        $('input#hide_name').prop('checked', false);
        
        $('input#tiny_url').prop('checked', false);
        
        $('input#app_finfo').prop('checked', false);
        
        $('input#tos_ok').prop('checked', false);

        $('input#tos_ok').click(function()
        {
            if($('input#tos_ok').is(":checked"))
            {
                $('#button_crypt').prop('class', 'button_submit');
                $('#button_crypt').prop('disabled', false);
            }
            else
            {
                $('#button_crypt').prop('class', 'button_submit_disabled');
                $('#button_crypt').prop('disabled', true);
            }
        });
        
        $('input#advanced_mode').click(function()
        {
            if($('input#advanced_mode').is(':checked')) {
                $('#adv_fields').slideDown('fast'); 
            } else {
                $('#adv_fields').slideUp('fast');  
            }
        });
                
        $("#cover_text_fields #pass").on('keyup mouseup change paste', function() {
            if($(this).val().length >= 1 && !$("#cover_text_fields #pass_warning").is(":visible")) {
                $("#cover_text_fields #pass_warning").fadeIn('fast');
            }  
            else if($(this).val().length === 0 && $("#cover_text_fields #pass_warning").is(":visible")) {
                $("#cover_text_fields #pass_warning").fadeOut('fast');
            }
        });
        
        $("#plain_textarea").on('keyup mouseup change paste', function() {
            if($("#referer").val().length === 0 && $(this).val().length >= 1 && !$("#dlock_warning").is(":visible")) {
                $("#dlock_warning").fadeIn('fast');
            }  
            else if($(this).val().length === 0 && $("#dlock_warning").is(":visible")) {
                $("#dlock_warning").fadeOut('fast');
            }
        });
        
        $("#referer").on('keyup mouseup change paste', function() {
            if($(this).val().length >= 1 && $("#dlock_warning").is(":visible")) {
                $("#dlock_warning").fadeOut('fast');
            }  
            else if($(this).val().length === 0 && !$("#dlock_warning").is(":visible") && $("#plain_textarea").val().length >= 1) {
                $("#dlock_warning").fadeIn('fast');
            }
        });

});