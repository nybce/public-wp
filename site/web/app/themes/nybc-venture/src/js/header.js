//*========================================
//* HEADER                                =
//*========================================
jQuery(function ($) {
  /* Open menu */
  $(document).on('click', '.menu-btn', function () {
    console.log('hit');
    $(this).toggleClass('is-active');
    $('.toggle-block').toggleClass('open');
  });
});