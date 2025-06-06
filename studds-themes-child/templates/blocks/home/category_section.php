<?php
/*
** Category Section
*/

$heading = get_sub_field('heading'); 
$select_category_for_slider = get_sub_field('select_category_for_slider');
?>

<?php if (!empty($select_category_for_slider)): ?>
    <section class="categories_section_wrap">
        <div class="container">
            <div class="categories_title">
                <?php if(!empty($heading)): ?> 
                    <h2><?php echo esc_html($heading); ?></h2>
                    <?php endif; ?>
                </div>
            </div>

            <div class="category_navs">
                <div class="swiper-button-prev"></div>
                <!-- Swiper Slider -->
                <div class="swiper main_custom_slider" id="categories__swipe">
                    <div class="swiper-wrapper">
                        <?php foreach ($select_category_for_slider as $selected_category) : 

                        $category_id = $selected_category->term_id;
                        $category_name = $selected_category->name; 
                        $category_image = get_field('category_background_image', 'product_cat_' . $category_id);
                    ?>
                        <div class="swiper-slide">
                            <div class="categories__swipe_dls">
                                <?php if ($category_image): ?>
                                    <img src="<?php echo esc_url($category_image); ?>" alt="">
                                <?php endif; ?>
                                <div class="category_title_on_image">
                                    <a href="<?php echo esc_url(get_term_link($selected_category)); ?>">
                                        <?php if(!empty($category_name)){?>
                                        <h4><?php echo esc_html($category_name); ?></h4>
                                        <?php }?>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    </div>
                </div>
                <div class="swiper-button-next"></div>
            </div>
            
            <div class="container mobile_slider_wrap">
                <div class="slider-status">
                    <div class="count"><span class="current">01</span> / <span class="total">6</span></div>
                    <div class="progress-bar">
                        <div class="progress-fill"></div>
                    </div>
                </div>
                <div class="prev-and-next">
                    <div class="swiper-button-next"></div>
                    <div class="swiper-button-prev"></div>
                </div> 
            </div>
            
            
               
            
        </div>
    </section>
<?php endif; ?>
