$(document).ready(function()
{
    $('button#https_crypt').click(
        function(){

            if($('button#https_crypt').html() == 'HTTPS links')
            {
                $('textarea#crypt_textarea').val($('textarea#crypt_textarea').val().replace(/http\:\/\//ig, 'https://'));
                $('button#https_crypt').html('HTTP links');
            }
            else
            {
                $('textarea#crypt_textarea').val($('textarea#crypt_textarea').val().replace(/https\:\/\//ig, 'http://'));
                $('button#https_crypt').html('HTTPS links');
            }
    });
});