<?php /* Template Name: FAQ Page */ 
get_header(); 
?>

<?php 
    global $boxshop_theme_options;
    boxshop_breadcrumbs_title(true, 'Frequently Asked Questions'); 
?>

<?php if (have_rows('faq_tabs')): ?>
<div class="container studds-faq">
    <div class="faq_questions_dls">
        <div class="faq_questions_title">
            <?php if ($main_title = get_field('main_title')): ?>
            <h2>
                <?php echo esc_html($main_title); ?>
            </h2>
            <?php endif; ?>
        </div>

        <div class="tabs_faq">
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <?php
                $index = 0;
                while (have_rows('faq_tabs')): the_row();
                    $tab_name = get_sub_field('tab_name');
                    $tab_id = 'tab-' . $index;
                ?>
                <li class="nav-item" role="presentation">
                    <button class="nav-link <?php echo $index === 0 ? 'active' : ''; ?>" id="<?php echo esc_attr($tab_id); ?>-tab" data-bs-toggle="tab" data-bs-target="#<?php echo esc_attr($tab_id); ?>" type="button" role="tab" aria-controls="<?php echo esc_attr($tab_id); ?>" aria-selected="<?php echo $index === 0 ? 'true' : 'false'; ?>">
                        <?php echo esc_html($tab_name); ?>
                    </button>
                </li>
                <?php $index++; endwhile; ?>
            </ul>

            <div class="tab-content" id="myTabContent">
                <?php
                $index = 0;
                while (have_rows('faq_tabs')): the_row();
                    $tab_id = 'tab-' . $index;
                    $faq_items = get_sub_field('faq_accordion');
                ?>
                <div class="tab-pane fade <?php echo $index === 0 ? 'show active' : ''; ?>" id="<?php echo esc_attr($tab_id); ?>" role="tabpanel" aria-labelledby="<?php echo esc_attr($tab_id); ?>-tab">
                    <div class="accordion" id="faqAccordion-<?php echo esc_attr($tab_id); ?>">
                        <?php
                        $faq_index = 0;
                        if ($faq_items):
                            foreach ($faq_items as $faq_item):
                                $question = $faq_item['question'];
                                $answer = $faq_item['answer'];
                                $collapse_id = 'collapse-' . $tab_id . '-' . $faq_index;
                                $heading_id = 'heading-' . $tab_id . '-' . $faq_index;
                                $is_first = $faq_index === 0;
                        ?>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="<?php echo esc_attr($heading_id); ?>">
                                <button class="accordion-button <?php echo !$is_first ? 'collapsed' : ''; ?>" type="button" data-bs-toggle="collapse" data-bs-target="#<?php echo esc_attr($collapse_id); ?>" aria-expanded="<?php echo $is_first ? 'true' : 'false'; ?>" aria-controls="<?php echo esc_attr($collapse_id); ?>">
                                    <?php echo esc_html($question); ?>
                                </button>
                            </h2>
                            <div id="<?php echo esc_attr($collapse_id); ?>" class="accordion-collapse collapse <?php echo $is_first ? 'show' : ''; ?>" aria-labelledby="<?php echo esc_attr($heading_id); ?>" data-bs-parent="#faqAccordion-<?php echo esc_attr($tab_id); ?>">
                                <div class="accordion-body">
                                    <?php echo esc_html($answer); ?>
                                </div>
                            </div>
                        </div>
                        <?php
                                $faq_index++;
                            endforeach;
                        endif;
                        ?>
                    </div>
                </div>
                <?php $index++; endwhile; ?>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php get_footer(); ?>
