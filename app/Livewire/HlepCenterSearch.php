<?php

namespace App\Livewire;

use App\Models\Business;
use App\Models\BusinessTranslation;
use App\Models\Category;
use App\Models\ExpertGuideArticle;
use App\Models\ExpertGuideArticleTranslation;
use Livewire\Component;

class HlepCenterSearch extends Component
{
    public $query = '';
    public $results = [];
    public $placeholder;

    public function mount($placeholder = 'Search...')
    {
        $this->placeholder = $placeholder;
    }

    public function updated($name, $value)
    {
        if ($name === 'query') {
            $this->performSearch();
        }
    }

    public function updatedQuery()
    {
        $this->performSearch();
    }

    public function performSearch()
    {
        $lang_id = getCurrentLanguageID();

        if (!empty($this->query)) {
            $this->results = ExpertGuideArticleTranslation::where('lang_id', $lang_id)
                ->where('title', 'like', '%' . $this->query . '%')
                ->limit(10)
                ->get()
                ->toArray();
        } else {
            $this->results = [];
        }
    }
    public function redirectToProduct($expert_guide_article_id)
    {
        $lang_id = getCurrentLanguageID();

        $article = ExpertGuideArticle::where('id', $expert_guide_article_id)
            ->with([
                'articleTranslations' => function ($query) use ($lang_id) {
                    $query->where('lang_id', $lang_id);
                },
                'category.translations' => function ($query) use ($lang_id) {
                    $query->where('lang_id', $lang_id);
                }
            ])->first();

        // Handle if article or translation doesn't exist
        if (!$article || $article->articleTranslations->isEmpty() || !$article->category || $article->category->translations->isEmpty()) {
            abort(404, 'Article or category not found in the current language.');
        }

        $cat_slug = $article->category->translations->first()->slug;
        $art_slug = $article->articleTranslations->first()->slug;

        return redirect()->route('expert-guide-article', [
            'locale' => app()->getLocale(),
            'cat_slug' => $cat_slug,
            'art_slug' => $art_slug
        ]);
    }

    public function render()
    {
        return view('livewire.hlep-center-search');
    }
}
