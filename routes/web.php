<?php

use App\Http\Controllers\Admin\SiteContent\SiteContentController;
use App\Http\Controllers\Admin\SiteContent\StaticContentController;
use App\Http\Controllers\Admin\{AiConfigurationController,AiPromptController,MailTemplateController,AdminBusinessController,AdminDashController,FeatureController, CategoriesController, SiteLanguagesController, FilterController, ArticleController, SitePagesController, AdminProductController, AdminSettingsController, DBrefreshController, ExpertGuideController, HomeContentController, ProductFetureController, ReviewController, UserManegementController, usinessController};

use App\Http\Controllers\AdminDealController;
use App\Http\Controllers\Auth\AuthenticationController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\User\MetaPages\MetaPagesController;

use App\Http\Controllers\User\{ViewController, CategoryController, ProductController, UserController, TermAndConditionController};
use App\Http\Controllers\Vendor\HomeController;
use App\Http\Controllers\PostbackController;
use App\Http\Controllers\UserDashboard\{UserDashboardController};
use App\Models\ExpertGuides;
use App\Models\ProductFeature;
use App\Models\Rule;
use Illuminate\Support\Facades\Route;
// use Illuminate\Support\Facades\Redis;

// Route::get('tttttt',function(){

//     dd(getLanguages());
// });

Route::get('auth/google', [AuthenticationController::class, 'redirectToGoogle'])->name('google.login');
Route::get('login/google/callback', [AuthenticationController::class, 'handleGoogleCallback']);
Route::get('/category/{id}', [ViewController::class, 'categoryShow'])->name('category-show');

Route::get('login/facebook', [AuthenticationController::class, 'redirectToFacebook'])->name('login.facebook');
Route::get('login/facebook/callback', [AuthenticationController::class, 'handleFacebookCallback']);

// Switch Language Route
Route::get('/switch-language/{lang_code}', [ViewController::class, 'changeLanguage'])->name('switch-language');

// Route for search results page


Route::get('/logout', [AuthenticationController::class, 'logout'])->name('logout');
Route::post('loginprocc', [AuthenticationController::class, 'loginProcc'])->name('login_process');

