<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExpertGuideCategory;
use App\Models\ExpertGuideCategoryTranslation;

use App\Models\ExpertGuideArticle;
use App\Models\ExpertGuideArticleTranslation;


use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Services\MediaService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ExpertGuideController extends Controller
{

        protected $mediaService;

    public function __construct(MediaService $mediaService)
    {
        $this->mediaService = $mediaService;
    }



    public function allCategory(){
        $lang_id = getCurrentLanguageID();

        $categories = ExpertGuideCategory::with(['expertGuideCategoryTranslation' => function ($query) use ($lang_id) {
                $query->where('lang_id', $lang_id);
            }])
            ->whereHas('expertGuideCategoryTranslation', function ($query) use ($lang_id) {
                $query->where('lang_id', $lang_id);
            })
            ->get();
        //  dd($categories);

        return view('Admin.expert_guide.category', compact('categories'));



    }
    public function addCategory($id = null){
        $category = null;
        if ($id) {
            $category = ExpertGuideCategory::with(['expertGuideCategoryTranslation'])->find($id);
            //    dd($category);
        }


        return view('Admin.expert_guide.add_category',compact('category'));
    }

    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('expert_guide_category_translations', 'name')
                    ->where('lang_id', session('lang_id', 1))
                    ->ignore($request->category_id, 'expert_guide_category_id'),
            ],
            'description' => 'required|string',
            'image' => $request->category_id ? 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048' : 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $lang_id = session('lang_id', 1);

        DB::beginTransaction();

        try {
            $category_id = $request->category_id;
            $category = null;

            if (!empty($category_id)) {
                $category = ExpertGuideCategory::find($category_id);

                if (!$category) {
                    return redirect()->back()->with('error', 'Category not found.');
                }

                // Handle image update
                if ($request->hasFile('image')) {
                    if (!empty($category->image) && file_exists(public_path($category->image))) {
                        unlink(public_path($category->image));
                    }

                    // $media = $this->mediaService->uploadMedia($request->file('image'), 'expert_guide_categories');
                    $media = $this->mediaService->uploadMedia($request->file('image'), 'expertGuide/categories');
                    $category->image = $media->dir_path . '/' . $media->file_name;
                }

                $category->save();

                // Generate unique slug
                $slug = Str::slug($request->name);
                $originalSlug = $slug;
                $count = 1;

                while (
                    ExpertGuideCategoryTranslation::where('slug', $slug)
                        ->where('lang_id', $lang_id)
                        ->where('expert_guide_category_id', '!=', $category->id)
                        ->exists()
                ) {
                    $slug = $originalSlug . '-' . $count++;
                }

                // Update or create translation
                $category->expertGuideCategoryTranslation()->updateOrCreate(
                    ['lang_id' => $lang_id],
                    [
                        'name' => $request->name,
                        'slug' => $slug,
                        'description' => $request->description,
                        'status' => 1,
                    ]
                );
            } else {
                // Create new category
                $category = new ExpertGuideCategory;
                $category->status = 1;

                if ($request->hasFile('image')) {
                    $media = $this->mediaService->uploadMedia($request->file('image'), 'expertGuide/categories');
                    // $media = $this->mediaService->uploadMedia($request->file('image'), 'expert_guide_categories');
                    $category->image = $media->dir_path . '/' . $media->file_name;
                }

                $category->save();

                // Generate unique slug
                $slug = Str::slug($request->name);
                $originalSlug = $slug;
                $count = 1;

                while (
                    ExpertGuideCategoryTranslation::where('slug', $slug)
                        ->where('lang_id', $lang_id)
                        ->exists()
                ) {
                    $slug = $originalSlug . '-' . $count++;
                }

                $category->expertGuideCategoryTranslation()->create([
                    'lang_id' => $lang_id,
                    'name' => $request->name,
                    'slug' => $slug,
                    'description' => $request->description,
                    'status' => 1,
                ]);
            }

            DB::commit();

            return redirect()->route('admin-expert-guide-category')
                ->with('success', 'Expert Guide Category saved successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    public function deleteCategory($id)
    {
        DB::beginTransaction();

        try {
            $category = ExpertGuideCategory::findOrFail($id);

            // Delete related translations
            $category->expertGuideCategoryTranslation()->delete();

            // Delete image if exists
            if (!empty($category->image) && file_exists(public_path($category->image))) {
                unlink(public_path($category->image));
            }

            // Delete category
            $category->delete();

            DB::commit();

            return redirect()->back()->with('success', 'Category deleted successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to delete category: ' . $e->getMessage());
        }
    }





    public function allArticle(){

        $lang_id = getCurrentLanguageID();

        $articles = ExpertGuideArticle::with([
            'articleTranslations' => fn($q) => $q->where('lang_id', $lang_id),
            'category.expertGuideCategoryTranslation' => fn($q) => $q->where('lang_id', $lang_id),
        ])
        ->whereHas('articleTranslations', fn($q) => $q->where('lang_id', $lang_id))
        ->get();


            // dd($articles);
        return view('Admin.expert_guide.article',compact('articles'));
    }

    public function addArticle($id = null){
        $lang_id = getCurrentLanguageID();

        $categories = ExpertGuideCategory::with(['expertGuideCategoryTranslation' => function ($query) use ($lang_id) {
                $query->where('lang_id', $lang_id);
            }])
            ->whereHas('expertGuideCategoryTranslation', function ($query) use ($lang_id) {
                $query->where('lang_id', $lang_id);
            })
            ->get();

            $article = null;
            if ($id) {
                $article = ExpertGuideArticle::with(['articleTranslations'=>function ($query) use ($lang_id){
                    $query->where('lang_id', $lang_id);
                }],'contents')->find($id);
                //    dd($article);
            }

            // dd($categories);
          return view('Admin.expert_guide.add_article', compact('categories','article'));
    }
    public function storeArticle(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:expert_guide_categories,id',
            'description' => 'required|string',
            'contents' => 'required|array',
            'preview_title' => 'required|string|max:255',
            'preview_description' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ]);

        $lang_id = getCurrentLanguageID();

        DB::beginTransaction();

        try {
            $article_id = $request->article_id;
            $article = null;

            if (!empty($article_id)) {
                $article = ExpertGuideArticle::find($article_id);

                if (!$article) {
                    return redirect()->back()->with('error', 'Article not found.');
                }

                // Handle image update
                if ($request->hasFile('image')) {
                    if (!empty($article->image) && file_exists(public_path($article->image))) {
                        unlink(public_path($article->image));
                    }

                    $media = $this->mediaService->uploadMedia($request->file('image'), 'expertGuide/articles');
                    // $media = $this->mediaService->uploadMedia($request->file('image'), 'expert_guide_articles');
                    $article->image = $media->dir_path . '/' . $media->file_name;
                }

                $article->category_id = $request->category_id;
                $article->save();
            } else {
                // New article
                $article = new ExpertGuideArticle;
                $article->category_id = $request->category_id;
                $article->status = 1;

                if ($request->hasFile('image')) {
                    $media = $this->mediaService->uploadMedia($request->file('image'), 'expertGuide/articles');
                    // $media = $this->mediaService->uploadMedia($request->file('image'), 'expert_guide_articles');
                    $article->image = $media->dir_path . '/' . $media->file_name;
                }

                $article->save();
            }

            // Generate unique slug
            $slug = Str::slug($request->title);
            $originalSlug = $slug;
            $count = 1;

            while (
                ExpertGuideArticleTranslation::where('slug', $slug)
                    ->where('lang_id', $lang_id)
                    ->where('expert_guide_article_id', '!=', $article->id)
                    ->exists()
            ) {
                $slug = $originalSlug . '-' . $count++;
            }

            // Update or create translation
            $article->articleTranslations()->updateOrCreate(
                ['lang_id' => $lang_id],
                [
                    'title' => $request->title,
                    'slug' => $slug,
                    'description' => $request->description,
                    'preview_title' => $request->preview_title,
                    'preview_description' => $request->preview_description,
                    'status' => 1,
                ]
            );

            // Delete old contents for this language (optional: soft delete if needed)
            $article->contents()->where('lang_id', $lang_id)->delete();

            // Save new contents
            foreach ($request->contents as $index => $section) {
                if (!empty($section['section_title']) || !empty($section['section_content'])) {
                    $article->contents()->create([
                        'lang_id' => $lang_id,
                        'section_title' => $section['section_title'] ?? '',
                        'section_content' => $section['section_content'] ?? '',
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('admin-expert-guide-article')
                ->with('success', 'Expert Guide Article saved successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    public function deleteArticle($id)
    {
        DB::beginTransaction();

        try {
            $article = ExpertGuideArticle::findOrFail($id);

            // Delete related translations
            $article->articleTranslations()->delete();

            // Delete related section contents
            $article->contents()->delete();

            // Delete image if exists
            if (!empty($article->image) && file_exists(public_path($article->image))) {
                unlink(public_path($article->image));
            }

            // Delete the main article
            $article->delete();

            DB::commit();

            return redirect()->back()->with('success', 'Article deleted successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to delete article: ' . $e->getMessage());
        }
    }

}
