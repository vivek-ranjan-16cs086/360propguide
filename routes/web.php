<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
//frontend
use App\Http\Controllers\frontend\FrontendPageController;
use App\Http\Controllers\frontend\StorageFileController;
use App\Http\Controllers\frontend\SubscriptionController;
use App\Http\Controllers\frontend\MailController;
use App\Http\Controllers\frontend\PropertyController;
use App\Http\Controllers\frontend\PropertyPostController;
use App\Http\Controllers\frontend\OtpLoginController;
use App\Http\Controllers\frontend\ProfileController;

//Admin Backend
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

// frontend page
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

Route::get('thankyou', function () {
    if (!session()->has('form_submitted')) {
        return redirect('/');
    }

    $form = session('form_submitted');
    session()->forget('form_submitted');

    return view('frontend.thankyou', compact('form')); 
})->name('thankyou');

//Frontend
Route::GET('/', [FrontendPageController::class, 'getHomePageData']);

Route::prefix('careers')->group(function () {
    Route::get('/', [FrontendPageController::class, 'getCareerPageData']);
    Route::get('/{slug}', [FrontendPageController::class, 'getCareerDetails']);
});
Route::GET('about-us', [FrontendPageController::class, 'getAboutUsPageData'])->name('about-us');

 // properties
 Route::group(['prefix' => 'properties'], function () {
    Route::get('/', [FrontendPageController::class, 'getPropertyListings']);
	Route::post('filters', [FrontendPageController::class, 'filterProperties'])->name('property.filters');
	Route::get('/{slug}', [FrontendPageController::class, 'getPropertyDetails'])->name('property.details');
});
 
Route::group(['prefix' => 'projects'], function () {
    Route::get('/', [FrontendPageController::class, 'getListingsPageData'])->name('projects');
    Route::match(['GET', 'POST'], 'search', [FrontendPageController::class, 'SearchProjects'])->name('projects.search');
    Route::post('filters', [FrontendPageController::class, 'applyFilters'])->name('filters');
    Route::get('/listing', [FrontendPageController::class, 'getListingsPageData'])->name('projects.listing');
    Route::get('{slug}', [FrontendPageController::class, 'getProjectDetails'])->name('projects.details');
});

//blogs
Route::group(['prefix' => 'blogs'], function () {

    Route::get('/', [FrontendPageController::class, 'getBlogsPageData']) ->name('get.blogs');
    Route::get('/{slug}', [FrontendPageController::class, 'getBlogDetails'])->name('blogs.details');

}); 

Route::post('subscribe', [SubscriptionController::class, 'subscribe'])->name('subscribe');
Route::post('/popup-download', [MailController::class, 'popupDownload'])->name('popup.download');
Route::post('/mail',[MailController::class,'SendContactMail'])->name('contact-mail');



