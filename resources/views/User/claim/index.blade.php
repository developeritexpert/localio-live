@extends('user_layout.master')

@section('title', 'Claim your Business Profile | Localio')

@section('content')
<style>
    .claim-hero {
        background: linear-gradient(135deg, #002244 0%, #003f7d 100%);
        color: #fff;
        padding: 60px 0 40px;
        text-align: center;
    }
    .claim-hero h1 {
        font-size: 36px;
        font-weight: 700;
        margin-bottom: 12px;
        color: #fff;
    }
    .claim-hero p {
        font-size: 16px;
        color: #d1e2f3;
        max-width: 650px;
        margin: 0 auto;
    }
    .claim-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.06);
        border: 1px solid #e9ecef;
        padding: 35px;
        margin-top: -30px;
        margin-bottom: 60px;
    }
    .claim-search-box {
        position: relative;
    }
    .claim-search-box input {
        height: 52px;
        border-radius: 8px;
        border: 2px solid #003f7d;
        padding-left: 45px;
        font-size: 16px;
    }
    .claim-search-box i.fa-search {
        position: absolute;
        left: 16px;
        top: 18px;
        color: #003f7d;
        font-size: 18px;
    }
    .search-results-dropdown {
        position: absolute;
        top: 56px;
        left: 0;
        right: 0;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.12);
        border: 1px solid #dee2e6;
        z-index: 999;
        max-height: 380px;
        overflow-y: auto;
        display: none;
    }
    .search-item {
        padding: 14px 18px;
        border-bottom: 1px solid #f1f3f5;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: space-between;
        transition: background 0.15s ease;
    }
    .search-item:hover {
        background: #f8faff;
    }
    .biz-avatar {
        width: 44px;
        height: 44px;
        border-radius: 8px;
        background: #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        color: #003f7d;
        object-fit: contain;
    }
    .selected-biz-header {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 25px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .claim-step-indicator {
        display: flex;
        align-items: center;
        margin-bottom: 30px;
        padding-bottom: 15px;
        border-bottom: 1px solid #eef2f6;
    }
    .step-pill {
        display: flex;
        align-items: center;
        font-weight: 600;
        font-size: 14px;
        color: #64748b;
    }
    .step-pill.active {
        color: #003f7d;
    }
    .step-num {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: #e2e8f0;
        color: #475569;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-right: 8px;
        font-size: 13px;
        font-weight: 700;
    }
    .step-pill.active .step-num {
        background: #003f7d;
        color: #fff;
    }
    .radio-card {
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        padding: 14px 18px;
        cursor: pointer;
        margin-bottom: 12px;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
    }
    .radio-card:hover {
        border-color: #94a3b8;
    }
    .radio-card.selected {
        border-color: #003f7d;
        background: #f0f6fc;
    }
    .radio-card input {
        margin-right: 12px;
    }
    .verify-tab-btn {
        padding: 12px 20px;
        border-radius: 8px;
        font-weight: 600;
        border: 1px solid #cbd5e1;
        background: #fff;
        color: #475569;
        cursor: pointer;
        margin-right: 10px;
        transition: all 0.2s;
    }
    .verify-tab-btn.active {
        background: #003f7d;
        color: #fff;
        border-color: #003f7d;
    }
    .code-input-lg {
        font-size: 28px;
        letter-spacing: 12px;
        text-align: center;
        font-weight: 700;
        height: 60px;
        max-width: 260px;
        margin: 0 auto;
        color: #003f7d;
    }
</style>

<div class="claim-hero">
    <div class="container">
        <h1>Claim your Business Profile</h1>
        <p>Take control of your presence on Localio: respond to customer reviews, get more ratings, and manage your team.</p>
    </div>
</div>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="claim-card">

                <!-- SEARCH SECTION -->
                <div id="section-search" style="{{ $selectedBusiness ? 'display: none;' : '' }}">
                    <h4 class="mb-3 font-weight-bold" style="color: #0f172a;">Search for your business</h4>
                    <p class="text-muted mb-4">Type the name of your company or product to find its Localio profile.</p>

                    <div class="claim-search-box mb-4">
                        <i class="fas fa-search"></i>
                        <input type="text" id="biz-search-input" class="form-control" placeholder="Search by business name (e.g. Bluehost, HostGator)..." autocomplete="off">
                        <div id="search-spinner" style="position: absolute; right: 15px; top: 16px; display: none;">
                            <i class="fas fa-spinner fa-spin text-primary"></i>
                        </div>
                        <div id="search-dropdown" class="search-results-dropdown"></div>
                    </div>

                    <div class="alert alert-light border d-flex align-items-center p-3" style="background: #f8fafc;">
                        <i class="fas fa-info-circle text-primary fa-lg me-3" style="margin-right: 12px;"></i>
                        <span style="font-size: 14px; color: #475569;">
                            To claim a business profile, you must be logged in to your Localio user account. You do not need a separate vendor account.
                        </span>
                    </div>
                </div>

                <!-- WIZARD SECTION -->
                <div id="section-wizard" style="{{ $selectedBusiness ? '' : 'display: none;' }}">
                    
                    <!-- Selected Business Card Header -->
                    <div class="selected-biz-header">
                        <div class="d-flex align-items-center">
                            <div id="selected-biz-icon-container" class="me-3" style="margin-right: 14px;">
                                @if($selectedBusiness && $selectedBusiness->icon_id)
                                    @php
                                        $media = \App\Models\Media::find($selectedBusiness->icon_id);
                                    @endphp
                                    @if($media)
                                        <img src="{{ asset($media->dir_path . '/' . $media->file_name) }}" class="biz-avatar" alt="">
                                    @else
                                        <div class="biz-avatar">{{ strtoupper(substr($selectedBusiness->translations->first()->name ?? 'B', 0, 1)) }}</div>
                                    @endif
                                @else
                                    <div class="biz-avatar" id="selected-biz-avatar-letter">B</div>
                                @endif
                            </div>
                            <div>
                                <h5 class="mb-1 font-weight-bold" id="selected-biz-name" style="color: #0f172a;">
                                    {{ $selectedBusiness?->translations?->first()?->name ?? 'Business Name' }}
                                </h5>
                                <div style="font-size: 13px; color: #64748b;">
                                    <span id="selected-biz-domain"><i class="fas fa-globe me-1"></i> {{ $selectedBusiness?->website_domain ?: 'Domain' }}</span>
                                    <span class="mx-2">•</span>
                                    <span id="selected-biz-category">{{ $selectedBusiness?->category?->translations?->first()?->name ?? 'Category' }}</span>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="resetToSearch()">
                            <i class="fas fa-search me-1"></i> Change
                        </button>
                    </div>

                    <!-- Steps Indicator -->
                    <div class="claim-step-indicator">
                        <div class="step-pill active" id="step-pill-1">
                            <span class="step-num">1</span> Connection & Role
                        </div>
                        <i class="fas fa-chevron-right text-muted mx-3" style="font-size: 12px; margin: 0 14px;"></i>
                        <div class="step-pill" id="step-pill-2">
                            <span class="step-num">2</span> Verification
                        </div>
                    </div>

                    <!-- STEP 1: Connection & Role -->
                    <div id="step-1-content">
                        <h5 class="font-weight-bold mb-3" id="connection-question-title">
                            How are you connected to {{ $selectedBusiness?->translations?->first()?->name ?? 'this business' }}?
                        </h5>

                        <div class="mb-4">
                            <label class="radio-card selected" onclick="selectRadio(this, 'owner')">
                                <input type="radio" name="connection_type" value="owner" checked>
                                <div>
                                    <div class="font-weight-bold text-dark">Owner</div>
                                    <small class="text-muted">I founded or own this business.</small>
                                </div>
                            </label>

                            <label class="radio-card" onclick="selectRadio(this, 'employee')">
                                <input type="radio" name="connection_type" value="employee">
                                <div>
                                    <div class="font-weight-bold text-dark">Employee</div>
                                    <small class="text-muted">I work directly at this business.</small>
                                </div>
                            </label>

                            <label class="radio-card" onclick="selectRadio(this, 'representative')">
                                <input type="radio" name="connection_type" value="representative">
                                <div>
                                    <div class="font-weight-bold text-dark">Authorized representative</div>
                                    <small class="text-muted">I am officially authorized to speak on behalf of this company.</small>
                                </div>
                            </label>

                            <label class="radio-card" onclick="selectRadio(this, 'agency')">
                                <input type="radio" name="connection_type" value="agency">
                                <div>
                                    <div class="font-weight-bold text-dark">Agency managing this business</div>
                                    <small class="text-muted">We manage public relations, marketing, or reviews for this client.</small>
                                </div>
                            </label>

                            <label class="radio-card" onclick="selectRadio(this, 'other')">
                                <input type="radio" name="connection_type" value="other">
                                <div>
                                    <div class="font-weight-bold text-dark">Other</div>
                                    <small class="text-muted">Other professional affiliation with this organization.</small>
                                </div>
                            </label>
                        </div>

                        <!-- Agency Confirmation Checkbox -->
                        <div id="agency-confirmation-box" class="alert alert-warning mb-4" style="display: none;">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="agency_authorized_check">
                                <label class="form-check-label font-weight-bold text-dark" for="agency_authorized_check">
                                    I confirm that our agency is legally authorized to act for and represent this business on Localio.
                                </label>
                            </div>
                        </div>

                        <!-- Role / Job Title -->
                        <div class="mb-4">
                            <label class="form-label font-weight-bold" style="color: #0f172a;">Your role / job title <span class="text-danger">*</span></label>
                            <input type="text" id="job_title_input" class="form-control form-control-lg" placeholder="e.g. Founder, Marketing Manager, Customer Support Lead" value="{{ Auth::user()?->job_title ?? '' }}">
                            <small class="text-muted">This helps our team and community verify who represents this business.</small>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn btn-primary btn-lg px-4" onclick="goToStep2()" style="background: #003f7d; border-color: #003f7d;">
                                Continue to Verification <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </div>

                    <!-- STEP 2: Verification Choice -->
                    <div id="step-2-content" style="display: none;">
                        <h5 class="font-weight-bold mb-3">Verify your authorization</h5>
                        <p class="text-muted mb-4">Choose how you'd like to verify your connection to this business.</p>

                        <!-- Method Tabs -->
                        <div class="d-flex mb-4">
                            <button type="button" class="verify-tab-btn active" id="tab-btn-auto" onclick="switchVerifyTab('auto')">
                                <i class="fas fa-bolt text-warning me-1"></i> Automatic Verification (Fastest)
                            </button>
                            <button type="button" class="verify-tab-btn" id="tab-btn-manual" onclick="switchVerifyTab('manual')">
                                <i class="fas fa-file-shield text-info me-1"></i> Manual Verification
                            </button>
                        </div>

                        <!-- TAB: AUTOMATIC -->
                        <div id="verify-pane-auto">
                            <div class="card p-3 mb-4" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                <h6 class="font-weight-bold text-dark mb-1">Verify using your work email</h6>
                                <p class="text-muted mb-2" style="font-size: 14px;">
                                    Localio automatically verifies claims when your email domain strictly matches the business domain:
                                    <strong class="text-primary" id="auto-biz-domain">@domain.com</strong>
                                </p>
                                <div class="badge bg-success p-2 text-start" style="width: fit-content; font-size: 13px;">
                                    ✓ Example: name@<span id="auto-example-domain">bluehost.com</span>
                                </div>
                                <small class="text-muted mt-2">
                                    Personal email providers (e.g. @gmail.com, @yahoo.com) cannot be verified automatically.
                                </small>
                            </div>

                            <div class="mb-4" id="auto-email-input-box">
                                <label class="form-label font-weight-bold">Work email address <span class="text-danger">*</span></label>
                                <input type="email" id="work_email_input" class="form-control form-control-lg" placeholder="name@yourcompany.com">
                                <div id="auto-email-error" class="text-danger mt-2 font-weight-bold" style="display: none; font-size: 14px;"></div>
                            </div>

                            <!-- CODE VERIFICATION SUB-STEP (Visible after code sent) -->
                            <div id="auto-code-box" style="display: none;" class="text-center p-4 border rounded mb-4 bg-light">
                                <i class="fas fa-envelope-open-text fa-3x text-primary mb-3"></i>
                                <h5 class="font-weight-bold">Enter Verification Code</h5>
                                <p class="text-muted" style="font-size: 14px;">
                                    We sent a 6-digit code to <strong id="sent-code-target-email"></strong>.<br>
                                    You can also click the link in that email to verify instantly.
                                </p>
                                <div class="my-3">
                                    <input type="text" id="verification_code_input" maxlength="6" class="form-control form-control-lg code-input-lg" placeholder="123456" autocomplete="one-time-code">
                                </div>
                                <div id="code-error" class="text-danger font-weight-bold mb-3" style="display: none;"></div>
                                <button type="button" class="btn btn-success btn-lg px-4" onclick="submitVerificationCode()">
                                    <i class="fas fa-check-circle me-1"></i> Verify & Claim Profile
                                </button>
                                <div class="mt-3">
                                    <a href="javascript:void(0)" onclick="resendVerificationCode()" class="text-muted" style="font-size: 13px;">
                                        Didn't receive code? Resend Code
                                    </a>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between" id="auto-actions-row">
                                <button type="button" class="btn btn-outline-secondary" onclick="goToStep1()">
                                    <i class="fas fa-arrow-left me-1"></i> Back
                                </button>
                                <button type="button" class="btn btn-primary btn-lg px-4" id="btn-send-code" onclick="submitStartClaim('automatic')" style="background: #003f7d; border-color: #003f7d;">
                                    Send Verification Code <i class="fas fa-paper-plane ms-2"></i>
                                </button>
                            </div>
                        </div>

                        <!-- TAB: MANUAL -->
                        <div id="verify-pane-manual" style="display: none;">
                            <div class="card p-3 mb-4" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                <h6 class="font-weight-bold text-dark mb-1">Request Manual Verification</h6>
                                <p class="text-muted mb-0" style="font-size: 14px;">
                                    Can't use an official domain email? Provide evidence of authorization (company documents, official corporate registry, or official channel proof) and our team will review manually.
                                </p>
                            </div>

                            <div class="mb-3">
                                <label class="form-label font-weight-bold">Contact email for updates</label>
                                <input type="email" id="manual_email_input" class="form-control" placeholder="your-email@example.com" value="{{ Auth::user()?->email ?? '' }}">
                            </div>

                            <div class="mb-3">
                                <label class="form-label font-weight-bold">Role / Company details & authorization explanation</label>
                                <textarea id="evidence_text_input" class="form-control" rows="3" placeholder="Explain your position, relationship with the business, and why manual verification is required..."></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label font-weight-bold">Evidence from official business channel</label>
                                <input type="text" id="evidence_channel_input" class="form-control" placeholder="e.g. Link to official team page, LinkedIn verified company profile, or support ticket">
                            </div>

                            <div class="mb-4">
                                <label class="form-label font-weight-bold">Official business documentation (Optional)</label>
                                <input type="file" id="evidence_doc_input" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                                <small class="text-muted">Accepts PDF, JPG, PNG, DOC (max 10MB). E.g. Certificate of incorporation, utility bill, or agency authorization letter.</small>
                            </div>

                            <div class="d-flex justify-content-between">
                                <button type="button" class="btn btn-outline-secondary" onclick="goToStep1()">
                                    <i class="fas fa-arrow-left me-1"></i> Back
                                </button>
                                <button type="button" class="btn btn-primary btn-lg px-4" id="btn-submit-manual" onclick="submitStartClaim('manual')" style="background: #003f7d; border-color: #003f7d;">
                                    Submit for Manual Review <i class="fas fa-check ms-2"></i>
                                </button>
                            </div>
                        </div>

                    </div>

                    <!-- SUCCESS STATE -->
                    <div id="claim-success-box" style="display: none;" class="text-center p-5">
                        <div class="mb-3 text-success">
                            <i class="fas fa-check-circle fa-4x"></i>
                        </div>
                        <h3 class="font-weight-bold" id="success-title">Profile Claim Submitted!</h3>
                        <p class="text-muted mb-4" id="success-message"></p>
                        <a href="{{ route('user-dashboard') }}" class="btn btn-primary btn-lg px-4" id="success-action-btn" style="background: #003f7d; border-color: #003f7d;">
                            Go to Dashboard
                        </a>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>

<script>
    let selectedBusinessId = {{ $selectedBusiness?->id ?? 'null' }};
    let currentBizDomain = "{{ $selectedBusiness?->website_domain ?? '' }}";
    let activeClaimId = null;
    let searchTimeout = null;

    const isLoggedIn = {{ Auth::check() ? 'true' : 'false' }};

    function checkAuthOrPrompt() {
        if (!isLoggedIn) {
            const loginModal = document.getElementById('login-modal');
            if (loginModal) {
                loginModal.style.display = 'flex';
                loginModal.classList.remove('hidden');
                document.body.classList.add('modal-open');
            } else if (window.Livewire) {
                Livewire.dispatch('show-login-modal');
            } else {
                window.location.href = "{{ route('login', ['locale' => app()->getLocale()]) }}";
            }
            return false;
        }
        return true;
    }

    // ── SEARCH AUTOCOMPLETE ──────────────────────────────────────────────────
    const searchInput = document.getElementById('biz-search-input');
    const searchDropdown = document.getElementById('search-dropdown');
    const searchSpinner = document.getElementById('search-spinner');

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const q = this.value.trim();
            if (q.length < 2) {
                searchDropdown.style.display = 'none';
                return;
            }

            searchSpinner.style.display = 'block';
            searchTimeout = setTimeout(() => {
                fetch(`{{ route('claim.search', ['locale' => app()->getLocale()]) }}?q=${encodeURIComponent(q)}`)
                    .then(res => res.json())
                    .then(data => {
                        searchSpinner.style.display = 'none';
                        renderSearchResults(data.results || []);
                    })
                    .catch(() => {
                        searchSpinner.style.display = 'none';
                    });
            }, 300);
        });
    }

    function renderSearchResults(items) {
        if (!items.length) {
            searchDropdown.innerHTML = '<div class="p-3 text-muted text-center">No businesses found matching your query.</div>';
            searchDropdown.style.display = 'block';
            return;
        }

        let html = '';
        items.forEach(item => {
            const avatarHtml = item.icon_url 
                ? `<img src="${item.icon_url}" class="biz-avatar me-3" alt="">`
                : `<div class="biz-avatar me-3">${(item.name || 'B').charAt(0).toUpperCase()}</div>`;

            const claimStatus = item.is_claimed 
                ? `<span class="badge bg-secondary">Claimed</span>`
                : `<button class="btn btn-sm btn-primary px-3" style="background:#003f7d; border-color:#003f7d;">Claim</button>`;

            html += `
                <div class="search-item" onclick="selectBusiness(${JSON.stringify(item).replace(/"/g, '&quot;')})">
                    <div class="d-flex align-items-center">
                        ${avatarHtml}
                        <div>
                            <div class="font-weight-bold text-dark">${item.name}</div>
                            <small class="text-muted">${item.website_domain || ''} • ${item.category_name}</small>
                        </div>
                    </div>
                    <div>${claimStatus}</div>
                </div>
            `;
        });

        searchDropdown.innerHTML = html;
        searchDropdown.style.display = 'block';
    }

    function selectBusiness(biz) {
        if (!checkAuthOrPrompt()) return;

        selectedBusinessId = biz.id;
        currentBizDomain = biz.website_domain || '';

        document.getElementById('selected-biz-name').textContent = biz.name;
        document.getElementById('selected-biz-domain').innerHTML = `<i class="fas fa-globe me-1"></i> ${biz.website_domain || 'Domain not specified'}`;
        document.getElementById('selected-biz-category').textContent = biz.category_name;
        document.getElementById('connection-question-title').textContent = `How are you connected to ${biz.name}?`;

        const iconContainer = document.getElementById('selected-biz-icon-container');
        if (biz.icon_url) {
            iconContainer.innerHTML = `<img src="${biz.icon_url}" class="biz-avatar" alt="">`;
        } else {
            iconContainer.innerHTML = `<div class="biz-avatar">${(biz.name || 'B').charAt(0).toUpperCase()}</div>`;
        }

        document.getElementById('auto-biz-domain').textContent = currentBizDomain ? `@${currentBizDomain}` : '@company.com';
        document.getElementById('auto-example-domain').textContent = currentBizDomain || 'company.com';

        searchDropdown.style.display = 'none';
        document.getElementById('section-search').style.display = 'none';
        document.getElementById('section-wizard').style.display = 'block';
    }

    function resetToSearch() {
        document.getElementById('section-wizard').style.display = 'none';
        document.getElementById('section-search').style.display = 'block';
        if (searchInput) searchInput.focus();
    }

    // ── RADIO SELECTION ──────────────────────────────────────────────────────
    function selectRadio(element, val) {
        document.querySelectorAll('.radio-card').forEach(card => card.classList.remove('selected'));
        element.classList.add('selected');
        const input = element.querySelector('input[type="radio"]');
        if (input) input.checked = true;

        const agencyBox = document.getElementById('agency-confirmation-box');
        if (agencyBox) {
            agencyBox.style.display = (val === 'agency') ? 'block' : 'none';
        }
    }

    function goToStep2() {
        if (!checkAuthOrPrompt()) return;

        const jobTitle = document.getElementById('job_title_input').value.trim();
        if (!jobTitle) {
            alert('Please enter your role or job title.');
            document.getElementById('job_title_input').focus();
            return;
        }

        const connType = document.querySelector('input[name="connection_type"]:checked').value;
        if (connType === 'agency') {
            const check = document.getElementById('agency_authorized_check');
            if (!check || !check.checked) {
                alert('Please confirm that your agency is authorized to represent this business.');
                return;
            }
        }

        document.getElementById('step-1-content').style.display = 'none';
        document.getElementById('step-2-content').style.display = 'block';

        document.getElementById('step-pill-1').classList.remove('active');
        document.getElementById('step-pill-2').classList.add('active');
    }

    function goToStep1() {
        document.getElementById('step-2-content').style.display = 'none';
        document.getElementById('step-1-content').style.display = 'block';

        document.getElementById('step-pill-2').classList.remove('active');
        document.getElementById('step-pill-1').classList.add('active');
    }

    function switchVerifyTab(tab) {
        if (tab === 'auto') {
            document.getElementById('tab-btn-auto').classList.add('active');
            document.getElementById('tab-btn-manual').classList.remove('active');
            document.getElementById('verify-pane-auto').style.display = 'block';
            document.getElementById('verify-pane-manual').style.display = 'none';
        } else {
            document.getElementById('tab-btn-manual').classList.add('active');
            document.getElementById('tab-btn-auto').classList.remove('active');
            document.getElementById('verify-pane-manual').style.display = 'block';
            document.getElementById('verify-pane-auto').style.display = 'none';
        }
    }

    // ── SUBMIT CLAIM ─────────────────────────────────────────────────────────
    function submitStartClaim(method) {
        if (!checkAuthOrPrompt()) return;

        const connType = document.querySelector('input[name="connection_type"]:checked').value;
        const jobTitle = document.getElementById('job_title_input').value.trim();
        const agencyAuth = (connType === 'agency' && document.getElementById('agency_authorized_check').checked) ? 1 : 0;

        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('business_id', selectedBusinessId);
        formData.append('connection_type', connType);
        formData.append('job_title', jobTitle);
        formData.append('agency_authorized', agencyAuth);
        formData.append('verification_method', method);

        if (method === 'automatic') {
            const email = document.getElementById('work_email_input').value.trim();
            const errDiv = document.getElementById('auto-email-error');
            errDiv.style.display = 'none';

            if (!email) {
                errDiv.textContent = 'Please enter your official business work email.';
                errDiv.style.display = 'block';
                return;
            }

            formData.append('work_email', email);

            const btn = document.getElementById('btn-send-code');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending Code...';

            fetch(`{{ route('claim.start', ['locale' => app()->getLocale()]) }}`, {
                method: 'POST',
                headers: { 'Accept': 'application/json' },
                body: formData,
            })
            .then(res => res.json())
            .then(data => {
                btn.disabled = false;
                btn.innerHTML = 'Send Verification Code <i class="fas fa-paper-plane ms-2"></i>';

                if (!data.success) {
                    let msg = data.message || 'Verification error.';
                    if (data.errors) {
                        const firstKey = Object.keys(data.errors)[0];
                        if (firstKey && data.errors[firstKey].length) {
                            msg = data.errors[firstKey][0];
                        }
                    }
                    errDiv.textContent = msg;
                    errDiv.style.display = 'block';
                    return;
                }

                if (data.already_member) {
                    window.location.href = data.redirect_url;
                    return;
                }

                // Code successfully sent!
                activeClaimId = data.claim_id;
                document.getElementById('sent-code-target-email').textContent = data.work_email;
                document.getElementById('auto-email-input-box').style.display = 'none';
                document.getElementById('auto-actions-row').style.display = 'none';
                document.getElementById('auto-code-box').style.display = 'block';
                document.getElementById('verification_code_input').focus();
            })
            .catch(() => {
                btn.disabled = false;
                btn.innerHTML = 'Send Verification Code <i class="fas fa-paper-plane ms-2"></i>';
                errDiv.textContent = 'A network error occurred. Please try again.';
                errDiv.style.display = 'block';
            });

        } else {
            // Manual verification
            const manualEmail = document.getElementById('manual_email_input').value.trim();
            const evidenceText = document.getElementById('evidence_text_input').value.trim();
            const evidenceChannel = document.getElementById('evidence_channel_input').value.trim();
            const docInput = document.getElementById('evidence_doc_input');

            formData.append('work_email', manualEmail);
            formData.append('evidence_text', evidenceText);
            formData.append('evidence_channel', evidenceChannel);

            if (docInput && docInput.files.length) {
                formData.append('evidence_document', docInput.files[0]);
            }

            const btn = document.getElementById('btn-submit-manual');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';

            fetch(`{{ route('claim.start', ['locale' => app()->getLocale()]) }}`, {
                method: 'POST',
                headers: { 'Accept': 'application/json' },
                body: formData,
            })
            .then(res => res.json())
            .then(data => {
                btn.disabled = false;
                btn.innerHTML = 'Submit for Manual Review <i class="fas fa-check ms-2"></i>';

                if (!data.success) {
                    let msg = data.message || 'Submission failed.';
                    if (data.errors) {
                        const firstKey = Object.keys(data.errors)[0];
                        if (firstKey && data.errors[firstKey].length) {
                            msg = data.errors[firstKey][0];
                        }
                    }
                    alert(msg);
                    return;
                }

                showSuccessScreen(
                    'Manual Verification Submitted!',
                    data.message,
                    '{{ route("user-dashboard", ["locale" => app()->getLocale()]) }}',
                    'Back to Dashboard'
                );
            })
            .catch(() => {
                btn.disabled = false;
                btn.innerHTML = 'Submit for Manual Review <i class="fas fa-check ms-2"></i>';
                alert('A network error occurred. Please try again.');
            });
        }
    }

    function submitVerificationCode() {
        const code = document.getElementById('verification_code_input').value.trim();
        const errDiv = document.getElementById('code-error');
        errDiv.style.display = 'none';

        if (code.length !== 6) {
            errDiv.textContent = 'Please enter the 6-digit verification code.';
            errDiv.style.display = 'block';
            return;
        }

        fetch(`{{ route('claim.verify_code', ['locale' => app()->getLocale()]) }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                claim_id: activeClaimId,
                verification_code: code,
            })
        })
        .then(res => res.json())
        .then(data => {
            if (!data.success) {
                let msg = data.message || 'Invalid code.';
                if (data.errors) {
                    const firstKey = Object.keys(data.errors)[0];
                    if (firstKey && data.errors[firstKey].length) {
                        msg = data.errors[firstKey][0];
                    }
                }
                errDiv.textContent = msg;
                errDiv.style.display = 'block';
                return;
            }

            showSuccessScreen(
                'Profile Verified Successfully!',
                'You now manage this business profile. You can respond to customer reviews, invite team members, and request updates.',
                data.redirect_url,
                'Manage Business Profile'
            );
        })
        .catch(() => {
            errDiv.textContent = 'A network error occurred. Please try again.';
            errDiv.style.display = 'block';
        });
    }

    function resendVerificationCode() {
        if (!activeClaimId) return;

        fetch(`{{ route('claim.resend_code', ['locale' => app()->getLocale()]) }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ claim_id: activeClaimId })
        })
        .then(res => res.json())
        .then(data => {
            alert(data.message || 'Code resent.');
        });
    }

    function showSuccessScreen(title, message, btnUrl, btnText) {
        document.getElementById('step-1-content').style.display = 'none';
        document.getElementById('step-2-content').style.display = 'none';
        document.querySelector('.claim-step-indicator').style.display = 'none';

        const successBox = document.getElementById('claim-success-box');
        document.getElementById('success-title').textContent = title;
        document.getElementById('success-message').textContent = message;

        const actionBtn = document.getElementById('success-action-btn');
        actionBtn.href = btnUrl;
        actionBtn.textContent = btnText;

        successBox.style.display = 'block';
    }
</script>
@endsection

