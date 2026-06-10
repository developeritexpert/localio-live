<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Article;
use App\Models\ArticleCategory;
use Illuminate\Support\Str;
use App\Models\ArticleCategoryTranslation;
use App\Models\ArticleTranslation;
use App\Models\Language;

class ArticleController extends Controller
{
    public function index()
    {
        // $articles = Article::all();
        $locale = getCurrentLocale();

        $lang_code = Language::where('lang_code', $locale)->first();

        $articles = Article::with(['articleCategory', 'translations' => function ($query) use ($lang_code) {
                                                $query->where('language_id', $lang_code->id);
                                            }]) ->get();
        return view('Admin.article.index',compact('articles'));
    }

    public function articleEdit($id)
    {
        $article = Article::with('articleCategory')->findOrFail($id);
        $categories = ArticleCategory::all();
        $locale = getCurrentLocale();
        $lang_code = Language::where('lang_code', $locale)->first();
        if (!$article) {
            return redirect()->back()->with('error', 'Article not found');
        }
        if ($lang_code) {
            $articleTranslation = ArticleTranslation::with('language')->where('article_id',$id)->where('language_id',$lang_code->id)->first();
        } else {
            $articleTranslation = Article::where('id',$id)->first();
        }
        return view('Admin.article.article_update', compact('article', 'categories','articleTranslation'));
    }
    public function add(Request $request)
    {
        $articleCategory = ArticleCategory::all();

        return view('Admin.article.add_article',compact('articleCategory'));
    }
    // add new article function
    public function addProcc(Request $request)
    {
        // Validation rules
        $rules = [
            'name' => 'required|unique:articles,name',
            'description' => 'required',
            'image' => 'required|image|mimes:png,jpg,svg',
            'category_id'   => 'required',
        ];

        // Validate the incoming request
        $validated = $request->validate($rules, [
            'image.required' => 'The image is required.',
            'image.image' => 'The file must be an image.',
            'image.mimes' => 'The image must be a file of type:png, jpg, svg.',
        ]);

        // create a new article data
        $article  = new Article;
        $article->name = $request->name;
        $article->slug = Str::slug($request->name);
        $article->description = $request->description ?? '';
        $article->category_id  = $request->category_id;


        // Handle the image upload
        if ($request->hasFile('image')) {
            $articleImage = $request->file('image');
            $extension = $article->slug . '-'. $articleImage->getClientOriginalExtension();

            // Store the image in the public storage folder
            $articleImage->move(public_path().'/ArticleImages/',$extension);
            $article->image = $extension;
        }
        // Save the article and return response
        try {
            if ($article->save()) {
                return redirect()->back()->with('success', 'Article saved successfully');
            } else {
                return redirect()->back()->with('error', 'Failed to save Article');
            }
        } catch (\Exception $e) {
            // Log the exception error for debugging purposes
            return redirect()->back()->with('error', 'An error occurred while saving the article.');
        }
    }

    // Article Update Function

    public function articleUpdateProcc(Request $request)
    {

        $lang_code = getCurrentLocale();

        $lang_code = Language::where('lang_code', $lang_code)->value('id');

        // $lang_code = Language::where('lang_code', $request->lang_code)->first();

        if ($lang_code) {
            $request->validate([
                'name' => 'required' ,
                'description' => 'required',
            ]);
            if ($request->article_tr_id) {
                $article = ArticleTranslation::find($request->article_tr_id);

                if (!$article) {
                    return back()->withErrors(['message' => 'Article translation not found.']);
                }
            }else{
                    $existingTranslation = ArticleTranslation::where('id', $request->article_tr_id)
                    ->where('language_id', $lang_code)
                    ->first();

                if ($existingTranslation) {
                    $article = $existingTranslation;
                } else {
                    $article = new ArticleTranslation;
                    $article->article_id = $request->article_id;
                    $article->language_id = $lang_code;

                }
            }
        }
        else {
            $request->validate([
                'name' => 'required' ,
                'description' => 'required',
            ]);
            $article = Article::find($request->article_id);

            if ($request->hasFile('image')) {
                $featuredImage = $request->file('image');
                $extension = $featuredImage->getClientOriginalExtension();
                if (!in_array(strtolower($extension), ['png', 'jpg', 'svg'])) {
                    return redirect()->back()->with('error', 'Only PNG, JPG, and SVG images are allowed.');
                }

                $featuredImageName = Str::slug($request->name) . rand(0, 1000) . time() . '.' . $extension;
                $featuredImage->move(public_path('/ArticleImages/'), $featuredImageName);
                $article->image = $featuredImageName;
            }
            // $article->category_id = $request->category_id ?? null;
        }
        $article->name = $request->name ?? '';

        $article->slug = Str::slug($request->name) ?? '';

        $article->description = $request->description ?? '';

        $article->save();
        return redirect()->back()->with('success', 'Article updated successfully.');
    }

    // End Article Update function

    // Article remeove Functioon

    public function articleRemove($id)
    {
        $article = Article::find($id)->first();
        if(!$article)
        {
            return redirect()->back()->with('error','article not found');
        }
        $article->delete();
        return redirect()->back()->with('success','article delete successfully');
    }

