function ced_rnx_currency_seprator(price)
{
	price = price.toFixed(2);
	price = price.replace('.',global_rnx.price_decimal_separator);
	price = price.replace(',',global_rnx.price_thousand_separator);
	return price;
}
// return function code
function ced_rnx_total()
{	
	//console.log('warranty clain');
	var total = 0;	
	var checkall = true;	
	jQuery("#ced_rnx_total_refund_amount").parent('td').siblings('th').html(global_rnx.ced_rnx_price_deduct_message);
	jQuery(".ced_rnx_return_column").each(function(){
		if(jQuery(this).find("td:eq(0)").children('.ced_rnx_return_product').is(':checked')){
			var product_price = jQuery(this).find("td:eq(0)").children('.ced_rnx_return_product').val();
			var product_qty = jQuery(this).find("td:eq(2)").children('.ced_rnx_return_product_qty').val();
			var product_total = product_price * product_qty;
			var this_obj = this;
			jQuery('.ced_rnx_return_notification_checkbox').show();
			jQuery(this).find("td:eq(3)").children('.ced_rnx_formatted_price').html(ced_rnx_currency_seprator(product_total));
			var order_id = jQuery('#ced_rnx_return_request_form').attr('data-orderid');

			jQuery.ajax({
				url 	: global_rnx.ajaxurl,
				type 	: "POST",
				cache 	: false,
				async 	: false,
				data 	: { 
					action:'ced_rnx_calculate_price_deduct_on_return',
					product_total:product_total, 
					product_qty:product_qty, 
					order_id:order_id 
				},
				success: function(response) 
				{
					product_total = response;
					product_total = parseFloat(product_total);
					jQuery(this_obj).find("td:eq(3)").children('.ced_rnx_formatted_price').html(ced_rnx_currency_seprator(product_total));
					jQuery('.ced_rnx_return_notification_checkbox').hide();
					jQuery("#ced_rnx_total_refund_amount").parent('td').siblings('th').html(global_rnx.ced_rnx_price_deduct_message);
				}
			});
			total += product_total;
		}
		else
		{
			checkall = false;
		}	
	});
	
	if(checkall)
	{
		jQuery('.ced_rnx_return_product_all').attr('checked', true);
	}	
	else
	{
		jQuery('.ced_rnx_return_product_all').attr('checked', false);
	}	
	jQuery("#ced_rnx_total_refund_amount .ced_rnx_formatted_price").html(ced_rnx_currency_seprator(total));
	return total;
}

var total = 0;	
var extra_amount = 0;
function ced_rnx_exchange_total(){	
	//console.log('exchange total-1');
	total = 0;	
	var checkall = true;	
	jQuery(".ced_rnx_exchange_column").each(function(){
		if(jQuery(this).find("td:eq(0)").children('.ced_rnx_exchange_product').is(':checked')){
			//console.log('else');
			var product_price = jQuery(this).find("td:eq(0)").children('.ced_rnx_exchange_product').val();
			var product_qty = jQuery(this).find('.ced_rnx_exchange_product_qty').val();
			var product_total = product_price * product_qty;
			jQuery(this).find("td:eq(3)").children('.ced_rnx_formatted_price').html(ced_rnx_currency_seprator(product_total));
			total += product_total;
		}
		else
		{
			checkall = false;
		}	
	});
	//console.log(total);
	if(checkall){
		jQuery('.ced_rnx_exchange_product_all').attr('checked', true);
	}else{
		jQuery('.ced_rnx_exchange_product_all').attr('checked', false);
	}	
	var selected_product = {};
	var count = 0;
	var orderid = jQuery("#ced_rnx_exchange_request_order").val();
	jQuery(".ced_rnx_exchange_column").each(function(){
		if(jQuery(this).find("td:eq(0)").children('.ced_rnx_exchange_product').is(':checked')){
			var product_info = {};
			var variation_id = jQuery(this).data("variationid");
			var product_id = jQuery(this).data("productid");
			var item_id = jQuery(this).data("itemid");
			var product_price = jQuery(this).find("td:eq(0)").children('.ced_rnx_exchange_product').val();
			var product_qty = jQuery(this).find('.ced_rnx_exchange_product_qty').val();
			product_info['product_id'] = product_id;
			product_info['variation_id'] = variation_id;
			product_info['item_id'] = item_id;
			product_info['price'] = product_price;
			product_info['qty'] = product_qty;
			selected_product[count] = product_info;
			count++;
		}
	});
	var data = {	
		action	:'ced_rnx_exchange_products',
		products: selected_product,
		orderid : orderid,
		security_check	:	global_rnx.ced_rnx_nonce	
	};
	var mwb_check_coupon;
	jQuery('.ced_rnx_return_notification_checkbox').show();
	jQuery.ajax({
		url: global_rnx.ajaxurl, 
		type: "POST",  
		data: data,
		async: false,
		dataType :'json',	
		success: function(response) 
		{
			jQuery('.ced_rnx_return_notification_checkbox').hide();
			jQuery(".ced_rnx_return_notification").html(response.msg);
			mwb_check_coupon = 0;
		}
	});
	
	// jQuery("#ced_rnx_total_exchange_amount .ced_rnx_formatted_price").html(ced_rnx_currency_seprator(total));
	
	var exchanged_amount = jQuery("#ced_rnx_exchanged_total").val();
	extra_amount = 0;
	if(exchanged_amount >= ( total + mwb_check_coupon ) )
	{
		extra_amount = exchanged_amount - ( total + mwb_check_coupon );
		jQuery('#ced_rnx_exchange_extra_amount i').html(global_rnx.extra_amount_msg);
	}
	else
	{
		if( mwb_check_coupon > exchanged_amount )
		{
			exchanged_amount = 0;
		}
		else
		{
			exchanged_amount = exchanged_amount - mwb_check_coupon;
		}
		extra_amount =  total  -  exchanged_amount;
		jQuery('#ced_rnx_exchange_extra_amount i').html(global_rnx.left_amount_msg);
	}
	jQuery(".ced_rnx_exchange_extra_amount .ced_rnx_formatted_price").html(ced_rnx_currency_seprator(extra_amount));
	return total;
}

