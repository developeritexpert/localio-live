
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
                  <h2 class="text-center">Recover Your Password</h2>
               </div>
               <!-- Your existing form -->
      
               <!-- Continue with the rest of your form -->
                <form class="login_form" action="{{ route('password-procc', ['locale' => app()->getLocale()]) }}" method="post" id="recoverForm"  >
                    @csrf
                  <div class="form-group">
                     {{-- <input type="email" class="form-control" name="email"  id="emailAddress" placeholder="Email"> --}}
                     <x-google-input type="email" name="email" id="emailAddress" label="Email"/>
                  </div>
                
                  
                  <div class="">
                     <button type="submit" class="cta cta_white">Send Email</button>
                  </div>
                  <p class="new-accnt text-center mt-4">
                     New to Localio? <a href="{{ route('register') }}" class="sk_blu big-bld">Sign up</a>
                  </p>
               </form>
            </div>
         </div>
    </section>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            console.log('DOM ready');
            const form = document.getElementById("recoverForm");
            const emailInput = document.getElementById("emailAddress");
    
            if (!form || !emailInput) {
                console.warn('Form or email input not found!');
                return;
            }
    
            form.addEventListener("submit", function (e) {
                console.log("Submit event triggered");
                removeErrors();
                let valid = true;
    
                const emailVal = emailInput.value.trim();
                console.log("Trying to submit with email:", emailVal);
    
                if (!emailVal) {
                    console.log("Email is empty");
                    showError(emailInput, "Please enter your email.");
                    valid = false;
                } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailVal)) {
                    console.log("Email format invalid");
                    showError(emailInput, "Please include @ and a valid email format.");
                    valid = false;
                }
    
                if (!valid) {
                    console.log("Preventing submission due to validation error");
                    e.preventDefault();
                } else {
                    console.log("Validation passed, submitting form...");
                    // Just to be safe, we re-trigger form submission
                    // This is often needed in frameworks where Alpine/Livewire might interrupt native behavior
                    setTimeout(() => {
                        form.submit(); // manual trigger
                    }, 10);
                }
            });
    
            emailInput.addEventListener("input", function () {
                emailInput.classList.remove("error");
                const errorText = emailInput.closest(".form-group").querySelector(".text-danger");
                if (errorText) {
                    errorText.remove();
                }
            });
    
            function showError(input, message) {
                input.classList.add("error");
                const formGroup = input.closest(".form-group");
                const errorDiv = document.createElement("div");
                errorDiv.className = "text-danger small";
                errorDiv.textContent = message;
                formGroup.appendChild(errorDiv);
            }
    
            function removeErrors() {
                const allErrors = document.querySelectorAll(".text-danger");
                allErrors.forEach(el => el.remove());
                emailInput.classList.remove("error");
            }
        });
    </script>
    
      
   
      
      

@endsection



