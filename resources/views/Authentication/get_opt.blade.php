
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
            <form class="login_form" action="{{ route('opt-procc', ['locale' => app()->getLocale()]) }}" method="post" id="otpForm">
               @csrf
            <div class="form-group">
               {{-- <input type="text" class="form-control" name="otp" placeholder="Enter your otp"> --}}

               <x-google-input label="otp" type="text" id="otp"  name="otp"/>
               
               
            </div>
               {{-- @if ($errors->has('otp'))
                  <span class="text-danger">{{ $errors->first('otp') }}</span>
               @endif --}}
            

               <button type="button" class="cta cta_white" id="confirmBtn" >Confirm Otp</button>
            
            <p class="new-accnt text-center mt-4">
               New to Localio? <a href="{{ route('register') }}rev" class="sk_blu big-bld">Sign up</a>
            </p>
         </form>
      </div>
   </div>
</section>



<script>
   document.addEventListener('DOMContentLoaded', function () {
       const form = document.getElementById('otpForm');
       const confirmBtn = document.getElementById('confirmBtn');
       const otpInput = document.getElementById('otp');

       if (!form || !confirmBtn || !otpInput) return;

       confirmBtn.addEventListener('click', function (e) {
           e.preventDefault(); // Prevent default form submission
           removeOtpError();

           const otp = otpInput.value.trim();
           let valid = true;

           if (!/^\d{6}$/.test(otp)) {
               showOtpError(otpInput, 'Please enter a valid 6-digit OTP.');
               valid = false;
           }

           if (valid) {
               form.submit(); // ✅ Submit the form
           }
       });

       otpInput.addEventListener('input', function () {
           removeOtpError();
       });

       function showOtpError(input, message) {
           const group = input.closest('.form-group');
           const box = input.closest('.input-box');

           if (!group || !box) return;

           box.classList.add('error');

           if (!group.querySelector('.text-danger')) {
               const error = document.createElement('div');
               error.className = 'text-danger small mt-1';
               error.textContent = message;
               group.appendChild(error);
           }
       }

       function removeOtpError() {
           const box = otpInput.closest('.input-box');
           const group = otpInput.closest('.form-group');
           box?.classList.remove('error');
           const err = group?.querySelector('.text-danger');
           if (err) err.remove();
       }
   });
</script>


@endsection


