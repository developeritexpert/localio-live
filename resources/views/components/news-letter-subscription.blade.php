<div class="container">
    <!-- Live as if you were to die tomorrow. Learn as if you were to live forever. - Mahatma Gandhi -->
        <div class="subs_content">
            <h2 data-aos="fade-up" data-aos-duration="1000">Get the Best Picks in Your Inbox</h2>
            <p data-aos="fade-up" data-aos-duration="1000">Drop your email to receive trusted software
                picks, all
                recommended by actual users.
            </p>
            <div class="mail_field" data-aos="fade-up" data-aos-duration="1000">
                <div class="email_box">
                    <input type="email" id="email" name="email" placeholder="Email">
                </div>
                <div class="accor-btn sbs_bttn">
                    <a href="" class="cta cta_white">Subscribe</a>
                </div>
            </div>
            <div data-aos="fade-up" data-aos-duration="1000">
                <label>
                    <input type="checkbox" name="agreement" required>
                    I agree to receive promotional emails from Localio and accept the
                    <a href="{{ route('privacy-policy', ['locale' => session('lang_code', 'en-us')]) }}"  target="_blank" style="text-decoration: underline;">Privacy Policy</a>
                    and
                    <a href="{{ route('terms-condition', ['locale' => session('lang_code', 'en-us')]) }}" target="_blank" style="text-decoration: underline;">Terms and Conditions.</a>
                </label>
            </div>

        </div>

</div>
