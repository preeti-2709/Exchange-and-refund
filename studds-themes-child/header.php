<?php //echo do_shortcode("[shiprocket_login]"); 
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <?php global $boxshop_theme_options, $boxshop_page_datas; ?>
    <meta charset="<?php bloginfo('charset'); ?>">

    <?php if (isset($boxshop_theme_options['ts_responsive']) && $boxshop_theme_options['ts_responsive'] == 1): ?>
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">
    <?php endif; ?>

    <link rel="profile" href="//gmpg.org/xfn/11">
    <?php
    boxshop_theme_favicon();
    wp_head();
    ?>
    <script>
        (function(n, t, i, r) {
            var u, f;
            n[i] = n[i] || {}, n[i].initial = {
                accountCode: "STUDD11111",
                host: "STUDD11111.pcapredict.com"
            }, n[i].on = n[i].on || function() {
                (n[i].onq = n[i].onq || []).push(arguments)
            }, u = t.createElement("script"), u.async = !0, u.src = r, f = t.getElementsByTagName("script")[0], f.parentNode.insertBefore(u, f)
        })(window, document, "pca", "//STUDD11111.pcapredict.com/js/sensor.js")
    </script>
</head>

<body <?php body_class(); ?>>
    <?php
    if (function_exists('wp_body_open')) {
        wp_body_open();
    }
    ?>
    <!-- START Bootstrap-Cookie-Alert -->
    <!--<div class="container1">-->
    <!--<div class="alert text-center cookiealert" role="alert">-->
    <!-- <b>Do you like cookies?</b> &#x1F36A; We use cookies to ensure you get the best experience on our website. <a href="https://cookiesandyou.com/" target="_blank">Learn more</a> -->
    <!--     <div class="col1"><b>By using this website, you accept the <a href="https://studdsonlinestore.com/terms-of-use/">Terms of Use</a> & <a href="https://studdsonlinestore.com/privacy-policy/">Privacy Policy</a>. In addition, we use cookies to provide you with an improved user experience and to personalize content. You may Manage Cookies by clicking on Settings</a></b></div>-->
    <!--     <div class="col"><button type="button" class="btn btn-primary btn-sm acceptcookies" style="background:#ff0000;">-->
    <!--        I agree-->
    <!--    </button></div>-->
    <!--</div>-->
    <!--</div>-->
    <div id="page" class="hfeed site">

        <?php if (!is_page_template('page-templates/blank-page-template.php')): ?>

            <!-- Page Slider -->
            <?php if (is_page() && isset($boxshop_page_datas)): ?>
                <?php if ($boxshop_page_datas['ts_page_slider'] && $boxshop_page_datas['ts_page_slider_position'] == 'before_header'): ?>
                    <div class="top-slideshow">
                        <div class="top-slideshow-wrapper">
                            <?php boxshop_show_page_slider(); ?>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
            <div class="mobile-menu-wrapper">
                <span class="ic-mobile-menu-close-button"><i class="fa fa-remove"></i></span>
                <?php
                if (has_nav_menu('mobile')) {
                    wp_nav_menu(array('container' => 'nav', 'container_class' => 'mobile-menu', 'theme_location' => 'mobile'));
                } else {
                    wp_nav_menu(array('container' => 'nav', 'container_class' => 'mobile-menu', 'theme_location' => 'primary'));
                }
                ?>
            </div>

            <?php //boxshop_get_header_template(); 
            ?>
            <?php
            global $boxshop_theme_options, $boxshop_page_datas;

            $header_classes = array();
            if (isset($boxshop_page_datas['ts_top_header_transparent']) && $boxshop_page_datas['ts_top_header_transparent']) {
                $header_classes[] = 'top-header-transparent';
                if (isset($boxshop_page_datas['ts_top_header_text_color'])) {
                    $header_classes[] = 'top-header-text-' . $boxshop_page_datas['ts_top_header_text_color'];
                }
            }

            if (isset($boxshop_theme_options['ts_enable_sticky_header']) && $boxshop_theme_options['ts_enable_sticky_header']) {
                $header_classes[] = 'has-sticky';
            }

            $extra_class = array();
            if ($boxshop_theme_options['ts_enable_tiny_shopping_cart'] == 0) {
                $extra_class[] = 'hidden-cart';
            } else {
                $extra_class[] = 'show-cart';
            }

            if ($boxshop_theme_options['ts_enable_search'] == 0) {
                $extra_class[] = 'hidden-search';
            } else {
                $extra_class[] = 'show-search';
            }

            if (class_exists('YITH_WCWL') && $boxshop_theme_options['ts_enable_tiny_wishlist']) {
                $extra_class[] = 'show-wishlist';
            } else {
                $extra_class[] = 'hidden-wishlist';
            }

            if ($boxshop_theme_options['ts_enable_tiny_account'] == 0) {
                $extra_class[] = 'hidden-myaccount';
            } else {
                $extra_class[] = 'show-myaccount';
            }
            ?>
            <header class="ts-header <?php echo esc_attr(implode(' ', $header_classes)); ?>">
                <div class="header-container">
                    <div class="header-template header-v3 header-sticky" <?php echo esc_attr(implode(' ', $extra_class)); ?>">





                        <!--<div class="banner-container"style="padding-left:0px!important; padding-right:0px!important;">-->
                        <!--<div class="headerstrip-wrapper headerstrip js-banner__link">-->
                        <!--    <div class="headerstrip-content-background"></div>-->
                        <!--    <div class="headerstrip-canvas is-hidden-desktop">-->
                        <!--      <div class="headerstrip-content ">-->
                        <!--        <div class="headerstrip-content headerstrip-text">-->
                        <!--           <strong>Please anticipate a delay in delivery of 4-5 days due to the high volume of incoming orders. </i>  -->
                        <!--                </div></div> </div>-->
                        <!--    <div class="headerstrip-canvas is-hidden-tablet-and-below">-->
                        <!--      <div class="headerstrip-content">-->
                        <!--               <div class="headerstrip-text" style="font-weight: 600; font-family:' Roboto' !important;">-->
                        <!--             <b><i>Please anticipate a delay in delivery of 4-5 days due to the high volume of incoming orders. </i></b> -->
                        <!--                                  </div>  </div>-->
                        <!--    </div>-->
                        <!--</div></div>-->









                        <div class="header-top">
                            <div class="container">

                                <div class="header-left">
                                    <?php if ($boxshop_theme_options['ts_header_contact_information']): ?>
                                        <div class="info-desc">
                                            <?php echo do_shortcode(stripslashes($boxshop_theme_options['ts_header_contact_information'])); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="header-right">
                                    <!-- <span class="ic-mobile-menu-button visible-phone"><i class="fa fa-bars"></i></span> -->
                                    <!-- <span class="ts-group-meta-icon-toggle visible-phone"><i class="fa fa-user-circle-o"
                                            style="font-weight: 100!important;color: white;"></i></span> -->



                                    <div class="header_top_wrap tablet_wrap">

                                        <?php do_action('boxshop_before_group_meta_header'); ?>


                                        <?php //if( $boxshop_theme_options['ts_header_currency'] ): 
                                        ?>
                                        <div class="header-currency">
                                            <?php //boxshop_woocommerce_multilingual_currency_switcher(); 
                                            ?>
                                            <a title="track" href="https://studds-revamp.postyoulike.com/wishlist"
                                                class="tini-track">
                                                <img src="<?php echo get_stylesheet_directory_uri() . '/assets/img/Heart.svg'; ?>"
                                                    alt=""> Wishlist
                                            </a>
                                        </div>
                                        <?php //endif; 
                                        ?>

                                        <?php //if( $boxshop_theme_options['ts_header_currency'] ): 
                                        ?>
                                        <div class="header-currency">
                                            <?php //boxshop_woocommerce_multilingual_currency_switcher(); 
                                            ?>
                                            <a title="track" href="https://studdstracking.shiprocket.co/" class="tini-track"
                                                target="_blank">
                                                <img src="<?php echo get_stylesheet_directory_uri() . '/assets/img/Truck.svg'; ?>"
                                                    alt=""> Track Order
                                            </a>
                                        </div>
                                        <?php //endif; 
                                        ?>

                                        <?php if ($boxshop_theme_options['ts_enable_tiny_account']): ?>
                                            <div class="my-account-wrapper">
                                                <?php echo boxshop_tiny_account(); ?>
                                            </div>
                                        <?php endif; ?>

                                        <?php if ($boxshop_theme_options['ts_enable_tiny_shopping_cart']): ?>
                                            <div class="shopping-cart-wrapper">
                                                <?php echo boxshop_tiny_cart(); ?>
                                            </div>
                                        <?php endif; ?>


                                        <?php do_action('boxshop_after_group_meta_header'); ?>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Mobile Top Header -->
                        <?php if (wp_is_mobile()) { ?>
                            <div class="mobile_view mobile_header_top">
                                <p>
                                    Hey
                                    <?php
                                    if (is_user_logged_in()) {
                                        $current_user = wp_get_current_user();
                                        echo '<span>' . esc_html($current_user->display_name) . '</span> !';
                                    }
                                    ?>
                                </p>
                            </div>
                        <?php } ?>


                        <div class="header-middle">
                            <div class="container">
                                <div class="logo-wrapper">
                                    <?php echo boxshop_theme_logo(); ?>
                                </div>
                                <div class="menu-wrapper hidden-phone">
                                    <div class="ts-menu">
                                        <?php
                                        if (has_nav_menu('primary')) {
                                            wp_nav_menu(array('container' => 'nav', 'container_class' => 'main-menu pc-menu ts-mega-menu-wrapper', 'theme_location' => 'primary', 'walker' => new My_Custom_Walker_Nav_Menu()));
                                        } else {
                                            wp_nav_menu(array('container' => 'nav', 'container_class' => 'main-menu pc-menu ts-mega-menu-wrapper'));
                                        }
                                        ?>
                                    </div>
                                </div>

                                <span class="ic-mobile-menu-button visible-phone"><i class="fa fa-bars"></i></span>



                                <div class="mobile_right_user">
                                    <?php if ($boxshop_theme_options['ts_enable_search']): ?>
                                        <div class="search-wrapper">
                                            <span class="toggle-search"></span>
                                            <?php //if (!wp_is_mobile()) { 
                                            ?>
                                            <div class="ts-search-by-category desktop_view_form">
                                                <?php get_search_form(); ?>
                                            </div>
                                            <?php //} 
                                            ?>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ($boxshop_theme_options['ts_enable_tiny_shopping_cart']): ?>
                                        <div class="shopping-cart-wrapper">
                                            <?php echo boxshop_tiny_cart(); ?>
                                        </div>
                                    <?php endif; ?>
                                    <!-- 768px below show -->
                                    <span class="ts-group-meta-icon-toggle visible-phone"><i class="fa fa-user-circle-o"
                                            style="font-weight: 100!important;color: white;"></i>
                                    </span>
                                </div>

                                <!-- when search icon click and check box her -->

                            </div>
                            <?php if (wp_is_mobile()) { ?>
                                <div class="group-meta-header header_top_wrap  mobile_wrap">

                                    <?php do_action('boxshop_before_group_meta_header'); ?>

                                    <?php if ($boxshop_theme_options['ts_enable_tiny_account']): ?>
                                        <div class="my-account-wrapper">
                                            <?php echo boxshop_tiny_account(); ?>
                                        </div>
                                    <?php endif; ?>



                                    <?php //if( $boxshop_theme_options['ts_header_currency'] ): 
                                    ?>
                                    <div class="header-currency">
                                        <?php //boxshop_woocommerce_multilingual_currency_switcher(); 
                                        ?>
                                        <a title="track" href="https://studds-revamp.postyoulike.com/wishlist" class="tini-track">
                                            <img src="<?php echo get_stylesheet_directory_uri() . '/assets/img/Heart.svg'; ?>"
                                                alt=""> Wishlist
                                        </a>
                                    </div>
                                    <?php //endif; 
                                    ?>

                                    <?php //if( $boxshop_theme_options['ts_header_currency'] ): 
                                    ?>
                                    <div class="header-currency">
                                        <?php //boxshop_woocommerce_multilingual_currency_switcher(); 
                                        ?>
                                        <a title="track" href="https://studdstracking.shiprocket.co/" class="tini-track"
                                            target="_blank">
                                            <img src="<?php echo get_stylesheet_directory_uri() . '/assets/img/Truck.svg'; ?>"
                                                alt=""> Track Order
                                        </a>
                                    </div>
                                    <?php //endif; 
                                    ?>


                                    <?php do_action('boxshop_after_group_meta_header'); ?>

                                </div>
                            <?php } ?>
                        </div>
                        <?php //if (wp_is_mobile()) { 
                        ?>
                        <!-- <div class="ts-search-by-category mobile_view_form"> -->
                        <?php //get_search_form(); 
                        ?>
                        <!-- </div> -->
                        <?php //} 
                        ?>
                    </div>

                </div>
            </header>

        <?php endif; ?>

        <?php do_action('boxshop_before_main_content'); ?>

        <div id="main" class="wrapper">