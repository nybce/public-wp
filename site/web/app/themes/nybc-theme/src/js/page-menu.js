jQuery(function ($) {
  // page menu
  $('.page-mobile-button').on('click', function () {
    $(this).toggleClass('active');
    $(this)
      .parents('.page-menu-wrapper')
      .find('.page-menu')
      .toggleClass('open');
  });
});
