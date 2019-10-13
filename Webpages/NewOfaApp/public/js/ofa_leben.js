$("tr.firstline").hover(
  function ()
  {
    $(this).css("background","lightgrey");

    if ($(this).next().hasClass("secondline"))
    {
      $(this).next().css("background","lightgrey");
    }
  }, 
  function ()
  {
    $(this).css("background","");
    $(this).next().css("background","");
  }
);

$("tr.secondline").hover(
  function ()
  {
    $(this).css("background","lightgrey");

    if ($(this).prev().hasClass("firstline"))
    {
      $(this).prev().css("background","lightgrey");
    }
  }, 
  function ()
  {
    $(this).css("background","");
    $(this).prev().css("background","");
  }
);
