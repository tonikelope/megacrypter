$(document).ready(function()
{
    $('.fancybox').fancybox(
    {
        padding: 0,
        openEffect : 'fade',
        closeEffect : 'fade',
        helpers : { 
            overlay : {closeClick: false}
        }}
    );

    $(function() {
        $('form[class="spinner_form"]').on('submit', function()
        {
            $(this).find(':text, textarea').prop('readonly', true);
            $(this).find('.button_submit').hide();
            $(this).find('.spinner').show();
            $(this).submit(function() { return false; });
        });
    });
});