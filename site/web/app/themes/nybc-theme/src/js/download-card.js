jQuery(function ($) {
  //visible expand more
  $('.expand-more').on('click', function () {
    $(this).parent().toggleClass('open-expand-block');
    $(this).parent().find('.download-card').addClass('active');

    if ($(this).parent().hasClass('open-expand-block')) {
      $(this).text($(this).data('active-text'));
    } else {
      $(this).text($(this).data('orig-text'));
      $(this).parent().find('.download-card').removeClass('active');
    }
  });
});
