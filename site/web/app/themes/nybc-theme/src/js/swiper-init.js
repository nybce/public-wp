import { _functions, isTouchScreen } from './global';
import Swiper from './lib/swiper.min';

jQuery(function ($) {
  _functions.getSwOptions = function (swiper) {
    let options = swiper.data('options');
    options = !options || typeof options !== 'object' ? {} : options;
    const $p = swiper.closest('.swiper-entry'),
      slidesLength = swiper.find('>.swiper-wrapper>.swiper-slide').length;
    if (!options.pagination) {
      options.pagination = {
        el: $p.find('.swiper-pagination')[0],
        clickable: true,
      };
    }
    if (!options.navigation) {
      options.navigation = {
        nextEl: $p.find('.swiper-button-next')[0],
        prevEl: $p.find('.swiper-button-prev')[0],
      };
    }
    options.preloadImages = false;
    options.lazy = {
      loadPrevNext: true,
    };
    options.observer = true;
    options.observeParents = true;
    options.watchOverflow = true;
    options.centerInsufficientSlides = true;
    if (!options.speed) {
      options.speed = 500;
    }
    options.roundLengths = true;
    if (slidesLength <= 1) {
      options.loop = false;
      $p.addClass('hide-control');
    }
    if (options.customFraction) {
      $p.addClass('custom-fraction-swiper');
      if (slidesLength > 1 && slidesLength < 10) {
        $p.find('.custom-current').text('1');
        $p.find('.custom-total').text(slidesLength);
      } else if (slidesLength > 1) {
        $p.find('.custom-current').text('1');
        $p.find('.custom-total').text(slidesLength);
      }
    }
    if (isTouchScreen) {
      options.direction = 'horizontal';
    }
    return options;
  };

  _functions.initSwiper = function (el) {
    new Swiper(el[0], _functions.getSwOptions(el));
  };

  setTimeout(function () {
    $('.swiper-entry .swiper-container').each(function () {
      _functions.initSwiper($(this));
    });
  }, 200);

  //custom fraction
  $('.custom-fraction-swiper').each(function () {
    const $this = $(this),
      $thisSwiper = $this.find('.swiper-container')[0].swiper;

    $thisSwiper.on('slideChange', function () {
      $this.find('.custom-current').text(function () {
        if ($thisSwiper.realIndex < 9) {
          return +($thisSwiper.realIndex + 1);
        }
        return $thisSwiper.realIndex + 1;
      });
    });
  });

  $('.swiper-thumbs').each(function () {
    const top = $(this).find('.swiper-container.swiper-thumbs-top')[0].swiper,
      bottom = $(this).find('.swiper-container.swiper-thumbs-bottom')[0].swiper;
    top.thumbs.swiper = bottom;
    top.thumbs.init();
    top.thumbs.update();
  });

  $('.swiper-control').each(function () {
    const top = $(this).find('.swiper-container')[0].swiper,
      bottom = $(this).find('.swiper-container')[1].swiper;
    top.controller.control = bottom;
    bottom.controller.control = top;
  });

  $('.news-swiper, .card-swiper').each(function () {
    if ($(this).find('.swiper-button-prev').hasClass('swiper-button-lock')) {
      $(this).children('.swiper-button-wrapper').addClass('swiper-button-lock');
    }
  });
});