// --------------- ADMIN ROUTES ----------------------
Route::group(['middleware' => ['auth', 'admin']], function () {
    Route::get('/admin-dashboard', [AdminDashController::class, 'index'])->name('admin_dashboard');
    Route::get('admin-dashboard/setting', [AdminDashController::class, 'profile']);
    Route::post('admin-dashboard/update-profile-procc', [AdminDashController::class, 'ProfileUpdateProcc']);
    Route::post('admin-dashboard/change-password-procc', [AdminDashController::class, 'updatePasswordProcc']);
    Route::get('admin-dashboard/who-we-are', [AdminDashController::class, 'whoWeAreContent'])->name('who_we_are_content');
    Route::post('admin-dashboard/who-we-are', [AdminDashController::class, 'updateWhoWeAre'])->name('admin.who_we_are.update');
    Route::get('admin/page-tile-translation/{id}', [AdminDashController::class, 'deletePageTileTranslation'])->name('admin.page_tile_translation.delete');
    Route::post('/admin/page-tile-translation/update', [AdminDashController::class, 'MPSsectionUpdate'])->name('admin.page_tile_translation.update');
    Route::post('/admin/page-tile-specialist-translation/update', [AdminDashController::class, 'SpecialistUpdate'])->name('admin.page_tile_specialist_translation.update');

    // Exclusive Deals Routes
    Route::get('/exclusive-deals',[AdminDealController::class,'index'])->name('exclusive-deals.index');
    Route::get('/admin-dashboard/exclusive-deals/create',[AdminDealController::class,'create'])->name('exclusive-deals.create');
    Route::get('/admin-dashboard/exclusive-deals/{id}/edit',[AdminDealController::class,'edit'])->name('exclusive-deals.edit');

    Route::get('/admin/page-expert-guide/update', [AdminDashController::class, 'ExpertGuide'])->name('admin.page-expert-guide.update');
    Route::post('/admin/expert-guide/update', [AdminDashController::class, 'ExperGuideUpdate'])->name('expertGuide.update');
    Route::post('/admin/page-education-translation/update', [AdminDashController::class, 'ESsectionUpdate'])->name('admin.page_education_translation.update');
    Route::post('/admin/page-right-tool-translation/update', [AdminDashController::class, 'RTsectionUpdate'])->name('admin.page_Right_tool_translation.update');
    Route::get('/admin/page-contact/update', [AdminDashController::class, 'Contact'])->name('admin.page-contact.update');
    Route::post('/admin/page-contact-content/update', [AdminDashController::class, 'ContactUpdate'])->name('admin.page-contact-content.update');
    Route::post('/admin/page-right-tool/update', [AdminDashController::class, 'RightToolItemUpdate'])->name('admin.page_Right_tool.update');

    Route::get('admin-dashboard/get-listed-page', [AdminDashController::class, 'getListedContent'])->name('admin-get-listed-content');
    Route::post('admin-dashboard/get-listed', [AdminDashController::class, 'updateGetListed'])->name('admin.get-listed-update');
    Route::post('/admin/page-get-listed-translation/update', [AdminDashController::class, 'ESsectionUpdate'])->name('admin.page_get_listed_translation.update');
    //  CategoriesController  categories
    Route::get('/admin-dashboard/categories', [CategoriesController::class, 'index'])->name('categories');
    Route::get('/admin-dashboard/categories/add/{id?}', [CategoriesController::class, 'add'])->name('add-category');
    Route::post('/admin-dashboard/categories/add-process', [CategoriesController::class, 'add_process'])->name('add-category-process');
    Route::get('/admin-dashboard/remove-category/{id}', [CategoriesController::class, 'remove'])->name('admin-remove-categories');


    Route::get('/admin-dashboard/categories/add/topic/{id?}', [CategoriesController::class, 'addTopic'])->name('add-topic-category');
    Route::post('/admin-dashboard/categories/add-category-topic-process', [CategoriesController::class, 'storeTopic'])->name('store-topic-category');
    Route::post('/admin-dashboard/categories/topics/update', [CategoriesController::class, 'updateTopic'])->name('update-topic-category');
    Route::get('/admin-dashboard/categories/topics/delete', [CategoriesController::class, 'deleteTopic'])->name('delete-topic-category');
    // add language change Route
    Route::get('/admin-dashboard/categories/set-category-language/{lang_id}', [CategoriesController::class, 'setLanguage'])
    ->name('set-category-language');



    // Business Controller
    // old
    // Route::get('admin-dashboard/businesses', [AdminBusinessController::class, 'business'])->name('business');

    Route::get('admin-dashboard/businesses/listing', [AdminBusinessController::class, 'BusinessListingLivewire'])->name('business.listing.livewire');

    Route::get('admin-dashboard/businesses/edit/{id?}', [AdminBusinessController::class, 'EditBusiness'])->name('edit.business');

    Route::get('admin-dashboard/businesses/delete/{id}', [AdminBusinessController::class, 'DeleteBusiness'])->name('delete.business');

    // Business Intergration Route
    Route::get('admin-dashboard/businesses/integration/{business}', [AdminBusinessController::class, 'editIntegration'])
    ->name('admin.integration.edit');
    Route::post('/admin/integration/update/{businessId}', [AdminBusinessController::class, 'updateIntegration'])
    ->name('admin.integration.save');


    // Route::get('admin-dashboard/businesses/edit/{business}', function ($businessId) {
    //     return view('Admin.business.index', [
    //         'businessId' => $businessId,
    //         'pageMode' => 'edit'
    //     ]);
    // })->name('admin.business.edit');


    // Show FAQ section for a business
        Route::get('admin-dashboard/businesses/faq/{business}', function ($businessId) {
            return view('Admin.business.index', [
                'faqBusinessId' => $businessId,
                'pageMode' => 'faq'
            ]);
        })->name('admin.business.faq');




    Route::get('admin-dashboard/businesses/pricing-options', [AdminBusinessController::class, 'priceoptions'])->name('priceoptions');
    Route::get('admin-dashboard/businesses/pricing-options/add/{id?}', [AdminBusinessController::class, 'priceoptionsAdd'])->name('priceoptionsAdd');
    Route::post('admin-dashboard/businesses/pricing-options/addprocess', [AdminBusinessController::class, 'priceoptionsAddprocess'])->name('priceoptionsAddprocess');
    Route::get('admin-dashboard/businesses/pricing-options/remove/{id}', [AdminBusinessController::class, 'priceoptionsremove'])->name('priceoptionsremove');
    // Offer Option translation route
    Route::post('admin-dashboard/businesses/pricing-options/translate', [AdminBusinessController::class, 'saveOfferTranslation'])->name('admin.save-offer-translation');

    // SiteLanguagesController

    Route::get('/admin-dashboard/site-languages', [SiteLanguagesController::class, 'index'])->name('site-languages');
    Route::get('/admin-dashboard/site-languages/add', [SiteLanguagesController::class, 'add'])->name('site-languages-add');
    Route::post('/admin-dashboard/site-languages/addProcc', [SiteLanguagesController::class, 'addProcc'])->name('site-languages-addProcc');
    Route::get('/admin-dashboard/site-language/update/{id}', [SiteLanguagesController::class, 'update'])->name('site-language-update');
    Route::post('/admin-dashboard/site-language/updateProcc', [SiteLanguagesController::class, 'updateProcc'])->name('site-language-updateProcc');
    Route::get('/admin-dashboard/remove-site-language/{id}', [SiteLanguagesController::class, 'remove'])->name('site-language-remove');
    Route::get('/admin-dashboard/remove-site-language/{id}', [SiteLanguagesController::class, 'toggleStatus'])->name('site-language-toggle-status');

    // Countrycontroller

    Route::get('/admin-dashboard/country', [CountryController::class, 'index'])->name('country.index');
    Route::get('/admin-dashboard/country/update/{id}', [CountryController::class, 'update'])->name('country.update');
    Route::post('/admin-dashboard/country/updateProcc', [CountryController::class, 'updateProcc'])->name('country.updateProcc');
    Route::get('/admin-dashboard/country/add', [CountryController::class, 'add'])->name('country.add');
    Route::post('/admin-dashboard/country/addProcc', [CountryController::class, 'addProcc'])->name('country.addProcc');
    Route::get('/admin-dashboard/country/delete/{id}', [CountryController::class, 'delete'])->name('country.delete');



    // Email Section
    Route::get('/admin-dashboard/mail-templates', [MailTemplateController::class, 'index'])
        ->name('mail-templates.index');

    Route::get('/admin-dashboard/mail-templates/create', [MailTemplateController::class, 'create'])
        ->name('mail-templates.create');

    Route::post('/admin-dashboard/mail-templates/store', [MailTemplateController::class, 'store'])
        ->name('mail-templates.store');

    Route::get('/admin-dashboard/mail-templates/show/{mailTemplate}', [MailTemplateController::class, 'show'])
        ->name('mail-templates.show');

    Route::get('/admin-dashboard/mail-templates/{mailTemplate}/edit', [MailTemplateController::class, 'edit'])
        ->name('mail-templates.edit');

    Route::put('/admin-dashboard/mail-templates/update/{mailTemplate}', [MailTemplateController::class, 'update'])
        ->name('mail-templates.update');

    Route::patch('/admin-dashboard/mail-templates/{mailTemplate}', [MailTemplateController::class, 'update'])
        ->name('mail-templates.patch');

    Route::get('/admin-dashboard/mail-templates/delete/{mailTemplate}', [MailTemplateController::class, 'destroy'])
        ->name('mail-templates.destroy');

    // AI Module
    Route::resource('/admin-dashboard/ai-prompts', AiPromptController::class);
    Route::get('/admin-dashboard/ai-prompt/type/business-prompts',[AiPromptController::class ,'businessPrompt'])->name('ai-prompt.business-prompt');

    //AI Configuration
    Route::get('/admin-dashboard/ai-configurations', [AiConfigurationController::class, 'index'])->name('ai-configurations.index');
    Route::get('/admin-dashboard/ai-configurations/create', [AiConfigurationController::class, 'create'])->name('ai-configurations.create');
    Route::post('/admin-dashboard/ai-configurations', [AiConfigurationController::class, 'store'])->name('ai-configurations.store');
    Route::delete('/admin-dashboard/ai-configurations/{aiConfiguration}', [AiConfigurationController::class, 'destroy'])->name('ai-configurations.destroy');


    // Additional custom routes
    Route::get('/admin-dashboard/mail-templates/{mailTemplate}/translation', [MailTemplateController::class, 'getTranslation'])
        ->name('mail-templates.translation');

    Route::post('/admin-dashboard/mail-templates/{mailTemplate}/preview', [MailTemplateController::class, 'preview'])
        ->name('mail-templates.preview');

    // DB-Refresh
    Route::get('/admin-dashboard/db-refresh', [AdminSettingsController::class, 'index'])->name('dbrefresh.index');
    Route::post('/admin-dashboard/db-refresh', [AdminSettingsController::class, 'refersh_database'])->name('dbrefresh.refresh');

    // FilterController :
    Route::get('/admin-dashboard/filters', [FilterController::class, 'index'])->name('filters');
    Route::get('/admin-dashboard/filter/add/{id}', [FilterController::class, 'add'])->name('filter-add');
    Route::post('/admin-dashboard/filter/addProcc', [FilterController::class, 'addProcc'])->name('filter-addProcc');
    Route::get('/admin-dashboard/filters/{id}', [FilterController::class, 'categoryfilters'])->name('categoryfilters');
    Route::get('/admin-dashboard/update-filter/{filterId}', [FilterController::class, 'updateFilter'])->name('update-filter');
    Route::post('/admin-dashboard/update-filter/updateProcc', [FilterController::class, 'updateProcc'])->name('update-filter-updateProcc');
    Route::get('/admin-dashboard/remove-filter/{id}', [FilterController::class, 'remove']);
    Route::get('admin-dashboard/filter/fetch-filters/{id}', [FilterController::class, 'getFilters'])->name('filter.getFilters');
    Route::post('admin-dashboard/filter/preview', [FilterController::class, 'preview'])->name('filter.preview');
    Route::post('admin-dashboard/filter/fetch-filters', [FilterController::class, 'fetchFilters'])->name('filter.fetch');
    Route::post('/admin-dashboard/update-filter-order', [FilterController::class, 'updateFilterOrder'])->name('update-filter-order');
    // Features Controller :
    Route::get('/admin-dashboard/features', [FeatureController::class, 'index'])->name('features');
    Route::get('/features/add', [FeatureController::class, 'create'])->name('features.create');
    Route::post('/features/store', [FeatureController::class, 'store'])->name('features.store');

    Route::get('/features/edit/{id}', [FeatureController::class, 'edit'])->name('features.edit');
    Route::post('/features/update/{id}', [FeatureController::class, 'update'])->name('features.update');

    Route::get('/features/delete/{id}', [FeatureController::class, 'destroy'])->name('features.delete');


    // ArticleController
    Route::get('/admin-dashboard/article', [ArticleController::class, 'index'])->name('article');
    Route::get('/admin-dashboard/article/add', [ArticleController::class, 'add'])->name('article-add');
    Route::post('/admin-dashboard/article/addProcc', [ArticleController::class, 'addProcc'])->name('article-addProcc');
    Route::get('/admin-dashboard/article-edit/{id}', [ArticleController::class, 'articleEdit'])->name('article-edit');
    Route::post('/admin-dashboard/article/update', [ArticleController::class, 'articleUpdateProcc'])->name('article-update');
    Route::get('/admin-dashboard/article-remove/{id?}', [ArticleController::class, 'articleRemove'])->name('article-remove');

    // Article Category Route

    Route::get('/admin-dashboard/article-category', [ArticleController::class, 'articleCategory'])->name('article-category');

    Route::get('/admin-dashboard/article/category/add', [ArticleController::class, 'articleCategoryAdd'])->name('article-category-add');

    Route::post('/admin-dashboard/article/category/addProcc', [ArticleController::class, 'articleCategoryAddProcc'])->name('article-category-addProcc');
    Route::get('/admin-dashboard/edit-article-category/{id}', [ArticleController::class, 'articleCategoryEdit'])->name('article-category-edit');
    Route::post('/admin-dashboard/article/category/update', [ArticleController::class, 'articleCategoryUpdate'])->name('article-category-update');

    Route::get('/admin-dashboard/remove-article-category/{id?}', [ArticleController::class, 'articleCategoryRemove'])->name('article-category-remove');

    // policies Route
    Route::get('/admin-dashboard/policies', [SitePagesController::class, 'policies'])->name('admin.policies');
    Route::get('admin-dashboard/policy/add/{id?}', [SitePagesController::class, 'policiesAddShow'])->name('policies_add_show');
    Route::post('/admin-dashboard/policy/add-process', [SitePagesController::class, 'policiesadd'])->name('policy-add');
    Route::get('/admin-dashboard/policy-remove/{id?}', [SitePagesController::class, 'pulicyRemove'])->name('policy-remove');

    // terms Route
    Route::get('/admin-dashboard/terms', [SitePagesController::class, 'terms_show'])->name('terms');
    Route::get('admin-dashboard/terms/add/{id?}', [SitePagesController::class, 'termsAdd_show'])->name('terms_add_show');
    Route::post('/admin-dashboard/terms/add-process', [SitePagesController::class, 'terms_add_process'])->name('terms_add_process');
    Route::get('/admin-dashboard/terms-remove/{id?}', [SitePagesController::class, 'terms_remove'])->name('terms-remove');


    // vendor list page
    Route::get('/admin-dashboard/vendor-listed', [SitePagesController::class, 'vendorListed'])->name('admin.vendor-listed');
    Route::post('/admin-dashboard/vendor-listed-update', [SitePagesController::class, 'vendorListedUpdate'])->name('admin.vendor-listed.update');


    Route::get('/admin-dashboard/vendor-how-it-work', [SitePagesController::class, 'vendorWork'])->name('admin.vendor-how-it-work');
    Route::post('/admin-dashboard/vendor-how-it-work-update', [SitePagesController::class, 'vendorWorkUpdate'])->name('admin.how-it-works.update');


    // Rules Route
    Route::get('/admin-dashboard/rules', [SitePagesController::class, 'rules'])->name('rules');
    Route::get('/admin-dashboard/rule/add', [SitePagesController::class, 'ruleAdd'])->name('rule-add');
    Route::post('/admin-dashboard/rule-add-procc', [SitePagesController::class, 'ruleAddProcc'])->name('rule-add-procc');
    Route::get('/admin-dashboard/rule-edit/{id}', [SitePagesController::class, 'ruleEdit'])->name('rule-edit');
    Route::get('/admin-dashboard/rule-remove/{id}', [SitePagesController::class, 'ruleRemove'])->name('rule-remove');

    // FAQ's Route
    Route::get('/admin-dashboard/faqs', [SitePagesController::class, 'faqs'])->name('faqs');
    Route::post('/admin/faqs/reorder', [SitePagesController::class, 'reorderFaqs'])->name('faqs.reorder');

    //Faqs Update
    Route::post('/faqs/update-line', [SitePagesController::class, 'updateLine'])->name('faqs.line.update');



    Route::get('/admin-dashboard/faq-add', [SitePagesController::class, 'faqAdd'])->name('faq-add');
    Route::post('/admin-dashboard/faq-add-procc', [SitePagesController::class, 'faqAddProcc'])->name('faq-add-procc');
    Route::get('/admin-dashboard/faq-edit/{id}', [SitePagesController::class, 'faqEdit'])->name('faq-edit');
    Route::get('/admin-dashboard/faq-remove/{id}', [SitePagesController::class, 'faqRemove'])->name('remove-faq');

    Route::get('/admin-dashboard/faqs-categories',[SitePagesController::class,'category'])->name('faqs-category');
    Route::get('/admin-dashboard/faq-category-add', [SitePagesController::class, 'categoryAdd'])->name('category-add');
    Route::post('/admin-dashboard/faq-category-add-procc', [SitePagesController::class,'categoryaddprocc'])->name('category-add-procc');
    Route::get('/admin-dashboard/faq-category-edit/{id}',[SitePagesController::class,'faqcategoryEdit'])->name('faqcategoryEdit');
    Route::get('/admin-dashboard/faq-category-remove/{id}', [SitePagesController::class, 'faqcategoryRemove'])->name('faqcategoryRemove');

    //  Products Route
    Route::get('/admin-dashboard/products', [AdminProductController::class, 'products'])->name('products');
    Route::get('/admin-dashboard/product/add', [AdminProductController::class, 'productAdd'])->name('product-add');
    Route::post('/admin-dashboard/product-add-procc', [AdminProductController::class, 'productAddProccess'])->name('product-add-procc');
    Route::get('/admin-dashboard/product-edit/{id}', [AdminProductController::class, 'productEdit'])->name('product-edit');
    Route::post('/admin-dashboard/product-update-procc/{id}', [AdminProductController::class, 'productUpdateProccess'])->name('product-update-procc');
    Route::get('/admin-dashboard/remove-product/{id}', [AdminProductController::class, 'removeProduct'])->name('product-remove');
    Route::post('/delete-price/{id}', [AdminProductController::class, 'deletePrice']);
    Route::get('/get-business-category', [AdminProductController::class, 'getBusinessCategory'])->name('get.business.category');
    Route::get('/get-category-name', [AdminProductController::class, 'getCategoryName'])->name('get.category.name');
    Route::get('/fetch-filters', [AdminProductController::class, 'fetchFilters'])->name('fetch-filters');

    // product feture Route
    Route::get('/admin-dashboard/product-feature', [ProductFetureController::class, 'index'])->name('productfeature.index');
    Route::get('/admin-dashboard/product-feture-add', [ProductFetureController::class, 'add'])->name('productfeature.add');
    Route::post('/admin-dashboard/product-feture-add-process', [ProductFetureController::class, 'add_process'])->name('productfeature.add-process');
    Route::get('/admin-dashboard/product-feture-update-process/{id}', [ProductFetureController::class, 'update'])->name('productfeature.update');
    Route::post('/admin-dashboard/product-feture-update-process', [ProductFetureController::class, 'update_process'])->name('productfeature.update-process');
    Route::get('/admin-dashboard/product-feture-remove-process/{id}', [ProductFetureController::class, 'remove'])->name('productfeature.remove-process');



    // SiteContent Route
    //Find Content with Language
    Route::post('/admin-dashboard/content-with-language', [SiteContentController::class, 'contentByLanguage'])->name('admin.getContentByLanguage');


    // Home Page Route
    Route::get('/admin-dashboard/home-page', [SiteContentController::class, 'homeContent'])->name('home-content');
    Route::post('/admin-dashboard/home-page-update', [SiteContentController::class, 'homeContentUpdate'])->name('home-content-update');
    Route::post('/admin-dashboard/update-lang-file', [SiteContentController::class, 'updateLangFile'])->name('update-lang-file');

    // Help center page
    Route::get('/admin-dashboard/help-center', [AdminDashController::class, 'helpCenter'])->name('admin.page-help-center');
    Route::post('/admin-dashboard/help-center/update', [AdminDashController::class, 'HelpCenterUpdate'])->name('admin.home_page_category.update');
    Route::get('admin/help-center/category/delete/{id}', [AdminDashController::class, 'deleteHomeCenterCategory'])->name('admin.help_center.category.delete');
    Route::post('/admin/help-center/category/update', [AdminDashController::class, 'updateHelpCenterCategory'])->name('admin.help_center.category.update');
    // Header Page Route
    Route::get('/admin-dashboard/header-page', [SiteContentController::class, 'headerPage'])->name('header-page');
    Route::post('/admin-dashboard/header-page-update', [SiteContentController::class, 'headerContentUpdate'])->name('header-content-update');

    // Footer Page Route
    Route::get('/admin-dashboard/footer-page', [SiteContentController::class, 'footerPage'])->name('footer-page');
    Route::post('/admin-dashboard/footer-page-update', [SiteContentController::class, 'footerPageUpdate'])->name('footer-page-update');

    // site page Route
    Route::get('/admin-dashboard/site-content-page', [StaticContentController::class, 'index'])->name('site-page');
    Route::post('/text-content-update', [StaticContentController::class, 'update'])->name('admin.text-content.update');

    // learn more modal Route
    Route::get('/admin-dashboard/learn-more-modal', [StaticContentController::class, 'modalindex'])->name('learn-modal');
    Route::get('/admin-dashboard/learn-more-modal/create', [StaticContentController::class, 'modalcreate'])->name('learn-modal-create');
    Route::get('/admin-dashboard/learn-more-modal/edit/{id}', [StaticContentController::class, 'edit'])->name('edit');
    Route::post('/admin-dashboard/learn-more-modal/store-update', [StaticContentController::class, 'modalstoreOrUpdate'])->name('modalstoreOrUpdate');
    Route::get('/admin-dashboard/learn-more-modal/delete/{id}', [StaticContentController::class, 'modaldestroy'])->name('modaldestroy');

    // Top Product Page Content Route
    Route::get('/admin-dashboard/top-product-page', [SiteContentController::class, 'topProductPageContent'])->name('top-product-page-content');
    Route::post('/admin-dashboard/product-page-update', [SiteContentController::class, 'topProductPageUpdate'])->name('product-page-update');

    // Reviews Section Route
    Route::get('/admin-dashboard/reviews', [ReviewController::class, 'reviews'])->name('reviews');
    Route::get('/admin-dashboard/Unpublished/reviews', [ReviewController::class, 'unpublishedReviews'])->name('admin.unpublished.reviews');

    Route::get('/admin-dashboard/review/add', [ReviewController::class, 'reviewAdd'])->name('review-add');
    Route::post('/admin-dashboard/review-add-procc', [ReviewController::class, 'reviewAddProc'])->name('review-add-procc');
    Route::get('/admin-dashboard/review-status-update/{id}', [ReviewController::class, 'reviewStatusUpdate'])->name('review-status-update');
    Route::get('/admin-dashboard/review-status-edit/{id}', [ReviewController::class, 'reviewEdit'])->name('review-edit');
    // Add Translation Route
    //Route::get('/admin-dashboard/review-status-translation/{id}', [ReviewController::class, 'reviewTranslation'])->name('review-translation');

    Route::post('/admin-dashboard/review-status-update/{id}', [ReviewController::class, 'reviewUpdate'])->name('review-update');
    Route::get('/admin-dashboard/review-delete/{id}', [ReviewController::class, 'reviewDelete'])->name('review-delete');
    Route::get('/admin-dashboard/configration/update', [AdminSettingsController::class, 'edit'])->name('admin-Default-edit');

    Route::post('/admin-dashboard/default/update', [AdminSettingsController::class, 'addWebSetting'])
        ->name('admin-setting-config-update');

    // User Management Route
    Route::get('/admin-dashboard/all-users', [UserManegementController::class, 'allUser'])->name('admin-all-user');
    Route::get('/admin-dashboard/user-status-update/{id}', [UserManegementController::class, 'statusupdate'])->name('admin-user-status-update');
    Route::get('/admin-dashboard/edit-users/{id?}', [UserManegementController::class, 'editUser'])->name('admin-edit-user');
    Route::get('/admin-dashboard/add-user', [UserManegementController::class, 'editUser'])->name('admin-add-user');
    Route::post('/admin-dashboard/update-user', [UserManegementController::class, 'updateUser'])->name('admin-update-user');
    Route::get('/admin-dashboard/delete-user/{id}', [UserManegementController::class, 'deleteUser'])->name('admin-delete-user');

    // Vendor registration request list Route
    Route::get('/admin-dashboard/vendor-registration-requests', [UserManegementController::class, 'allVendorRegisterRequest'])
    ->name('allVendorRegisterRequest');
    Route::get('/admin-dashboard/vendor-registration/{id}/{action}', [UserManegementController::class, 'handleRegisterRequest'])
    ->name('admin-vendor-registration.handle')
    ->where('action', 'approve|reject');


    Route::get('/admin-dashboard/all-vendors', [UserManegementController::class, 'allvendor'])->name('admin-all-vendors');
    Route::get('/admin-dashboard/edit-vendors/{id?}', [UserManegementController::class, 'editVendor'])->name('admin-edit-vendors');
    Route::get('/admin-dashboard/add-vendors', [UserManegementController::class, 'editVendor'])->name('admin-add-vendors');
    Route::post('/admin-dashboard/update-vendors', [UserManegementController::class, 'updateVendor'])->name('admin-update-vendors');
    Route::get('/admin-dashboard/delete-vendors/{id}', [UserManegementController::class, 'deleteVendor'])->name('admin-delete-vendors');


    // Expert Guide Category & Article
    Route::get('/admin-dashboard/expert-guide/category',[ExpertGuideController::class,'allCategory'])->name('admin-expert-guide-category');
    Route::get('/admin-dashboard/expert-guide/add-category/{id?}',[ExpertGuideController::class,'addCategory'])->name('admin-expert-guide-add-category');
    Route::post('/admin-dashboard/expert-guide/add-category/{id?}',[ExpertGuideController::class,'storeCategory'])->name('admin-expert-guide-store-category');
    // Route::post('/admin-dashboard/expert-guide/update-category/{id}',[ExpertGuideController::class,'updateCategory'])->name('admin-expert-guide-update-category');
    Route::get('/admin-dashboard/expert-guide/delete-category/{id}', [ExpertGuideController::class, 'deleteCategory'])->name('admin-expert-guide-delete-category');

    Route::get('/admin-dashboard/expert-guide/article',[ExpertGuideController::class,'allArticle'])->name('admin-expert-guide-article');
    Route::get('/admin-dashboard/expert-guide/add-article/{id?}',[ExpertGuideController::class,'addArticle'])->name('admin-expert-guide-add-article');
    Route::post('/admin-dashboard/expert-guide/add-article/{id?}',[ExpertGuideController::class,'storeArticle'])->name('admin-expert-guide-store-article');
    Route::get('/admin-dashboard/expert-guide/delete-article/{id}', [ExpertGuideController::class, 'deleteArticle'])->name('admin-expert-guide-delete-article');


    // Business category Translation Route
    Route::post('/admin/category/translation/save', [CategoryController::class, 'BusinessCategoryTranslationStore'])
    ->name('admin.save-category-translation');



    // ad tracking

    Route::get('/admin-dashboard/ad-tracking', [AdminSettingsController::class, 'trackingStats'])->name('admin-ad-tracking-status');


    // Site Content
    Route::get('/admin-dashboard/site-content',[StaticContentController::class,'allContent'])->name('admin-all-static-content');
    Route::post('/admin-dashboard/site-content/update', [StaticContentController::class, 'updateContent'])->name('admin-static-content-update');


    // vendor Change Request
    Route::get('/admin-dashboard/vendor-change-requests',[AdminBusinessController::class,'allVendorRequest'])->name('admin-vendor-change-request');
    Route::get('admin-dashboard//vendor-change/{id}/{action}', [AdminBusinessController::class, 'handleRequest'])->name('admin-vendor-change.handle')->where('action', 'approve|reject');

    Route::get('/admin-dashboard/vendor-product-change-requests',[AdminBusinessController::class,'allVendorProductRequest'])->name('admin-vendor-product-change-request');
    Route::get('admin-dashboard//product-change/{id}/{action}', [AdminBusinessController::class, 'handleProductRequest'])->name('admin-product-change.handle')->where('action', 'approve|reject');


    Route::get('/admin-dashboard/vendor-review-feedback',[AdminBusinessController::class,'allUserReviewFeedback'])->name('admin-vendor-review-feedback');
    // routes/web.php
    Route::get('/admin-dashboard//feedback/handle/{id}/{action}', [AdminBusinessController::class, 'handleReviewFeedback'])->name('admin.review.feedback.handle');


});

