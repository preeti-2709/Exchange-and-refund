jQuery(document).ready(function ($) {
  // Function to update both images and product link
  function updateProductImagesAndLink(
    $productSection,
    attributeKey,
    attributeValue,
    mainImageUrl
  ) {
    const productClass = $productSection.attr("class");
    const productID = productClass.match(/post-(\d+)/)?.[1];

    if (productID && attributeValue) {
      const attributes = {};
      attributes[attributeKey] = attributeValue;

      // Update the product link URL (only for the <a> directly inside .thumbnail-wrapper)
      const $productLink = $productSection
        .find(".thumbnail-wrapper > a")
        .first(); // Ensures targeting the correct <a>
      const originalUrl = $productLink.attr("href").split("?")[0];
      const updatedUrl = `${originalUrl}?${attributeKey}=${attributeValue}`;
      $productLink.attr("href", updatedUrl);

      // Update the main product image
      const $mainImage = $productSection.find("img.wp-post-image");
      if (mainImageUrl) {
        $mainImage.attr("src", mainImageUrl);
        $mainImage.attr("data-src", mainImageUrl);
      }

      // Fetch and update the back image via AJAX
      $.ajax({
        url: shop_params.ajax_url,
        type: "POST",
        data: {
          action: "get_matched_variation_image",
          product_id: productID,
          attributes: attributes,
        },
        success: function (response) {
          if (response.success) {
            const additionalImageUrl = response.data.image_url;
            const mainImageUrl = response.data.main_url;
            const $backImage = $productSection.find(".product-image-back");
            const mainImageUrlSection = $productSection.find(".wp-post-image");

            // Update the back image
            $backImage.attr("src", additionalImageUrl);
            $backImage.attr("data-src", additionalImageUrl);
            mainImageUrlSection.attr("src", mainImageUrl);
            mainImageUrlSection.attr("data-src", mainImageUrl);
          }
        },
        error: function (jqXHR, textStatus, errorThrown) {
          console.error("AJAX Error:", textStatus, errorThrown);
          if (jqXHR.responseJSON && jqXHR.responseJSON.data) {
            console.error("Error details:", jqXHR.responseJSON.data);
          } else if (jqXHR.responseText) {
            console.error("Response Text:", jqXHR.responseText);
          }
        },
      });
    }
  }

  // Function to collect all selected filter values
  function getAllSelectedFilters() {
    const selectedFilters = {};

    // Collect selected base colors
    const selectedBaseColors = [];
    $('#base-color-filter-form input[type="checkbox"]:checked').each(
      function () {
        selectedBaseColors.push($(this).val());
      }
    );
    if (selectedBaseColors.length) {
      selectedFilters.base_color = selectedBaseColors;
    }

    // Collect selected sizes
    const selectedSizes = [];
    $('#size-filter-form input[type="checkbox"]:checked').each(function () {
      selectedSizes.push($(this).val());
    });
    if (selectedSizes.length) {
      selectedFilters.size = selectedSizes;
    }

    // Collect selected categories
    const selectedCategories = [];
    $('#category-filter-form input[type="checkbox"]:checked').each(function () {
      selectedCategories.push($(this).val());
    });
    if (selectedCategories.length) {
      selectedFilters.category = selectedCategories;
    }

    // Collect selected finishes
    const selectedFinishes = [];
    $('#finish-filter-form input[type="checkbox"]:checked').each(function () {
      selectedFinishes.push($(this).val());
    });
    if (selectedFinishes.length) {
      selectedFilters.finish = selectedFinishes;
    }

    // Collect selected models
    const selectedModels = [];
    $('#model-filter-form input[type="checkbox"]:checked').each(function () {
      selectedModels.push($(this).val());
    });
    if (selectedModels.length) {
      selectedFilters.model = selectedModels;
    }

    // Collect selected ratings
    const selectedRatings = [];
    $('#rating-filter-form input[type="checkbox"]:checked').each(function () {
      selectedRatings.push($(this).val());
    });
    if (selectedRatings.length) {
      selectedFilters.rating = selectedRatings;
    }

    return selectedFilters;
  }

  // Function to handle AJAX requests for filters
  function filterWidgetAjaxFunction(
    action,
    requestData,
    targetElementId,
    urlParamKey,
    selectedValues = {}
  ) {
    // Extract the current page number from the URL
    const currentUrl = new URL(window.location.href);
    const currentPage = currentUrl.pathname.match(/page\/(\d+)/)
      ? currentUrl.pathname.match(/page\/(\d+)/)[1]
      : 1;

    // Include the page number in the AJAX request
        if (!requestData.paged) {
          requestData.paged = currentPage;
        }
    $.ajax({
      url: ajax_params.ajax_url,
      type: "GET",
      data: {
        action: action,
        ...requestData, // Spread all selected filter data
      },
      success: function (response) {
        $(`#${targetElementId}`).html(response);

        // Get all selected filters and update the UI
        const selectedFilters = getAllSelectedFilters();
        updateSelectedFilters(selectedFilters);

        // Update the URL with selectedValues
        const updatedUrl = new URL(window.location.href);
        const searchParams = new URLSearchParams();

        // Add selectedValues to the URL search params
        for (const [key, values] of Object.entries(selectedValues)) {
          if (values.length > 0) {
            searchParams.append(key, values.join(","));
          }
        }

        // Replace the existing query string with the updated search params
        updatedUrl.search = searchParams.toString();

        // Push the updated URL to the browser's history
        history.pushState(null, "", decodeURIComponent(updatedUrl.toString()));
      },
      error: function () {
        $(`#${targetElementId}`).html("<p>Error loading results.</p>");
      },
    });
  }

  // AJAX success callback to update selected filters dynamically
  function updateSelectedFilters(selectedFilters) {
    const $selectedFiltersContainer = $(
      ".selected_filter_roles .selected-filters"
    );
    $selectedFiltersContainer.empty(); // Clear existing filters

    // Iterate through each filter category and add it to the list
    for (const [filterKey, filterValues] of Object.entries(selectedFilters)) {
      filterValues.forEach((value) => {
        const filterHTML = `
        <li data-filter-key="${filterKey}" data-filter-value="${value}">
          <span>${value}</span>
          <svg width="14" height="14" class="removeFilter" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M3.73366 11.0837L2.91699 10.267L6.18366 7.00033L2.91699 3.73366L3.73366 2.91699L7.00033 6.18366L10.267 2.91699L11.0837 3.73366L7.81699 7.00033L11.0837 10.267L10.267 11.0837L7.00033 7.81699L3.73366 11.0837Z" fill="#ED2228"></path>
          </svg>
        </li>
      `;
        $selectedFiltersContainer.append(filterHTML);
      });
    }

    // Show or hide the "Clear All" button based on filter count
    if (Object.keys(selectedFilters).length > 0) {
      $(".clear-all-filters").show();
    } else {
      $(".clear-all-filters").hide();
    }
  }

  // Function to trigger the filter process
  function triggerProductFilter() {
    const selectedFilters = getAllSelectedFilters();

    // Call the common AJAX function
    filterWidgetAjaxFunction(
      "filter_products", // Unified action name
      selectedFilters, // Request data
      "studds_product_filter_results", // Target element ID
      "filters", // URL parameter key
      selectedFilters // Selected values
    );
  }

  // Function to preselect filters based on URL parameters
  function preselectFiltersFromURL() {
    const urlParams = new URLSearchParams(window.location.search);

    // Helper function to preselect checkboxes
    function preselectCheckboxes(formId, paramName) {
      const paramValues = urlParams.get(paramName)?.split(",") || [];
      if (paramValues.length) {
        $(`#${formId} input[type="checkbox"]`).each(function () {
          if (paramValues.includes($(this).val())) {
            $(this).prop("checked", true);
          }
        });
      }
    }

    // Preselect filters for each form
    preselectCheckboxes("base-color-filter-form", "base_color");
    preselectCheckboxes("size-filter-form", "size");
    preselectCheckboxes("category-filter-form", "category");
    preselectCheckboxes("finish-filter-form", "finish");
    preselectCheckboxes("model-filter-form", "model");
    preselectCheckboxes("rating-filter-form", "rating");
  }

  // Trigger filter processing on page load based on preselected filters
  preselectFiltersFromURL();

  // Trigger the filter process after preselecting filters
  // triggerProductFilter();

  $(document).on(
    "click",
    ".woocommerce-pagination .page-numbers a",
    function (e) {
      e.preventDefault(); // Prevent default link behavior

      const page = jQuery(this).data("page");

      var selectedFilters = getAllSelectedFilters();

      selectedFilters.paged = page;
      // console.log(selectedFilters);

      // Call the common AJAX function
      filterWidgetAjaxFunction(
        "filter_products", // Unified action name
        selectedFilters, // Request data
        "studds_product_filter_results", // Target element ID
        "filters", // URL parameter key
        selectedFilters // Selected values
      );
    }
  );

  // Event listener to remove individual filter
  $(document).on("click", ".removeFilter", function () {
    const filterElement = $(this).closest("li");
    const filterKey = filterElement.data("filter-key");
    const filterValue = filterElement.data("filter-value");

    // Remove the filter value from the respective filter category
    const selectedFilters = getAllSelectedFilters();
    if (selectedFilters[filterKey]) {
      selectedFilters[filterKey] = selectedFilters[filterKey].filter(
        (value) => value !== filterValue
      );

      // If the category becomes empty, delete it
      if (selectedFilters[filterKey].length === 0) {
        delete selectedFilters[filterKey];
      }
    }

    // Uncheck the corresponding checkbox in the filter form
    $(`input[type="checkbox"][value="${filterValue}"]`).prop("checked", false);

    // Update the UI and trigger filtering
    updateSelectedFilters(selectedFilters);
    triggerProductFilter(); // Re-fetch the products with updated filters
  });

  // Attach event listeners for all filters
  $('#base-color-filter-form input[type="checkbox"]').on(
    "change",
    triggerProductFilter
  );
  $('#size-filter-form input[type="checkbox"]').on(
    "change",
    triggerProductFilter
  );
  $('#category-filter-form input[type="checkbox"]').on(
    "change",
    triggerProductFilter
  );
  $('#finish-filter-form input[type="checkbox"]').on(
    "change",
    triggerProductFilter
  );
  $('#model-filter-form input[type="checkbox"]').on(
    "change",
    triggerProductFilter
  );
  $('#rating-filter-form input[type="checkbox"]').on(
    "change",
    triggerProductFilter
  );

  // Clear all selected filters
  $(document).on("click", ".clear-filer", function () {
    // Uncheck all checkboxes in the filter forms
    $("input[type='checkbox']").prop("checked", false);

    // Trigger the filter update to reflect the cleared state
    updateSelectedFilters({});
    triggerProductFilter(); // Re-fetch the products with no filters
  });

  //add class selected if checkbox is checked
  $("#base-color-filter-form label").on("click", function () {
    var $checkbox = $(this).find('input[type="checkbox"]');
    // Toggle checkbox checked property
    $checkbox.prop("checked", !$checkbox.prop("checked"));
    // Toggle selected class based on checked
    $(this).toggleClass("selected", $checkbox.prop("checked"));
  });

  // Handle swatch click events
  $(".vi-wpvs-option-wrap").on("click", function () {
    const $swatch = $(this);

    const $productSection = $swatch.closest("section.product");
    const attributeKey = "attribute_pa_color";
    const attributeValue = $swatch.attr("data-attribute_value");
    const mainImageUrl = $swatch.attr("data-loop_source");

    updateProductImagesAndLink(
      $productSection,
      attributeKey,
      attributeValue,
      mainImageUrl
    );
  });

  // On page load, handle default variation
  $("section.product").each(function () {
    const $productSection = $(this);
    const $defaultSwatch = $productSection.find(
      ".vi-wpvs-option-wrap-selected"
    );

    if ($defaultSwatch.length) {
      const attributeKey = "attribute_pa_color";
      const attributeValue = $defaultSwatch.attr("data-attribute_value");
      const mainImageUrl = $defaultSwatch.attr("data-loop_source");

      // Update the product link and images with the default variation
      updateProductImagesAndLink(
        $productSection,
        attributeKey,
        attributeValue,
        mainImageUrl
      );
    }
  });
});
