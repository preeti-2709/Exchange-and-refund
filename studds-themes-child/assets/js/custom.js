jQuery(document).ready(function ($) {
  /*
  ** Home Page - Our Story Section
  */ 
  document
    .querySelectorAll(".thunder-product-slider.our-stories-slider")
    .forEach(function (section) {
      const swiperThumbs = new Swiper(section.querySelector(".main_custom_slider"), {
        spaceBetween: 0,
        watchSlidesProgress: true,
        watchSlidesVisibility: true,
        centeredSlides: false,       
        slideToClickedSlide: true, 
        speed: 700,
        loop: true,
        breakpoints: {
          320: {
            slidesPerView: 2,
          },
          480: {
            slidesPerView: 3,
          },
          768: {
            slidesPerView: 4,
          },
          1025: {
            slidesPerView: 6,
          },
        },
      });

      const swiperMain = new Swiper(section.querySelector(".custom_slider"), {
        speed: 700,
        loop: true,
        effect: "fade",
        fadeEffect: {
          crossFade: true,
        },
        navigation: {
          nextEl: section.querySelector(".swiper-button-next"),
          prevEl: section.querySelector(".swiper-button-prev"),
        },
        thumbs: {
          swiper: swiperThumbs,
        },
        on: {
          slideChange: function () {
            const realIndex = this.realIndex;
            swiperThumbs.slideToLoop(realIndex);
          }
        }
      });
    });

});
