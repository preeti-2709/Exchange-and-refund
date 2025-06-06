jQuery(document).ready(function ($) {
    
    var today = new Date().toISOString().split('T')[0]; // Get today's date in YYYY-MM-DD format

    // Function to show error message (removes old error if exists)
    function showError(input, message) {
        $(input).next('.error-message').remove();
        $(input).after('<span class="error-message" style="color:red;">' + message + '</span>');
    }
    
    // Function to format date as 'YYYY-MM-DD'
    function formatDate(date) {
        var day = ('0' + date.getDate()).slice(-2);
        var month = ('0' + (date.getMonth() + 1)).slice(-2);
        var year = date.getFullYear();
        return year + '-' + month + '-' + day;
    }

    // Event listener for changes to the billing date input
    $('input[name="billing_date"]').on('change', function() {
        var billingDate = new Date($(this).val());
        var today = new Date();
        today.setHours(0, 0, 0, 0); // Set to midnight to compare only dates

        // Clear any previous error messages
        clearError(this);

        // Check if the billing date is valid and not in the future
        if (!isNaN(billingDate) && billingDate <= today) {
            // Set warranty start date to billing date
            var warrantyStartDate = billingDate;

            // Set warranty end date to one year after the billing date
            var warrantyEndDate = new Date(billingDate);
            warrantyEndDate.setFullYear(warrantyEndDate.getFullYear() + 1);

            // Update the warranty date fields
            $('input[name="warranty_start_date"]').val(formatDate(warrantyStartDate));
            $('input[name="warranty_end_date"]').val(formatDate(warrantyEndDate));
        } else {
            // Display error message if billing date is invalid or in the future
            showError(this, "Billing date cannot be greater than today.");
            $(this).val(''); // Clear the invalid date
            $('input[name="warranty_start_date"]').val('');
            $('input[name="warranty_end_date"]').val('');
        }
    });


    // Function to clear error messages
    function clearError(element) {
        // Implement your error clearing logic here
        // For example, you can remove the error class and any associated message
        $(element).removeClass('error');
        $(element).siblings('.error-message').remove();
        if ($('.error-message').length === 0) {
            $("#warranty-form-submit-btn").removeClass("disable-button");
        }
        
    }

    // **Validation for Text Fields (Country, State, City, First & Last Name)**
    $('input[name="country"], input[name="state"], input[name="city"], input[name="first_name"], input[name="last_name"]').on('input', function () {
        var value = $(this).val().trim();
        if (value === '') {
            showError(this, "This field is required.");
        } else if (!/^[A-Za-z\s]+$/.test(value)) {
            showError(this, "Only letters and spaces are allowed.");
        } else {
            clearError(this);
        }
    });

    
    $('input[name="email"]').on('input', function () {
        var value = $(this).val().trim();
        if (value === '') {
            showError(this, "Email is required.");
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
            showError(this, "Invalid email format.");
        } else {
            clearError(this);
        }
    });
    
    $('input[name="contact_number"]').on('input', function () {
        var value = $(this).val().trim();
        if (value === '') {
            showError(this, "Contact number is required.");
        } else if (!/^\+?(\d{1,3})?[-.\s]?\(?\d{1,4}\)?[-.\s]?\d{3,4}[-.\s]?\d{3,4}$/.test(value)) {
            showError(this, "Invalid contact number.");
        } else {
            clearError(this);
        }
    });

    $('input[name="invoice_pdf"]').on('change', function () {
        var file = this.files[0];
    
        if (!file) {
            showError(this, "Please upload a file.");
            return;
        }
    
        var fileType = file.name.split('.').pop().toLowerCase();
        var allowedExtensions = ['pdf', 'png', 'jpg', 'jpeg', 'ttf'];
    
        if ($.inArray(fileType, allowedExtensions) === -1) {
            showError(this, "Only PDF, PNG, JPG, JPEG, and TTF files are allowed.");
            $(this).val('');
            return;
        }
    
        var maxSizeInBytes = 5 * 1024 * 1024;
        if (file.size > maxSizeInBytes) {
            showError(this, "File size must be less than 5 MB.");
            $(this).val('');
            return;
        }
    
        clearError(this);
    });


    $('input[name="invoice_number"]').on('input', function () {
        var value = $(this).val().trim();
        if (value === '') {
            showError(this, "Invoice number is required.");
        } else if (!/^[A-Za-z0-9\-]+$/.test(value)) {
            showError(this, "Invalid invoice number.");
        } else {
            clearError(this);
        }
    });
    
    // **Form Submission Validation**
    $('#warranty-form').submit(function (event) {
       

        $('.loading_img_gif').removeClass("d-none");
        event.preventDefault(); // Prevent normal form submission
    
        $('.error-message').remove(); // Clear previous errors
        var isValid = true;
    
        // Check required text fields
        $('input[name="first_name"], input[name="last_name"], input[name="country"], input[name="state"], input[name="city"]').each(function () {
            if ($(this).val().trim() === '') {
                showError(this, "This field is required.");
                isValid = false;
            }
        });
    
        // Check email
        var email = $('input[name="email"]').val().trim();
        if (email === '') {
            showError('input[name="email"]', "This field is required.");
            isValid = false;
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            showError('input[name="email"]', "Invalid email format.");
            isValid = false;
        }
    
        // Check contact number
        var contactNumber = $('input[name="contact_number"]').val().trim();
        if (contactNumber === '') {
            showError('input[name="contact_number"]', "This field is required.");
            isValid = false;
        }
    
        // Check invoice number
        var invoiceNumber = $('input[name="invoice_number"]').val().trim();
        if (invoiceNumber === '') {
            showError('input[name="invoice_number"]', "This field is required.");
            isValid = false;
        }
        
        // ✅ Validate Invoice PDF
        var invoiceFile = $('input[name="invoice_pdf"]')[0].files[0];
        if (!invoiceFile) {
            showError('input[name="invoice_pdf"]', "This field is required.");
            isValid = false;
        }
    
        // Check billing date
        var billingDate = $('input[name="billing_date"]').val().trim();
        if (billingDate === '') {
            showError('input[name="billing_date"]', "This field is required.");
            isValid = false;
        } else if (billingDate > today) {
            showError('input[name="billing_date"]', "Billing date cannot be greater than today.");
            isValid = false;
        }
    
        // Stop form submission if validation fails
        if (!isValid) {
            
            $('.loading_img_gif').addClass("d-none");
            return;
        }
        
        // Stop form submission if validation fails
        if (isValid) {
           
        } else {
           
            $('.loading_img_gif').addClass("d-none");
        }

        
        var formData = new FormData(this);
        formData.append('action', 'warranty_activation'); // Add action
        // formData.append('nonce', warranty_ajax.nonce);    // Add nonce for security

          $.ajax({
            url: warranty_ajax.ajax_url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    // Show success message
                    $('#warranty-form-container').hide();
                     $('#warranty-success-message').removeClass("d-none");
                    $('#warranty-success-message').show();
                    
                    // Optionally update success message with additional data from response
                    $('#warranty-success-message .activatation_dates span').text(response.data.warranty_start_date);
                    $('#warranty-success-message .expiry_date span').text(response.data.warranty_end_date);
      
                } else {
                    console.log('Warranty activation failed: ' + response.data);
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error:", error, xhr.responseText);
                console.log('Error activating warranty. Please check the console for details.');
            }
        });

    });
});