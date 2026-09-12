<?php

use Illuminate\Support\Facades\Route;
// Frontend controllers
use App\Http\Controllers\frontend\FrontendPageController;
use App\Http\Controllers\frontend\StorageFileController;
use App\Http\Controllers\frontend\SubscriptionController;
use App\Http\Controllers\frontend\MailController;
use App\Http\Controllers\frontend\PropertyController;
use App\Http\Controllers\frontend\PropertyPostController;
use App\Http\Controllers\frontend\OtpLoginController;
use App\Http\Controllers\frontend\ProfileController;

// Admin controllers
use App\Http\Controllers\admin\AuthController;
use App\Http\Controllers\admin\DashboardController;  
use App\Http\Controllers\admin\CareerController;
use App\Http\Controllers\admin\ChangePassController;
use App\Http\Controllers\admin\BlogsController;
use App\Http\Controllers\admin\ProjectController;
use App\Http\Controllers\admin\AminityController;
use App\Http\Controllers\admin\DeveloperController;
use App\Http\Controllers\admin\LocationController;
use App\Http\Controllers\admin\SettingsController;
use App\Http\Controllers\admin\QueryController;
use App\Http\Controllers\admin\CustomLinkController; 
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\admin\PropertiesController;
use App\Http\Controllers\NotificationController;

/*
|--------------------------------------------------------------------------
| Frontend
|--------------------------------------------------------------------------
*/

// Static pages
Route::get('max-estate-105', function () {
    return view('frontend.static.max-estates');
});
Route::get('experion-151', function () {
    return view('frontend.static.experion');
});

Route::get('contact', function () {
    return view('frontend.contact');               
});
Route::get('services', function () { 
    return view('frontend.services');
});
Route::get('disclaimer', function () {
    return view('frontend.disclaimer');
});
Route::get('terms', function () {
    return view('frontend.terms');
});
Route::get('privacy', function () {
    return view('frontend.privacy');
});

Route::get('project', function () {
    return view('frontend.project');
})->name('frontend.projects');

Route::get('thankyou', [FrontendPageController::class, 'getThankYouPage'])->name('thankyou');

// Dynamic frontend pages
Route::get('/', [FrontendPageController::class, 'getHomePageData']);

Route::prefix('careers')->group(function () {
    Route::get('/', [FrontendPageController::class, 'getCareerPageData']);
    Route::get('/{slug}', [FrontendPageController::class, 'getCareerDetails']);
});
Route::get('about-us', [FrontendPageController::class, 'getAboutUsPageData'])->name('about-us');

// Property browsing
Route::prefix('properties')->group(function () {
    Route::get('/', [FrontendPageController::class, 'getPropertyListings']);
	Route::post('filters', [FrontendPageController::class, 'filterProperties'])->name('property.filters');
	Route::get('/{slug}', [FrontendPageController::class, 'getPropertyDetails'])->name('property.details');
});

// Project browsing
Route::prefix('projects')->group(function () {
    Route::get('/', [FrontendPageController::class, 'getListingsPageData'])->name('projects');
    Route::match(['GET', 'POST'], 'search', [FrontendPageController::class, 'SearchProjects'])->name('projects.search');
    Route::post('filters', [FrontendPageController::class, 'applyFilters'])->name('filters');
    Route::get('/listing', [FrontendPageController::class, 'getListingsPageData'])->name('projects.listing');
    Route::get('{slug}', [FrontendPageController::class, 'getProjectDetails'])->name('projects.details');
});

// Blog browsing
Route::prefix('blogs')->group(function () {
    Route::get('/', [FrontendPageController::class, 'getBlogsPageData']) ->name('get.blogs');
    Route::get('/{slug}', [FrontendPageController::class, 'getBlogDetails'])->name('blogs.details');
});

// Subscriptions and enquiries
Route::post('subscribe', [SubscriptionController::class, 'subscribe'])->name('subscribe');
Route::post('/popup-download', [MailController::class, 'popupDownload'])->name('popup.download');
Route::post('/mail',[MailController::class,'SendContactMail'])->name('contact-mail');

