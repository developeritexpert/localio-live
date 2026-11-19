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

                    // 4. Convert Livewire wire:* attributes to valid HTML5 data-wire-* attributes for W3C compliance
                    if (str_contains($content, 'wire:')) {
                        $content = preg_replace('/(?<=\s)wire:([a-zA-Z0-9_\-\.:]+)(?=[=\s\/>])/i', 'data-wire-$1', $content);

                        $restorerScript = '<script id="w3c-livewire-restorer">'
                            . '(function(){'
                            . 'function r(root){'
                            . 'if(!root)root=document.documentElement;'
                            . 'var list=[];'
                            . 'if(root.nodeType===1)list.push(root);'
                            . 'if(root.querySelectorAll){'
                            . 'var sub=root.querySelectorAll("*");'
                            . 'for(var s=0;s<sub.length;s++)list.push(sub[s]);'
                            . '}'
                            . 'for(var i=0;i<list.length;i++){'
                            . 'var el=list[i];var attrs=el.attributes;if(!attrs)continue;'
                            . 'for(var j=0;j<attrs.length;j++){'
                            . 'var a=attrs[j].name;'
                            . 'if(a.indexOf("data-wire-")===0){'
                            . 'var w="wire:"+a.substring(10);'
                            . 'if(!el.hasAttribute(w)){el.setAttribute(w,attrs[j].value);}'
                            . '}'
                            . '}'
                            . '}'
                            . '}'
                            . 'if(window.MutationObserver){'
                            . 'new MutationObserver(function(m){'
                            . 'for(var i=0;i<m.length;i++){'
                            . 'for(var j=0;j<m[i].addedNodes.length;j++){'
                            . 'var n=m[i].addedNodes[j];if(n.nodeType===1){r(n);}'
                            . '}'
                            . '}'
                            . '}).observe(document.documentElement,{childList:true,subtree:true});'
                            . '}'
                            . 'document.addEventListener("DOMContentLoaded",function(){r(document.documentElement);},{capture:true});'
                            . '})();'
                            . '</script>';

                        if (stripos($content, '</head>') !== false) {
                            $content = str_ireplace('</head>', $restorerScript . '</head>', $content);
                        } else {
                            $content = $restorerScript . $content;
                        }
                    }

                    $response->setContent($content);
                }
            }
        }

        return $response;
    }
}
