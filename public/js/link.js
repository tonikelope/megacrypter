	
$(document).ready(function()
{
    $('#button_download').prop('disabled', true);
    $('#button_stream').prop('disabled', true);
    $('input#tos_ok').prop('checked', false);

    $('input#tos_ok').click(function()
    {
        if($('input#tos_ok').is(":checked"))
        {
            $('#button_download').prop('class', 'button_submit');
            $('#button_download').prop('disabled', false);
            $('#button_stream').prop('class', 'button_submit');
            $('#button_stream').prop('disabled', false);
        }
        else
        {
            $('#button_download').prop('class', 'button_submit_disabled');
            $('#button_download').prop('disabled', true);
            $('#button_stream').prop('class', 'button_submit_disabled');
            $('#button_stream').prop('disabled', true);
        }
    });
    
    if(typeof timer_total !== 'undefined') {
        countDownTimer();
        interval=setInterval(countDownTimer, 1000);
    }
});
        
function download_file()
{
    if(typeof interval !== 'undefined')
        clearInterval(interval);
    
    $('#timer').hide();
    $('#form_block').hide();
    $('#downloader').css('display', 'inline-block');
    
    $(window).bind('beforeunload', function(){
        return 'If download is in progress, you should stop it before closing this window.';
        });
        
    $(window).bind('unload', function(){
        document.getElementById('dl_applet').stopDownloader();
        });
}

function dl_applet_exit_ok(message)
{
    $('#downloader #dl_applet_message').css('display', 'inline-block');
    $('#downloader #dl_applet_message').addClass('info_ok');
    $('#downloader #dl_applet_message').text(message);
}

function dl_applet_exit_error(message)
{
    $('#downloader #dl_applet_message').css('display', 'inline-block');
    $('#downloader #dl_applet_message').addClass('info_error');
    $('#downloader #dl_applet_message').text(message);

}

function stream_file()
{
    if(typeof interval !== 'undefined')
        clearInterval(interval);
    
    $('#timer').hide();
    $('#form_block').hide();
    $('#streamer').css('display', 'inline-block');
    $("#lightsOn").hide();
    
        $("#lightsOff").click(function () {

             $("div#lb-bg").css({opacity: 0, display: "block"}).animate({opacity: 0.9},500);
             $("#vlc_player").css('border', 'solid 4px darkgray');
             $("#lightsOff").fadeOut(0);
             $("#lightsOn").show(); 

        });

        $("#lightsOn").click(function () {

              $("#vlc_player").css('border', 'solid 4px');
              $("div#lb-bg").fadeOut(500);
              $("#lightsOn").hide();
              $("#lightsOff").show();

        });
    
        $('#vlc_fullscreen').click(
                function(){
                var vlc = document.getElementById("vlc_player");
                vlc.video.toggleFullscreen();
            });        
        
    $(window).bind('beforeunload', function(){
        return 'Sure?';
        });
    
    $(window).bind('unload', function(){
        document.getElementById('st_applet').stopServer();
        });
}

function start_vlc_player()
{   
    hide_applet_spinner();
    
    $('#vlc_wrapper').css('display', 'inline-block');
    
    var vlc = document.getElementById("vlc_player");
 
    vlc.playlist.add('http://localhost:1337/video'+window.location.pathname);
    
    vlc.playlist.play();
}

function dl_applet_byebye()
{
    $('#dl_applet').hide();
    disable_beforeunload();
    disable_unload();
}

function disable_beforeunload()
{
    $(window).unbind('beforeunload');
}

function disable_unload()
{
    $(window).unbind('unload');
}

function hide_applet_spinner()
{
    $('.applet_spinner').hide();
}

function countDownTimer()
{
    if(timer_total>0)
    {
        var d = Math.floor(timer_total / 86400);

        var h = Math.floor((timer_total % 86400) / 3600);

        var m = Math.floor(((timer_total % 86400) % 3600) / 60);

        var s = Math.floor(((timer_total % 86400) % 3600) % 60);

        timer_total--;

        $('#timer').html(d+"d "+(h<10?'0':'')+h+":"+(m<10?'0':'')+m+":"+(s<10?'0':'')+s);
    }
    else
    {
        clearInterval(interval);
        location.reload();
    }
}