@extends('admin_layout.master')

@section('content')
            <!-- Render Livewire BusinessEdit component -->
            @livewire('business-edit', [
                'id' => $id ?? null,
            ])
            
@endsection

