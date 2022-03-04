import './lib/jquery.sumoselect.min';

jQuery(function ($) {
  // sumoselect
  if ($('.SelectBox').length) {
    $('.SelectBox').each(function () {
      $(this).SumoSelect({
        floatWidth: 0,
        nativeOnDevice: [],
      });
    });
  }
});
