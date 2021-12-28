jQuery(function ($) {
  // accordion
  $(document).on(
    'click',
    '.accordion:not(.employment-accord) .accordion-item .accordion-title',
    function () {
      if ($(this).hasClass('active')) {
        $(this).removeClass('active').next().slideUp();
      } else {
        $(this)
          .closest('.accordion')
          .find('.accordion-title')
          .not(this)
          .removeClass('active')
          .next()
          .slideUp();
        $(this).addClass('active').next().slideDown();
      }
    }
  );
});
