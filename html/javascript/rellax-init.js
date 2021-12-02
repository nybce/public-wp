jQuery(function ($) {

  //rellax
  setTimeout(function () {
    if (!isIE && $('.rellax').length && $(window).width() > 1199) {
      var rellax = new Rellax('.rellax', {
        center: true
      });
    }
  }, 0);

});