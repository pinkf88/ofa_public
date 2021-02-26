var last_trackid = '';

$("tr.firstline").hover(function()
{
    $(this).css("background", "lightgrey");
}, function()
{
    $(this).css("background", "");
});

$("tr.firstline").mouseenter(function()
{
    $("#" + this.id).addClass("hasFocus");

    if (last_trackid != this.id) {
        last_trackid = this.id;

        setTimeout(function() {
            if ($("#" + last_trackid).hasClass("hasFocus"))
            {
                track_showInformation(last_trackid);
            }
        }, 800);
    }
});

$("tr.firstline").mouseleave(function()
{
    if (this.id != last_trackid) {
        $("#" + this.id).removeClass("hasFocus");
    }
});

function track_showInformation(trackid)
{
    var output = "";

    $.ajax({
        dataType : "json",
        url : "/inc/ofa_GetTrackInfo.php?trackid=" + trackid
    }).done(function(data)
    {
        output += data.bilddaten + '\n';
        output += data.trackdaten + '\n';

        $("#trackinformation").html(output);
    }).fail(function(jqXHR, textStatus)
    {
        console.log("Database access failed: " + textStatus);
    });

    $(".fancybox").fancybox();
}

function track_playTrack(trackid)
{
    $.ajax({
        url : "/inc/ofa_ControlMedia.php?type=track&trackid=" + trackid
    }).done(function(data)
    {
    }).fail(function(jqXHR, textStatus)
    {
        console.log("Database access failed: " + textStatus);
    });
}

function track_addToRunningTracks(id)
{
    $.ajax({
        type : "GET",
        url : "/inc/ofa_ControlMedia.php?type=running&source=2&id=" + id + "&roomid=1"
    }).done(function(data)
    {
    }).fail(function(jqXHR, textStatus)
    {
        console.log("Database access failed: " + textStatus);
    });

}

function track_setStudioLive(trackid, studio)
{
    var html = '';

    if (studio == 11) {
        html = '<a href="javascript:track_setStudioLive(\'' +  trackid + '\', 10);">Studio</a>';
    } else {
        html = '<a href="javascript:track_setStudioLive(\'' +  trackid + '\', 11);">Live</a>';
    }
    
    $("#studio_" + trackid).html(html);

    $.ajax({
        type: 'GET',
        url : "/inc/ofa_UpdateStudioLive.php?id=" + trackid + "&studio=" + studio
    }).done(function(data)
    {
    }).fail(function(jqXHR, textStatus)
    {
        console.log("ERROR track_setStudioLive(): " + textStatus);
    });
}
