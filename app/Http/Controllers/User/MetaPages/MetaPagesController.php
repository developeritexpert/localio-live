<?php

namespace App\Http\Controllers\User\MetaPages;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\Contact;
use App\Models\Faq;
use App\Models\WhoWeAre;
use App\Models\ExpertGuides;
use App\Models\PolicyTranslation;
use App\Models\ContactContent;
use App\Models\ExpertGuideArticle;
use App\Models\ExpertGuideCategory;
use App\Models\HelpCenterContent;
use App\Models\HomeContent;
use App\Models\PageTile;
use App\Models\Review;

use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Services\MediaService;

class MetaPagesController extends Controller
{
    public function expertGuide()
    {
        $lang_id = getCurrentLanguageID();
        $expertGuide = ExpertGuides::where('lang_id', $lang_id)->first()  ?? ExpertGuides::where('lang_id', 1)->first();
        $pageTileTranslationEducation = PageTile::where('source', 'educationItem')
            ->with('translations')->where('lang_id', $lang_id)
            ->get();
        $pageTileTranslationRightTools = PageTile::where('source', 'righttools')
            ->with('translations')->where('lang_id', $lang_id)
            ->get();

        $categories = ExpertGuideCategory::with(['expertGuideCategoryTranslation' => function ($query) use ($lang_id) {
            $query->where('lang_id', $lang_id);
        }])
            ->whereHas('expertGuideCategoryTranslation', function ($query) use ($lang_id) {
                $query->where('lang_id', $lang_id);
            })
            ->get();
        $reviews = Review::with([
            'translations' => function ($query) use ($lang_id) {
                $query->where('language_id', $lang_id);
            },
            'business.translations' => function ($query) use ($lang_id) {
                $query->where('lang_id', $lang_id);
            },
            'business.reviews', // for calculating average rating
            'user' => function ($query) {
                $query->where('user_type', '!=', 'admin');
            }
        ])
            ->whereHas('user', function ($query) {
                $query->where('user_type', '!=', 'admin');
            })
            ->latest()
            ->take(7)
            ->get()
            ->unique('business_id');
        return view('User.meta-pages.support.expert_guide', compact('reviews', 'expertGuide', 'pageTileTranslationEducation', 'pageTileTranslationRightTools', 'categories'));
    }
    public function expertGuideCategory($lang_code, $slug)
    {
        $lang_id = getCurrentLanguageID();

        $articles = ExpertGuideCategory::whereHas('translationForCurrentLang', function ($query) use ($slug, $lang_id) {
            $query->where('slug', $slug)->where('lang_id', $lang_id);
        })
            ->with([
                'articles.articleTranslations' => function ($query) use ($lang_id) {
                    $query->where('lang_id', $lang_id);
                },
                'translationForCurrentLang'
            ])
            ->firstOrFail();
        $categories = ExpertGuideCategory::with([
            'translationForCurrentLang' // only translation for current lang
        ])->get();

        $currentCategorySlug = $slug ?? null;
        $reviews = Review::with([
            'translations' => function ($query) use ($lang_id) {
                $query->where('language_id', $lang_id);
            },
            'business.translations' => function ($query) use ($lang_id) {
                $query->where('lang_id', $lang_id);
            },
            'business.reviews', // for calculating average rating
            'user' => function ($query) {
                $query->where('user_type', '!=', 'admin');
            }
        ])
            ->whereHas('user', function ($query) {
                $query->where('user_type', '!=', 'admin');
            })
            ->latest()
            ->take(7)
            ->get()
            ->unique('business_id');
        return view('User.meta-pages.support.expert_guide_category', compact('reviews', 'articles', 'categories', 'currentCategorySlug'));
    }

