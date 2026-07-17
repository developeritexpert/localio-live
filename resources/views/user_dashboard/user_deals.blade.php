@extends('user_dashboard_layout.master')

@section('content')

<div class="col-lg-9 p-0">
<div class="user_content">
    <section class="my_eals">
        <div class="uer_nm">
          <h1>My Deals</h1>
          <div id="dealsContainer"></div>

        </div>
    </section>
</div>
</div> 


@endsection

@push('scripts')
<script>
  document.addEventListener("DOMContentLoaded", function () {
    const token = "{{ $token }}";
    const defaultImage = "{{ asset('images/default.png') }}";
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    const container = document.getElementById('dealsContainer');
    const dashboardDefaultImageUrl = "{{ dashboardDefaultImage() }}";

    if (!container) return;

    fetch('/api/my-deals', {
      headers: {
        'Authorization': 'Bearer ' + token,
        'Accept': 'application/json',
      }
    })
    .then(res => res.json())
    .then(data => {
      container.innerHTML = '';

      if (!data.success || !Array.isArray(data.deals) || !data.deals.length) {
        container.innerHTML = `
        <div class="text-center">
          <img src="${dashboardDefaultImageUrl}" alt="No Favorites" class="img-fluid mb-3" style="width: 280px; height: 280px;">
          <p>No deals available.</p>
        </div>
      `;
      return;
      }
      
      const assetBaseUrl = "{{ asset('') }}";
      
      // Store interests for lookup
      const interests = data.debug?.interests || [];
      
      // Create a map of business ID to interest type
      const businessInterestMap = {};
      interests.forEach(interest => {
        if (interest.type === 'business') {
          businessInterestMap[interest.id] = interest.type;
        }
      });

      data.deals.forEach(deal => {
        const business = deal.businesses?.[0];
        if (!business) return;

        const original = parseFloat(deal.original_price);
        const discounted = parseFloat(deal.discounted_price);
        if (isNaN(original) || isNaN(discounted)) return;

        const currency = deal.currency || '$';
        const discountPercent = Math.round(((original - discounted) / original) * 100);
        const logoPath = business.logo || business.icon_id;
        const businessLogo = logoPath ? assetBaseUrl + logoPath : defaultImage;
        const businessName = business.name || 'Business';
        const businessDescription = business.description || `${business.name} is offering this deal.`;
        const businessLink = business.link || '#';
        const rating = business.reviews?.average ?? 0;
        const ratingCount = business.reviews?.count ?? 0;
        
        // Determine interest type based on business ID
        const interestType = businessInterestMap[business.id.toString()] || 'business';

        // Generate stars
        const filledStars = Math.floor(rating);
        let starsHtml = '';
        for (let i = 0; i < 5; i++) {
          starsHtml += `<i class="fas fa-star${i < filledStars ? ' filled' : ''}"></i>`;
        }

        // Create wrapper
        const wrapper = document.createElement('div');
        wrapper.className = 'my_review_deals';
        wrapper.dataset.dealId = business.id; // Use business ID as deal ID
        wrapper.dataset.interestType = interestType;

        wrapper.innerHTML = `
          <div class="child_my_reviews mb-2">
            <div class="child_re_1_review">
              <div class="re_child_1">
                <div class="img_rechild_1">
                  <img src="${businessLogo}" alt="${businessName} Logo" loading="lazy">
                </div>
                <div class="">
                  <h3>${businessName}</h3>
                  <div class="img_p_info">
                    <p>${rating.toFixed(1)}<span>${starsHtml}</span></p>
                    <span>${ratingCount} ratings</span>
                  </div>
                </div>
              </div>
              <div class="re_child_2 size22">
                <i class="fa-solid fa-xmark delete-deal" data-id="${business.id}" data-interest-type="${interestType}"></i>
                <p>Original Price: ${currency}${original.toFixed(2)}/year</p>
                <p>${discountPercent}% Off</p>
              </div>
            </div>
            <div class="child_re_2_review">
              <p>${businessDescription}</p>
              <div class="info_review_child">
                <p>${currency}${discounted.toFixed(2)} <span>/ Year</span></p>
                <div class="btn-holder">
                  <a href="${businessLink}" class="cta unq_btn" target="_blank" rel="noopener">
                    Visit website<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px;margin-left:6px;flex-shrink:0;vertical-align:middle;"><path d="M15 3h6v6"></path><path d="M10 14 21 3"></path><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path></svg>
                  </a>
                </div>
              </div>
            </div>
          </div>
        `;

        // Image fallback
        const img = wrapper.querySelector('img');
        img.addEventListener('error', () => {
          img.src = defaultImage;
        }, { once: true });

      // Delete handler
      const deleteBtn = wrapper.querySelector('.delete-deal');
      deleteBtn.addEventListener('click', function () {
        const dealId = this.dataset.id;
        const interestType = this.dataset.interestType;

        console.log("🛠️ DEBUG: Delete clicked, deal ID =", dealId, "Interest Type =", interestType);

        if (!dealId) {
          Swal.fire({
            toast: true,
            icon: 'error',
            title: 'Missing deal ID.',
            position: 'top-end',
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true
          });
          return;
        }

        fetch('/api/delete-user-interest-deal', {
          method: 'POST',
          headers: {
            'Authorization': 'Bearer ' + token,
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
          },
          body: JSON.stringify({     
            deal_id: dealId,
            interest_type: interestType 
          })
        })
        .then(res => res.json())
        .then(data => {
          console.log("✅ Response:", data);
          if (data.deleted) {
            wrapper.remove();
            
              // ✅ If no deals left, show default message
            if (!document.querySelectorAll('.deal-wrapper').length) {
              container.innerHTML = `
                <div class="text-center">
                  <img src="${dashboardDefaultImageUrl}" alt="No Favorites" class="img-fluid mb-3" style="width: 280px; height: 280px;">
                  <p>No deals available.</p>
                </div>
              `;
            }

            Swal.fire({
              toast: true,
              icon: 'success',
              title: data.message || 'Deal removed successfully.',
              position: 'top-end',
              showConfirmButton: false,
              timer: 2000,
              timerProgressBar: true
            });
          } else {
            Swal.fire({
              toast: true,
              icon: 'error',
              title: data.message || 'Unable to remove deal.',
              position: 'top-end',
              showConfirmButton: false,
              timer: 2000,
              timerProgressBar: true
            });
          }
        })
        .catch(error => {
          console.error("❌ Error:", error);
          Swal.fire({
            toast: true,
            icon: 'error',
            title: 'Server error. Please try again.',
            position: 'top-end',
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true
          });
        });
      });


        container.appendChild(wrapper);
      });
    })
    .catch(() => {
      container.innerHTML = '<p>Failed to load deals.</p>';
    });
  });
</script>
@endpush
