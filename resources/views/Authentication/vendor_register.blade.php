{{--
@extends('user_layout.master')
@section('content')

    <section class="banner_sec help-cntr-bnr inr-bnr dark lg_Bnr" style="background-color: #003F7D;">
         <div class="bubble-wrp">
            <img src="{{ asset('front/img/small-bnnr-bg.png') ?? '' }}" alt="">
         </div>
    </section>
    <section class="contact_sec login_form p_120 pt-0 light">
         <div class="container">
            <div class="contact_content" data-aos="fade-up" data-aos-duration="1000">
                <div class="hd_text">
                    <h2 class="text-center">Get Started</h2>
                    <p class="text-center">Create an account</p>
                <!-- </div>
                <div class="scl_login">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="login_box size18">
                            <a href="{{route('google.login')}}" class="login_link" style="background: #DB4437;">
                            <span class="scl-icn"><i class="fa-brands fa-google"></i></span>
                            Login with Google
                            </a>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="login_box size18">
                            <a href="{{ route('login.facebook') }}" class="login_link">
                            <span class="scl-icn"><i class="fa-brands fa-facebook"></i></span>
                            Login with Facebook
                            </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="or-separator">
                  <span class="size16">or</span>
                </div> -->
               <!-- Continue with the rest of your form -->
                <form class="register_form" action="{{ route('vendor-register-process', ['locale' => app()->getLocale()]) }}" method="post" id="registerForm">
                    @csrf
                    <!-- First Name Input Field -->
                    <div class="form-group ">
                        <label for="">First Name</label>
                        <input type="text" class="form-control" name="first_name" id="firstName">
                        <span id="firstNameError" class="error text-danger"></span>
                        @if ($errors->has('first_name'))
                            <div class="col-12">
                                <span class="text-danger">{{ $errors->first('first_name') }}</span>
                            </div>
                        @endif
                    </div>

                    <!-- Last Name Input Field -->
                    <div class="form-group ">
                        <label for="">Last Name</label>
                        <input type="text" class="form-control" name="last_name" id="lastName">
                        <span id="lastNameError" class="error text-danger"></span>
                        @if ($errors->has('last_name'))
                            <div class="col-12">
                                <span class="text-danger">{{ $errors->first('last_name') }}</span>
                            </div>
                        @endif
                    </div>
                    <div class="form-group ">
                        <label for="">Job Title</label>
                        <input type="text" class="form-control" name="job_title" id="jobTitle">
                        <span id="jobTitleError" class="error text-danger"></span>
                        @if ($errors->has('job_title'))
                            <div class="col-12">
                                <span class="text-danger">{{ $errors->first('job_title') }}</span>
                            </div>
                        @endif
                    </div>
                    <div class="form-group">
                        <label for="">Business Email</label>
                        <input type="email" class="form-control" name="business_email" id="emailAddress">
                        <span id="emailAddressError" class="error text-danger"></span>

                        @if ($errors->has('business_email'))
                            <div class="col-12">
                                <span class="text-danger">{{ $errors->first('business_email') }}</span>
                            </div>
                        @endif
                    </div>

                    <div class="form-group">
                        <label for="">Business Phone</label>
                        <input type="text" class="form-control" name="business_phone" id="businessPhone">
                        <span id="businessPhoneError" class="error text-danger"></span>

                        @if ($errors->has('business_phone'))
                            <div class="col-12">
                                <span class="text-danger">{{ $errors->first('business_phone') }}</span>
                            </div>
                        @endif
                    </div>
                    <label for="">Corporate Headquarters Location</label>
                    <select name="country_id" class="form-control form-inp-box move-focus mb-4" id="country" style="cursor:pointer;">
                        @foreach($countries as $key=> $country)
                        <option value="{{$country->id}}">{{$country->name}}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('country_id'))
                        <span class="text-danger">{{ $errors->first('country_id') }}</span>
                    @endif


                    <div class="form-group">
                        <label for="">Password</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Password">
                        <span id="togglePassword" class="eye-icon" >
                            <i class="fa fa-eye-slash"></i>
                        </span>
                    </div>
                    <div class="form-group">
                        <label for="">Company Name</label>
                        <input type="text" class="form-control" id="companyName" name="company_name">
                        @if ($errors->has('company_name'))
                            <span class="text-danger">{{ $errors->first('company_name') }}</span>
                        @endif
                        <span id="companyNameError" class="error text-danger"></span>
                    </div>
                    <div class="form-group">
                        <label for="">Company Size</label>
                        <select class="form-control" name="company_size" id="companySize">
                            <option value="" disabled selected hidden>-- Select one --</option>
                            <option value="1-20">1 - 20 Employees</option>
                            <option value="21-50">21 - 50 Employees</option>
                            <option value="51-250">51 - 250 Employees</option>
                            <option value="251-500">251 - 500 Employees</option>
                            <option value="501-5000">501 - 5000 Employees</option>
                            <option value="5001">5001 and up Employees</option>
                        </select>
                        <span id="companySizeError" class="error text-danger"></span>
                        @if ($errors->has('company_size'))
                            <span class="text-danger">{{ $errors->first('company_size') }}</span>
                        @endif
                    </div>

                    <h4>Do you primarily sell software or services?</h4>
                    <input type="radio" id="software" name="category">
                    <label for="software">Software</label>

                    <div id="software-fields" style="display: none;">


                        <div class="form-group ">
                        <label for="product_name">Product Name</label>
                            <input type="text" class="form-control" name="product_name" id="productName">
                            <span id="productNameError" class="error text-danger"></span>
                        </div>
                        <div class="form-group ">
                            <label for="product_name">Product URL</label>
                            <input type="text" class="form-control" name="product_url" id="productUrl">
                            <span id="productUrlError" class="error text-danger"></span>
                        </div>

                    </div>

                    <input type="radio" id="service" name="category">
                    <label for="service">Service</label>

                    <div id="service-fields" style="display: none;">
                        <div class="form-group ">
                            <label for="product_name">Website URL</label>
                            <input type="text" class="form-control" name="website_url" id="websiteUrl">
                            <span id="websiteUrlError" class="error text-danger"></span>
                        </div>
                    </div>
                    <div class="container">
                        <span class="text-danger" id="allErr"></span>
                    </div>

                    <div class="accor-btn">
                        <button type="submit" class="cta cta_white register_btn">Sign Up</button>
                    </div>

               </form>
            </div>
         </div>

    </section>


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    // $(document).ready(function(){
    //     $('.register_btn').click(function(e){
    //         e.preventDefault();  // Prevent form submission

    //         let form = $('#registerForm');
    //         let valid = true;

    //         // Validate each input field and select element
    //         form.find('input, select').each(function() {
    //             let input = $(this);
    //             let errorElement = $('#' + input.attr('id') + 'Error');
    //             let value = input.val()?.trim();
    //             let errorMessage = '';

    //             // Select field validation
    //             if (input.is('select')) {
    //                 if (!value) {
    //                     errorMessage = 'Please select an option from the dropdown.';
    //                 }
    //             }
    //             // Email field validation for business email
    //             else if (input.attr('type') === 'email') {
    //                 if (value === '') {
    //                     errorMessage = 'Please enter an email.';
    //                 } else {
    //                     // Modify the regex to allow only business email (example: @company.com, @organization.org)
    //                     var emailRegex = /^[a-zA-Z0-9._%+-]+@(company\.com|organization\.org)$/;
    //                     if (!emailRegex.test(value)) {
    //                         errorMessage = 'Please enter a valid business email address (e.g., example@company.com).';
    //                     }
    //                 }
    //             }
    //             // Text field validation
    //             else if (input.attr('type') === 'text' && value === '') {
    //                 errorMessage = 'This field is required.';
    //             }

    //             // If there's an error message, display it
    //             if (errorMessage) {
    //                 errorElement.text(errorMessage).show();
    //                 valid = false;
    //             } else {
    //                 errorElement.hide();
    //             }
    //         });

    //         // If all fields are valid, submit the form
    //         if (valid) {
    //             form.submit();
    //         }
    //     });
    // });
    $(document).ready(function () {
        // Hide or show fields based on the selected radio button
        $('input[name="category"]').change(function () {
            if ($('#software').is(':checked')) {
                $('#software-fields').show();
                $('#service-fields').hide();
            } else if ($('#service').is(':checked')) {
                $('#service-fields').show();
                $('#software-fields').hide();
            }
        });
    });

    $(document).ready(function(){
    $('.register_btn').click(function(e){
        e.preventDefault();  // Prevent form submission

        let form = $('#registerForm');
        let valid = true;
        let allError = $('#allErr');


        // Get the selected category
        let categorySelected = $('input[name="category"]:checked').val();

        // If no category is selected, show an error
        if (!categorySelected) {
            allError.text('All fields are required');
            // alert('Please select a category (Software or Service).');
            valid = false;
        }

        // Validate fields based on the selected category
        if (categorySelected === 'software') {
            // Validate software fields only
            $('#software-fields input').each(function() {
                let input = $(this);
                let value = input.val()?.trim();
                let errorElement = $('#' + input.attr('id') + 'Error');
                if (value === '') {
                    errorElement.text('This field is required.').show();
                    valid = false;
                } else {
                    errorElement.hide();
                }
            });

            // Don't validate service fields, hide any existing errors
            $('#service-fields input').each(function() {
                let errorElement = $('#' + $(this).attr('id') + 'Error');
                errorElement.hide();
            });

        } else if (categorySelected === 'service') {
            // Validate service fields only
            $('#service-fields input').each(function() {
                let input = $(this);
                let value = input.val()?.trim();
                let errorElement = $('#' + input.attr('id') + 'Error');
                if (value === '') {
                    errorElement.text('This field is required.').show();
                    valid = false;
                } else {
                    errorElement.hide();
                }
            });

            // Don't validate software fields, hide any existing errors
            $('#software-fields input').each(function() {
                let errorElement = $('#' + $(this).attr('id') + 'Error');
                errorElement.hide();
            });
        }

        // If all fields are valid, submit the form
        if (valid) {
            form.submit();
        }
    });
});

// $(document).ready(function(){
//     $('.register_btn').click(function(e){
//         e.preventDefault();  // Prevent form submission

//         let form = $('#registerForm');
//         let valid = true;
//         let allError = $('#allErr');

//         // Reset all error messages
//         allError.text('');

//         // Get the selected category
//         let categorySelected = $('input[name="category"]:checked').val();

//         // If no category is selected, show an error
//         if (!categorySelected) {
//             allError.text('All fields are required');
//             valid = false;
//         }

//         // Validate general fields (first name, last name, job title, etc.)
//         $('#registerForm input, #registerForm select').each(function() {
//             let input = $(this);
//             let value = input.val()?.trim();
//             let errorElement = $('#' + input.attr('id') + 'Error');

//             if (input.attr('name') && (input.attr('name') !== 'category') && value === '') {
//                 // Only validate non-radio inputs and select elements
//                 errorElement.text('This field is required.').show();
//                 valid = false;
//             } else {
//                 errorElement.hide();
//             }
//         });

//         // Validate business email (if required)
//         let email = $('#emailAddress').val()?.trim();
//         let emailError = $('#emailAddressError');
//         let emailRegex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
//         if (!emailRegex.test(email)) {
//             emailError.text('Please enter a valid business email address.').show();
//             valid = false;
//         } else {
//             emailError.hide();
//         }

//         // Validate business phone field (exactly 10 digits)
//         let businessPhone = $('#businessPhone').val().trim();
//         let businessPhoneError = $('#businessPhoneError');
//         let phoneRegex = /^[0-9]{10}$/; // Regex for exactly 10 digits
//         if (!phoneRegex.test(businessPhone)) {
//             businessPhoneError.text('The business phone number must be exactly 10 digits.').show();
//             valid = false;
//         } else {
//             businessPhoneError.hide();
//         }

//         // Validate company size field
//         let companySize = $('#companySize').val()?.trim();
//         let companySizeError = $('#companySizeError');
//         if (!companySize) {
//             companySizeError.text('Please select a company size.').show();
//             valid = false;
//         } else {
//             companySizeError.hide();
//         }

//         // Validate country field
//         let country = $('#country').val();
//         let countryError = $('#countryError');
//         if (!country) {
//             countryError.text('Please select a country.').show();
//             valid = false;
//         } else {
//             countryError.hide();
//         }

//         // Validate fields based on selected category (Software or Service)
//         if (categorySelected === 'software') {
//             // Validate software fields only
//             $('#software-fields input').each(function() {
//                 let input = $(this);
//                 let value = input.val()?.trim();
//                 let errorElement = $('#' + input.attr('id') + 'Error');
//                 if (value === '') {
//                     errorElement.text('This field is required.').show();
//                     valid = false;
//                 } else {
//                     errorElement.hide();
//                 }
//             });

//             // Don't validate service fields, hide any existing errors
//             $('#service-fields input').each(function() {
//                 let errorElement = $('#' + $(this).attr('id') + 'Error');
//                 errorElement.hide();
//             });

//         } else if (categorySelected === 'service') {
//             // Validate service fields only
//             $('#service-fields input').each(function() {
//                 let input = $(this);
//                 let value = input.val()?.trim();
//                 let errorElement = $('#' + input.attr('id') + 'Error');
//                 if (value === '') {
//                     errorElement.text('This field is required.').show();
//                     valid = false;
//                 } else {
//                     errorElement.hide();
//                 }
//             });

//             // Don't validate software fields, hide any existing errors
//             $('#software-fields input').each(function() {
//                 let errorElement = $('#' + $(this).attr('id') + 'Error');
//                 errorElement.hide();
//             });
//         }
//         // If all fields are valid, submit the form
//         if (valid) {
//             form.submit();
//         }
//     });
// });



</script>
@endsection --}}
