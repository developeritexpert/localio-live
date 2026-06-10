<div>
    <div x-data="{ 
        open: @entangle('show'),
        title: @entangle('modalTitle'),
        pre_configured_prompt: @entangle('pre_configured_prompt'),
        prompt_name: @entangle('prompt_name'),
        loading: @entangle('loading'),
        ai_output: @entangle('ai_output'),
    }" 
    x-watch="open" 
    x-on:open-changed="if (open) { setTimeout(() => { loading = true; $wire.sendPrompt(); }, 100); }">
        <!-- Modal Backdrop -->
        <div x-show="open" :class="{ 'show': open }" class="modal-backdrop fade" style="display: none;"></div>
        
        <!-- Modal -->
        <div x-show="open" :class="{ 'show': open }" class="modal fade" tabindex="-1" style="display: none;">
            <div class="modal-dialog modal-lg" role="document" style="min-width:80%;">
                <div class="modal-content">
                    <!-- Modal Header -->
                    <button type="button" class="close" @click="$wire.closeModal()" aria-label="Close">
                        <em class="icon ni ni-cross"></em>
                    </button>
                    <div class="modal-header">
                        <h5 class="modal-title" x-text="title"></h5>
                    </div>

                    <!-- Modal Body -->
                    <div class="modal-body" style="max-height:60vh; overflow-y:auto;">
                        <div class="row align-items-start">
                            <!-- AI Response Section -->    
                            <div class="col-md-7">
                                <div class="mb-3">
                                    <label for="ai-model-select" class="form-label fw-bold">Select AI Model</label>
                                    <select wire:model="selectedModel" class="form-select">
                                        {{-- @foreach ($aiModelRefs as $ref)
                                            <option value="{{ $ref }}">{{ $ref }}</option>
                                        @endforeach --}}
                                        <option value="gemini">Gemini 2.0</option>
                                    </select>
                                </div>

                                <template x-if="loading">
                                    <div class="text-center p-4" x-cloak>
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Generating AI response...</span>
                                        </div>
                                        <p class="mt-2 text-muted">Please wait while we generate your content...</p>
                                    </div>
                                </template>
    
                                <template x-if="!loading && ai_output">
                                    <div class="card ai-response-container">
                                        <label class="ai-response-label">AI Response</label>
                                        <div class="ai-response-box p-3">
                                            <p class="ai-response-text" x-text="ai_output" id="ai-output"></p>
                                        </div>
                                    </div>
                                </template>

                                <template x-if="!loading && !ai_output">
                                    <div class="card ai-response-container">
                                        <label class="ai-response-label">AI Response</label>
                                        <div class="ai-response-box p-3 text-center text-muted">
                                            <p>No content generated yet. Click "Generate" to create AI content.</p>
                                        </div>
                                    </div>
                                </template>
                            </div>
    
                            <!-- Pre-configured Prompt Section -->
                            <div class="col-md-5">
                                <div class="card p-3">
                                    <label for="pre-configured" class="form-label fw-bold">
                                        Pre-configured Prompt: <span x-text="prompt_name" class="text-primary"></span>
                                    </label>
                                    <textarea 
                                        wire:model="pre_configured_prompt"
                                        class="form-control p-3 shadow-sm rounded ai-preconfigured-prompt-textbox"
                                        rows="6" 
                                        placeholder="Enter your prompt here...">
                                    </textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Modal Footer -->
                    <div class="modal-footer bg-light">
                        <div>
                            <template x-if="!ai_output && !loading">
                                <button type="button" @click="loading = true; $wire.sendPrompt()" class="btn btn-primary">
                                    Generate
                                </button>
                            </template>
                            
                            <template x-if="loading">
                                <button type="button" disabled class="btn btn-primary">
                                    <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                                    <span x-text="ai_output ? 'Regenerating...' : 'Generating...'"></span>
                                </button>
                            </template>
                            
                            <template x-if="ai_output && !loading">
                                <button type="button" @click="loading = true; $wire.regenerateResponse()" class="btn btn-primary">
                                    <i class="icon ni ni-reload me-1"></i> Regenerate
                                </button>
                            </template>
                            
                            <button 
                                type="button" 
                                wire:click="confirmAndSave()" 
                                :disabled="!ai_output || loading"
                                class="btn btn-success"
                                x-show="ai_output && !loading">
                                Confirm and Save
                            </button>
                            <button 
                                type="button" 
                                wire:click="closeModal()" 
                                class="btn btn-outline-secondary">
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>  