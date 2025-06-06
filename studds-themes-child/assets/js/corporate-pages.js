jQuery(document).ready(function ($) {
/*
** Media Page Ajax
*/ 
    function loadMediaContent(year = '', page = 1) {
        $.ajax({
            url: corporate_page_ajax.ajaxurl,
            method: 'POST',
            data: {
                action: 'load_media_items',
                // nonce: corporate_page_ajax.nonce,
                year: year,
                page: page,
            },
            beforeSend: function () {
                $('.media-grid').html('<div class="loading">Loading...</div>');
            },
            success: function (response) {
                console.log(response, "response");
                if (response.success) {
                    $('.media-grid').html(response.data.content);
                    $('.ts-pagination.media-page').html(response.data.pagination);
                    $('.media-tab').removeClass('active');
                    if (year) {
                        $('.media-tab[data-year="' + year + '"]').addClass('active');
                    } else {
                        $('.media-tab[data-year=""]').addClass('active');
                    }

                    // Update URL
                    const url = new URL(window.location.href);
                    url.searchParams.set('year', year);
                    url.pathname = '/media/page/' + page + '/';
                    window.history.pushState({}, '', url.toString());

                } else {

                    $('.media-grid').html('<div class="no-media-message">No media items found.</div>');
                    $('.ts-pagination.media-page').empty();
                }
            }
        });
    }
    const urlParams = new URLSearchParams(window.location.search);
    const year = urlParams.get('year') || '';
    const pathParts = window.location.pathname.split('/').filter(Boolean);
    let page = 1;
    if (pathParts.includes('page')) {
        const pageIndex = pathParts.indexOf('page') + 1;
        page = parseInt(pathParts[pageIndex]) || 1;
    }
    $('.media-tab').on('click', function (e) {
        e.preventDefault();
        const year = $(this).data('year') || '';
        loadMediaContent(year);
    });
    $(document).on('click', '.ts-pagination.media-page a', function (e) {
        e.preventDefault();
        const year = $('.media-tab.active').data('year') || '';
        const page = $(this).text();
        loadMediaContent(year, page);
    });
    loadMediaContent();


/*
** Event Page Ajax
*/ 

    function loadEvents(yearSlug = '', page = 1) {
        $.ajax({
            url: corporate_page_ajax.ajaxurl,
            type: 'POST',
            data: {
                action: 'load_events',
                year: yearSlug,
                paged: page,
                // nonce: events_ajax_obj.nonce
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
                        $('.event-tab[href="' + window.location.pathname + '"]').addClass('active');
                    } else {
                        $('.event-tab[data-year="' + yearSlug + '"]').addClass('active');
                    }
                } else {
                    $('.event-grid').html('<p>No events found.</p>');
                    $('.ts-pagination.event-page').html('');
                }
            }
        });
    }
    $('.event-tabs .event-tab').on('click', function () {
        // e.preventDefault();
        console.log("Clicked");
        const year = $(this).data('year') || '';
        loadEvents(year);
    });
    $(document).on('click', '.ts-pagination.event-page a', function (e) {
        e.preventDefault();
        const page = $(this).attr('href').split('paged=')[1] || 1;
        const activeTab = $('.event-tab.active').data('year') || '';
        loadEvents(activeTab, page);
    });
    loadEvents(); 

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
    $('.tabs_faq.gallery-page button[data-tab]').on('click', function() {
        var tab = $(this).data('tab');
        $('.tabs_faq .nav-link').removeClass('active');
        $(this).addClass('active');
        loadGallery(tab, 1, false);
    });
    $('#load-more-gallery-page').on('click', function() {
        var tab = $(this).data('tab');
        var page = $(this).data('page');
        loadGallery(tab, page, true);
    });
});
