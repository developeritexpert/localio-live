@extends('admin_layout.master')

@section('title', 'AI Prompts')

@section('content')
<div class="nk-content-inner business-prompts">
    <div class="nk-content-body">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">Business Prompts</h3>
                    <p class="nk-block-des text-soft">Manage your AI prompt templates for automated content generation.</p>
                </div>
            </div>
        </div>
        <div class="nk-block">
            <div class="card card-bordered card-stretch">
                <div class="card-inner-group">
                    <div class="card-inner position-relative card-tools-toggle">
                        <div class="card-title-group">
                            <div class="card-tools">
                                <div class="card card-bordered card-preview">

                                    @livewire('admin.business-prompt-attach')
                                </div>
                            </div>
                        </div>
                    </div>



                </div>
            </div>
        </div>
    </div>
</div>
@endsection
