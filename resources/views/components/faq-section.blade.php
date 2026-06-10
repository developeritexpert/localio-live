<section class="faq-section p_120 pt-0 light">
    <div class="container">
        <div class="faq-inner">
            <div class="faq-hd text-center" data-aos="zoom-in" data-aos-duration="1000">
                <h2 style="text-align: start;">{{ $help->left_section_title ?? '' }}</h2>
                <p style="text-align: start;">{{ $help->left_section_description ?? '' }}
                </p>
            </div>
            <div class="faq-accor">
                <div class="accordion" id="accordionExample">
                    <div class="accordion" id="accordionExample">
                        @if ($faqs->isNotEmpty() && $faqs->first()->translations->isNotEmpty())
                            <!-- Check if $faqs is not empty and at least one faq has translations -->
                            @foreach ($faqs->take(5) as $index => $faq)
                                @php
                                    // Get the first translation of each FAQ
                                    $faqtranslation = $faq->translations->first();
                                    $isFirst = $index === 0; // ✅ Check if this is the first FAQ
                                @endphp

                                @if ($faqtranslation)
                                    <!-- Check if the faqtranslation is not null -->
                                    <div class="accordion-item" data-aos="fade-up" data-aos-duration="1000">
                                        <h2 class="accordion-header" id="headingOne{{ $faq->id }}">
                                            <button class="accordion-button {{ $isFirst ? '' : 'collapsed' }}" type="button"
                                                data-bs-toggle="collapse"
                                                data-bs-target="#collapseOne{{ $faq->id }}"
                                                aria-expanded="{{ $isFirst ? 'true' : 'false' }}"
                                                aria-controls="collapseOne{{ $faq->id }}">
                                                <span>{{ $faqtranslation->question ?? 'No question available' }}</span>
                                            </button>
                                        </h2>
                                        <div id="collapseOne{{ $faq->id }}" class="accordion-collapse collapse {{ $isFirst ? 'show' : '' }}"
                                            aria-labelledby="headingOne{{ $faq->id }}"
                                            data-bs-parent="#accordionExample">
                                            <div class="accordion-body">
                                                {!! $faqtranslation->answer ?? 'No answer available' !!}
                                            </div>
                                        </div>
                                    </div>

                                @endif
                            @endforeach
                            <div class="btn-holder">

                                <a class="view-all" href="{{ route('FaqsShow') }}">{{ static_text('view_more') }}
                                </a>


                               </div>
                        @else
                            {{-- <!-- Display this if no FAQs have translations -->
                            <div class="accordion-item" data-aos="fade-up" data-aos-duration="1000">
                                <h2 class="accordion-header" id="headingOne">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                        <span>{{ $help->faq_section_title ?? static_text('no_faq_avaliable') }}</span>
                                    </button>
                                </h2>
                                <div id="collapseOne" class="accordion-collapse collapse"
                                    aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                    <div class="accordion-body">
                                        {{ $help->faq_section_title ?? static_text('no_faq_avaliable') }}
                                    </div>
                                </div>
                            </div> --}}
                            <div class="text-center py-5">
                                {{-- <h3>No FAQ available in this language.</h3> --}}
                                {{-- <p>Please Check this</p> --}}
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