//admin panel  
Route::get('7439/login', function () {
    if (Auth::check() && Auth::user()->role_id == 1) {
        return redirect()->route('dashboard'); // Already logged in as admin
    }

    return view('admin.login');
})->middleware('PreventBackPage')->name('loginPage');
Route::post('7439/login', [AuthController::class, 'login'])->name(name: 'login');
Route::group(["prefix" => "7439", "middleware" => ["auth", "admin:1", "session.version", "PreventBackPage"]], function () {
	
    Route::GET('logout', [AuthController::class, 'logout'])->name(name: 'logout');

    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
	
    Route::get('password', [ChangePassController::class, 'changePassword'])->name('password');
	Route::post('password', [ChangePassController::class, 'updatePassword'])->name('password');

    Route::group(['prefix' => 'aminity-list'], function(){
       Route::post('store',[AminityController::class, 'store'])->name('aminity-list.store');
       Route::POST('update', [AminityController::class, 'update'])->name('aminity-list.update');
    });
    Route::group(['prefix' => 'developer-details'], function(){
        Route::post('store',[DeveloperController::class, 'store'])->name('developer-details.store');
        Route::POST('update', [DeveloperController::class, 'update'])->name('developer-details.update');
     });

    // For Career
    Route::group(['prefix' => 'career'], function () {
        //GET
        Route::GET('/', [CareerController::class, 'index'])->name('career.index');
        Route::GET('add', [CareerController::class, 'add'])->name('career.add');
        Route::GET('ajax-list', [CareerController::class, 'ajaxList'])->name('career.ajax-list');
        Route::GET('edit/{id}', [CareerController::class, 'edit'])->name('career.edit');
        Route::GET('delete/{id}', [CareerController::class, 'moveToBin'])->name('career.delete');


        //POST
        Route::POST('store', [CareerController::class, 'store'])->name('career.store');
        Route::POST('update', [CareerController::class, 'update'])->name('career.update');
    });
	
	// develoepr
    Route::group(['prefix' => 'developers'], function () {

        //GET
        Route::GET('/', [DeveloperController::class, 'index'])->name('developers.index');
        Route::GET('add', [DeveloperController::class, 'add'])->name('developers.add');
        Route::GET('ajax-list', [DeveloperController::class, 'ajaxList'])->name('developers.ajax-list');
        Route::GET('edit/{id}', [DeveloperController::class, 'edit'])->name('developers.edit');
        Route::GET('delete/{id}', [DeveloperController::class, 'moveToBin'])->name('developers.delete');

        Route::GET('status/{id}',[DeveloperController::class,'changeStatus'])->name('developers.change-status');

        //POST
        Route::POST('store', [DeveloperController::class, 'store'])->name('developers.store');
        Route::PUT('update', [DeveloperController::class, 'update'])->name('developers.update');
    });

    Route::group(['prefix' => 'locations'], function () {
        Route::GET('/', [LocationController::class, 'index'])->name('locations.index');
        Route::GET('add', [LocationController::class, 'add'])->name('locations.add');
        Route::GET('ajax-list', [LocationController::class, 'ajaxList'])->name('locations.ajax-list');
        Route::GET('children/{id}', [LocationController::class, 'children'])->name('locations.children');
        Route::GET('edit/{id}', [LocationController::class, 'edit'])->name('locations.edit');
        Route::GET('delete/{id}', [LocationController::class, 'moveToBin'])->name('locations.delete');
        Route::POST('store', [LocationController::class, 'store'])->name('locations.store');
        Route::PUT('update', [LocationController::class, 'update'])->name('locations.update');
    });
	
    Route::group(['prefix' => 'blogs'], function () {
        //GET
        Route::GET('/', [BlogsController::class, 'index'])->name('blogs.index');
        Route::GET('add', [BlogsController::class, 'add'])->name('blogs.add');
        Route::GET('ajax-list', [BlogsController::class, 'ajaxList'])->name('blogs.ajax-list');
        Route::GET('edit/{id}', [BlogsController::class, 'edit'])->name('blogs.edit');
		Route::GET('status/{id}', [BlogsController::class, 'changeStatus'])->name('blogs.change-status');
        Route::GET('delete/{id}', [BlogsController::class, 'moveToBin'])->name('blogs.delete');


        //POST
        Route::POST('store', [BlogsController::class, 'store'])->name('blogs.store');
        Route::POST('update', [BlogsController::class, 'update'])->name('blogs.update');
		Route::POST('upload-image',[BlogsController::class,'uploadImage']);
		Route::POST('/check-slug', [BlogsController::class, 'checkSlug'])->name('check.slug');
    });
    Route::group(['prefix' => 'properties'], function () {
		
        //GET
        Route::GET('/', [PropertiesController::class, 'index'])->name('properties.index');
        Route::GET('ajax-list', [PropertiesController::class, 'ajaxList'])->name('properties.ajax-list');
        Route::GET('view/{id}', [PropertiesController::class, 'view'])->name('properties.view');
        Route::GET('status/{id}', [PropertiesController::class, 'changeStatus'])->name('properties.change-status');
        Route::get('/{id}/approve', [PropertiesController::class, 'approveProperties'])->name('properties.approve');
        Route::get('/{id}/reject', [PropertiesController::class, 'rejectProperties'])->name('properties.reject');
    });
    Route::group(['prefix' => 'projects'], function () {
		
        //GET
        Route::GET('/', [ProjectController::class, 'index'])->name('projects.index');
        Route::GET('add', [ProjectController::class, 'add'])->name('projects.add');
        Route::GET('ajax-list', [ProjectController::class, 'ajaxList'])->name('projects.ajax-list');
        Route::GET('edit/{id}', [ProjectController::class, 'edit'])->name('projects.edit');
        Route::GET('delete/{id}', [ProjectController::class, 'moveToBin'])->name('projects.delete');

        Route::GET('status/{id}',[ProjectController::class,'changeStatus'])->name('projects.change-status');

        //POST
        Route::POST('store', [ProjectController::class, 'store'])->name('projects.store');
        Route::POST('update', [ProjectController::class, 'update'])->name('projects.update');
    });
	Route::group(['prefix' => 'settings'], function () {
        Route::GET('/',[SettingsController::class,'index'])->name('settings.index');
        Route::POST('store', [SettingsController::class, 'store'])->name('credentials.store');
    });
	
	Route::group(['prefix' => 'queries'],function(){
		//GET METHODS
		Route::GET('/',[QueryController::class,'index'])->name('queries.index');
		Route::GET('ajax-list',[QueryController::class,'ajaxList'])->name('queries.ajax-list');
		Route::GET('view/{id}',[QueryController::class,'view'])->name('queries.view');
		
		//POST METHODS
        Route::POST('reply',[QueryController::class,'reply'])->name('queries.reply');
		
	});
	
	Route::group(['prefix' => 'custom-links'],function(){
		//GET METHODS
		Route::GET('/',[CustomLinkController::class,'index'])->name('custom-links.index');
		Route::GET('ajax-list',[CustomLinkController::class,'ajaxList'])->name('custom-links.ajax-list');
		Route::GET('view/{id}',[CustomLinkController::class,'view'])->name('custom-links.view');
		Route::GET('add',[CustomLinkController::class,'create'])->name('custom-links.add'); 
		Route::GET('edit/{id}',[CustomLinkController::class,'edit'])->name('custom-links.edit'); 
		Route::GET('delete/{id}',[CustomLinkController::class,'destroy'])->name('custom-links.destroy'); 
		Route::GET('status/{id}',[CustomLinkController::class,'toggleStatus'])->name('custom-links.status'); 
		
		//POST METHODS
		Route::POST('store',[CustomLinkController::class,'store'])->name('custom-links.store');
		Route::post('update/{id}', [CustomLinkController::class, 'update'])->name('custom-links.update');
		
	});
});

