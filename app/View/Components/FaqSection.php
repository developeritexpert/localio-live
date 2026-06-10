<?php

namespace App\View\Components;
use App\Models\Faq;
use App\Models\HelpCenterContent;
use App\Models\PageTile;
use App\Models\ExpertGuideArticle;
use App\Models\ExpertGuideCategory;



use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class FaqSection extends Component
{
    /**
     * Create a new component instance.
     */
    public $faqs=[];
    public $help;
    public $type;
    // public $pageTileTranslationEducation;
    // public $allArticles = [];
    // public $currentArticleSlug;
    // public $categories = [];



    public function __construct($type="user")
    {
        $langId = session('lang_id', 1); // default to 1 if not set

        $this->type = $type;
        $this->help = HelpCenterContent::where('lang_id', $langId)
        ->with('categories')
        ->first();
        
        $this->faqs = Faq::where('type', $type)
        ->where('status', 'active')
        ->orderBy('position', 'asc') // ✅ Order by position
        ->with(['translations' => function ($query) use ($langId) {
            $query->where('lang_id', $langId);
        }])
        ->get();


    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.faq-section', [
            'faqs' => $this->faqs,
            'help'=>$this->help,

        ]);
    }
}
