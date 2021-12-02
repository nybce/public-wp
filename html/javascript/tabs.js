jQuery(function ($) {
  // tabs
  $('.tab-title').on('click', function () {
    $(this).parent().toggleClass('active');
  });
  $('.tab-toggle div').on('click', function () {
    var tab = $(this).closest('.tabs').find('.tab');
    var i = $(this).index();
    $(this).addClass('active').siblings().removeClass('active');
    tab.eq(i).siblings('.tab:visible').fadeOut(function () {
      tab.eq(i).fadeIn();
    });
    $(this).closest('.tab-nav').removeClass('active').find('.tab-title').text($(this).text());
  });

});