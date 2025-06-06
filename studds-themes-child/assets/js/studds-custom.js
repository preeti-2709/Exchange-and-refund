jQuery(document).ready(function ($) {
  var windowWidth = $(window).width();
  console.log("Window width is: " + windowWidth + "px");

  // console.log(ajax_params.ajax_url);
  

  /* Home page hero section New Updates button  */
  $(".new-updates-tab").on("click", function () {
    $(".new-updates-popup").addClass("active");
    $(".new-updates-tab").addClass("new-updates-move active");
  });

  $(".close-popup").on("click", function () {
    $(".new-updates-popup").removeClass("active");
    $(".new-updates-tab").removeClass("new-updates-move");
  });



  /* Move to Top Button */
  var backToTop = $("#back-to-top-btn");
  backToTop.hide();
  // Safe check: if .hero-banner exists
  var bannerHeight = $(".hero-banner").length
    ? $(".hero-banner").outerHeight() - 120
    : 300;

    function toggleBackToTop() {
      if ($(window).scrollTop() > bannerHeight) {
        backToTop.fadeIn();
      } else {
        backToTop.fadeOut();
      }
    }
  toggleBackToTop();
  $(window).on("scroll", function () {
    toggleBackToTop();
  });
  backToTop.on("click", function (e) {
    e.preventDefault();
    $("html, body").animate({ scrollTop: 0 }, 300);
  });

  /* Move to Top Button */

  const tagsToShow = 6; // Number of tags to display initially
  const $tagCloud = $("#tag_cloud-2 .tagcloud");
  const $tags = $tagCloud.find("a");
  const totalTags = $tags.length;

  // Initially hide all tags except the first few
  $tags.slice(tagsToShow).hide();

  // Dynamically add the Load More button
  if (totalTags > tagsToShow) {
    $tagCloud.after(
      '<a id="loadMore" style="margin-top: 10px;">Load More...</a>'
    );
  }

  // Load More button click event
  $("#loadMore").on("click", function () {
    const hiddenTags = $tags.filter(":hidden");
    hiddenTags.slice(0, tagsToShow).fadeIn();

    if (hiddenTags.length <= tagsToShow) {
      $(this).fadeOut();
    }
  });

  // slider-status js

  const totalSlides = 6;
  let currentSlide = 1;

  function updateSliderStatus(slideNumber) {
    let percentage = ((slideNumber - 1) / (totalSlides - 1)) * 100;
    $(".mobile_slider_wrap .current").text(
      slideNumber.toString().padStart(2, "0")
    );
    $(".mobile_slider_wrap .progress-fill").css("width", percentage + "%");
  }

  // Example: update every 2 seconds
  setInterval(() => {
    currentSlide = currentSlide < totalSlides ? currentSlide + 1 : 1;
    updateSliderStatus(currentSlide);
  }, 2000);

  /* search button */
  $("#blog-search-form").on("submit", function (e) {
    var searchInput = $("#search").val().trim();
    if (searchInput === "") {
      e.preventDefault();
      window.location.href = "/blog/";
    }
  });
  /* search button */


  // second about us section
  function showSlide(year) {
    $(".about-slide-content").removeClass("active").fadeOut(300);
    $("#slide-" + year)
      .fadeIn(300)
      .addClass("active");
    $(".tab").removeClass("active-tab");
    $(".tab")
      .filter(function () {
        return $(this).text().trim() === year;
      })
      .addClass("active-tab");
  }

  $(".about-slide-content").first().fadeIn(300).addClass("active");
  $(".tab").first().addClass("active-tab");

  $(".tab").on("click", function () {
    var year = $(this).text().trim();
    showSlide(year);
  });

  // Counter section on the home page
  // $(".counter-number").each(function () {
  //   var $this = $(this);
  //   var target = parseInt($this.attr("data-target"));

  //   $({ countNum: $this.text() }).animate(
  //     { countNum: target },
  //     {
  //       duration: 2000,
  //       easing: "swing",
  //       step: function () {
  //         $this.text(Math.floor(this.countNum));
  //       },
  //       complete: function () {
  //         $this.text(this.countNum);
  //       },
  //     }
  //   );
  // });

 /* Counter section on the home page */ 
var counterTriggered = false;
function runCounter() {
  $(".counter-number").each(function () {
    var $this = $(this);
    var target = parseInt($this.attr("data-target"));

    $({ countNum: 0 }).animate(
      { countNum: target },
      {
        duration: 2000,
        easing: "swing",
        step: function () {
          $this.text(Math.floor(this.countNum));
        },
        complete: function () {
          $this.text(this.countNum);
        },
      }
    );
  });
}

$(window).on("scroll", function () {
  var $counterSection = $(".counter-wrapper");
  if ($counterSection.length === 0 || counterTriggered) return;

  var sectionTop = $counterSection.offset().top;
  var sectionBottom = sectionTop + $counterSection.outerHeight();
  var scrollTop = $(window).scrollTop();
  var windowHeight = $(window).height();

  if (scrollTop + windowHeight > sectionTop && scrollTop < sectionBottom) {
    counterTriggered = true;
    runCounter();
  }
});
 /* Counter section on the home page end*/ 


  // test code----------------------------------------------
  var totalItems = $(".accessories-text .accessory-item").length;
  var currentIndex = 0;

  // Slide to Item Function
  function slideTo(index) {
    // Handle Text Slide (Vertical Scroll)
    var itemHeight = $(".accessory-item").outerHeight(true);
    var textScroll =
      index * itemHeight -
      $(".accessories-text-wrapper").height() / 2 +
      itemHeight / 2;

    $(".accessory-item").removeClass("active");
    $(".accessory-item").eq(index).addClass("active");

    $(".accessories-text").css({
      transform: "translateY(" + -textScroll + "px)",
    });

    // Handle Image Slide (Vertical Scroll)
    var imageHeight = $(".accessory-slide").outerHeight(true);
    var imageScroll = -index * imageHeight;

    $(".accessories-image-slider").css({
      transform: "translateY(" + imageScroll + "px)",
    });

    $(".accessory-slide").removeClass("active");
    $(".accessory-slide").eq(index).addClass("active");
  }

  // Handle Click on Text Item
  $(".accessory-item").click(function () {
    currentIndex = $(this).data("index");
    slideTo(currentIndex);
  });

  // Handle Click on Image Slide
  $(".accessory-slide").click(function () {
    currentIndex = $(this).data("index");
    slideTo(currentIndex);
  });

  // Handle Up Arrow Click
  $(".up-arrow").click(function () {
    currentIndex--;
    if (currentIndex < 0) {
      currentIndex = totalItems - 1;
    }
    slideTo(currentIndex);
  });

  // Handle Down Arrow Click
  $(".down-arrow").click(function () {
    currentIndex++;
    if (currentIndex >= totalItems) {
      currentIndex = 0;
    }
    slideTo(currentIndex);
  });

  // Auto Slide (Optional)
  setInterval(function () {
    currentIndex++;
    if (currentIndex >= totalItems) {
      currentIndex = 0;
    }
    slideTo(currentIndex);
  }, 5000); // Change slide every 5 seconds

  // Testimonial Slider section home page

      // Category widget
      $(".ts-dropdown-toggle").on("click", function () {
        const $dropdown = $(this).next();
        $dropdown.toggle();
      });

      $(".filters-container .widget-title-wrapper").each(function () {
      const $widget = $(this);
      const $title = $widget.find(".widget-title");
      const $content = $widget.next(); // assumes .widget-content is the next sibling

      if ($title.length && $content.length) {
        $title.css("cursor", "pointer");
        $content.addClass("d-none"); // Hide all contents initially

        // Add dropdown arrow
        const $arrow = $("<span><svg width='24' height='24' viewBox='0 0 24 24' fill='none' xmlns='http://www.w3.org/2000/svg'><path d='M6 9L12 15L18 9' stroke='#010101' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'/></svg></span>");
        $title.append($arrow);

        $title.on("click", function () {
          // Close all other contents and reset arrows
          $(".filters-container .widget-title-wrapper").each(function () {
            const $otherContent = $(this).next();
            const $otherArrow = $(this).find("span");
            if ($otherContent[0] !== $content[0]) {
              $otherContent.addClass("d-none");
              $otherArrow.html("<svg width='24' height='24' viewBox='0 0 24 24' fill='none' xmlns='http://www.w3.org/2000/svg'><path d='M6 9L12 15L18 9' stroke='#010101' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'/></svg>");
            }
          });

          // Toggle current
          const isNowVisible = $content.hasClass("d-none");
          $content.toggleClass("d-none");
          $arrow.html(isNowVisible ? "<svg width='24' height='24' viewBox='0 0 24 24' fill='none' xmlns='http://www.w3.org/2000/svg'><path d='M18 14.9998L11.9999 8.99989L6 15' stroke='#010101' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'/></svg>" : " <svg width='24' height='24' viewBox='0 0 24 24' fill='none' xmlns='http://www.w3.org/2000/svg'><path d='M6 9L12 15L18 9' stroke='#010101' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'/></svg>");
        });
      }
    });

    // Reveal the container after setup
    $(".filters-container").removeClass("d-none");
    
    /*
    ** Investor Relation - Draft Red Herring Prospectus
    */
      const noModalEl = $('#noCheckboxModal')[0];
      const confirmModalEl = $('#confirmModal')[0];
      const cancelModalEl = $('#cancelModal')[0];

      if (noModalEl && confirmModalEl && cancelModalEl) {
        const noCheckboxModal = new bootstrap.Modal(noModalEl);
        const confirmModal = new bootstrap.Modal(confirmModalEl);
        const cancelModal = new bootstrap.Modal(cancelModalEl);

        $('#investor-relation-confirm-btn').on('click', function() {
            if (!$('#disclaimer-check').is(':checked')) {
                noCheckboxModal.show();
            } else {
                confirmModal.show();
            }
        });

        $('#investor-relation-cancel-btn').on('click', function() {
            cancelModal.show();
        });
      }



    // TOp Header Toggle Js
    
    // function handleMobileHeaderToggle() {
    //   if (Math.max(window.outerWidth, jQuery(window).width()) <= 767) {
    //     // Hide initially and reset state
    //     jQuery('.group-meta-header.mobile_wrap').hide().removeClass('open');
    //     jQuery('.ts-group-meta-icon-toggle').removeClass('active');
    
    //     // Remove previous event handlers to avoid stacking
    //     jQuery('.ts-group-meta-icon-toggle').off('click');
    
    //     // Add toggle click event
    //     jQuery('.ts-group-meta-icon-toggle').on('click', function () {
    //       jQuery('.group-meta-header.mobile_wrap').slideToggle(600).toggleClass('open');
    //       jQuery(this).toggleClass('active');
    //     });
    //   } else {
    //     // For larger screens, reset styles and unbind click events
    //     jQuery('.group-meta-header.mobile_wrap').show().removeClass('open');
    //     jQuery('.ts-group-meta-icon-toggle').removeClass('active').off('click');
    //   }
    // }
    
    // jQuery(document).ready(function () {
    //   handleMobileHeaderToggle();
    
    //   jQuery(window).on('resize', function () {
    //     handleMobileHeaderToggle();
    //   });
    // });




});



