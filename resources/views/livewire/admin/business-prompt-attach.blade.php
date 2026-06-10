<div>
    @foreach ($businessPromptAttach as $prompt)
    <div class="card-inner">
        <div class="row top-info-div mb-3">
            <div class="col-md-12">
                <div class="d-flex gap-2 align-items-center">
                    <div class="d-flex justify-content-between align-items-center">
                        <label for=""> <b>Resouce ID: </b> </label>
                        <p> {{ $prompt->resource_id}}</p>
           
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <label for=""> <b>Prompt Name: </b> </label>
                        {{ $this->getSelectedPromptName($prompt->id) ?? 'Not found' }}
               
                    </div>
                </div>
            </div>

            <div class="form-group" >
                <div class="d-flex justify-content-between align-items-start">
                    <div class="">
                        <img src="{{ asset($prompt->frontend_img_path) }}" alt="Document"
                            style="width: 100%; display: block; height:250px ">
                           
                    </div>
                    <div class="" style="flex: 0 0 50%; padding:0px 10px;">
                        <label for=""><b>Select Prompt</b></label>

                        <select class="form-control mt-2 prompt-select" wire:model="selectedPrompts.{{ $prompt->id }}" wire:change="savePromptSelection({{ $prompt->resource_id }}, $event.target.value)">
                            <option value="" hidden {{ empty($prompt->prompt_id) ? 'selected' : '' }}>Select Prompt</option>

                            
                            @foreach ($prompts as $pro)
                                <option value="{{ $pro->id }}" {{ $prompt->prompt_id == $pro->id ? 'selected' : '' }}>{{ $pro->name }}</option>
                            @endforeach
                        </select>
                    
                    </div>
                </div>
            </div>

        </div>
    </div>
    @endforeach
</div>
