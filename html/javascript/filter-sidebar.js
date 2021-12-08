jQuery(function ($) {

  // filter
  $(".filter-mobile-button").on("click", function () {
    $(this).toggleClass("active");
    $(this).parents('.filter-sidebar').find('.filter-sidebar-item').toggleClass('open');
  });

});