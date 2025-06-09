jQuery(document).ready(function ($) {
 /*
** Media Page Ajax
*/ 
function loadMediaContent(year = '', page = 1, updateUrl = true) {
    $.ajax({
        url: corporate_page_ajax.ajaxurl,
        method: 'POST',
        data: {
            action: 'load_media_items',
            nonce: corporate_page_ajax.nonce,
            year: year,
            page: page,
        },
        beforeSend: function () {
            $('.media-grid').html('<div class="loading">Loading...</div>');
        },
        success: function (response) {
            if (response.success) {
                $('.media-grid').html(response.data.content);
                $('.ts-pagination.media-page').html(response.data.pagination);

                // Set active tab
                $('.media-tab').removeClass('active');
                $('.media-tab[data-year="' + year + '"]').addClass('active');

                // Update browser URL if needed
                if (updateUrl) {
                    let newUrl = '/media';

                    if (page > 1) {
                        newUrl += '/page/' + page + '/';
                    }

                    if (year) {
                        newUrl += (page > 1 ? '?' : '/?') + 'year=' + encodeURIComponent(year);
                    }

                    history.pushState(null, '', newUrl);
                }

            } else {
                $('.media-grid').html('<div class="no-media-message">No media items found.</div>');
                $('.ts-pagination.media-page').empty();
            }
        }
    });
}

// Year tab click
$('.media-tab').on('click', function (e) {
    e.preventDefault();
    const year = $(this).data('year') || '';
    loadMediaContent(year, 1, true);
});

// Pagination click
$(document).on('click', '.ts-pagination.media-page a', function (e) {
    e.preventDefault();

    // Try to extract the page number from pagination
    const href = $(this).attr('href');
    let page = 1;
    const match = href.match(/page\/(\d+)/);
    if (match && match[1]) {
        page = parseInt(match[1]);
    } else {
        const number = parseInt($(this).text());
        if (!isNaN(number)) page = number;
    }

    const year = $('.media-tab.active').data('year') || '';
    loadMediaContent(year, page, true);
});

// Handle browser back/forward buttons
window.addEventListener('popstate', function () {
    const urlParams = new URLSearchParams(window.location.search);
    const year = urlParams.get('year') || '';
    const match = window.location.pathname.match(/page\/(\d+)/);
    const page = match ? parseInt(match[1]) : 1;
    loadMediaContent(year, page, false); // Do not push URL again
});

// On initial load (based on URL)
(function () {
    const urlParams = new URLSearchParams(window.location.search);
    const year = urlParams.get('year') || '';
    const match = window.location.pathname.match(/page\/(\d+)/);
    const page = match ? parseInt(match[1]) : 1;
    loadMediaContent(year, page, false);
})();


/*
** Event Page Ajax
*/ 

function loadEvents(yearSlug = '', page = 1, updateUrl = true) {
    $.ajax({
        url: corporate_page_ajax.ajaxurl,
        type: 'POST',
        data: {
            action: 'load_events',
            nonce: corporate_page_ajax.nonce,
            year: yearSlug,
            paged: page,
        },
        beforeSend: function () {
            $('.event-grid').html('<p>Loading...</p>');
        },
        success: function (response) {
            if (response.success) {
                $('.event-grid').html(response.data.events);
                $('.ts-pagination.event-page').html(response.data.pagination);
                $('.event-tab').removeClass('active');

                if (yearSlug === '') {
                    $('.event-tab[data-year=""]').addClass('active');
                } else {
                    $('.event-tab[data-year="' + yearSlug + '"]').addClass('active');
                }

                // Update URL using history API
                if (updateUrl) {
                    let newUrl = '/events';
                    
                    if (page > 1) {
                        newUrl += '/page/' + page + '/';
                    }

                    if (yearSlug) {
                        newUrl += (page > 1 ? '?' : '/?') + 'year=' + yearSlug;
                    }

                    history.pushState(null, '', newUrl);
                }

            } else {
                $('.event-grid').html('<p>No events found.</p>');
                $('.ts-pagination.event-page').html('');
            }
        }
    });
}

// Tab click handler
$('.event-tabs .event-tab').on('click', function (e) {
    e.preventDefault();
    const year = $(this).data('year') || '';
    loadEvents(year, 1, true);
});

// Pagination click handler
$(document).on('click', '.ts-pagination.event-page a', function (e) {
    e.preventDefault();
    const year = $('.event-tab.active').data('year') || '';
    const page = parseInt($(this).text()) || 1;
    loadEvents(year, page, true);
});

// Handle browser back/forward buttons
window.addEventListener('popstate', function () {
    const urlParams = new URLSearchParams(window.location.search);
    const year = urlParams.get('year') || '';
    const matches = window.location.pathname.match(/page\/(\d+)/);
    const page = matches ? parseInt(matches[1]) : 1;
    loadEvents(year, page, false); // false to prevent infinite loop
});

// Initial page load (based on URL)
(function () {
    const path = window.location.pathname;
    if (path.includes('/events')) {
        const urlParams = new URLSearchParams(window.location.search);
        const year = urlParams.get('year') || '';
        const matches = path.match(/page\/(\d+)/);
        const page = matches ? parseInt(matches[1]) : 1;
        loadEvents(year, page, false);
    }
})();


    /*
    ** Gallery Page
    */
   
    function loadGallery(tab = 'image', page = 1, append = false) {
        $.ajax({
        url: corporate_page_ajax.ajaxurl,
        type: 'POST',
        data: {
            action: 'load_gallery_items',
            tab: tab,
            page: page
        },
        beforeSend: function() {
            $('#load-more-gallery-page').text('Loading...');
        },
        success: function(res) {
            if (res.success) {
            if (!append) {
                $('#gallery-container').html(res.data.html);
            } else {
                $('#gallery-container').append(res.data.html);
            }
            $('#load-more-gallery-page').data('page', res.data.current_page).data('tab', tab).text('Load More');

            if (res.data.current_page >= res.data.max_pages) {
                $('#load-more-gallery-page').hide();
            } else {
                $('#load-more-gallery-page').show();
            }
            }
        }
        });
    }

    // Tab switching
    $('.tabs_faq.gallery-page button[data-tab]').on('click', function() {
        var tab = $(this).data('tab');
        $('.tabs_faq .nav-link').removeClass('active');
        $(this).addClass('active');
        loadGallery(tab, 1, false);
    });

    // Load More
    $('#load-more-gallery-page').on('click', function() {
        var tab = $(this).data('tab');
        var page = $(this).data('page');
        loadGallery(tab, page, true);
    });
    
});
