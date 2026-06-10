@extends('admin_layout.master')

@section('content')
            <!-- Render Livewire BusinessForm component -->
            @livewire('business-form', [
                'businessId' => $businessId ?? null,
                'faqBusinessId' => $faqBusinessId ?? null,
                'faqEditId' => $faqEditId ?? null,
                'pageMode' => $pageMode ?? null
            ])
            
            
@endsection

