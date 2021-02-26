function control_playPictures(runtype)
{
    var url = '/inc/ofa_ControlMedia.php?type=pictures'
        + '&bildtyp=1'
        + '&jahr=' + $('#control_year').val()
        + '&landid=' + $('#control_country').val()
        + '&ortid=' + $('#control_location').val()
        + '&nummer_von='
        + '&nummer_bis='
        + '&wertung_min=0'
        + '&countperpage=1000000'
        + '&suchtext=' + $('#control_search').val()
        + '&runtype=' + runtype;

    // console.log(url);

    $.ajax({
        url: url
    }).done(function(data)
    {
        if (data != 'ERROR') {
            startSeriesTimer();
        }

        $('#lastinfo').html(data);
    }).fail(function(jqXHR, textStatus)
    {
        console.log('control_playPictures(): Database access failed: ' + textStatus);
        console.log(url);
    });
}