    public function expertGuideArticle($lang_code, $cat_slug, $art_slug)
    {
        $lang_id = getCurrentLanguageID();

        // Get the main article with translations and contents for the current language
        $translationarticle = ExpertGuideArticle::whereHas('articleTranslations', function ($query) use ($art_slug, $lang_id) {
            $query->where('slug', $art_slug)
                ->where('lang_id', $lang_id);
        })
            ->with([
                'articleTranslations' => function ($query) use ($lang_id) {
                    $query->where('lang_id', $lang_id);
                },
                'contents' => function ($query) use ($lang_id) {
                    $query->where('lang_id', $lang_id);
                },
                'category.translationForCurrentLang'
            ])
            ->firstOrFail();

        // Load all categories with translations
        $categories = ExpertGuideCategory::with('translationForCurrentLang')->get();

        // Load all articles in this category
        $allArticles = ExpertGuideArticle::whereHas('category.translationForCurrentLang', function ($query) use ($cat_slug, $lang_id) {
            $query->where('slug', $cat_slug)->where('lang_id', $lang_id);
        })
            ->with([
                'translationForCurrentLang',
                'category.translationForCurrentLang',
                'contents' => function ($query) use ($lang_id) {
                    $query->where('lang_id', $lang_id);
                }
            ])
            ->get();

        // Get related articles (same category, excluding current one)
        $relatedArticles = ExpertGuideArticle::where('category_id', $translationarticle->category_id)
            ->where('id', '!=', $translationarticle->id)
            ->latest()
            ->take(3)
            ->with([
                'translationForCurrentLang',
                'category.translationForCurrentLang'
            ])
            ->get();
        $reviews = Review::with([
            'translations' => function ($query) use ($lang_id) {
                $query->where('language_id', $lang_id);
            },
            'business.translations' => function ($query) use ($lang_id) {
                $query->where('lang_id', $lang_id);
            },
            'business.reviews', // for calculating average rating
            'user' => function ($query) {
                $query->where('user_type', '!=', 'admin');
            }
        ])
            ->whereHas('user', function ($query) {
                $query->where('user_type', '!=', 'admin');
            })
            ->latest()
            ->take(7)
            ->get()
            ->unique('business_id');
        // Return view with all required variables
        return view('User.meta-pages.support.expert_guide_article', [
            'reviews' => $reviews,
            'translationarticle'     => $translationarticle,
            'categories'             => $categories,
            'allArticles'            => $allArticles,
            'relatedArticles'        => $relatedArticles,
            'currentArticleSlug'     => $art_slug,
            'currentCategorySlug'    => $translationarticle->category->translationForCurrentLang->slug ?? null,
        ]);
    }

    public function helpCenter()
    {
        $lang_id = getCurrentLanguageID();

        $help = HelpCenterContent::where('lang_id', $lang_id)
            ->with('categories')
            ->first() ?? HelpCenterContent::where('lang_id', 1)->with('categories')->first();

        $expertGuide = ExpertGuides::where('lang_id', $lang_id)
            ->first() ?? ExpertGuides::where('lang_id', 1)->first();

        $pageTileTranslationEducation = PageTile::where('source', 'educationItem')
            ->with('translations')
            ->where('lang_id', $lang_id)
            ->get();

        $allArticles = ExpertGuideArticle::with(['translationForCurrentLang', 'category.translationForCurrentLang', 'contents'])
            ->where('status', 1)
            ->get();

        $categories = ExpertGuideCategory::with([
            'translationForCurrentLang',
            'articles.translationForCurrentLang'
        ])->get();

        $currentArticleSlug = request()->route('art_slug'); // assuming you use art_slug in route

        return view('User.meta-pages.support.help', compact(
            'help',
            'expertGuide',
            'pageTileTranslationEducation',
            'allArticles',
            'categories',
            'currentArticleSlug'
        ));
    }



    public function contact()
    {
        $lang_id = getCurrentLanguageID();
        $contact = ContactContent::where('lang_id', $lang_id)->first() ?? ContactContent::where('lang_id', 1)->first();
        $pageTileTranslationRightTool = PageTile::where('source', 'right_tool_item')
            ->with('translations')->where('lang_id', $lang_id)
            ->get();
        // dd($pageTileTranslationRightTool);
        $homeContents = HomeContent::where('lang_id', $lang_id)->pluck('meta_value', 'meta_key');
        if ($homeContents->isEmpty()) {
            $homeContents = HomeContent::where('lang_id', 1)->pluck('meta_value', 'meta_key');
        }
        $homeContantImages = HomeContent::where('lang_id', 1)
            ->whereIn('meta_key', [
                'header_background_img',
                'header_img',
                'trusted_brands_img',
                'ai_section_left_img',
                'ai_section_right_img',
                'ai_send_img',
                'review_section_right_img',
                'review_section_left_img',
                'find_tool_left_img',
                'find_tool_right_img',
                'user_reviews_img',
                'price_compare_img',
                'independent_img',
            ])
            ->get()
            ->keyBy('meta_key');
        return view('User.meta-pages.support.contact', compact('homeContantImages','homeContents', 'contact', 'pageTileTranslationRightTool'));
    }

