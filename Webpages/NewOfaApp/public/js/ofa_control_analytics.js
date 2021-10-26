function control_analyticsOverview()
{
    control_overlayOn();

    $.ajax({
        url :   '/inc/ofa_ControlAnalytics.php?type=overview&days=' +  $('#control_admin_overview_days').val()
    }).done(function(data)
    {
        var overview = JSON.parse(data);
        var html = 'Übersicht ' + overview.date_min + ' - ' + overview.date_max;
        $('#analytics_overview_title').html(html);

        html = '';

        for (var i = 0; i < overview.webs.length; i++) {
            var id = 'web' + overview.paths[i].replace(/\//g, '_');

            html += '<p class="overview_data">'
                + '<span class="overview_data_web"><a href="javascript:control_analyticsOverviewWeb(\'' + id + '\', \'' + overview.webs[i] + '\')">' + overview.webs[i] + '</a></span>'
                + '<span class="overview_data_path"><a href="https://www.juergen-reichmann.de' + overview.paths[i] + '" target="_blank">' + overview.paths[i] + '</a></span>'
                + '<span class="overview_data_pageview">' + overview.pageviews[i] + '</span>'
                + '</p>'
                + '<div id="' + id + '" class="overview_web"></div>'
                + '<hr class="analytics">';
        }

        $('#analytics_overview').html(html);

        $.ajax({
            url :   '/inc/ofa_ControlAnalytics.php?type=auswahl&days=' +  $('#control_admin_overview_days').val()
        }).done(function(data)
        {
            var auswahl = JSON.parse(data);
            var html = '';
        
            for (var i = 0; i < auswahl.landids.length; i++) {
                html += '<p class="auswahl_data">'
                    + '<span class="auswahl_data_land"><a href="javascript:control_analyticsAuswahlLand(\'' + auswahl.landids[i] + '\')">' + auswahl.laender[i] + '</a></span>'
                    + '<span class="auswahl_data_pageview">' + auswahl.pageviews[i] + '</span>'
                    + '</p>'
                    + '<div id="land_' + auswahl.landids[i] + '" class="auswahl_land"></div>'
                    + '<hr class="analytics">';
            }
        
            $('#analytics_auswahl').html(html);

            setTimeout(control_overlayOff, 4000);
        }).fail(function(jqXHR, textStatus)
        {
            console.log('ERROR control_analyticsOverview(): ' + textStatus);
            control_overlayOff();
        });        
    }).fail(function(jqXHR, textStatus)
    {
        console.log('ERROR control_analyticsOverview(): ' + textStatus);
        control_overlayOff();
    });        
}

function control_analyticsOverviewWeb(webid, web)
{
    control_overlayOn();

    $.ajax({
        url :   '/inc/ofa_ControlAnalytics.php?type=web&web=' + web + '&days=' +  $('#control_admin_overview_days').val()
    }).done(function(data)
    {
        var web = JSON.parse(data);
        var html = '';
        
        for (var i = 0; i < web.titles.length; i++) {
            var id = web.paths[i].replace(/\//g, '_');
        
            html += '<hr class="analytics">'
                + '<p class="web_data">'
                + '<span class="web_data_web">' + web.titles[i] + '</span>'
                + '<span class="web_data_path"><a href="https://www.juergen-reichmann.de' + web.paths[i] + '" target="_blank">' + web.paths[i] + '</a></span>'
                + '<span class="web_data_pageview">' + web.pageviews[i] + '</span>'
                + '</p>';
        }
        
        $('#' + webid).html(html);
        $('#' + webid).show();
        setTimeout(control_overlayOff, 2500);
    }).fail(function(jqXHR, textStatus)
    {
        console.log('ERROR control_analyticsOverview(): ' + textStatus);
        control_overlayOff();
    });        
}

function control_analyticsAuswahlLand(landid)
{
    control_overlayOn();

    $.ajax({
        url :   '/inc/ofa_ControlAnalytics.php?type=land&landid=' + landid + '&days=' +  $('#control_admin_overview_days').val()
    }).done(function(data)
    {
        var ortemotive = JSON.parse(data);
        var html = '';
        
        for (var i = 0; i < ortemotive.ortemotive.length; i++) {
            html += '<hr class="analytics">'
                + '<p class="ortemotive_data">'
                + '<span class="ortemotive_data_ortemotive">' + ortemotive.ortemotive[i] + '</span>'
                + '<span class="ortemotive_data_url"><a href="https://www.juergen-reichmann.de' + ortemotive.urls[i] + '" target="_blank">' + ortemotive.urls[i] + '</a></span>'
                + '<span class="ortemotive_data_pageview">' + ortemotive.pageviews[i] + '</span>'
                + '</p>';
        }
        
        $('#land_' + landid).html(html);
        $('#land_' + landid).show();
        setTimeout(control_overlayOff, 2500);
    }).fail(function(jqXHR, textStatus)
    {
        console.log('ERROR control_analyticsOverview(): ' + textStatus);
        control_overlayOff();
    });        
}
