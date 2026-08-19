<div>
    @if ($errorMessage)
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        {{ $errorMessage }}
        <button type="button" class="btn-close" wire:click="$set('errorMessage', '')"></button>
    </div>
    @endif

    @if (count($comparedProductIds) > 0)
    <div class="fixed-bottom bg-light p-3 shadow-lg" id="compareBar" style="border-top: 1px solid #e2e8f0; z-index: 1050;">
        <div class="container">
            <div class="remove-all d-flex justify-content-between align-items-center flex-wrap" style="gap: 15px;">
                <!-- Left side: Remove All & Selected Product Icons -->
                <div class="d-flex align-items-center flex-wrap" style="gap: 15px;">
                    
                    <div class="d-flex align-items-center" style="gap: 10px;">
                        @foreach ($comparedProducts as $product)
                            <div class="position-relative d-flex align-items-center justify-content-center bg-white" style="width: 44px; height: 44px; border: 1px solid #cbd5e1; border-radius: 8px; padding: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                                <x-business-logo :business="$product" />
                                <button type="button" wire:click="removeProduct({{ $product->id }})" onclick="const input = document.getElementById('compare{{ $product->id }}'); if (input) { input.checked = false; input.dispatchEvent(new Event('change', { bubbles: true })); }" style="position: absolute; top: -7px; right: -7px; width: 18px; height: 18px; border-radius: 50%; background: #334155; color: #ffffff; border: none; font-size: 10px; display: flex; align-items: center; justify-content: center; cursor: pointer; padding: 0; box-shadow: 0 1px 3px rgba(0,0,0,0.2);" title="Remove">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        @endforeach

                        @php
                            $emptySlots = max(0, 2 - count($comparedProducts));
                        @endphp
                        @for ($i = 0; $i < $emptySlots; $i++)
                            <div style="width: 44px; height: 44px; border: 1px dashed #cbd5e1; border-radius: 8px; background: #f8fafc;"></div>
                        @endfor
                    </div>
                    <a href="javascript:void(0)" wire:click="clearAll" onclick="document.querySelectorAll('.blue-chkbox input[type=checkbox]').forEach(input => { input.checked = false; input.dispatchEvent(new Event('change', { bubbles: true })); });" style="font-size: 13px; font-weight: 600; color: #06498b; text-decoration: none;" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">
                        Remove all
                    </a>
                </div>

                <!-- Right side: Compare Action Button -->
                <button class="blue-btn btn btn-primary start-comparing-btn" wire:click="goToComparison" @if(count($comparedProductIds) < 2) disabled title="Please select 2 products to compare" style="opacity: 0.6; cursor: not-allowed;" @endif>
                    Start comparing
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