    public function contactFormSubmit(Request $request, MediaService $mediaService)
{
    $request->validate([
        'query_type' => 'required',
        'subject' => 'required|string|max:255',
        'message' => 'required|string',
        'attachment' => 'nullable|file|mimes:jpg,jpeg,svg,png,pdf,docx,txt|max:2048',
    ]);

    try {
        $mediaId = null;

        if ($request->hasFile('attachment')) {
            $media = $mediaService->uploadMedia(
                $request->file('attachment'),
                'contact_attachments'
            );

            $mediaId = $media->id;
        }

        Contact::create([
            'user_id'     => auth()->id(),
            'query_type'  => $request->query_type,
            'subject'     => $request->subject,
            'message'     => $request->message,
            'media_id'    => $mediaId,
        ]);

        return redirect()->back()->with(
            'success',
            'Thank you for contacting us. We have received your message and will get back to you shortly.'
        );

    } catch (\Exception $e) {
        return redirect()->back()->with(
            'error',
            'Something went wrong. Please try again.'
        );
    }
}


    // public function contactFormSubmit(Request $request, MediaService $mediaService)
    // {
    //     // dd($request->all());
    //     // dd('here');
    //     $request->validate([
    //         'query_type' => 'required', // mapped to reason_id
    //         'subject' => 'required|string|max:255',
    //         'message' => 'required|string',
    //         'attachment' => 'nullable|file|mimes:jpg,jpeg,svg,png,pdf,docx,txt|max:2048',
    //     ]);

    //     try {
    //         // Create ticket
    //         $ticket = Ticket::create([
    //             'user_id' => auth()->id(),
    //             'reason_id' => $request->query_type, // match name from form
    //             'subject' => $request->subject,
    //             'status' => 'open',
    //             'seen_status' => false,
    //         ]);

    //         $media = null;
    //         if ($request->hasFile('attachment')) {
    //             $media = $mediaService->uploadMedia($request->file('attachment'), 'ticket_attachments');
    //         }

    //         // Create initial message
    //         TicketMessage::create([
    //             'ticket_id' => $ticket->id,
    //             'user_id' => auth()->id(),
    //             'sent_by' => 'user',
    //             'message' => $request->message,
    //             'media_id' => $media ? $media->id : null,
    //             'seen_status' => false,
    //         ]);

    //         return redirect()->back()->with('ticket_success', [
    //             'message' => 'Thanks for contacting our support team! Your request has been received and we’re already working on it. Here are the details of your ticket:<br><br>
    //                           <strong>Ticket Number:</strong> ' . $ticket->ticket_id . '<br>
    //                           <strong>Date Submitted:</strong> ' . now()->format('F j, Y') . '<br><br>
    //                           Our team will get back to you shortly. You can track the progress of your request using the link below.',
    //             'ticket_id' => $ticket->ticket_id,
    //         ]);

    //     } catch (\Exception $e) {
    //         // dd($e);
    //         return redirect()->back()->with('error', 'Something went wrong. Please try again.');
           
    //     }
    // }

    public function whoWeAre()
    {
        $lang_id = getCurrentLanguageID();
        $whoWeAre = WhoWeAre::where('lang_id', $lang_id)->first() ?? WhoWeAre::where('lang_id', 1)->first();

        $pageTileTranslationPopular = PageTile::where('source', 'popularItem')->where('lang_id', $lang_id)
            ->with('translations')
            ->get();
        // dd($pageTileTranslationPopular);
        $specilistTileTranslation = PageTile::where('source', 'specialists')->where('lang_id', $lang_id)
            ->with('translations')
            ->get();

        return view('User.meta-pages.site-pages.who_we_are', compact('whoWeAre', 'pageTileTranslationPopular', 'specilistTileTranslation'));
    }

    public function showPrivacyPolicy($id)
    {
        $privacy_policy = PolicyTranslation::first();

        //dd($policy_data); // Debugging: Check if data is retrieved

        return view('User.terms_condition.privacy_policy', compact('privacy_policy'));
    }
}
