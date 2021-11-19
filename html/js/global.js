let _functions = {},
  winW, winH, winScr, isTouchScreen, is_Mac, isIE;

jQuery(function ($) {

  "use strict";

  /* function on page ready */
  isTouchScreen = navigator.userAgent.match(/Android/i) || navigator.userAgent.match(/webOS/i) || navigator.userAgent.match(/iPhone/i) || navigator.userAgent.match(/iPad/i) || navigator.userAgent.match(/iPod/i);
  is_Mac = navigator.platform.toUpperCase().indexOf('MAC') >= 0;
  isIE = /MSIE 9/i.test(navigator.userAgent) || /rv:11.0/i.test(navigator.userAgent) || /MSIE 10/i.test(navigator.userAgent);

  const $body = $('body');

  $body.addClass('loaded');

  if (isTouchScreen) $('html').addClass('touch-screen');
  if (is_Mac) $body.addClass('mac');
  if (isIE) $body.addClass('ie');

  _functions.productSwiperWrapperHeight = function () {
    if (!$('.products_swiper').length) return; {
      setTimeout(function () {
        const h = $('.products_swiper').find('.swiper-container').height();
        $('.products_swiper').find('.swiper-wrapper').css({
          'height': h
        });
      }, 100);
    }
    if (!$('.products_swiper-2').length) return; {
      setTimeout(function () {
        const h = $('.products_swiper-2').find('.swiper-container').height();
        $('.products_swiper-2').find('.swiper-wrapper').css({
          'height': h
        });
      }, 100);
    }
  }

  _functions.pageCalculations = function () {
    winW = $(window).width();
    winH = $(window).height();

    // _functions.productSwiperWrapperHeight();
  }

  _functions.pageCalculations();


  //rellax
  setTimeout(function () {
    if (!isIE && $('.rellax').length && $(window).width() > 1199) {
      var rellax = new Rellax('.rellax', {
        center: true
      });
    }
  }, 0);



  //images preload
  _functions.imagesLazyLoad = function () {
    /* images load */
    $('img[data-i-src]:not(.imgLoaded)').each(function (i, el) {
      let loadImage = new Image();
      loadImage.src = $(el).data('i-src');

      loadImage.onload = function () {
        $(el).attr({
          'src': $(el).data('i-src')
        }).addClass('imgLoaded');
      };
      loadImage = null;
    });

    $('iframe[data-i-src]:not(.imgLoaded)').each(function (i, el) {
      $(el).attr({
        'src': $(el).data('i-src')
      }).addClass('imgLoaded');
    });

    $('[data-bg]:not(.bgLoaded)').each(function (i, el) {
      let loadImage = new Image();
      loadImage.src = $(el).data('bg');

      loadImage.onload = function () {
        $(el)
          .css({
            'background-image': 'url(' + $(el).data('bg') + ')'
          })
          .addClass('bgLoaded');
      }
      loadImage = null;
    });
  }

  //images preload
  setTimeout(function () {
    _functions.imagesLazyLoad();
  }, 100);

  _functions.pageScroll = function (current, header_height) {
    $('html, body').animate({
      scrollTop: current.offset().top - header_height
    }, 700);
  }


  // inputmask
  $(".inputmask").inputmask({
    //clearMaskOnLostFocus: false
    showMaskOnHover: false
  });

  //sumoselect
  if ($('.SelectBox').length) {
    $('.SelectBox').each(function () {
      $(this).SumoSelect({
        floatWidth: 0,
        nativeOnDevice: []
      });
    });

    $('.SelectBox').on('sumo:opened', function (sumo) {
      if (winW < 768) _functions.pageScroll($(this), $('.header').outerHeight() + 30);
    });
  }

  /* function on page scroll */
  $(window).scroll(function () {
    _functions.scrollCall();
  });

  var prev_scroll = 0;
  _functions.scrollCall = function () {
    winScr = $(window).scrollTop();
    if (winScr > 10) {
      $('header').addClass('scrolled');
    } else if (winScr < 10) {
      $('header').removeClass('scrolled');
      prev_scroll
    }


    //show-hide header on scroll
    if (winScr > prev_scroll) {
      $('header').addClass('hide-top');
    } else {
      $('header').removeClass('hide-top');
    }
    prev_scroll = winScr;

    if (winScr <= 10) {
      $('header').removeClass('hide-top');
      prev_scroll = 0;
    }

    if ($('header').hasClass('hide-top')) {
      $('.sidebar').addClass("top");
    } else {
      $('.sidebar').removeClass("top");
    }
  }

  setTimeout(_functions.scrollCall, 0);

  /* function on page resize */
  _functions.resizeCall = function () {
    setTimeout(function () {
      _functions.pageCalculations();
    }, 100);
  };

  if (!isTouchScreen) {
    $(window).resize(function () {
      _functions.resizeCall();
    });
  } else {
    window.addEventListener("orientationchange", function () {
      _functions.resizeCall();
    }, false);
  }


  /* swiper sliders */
  _functions.getSwOptions = function (swiper) {
    let options = swiper.data('options');
    options = (!options || typeof options !== 'object') ? {} : options;
    const $p = swiper.closest('.swiper-entry'),
      slidesLength = swiper.find('>.swiper-wrapper>.swiper-slide').length;
    if (!options.pagination) options.pagination = {
      el: $p.find('.swiper-pagination')[0],
      clickable: true
    };
    if (!options.navigation) options.navigation = {
      nextEl: $p.find('.swiper-button-next')[0],
      prevEl: $p.find('.swiper-button-prev')[0]
    };
    options.preloadImages = false;
    options.lazy = {
      loadPrevNext: true
    };
    options.observer = true;
    options.observeParents = true;
    options.watchOverflow = true;
    options.centerInsufficientSlides = true;
    if (!options.speed) options.speed = 500;
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
    if (isTouchScreen) options.direction = "horizontal";
    return options;
  };
  _functions.initSwiper = function (el) {
    const swiper = new Swiper(el[0], _functions.getSwOptions(el));
  };

  $('.swiper-entry .swiper-container').each(function () {
    _functions.initSwiper($(this));
  });

  //custom fraction
  $('.custom-fraction-swiper').each(function () {
    var $this = $(this),
      $thisSwiper = $this.find('.swiper-container')[0].swiper;

    $thisSwiper.on('slideChange', function () {
      $this.find('.custom-current').text(
        function () {
          if ($thisSwiper.realIndex < 9) {
            return + ($thisSwiper.realIndex + 1)
          } else {
            return $thisSwiper.realIndex + 1
          }
        }
      )
    });
  });

  $('.swiper-thumbs').each(function () {
    var top = $(this).find('.swiper-container.swiper-thumbs-top')[0].swiper,
      bottom = $(this).find('.swiper-container.swiper-thumbs-bottom')[0].swiper;
    top.thumbs.swiper = bottom;
    top.thumbs.init();
    top.thumbs.update();
  });

  $('.swiper-control').each(function () {
    var top = $(this).find('.swiper-container')[0].swiper,
      bottom = $(this).find('.swiper-container')[1].swiper;
    top.controller.control = bottom;
    bottom.controller.control = top;
  });


  //popup
  let popupTop = 0;
  _functions.removeScroll = function () {
    popupTop = $(window).scrollTop();
    $('html').css({
      // "position": "fixed",
      "top": -$(window).scrollTop(),
      "width": "100%"
    }).addClass("overflow-hidden");
  }
  _functions.addScroll = function () {
    $('html').css({
      // "position": "static"
    }).removeClass("overflow-hidden");
    window.scroll(0, popupTop);
  }

  _functions.openPopup = function (popup) {
    $('.popup-content').removeClass('active');
    $(popup + ', .popup-wrapper').addClass('active');
    _functions.removeScroll();
  };

  _functions.videoPopup = function (src) {
    $('#video-popup .embed-responsive').html('<iframe src="' + src + '"></iframe>');
    _functions.openPopup('#video-popup');
  };

  _functions.closePopup = function () {
    $('.popup-wrapper, .popup-content').removeClass('active');

    // $('.popup-iframe').html('');
    $('#video-popup iframe').remove();

    _functions.addScroll();
  };

  _functions.textPopup = function (title, description) {
    $('#text-popup .text-popup-title').html(title);
    $('#text-popup .text-popup-description').html(description);
    _functions.openPopup('#text-popup');
  };

  $(document).on('click', '.video-popup', function (e) {
    e.preventDefault();
    _functions.videoPopup($(this).data('src'));
  });

  $(document).on('click', '.open-popup', function (e) {
    e.preventDefault();
    _functions.openPopup('.popup-content[data-rel="' + $(this).data('rel') + '"]');
  });

  $(document).on('click', '.popup-wrapper .close-popup, .popup-wrapper .layer-close', function (e) {
    e.preventDefault();
    _functions.closePopup();
  });

  // detect if user is using keyboard tab-button to navigate
  // with 'keyboard-focus' class we add default css outlines
  function keyboardFocus(e) {
    if (e.keyCode !== 9) {
      return;
    }

    switch (e.target.nodeName.toLowerCase()) {
      case 'input':
      case 'select':
      case 'textarea':
        break;
      default:
        document.documentElement.classList.add('keyboard-focus');
        document.removeEventListener('keydown', keyboardFocus, false);
    }
  }

  document.addEventListener('keydown', keyboardFocus, false);

  // filter style click
  $('.filters-list li').on('click', function () {
    $(this)
      .addClass('active')
      .siblings()
      .removeClass('active');
  });

  // categories mobile
  $('.category-title').on('click', function () {
    $(this)
      .toggleClass('active')
      .closest('.categories-menu')
      .toggleClass('active');
  });

  $('.categories-list-item').on('click', function () {
    $(this)
      .closest('.categories-menu')
      .removeClass('active')
      .find('.category-title')
      .removeClass('active');
  });


  // Invalid Input
  $('.input[required]').on('blur', function () {
    if ($(this).val().trim()) {
      $(this).removeClass('invalid');
    } else {
      $(this).addClass('invalid');
    }
  });



  // product filters
  $('.filters-list li').on('click', function () {
    const filter = $(this).data('filter');
    $('.filters-row > *').hide();
    $('.filters-row > ' + filter).show();
  });



  //show all
  $('.filter-all .show-all-btn').on('click', function () {
    $(this).parent('.filter-all').toggleClass('show');
    $(this).parent().find('.filter-list').slideToggle(600);

    if ($(this).parents('.filter-all').hasClass('show')) {
      $(this).text($(this).data('active-text'));
    } else {
      $(this).text($(this).data('orig-text'));
    }
  });



  //visible more text seo block
  $('.read-more').on('click', function () {
    $(this).parents('.more-text').toggleClass('open-more-text');
    $(this).parent().find('.text').slideToggle(600);

    if ($('.more-text').hasClass('open-more-text')) {
      $('.read-more').text($(this).data('active-text'));
    } else {
      $('.read-more').text($(this).data('orig-text'));
    }
  });



  //autocomplete
  // var available_tags = [
  //   "Мобільний телефон Xiaomi Poco X3 6/128GB",
  //   "Мобільний телефон Xiaomi Poco X3 6/64GB",
  //   "Мобільний телефон Xiaomi Poco X3 6/256GB",
  //   " Xiaomi Poco X3 6/128GB планшет",
  //   "Мобільний телефон Xiaomi Poco X3 8/128GB",
  //   "Мобільний телефон Xiaomi Poco X3 8/64GB",
  //   "Мобільний телефон Xiaomi Poco X3 8/256GB"
  // ];
  var availableTags = [
    {
      value: 'Мобільний телефон Xiaomi Redmi Note 10 4/128GB Lake Green',
      icon: 'img/content/search-img-1.png'
    },
    {
      value: 'Мобильный телефон Vivo V21 8/128GB Dusk Blue',
      icon: 'img/content/search-img-2.png'
    },
    {
      value: 'Мобильный телефон Huawei P40 lite 6/128GB Black',
      icon: 'img/content/search-img-3.png'
    },
    {
      value: 'Мобільний телефон Xiaomi Redmi Note 10 4/128GB Lake Green',
      icon: 'img/content/search-img-4.png'
    },
    {
      value: 'Мобильный телефон Nokia 3.4 3/64GB Charcoal',
      icon: 'img/content/search-img-5.png'
    }
  ];

  var btnSeeAll = [
    {
      value: 'Переглянути всі',
      counter: '128',

    }
  ]

  if ($('.search-form').length) {
    $('.search-form input').each(function () {
      $(this).autocomplete({
        source: availableTags

      }).autocomplete('instance')._renderItem = function (ul, item) {
        return $('<li>')
          .append('<span class="img-wrapp"><img src="' + item.icon + '" alt="preview image"/></span>' + '<div class="search-item-title">' + item.value + '</div>')
          .appendTo(ul)
      }
    });
  };

  //menu
  $(".mobile-button").on("click", function () {
    $(this).toggleClass("active");
    $(".dropdown-menu").removeClass("active");
    $(".dropdown-toggle").removeClass("active");
    $('.sidebar-close').removeClass('active');
    $('.sidebar').removeClass('active');
    $("html").toggleClass("overflow-menu");
    $(this).parents("header").toggleClass("open-menu");
    _functions.addScroll();
  });

  //open megamenu
  $(".dropdown-toggle").on("click", function (e) {
    e.preventDefault();
    $('header').removeClass('open-menu');
    $('.mobile-button').removeClass('active');
    $("html").removeClass("overflow-menu");
    $('.sidebar-close').removeClass('active');
    $('.sidebar').removeClass('active');

    if ($(this).hasClass("active")) {
      $(this).removeClass("active");
      $(".dropdown-menu").removeClass("active");
      // _functions.addScroll();
    } else {
      $(".dropdown-menu").addClass("active");
      $(this).addClass("active");
      // _functions.removeScroll();
    }
  });

  $(".bg-layer").on("click", function () {
    $(this).closest(".dropdown-menu").removeClass("active");
    $(".dropdown-toggle").removeClass("active");
    _functions.addScroll();
  });



  //layer close
  $('.layer-close').on('click', function () {
    $('header').find('.header-bottom').removeClass('active')
    $('header').find('.ham').removeClass('active')
    $('header').removeClass('active')
    $('html').removeClass('overflow-menu');
    _functions.addScroll();
  });



  // mobile btn sub menu
  $(document).on('click', '.dropdown-btn', function () {
    if ($(window).width() < 1199) {
      $(this).parent().addClass('active');
    }
  });
  $(document).on('click', '.dropdown-list-title', function () {
    if ($(window).width() < 1199) {
      $(this).parent().toggleClass('active');
      $(this).parent().find('.sub-menu').slideToggle(600);
    }
  });
  $('.dropdown-close').on('click', function () {
    if ($(window).width() < 1199) {
      $(this).parents('.dropdown-item').removeClass('active');
    }
  });



  //accordion
  $(document).on('click', '.accordion-item:not(.edit) .accordion-title', function () {
    var headerHeight = $('header').height(),
      current = $(this);
    if ($(this).hasClass('active')) {
      $(this).removeClass('active').next().slideUp();
    } else {
      $(this).closest('.accordion').find('.accordion-title').not(this).removeClass('active').next().slideUp();
      $(this).addClass('active').next().slideDown();
    }
  });

  //filters
  $('.filter-title').on('click', function () {
    $(this).toggleClass('active');
    $(this).next('.filter-inner').slideToggle(500);
  });
  if ($(window).width() < 992) {
    var sidebarClose = $('.sidebar-close').attr('data-close'),
      sidebarFilters = $('.sidebar-close').find('img').attr('src');

    $('.sidebar-close').on('click', function () {
      $(this).toggleClass('active');
      $('.sidebar').toggleClass('active');
      if ($(this).hasClass('active')) {
        _functions.removeScroll();
      } else {
        _functions.addScroll();
      }
    });
    $('.sidebar-overlay').on('click', function () {
      $(this).closest('.sidebar').removeClass('active');
      $('.sidebar-close').removeClass('active');
      $('.sidebar-close').find('img').attr('src', sidebarFilters);
      _functions.addScroll();
    });
  }

  //clear filter
  $('.clear-filter').on("click", function (e) {
    e.preventDefault();
    $('.sidebar-entry').each(function () {
      $('input').prop('checked', false);
      $('li.active').removeClass('active');
    });
  });


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

  // open dropdown-category
  $(".dropdown-category-item").hover(function () {
    if ($(window).width() > 1200) {
      $(this).find('.dropdown-submenu').toggleClass('active');
    }
  });

  $(".dropdown-category-item").on('click', function () {
    if ($(window).width() < 1200) {
      $(this).toggleClass('active');
      $(this).find('.dropdown-submenu').slideToggle(500);
    }
  });

  // dropdown-list
  $(document).on('click', '.navigation ul li.dropdown', function () {
    if ($(window).width() < 1200) {
      $(this).toggleClass('active').find('.dropdown-list').toggleClass('active');
    }
  });

    // dropdown-list
    $(document).on('click', '.tag', function () {
      $(this).toggleClass('active');
    });


});