    // End article Remove function

    public function articleCategory()
    {
        $locale = getCurrentLocale();

        $lang_code = Language::where('lang_code', $locale)->first();

        $articleCategory = ArticleCategory::with(['translations' => function ($query) use ($lang_code) {
                                                $query->where('language_id', $lang_code->id);
                                            }])->get();
        return view('Admin.article.article_categories',compact('articleCategory'));


    }
    public function articleCategoryAdd()
    {
        return view('Admin.article.add_article_category');
    }
    public function articleCategoryAddProcc(Request $request)
    {
        // Validation rules
        $rules = [
            'name' => 'required|unique:article_categories,name',
            'description' => 'required', // No unique rule needed for description
            'image' => 'required|image|mimes:png,jpg,svg',
        ];

        // Validate the incoming request
        $validated = $request->validate($rules, [
            'image.required' => 'The image is required.',
            'image.image' => 'The file must be an image.',
            'image.mimes' => 'The image must be a file of type: jpeg, png, jpg, svg, webp.',
        ]);

        // create a new articleCategory data

        $articleCategory  = new ArticleCategory;
        $articleCategory->name = $request->name;
        $articleCategory->slug = Str::slug($request->name);
        $articleCategory->description = $request->description ?? '';

        // Handle the image upload
        if ($request->hasFile('image')) {
            $articleCategoryImage = $request->file('image');
            $imageName = $articleCategory->slug . '-' . rand(0, 1000) . time() . '.' . $articleCategoryImage->getClientOriginalExtension();

            // Store the image in the public storage folder
            $articleCategoryImage->move(public_path().'/ArticleCategoryImages/',$imageName);
            $articleCategory->image = $imageName;
        }

        // Save the articleCategory and return response
        try {
            if ($articleCategory->save()) {
                return redirect()->back()->with('success', 'Article Category saved successfully');
            } else {
                return redirect()->back()->with('error', 'Failed to save Article Category');
            }
        } catch (\Exception $e) {
            // Log the exception error for debugging purposes
            return redirect()->back()->with('error', 'An error occurred while saving the Article Category.');
        }
    }
    public function articleCategoryEdit($id)
    {
        $locale = getCurrentLocale();
        $siteLanguage = Language::where('lang_code', $locale)->first();
        $articleCategory = ArticleCategory::find($id);

        if (!$articleCategory) {
            return redirect()->back()->with('error', 'Article Category not found');
        }
        if ($siteLanguage) {
            $articleTranslationCategory = ArticleCategoryTranslation::with('language')->where('article_category_id',$id)->where('language_id',$siteLanguage->id)->first();
        } else {
            $articleTranslationCategory = ArticleCategory::where('id',$id)->first();
        }
        return view('Admin.article.article_category_update', compact('articleCategory', 'articleTranslationCategory'));
    }

    public function articleCategoryUpdate(Request $request)
    {
        // dd($request->all());
        $lang_code = getCurrentLocale();

        $siteLanguage = Language::where('lang_code', $lang_code)->value('id');
        if ($siteLanguage) {
            $request->validate([
                'name' => 'required' ,
                'description' => 'required',
            ]);
            if ($request->article_tr_id) {
                $category = ArticleCategoryTranslation::find($request->article_tr_id);
                if (!$category) {
                    return back()->withErrors(['message' => 'Category translation not found.']);
                }
            }else{
                    $existingTranslation = ArticleCategoryTranslation::where('id', $request->article_tr_id)
                    ->where('language_id', $siteLanguage)
                    ->first();
                if ($existingTranslation) {
                    $category = $existingTranslation;
                } else {
                    $category = new ArticleCategoryTranslation;
                    $category->article_category_id = $request->article_ct_id;
                    $category->language_id = $siteLanguage;
                }
            }
        }
        else {
            $request->validate([
                'name' => 'required' ,
                'description' => 'required',
            ]);
            $category = ArticleCategory::find($request->article_ct_id) ?? new ArticleCategory;
            if ($request->hasFile('image')) {
                $featuredImage = $request->file('image');
                $extension = $featuredImage->getClientOriginalExtension();
                if (!in_array(strtolower($extension), ['png', 'jpg', 'svg'])) {
                    return redirect()->back()->with('error', 'Only PNG, JPG, and SVG images are allowed.');
                }
                $featuredImageName = Str::slug($request->name) . rand(0, 1000) . time() . '.' . $extension;
                $featuredImage->move(public_path('/ArticleCategoryImages/'), $featuredImageName);
                $category->image = $featuredImageName;
            }
        }
        $category->name = $request->name ?? '';
        $category->slug = Str::slug($request->name) ?? '';
        $category->description = $request->description ?? '';
        $category->save();
        return redirect()->back()->with('success', 'Article updated successfully.');
    }

    public function articleCategoryRemove($id)
    {
        $articleCategoryRemove = ArticleCategory::find($id);
        if(!$articleCategoryRemove)
        {
            return redirect()->back()->with('error','article category not found');
        }
        $articleCategoryRemove->delete();
        return redirect()->back()->with('success','remove article category successfull');
    }

}
