jQuery(document).ready(function ($) {
  /**
   * Initialize a Swiper slider instance safely.
   * @param {string|HTMLElement} target - CSS selector string or DOM element for Swiper container.
   * @param {object} config - Swiper configuration options.
   * @returns {Swiper|null} Swiper instance or null if target not found.
   */
  function initializeSwiper(target, config) {
    const container =
      typeof target === "string" ? document.querySelector(target) : target;
    if (!container) return null;
    return new Swiper(container, config);
  }

  /**
   * Initialize the main custom slider on the home page hero banner.
   * Uses a slide effect with a transition speed of 600ms.
   */
  const slides = document.querySelectorAll(".main_custom_slider .swiper-slide");
  console.log("Slides count found:", slides.length);

  var swiper_hero_banner = new Swiper(".hero-banner .main_custom_slider", {
    speed: 600,
    effect: "slide",
    loop: slides.length > 2, // ensure slides is defined appropriately
    autoplay: {
      delay: 3000,
      disableOnInteraction: false,
      pauseOnMouseEnter: true
    },
  });

  // Register event for slide transition start
  // swiper_hero_banner.on("slideChangeTransitionStart", function () {
  //   console.log("🔄 Slide change START");

  //   // Pause previous video if any
  //   const previousSlide = this.slides[this.previousIndex];
  //   const prevVideo = previousSlide?.querySelector("video");
  //   if (prevVideo) {
  //     console.log("⏸️ Pausing previous video");
  //     prevVideo.pause();
  //     prevVideo.currentTime = 0;
  //     prevVideo.removeAttribute("data-watched");
  //   }
  // });

  // Register event for slide transition end
  // swiper_hero_banner.on("slideChangeTransitionEnd", function () {
  //   const swiperInstance = swiper_hero_banner;
  //   const currentSlide = swiper_hero_banner.slides[swiperInstance.activeIndex];
  //   const video = currentSlide?.querySelector("video");

  //   if (video) {
  //     const videoDurationMs = video.duration * 1000;
  //     console.log("🎥 Video found — pausing autoplay");
  //     console.log(videoDurationMs);
  //     console.log(swiperInstance);

  //     console.log("sss");
  //     swiperInstance.autoplay.stop();

  //     if (swiperInstance.autoplay.running) {
  //       console.log("▶️ Swiper autoplay is running");
  //     } else {
  //       console.log("⏸️ Swiper autoplay is paused/stopped");
  //     }
  //     console.log(swiperInstance.autoplay);
  //     setTimeout(function () {
  //       swiperInstance.autoplay.start();
  //       console.log("sss");
  //     }, videoDurationMs);

      // Clear any previous onended handler
      // video.onended = null;

      // const playVideo = () => {
      //   if (!video.hasAttribute("data-watched")) {
      //     console.log("▶️ Playing video..");
      //     video
      //       .play()
      //       .then(() => video.setAttribute("data-watched", "true"))
      //       .catch((err) => console.warn("⚠️ Video play failed:", err));
      //   }
      // };

      // Check if video is ready to play
      // if (video.readyState >= 2) {
      //   playVideo();
      // } else {
      //   video.addEventListener("canplay", playVideo, { once: true });
      // }

      // video.onended = () => {
      //   console.log("✅ Video ended — resuming autoplay");
      //   swiperInstance.autoplay.start();
      // };

      // video.addEventListener("mouseenter", () => {
      //   console.log("🛑 Hover on video — autoplay paused");
      //   swiperInstance.autoplay.stop();
      // });

      // video.addEventListener("mouseleave", () => {
      //   if (video.paused || video.ended) {
      //     console.log("🔁 Mouse left — autoplay resume");
      //     swiperInstance.autoplay.start();
      //   }
      // });
  //   } else {
  //     console.log("🖼️ No video in current slide — autoplay active");
  //     if (!swiperInstance.autoplay.running) {
  //       swiperInstance.autoplay.start();
  //     }
  //   }
  // });

  // const swiper = new Swiper('.hero-banner .main_custom_slider', {
  //   loop: true,
  //   slidesPerView: 1,
  //   autoplay: {
  //       delay: 3000, // 3 seconds default
  //       disableOnInteraction: false
  //   },
  //   on: {
  //       transitionStart: function () {
  //           const currentSlide = document.querySelector('.hero-banner .main_custom_slider .swiper-slide-active');
  //           const video = currentSlide.querySelector('.hero-banner .main_custom_slider video');

  //           // Check if the current slide contains a video
  //           if (video) {
  //               swiper.autoplay.stop(); // Stop automatic sliding
  //               video.currentTime = 0; // Reset video to start
  //               video.play(); // Play video automatically
  //               video.onended = () => {
  //                   swiper.slideNext(); // Move to the next slide when video ends
  //                   swiper.autoplay.start(); // Resume autoplay for other slides
  //               };
  //           }
  //       }
  //   }
  // });

  /**
   * Initialize the "Helmet Parts – New Launches" slider.
   * - Enables looping and pagination with clickable dots.
   */
  var helmet_parts_section = $(".helmet-parts-swiper").closest(
    ".swiper-section"
  );
  initializeSwiper(".helmet-parts-swiper", {
    loop: true,
    spaceBetween: 20,
    pagination: {
      el: helmet_parts_section.find(".helmet-parts-pagination")[0],
      clickable: true,
    },
    breakpoints: {
      0: {
        slidesPerView: 2,
      },
      550: {
        slidesPerView: 3,
      },
      1025: {
        slidesPerView: 5,
      },
    },
    navigation: {
      nextEl: helmet_parts_section.find(".swiper-button-next")[0],
      prevEl: helmet_parts_section.find(".swiper-button-prev")[0],
    },
  });

  /**
   * Initialize the DC Collection Slider.
   *
   * - Enables infinite loop for continuous sliding.
   * - Adds pagination with clickable bullets using Swiper's built-in pagination support.
   */
  initializeSwiper("#drifter-slider-section .drifter-slider-swiper", {
    loop: true,
    pagination: {
      el: ".swiper-pagination",
      clickable: true,
    },
  });

  /**
   * Initializes paired Swipers inside each `.accessories_slider`:
   * - `main_custom_slider`: Thumbnail slider with responsive item counts.
   * - `custom_slider`: Main slider linked to thumbnail for navigation.
   */
  document.querySelectorAll(".accessories_slider").forEach(function (section) {
    const swiperThumbs = initializeSwiper(
      section.querySelector(".main_custom_slider"),
      {
        spaceBetween: 0,
        watchSlidesProgress: true,
        speed: 500,
        centeredSlides: false,
        initialSlide: 1,
        breakpoints: {
          320: {
            slidesPerView: 2,
          },
          480: {
            slidesPerView: 3,
          },
          768: {
            slidesPerView: 3,
          },
          1024: {
            slidesPerView: 4,
          },
          1440: {
            slidesPerView: 6,
          },
          1920: {
            slidesPerView: 8,
          },
        },
      }
    );

    const swiperMain = initializeSwiper(
      section.querySelector(".custom_slider"),
      {
        speed: 600,
        navigation: {
          nextEl: section.querySelector(".swiper-button-next"),
          prevEl: section.querySelector(".swiper-button-prev"),
        },
        thumbs: {
          swiper: swiperThumbs,
        },
      }
    );
  });

  /**
   * Initialize main custom slider with autoplay, navigation, and pagination.
   */
  initializeSwiper(".main_custom_slider", {
    loop: true,
    spaceBetween: 30,
    navigation: {
      nextEl: ".swiper-button-next",
      prevEl: ".swiper-button-prev",
    },
    pagination: {
      el: ".swiper-pagination",
      clickable: true,
    },
    autoplay: {
      delay: 5000,
      disableOnInteraction: false,
    },
  });

  /**
   * Initialize testimonial slider.
   */
  initializeSwiper(".testimonial_slider .main_custom_slider", {
    slidesPerView: 1,
    initialSlide: 3,
    spaceBetween: 0,
    loop: true,
    slideToClickedSlide: true,
    pagination: {
      el: ".swiper-pagination",
      clickable: true,
    },
    navigation: {
      nextEl: ".swiper-button-next",
      prevEl: ".swiper-button-prev",
    },
    breakpoints: {
      640: {
        slidesPerView: 2,
      },
      768: {
        slidesPerView: 2,
      },
      1025: {
        slidesPerView: 5,
      },
    },
  });

  /**
   * Initialize category slider (used for category thumbnails or cards).
   */
  initializeSwiper("#categories__swipe", {
    loop: true,
    spaceBetween: 20,
    autoplay: {
      delay: 3000,
    },
    pagination: {
      el: ".swiper-pagination",
      clickable: true,
    },
    breakpoints: {
      0: {
        slidesPerView: 1,
      },
      768: {
        slidesPerView: 2,
      },
      1025: {
        slidesPerView: 4,
      },
    },
  });

  /**
   * Initialize Blog Section Slider on the Home Page
   */

  initializeSwiper(".blog-section-carousel", {
    loop: true,
    spaceBetween: 20,
    autoplay: {
      delay: 3000,
    },
    navigation: {
      nextEl: ".swiper-button-next",
      prevEl: ".swiper-button-prev",
    },
    pagination: {
      el: ".swiper-pagination",
      clickable: true,
    },
    breakpoints: {
      0: {
        slidesPerView: 1,
      },
      600: {
        slidesPerView: 2,
      },
      1025: {
        slidesPerView: 3,
      },
    },
  });

  /**
   * Initialize Subcategories Slider
   */
  initializeSwiper(".subcategories_categories", {
    loop: true,
    spaceBetween: 24,
    navigation: {
      nextEl: ".swiper-button-next",
      prevEl: ".swiper-button-prev",
    },
    breakpoints: {
      0: {
        slidesPerView: 1,
      },
      768: {
        slidesPerView: 2,
      },
      1025: {
        slidesPerView: 3,
      },
    },
  });

  /**
   * Initialize Testimonial Review Slider
   */
  initializeSwiper(".testi_review_section", {
    slidesPerView: 1,
    initialSlide: 3,
    spaceBetween: 26,
    loop: true,
    slideToClickedSlide: true, // :point_left: Slide to clicked slide
    pagination: {
      el: ".swiper-pagination",
      clickable: true,
    },
    navigation: {
      nextEl: ".swiper-button-next",
      prevEl: ".swiper-button-prev",
    },
    breakpoints: {
      640: {
        slidesPerView: 1,
      },
      768: {
        slidesPerView: 2,
      },
      1025: {
        slidesPerView: 3,
      },
      1920: {
        slidesPerView: 3,
      },
    },
  });

  /**
   * Initialize Main Product Slider
   */
  const swiperProduct = new Swiper(".myProductSwiper", {
    slidesPerView: 5,
    loop: true,
    centeredSlides: true,
    spaceBetween: 10,
    freeMode: true,
    watchSlidesProgress: true,
    speed: 500,
    allowTouchMove: false,
    simulateTouch: false,
    navigation: {
      nextEl: ".swiper-button-next.main-slider-next",
      prevEl: ".swiper-button-prev.main-slider-prev",
    },
    breakpoints: {
      0: { slidesPerView: 1 },
      768: { slidesPerView: 3 },
      1025: { slidesPerView: 5 },
    },
    on: {
      slideChange: updateCenteredSlide,
    },
  });

  /**
   * Home Page Graphics Section
   *
   * Manages individual product sliders with synchronized main image and color swatches.
   * Highlights the centered product in the main product slider.
   */

  function updateCenteredSlide() {
    $(".graphics-section-single-product").removeClass("active-center");
    $(
      ".myProductSwiper .swiper-slide.swiper-slide-active .graphics-section-single-product"
    ).addClass("active-center");
  }

  // Initialize swipers for each product
  $(".graphics-section-single-product").each(function () {
    const $product = $(this);

    // Main image swiper
    const $mainImageSwiper = $product.find(".main-image-swiper")[0];
    const mainSwiper = new Swiper($mainImageSwiper, {
      slidesPerView: 1,
      loop: false,
      spaceBetween: 10,
      navigation: {
        nextEl: $product.find(".main-image-next")[0],
        prevEl: $product.find(".main-image-prev")[0],
      },
    });

    // Store reference for later use
    $product.data("main-swiper", mainSwiper);

    // Color swatch swiper
    const $swatchSwiper = $product.find(".color-swatches-swiper")[0];
    new Swiper($swatchSwiper, {
      slidesPerView: "auto",
      spaceBetween: 10,
      loop: false,
    });

    // Attach click handler to swatches
    $product.find(".color-swatches").on("click", ".swatch-slide", function () {
      const index = $(this).index();
      const swiper = $product.data("main-swiper");
      if (swiper) swiper.slideTo(index);
    });
  });

  // Highlight and bind functionality to the centered product
  updateCenteredSlide();
  swiperProduct.on("slideChangeTransitionEnd", function () {
    updateCenteredSlide();
  });

  // Add height to Scroll to top
  $('a[href="#drifter-slider-section"]').click(function (e) {
    console.log("adhasdyha");
    e.preventDefault();

    var target = $("#drifter-slider-section");

    if (target.length) {
      var headerHeight = $(".site-header").outerHeight() || 200; // fallback if header not found
      $("html, body").animate(
        {
          scrollTop: target.offset().top - headerHeight,
        },
        1000
      );

      return false;
    }
  });
});

// footer dropdown menu

function handleMobileDropdown() {
  if (jQuery(window).width() <= 767) {
    // Hide all dropdown contents
    jQuery(".foo_column_content").hide();

    // Prevent duplicate event bindings
    jQuery(".foo_column_title")
      .off("click")
      .css("cursor", "pointer")
      .on("click", function () {
        jQuery(this).toggleClass("open");
        jQuery(this).next(".foo_column_content").slideToggle(200);
      });
  } else {
    // On larger screens, show all content and remove click events
    jQuery(".foo_column_content").show();
    jQuery(".foo_column_title")
      .off("click")
      .removeClass("open")
      .css("cursor", "default");
  }
}

jQuery(document).ready(function () {
  handleMobileDropdown();

  jQuery(window).on("resize", function () {
    handleMobileDropdown();
  });
});
