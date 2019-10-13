$(document).ready(function() 
{
    url = "/inc/ofa_GetStartInfo.php?tag=" + (new Date()).getDate() + "&monat=" + ((new Date()).getMonth() + 1) + '&jahr=' + ((new Date()).getYear() + 1900);    

    $('#history').html('<b>Hallo</b>, hier bin ich. ' + url);
    
    $.ajax({
        url : url 
    }).done(function(data)
    {
        $('#history').html(data) + '\n';
    }).fail(function(jqXHR, textStatus)
    {
         alert("Database access failed: " + textStatus);
    });
    
});

/*
s := 'SELECT * FROM leben WHERE ';

for i := qryJahr_MINYEARDatumVon.AsInteger to wJahrBis do
begin
  if i <> qryJahr_MINYEARDatumVon.AsInteger then
    s := s + 'OR ';

  if rbExakt.Checked then
  begin
    s := s + '("' + Format ('%4.4d-%2.2d-%2.2d', [i, wMonat, wTag]) + '" BETWEEN DatumVon AND DatumBis) ';
  end
  else
  begin
    s := s + '(DatumVon BETWEEN ("' + Format ('%4.4d-%2.2d-%2.2d', [i, wMonat, wTag]) + '" - INTERVAL 3 DAY) '
           + 'AND ("' + Format ('%4.4d-%2.2d-%2.2d', [i, wMonat, wTag]) + '" + INTERVAL 3 DAY)) ';
  end;
end;

s := s + 'ORDER BY DatumVon, Nr';
*/