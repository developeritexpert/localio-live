<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class W3cHtmlCompliance
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only process HTML GET responses
        if ($request->isMethod('GET') && $response instanceof \Illuminate\Http\Response) {
            $contentType = $response->headers->get('Content-Type');
            if (!$contentType || str_contains($contentType, 'text/html')) {
                $content = $response->getContent();
                if (is_string($content)) {
                    // 1. Remove autocomplete attribute from hidden inputs
                    if (str_contains($content, '<input')) {
                        $content = preg_replace_callback('/<input\b[^>]*>/i', function ($match) {
                            $input = $match[0];
                            if (stripos($input, 'type="hidden"') !== false || stripos($input, "type='hidden'") !== false) {
                                $input = preg_replace('/\s+autocomplete=(["\'][^"\']*["\']|[^\s>]+)/i', '', $input);
                            }
                            return $input;
                        }, $content);
                    }

                    // 2. Clean nested double <p><p> and </p></p> from database rich-text content
                    if (str_contains($content, '</p></p>') || str_contains($content, '<p><p>')) {
                        $content = preg_replace('/<p>\s*<p>/i', '<p>', $content);
                        $content = preg_replace('/<\/p>\s*<\/p>/i', '</p>', $content);
                    }

                    // 3. Remove trailing slash on void elements
                    $content = preg_replace('/<(link|input|meta)\b([^>]*?)\s*\/>/i', '<$1$2>', $content);

                    $response->setContent($content);
                }
            }
        }

        return $response;
    }
}
