<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class RemoveLivewireTemp
{
    public function handle(Request $request, Closure $next)
    {
        File::deleteDirectory(storage_path('app/livewire-tmp'));
        return $next($request);
    }
}
