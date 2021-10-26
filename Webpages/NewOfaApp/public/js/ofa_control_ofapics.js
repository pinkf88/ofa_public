function ofapics_request()
{
    var numbers = $('#ofapics').val();
    console.log(numbers);

    $.ajax({
        url :   '/inc/ofa_ControlOfaPics.php?numbers=' + numbers.replace(/ /g, '_')
    }).done(function(data)
    {
        console.log(data);
    });
}
