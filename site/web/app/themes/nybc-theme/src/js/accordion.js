jQuery(function ($) {
  // accordion
  $(document).on(
    'click',
    '.accordion:not(.employment-accord) .accordion-item .accordion-title',
    function () {
      if ($(this).hasClass('active')) {
        $(this).removeClass('active').next().slideUp();
        $(this).attr('aria-expanded', false);
        $(this)
          .next()
          .attr('tabindex', -1)
          .find('.text-lg')
          .attr('tabindex', -1);
        window.history.back();
      } else {
        $(this)
          .closest('.accordion')
          .find('.accordion-title')
          .not(this)
          .removeClass('active')
          .next()
          .slideUp();
        $(this).addClass('active').next().slideDown();
        $(this).attr('aria-expanded', true);
        $(this)
          .next()
          .removeAttr('tabindex')
          .find('.text-lg')
          .removeAttr('tabindex');
        console.log('acc open');
        var ac_id = $(this).closest('.accordion-item').attr('id');
        console.log(ac_id);
        var newurl =
          window.location.protocol +
          '//' +
          window.location.host +
          window.location.pathname +
          '#' +
          ac_id;
        console.log(newurl);
        window.history.pushState({ path: newurl }, '', newurl);
      }
    }
  );
  $(
    '.accordion:not(.employment-accord) .accordion-item .accordion-title'
  ).keypress(function (event) {
    const keycode = event.keyCode ? event.keyCode : event.which;
    if (keycode === 13) {
      event.preventDefault();
      $(this).click();
    }
  });
  $(document).ready(function () {
    console.log('acc open on ready');
    var href = window.location.href;
    console.log(href);
    var ac_id = '';
    var ac_target = '';
    if (href.indexOf('#') > -1) {
      ac_id = '#' + href.split('#')[1];
      ac_target = ac_id + ' .accordion-title';
      console.log(ac_id);
    } else {
      return;
    }
    $(ac_target).addClass('active').next().slideDown();
    var newurl = href.split('#')[0] + ac_id;
    window.history.pushState({ path: newurl }, '', newurl);
  });
});
