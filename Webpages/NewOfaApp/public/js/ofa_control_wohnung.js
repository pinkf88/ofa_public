// https://github.com/flot/flot/blob/HEAD/docs/API.md
// https://www.flotcharts.org/flot/examples/

var selected_day = getCurrentDayString();
const base_url = '/inc/ofa_ControlHome.php';

var config = {
    sensor_temperature_outside_1: {
        hue_id: 6
    },
    sensor_temperature_cellar_1: {
        hue_id: 10
    },
    sensor_temperature_corridor_1: {
        hue_id: 16
    }
};

function control_runHome()
{
    getHome();

    if (selected_day == getCurrentDayString()) {
        getDay('');
    }

    // setTimeout(control_runHome, 5 * 60 * 1000);     // 5 Minuten
}

function getCurrentDayString()
{
    var today = new Date();
    var dd = today.getDate();
    var mm = today.getMonth() + 1;
    var yyyy = today.getFullYear();

    if (dd < 10) {
        dd = '0' + dd
    }

    if (mm < 10) {
        mm = '0' + mm
    }

    return yyyy + '-' + mm + '-' + dd;
}

function getTimeSeconds(time)
{
    var seconds = 0;

    try {
        seconds = Number(time.split(':')[0]) * 60 * 60 + Number(time.split(':')[1]) * 60 + Number(time.split(':')[2]);
    } catch (err) {
        console.log('getTimeSeconds(): ', err.message);
        console.log('getTimeSeconds(): time=', time);
    }

    return seconds;
}

function getHome()
{
    var url = base_url + '?temperatures';

    $.ajax({
        type: 'GET',
        url:  url
    }).done(function(result) {
        var datasets = JSON.parse(result).datasets;

        datasets.forEach(data => {
            if (data != undefined && data != null) {
                if (data.hue_id == config.sensor_temperature_outside_1.hue_id) {
                    $('#temperature_outside').html(data.temperature);
                    $('#lastupdated_outside').html(data.lastupdated);
                    $('#battery_outside').html(data.battery);
                } else if (data.hue_id == config.sensor_temperature_cellar_1.hue_id) {
                    $('#temperature_cellar').html(data.temperature);
                    $('#lastupdated_cellar').html(data.lastupdated);
                    $('#battery_cellar').html(data.battery);
                } else if (data.hue_id == config.sensor_temperature_corridor_1.hue_id) {
                    $('#temperature_corridor').html(data.temperature);
                    $('#lastupdated_corridor').html(data.lastupdated);
                    $('#battery_corridor').html(data.battery);
                }
            }
        });
    }).fail(function(jqXHR, textStatus) {
        console.log('getHome(): Ajax-Fehler bei ' + url);
    });
}

function getDay(daystring)
{
    var url = base_url + '?day';

    if (daystring != '') {
        selected_day = daystring;
        url += '=' + daystring;
    }

    $.ajax({
        type:   'GET',
        url:    url
    }).done(function(result)
    {
        var data = JSON.parse(result);
        // console.log(JSON.stringify(data, null, 4));

        $('#control_home_date').html(data.day);
        $('#temperature_min').html(data.temperature_min);
        $('#temperature_max').html(data.temperature_max);
        $('#sunrise').html(data.sunrise);
        $('#sunset').html(data.sunset);

        var sunrise = getTimeSeconds(data.sunrise);
        var sunset = getTimeSeconds(data.sunset);

        plot(data.x_time_min, data.x_time_max, data.y_temp_min, data.y_temp_max, data.data_series, sunrise, sunset);

        $('#jq-dropdown-days-list').empty();

        for (var day of data.days) {
            $('#jq-dropdown-days-list').append("<li style=\"color: black;\"><a href=\"javascript:getDay('" + day + "')\">" + day + "</a></li>");
        }
    }).fail(function(jqXHR, textStatus)
    {
        console.log('getDay(): Ajax-Fehler');
    });
}

const COLORS = [
    'forestgreen',
    'olive',
    'chocolate',
    'lime'
];

const LOCATIONS = [
    'Terasse (H)',
    'Terasse (N)',
    'Gartenhaus',
    'Elke'
];

function plot(x_min, x_max, y_min, y_max, data_series, sunrise, sunset)
{
    /*
    console.log('plot()', x_min, x_max);

    var date = new Date(x_min);
    var result = date.toLocaleDateString('de-DE', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
    });

    console.log(result, (new Date()).getTime());
    */

    var legendSettings = {
        position:   'nw',
        show:       true,
        noColumns:  1,
        container:  null
    };

    var no_points = {
        show: false
    };

    // console.log('sun:', sunrise, sunset);

    var markings = [
        { color: 'yellow', lineWidth: 3, xaxis: { from: sunrise, to: sunrise } },
        { color: 'yellow', lineWidth: 3, xaxis: { from: sunset, to: sunset } }
    ];

    var plot_data = [];

    for (var i = 0; i < data_series.length; i++) {
        plot_data.push({
            label:  LOCATIONS[i],
            data:   data_series[i],
            points: no_points,
            color:  COLORS[i],
            lines: {
                show:       true, 
                lineWidth:  3
            }, 
        });
    }

    $.plot($('#placeholder'), plot_data, {
        legend: legendSettings,
        xaxis: {
            autoScale:      'none',
            timeBase:       'seconds',
            mode:           'time',
            timeformat:     '%H:%M',
            tickSize:       [2, 'hour'],
            showTicks:      false,
            twelveHourClock: false,
            min:            (new Date(x_min)).getTime(),
            max:            (new Date(x_max)).getTime()
        },
        yaxis: {
            min: y_min,
            max: y_max
        },
        series: {
            lines: { show: true },
            points: { show: true }
        },
        grid: {
            hoverable: true,
            clickable: false,
            markings: markings
        }
    });

    $('#placeholder').bind('plothover', function (event, pos, item) {
        if (!pos.x || !pos.y) {
            return;
        }

        if (item) {
            $('#tooltip').html(item.series.label + ': ' + item.datapoint[1].toFixed(1) + '°C')
                .css({
                    top: item.pageY + 5, 
                    left: item.pageX + 5
                })
                .fadeIn(200);
        } else {
            $('#tooltip').hide();
        }
    });

    $('#placeholder').bind('plothovercleanup', function (event, pos, item) {
            $('#tooltip').hide();
    });
}
