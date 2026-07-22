<?php

namespace App\Http\Controllers\Admin\SiteContent;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\LearnMoreContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use App\Models\StaticContentKey;
use App\Models\StaticContentTranslation;
use Illuminate\Support\Str;

class StaticContentController extends Controller
{
    public function modalindex()
    {
        $langId = getCurrentLanguageID();

        $sections = LearnMoreContent::where('lang_id', $langId)
        ->orderBy('sort_order')
        ->get();

        return view('Admin.learn-more-modal.index', compact('sections'));
    }
    public function modalcreate()
    {
        return view('Admin.learn-more-modal.add',);
    }
    public function edit($id)
    {
        $langId = getCurrentLanguageID();

        $section = LearnMoreContent::where('lang_id', $langId)
            ->findOrFail($id);
        return view('Admin.learn-more-modal.add', compact('section',  'langId'));
    }
    public function modalstoreOrUpdate(Request $request)
    {
        $langId = getCurrentLanguageID();

        // Basic validation first (required, max length)
        $request->validate([
            'sections' => 'required|array',
            'sections.*.title' => 'required|string|max:255',
            'sections.*.content' => 'required|string',
        ]);
        foreach ($request->input('sections') as $index => $sectionData) {
            if (empty($sectionData['title'])) continue;

            $query = LearnMoreContent::where('lang_id', $langId)
                ->where('title', $sectionData['title']);

            if (!empty($sectionData['id'])) {
                $query->where('id', '!=', $sectionData['id']);
            }

            if ($query->exists()) {
                return redirect()->back()
                    ->withErrors(["sections.{$index}.title" => "The title '{$sectionData['title']}' must be unique for this language."])
                    ->withInput();
            }
        }

        // Check for duplicates within input array
        $titles = array_map(fn($s) => $s['title'], $request->input('sections'));
        if (count($titles) !== count(array_unique($titles))) {
            return redirect()->back()
                ->withErrors(['sections' => 'Titles must be unique within the input.'])
                ->withInput();
        }

        // Everything validated - proceed to DB operations
        DB::transaction(function () use ($request, $langId) {
            foreach ($request->input('sections') as $index => $sectionData) {
                if (empty($sectionData['title']) && empty($sectionData['content'])) {
                    continue;
                }

                if (isset($sectionData['id']) && !empty($sectionData['id'])) {
                    $content = LearnMoreContent::where('id', $sectionData['id'])
                        ->where('lang_id', $langId)
                        ->first();

                    if ($content) {
                        $content->update([
                            'title' => $sectionData['title'],
                            'content' => $sectionData['content'],
                            // 'sort_order' => $index + 1,
                            'section_type' => 'body',
                        ]);
                    } else {
                        // If id given but record not found for this lang, create new
                        $this->createLearnMoreContent($sectionData, $index, $langId);
                    }
                } else {
                    $this->createLearnMoreContent($sectionData, $index, $langId);
                }
            }
        });

        return redirect()->route('learn-modal')->with('success', 'Learn More content saved successfully.');
    }

    private function createLearnMoreContent($sectionData, $index, $langId)
    {
        return LearnMoreContent::create([
            'title' => $sectionData['title'],
            'content' => $sectionData['content'],
            'lang_id' => $langId,
            // 'sort_order' => $index + 1,
            'slug' => Str::slug($sectionData['title'] . '-' . time()),
            'section_type' => 'body',
        ]);
    }
    public function modaldestroy($id)
    {
        $content = LearnMoreContent::findOrFail($id);
        $content->delete();
        return back()->with('success', 'Section deleted successfully.');
    }

    public function index()
    {
        $langId = getCurrentLanguageID();
        $defaultLangId =1;
        $keys = StaticContentKey::with(['translations' => function ($q) use ($langId, $defaultLangId) {
            if ($langId !== $defaultLangId) {
                $q->where('lang_id', $langId);
            }
        }])->get()->keyBy('key');

        $sections = [
            'company_size' => [
                'title' => 'Company Size Options',
                'keys' => [
                    'company_size_1',
                    'company_size_2',
                    'company_size_3',
                    'company_size_4',
                ],
            ],
            'common' => [
                'title' => 'Common Page Text',
                'keys' => [
                    'compare_with_alternative',
                    'top_reviews',
                    'latest_reviews',
                    'suggested_products',
                    'view_more',
                    'trending',
                    'learn_more',
                ],
            ],
            'auth' => [
                'title' => 'Authentication Text',
                'keys' => [
                    'login_to_your_account',
                    'login',
                    'sign_up',
                    'forgot_password',
                    'already_have_an_account',
                    'welcome_back',
                    'register_to_localio',
                    'create_an_account',
                    'new_to_localio',
                ],
            ],
            'product' => [
                'title' => 'Product Section Text',
                'keys' => [
                    'explore_products',
                    'top_rated_product_title',
                    'top_rated_product_des',
                    'exclusive_deals_title',
                    'exclusive_deals_des',
                    'product_search_placeholder',
                    'filters',
                    'user_rating',
                    'price_range',
                    'no_prod_mach_fil',
                    'reset_filter',
                    'localio_commissions_message',
                ],
            ],
            'help' => [
                'title' => 'Help & Support Text',
                'keys' => [
                    'help_search_bar_text',
                    'no_faq_avaliable',
                    'knowledge_base',
                    'knowledge_base_desc',
                    'no_policy',
                    'no_term',
                    'contact_us',
                    'send_message',
                ],
            ],
            'Mail' => [
                'title' => 'Mail text For Category And Top Rated Page',
                'keys' => [
                    'top_rated_mail_section_titile',
                    'top_rated_mail_section_desc',
                    'subscribe',
                    'mail_below_text',
                ],
            ],
        ];
        return view('Admin.site-content.site_content', compact('keys', 'sections', 'langId'));
    }

    public function update(Request $request)
    {
        $texts = $request->input('texts', []);
        $langId = getCurrentLanguageID();
        $defaultLangId = 1;

        DB::beginTransaction();
        try {
            foreach ($texts as $key => $value) {
                $keyModel = StaticContentKey::firstOrCreate(['key' => $key]);

                if ($langId == $defaultLangId) {
                    $keyModel->default_value = $value;
                    $keyModel->save();
                } else {
                    StaticContentTranslation::updateOrCreate(
                        ['static_content_key_id' => $keyModel->id, 'lang_id' => $langId],
                        ['value' => $value]
                    );
                }
            }

            DB::commit();
            cache()->forget('static_texts_all');
            return back()->with('success', 'Text content updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed: ' . $e->getMessage());
        }
    }

    public function allContent(){
        // $langId = $request->input('lang_id', 1); // default to lang_id = 1
        $langId = getCurrentLanguageID();
        $siteContent = StaticContentKey::with('translations')->get();
    
        $siteContent->each(function ($item) use ($langId) {
            $item->value = $item->getTranslation($langId);
        });
        // dd(['data' => $siteContent]);

        return view('Admin.site-content.edit_site_content', ['data' => $siteContent]);
    }


    public function updateContent(Request $request)
    {
        $contentKeys = StaticContentKey::all();

        foreach ($contentKeys as $contentKey) {
            $inputValue = $request->input($contentKey->key);

            if ($inputValue !== null) {
                $contentKey->default_value = $inputValue;
                $contentKey->save();
            }
        }

        return redirect()->back()->with('success', 'Static content updated successfully.');
    }


}