var files = {};
jQuery(document).ready(function(){
	var session_time = localStorage.getItem("session_time");
	if (global_rnx.exchange_session == 1 && session_time != '') 
	{	
		jQuery(document).on('change','.variations .value select',function(){
			var add_to_cart_btn_class = jQuery( '.single_add_to_cart_button' ).attr('class');
			if(add_to_cart_btn_class.match('wc-variation-is-unavailable') == 'wc-variation-is-unavailable')	
			{
				jQuery('.ced_rnx_add_to_exchanged_detail_variable').attr('disabled','disabled');
				jQuery('.ced_rnx_add_to_exchanged_detail_variable').hide();
				jQuery('.single_add_to_cart_button').attr('disabled','disabled');
				jQuery('.single_variation_wrap').hide();
			}
			else
			{
				jQuery('.ced_rnx_add_to_exchanged_detail_variable').removeAttr('disabled');
				jQuery('.ced_rnx_add_to_exchanged_detail_variable').show();
				jQuery('.single_add_to_cart_button').removeAttr('disabled');
				jQuery('.single_variation_wrap').hide();
			}

		});
		if(global_rnx.ced_rnx_add_to_cart_enable != 'yes')
		{
			jQuery( '.single_variation_wrap' ).hide();
			jQuery('.single_add_to_cart_button').attr('disabled','disabled');

		}
		
		if(session_time){
			setTimeout(function() {
				jQuery('.single_variation_wrap').hide();
			}, 100);
		}
	}
	
	jQuery( '.ced_rnx_cancel_order' ).each( function(){
		jQuery( this ).attr( 'data-order_id', jQuery( this ).attr( 'href' ).split( 'http://' )[1] );
		jQuery( this ).attr( 'href', 'javascript:void(0);' ); 
	});

	jQuery(document).on('click' , '.ced_rnx_cancel_order' , function(){
		jQuery( this ).prop("disabled",true);
		var order_id = jQuery(this).attr('data-order_id');
		jQuery.ajax({
			url: global_rnx.ajaxurl, 
			type: "POST",             
			data: { action : 'ced_rnx_cancel_customer_order' , order_id : order_id , security_check	:	global_rnx.ced_rnx_nonce },
			success: function(respond)   
			{
				window.location.href = respond;
			}
		});
	});
	
	/***************************************************** Return Request code start ********************************************************/
	ced_rnx_total();
	
	//Check all
	jQuery(document).on('change' , '.ced_rnx_return_product_all',function(){
		if(jQuery(this).is(':checked')){
			jQuery(".ced_rnx_return_product").each(function(){
				jQuery(this).attr('checked', true);
			});
		}
		else{
			jQuery(".ced_rnx_return_product").each(function(){
				jQuery(this).attr('checked', false);
			});
		}	
		ced_rnx_total();
	});
	
	//Check one by one
	jQuery(document).on('change','.ced_rnx_return_product',function(){
		// ced_rnx_total();
	});
	
	//Update qty
	jQuery(".ced_rnx_return_product_qty").change(function(){
		ced_rnx_total();
	});
	
	//Add more files to attachment
	jQuery(".ced_rnx_return_request_morefiles").click(function(){
		var count = jQuery(this).data('count');
		var max   = jQuery(this).data('max');
		var html = '<br/><input type="file" class="input-text ced_rnx_return_request_files" name="ced_rnx_return_request_files[]">';
		if (count < max) {
			jQuery("#ced_rnx_return_request_files").append(html);
			jQuery(document).find(".ced_rnx_return_request_morefiles").data('count', count+1);
		}
	});
	
	jQuery('#ced_rnx_coupon_regenertor').click(function(){
		var id = jQuery(this).data('id');
		jQuery('.regenerate_coupon_code_image').css('display' , 'inline-block');
		jQuery.ajax({
			url: global_rnx.ajaxurl, 
			type: "POST",             
			data: { action : 'ced_rnx_coupon_regenertor' , id : id , security_check	:	global_rnx.ced_rnx_nonce },
			success: function(respond)   
			{
				var response = jQuery.parseJSON( respond );
				var wallet_regenraton = '';
				wallet_regenraton = '<b>'+global_rnx.wallet_msg+':<br>'+response.coupon_code_text+': '+response.coupon_code+'<br>'+response.wallet_amount_text+': <span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">'+response.currency_symbol+'</span>'+response.coupon_price+'</span> </b>';
				jQuery('.ced_rnx_wallet').html(wallet_regenraton);
				jQuery('.regenerate_coupon_code_image').css('display' , 'none');
			}
		});
	});
	//Pick all attached files
	jQuery("#ced_rnx_return_request_files").on('change',".ced_rnx_return_request_files",function(e){
		files = {};
		var file_type = e.target.files;
		if(typeof file_type[0]['type'] != 'undefined')
		{
			var type = file_type[0]['type'];
		}	
		if(type == 'image/png' || type == 'image/jpg' || type == 'image/jpeg')
		{
		}	
		else
		{
			jQuery(this).val("");
		}	
		
		var count = 0;
		jQuery(".ced_rnx_return_request_files").each(function(){
			var filename = jQuery(this).val();
			files[count] = e.target.files;
			count++;
		});
		
	});
	//Submit Retun Request form
	jQuery("#ced_rnx_return_request_form").on('click', function(e){
		e.preventDefault();	
		var orderid = jQuery(this).data('orderid');
		// var refund_amount = ced_rnx_total();
		var alerthtml = '';
		var selected_product = {};
		var count = 0;
		var mwb_rnx_selected = false;
		
		var rr_subject  = '';
		var rr_reason = '';

		jQuery(".ced_rnx_return_column").each(function(){
			if(jQuery(this).find("td:eq(0)").children('.ced_rnx_return_product').is(':checked')){
				mwb_rnx_selected = true;
			}	
			
		});
		if(mwb_rnx_selected == false){
			alerthtml += '<li>'+global_rnx.select_product_msg_exchange +'</li>';
		}

		jQuery(".ced_rnx_return_column").each(function(){
			if(jQuery(this).find("td:eq(0)").children('.ced_rnx_return_product').is(':checked')){
				var rr_subject = jQuery(this).find("#ced_rnx_return_request_subject").val();
				if(rr_subject == '' || rr_subject == null){
					alerthtml += '<li>Please enter warranty claim subject.</li>';
				}else if(rr_subject == 'Other'){
					var rr_subject1 = jQuery(this).find(".ced_rnx_return_request_subject_text").val();
					if(rr_subject1 == '' || rr_subject1 == null || ! rr_subject1.match(/[[A-Za-z]/i ) ){
						alerthtml += '<li>Please enter warranty claim reason.</li>';
					}
				}
			}
		});
		
		// select files code- 30-11-22
		jQuery('.ced_rnx_return_column').each(function(){
			if(jQuery(this).find("td:eq(0)").children('.ced_rnx_return_product').is(':checked')){
				var files = jQuery(this).closest('tr').find(".ced_rnx_return_request_files").val();
				if(files == '' || files == null || files == '0'){
					alerthtml += '<li>'+global_rnx.exchange_reason_file_msg +'</li>';
				}
			}
			
		});

		if(alerthtml != '') {
			jQuery("#ced-return-alert").show();
			jQuery("#ced-return-alert").html(alerthtml);
			jQuery('html, body').animate({
				scrollTop: jQuery("#ced_rnx_return_request_container").offset().top
			}, 800);
			return false;
		} else {
			jQuery("#ced-return-alert").hide();
			jQuery("#ced-return-alert").html(alerthtml);
		}	
		var formData = new FormData();
		jQuery(".ced_rnx_return_column").each(function(){
			if(jQuery(this).find("td:eq(0)").children('.ced_rnx_return_product').is(':checked')){
				var product_info = {};
				var variation_id = jQuery(this).data("variationid");
				var product_id = jQuery(this).data("productid");
				var item_id = jQuery(this).data("itemid");
				var product_price = jQuery(this).find("td:eq(0)").children('.ced_rnx_return_product').val();
				var product_qty = jQuery(this).find('.ced_rnx_return_product_qty').val();
				var rr_subject = jQuery(this).closest('tr').find("#ced_rnx_return_request_subject").val();
				var rr_reason = jQuery(this).closest('tr').find(".ced_rnx_return_request_subject_text").val();
				product_info['product_id'] = product_id;
				product_info['variation_id'] = variation_id;
				product_info['item_id'] = item_id;
				product_info['price'] = product_price;
				product_info['qty'] = product_qty;
				product_info['subject'] = rr_subject;
				product_info['reason'] = rr_reason;
							
				// Read selected files
			   var totalfiles = jQuery(this).closest('tr').find('.ced_rnx_return_request_files')[0].files.length;
			   //console.log(totalfiles);
			   for (var index = 0; index < totalfiles; index++) {
			      formData.append("files["+product_id+"]["+count+"][]", jQuery(this).closest('tr').find('.ced_rnx_return_request_files')[0].files[index]);
			   }	
				selected_product[count] = product_info;
				count++;
			}
		});
		var ced_rnx_refund_method = jQuery('input[name=ced_rnx_refund_method]:checked').val();
		var data = {	
			action	:'ced_rnx_return_product_info',
			products: selected_product,
			// amount	: refund_amount,
			subject	: rr_subject,
			reason	: rr_reason,
			orderid : orderid,
			refund_method : ced_rnx_refund_method,
			security_check:global_rnx.ced_rnx_nonce	
		}
		// var formData = new FormData();
		formData.append('data_list', JSON.stringify(data));
		jQuery(".ced_rnx_return_notification").show();
		jQuery("body").css("cursor", "progress");
		//console.log(data);
		//Send return request
		jQuery.ajax({
			url: global_rnx.ajaxurl+'?action=ced_rnx_return_product_info', 
			type: "POST",  
			data: formData,
			dataType :'json',	
			processData: false,
            contentType: false,
			success: function(response) 
			{
				jQuery(".ced_rnx_return_notification").hide();
				jQuery("#ced-return-alert").html(response.msg);
				jQuery("#ced-return-alert").removeClass('woocommerce-error');
				jQuery("#ced-return-alert").addClass("woocommerce-message");
				jQuery("#ced-return-alert").css("color", "white");
				jQuery("#ced-return-alert").show();
				jQuery('html, body').animate({
					scrollTop: jQuery("#ced_rnx_return_request_container").offset().top
				}, 800);
				window.setTimeout(function() {
						window.location.href = global_rnx.myaccount_url+"/orders";
					}, 500);
				
			}
		});
			
	});
// select reason changes added - 30-11-22
jQuery(".ced_rnx_return_request_subject ").change(function(){
		//console.log('i am here warranty');
		var reason = jQuery(this).val();
		var key_id = jQuery('option:selected',this).data("keyid");
		//console.log(jQuery(this));
		//console.log(key_id);
		//console.log(reason);
		if(reason == null || reason == ''){
			//console.log('if');
			jQuery(this).find('.request_subject_text').show();
			//console.log(jQuery(this).parent().parent().find('.request_subject_text').show());
			jQuery(this).parent().parent().find('.request_subject_text').show();
			jQuery(this).parent().parent().find('.ced_rnx_return_request_subject_text').hide();
			jQuery(this).parent().parent().find('.images_return_form').hide(); 
		}else if(key_id == '0'){
			//console.log('if else');
			jQuery(this).parent().parent().find('.images_return_form').show();
			jQuery(this).parent().parent().find('.ced_rnx_return_request_subject_text').show();
			jQuery(this).parent().parent().find('.request_subject_text').show();
		}else{
			//console.log('else');
			jQuery(this).parent().parent().find('.images_return_form').show();
			jQuery(this).parent().parent().find('.request_subject_text').hide();
		}
	});



/***************************************************** Return Request Code End ********************************************************/

/***************************************************** ExchaNge Request code start ********************************************************/

	//ced_rnx_exchange_total();
	
	//Check all
	jQuery(".ced_rnx_exchange_product_all").click(function(){
		if(jQuery(this).is(':checked')){
			jQuery(".ced_rnx_exchange_product").each(function(){
				jQuery(this).attr('checked', true);
			});
		}
		else{
			jQuery(".ced_rnx_exchange_product").each(function(){
				jQuery(this).attr('checked', false);
			});
		}
		ced_rnx_exchange_total();
	});
	
	//Check One by One
	jQuery(".ced_rnx_exchange_product").click(function(){
		ced_rnx_exchange_total();
	});
	
	//Update product qty
	jQuery(".ced_rnx_exchange_product_qty").change(function(){
		ced_rnx_exchange_total();
	});
	jQuery(".ced_rnx_exchange_to_product_qty").change(function(){
		var data = {
			action:'ced_rnx_exchange_to_product_qty',
			orderid:jQuery('#ced_rnx_exchange_request_order').val(),
			id:jQuery(this).data('product-id'),
			qty:jQuery(this).val(),
			security_check	:	global_rnx.ced_rnx_nonce	
		}
		jQuery.ajax({
			url: global_rnx.ajaxurl, 
			type: "POST",  
			data: data,
			dataType :'json',	
			success: function(response) 
			{ 
				location.reload();
			}
		});
		ced_rnx_exchange_total();
	});
	
	/***************************************************** Exchange Request code End ********************************************************/
	
	/************************************************** Add Product to exchange *****************************************************/
	
	ced_rnx_exchange_total();
	
	jQuery(document).on('click' , '.ced_rnx_ajax_add_to_exchange' , function(){

		var current = jQuery(this);
		jQuery(this).addClass('loading');
		var product_id = jQuery(this).data('product_id');
		var product_sku = jQuery(this).data('product_sku');
		var quantity = jQuery(this).data('quantity');
		var price = jQuery(this).data('price');
		var product_info = {};
		product_info['id'] = product_id;
		product_info['qty'] = quantity;
		product_info['sku'] = product_sku;
		product_info['price'] = price;
		
		var data = {	
			action	:'ced_rnx_add_to_exchange',
			products: product_info,
			security_check	:	global_rnx.ced_rnx_nonce	
		}
		
		//Add Exchange Product
		
		jQuery.ajax({
			url: global_rnx.ajaxurl, 
			type: "POST",  
			data: data,
			dataType :'json',	
			success: function(response) 
			{

				current.removeClass('loading');
				current.addClass('added');
				current.parent().html('<a data-price="'+price+'" data-quantity="'+quantity+'" data-product_id="'+product_id+'" data-product_sku="'+product_sku+'" class="button ced_rnx_ajax_add_to_exchange" tabindex="0">'+global_rnx.exchange_text+'</a><a class="button" href="'+response.url+'">'+response.message+'</a>');
			}
		});
	});
	
	jQuery(".exchnaged_product_remove").click(function(){
		jQuery('.ced_rnx_return_notification_checkbox').show();
		var current = jQuery(this);
		var orderid = jQuery("#ced_rnx_exchange_request_order").val();
		var id = jQuery(this).data("key");
		var data = {	
			action	:'ced_rnx_exchnaged_product_remove',
			id: id,
			orderid : orderid,
			security_check	:	global_rnx.ced_rnx_nonce	
		}
		//console.log('remove it all');
		jQuery(this).parent('.product-exchange');
		console.log(jQuery(this).closest('.product-exchange').find('.ced_rnx_prod_img').remove());
		jQuery(this).closest('.product-exchange').find('.show_choose_button').show();
		console.log(jQuery(this).closest('.ced_rnx_product_title').remove());

		jQuery.ajax({
			url: global_rnx.ajaxurl, 
			type: "POST",  
			data: data,
			dataType :'json',	
			success: function(response) 
			{	
				
				// current.parent().remove();
				// var rowCount = jQuery('.ced_rnx_exchanged_products >tbody >tr').length;
				// if(rowCount <= 0)
				// {
					// jQuery('.ced_rnx_exchanged_products').remove();
				// }
				jQuery("#ced_rnx_exchanged_total").val(response.total_price);
				jQuery("#ced_rnx_total_exchange_amount .ced_rnx_formatted_price").html(response.total_price.toFixed(2));
				// ced_rnx_exchange_total();
				jQuery('.ced_rnx_return_notification_checkbox').hide();
			}
		});
	});
	
	jQuery(document).on('click', '.ced_rnx_add_to_exchanged_detail' , function(){

		var current = jQuery(this);
		jQuery(this).addClass('loading');
		var product_id = jQuery(this).data('product_id');
		var product_sku = jQuery(this).data('product_sku');
		var quantity = jQuery(".qty").val();
		var price = jQuery(this).data('price');
		var variations = {};
		jQuery(".variations select").each(function(){
			var name = jQuery(this).data("attribute_name");
			var val = jQuery(this).val();
			variations[name] = val;
		});
		
		var grouped = {};
		jQuery(".group_table tr").each(function(){
			quantity = jQuery(this).find("td:eq(0)").children().children().val();
			id = jQuery(this).find("td:eq(0)").children().children().attr('name');
			id = id.match(/\d+/);
			id = id[0];
			grouped[id] = quantity;
			
		});
		
		var product_info = {};
		product_info['id'] = product_id;
		product_info['qty'] = quantity;
		product_info['sku'] = product_sku;
		product_info['price'] = price;
		product_info['variations'] = variations;
		product_info['grouped'] = grouped;
		
		var data = {	
			action	:'ced_rnx_add_to_exchange',
			products: product_info,
			security_check	:	global_rnx.ced_rnx_nonce	
		}
		
		//Add Exchange Product
		
		jQuery.ajax({
			url: global_rnx.ajaxurl, 
			type: "POST",  
			data: data,
			dataType :'json',	
			success: function(response) 
			{

				current.removeClass('loading');
				current.addClass('added');
				window.location.href = response.url;
				// current.parent().html('<button data-price="'+price+'" data-quantity="'+quantity+'" data-product_id="'+product_id+'" data-product_sku="'+product_sku+'" class="ced_rnx_add_to_exchanged_detail button alt added" tabindex="0">'+global_rnx.exchange_text+'</button><a class="button" href="'+response.url+'">'+response.message+'</a>');
			}
		});
	});
	
	jQuery(".ced_rnx_exchange_request_submit").click(function(){
		//console.log('submitting...');
		// jQuery('#confirm-exchange').modal('show');
		var checkboxChecked = jQuery("#terms").is(":checked");
	
		if (checkboxChecked) {
		var orderid = jQuery("#ced_rnx_exchange_request_order").val();
		// var total = ced_rnx_exchange_total();
		var alerthtml = '';
		var rr_subject = '';
		var selected_product = {};
		var count = 0;
		var mwb_rnx_selected = false;

		jQuery(".ced_rnx_exchange_column").each(function(){
			if(jQuery(this).find("td:eq(0)").children('.ced_rnx_exchange_product').is(':checked')){
				mwb_rnx_selected = true;
				var exchange_amount = jQuery(this).closest('tr').find(".ced_rnx_exchanged_total").val();
				//console.log(exchange_amount);
				if(exchange_amount  == 0){
					alerthtml += '<li>'+global_rnx.before_submit_exchange +'</li>';
				}
			}	
			
		});
		if(mwb_rnx_selected == false){
			alerthtml += '<li>'+global_rnx.select_product_msg_exchange +'</li>';
		}

		jQuery('.ced_rnx_exchange_column').each(function(){
			if(jQuery(this).find("td:eq(0)").children('.ced_rnx_exchange_product').is(':checked')){
				var rr_subject = jQuery(this).closest('tr').find("#ced_rnx_exchange_request_subject").val();
				if(rr_subject == '' || rr_subject == null || rr_subject == '0'){
					rr_subject = jQuery(this).closest('tr').find("#ced_rnx_exchange_request_subject_text").val();
					if(rr_subject == '' || rr_subject == null || ! rr_subject.match(/[[A-Za-z]/i ) ){
						alerthtml += '<li>'+global_rnx.exchange_reason_msg +'</li>';
					}	
				}
			}
			
		});
		// select files code- 21-11-22
		jQuery('.ced_rnx_exchange_column').each(function(){
			if(jQuery(this).find("td:eq(0)").children('.ced_rnx_exchange_product').is(':checked')){
				var files = jQuery(this).closest('tr').find(".ced_rnx_return_request_files").val();
				if(files == '' || files == null || files == '0'){
					//alerthtml += '<li>'+global_rnx.exchange_reason_file_msg +'</li>';
				}
			}
			
		});
		jQuery('.image_error_st').each(function(){
			//if(jQuery(this).find("td:eq(0)").children('.ced_rnx_exchange_product').is(':checked')){
				var totalfiles = document.getElementById("ced_rnx_return_request_files").files.length;
				//console.log(totalfiles);
				if(totalfiles == '' || totalfiles < '1'){
					alerthtml += '<li>Please attach minimum 1 image of product for which exchange requested so that reason of exchange can be understood by us.</li>';
				   
				    
				}
		//	}
			
		});
// 		jQuery('.ced_rnx_exchange_column').each(function(){
// 			if(jQuery(this).find("td:eq(0)").children('.ced_rnx_exchange_product').is(':checked')){
// 				var totalfiles = jQuery(this).closest('tr').find('.ced_rnx_return_request_files')[0].files.length;
// 				console.log(totalfiles);
// 				if(totalfiles == '' || totalfiles < '1'){
// 					alerthtml += '<li>Please attach minimum 1 image of product for which exchange requested so that reason of exchange can be understood by us.</li>';
// 				}
// 			}
			
// 		});


		if(alerthtml != ''){
			jQuery("#ced-exchange-alert").show();
			jQuery("#ced-exchange-alert").html(alerthtml);
			jQuery('html, body').animate({
				scrollTop: jQuery("#ced_rnx_exchange_request_container").offset().top
			}, 800);
			return false;
		}else{
			jQuery("#ced-exchange-alert").hide();
			jQuery("#ced-exchange-alert").html(alerthtml);
		}	
		
		var selected_product_exchange = {};
		var count = 0;
		var orderid = jQuery("#ced_rnx_exchange_request_order").val();
		jQuery('.ced_rnx_exchange_column').each(function(){
			if(jQuery(this).find("td:eq(0)").children('.ced_rnx_exchange_product').is(':checked')){
				check = true;
				var product_info = {};
				var variation_id = jQuery(this).data("variationid");
				var product_id = jQuery(this).data("productid");
				var item_id = jQuery(this).data("itemid");
				var product_qty = jQuery(this).find('.ced_rnx_exchange_product_qty').val();
				product_info['product_id'] = product_id;
				product_info['variation_id'] = variation_id;
				product_info['item_id'] = item_id;
				product_info['qty'] = product_qty;
				selected_product_exchange[count] = product_info;
				count++;
			}
		});
		var formData = new FormData();

		// var form = document.getElementById("ced_rnx_exchange_request_form");

		var selected_product_reason= [];
		jQuery('.ced_rnx_exchange_column').each(function(){
			if(jQuery(this).find("td:eq(0)").children('.ced_rnx_exchange_product').is(':checked')){
				var rr_subject = jQuery(this).closest('tr').find("#ced_rnx_exchange_request_subject").val();
				//console.log(rr_subject);
				if(rr_subject != '' || rr_subject != null || rr_subject != '0'){
					var product_info_reason = {};
						var product_id = jQuery(this).data("productid");
						var key1 = jQuery(this).find('.ced_rnx_exhange_shop').data("key");
						var property_files = jQuery('.ced_rnx_return_request_files')[0].files[0];
						product_info_reason['product_id_key'] = key1;
						product_info_reason['product_id'] = product_id;
						product_info_reason['subject'] = rr_subject;
						product_info_reason['reason'] = rr_subject;
						product_info_reason['Images'] = property_files;
						selected_product_reason[key1] = product_info_reason;
						// formData.append(selected_product_reason[key1], product_info_reason);

						// Read selected files
						   var totalfiles = document.getElementById("ced_rnx_return_request_files").files.length;
						   //console.log(totalfiles);
						   //var totalfiles = jQuery(this).closest('tr').find('.ced_rnx_return_request_files')[0].files.length;
						   for (var index = 0; index < totalfiles; index++) {
						      formData.append("files["+product_id+"]["+key1+"][]", document.getElementById("ced_rnx_return_request_files").files[index]);
						   }

				}
			}
			
		});
		var property_files = jQuery('.ced_rnx_return_request_files')[0].files[0];
		var data_list = {	
			action	:'ced_rnx_submit_exchange_request',
			orderid: orderid,
			product_reason: selected_product_reason,
			subject: rr_subject,
			security_check	:global_rnx.ced_rnx_nonce,
			property_files	:property_files	
		}
		formData.append('data_list', JSON.stringify(data_list));
		//console.log('data_list',JSON.stringify(data_list));
		jQuery('.ced_rnx_return_notification_checkbox').show();
		jQuery.ajax({
			url: global_rnx.ajaxurl+'?action=ced_rnx_submit_exchange_request', 
			type: "POST",  
			data: formData,
			dataType: "json",
			processData: false,
            contentType: false,
			success: function(response) 
			{
			    if (response.success === true) {  
			     //console.log("Success response received:", response);
			     //console.log("Response success value:", response.success);
        //          console.log("Response success type:", typeof response.success);
			    //return;
				jQuery('.ced_rnx_return_notification_checkbox').hide();
				jQuery(".ced_rnx_exchange_notification").hide();
				jQuery("#confirm-exchange").hide();
				var html = "Your request is in process, we will inform you once this is approved.";
				// jQuery("#ced-exchange-alert").html(response.msg);
				jQuery("#ced-exchange-alert").html(html);
				jQuery("#ced-exchange-alert").removeClass('woocommerce-error');
				jQuery("#ced-exchange-alert").addClass("woocommerce-message");
				jQuery("#ced-exchange-alert").css("color", "white");
				jQuery("#ced-exchange-alert").show();
				jQuery('html, body').animate({
					scrollTop: jQuery("#ced_rnx_exchange_request_container").offset().top
				}, 800);
				window.setTimeout(function() {
					window.location.href = global_rnx.myaccount_url+"/orders";
				}, 500);
			} else {
            // Handle case where response.success is false
            //console.log("Processing failure condition");
            var errorMsg = response.data.msg || "Failed to submit exchange request. Please try again later.";
            alert(errorMsg);
            	window.setTimeout(function() {
					window.location.href = global_rnx.myaccount_url+"/orders";
				}, 500);
            // Optionally redirect to orders page or handle as needed
            // window.location.href = global_rnx.myaccount_url + "/orders";
        }
			},
			error: function(jqXHR, textStatus, errorThrown) {
                console.error("Error response received:", jqXHR);
                // Hide loading spinner or notification checkbox on error
                jQuery('.ced_rnx_return_notification_checkbox').hide();
                
                // Prepare error message based on response or default message
                var errorMsg = "Failed to submit exchange request. Please try again later.";
                if (jqXHR.responseJSON && jqXHR.responseJSON.data && jqXHR.responseJSON.data.msg) {
                    errorMsg = jqXHR.responseJSON.data.msg;
                }
                
                // Display error message to the user
                alert(errorMsg);
                
                // Optionally redirect to orders page or handle as needed
                // window.location.href = global_rnx.myaccount_url + "/orders";
            }
		});
		jQuery("#confirm-exchange").hide(); 
	}
	else {
        // If the checkbox is not checked, prevent the default action (submission)
        alert("Can't proceed as you didn't agree to the terms!");
        return false;
    }
	
	});
	
	if ( jQuery( document ).find("#ced_rnx_return_request_subject").length > 0 ) {
		jQuery( document ).find("#ced_rnx_return_request_subject").select();
	}
	if ( jQuery( document ).find("#ced_rnx_exchange_request_subject").length > 0 ) {
		jQuery( document ).find("#ced_rnx_exchange_request_subject").select();
	}

	jQuery(".ced_rnx_exhange_shop").click(function(e){
		console.log(jQuery(this).closest('.product-name-exchange').closest('.ced_rnx_exchange_column'));
		//console.log('here id');
		var check = false;
		var selected_product = {};
		var count = 0;
		var orderid = jQuery("#ced_rnx_exchange_request_order").val();
		var ordering = jQuery(this).data("key");
		var price = jQuery(this).data("price");
		var price = jQuery(this).data("price");
		var product_ids = jQuery(this).attr('id');
		var alerthtml = '';
		var rr_subject = '';
		var permalink = jQuery(this).closest('.ced_rnx_exchange_column').find('.product_permalink').val();
		//console.log(product_ids);
		// if(ordering){
			
			var mwb_rnx_selected = false;
			jQuery(".ced_rnx_exchange_column").each(function(){
				if(jQuery(this).find("td:eq(0)").children('.ced_rnx_exchange_product').is(':checked')){
					mwb_rnx_selected = true;
					
				}	
				
			});
			if(mwb_rnx_selected == false){
				alerthtml += '<li>'+global_rnx.select_product_msg_exchange +'</li>';
			}
			jQuery('.ced_rnx_exchange_column').each(function(){
				if(jQuery(this).find("td:eq(0)").children('.ced_rnx_exchange_product').is(':checked')){
					//console.log('here check ');
					var rr_subject = jQuery(this).closest('tr').find("#ced_rnx_exchange_request_subject").val();
					//console.log(rr_subject);
					// check = false;
					if(rr_subject == '' || rr_subject == null || rr_subject == '0'){
						rr_subject = jQuery(this).closest('tr').find("#ced_rnx_exchange_request_subject_text").val();
						if(rr_subject == '' || rr_subject == null || ! rr_subject.match(/[[A-Za-z]/i ) ){
							//alerthtml += '<li>'+global_rnx.exchange_reason_msg +'</li>';
						}	
					}
				}
				
			});
			if(alerthtml != ''){
				jQuery("#ced-exchange-alert").show();
				jQuery("#ced-exchange-alert").html(alerthtml);
				jQuery('html, body').animate({
					scrollTop: jQuery("#ced_rnx_exchange_request_container").offset().top
				}, 800);
				return false;
			}else{
				jQuery("#ced-exchange-alert").hide();
				jQuery("#ced-exchange-alert").html(alerthtml);
			}	
			jQuery(this).closest('.product-name-exchange').closest('.ced_rnx_exchange_column').each(function(){
				if(jQuery(this).find("td:eq(0)").children('.ced_rnx_exchange_product').is(':checked')){
					check = true;
					var product_info = {};
					var variation_id = jQuery(this).data("variationid");
					var product_id = jQuery(this).data("productid");
					var item_id = jQuery(this).data("itemid");
					var product_price = jQuery(this).find("td:eq(0)").children('.ced_rnx_exchange_product').val();
					var product_qty = jQuery(this).find('.ced_rnx_exchange_product_qty').val();
					product_info['product_id'] = product_id;
					product_info['variation_id'] = variation_id;
					product_info['item_id'] = item_id;
					product_info['price'] = product_price;
					product_info['qty'] = product_qty;
					selected_product[count] = product_info;
					count++;
				}else{
					var alerthtml = '<li>'+global_rnx.select_product_msg_exchange+'</li>';
				}
			});
			//console.log(selected_product);
			if (check == true) 
			{
				var data = {	
					action	:'ced_set_exchange_session',
					products: selected_product,
					orderid : orderid,
					ordering :ordering,
					price :price,
					reason :rr_subject,
					product_ids:product_ids,
					security_check:global_rnx.ced_rnx_nonce	
				}
				//console.log(data);
				jQuery.ajax({
					url: global_rnx.ajaxurl, 
					type: "POST",  
					data: data,
					dataType :'json',	
					success: function(response) 
					{
						/*if(global_rnx.ced_rnx_exchange_variation_enable == true){	
							jQuery('.ced_rnx_exchange_notification_choose_product ').hide();
							jQuery('#ced_rnx_variation_list').html('');
							jQuery('#ced_rnx_variation_list').html('<h4><Strong>'+global_rnx.ced_rnx_exchnage_with_same_product_text+'<Strong></h4>');
							jQuery(".ced_rnx_exchange_column").each(function(){
								if(jQuery(this).find("td:eq(0)").children('.ced_rnx_exchange_product').is(':checked')){
									var product_name = jQuery(this).find("td:eq(1)").children('.ced_rnx_product_title').children('a').html();
									var product_url = jQuery(this).find("td:eq(1)").children('.ced_rnx_product_title').children('a').attr('href');
									var clone = jQuery(this).find("td:eq(1)").clone().appendTo("#ced_rnx_variation_list");
									product_name = product_name.split('-');
									product_name = product_name[0];
									product_url = product_url.split('?');
									product_url = product_url[0];
									clone.find('.ced_rnx_product_title a').html(product_name);
									clone.wrap('<a href="'+product_url+'"></a><br>');
									jQuery('.ced_rnx_exchange_note').append('<a href="'+product_url+'"><strong>'+product_name+'<strong></a><br>');
									}
								});
						}else{*/
							// jQuery('.ced_rnx_exchange_notification_choose_product').show();
							// window.location.href = permalink;
							localStorage.setItem("exchange_session_start",'1');
							window.location.href = global_rnx.shop_url;
						// }
					}
				});
				// return false;
			}
			//console.log(alerthtml);
			// if(check == false)
			// {
			// 	e.preventDefault();
			// 	console.log('here');
			// 	// var alerthtml = '<li>'+global_rnx.select_product_msg_exchange+'</li>';
			// 	jQuery("#ced-exchange-alert").show();
			// 	jQuery("#ced-exchange-alert").html(alerthtml);
			// 	jQuery('html, body').animate({
			// 		scrollTop: jQuery("#ced_rnx_exchange_request_container").offset().top
			// 	}, 800);
			// 	return false;
			// }	
		// }
	});
	
	jQuery("#ced_rnx_return_request_subject").change(function(){
		var reason = jQuery(this).val();
		if(reason == null || reason == ''){
			jQuery("#ced_rnx_return_request_subject_text").show();
		}else{
			jQuery("#ced_rnx_return_request_subject_text").hide();
		}
	});
	
	var reason = jQuery("#ced_rnx_return_request_subject").val();
	
	if(reason == null || reason == ''){
		jQuery("#ced_rnx_return_request_subject_text").show();
	}else{
		jQuery("#ced_rnx_return_request_subject_text").hide();
	}
	
	jQuery(".ced_rnx_exchange_request_subject").change(function(){
		//console.log('i am here');
		var reason = jQuery(this).val();
		var key_id = jQuery('option:selected',this).data("keyid");
		//console.log(jQuery(this));
		//console.log(key_id);
		//console.log(reason);
		if(reason == null || reason == ''){
			jQuery(this).find('.request_subject_text').show();
			console.log(jQuery(this).parent().parent().find('.request_subject_text').show());
			jQuery(this).parent().parent().find('.request_subject_text').show();
			jQuery(this).parent().parent().find('.images_return_form').hide();
		}else if(key_id == '1' || key_id == '2' || key_id == '3' || key_id == '4' || key_id == '5' || key_id == '6' || key_id == '7'){
			jQuery(this).parent().parent().find('.images_return_form').show();
			jQuery(this).parent().parent().find('.request_subject_text').hide();
		}else{
			jQuery(this).parent().parent().find('.images_return_form').hide();
			jQuery(this).parent().parent().find('.request_subject_text').hide();
		}
	});
	
	// var reason = jQuery("#ced_rnx_exchange_request_subject").val();
	// if(reason == null || reason == ''){
	// 	jQuery("#ced_rnx_exchange_request_subject_text").show();
	// }else{
	// 	jQuery("#ced_rnx_exchange_request_subject_text").hide();
	// }
	
	jQuery(document).on('click','.ced_rnx_add_to_exchanged_detail_variable',function(){
		var variation_id = jQuery('[name="variation_id"]').val();
		if(variation_id == null || variation_id <= 0)
		{
			alert('Please choose variation');
			return false;
		}
		var current = jQuery(this);
		jQuery(this).addClass('loading');
		var product_id = jQuery(this).data('product_id');
		var quantity = jQuery(".qty").val();
		var variations = {};
		jQuery(".variations select").each(function(){
			var name = jQuery(this).data("attribute_name");
			var val = jQuery(this).val();
			variations[name] = val;
		});
		
		var grouped = {};
		jQuery(".group_table tr").each(function(){
			quantity = jQuery(this).find("td:eq(0)").children().children().val();
			id = jQuery(this).find("td:eq(0)").children().children().attr('name');
			id = id.match(/\d+/);
			id = id[0];
			grouped[id] = quantity;
			
		});
		
		var product_info = {};
		product_info['id'] = product_id;
		product_info['variation_id'] = variation_id;
		product_info['qty'] = quantity;
		product_info['variations'] = variations;
		product_info['grouped'] = grouped;
		var data = {	
			action	:'ced_rnx_add_to_exchange',
			products: product_info,
			security_check	:	global_rnx.ced_rnx_nonce	
		}
		
		//Add Exchange Product

		jQuery.ajax({
			url: global_rnx.ajaxurl, 
			type: "POST",  
			data: data,
			dataType :'json',	
			success: function(response) 
			{
				current.removeClass('loading');
				current.addClass('added');
				window.location.href = response.url;
				// current.parent().html('<button class="ced_rnx_add_to_exchanged_detail_variable button alt" data-product_id="'+product_id+'"> '+global_rnx.exchange_text+' </button><a class="button" href="'+response.url+'">'+response.message+'</a>');
			}
		});
	});
	jQuery('.ced_rnx_guest_form').on('submit', function(e){
		var order_id = jQuery('#order_id').val();
		var order_email = jQuery('#order_email').val();
		if(order_id == '' || order_email == '')
		{
			
		}
	});
   jQuery('.ced_rnx_cancel_product_all').on('click',function(){
		if (this.checked) {
            jQuery(".ced_rnx_cancel_product").each(function() {
                this.checked=true;
            });
        } else {
            jQuery(".ced_rnx_cancel_product").each(function() {
                this.checked=false;
            });
        }
	});
	jQuery('.ced_rnx_cancel_product').on('click',function(){
		if (jQuery(this).is(":checked")) {
            var isAllChecked = 0;

            jQuery(".ced_rnx_cancel_product").each(function() {
                if (!this.checked)
                {
                    isAllChecked = 1;
                }
            });

            if (isAllChecked == 0) {
                jQuery(".ced_rnx_cancel_product_all").prop("checked", true);
            }     
        }
        else {
            jQuery(".ced_rnx_cancel_product_all").prop("checked", false);
        }

	});
	function popup(){

	}
	jQuery('.ced_rnx_cancel_product_submit').on('click',function(){
		var wr_qty = false;
		var can_all = 0;
		var one_check = false;
		var alerthtml = '';
		$("#agree_chk_error").hide();
		jQuery(".ced_rnx_cancel_product").each(function() {
			if ( this.checked ) {
				one_check = true;
				var parent = jQuery(this).closest('.ced_rnx_cancel_column').find('.product-quantity').find('.ced_rnx_cancel_product_qty');

				var qty_val = parent.val();
				var qty_max = parent.attr("max");

				if( qty_val < qty_max ) {
					can_all = 1;
				} else if( qty_val > qty_max ) {
					wr_qty = true;
				}
			}
		});

		jQuery( "#ced-return-alert" ).css( "color", "#fff" );
		if( ! one_check ) {
			alerthtml = global_rnx.select_product_msg_cancel;
		}
		if( true == wr_qty ) {
			alerthtml = global_rnx.correct_quantity;
		}
        //changes cancel order adding reason - 27-09-22-aarti ojha
        var return_reason_msg = 'Please enter cancel reason.';
		var rr_subject = jQuery("#ced_rnx_return_request_subject").val();
		if(rr_subject == '' || rr_subject == null)
		{
		    var rr_subject1 = jQuery("#ced_rnx_return_request_subject_text").val();
			if(rr_subject1 == '' || rr_subject1 == null || ! rr_subject1.match(/[[A-Za-z]/i ) )
			{
				// alerthtml += '<li>'+return_reason_msg+'</li>';
			}
		}
		var rr_subject1 = jQuery("#ced_rnx_return_request_subject_text").val();

		jQuery('.ced_rnx_cancel_product').each(function(){
			if(jQuery(this).is(':checked')){
				reason = jQuery(this).closest('tr').find('.ced_rnx_return_request_subject').val();
				if(reason == '' || reason == null || ! reason.match(/[[A-Za-z]/i ) ){
					alerthtml += '<li>'+return_reason_msg+'</li>';
				}
				
			}
		});

		// var rr_reason = jQuery(".ced_rnx_return_request_reason").val();
		// if(rr_reason == '' || rr_reason == null){
		// 	alerthtml += '<li>'+return_reason_msg+'</li>';
		// }else{
		// 	r_reason = rr_reason.trim();
		// 	if(r_reason == '' || r_reason == null){
		// 		alerthtml += '<li>'+return_reason_msg+'</li>';
		// 	}
		// }
        //END - 27-09-22
		
		if(alerthtml != '') {
			jQuery("#ced-return-alert").html(alerthtml);
			jQuery("#ced-return-alert").show();
			jQuery('html, body').animate({
				scrollTop: jQuery("#ced_rnx_return_request_container").offset().top
			}, 800);
			return false;
			
		}else{
			jQuery("#ced-return-alert").hide();
			// jQuery("#ced-return-alert").html(alerthtml);
			$('#confirm-delete').modal('show');
		}
		// var order_id = jQuery('.ced_rnx_cancel_product_all').val();
		/*if( can_all == 0 && jQuery('.ced_rnx_cancel_product_all').is(':checked')){
			if(confirm(global_rnx.ced_rnx_confirm))
			{		
				jQuery('.ced_rnx_return_notification').show();
				jQuery.ajax({
					url: global_rnx.ajaxurl, 
					type: "POST",             
					data: { action : 'ced_rnx_cancel_customer_order' , order_id : order_id , security_check	:	global_rnx.ced_rnx_nonce },
					success: function(respond)   
					{
						jQuery('.ced_rnx_return_notification').show();
						window.location.href = respond;
					}
				});
			}
		}else{
			if(confirm(global_rnx.ced_rnx_confirm_products)){
				jQuery('.ced_rnx_return_notification').show();
				var item_ids = [];
				var index = 0;
				var quantity = 0;
				var item_id = 0;
				jQuery('.ced_rnx_cancel_product').each(function(){
					if(jQuery(this).is(':checked'))
					{
						quantity = jQuery(this).closest('tr').find('.ced_rnx_cancel_product_qty').val();
						item_id = jQuery(this).val();
						item_ids.push([item_id, quantity]);
					}
				});

				jQuery.ajax({
					url: global_rnx.ajaxurl, 
					type: "POST",             
					data:{ 	action : 'ced_rnx_cancel_customer_order_products' , 
							order_id : order_id ,
							item_ids : item_ids , 
							subject	: rr_subject1,
							reason	: rr_reason,
							security_check	:	global_rnx.ced_rnx_nonce 
						},
					success: function(respond)   
					{
						jQuery('.ced_rnx_return_notification').hide();
						window.location.href = respond;
					}
				});
				
			}
		}*/

	});

	jQuery(document).on('click','.mwb_order_send_msg_dismiss',function(e) {
		e.preventDefault();
		jQuery('.mwb_order_msg_notice_wrapper').hide();
	});

	jQuery(document).on('click','.mwb_reload_messages',function(e) {
		e.preventDefault();
		jQuery(this).addClass('mwb-loader-icon');
		jQuery('.mwb_order_msg_sub_container').load(document.URL +  ' .mwb_order_msg_main_container');
		setTimeout(function() {
			jQuery('.mwb_reload_messages').removeClass('mwb-loader-icon');
            jQuery('.mwb_order_msg_reload_notice_wrapper').show();
		}, 2000);
        setTimeout(function() {
			jQuery('.mwb_order_msg_reload_notice_wrapper').hide();
		}, 3000);
	});
});

jQuery('#confirm-delete').on('click', '.remove_items', function(e) {
	$("#agree_chk_error").hide();
	 var order_id = jQuery('.order_id').val();
	if(jQuery('#terms').is(':checked')){
		$("#agree_chk_error").hide();
		//console.log('if');
		$('#confirm-delete').modal('show');
		var can_all = 0;
		// if( can_all == 0 && jQuery('.ced_rnx_cancel_product_all').is(':checked')){				
			// jQuery('.ced_rnx_return_notification').show();
			// jQuery.ajax({
			// 	url: global_rnx.ajaxurl, 
			// 	type: "POST",             
			// 	data: { action : 'ced_rnx_cancel_customer_order' , order_id : order_id , security_check	:	global_rnx.ced_rnx_nonce },
			// 	success: function(respond)   
			// 	{	
			// 		$('#confirm-delete').modal('hide');
			// 		jQuery('.ced_rnx_return_notification').show();
			// 		window.location.href = respond;
			// 	}
			// });		
		// }else{
			// if(confirm(global_rnx.ced_rnx_confirm_products)){
				jQuery('.ced_rnx_return_notification').show();
				var item_ids = [];
				var index = 0;
				var quantity = 0;
				var item_id = 0;
				jQuery('.ced_rnx_cancel_product').each(function(){
					if(jQuery(this).is(':checked')){
						quantity = jQuery(this).closest('tr').find('.ced_rnx_cancel_product_qty').val();
						reason = jQuery(this).closest('tr').find('.ced_rnx_return_request_subject').val();
						item_id = jQuery(this).val();
						item_ids.push([item_id, quantity,reason]);
					}
				});
				if ($('.ced_rnx_cancel_product:checked').length == $('.ced_rnx_cancel_product').length) {
				   	var cancel_all = 'cancelled';
				}else{
				    var cancel_all = 'partial-cancel';
				}
				//console.log(item_ids);
				jQuery.ajax({
					url: global_rnx.ajaxurl, 
					type: "POST",             
					data:{ 	action : 'ced_rnx_cancel_customer_order_products' , 
							order_id : order_id ,
							item_ids : item_ids , 
							cancel_all : cancel_all,
							security_check	:	global_rnx.ced_rnx_nonce 
						},
					success: function(respond)   
					{	$('#confirm-delete').modal('hide');
						jQuery('.ced_rnx_return_notification').hide();
						window.location.href = respond;
					}
				});
				
			// }
		// }

	}else{
		$("#agree_chk_error").show();
		//console.log('else');
	}
});


jQuery('.new_close').on('click', function(e) {
	//console.log('close');
    $('#confirm-delete').modal('hide');
});