Route::post('/user-register-process', [AuthenticationController::class, 'registerProcc'])->middleware('guest')->name('user-register-process');
Route::post('/vendor-register-process', [AuthenticationController::class, 'vendorRegisterProcess'])->name('vendor-register-process')->middleware('guest');

Route::post('/user/review-add-procc', [ReviewController::class, 'reviewAddProc'])->name('review.store')->middleware('auth');
Route::get('/user/review-delete/{id}', [ReviewController::class, 'destroy'])->name('review.delete')->middleware('auth');

// --------------- USER ROUTES ----------------------
Route::group(['prefix' => '{locale?}', 'middleware' => ['guest', 'AddLocaleAutomatically','CaptureAdClickId']], function () {



    Route::get('/', [ViewController::class, 'home'])->name('home');
    Route::get('/login', [AuthenticationController::class, 'index'])->name('login');
    Route::get('/register', [AuthenticationController::class, 'register'])->name('register');
    // Vendor Register Route
    // Route::get('/vendor-register', [AuthenticationController::class, 'vendorRegisterForm'])->name('vendor-register');
    // End Vendor Register Route

    Route::get('/recover-password', [AuthenticationController::class, 'forgotPassword'])->name('recover-password');
    Route::post('/password-procc', [AuthenticationController::class, 'forgotProcc'])->name('password-procc');
    Route::get('/otp-confirm', [AuthenticationController::class, 'otpConfirm'])->name('get-otp');
    Route::post('/opt-procc', [AuthenticationController::class, 'optProcc'])->name('opt-procc');
    Route::get('/new-password', [AuthenticationController::class, 'newPassword'])->name('new-passwod');
    Route::post('/new-password-procc', [AuthenticationController::class, 'newPasswordProcc'])->name('new-password-procc');

    // Category Controller
    Route::get('/categories', [CategoryController::class, 'index'])->name('category');
    Route::get('/categories/{slug}', [CategoryController::class, 'categoryDetail'])->name('category.detail');
    


    // Product Controller
    Route::get('/software/{slug}', [ProductController::class, 'productDetail'])->name('product.details');
    Route::get('/product', [ProductController::class, 'productDetail'])->name('product');
    Route::get('/products/{id}', [ProductController::class, 'productDetail'])->name('user.product_detail');
    Route::get('/top-rated-products/{category?}', [ProductController::class, 'topRatedProduct'])->name('top-rated-product');
    Route::get('/Exclusive-Businesses-Deals', [ProductController::class, 'ExclusiveBusinessDeals'])->name('exclusive-business-deals');
    Route::get('/product-comparison', [ProductController::class, 'productComparison'])->name('product-comparison');
    Route::post('/remove-from-comparison/{productId?}', [ProductController::class, 'removeFromComparison'])->name('remove-from-comparison');
    Route::post('/clear-comparison', [ProductController::class, 'clearComparison'])->name('clear-comparison');

    // Key Feature review Route
    Route::post('/feature/review/store', [ProductController::class, 'storeFeatureReview'])
    ->name('business.feature.review.store');


    //TermAndConditionController
    Route::get('/privacy-policy', [TermAndConditionController::class, 'privacyPolicy'])->name('privacy-policy');
    Route::get('/terms-condition', [TermAndConditionController::class, 'termsCondtion'])->name('terms-condition');

    // SiteMetaPages Controller
    Route::get('/expert-guides', [MetaPagesController::class, 'expertGuide'])->name('expert-guide');
    Route::get('/expert-guides/{slug}', [MetaPagesController::class, 'expertGuideCategory'])->name('expert-guide-category');
    Route::get('/expert-guides/{cat_slug}/{art_slug}', [MetaPagesController::class, 'expertGuideArticle'])->name('expert-guide-article');


    Route::get('/help-center', [MetaPagesController::class, 'helpCenter'])->name('help-center');
    Route::get('/who-we-are', [MetaPagesController::class, 'whoWeAre'])->name('who-we-are');
    Route::get('/contact', [MetaPagesController::class, 'contact'])->name('contact');
    Route::post('/contact-form-submit', [MetaPagesController::class, 'contactFormSubmit'])->name('contact.submit');

    // Route::get('/privacy-policy', [MetaPagesController::class, 'showPrivacyPolicy'])->name('privacy-policy');

    Route::get('/blog', [MetaPagesController::class, 'blog'])->name('blog');

    Route::get('/vendor-get-listed', [HomeController::class, 'vendorGetListed'])->name('vendor-get-listed');
    Route::get('/vendor-how-it-works', [HomeController::class, 'vendorHowItWork'])->name('vendor-how-it-work');

    Route::get('/vendor-help', [HomeController::class, 'vendorHelp'])->name('vendor-help');

    // Create Vendor RegisterList Page
    Route::get('/vendor-register', [HomeController::class, 'vendorRegisterList'])->name('vendor-register');

    // Handle form submission
    Route::post('/vendor-register', [HomeController::class, 'vendorRegisterListStore'])->name('vendor-register.store');

    //Vendor Request Page Route
    Route::get('/vendor/request', [HomeController::class, 'showVendorRequest'])->name('vendor.request');

    //End Vendor Register Route


    Route::post('fetch-product', [ProductController::class, 'fetchProduct'])->name('fetch.product');

    Route::post('wishlist', [ProductController::class, 'addToWishlist'])->name('wishlist');
    Route::delete('/wishlist/{id}', [ProductController::class, 'destroyWishlist'])->name('wishlist.destroy');
    // Clean FAQ route with optional slug
    Route::get('/questions-answers/{slug?}', [ViewController::class, 'Faqs'])->name('FaqsShow');

    Route::get('/{slug}/all-review/', [ViewController::class, 'allReview'])->name('ReviewShow');
    //Review Transalation route
    // Route::get('/review/transalation',[ViewController::class, 'ReviewTranslation'])->name('review.translation');
    Route::post('/review/translation', [ViewController::class, 'ReviewTranslation'])
        ->name('review.translation');
    //user-dashboard

});
Route::group(['prefix' => '{locale?}', 'middleware' => ['User']], function () {
    Route::get('/user-dashboard', [UserDashboardController::class, 'userAccount'])->name('user-dashboard');
    Route::get('/user-dashboard/product', [UserDashboardController::class, 'userProduct'])->name('user-product');
    Route::get('/user-dashboard/profile', [UserDashboardController::class, 'userProfile'])->name('user-profile');
    Route::get('/user-dashboard/review', [UserDashboardController::class, 'userReview'])->name('user-review');
    Route::get('/user-dashboard/reward', [UserDashboardController::class, 'userReward'])->name('user-reward');
    Route::get('/user-dashboard/deals', [UserDashboardController::class, 'userDeal'])->name('user-deal');
    Route::get('/user-dashboard/support', [UserDashboardController::class, 'userSupport'])->name('user-support');
    Route::get('/user-dashboard/support/{id}',[UserDashboardController::class,'supportView'])->name('user-support-view');

    Route::get('/user-dashboard/configuration', [UserDashboardController::class, 'userConfiguration'])->name('user-configuration');
    Route::post('/user-dashboard-configuration-update', [UserDashboardController::class, 'updatePassword'])->name('user-updatePassword');

    Route::post('/user-dashboard-configuration-email-prefernce-update', [UserDashboardController::class, 'updateEmailPreferences'])
    ->name('user.email-preferences.update');

});