// Frontend authentication
Route::middleware(['web'])->group(function () {
    Route::get('/login', [OtpLoginController::class, 'showForm'])
        ->middleware('PreventBackPage')
        ->name('frontend.login');

    Route::post('/otp-send', [OtpLoginController::class, 'sendOtp'])
        ->name('frontend.otp.send');

    Route::post('/otp-verify', [OtpLoginController::class, 'verifyOtp'])
        ->name('frontend.otp.verify');
});

// Authenticated property-owner pages
Route::middleware(['admin:2', 'PreventBackPage'])->group(function () {
	Route::post('/logout', [OtpLoginController::class, 'logout'])->name('frontend.logout');
    Route::get('/dashboard', [PropertyController::class, 'index'])->name('list');
    Route::post('delete/property/{property}', [PropertyController::class, 'destroy'])->name('property.destroy');
    Route::post('/{property}/toggle', [PropertyController::class, 'toggleStatus'])->name('toggle');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

// Property posting flow
Route::prefix('postproperty')->middleware('auth', 'admin:2', 'PreventBackPage')->name('postproperty.')->group(function () {
    Route::get('/', [PropertyPostController::class, 'create'])->name('create');
    Route::post('/', [PropertyPostController::class, 'saveCreate'])->name('create.save');

	Route::post('/property-image/json-delete', [PropertyPostController::class, 'deleteImageFromJson'])->name('image.json.delete');
    Route::get('/{property}/property_details', [PropertyPostController::class, 'propertyDetails'])->name('edit.property_details');
    Route::post('/{property}/property_details', [PropertyPostController::class, 'savePropertyDetails'])->name('edit.property_details.save');

    Route::get('/{property}/advanced_details', [PropertyPostController::class, 'localityDetails'])->name('edit.advanced_details');
    Route::post('/{property}/advanced_details', [PropertyPostController::class, 'saveLocalityDetails'])->name('edit.advanced_details.save');

    Route::get('/{property}/price_details', [PropertyPostController::class, 'priceDetails'])->name('edit.price_details');
    Route::post('/{property}/price_details', [PropertyPostController::class, 'savePriceDetails'])->name('edit.price_details.save');

    Route::get('/{property}/amenities', [PropertyPostController::class, 'amenitiesDetails'])->name('edit.amenities');
    Route::post('/{property}/amenities', [PropertyPostController::class, 'saveAmenitiesDetails'])->name('edit.amenities.save');

    Route::get('/{property}/galleries', [PropertyPostController::class, 'galleries'])->name('edit.galleries');
    Route::post('/{property}/galleries/save', [PropertyPostController::class, 'saveGalleries'])->name('edit.galleries.save');

    Route::get('/{property}/verify', [PropertyPostController::class, 'verify'])->name('edit.verify');
    Route::post('/{property}/submit', [PropertyPostController::class, 'submit'])->name('edit.submit');
});

/*
|--------------------------------------------------------------------------
| Admin
|--------------------------------------------------------------------------
*/

Route::get('7439/login', [AuthController::class, 'loginView'])
    ->middleware('PreventBackPage')
    ->name('loginPage');
Route::post('7439/login', [AuthController::class, 'login'])->name(name: 'login');
Route::prefix('7439')->middleware(['auth', 'admin:1', 'session.version', 'PreventBackPage'])->group(function () {
    // Authentication, dashboard, and password
    Route::get('logout', [AuthController::class, 'logout'])->name(name: 'logout');
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('password', [ChangePassController::class, 'changePassword'])->name('password');
    Route::post('password', [ChangePassController::class, 'updatePassword'])->name('password');

    // Amenity details
    Route::prefix('aminity-list')->group(function () {
        Route::post('store', [AminityController::class, 'store'])->name('aminity-list.store');
        Route::post('update', [AminityController::class, 'update'])->name('aminity-list.update');
    });

    Route::prefix('developer-details')->group(function () {
        Route::post('store', [DeveloperController::class, 'store'])->name('developer-details.store');
        Route::post('update', [DeveloperController::class, 'update'])->name('developer-details.update');
    });

    // Careers
    Route::prefix('career')->group(function () {
        Route::get('/', [CareerController::class, 'index'])->name('career.index');
        Route::get('add', [CareerController::class, 'add'])->name('career.add');
        Route::get('ajax-list', [CareerController::class, 'ajaxList'])->name('career.ajax-list');
        Route::get('edit/{id}', [CareerController::class, 'edit'])->name('career.edit');
        Route::get('delete/{id}', [CareerController::class, 'moveToBin'])->name('career.delete');
        Route::post('store', [CareerController::class, 'store'])->name('career.store');
        Route::post('update', [CareerController::class, 'update'])->name('career.update');
    });

    // Developers
    Route::prefix('developers')->group(function () {
        Route::get('/', [DeveloperController::class, 'index'])->name('developers.index');
        Route::get('add', [DeveloperController::class, 'add'])->name('developers.add');
        Route::get('ajax-list', [DeveloperController::class, 'ajaxList'])->name('developers.ajax-list');
        Route::get('edit/{id}', [DeveloperController::class, 'edit'])->name('developers.edit');
        Route::get('delete/{id}', [DeveloperController::class, 'moveToBin'])->name('developers.delete');
        Route::get('status/{id}', [DeveloperController::class, 'changeStatus'])->name('developers.change-status');
        Route::post('store', [DeveloperController::class, 'store'])->name('developers.store');
        Route::put('update', [DeveloperController::class, 'update'])->name('developers.update');
    });

    // Locations
    Route::prefix('locations')->group(function () {
        Route::get('/', [LocationController::class, 'index'])->name('locations.index');
        Route::get('add', [LocationController::class, 'add'])->name('locations.add');
        Route::get('ajax-list', [LocationController::class, 'ajaxList'])->name('locations.ajax-list');
        Route::get('children/{id}', [LocationController::class, 'children'])->name('locations.children');
        Route::get('edit/{id}', [LocationController::class, 'edit'])->name('locations.edit');
        Route::get('delete/{id}', [LocationController::class, 'moveToBin'])->name('locations.delete');
        Route::post('store', [LocationController::class, 'store'])->name('locations.store');
        Route::put('update', [LocationController::class, 'update'])->name('locations.update');
    });

    // Blogs
    Route::prefix('blogs')->group(function () {
        Route::get('/', [BlogsController::class, 'index'])->name('blogs.index');
        Route::get('add', [BlogsController::class, 'add'])->name('blogs.add');
        Route::get('ajax-list', [BlogsController::class, 'ajaxList'])->name('blogs.ajax-list');
        Route::get('edit/{id}', [BlogsController::class, 'edit'])->name('blogs.edit');
        Route::get('status/{id}', [BlogsController::class, 'changeStatus'])->name('blogs.change-status');
        Route::get('delete/{id}', [BlogsController::class, 'moveToBin'])->name('blogs.delete');
        Route::post('store', [BlogsController::class, 'store'])->name('blogs.store');
        Route::post('update', [BlogsController::class, 'update'])->name('blogs.update');
        Route::post('upload-image', [BlogsController::class, 'uploadImage']);
        Route::post('/check-slug', [BlogsController::class, 'checkSlug'])->name('check.slug');
    });

    // Properties
    Route::prefix('properties')->group(function () {
        Route::get('/', [PropertiesController::class, 'index'])->name('properties.index');
        Route::get('ajax-list', [PropertiesController::class, 'ajaxList'])->name('properties.ajax-list');
        Route::get('view/{id}', [PropertiesController::class, 'view'])->name('properties.view');
        Route::get('status/{id}', [PropertiesController::class, 'changeStatus'])->name('properties.change-status');
        Route::get('/{id}/approve', [PropertiesController::class, 'approveProperties'])->name('properties.approve');
        Route::get('/{id}/reject', [PropertiesController::class, 'rejectProperties'])->name('properties.reject');
    });

    // Projects
    Route::prefix('projects')->group(function () {
        Route::get('/', [ProjectController::class, 'index'])->name('projects.index');
        Route::get('add', [ProjectController::class, 'add'])->name('projects.add');
        Route::get('ajax-list', [ProjectController::class, 'ajaxList'])->name('projects.ajax-list');
        Route::get('edit/{id}', [ProjectController::class, 'edit'])->name('projects.edit');
        Route::get('delete/{id}', [ProjectController::class, 'moveToBin'])->name('projects.delete');
        Route::get('status/{id}', [ProjectController::class, 'changeStatus'])->name('projects.change-status');
        Route::post('store', [ProjectController::class, 'store'])->name('projects.store');
        Route::post('update', [ProjectController::class, 'update'])->name('projects.update');
    });

    // Settings
    Route::prefix('settings')->group(function () {
        Route::get('/', [SettingsController::class, 'index'])->name('settings.index');
        Route::post('store', [SettingsController::class, 'store'])->name('credentials.store');
    });

    // Queries
    Route::prefix('queries')->group(function () {
        Route::get('/', [QueryController::class, 'index'])->name('queries.index');
        Route::get('ajax-list', [QueryController::class, 'ajaxList'])->name('queries.ajax-list');
        Route::get('view/{id}', [QueryController::class, 'view'])->name('queries.view');
        Route::post('reply', [QueryController::class, 'reply'])->name('queries.reply');
	});

    // Custom links
    Route::prefix('custom-links')->group(function () {
        Route::get('/', [CustomLinkController::class, 'index'])->name('custom-links.index');
        Route::get('ajax-list', [CustomLinkController::class, 'ajaxList'])->name('custom-links.ajax-list');
        Route::get('view/{id}', [CustomLinkController::class, 'view'])->name('custom-links.view');
        Route::get('add', [CustomLinkController::class, 'create'])->name('custom-links.add');
        Route::get('edit/{id}', [CustomLinkController::class, 'edit'])->name('custom-links.edit');
        Route::get('delete/{id}', [CustomLinkController::class, 'destroy'])->name('custom-links.destroy');
        Route::get('status/{id}', [CustomLinkController::class, 'toggleStatus'])->name('custom-links.status');
        Route::post('store', [CustomLinkController::class, 'store'])->name('custom-links.store');
        Route::post('update/{id}', [CustomLinkController::class, 'update'])->name('custom-links.update');
	});
});

/*
|--------------------------------------------------------------------------
| Tools
|--------------------------------------------------------------------------
*/

Route::get('clear', function () {
	Artisan::call('cache:clear');
	Artisan::call('route:clear');
	Artisan::call('config:clear');
	Artisan::call('view:clear');   
	Artisan::call('clear-compiled');
	Artisan::call('optimize:clear');
	
	return "Caches cleared successfully." ;    
});

Route::get('emi', function () {
    return view('frontend.tools.emi');
})->name('emi-calculator');

Route::get('area', function () {
    return view('frontend.tools.area');
})->name('area-calculator');

Route::get('loan', function () {
    return view('frontend.tools.loan');
})->name('loan-calulator');

Route::get('ability', function () {
    return view('frontend.tools.ability');
})->name('ability-get');

Route::get('budget', function () {
    return view('frontend.tools.budget');
})->name('budget-get');

/*
|--------------------------------------------------------------------------
| Special routes
|--------------------------------------------------------------------------
*/

Route::get('/api/load-more-links', [CustomLinkController::class, 'loadMore']);

Route::get('/storage/{path}', [StorageFileController::class, 'show'])
    ->where('path', '.*');

Route::post('/save-fcm-token', [NotificationController::class, 'saveToken']);

// This broad route must remain after every specific GET route.
Route::get('/{slug}', [FrontendPageController::class, 'showFilteredProjects'])
    ->where('slug', '.*');
