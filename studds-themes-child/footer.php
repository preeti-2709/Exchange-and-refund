<!--Zoho Campaigns Web-Optin Form's Header Code Starts Here-->
<script type="text/javascript" src="https://studds-zgpvh.maillist-manage.net/js/optin.min.js" onload="setupSF('sf3z5d098131c766cfc135db70e33e24bae4676bb7ca7d666967c336d6d918491aae','ZCFORMVIEW',false,'light',false,'0')"></script>
<script type="text/javascript">
	function runOnFormSubmit_sf3z5d098131c766cfc135db70e33e24bae4676bb7ca7d666967c336d6d918491aae(th){
		/*Before submit, if you want to trigger your event, "include your code here"*/
	};
</script>

<?php if( !is_page_template('page-templates/blank-page-template.php') ): ?>
	<?php
		 $main_text = get_field('main_text', 'option');
		 $studds_address = get_field('studds_address', 'option');
		 $phone_number = get_field('phone_number', 'option');
		 $email_id = get_field('email_id', 'option');
		 $footer_image = get_field('footer_image', 'option');

	?>


<!-- Footer above html section -->


<footer class="bg-black text-white footer-custom">
  <div class="container">
	<!-- Here Newletter section-->
	<div class="row newsletter-form">
		<!--Zoho Campaigns Web-Optin Form's Header Code Ends Here--><!--Zoho Campaigns Web-Optin Form Starts Here-->
				<div class="container">
					<div class="make-it-align-center">
						<div class="new_letter_join_wrap latest_studds_btm">		
							<h2>SUBSCRIBE TO OUR NEWSLETTER</h2>
							<div id="sf3z5d098131c766cfc135db70e33e24bae4676bb7ca7d666967c336d6d918491aae" data-type="signupform" style="opacity: 1;">
								<div id="customForm">
									<div name="SIGNUP_BODY" changeitem="BG_IMAGE" class="signup_body">
						
											<form method="POST" id="zcampaignOptinForm" class="join_form_newsletter"   action="https://studds-zgpvh.maillist-manage.net/weboptin.zc" target="_zcSignup">							
												<div class="input_left_form" >
													<div id="Zc_SignupSuccess" style="z-index: 222;position: absolute; width: 100%; background-color: white; padding: 3px; border: 3px solid rgb(194, 225, 154); margin-bottom: 10px; word-break: break-all; opacity: 1; display: none">
														<div style="width: 10%; padding: 5px; display: table-cell">
															<img class="successicon" src="https://campaigns.zoho.com/images/challangeiconenable.jpg" alt="Success Icon" style="width: 20px">
														</div>
														<div style="display: table-cell">
															<span id="signupSuccessMsg" style="color: rgb(73, 140, 132); font-family: sans-serif;font-size: 17px;margin-top: 24px;margin-bottom: 24px;line-height: 30px; display: block"></span>
														</div>
													</div>
													<input placeholder="Email" changeitem="SIGNUP_FORM_FIELD" name="CONTACT_EMAIL" id="EMBED_FORM_EMAIL_LABELL" type="text" class="news_letter_input" >
												</div>
												<div class="subscribe_btns">
													<input type="button" name="SIGNUP_SUBMIT_BUTTON" id="zcWebOptinn" value="SUBSCRIBE">
												</div>
												<input type="hidden" id="fieldBorder" value="">
												<input type="hidden" id="submitType" name="submitType" value="optinCustomView">
												<input type="hidden" id="emailReportId" name="emailReportId" value="">
												<input type="hidden" id="formType" name="formType" value="QuickForm">
												<input type="hidden" name="zx" id="cmpZuid" value="12feb9515">
												<input type="hidden" name="zcvers" value="3.0">
												<input type="hidden" name="oldListIds" id="allCheckedListIds" value="">
												<input type="hidden" id="mode" name="mode" value="OptinCreateView">
												<input type="hidden" id="zcld" name="zcld" value="1d879e64c5c26b89">
												<input type="hidden" id="zctd" name="zctd" value="1d879e64c5b747f9">
												<input type="hidden" id="document_domain" value="">
												<input type="hidden" id="zc_Url" value="studds-zgpvh.maillist-manage.net">
												<input type="hidden" id="new_optin_response_in" value="1">
												<input type="hidden" id="duplicate_optin_response_in" value="1">
												<input type="hidden" name="zc_trackCode" id="zc_trackCode" value="ZCFORMVIEW">
												<input type="hidden" id="zc_formIx" name="zc_formIx" value="3z5d098131c766cfc135db70e33e24bae4676bb7ca7d666967c336d6d918491aae">
												<input type="hidden" id="viewFrom" value="URL_ACTION">
												<span style="display: none" id="dt_CONTACT_EMAIL">1,true,6,Contact Email,2</span>
												<span style="display: none" id="dt_FIRSTNAME">1,false,1,First Name,2</span>
												<span style="display: none" id="dt_LASTNAME">1,false,1,Last Name,2</span>
											</form>
									</div>
								</div>
								<img src="https://studds-zgpvh.maillist-manage.net/images/spacer.gif" alt="Spacer" id="refImage" onload="referenceSetter(this)" style="display:none;">
							</div>
							<div class="text-below-form">By subscribing, you agree to receive updates, offers, and promotional emails from STUDDS</div>
						</div>
					</div>
				</div>
			
			<input type="hidden" id="signupFormType" value="QuickForm_Vertical">
			<div id="zcOptinOverLay" oncontextmenu="return false" style="display:none;text-align: center; background-color: rgb(0, 0, 0); opacity: 0.5; z-index: 100; position: fixed; width: 100%; top: 0px; left: 0px; height: 988px;"></div>
			<div id="zcOptinSuccessPopup" style="display:none;z-index: 9999;width: 800px; height: 40%;top: 84px;position: fixed; left: 26%;background-color: #FFFFFF;border-color: #E6E6E6; border-style: solid; border-width: 1px;  box-shadow: 0 1px 10px #424242;padding: 35px;">
				<span style="position: absolute;top: -16px;right:-14px;z-index:99999;cursor: pointer;" id="closeSuccess">
					<img src="https://studds-zgpvh.maillist-manage.net/images/videoclose.png" alt="Video close">
				</span>
				<div id="zcOptinSuccessPanel"></div>
			</div>
			<!--Zoho Campaigns Web-Optin Form Ends Here-->
	</div>	

	<!-- here tablate footer section -->
	
	<div class="tablate_show tablate_footer">
		<div class="row bottom_footer">
			<div class="col-6">
				<div class="footer_logo">
					<img src="<?php echo $footer_image; ?>" alt="Studds Logo" >
				</div>
			</div>      
			<div class="col-6">
				<div class="social_icons">		
					<?php
						if( have_rows('social_media_links', 'option') ):
							while( have_rows('social_media_links', 'option') ) : the_row();
							$icon = get_sub_field('icon');
							$link = get_sub_field('link');
						?>
							<a href="<?php echo $link; ?>" class="text-white"><img src="<?php echo $icon; ?>"></a>
							<?php
							endwhile;
						endif;
					?>
				</div>
			</div>
		</div>
	</div>

   <!-- Here Newletter section-->
    <div class="row footer_row_wrap">
		<div class="col-lg-3 col-md-4 foo_column mobile_footer_wrap">
		 	<h5 class="foo_column_title">
				<?php $first_section_head = get_field('main_text', 'option');?>
				<?php if (!empty($first_section_head)) : ?>
					<?php echo $first_section_head; ?>
				<?php endif; ?>
		  	</h5>
			<?php if (!empty($studds_address) || !empty($phone_number) || !empty($email_id)) : ?>
				<p><img src="<?php echo get_stylesheet_directory_uri().'/assets/img/address_studds.svg'; ?>"><?php echo $studds_address; ?></p>
				<p><img src="<?php echo get_stylesheet_directory_uri().'/assets/img/Phone.svg'; ?>"><?php echo $phone_number; ?></p>
				<p><img src="<?php echo get_stylesheet_directory_uri().'/assets/img/email_studds.svg'; ?>"><?php echo $email_id; ?></p>
			<?php endif; ?>
		</div>

      <!-- Helmets -->
	  <?php
		if( have_rows('other_links', 'option') ):
			while( have_rows('other_links', 'option') ) : the_row();
			$main_text = get_sub_field('main_text');
			// $information = get_sub_field('information');
		?>
      <div class="col-lg-2 col-md-4 foo_column">
        <h5 class="foo_column_title"><?php echo $main_text; ?></h5>
		<div class="foo_column_content">
        <ul class="list-unstyled">
		<?php
		if( have_rows('related_links', 'option') ):
			while( have_rows('related_links', 'option') ) : the_row();
				$link = get_sub_field('link');
			?>
			<li><a href="<?php echo $link['url']; ?>" class="text-white text-decoration-none"><?php echo $link['title'];?></a></li>
			<?php
			endwhile;
		endif;
		?>
        </ul>
		</div>
      </div>
	<?php
		endwhile;
	endif;
	?>


    </div>

	<!-- Here mobile address section -->
	<div class=" mobile_view mobile_address_wrap" >
		<div class="row footer_row_wrap">

			<div class="col-lg-3 col-md-4 foo_column">
				<h5 class="foo_column_title">
					<?php $first_section_head = get_field('main_text', 'option');?>
					<?php if (!empty($first_section_head)) : ?>
						<?php echo $first_section_head; ?>
					<?php endif; ?>
				</h5>
				<?php if (!empty($studds_address) || !empty($phone_number) || !empty($email_id)) : ?>
					<p><img src="<?php echo get_stylesheet_directory_uri().'/assets/img/address_studds.svg'; ?>"><?php echo $studds_address; ?></p>
					<p><img src="<?php echo get_stylesheet_directory_uri().'/assets/img/Phone.svg'; ?>"><?php echo $phone_number; ?></p>
					<p><img src="<?php echo get_stylesheet_directory_uri().'/assets/img/email_studds.svg'; ?>"><?php echo $email_id; ?></p>
				<?php endif; ?>
			</div>

		</div>

		<div class="social_icons">		
					<?php
				if( have_rows('social_media_links', 'option') ):
					while( have_rows('social_media_links', 'option') ) : the_row();
					$icon = get_sub_field('icon');
					$link = get_sub_field('link');
				?>
					<a href="<?php echo $link; ?>" class="text-white"><img src="<?php echo $icon; ?>"></a>
					<?php
					endwhile;
				endif;
			?>
		</div>
	</div>

    <div class="row bottom_footer desktop_footer">
	  <div class="col-md-3">
			<div class="footer_logo">
				<img src="<?php echo $footer_image; ?>" alt="Studds Logo" >
			</div>
	  </div>
      <div class="col-lg-6">
			<div class="copyright_footer">
			<p class="mb-0">Copyright © <a href="#" class="text-white text-decoration-underline">STUDDS Accessories Limited</a>. All Rights Reserved 2025</p>
			</div>
      </div>
      <div class="col-md-3">
		<div class="social_icons">		
			<?php
				if( have_rows('social_media_links', 'option') ):
					while( have_rows('social_media_links', 'option') ) : the_row();
					$icon = get_sub_field('icon');
					$link = get_sub_field('link');
				?>
					<a href="<?php echo $link; ?>" class="text-white"><img src="<?php echo $icon; ?>"></a>
					<?php
					endwhile;
				endif;
				?>
		</div>
      </div>
    </div>

	

    <!-- Back to top button here --> 
    <a href="#" id="back-to-top-btn" class="btn btn-danger rounded-circle position-fixed" style="bottom: 20px; right: 20px; display:flex;">
      <!-- <i class="bi bi-arrow-up"></i> -->
	   <img src="<?php echo get_stylesheet_directory_uri().'/assets/img/move-to-top-icon-studds.svg'; ?>" alt="Move to top">
    </a>
  </div>
</footer>

<?php endif; ?>


<?php 
global $boxshop_theme_options;
if( ( !wp_is_mobile() && $boxshop_theme_options['ts_back_to_top_button'] ) || ( wp_is_mobile() && $boxshop_theme_options['ts_back_to_top_button_on_mobile'] ) ): 
?>
<!-- here too -->
<!-- <div id="to-top" class="scroll-button">
	<a class="scroll-button" href="javascript:void(0)" title="<?php esc_attr_e('Back to Top', 'boxshop'); ?>"><?php esc_html_e('Back to Top', 'boxshop'); ?></a>
</div> -->
<?php endif; ?>
<?php wp_footer(); ?>

</body>
</html>