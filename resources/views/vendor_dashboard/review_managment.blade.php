@extends('vendor_dashboard_layout.master')
@section('content')
<div class="col-lg-9 p-0">
      <div class="user_content">
         <div class="uer_nm">
            <h1> Reviews & Feedback</h1>
         </div>
         @livewire('vendor.review-filter')
      </div>
</div>
@endsection

@push('scripts')
<script>
    window.addEventListener('close-feedback-modal', () => {
        const modal = bootstrap.Modal.getInstance(document.getElementById('feedbackModal'));
        modal.hide();
    });
</script>

@endpush
