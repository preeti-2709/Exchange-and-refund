<?php
  /*
   * Template name: Registration Form
   */
?>
<?php if(is_user_logged_in()){
  wp_redirect(get_permalink(get_option('woocommerce_myaccount_page_id')));
} ?>
<?php 
        global $boxshop_page_datas, $boxshop_theme_options;
        get_header();
        $extra_class = "";

        $page_column_class = boxshop_page_layout_columns_class($boxshop_page_datas['ts_page_layout']);
        
        $show_breadcrumb = ( !is_home() && !is_front_page() && isset($boxshop_page_datas['ts_show_breadcrumb']) && absint($boxshop_page_datas['ts_show_breadcrumb']) == 1 );
        $show_page_title = ( !is_home() && !is_front_page() && absint($boxshop_page_datas['ts_show_page_title']) == 1 );
        if( function_exists('is_bbpress') && is_bbpress() ){
        	$show_page_title = true;
        	$show_breadcrumb = true;
        }
        if( ($show_breadcrumb || $show_page_title) && isset($boxshop_theme_options['ts_breadcrumb_layout']) ){
        	$extra_class = 'show_breadcrumb_'.$boxshop_theme_options['ts_breadcrumb_layout'];
        }
        
        boxshop_breadcrumbs_title($show_breadcrumb, $show_page_title, get_the_title());

?>
<?php do_action( 'woocommerce_before_customer_login_form' ); ?>
<div class="page-container <?php echo esc_attr($extra_class) ?>">
    <div id="main-content" class="<?php echo esc_attr($page_column_class['main_class']); ?>">	
		<div id="primary" class="site-content">
            <div class="custom-registration">
                <div class="u-columns col2-set" id="customer_login">
                    <div class="u-column1 col-1">
        	            
                    	<form method="post" class="woocommerce-form woocommerce-form-register register" <?php do_action( 'woocommerce_register_form_tag' ); ?> >
                    		<?php do_action( 'woocommerce_register_form_start' ); ?>
                    		<?php if ( 'no' === get_option( 'woocommerce_registration_generate_username' ) ) : ?>
                    			<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                    				<label for="reg_username"><?php esc_html_e( 'Username', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
                    				<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="username" id="reg_username" autocomplete="username" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" /><?php // @codingStandardsIgnoreLine ?>
                    			</p>
                    		<?php endif; ?>
                    		<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                    			<label for="reg_email"><?php esc_html_e( 'Email address', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
                    			<input type="email" class="woocommerce-Input woocommerce-Input--text input-text" name="email" id="reg_email" autocomplete="email" value="<?php echo ( ! empty( $_POST['email'] ) ) ? esc_attr( wp_unslash( $_POST['email'] ) ) : ''; ?>" /><?php // @codingStandardsIgnoreLine ?>
                    		</p>
                    		<?php if ( 'no' === get_option( 'woocommerce_registration_generate_password' ) ) : ?>
                    			<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                    				<label for="reg_password"><?php esc_html_e( 'Password', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
                    				<input type="password" class="woocommerce-Input woocommerce-Input--text input-text" name="password" id="reg_password" autocomplete="new-password" />
                    			</p>
                    		<?php else : ?>
                    			<p><?php esc_html_e( 'A password will be sent to your email address.', 'woocommerce' ); ?></p>
                    		<?php endif; ?>
                    		<?php do_action( 'woocommerce_register_form' ); ?>
                    		<p class="woocommerce-form-row form-row">
                    			<?php wp_nonce_field( 'woocommerce-register', 'woocommerce-register-nonce' ); ?>
                    			<button type="submit" class="woocommerce-Button woocommerce-button button woocommerce-form-register__submit" name="register" value="<?php esc_attr_e( 'Register', 'woocommerce' ); ?>"><?php esc_html_e( 'Register', 'woocommerce' ); ?></button>
                    		</p>
                    		<?php do_action( 'woocommerce_register_form_end' ); ?>
                    	</form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php do_action( 'woocommerce_after_customer_login_form' ); ?>
<?php get_footer();?>