Route::middleware(['web'])->group(function () {

    Route::get('/login', [OtpLoginController::class, 'showForm'])
        ->middleware('PreventBackPage')
        ->name('frontend.login');

    Route::post('/otp-send', [OtpLoginController::class, 'sendOtp'])
        ->name('frontend.otp.send');

    Route::post('/otp-verify', [OtpLoginController::class, 'verifyOtp'])
        ->name('frontend.otp.verify');

});

Route::middleware([ 'admin:2', 'PreventBackPage'])->group(function () {
	// Logout
	Route::post('/logout', [OtpLoginController::class, 'logout'])->name('frontend.logout');
    Route::get('/dashboard', [PropertyController::class, 'index'])->name('list');
    Route::post('delete/property/{property}', [PropertyController::class, 'destroy'])->name('property.destroy');
    Route::post('/{property}/toggle', [PropertyController::class, 'toggleStatus'])->name('toggle');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

Route::prefix('postproperty')->middleware('auth', 'admin:2','PreventBackPage')->name('postproperty.')->group(function () {
    
    // New Property Post - Add Flow
    Route::get('/', [PropertyPostController::class, 'create'])->name('create');
    Route::post('/', [PropertyPostController::class, 'saveCreate'])->name('create.save');

    // Edit Draft
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


Route::get('clear',function(){
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

Route::get('/api/load-more-links', [CustomLinkController::class, 'loadMore']);

Route::get('/storage/{path}', [StorageFileController::class, 'show'])
    ->where('path', '.*');

//Route::GET('/{slug}', [FrontendPageController::class, 'showFilteredProjects']);
Route::get('/{slug}', [FrontendPageController::class, 'showFilteredProjects'])
    ->where('slug', '.*');
//Route::get('/generate-project-links', [CustomLinkController::class, 'generateProjectLinks']); 
Route::post('/save-fcm-token', [NotificationController::class, 'saveToken']);
