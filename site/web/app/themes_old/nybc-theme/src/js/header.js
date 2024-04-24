jQuery(function ($) {
  //menu
  $('.mobile-button').on('click', function () {
    $(this).toggleClass('active');
    $('.dropdown-menu').removeClass('active');
    $('.dropdown-toggle').removeClass('active');
    $('html').toggleClass('overflow-menu');
    $(this).parents('header').find('.toggle-block').toggleClass('open');
  });

  // mobile menu
  $(document).on('click', '.dropdown-btn', function () {
    if ($(window).width() < 1300) {
      $(this).parent().addClass('active');
      $(this).parents().find('.toggle-block.open').addClass('remove-overflow');
    } else {
      $(this).parent().toggleClass('active');
    }
  });

  $('.dropdown-btn').keypress(function (event) {
    const keycode = event.keyCode ? event.keyCode : event.which;
    if (keycode === 13) {
      event.preventDefault();
      $(this).parent().toggleClass('active');
    }
  });

  $('.dropdown-close').on('click', function () {
    if ($(window).width() < 1300) {
      $(this).parents('.dropdown-item').removeClass('active');
      $(this)
        .parents()
        .find('.toggle-block.open')
        .removeClass('remove-overflow');
    }
  });

  $('.dropdown-v2-item.has-children').on('mouseenter', function () {
    if ($(window).width() > 1023) {
      var menuid = $(this).data('menuid');
      var target_submenu = '#submenu-' + menuid;
      $('.sub-dropdown').removeClass('active');
      $(target_submenu).addClass('active');
      $(this).addClass('active');
    }
  });
  $('.sub-dropdown').on('mouseleave', function () {
    if ($(window).width() > 1023) {
      var menuid = $(this).attr('id');
      var targetid = parseInt(menuid.split('-')[1]);
      $('.sub-dropdown').removeClass('active');
      $('.dropdown-v2-item.has-children').removeClass('active');
    }
  });
});
