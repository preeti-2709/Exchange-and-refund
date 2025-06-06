<?php 
	get_header(); 
?>

<?php 
    global $boxshop_theme_options;
    boxshop_breadcrumbs_title(true, 'Investor Relations'); 
    $selected_type = get_field('select_investor_relationship_data_type');
    $page_title = get_the_title(); 
?>
<div class="investor-relations-wrapper" style="display: flex; gap: 30px; padding: 40px; color: #fff; font-family: sans-serif;">
    
    <!-- Sidebar -->
    <aside class="investor-relations-sidebar" style="width: 250px;">
        <ul style="list-style: none; padding: 0; margin: 0;">
            <?php
            $args = array(
                'post_type' => 'investor-relation',
                'posts_per_page' => -1,
                'orderby' => 'date',
                'order' => 'ASC',
            );
            $sidebar_query = new WP_Query($args);
            if ($sidebar_query->have_posts()) :
                while ($sidebar_query->have_posts()) : $sidebar_query->the_post();
                    $active = (get_the_ID() === get_queried_object_id()) ? 'style="color: #ff3366;"' : '';
                    echo '<li style="margin-bottom: 15px;"><a href="' . get_permalink() . '" ' . $active . '>' . get_the_title() . '</a></li>';
                endwhile;
                wp_reset_postdata();
            endif;
            ?>
        </ul>
    </aside>

    <!-- Main Content -->
    <main class="investor-relations-content" style="flex-grow: 1;">

        <?php if (!empty($page_title)) : ?>
            <h1 style="border-bottom: 2px solid #00bcd4; padding-bottom: 10px;">
                <?php echo esc_html($page_title); ?>
            </h1>
        <?php endif; ?>

        <?php if ($selected_type === 'PDFs'): ?>
            <div class="pdf-content">
                <div class="content-body" style="margin-top: 20px;">
                    <?php if (have_rows('pdf_name_and_link')) : ?>
                        <ul style="padding-left: 0; list-style: none;">
                            <?php while (have_rows('pdf_name_and_link')) : the_row(); 
                                $name = get_sub_field('name');
                                $pdf = get_sub_field('pdf'); // This returns the URL of the uploaded PDF
                            ?>
                                <li style="margin-bottom: 10px;">
                                    <a href="<?php echo esc_url($pdf); ?>" target="_blank" style="color: #00bcd4; text-decoration: none;">
                                        <?php echo esc_html($name); ?>
                                    </a>
                                </li>
                            <?php endwhile; ?>
                        </ul>
                    <?php else : ?>
                        <p>No reports available.</p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($selected_type === 'Tabs'): ?>
            <div class="tabs-and-accordion-content">
                <?php if (have_rows('tabs')): ?>
                    <div class="container my-4">

                        <!-- Nav Tabs -->
                        <ul class="nav nav-tabs" id="tabNav" role="tablist">
                            <?php $tabIndex = 0; while (have_rows('tabs')): the_row(); ?>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link <?php echo $tabIndex === 0 ? 'active' : ''; ?>"
                                            id="tab-<?php echo $tabIndex; ?>-tab"
                                            data-bs-toggle="tab"
                                            data-bs-target="#tab-<?php echo $tabIndex; ?>"
                                            type="button"
                                            role="tab"
                                            aria-controls="tab-<?php echo $tabIndex; ?>"
                                            aria-selected="<?php echo $tabIndex === 0 ? 'true' : 'false'; ?>">
                                        <?php echo esc_html(get_sub_field('tab_title')); ?>
                                    </button>
                                </li>
                            <?php $tabIndex++; endwhile; ?>
                        </ul>

                        <!-- Tab Content -->
                        <div class="tab-content mt-3" id="tabContent">
                            <?php $tabIndex = 0; while (have_rows('tabs')): the_row(); ?>
                                <div class="tab-pane fade <?php echo $tabIndex === 0 ? 'show active' : ''; ?>"
                                    id="tab-<?php echo $tabIndex; ?>"
                                    role="tabpanel"
                                    aria-labelledby="tab-<?php echo $tabIndex; ?>-tab">
                                    <!-- Title description data -->
                                    <?php if( have_rows('title_and_description') ): ?>
                                        <div class="container my-4">
                                            <div class="row">
                                                <?php while( have_rows('title_and_description') ): the_row(); 
                                                    $title = get_sub_field('title');
                                                    $description = get_sub_field('description');

                                                    // Show block only if at least one field is non-empty
                                                    if( !empty($title) || !empty($description) ):
                                                ?>
                                                    <div class="col-md-6 mb-4">
                                                        <div class="card shadow-sm h-100">
                                                            <div class="card-body">
                                                                <?php if( !empty($title) ): ?>
                                                                    <h5 class="card-title text-uppercase text-primary mb-3">
                                                                        <?php echo esc_html($title); ?>
                                                                    </h5>
                                                                <?php endif; ?>

                                                                <?php if( !empty($description) ): ?>
                                                                    <div class="card-text">
                                                                        <?php echo wp_kses_post($description); ?>
                                                                    </div>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endif; endwhile; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Title description data end-->
                                    <!-- accordion data -->
                                    <?php if (have_rows('accordions')): ?>
                                        <div class="accordion" id="accordionTab<?php echo $tabIndex; ?>">
                                            <?php $accIndex = 0; while (have_rows('accordions')): the_row(); ?>
                                                <?php
                                                    $accordionID = "tab{$tabIndex}_accordion{$accIndex}";
                                                    $headingID = $accordionID . "_heading";
                                                    $collapseID = $accordionID . "_collapse";
                                                ?>
                                                <div class="accordion-item">
                                                    <h2 class="accordion-header" id="<?php echo $headingID; ?>">
                                                        <button class="accordion-button <?php echo $accIndex !== 0 ? 'collapsed' : ''; ?>"
                                                                type="button"
                                                                data-bs-toggle="collapse"
                                                                data-bs-target="#<?php echo $collapseID; ?>"
                                                                aria-expanded="<?php echo $accIndex === 0 ? 'true' : 'false'; ?>"
                                                                aria-controls="<?php echo $collapseID; ?>">
                                                            <?php echo esc_html(get_sub_field('accordion_title')); ?>
                                                        </button>
                                                    </h2>
                                                    <div id="<?php echo $collapseID; ?>"
                                                        class="accordion-collapse collapse <?php echo $accIndex === 0 ? 'show' : ''; ?>"
                                                        aria-labelledby="<?php echo $headingID; ?>"
                                                        data-bs-parent="#accordionTab<?php echo $tabIndex; ?>">

                                                        <div class="accordion-body">
                                                            <?php if (have_rows('pdf_name_and_link')): ?>
                                                                <ul class="list-unstyled">
                                                                    <?php while (have_rows('pdf_name_and_link')): the_row(); 
                                                                        $pdf = get_sub_field('pdf');
                                                                        $name = get_sub_field('name');
                                                                    ?>
                                                                        <li class="mb-2">
                                                                            <a href="<?php echo esc_url($pdf); ?>" target="_blank" rel="noopener">
                                                                                <i class="bi bi-file-earmark-pdf-fill text-danger me-1"></i>
                                                                                <?php echo esc_html($name); ?>
                                                                            </a>
                                                                        </li>
                                                                    <?php endwhile; ?>
                                                                </ul>
                                                            <?php else: ?>
                                                                <p>No PDFs available.</p>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php $accIndex++; endwhile; ?>
                                        </div>
                                    <?php endif; ?>
                                    <!-- accordion data end-->

                                </div>
                            <?php $tabIndex++; endwhile; ?>
                        </div>

                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if ($selected_type === 'Tabs Only'): ?>
            <div class="tabs-only">
                <?php if (have_rows('tabs_only')): ?>
                    <div class="container my-4">

                        <!-- Nav Tabs -->
                        <ul class="nav nav-tabs" id="tabOnlyNav" role="tablist">
                            <?php $index = 0; while (have_rows('tabs_only')): the_row(); ?>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link <?php echo $index === 0 ? 'active' : ''; ?>"
                                            id="tabOnly-<?php echo $index; ?>-tab"
                                            data-bs-toggle="tab"
                                            data-bs-target="#tabOnly-<?php echo $index; ?>"
                                            type="button"
                                            role="tab"
                                            aria-controls="tabOnly-<?php echo $index; ?>"
                                            aria-selected="<?php echo $index === 0 ? 'true' : 'false'; ?>">
                                        <?php echo esc_html(get_sub_field('tab_title')); ?>
                                    </button>
                                </li>
                            <?php $index++; endwhile; ?>
                        </ul>

                        <!-- Tab Content -->
                        <div class="tab-content mt-3" id="tabOnlyContent">
                            <?php $index = 0; while (have_rows('tabs_only')): the_row(); ?>
                                <div class="tab-pane fade <?php echo $index === 0 ? 'show active' : ''; ?>"
                                    id="tabOnly-<?php echo $index; ?>"
                                    role="tabpanel"
                                    aria-labelledby="tabOnly-<?php echo $index; ?>-tab">

                                    <?php if (have_rows('pdf_name_and_link')): ?>
                                        <ul class="list-unstyled">
                                            <?php while (have_rows('pdf_name_and_link')): the_row(); 
                                                $pdf = get_sub_field('pdf');
                                                $name = get_sub_field('name');
                                            ?>
                                                <li class="mb-2">
                                                    <a href="<?php echo esc_url($pdf); ?>" target="_blank" rel="noopener">
                                                        <i class="bi bi-file-earmark-pdf-fill text-danger me-1"></i>
                                                        <?php echo esc_html($name); ?>
                                                    </a>
                                                </li>
                                            <?php endwhile; ?>
                                        </ul>
                                    <?php else: ?>
                                        <p>No PDFs available in this tab.</p>
                                    <?php endif; ?>

                                </div>
                            <?php $index++; endwhile; ?>
                        </div>

                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if ($selected_type === 'Videos'): ?>
            <div class="investor-relation-video-content">
                <?php if (have_rows('video_section')): ?>
                    <div class="container my-5">
                        <div class="row">
                            <?php while (have_rows('video_section')): the_row(); 
                                $video_title = get_sub_field('video_title');
                                $video_file = get_sub_field('video_file');
                                if ($video_file):
                            ?>
                                <div class="col-md-6 mb-4">
                                    <?php if (!empty($video_title)): ?>
                                        <h5 class="text-black text-center mb-2 text-uppercase fw-bold"><?php echo esc_html($video_title); ?></h5>
                                    <?php endif; ?>
                                    <video class="w-100 rounded" controls preload="metadata">
                                        <source src="<?php echo esc_url($video_file); ?>" type="video/mp4">
                                        Your browser does not support the video tag.
                                    </video>
                                </div>
                            <?php endif; endwhile; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if ($selected_type === 'Disclaimer'): ?>
            <?php
                $content = get_the_content();
                $disclaimer_checkbox_text = get_field('disclaimer_checkbox_text');
                
                $modal_no_checkbox_text = get_field('checkbox_not_selected_text');
                $modal_confirm_text = get_field('confirm_text_modal_text');
                $modal_cancel_text = get_field('cancel_text_modal_text');
            ?>
            
            <?php if (!empty($content)): ?>
                <div id="disclaimer-container" class="container text-black p-4 rounded mb-4">
                    <div class="mb-3">
                        <?php echo apply_filters('the_content', $content); ?>
                    </div>

                    <?php if (!empty($disclaimer_checkbox_text)): ?>
                        <div class="form-check mb-3 text-black">
                            <input class="form-check-input" type="checkbox" value="" id="disclaimer-check" style="border: 1px solid red; width: 14px;">
                            <label class="form-check-label" for="disclaimer-check">
                                <?php echo esc_html($disclaimer_checkbox_text); ?>
                            </label>
                        </div>
                    <?php endif; ?>

                    <div class="d-flex gap-2">
                        <button id="investor-relation-confirm-btn" class="btn btn-danger">I Confirm</button>
                        <button id="investor-relation-cancel-btn" class="btn btn-secondary">I Do Not Confirm</button>
                    </div>
                </div>

                <!-- Modal for no checkbox selected -->
                    <div class="modal fade" id="noCheckboxModal" tabindex="-1" aria-labelledby="noCheckboxModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="noCheckboxModalLabel">Attention</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <?php echo !empty($modal_no_checkbox_text) ? $modal_no_checkbox_text : 'Please Select the Legal Disclaimer Checkbox.'; ?>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
                                </div>
                            </div>
                        </div>
                    </div>

                <!-- Modal for confirm after checkbox checked -->
                <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="confirmModalLabel">Confirmed</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <?php echo !empty($modal_confirm_text) ? apply_filters('the_content', $modal_confirm_text) : 'Thank you for confirming.'; ?>
                            </div>
                            <div class="modal-footer">
                                <a href="<?php the_field('download_link'); ?>" class="btn btn-success" download>Download</a>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal for cancel button -->
                <div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="cancelModalLabel">Access Restricted</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <?php echo !empty($modal_cancel_text) ? apply_filters('the_content', $modal_cancel_text) : 'Access Restricted.'; ?>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>


    </main>

</div>
<?php get_footer(); ?>