Route::group(['prefix' => '{locale?}', 'middleware' => ['vendor']], function () {
    // Route::get('/vendor-dashboard', [HomeController::class, 'index'])
    //     ->name('vendor-dashboard');
    Route::get('/vendor-overview', [HomeController::class, 'dash'])
        ->name('vendor-overview');



    Route::get('/vendor-product-offers', [HomeController::class, 'productOffer'])
        ->name('vendor-product-offer');


    Route::get('/vendor-add-new-list', [HomeController::class, 'addList'])
        ->name('vendor-add-new-list');

    Route::get('/vendor-advertising', [HomeController::class, 'advertising'])
        ->name('vendor-advertising');

    Route::get('/vendor-analytics-reports', [HomeController::class, 'analytic'])
        ->name('vendor-analytics');

    Route::get('/vendor-campaign', [HomeController::class, 'compaign'])
        ->name('vendor-campaign');



    Route::get('/vendor-my-listing', [HomeController::class, 'myListing'])
        ->name('vendor-my-listing');

    Route::get('/vendor-managing-campaign', [HomeController::class, 'm_Campaign'])
        ->name('vendor-managing-campaign');

    Route::get('/vendor-my-listing', [HomeController::class, 'myListing'])
        ->name('vendor-my-listing');

    Route::get('/vendor-review', [HomeController::class, 'review'])
        ->name('vendor-review');

    Route::get('/vendor-review-managment', [HomeController::class, 'reviewManagment'])
        ->name('vendor-review-managment');


    Route::get('/vendor-edit-list', [HomeController::class, 'editList'])
        ->name('vendor-edit-list');
    Route::get('/vendor-profile', [HomeController::class, 'vendorProfile'])->name('vendor-profile');

    // Vendor Product
    Route::get('/vendor-total-product-listing', [HomeController::class, 'allProduct'])->name('vendor-total-product');

    Route::get('/vendor-add-product/{product_id?}', [HomeController::class, 'addProduct'])->name('vendor-add-product');

    // visit user
    Route::get('/vendor-profile-view', [HomeController::class, 'businessView'])->name('vendor-business-view');

    //vendor configuration route
    Route::get('/vendor-dashboard/configuration', [HomeController::class, 'vendorConfiguration'])->name('vendor-configuration');
    Route::post('/vendor-dashboard-configuration-update', [HomeController::class, 'updatePassword'])->name('vendor-updatePassword');


});



Route::get('/set-site-active-language/{lang_id}', [SiteLanguagesController::class, 'setActiveSiteLanguage'])->name('set-site-languages');
Route::get('/set-admin-active-language/{lang_id}', [SiteLanguagesController::class, 'setActiveAdminLanguage'])->name('set-admin-languages');
Route::get('/refresh-csrf', function () {
    return response()->json(['token' => csrf_token()]);
});

// Ad tracking handling
Route::post('/postback', [PostbackController::class, 'handle']);





