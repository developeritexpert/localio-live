<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Policy;
use App\Models\Language;
use App\Models\TermsTranslation;
use App\Models\PolicyTranslation;
use Illuminate\Support\Facades\Session;

class TermAndConditionController extends Controller
{
    public function showLegalPage($slug = 'terms-of-service')
    {
        $locale = getCurrentLocale();
        $lang_code = Language::where('lang_code', $locale)->first();

        if (!$lang_code) {
            $lang_code = Language::where('lang_code', 'en-us')->first();
        }

        $titles = [
            'terms-of-service'      => 'Terms of service',
            'privacy-policy'        => 'Privacy policy',
            'cookie-policy'         => 'Cookie policy',
            'community-guidelines'  => 'Community guidelines',
            'affiliate-disclosure'  => 'Affiliate disclosure',
            'copyright-dmca-policy' => 'Copyright & DMCA policy',
            'legal-notice'          => 'Legal notice',
        ];

        $documentTitle = $titles[$slug] ?? 'Legal Document';

        $langId = $lang_code ? $lang_code->id : 1;

        $doc = PolicyTranslation::where('key', $slug)
            ->where('lang_id', $langId)
            ->first();

        if (!$doc) {
            if ($slug == 'privacy-policy') {
                $policies = PolicyTranslation::where('lang_id', $langId)->get();
                if ($policies->isNotEmpty()) {
                    $description = $policies->pluck('description')->implode("<br><br>");
                    $doc = (object)[
                        'title' => 'Privacy policy',
                        'description' => $description,
                    ];
                }
            } elseif ($slug == 'terms-of-service') {
                $terms = TermsTranslation::where('lang_id', $langId)->get();
                if ($terms->isNotEmpty()) {
                    $description = $terms->pluck('description')->implode("<br><br>");
                    $doc = (object)[
                        'title' => 'Terms of service',
                        'description' => $description,
                    ];
                }
            }
        }

        if (!$doc) {
            $doc = (object)[
                'title' => $documentTitle,
                'description' => '<p>Content for ' . e($documentTitle) . ' will be added soon.</p>',
            ];
        }

        return view('User.terms_condition.legal_page', [
            'document'      => $doc,
            'activeSlug'    => $slug,
            'documentTitle' => $documentTitle,
        ]);
    }

    public function privacyPolicy()
    {
        return $this->showLegalPage('privacy-policy');
    }

    public function termsCondtion()
    {
        return $this->showLegalPage('terms-of-service');
    }

    public function cookiePolicy()
    {
        return $this->showLegalPage('cookie-policy');
    }

    public function communityGuidelines()
    {
        return $this->showLegalPage('community-guidelines');
    }

    public function affiliateDisclosure()
    {
        return $this->showLegalPage('affiliate-disclosure');
    }

    public function copyrightDmcaPolicy()
    {
        return $this->showLegalPage('copyright-dmca-policy');
    }

    public function legalNotice()
    {
        return $this->showLegalPage('legal-notice');
    }
}
