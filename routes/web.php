<?php

use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\ConfigurationController;
use App\Http\Controllers\GatewayController;
use App\Http\Controllers\GeneralController;
use App\Http\Controllers\GenerationController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\MembershipController;
use App\Http\Controllers\MessagesController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaypalController;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\PhotosController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\TypeController;
use App\Http\Controllers\User\FamilyTreeController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\User\UserRegisterController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\DeathCertificateController;
use Illuminate\Support\Facades\Route;

// Stripe webhook endpoint (must be outside authentication)
Route::post('/stripe/webhook', [StripeController::class, 'webhook'])->name('stripe.webhook')->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

Route::namespace('User')->group(function () {
    Route::get('/', 'UserLoginController@index')->name('user.loginpage')->middleware('userredirectIfAuthenticated');
    Route::post('loginuser', 'UserLoginController@login')->name('user.login');
    Route::get('register', 'UserRegisterController@index')->name('user.registerpage')->middleware('userredirectIfAuthenticated');
    Route::post('registeruser', 'UserRegisterController@register')->name('user.register');
    Route::get('/forgot-password', [UserRegisterController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [UserRegisterController::class, 'sendResetLinkEmail'])->name('password.email');

    // User dashboard route protected by 'user' middleware
    Route::get('/set-language/{locale}', 'LanguageController@setLanguage')->name('setLanguage');
    Route::prefix('user')->middleware('user')->group(function () {
        Route::get('logout', 'UserLoginController@logout')->name('user.logout');
        Route::get('dashboard', 'UserDashboardController@index')->name('user.dashboard');
        Route::get('familytreelist', 'FamilyTreeController@index')->name('user.familytree');
        Route::post('familytreeAdd', 'FamilyTreeController@store')->name('user.familytreeAdd');
        Route::post('updatefamilytree', 'FamilyTreeController@update')->name('user.familytreeupdate');
        Route::get('addmember/{id}', 'FamilyTreeController@addmember')->name('user.addmember');
        Route::post('addmember', 'FamilyTreeController@memberstore')->name('user.addmember.store');
        Route::post('/members/import', [ImportController::class, 'import'])->name('members.import');
        Route::get('/modal/edit/{id}', 'FamilyTreeController@editModal')->name('member.editModal');
        Route::put('/members/{id}', 'FamilyTreeController@updateMember')->name('members.update');
        Route::delete('/member/{id}', 'FamilyTreeController@destroy')->name('member.destroy');
        Route::get('/get-family-tree/{id}', 'FamilyTreeController@getFamilyTree')->name('getFamilyTree');
        Route::post('/family-tree/delete', 'FamilyTreeController@deleteFamilyTree')->name('user.familytreedelete');
        Route::get("blogs", [BlogController::class, "index"])->name("user.blog");
        Route::post('/store-blogs', [BlogController::class, 'store'])->name('blogs.store');
        Route::get("create-blog", [BlogController::class, "create"])->name("user.blog.create");
        Route::get("edit-blog/{id}", [BlogController::class, "edit"])->name("blogs.edit");
        Route::get("create-news", [NewsController::class, "create"])->name("user.news.create");
        Route::get("edit-news/{id}", [NewsController::class, "edit"])->name("user.news.edit");
        Route::delete('/blogs/{blog}', [BlogController::class, 'destroy'])->name('blogs.destroy');
        Route::post("store-news", [NewsController::class, "store"])->name("user.news.store");
        Route::delete('/user/news/{news}', [NewsController::class, 'destroy'])->name('user.news.destroy');
        Route::post('/update-user-profile', [UserController::class, "update"])->name('user.profile.update');
        Route::post('/update-general-profile', [UserController::class, "generalupdate"])->name('user.general.update');
        Route::post("store-socials", [UserController::class, "storesocials"])->name("user.socialstores");
        Route::get("socialmedia", [MediaController::class, "index"])->name("user.socialmedia");
        Route::get("membership", [MembershipController::class, "index"])->name("user.memberships");
        Route::get("photos", [PhotosController::class, "index"])->name("user.photos");
        Route::post('/user/upload-photo', [PhotosController::class, 'uploadPhoto'])->name('user.upload.photo');
        Route::get("news", [NewsController::class, "index"])->name("user.news");
        Route::get('search', 'UserController@search')->name('user.search');
        Route::get("family-lisitng", [FamilyTreeController::class, "listing"])->name("user.familylisting");
        Route::get("messages", [MessagesController::class, "usermessages"])->name("user.messageboard");
        Route::post("messages/send-email", [MessagesController::class, "sendEmailToMembers"])->name("user.messages.send-email");
        Route::get("messages-from", [MessagesController::class, "usermessagesto"])->name("user.messageto");
        Route::post('/user/message/store', [MessagesController::class, 'usersstore'])->name('user.message.store');
        Route::get("import", [ImportController::class, "index"])->name("user.import");
        Route::get("export", [ImportController::class, "export"])->name("user.export");
        Route::get('/import/sample-csv', [ImportController::class, 'downloadSample'])->name('import.sample.csv');
        Route::post('/export-members', [ImportController::class, 'exportMembers'])->name('export.members');
        Route::get('/messages/{id}', [MessagesController::class, 'usershow'])->name('user.messages.show');
        Route::post('/messages/{id}/reply', [MessagesController::class, 'userReply'])->name('user.messages.reply');
        Route::get("send-message", [MessagesController::class, "sendmessage"])->name("user.send.message");
        Route::get("security", [ConfigurationController::class, "index"])->name("user.security");
        // web.php
        Route::post('/user/photos/delete', [PhotosController::class, 'delete'])->name('user.photos.delete');
        Route::get("templates", [TemplateController::class, "templateuser"])->name("user.templates");
        Route::post("store-templates", [TemplateController::class, "storetemplate"])->name("user.templates.store");
        // web.php
        Route::get('/paypal/subscribe/{id}', [PayPalController::class, 'subscribe'])->name('paypal.subscribe');
        Route::get('/paypal/success', [PayPalController::class, 'success'])->name('paypal.success');
        Route::get('/paypal/cancel', [PayPalController::class, 'cancel'])->name('paypal.cancel');

        Route::get('/stripe/checkout/{id}', [StripeController::class, 'checkout'])->name('stripe.checkout');
        Route::get('/stripe/success', [StripeController::class, 'success'])->name('stripe.success');
        Route::get('/stripe/cancel', [StripeController::class, 'cancel'])->name('stripe.cancel');


        Route::get("general-information", [ConfigurationController::class, "general"])->name("user.general");
        Route::get('settings', function () {
            return view('user-view.settings.index');
        })->name('user.setting');
        Route::get('profile', 'UserprofileController@index')->name('user.profile');
        Route::post('profileupdate', 'UserprofileController@update')->name('user.profileupdate');
        Route::get('password', function () {
            return view('user-view.settings.password');
        });
        Route::post('changepassword', 'UserprofileController@Password')->name('user.Password');

        // Deceased Members & Death Certificate
        Route::get('deceased-members', [DeathCertificateController::class, 'index'])->name('user.deceased');
        Route::get('death-certificate/{id}', [DeathCertificateController::class, 'certificate'])->name('user.death.certificate');

        // Chatbot Route
        Route::post('chatbot/chat', [ChatbotController::class, 'chat'])->name('chatbot.chat');
    });
});
Route::post('addfirstmember', [FamilyTreeController::class, "firstmemberstore"])->name('user.addfirstmember.store')->middleware("user");
Route::prefix('admin')->namespace('Admin')->group(function () {
    Route::get('/', 'AuthController@index')->name('admin.index')->middleware('adminredirectIfAuthenticated');
    Route::post('/', 'AuthController@login')->name('admin.login-form');
    Route::get('logout', 'AuthController@logout')->name('admin.logout');

    // Admin dashboard route protected by 'admin' middleware
    Route::middleware('admin')->group(function () {
        Route::get('dashboard', 'AdminDashboardController@index')->name('admin.dashboard');
        Route::get('profile', 'AdminProfileController@index')->name('admin.profile');
        Route::post('profile', 'AdminProfileController@update')->name('admin.profile-form');
        Route::post('Password', 'AdminProfileController@Password')->name('admin.Password-form');

        Route::get('language', 'AdminLanguageController@index')->name('admin.language');
        Route::post('language', 'AdminLanguageController@store')->name('admin.language.store');
        Route::post('language/update', 'AdminLanguageController@update')->name('admin.language.update');
        Route::post('language/destroy/{id}', 'AdminLanguageController@destroy')->name('admin.language.delete');
        Route::get('language/translate/{language_code}', 'AdminTranslateController@index')->name('admin.language.translate');
        Route::post('language/translate/save/{language_code}', 'AdminTranslateController@save')->name('admin.translate.save');
        Route::get('language/switch/{language_code}', 'AdminLanguageController@switch')->name('language.switch');

        Route::get('plan', 'AdminPlanController@index')->name('admin.plan');
        Route::post('Addplan', 'AdminPlanController@store')->name('admin.plan.store');
        Route::post('Deleteplan/{id}', 'AdminPlanController@delete')->name('admin.plan.delete');
        Route::get('Editplan/{id}', 'AdminPlanController@edit')->name('admin.plan.edit');
        Route::post('updateplan', 'AdminPlanController@update')->name('admin.plan.update');

        Route::get('user', 'AdminUserController@index')->name('admin.user');
        Route::post('Deactivateeuser/{id}', 'AdminUserController@deactivate')->name('admin.user.deactivate');
        Route::post('activateuser/{id}', 'AdminUserController@activate')->name('admin.user.activate');
        Route::get("orders", [OrderController::class, "index"])->name("admin.orders");
        Route::get("templates", [TemplateController::class, "templates"])->name("admin.templates");
        Route::get("general", [GeneralController::class, "index"])->name("admin.general");

        Route::get("security", [GeneralController::class, "security"])->name("admin.security");
        Route::get("payments-gateway", [GeneralController::class, "payments"])->name("admin.payments");
        Route::get("social-media", [GeneralController::class, "socialmedia"])->name("admin.socialmedia");
        Route::get("recaptcha", [GeneralController::class, "recaptcha"])->name("admin.recaptcha");
        Route::post('/recaptcha', [GeneralController::class, 'update'])->name('recaptcha.update');
        Route::get("membership-plans", [GeneralController::class, "memberships"])->name("admin.memberships");
        Route::get("messages-from", [MessagesController::class, "messagefrom"])->name("admin.messagesfrom");
        Route::get("messages-to", [MessagesController::class, "messageto"])->name("admin.messagesto");
        Route::get('messages/create', [MessagesController::class, 'create'])->name('admin.messages.create');
        Route::get('/messages/{id}', [MessagesController::class, 'show'])->name('admin.messages.show');
        Route::put('/admin/profile/update', [AdminUserController::class, 'updateProfile'])->name('admin.profile.update');
        Route::put('/admin/password/update', [AdminUserController::class, 'updatePassword'])->name('admin.password.update');
        Route::post('/admin/gateway/update-cod', [GatewayController::class, 'updateCod'])->name('admin.gateway.update.cod');
        Route::post('/admin/gateway/update-digital', [GatewayController::class, 'updateDigital'])->name('admin.gateway.update.digital');
        Route::post('/admin/gateway/update-paypal', [GatewayController::class, 'updatePaypal'])->name('admin.gateway.update.paypal');
        Route::post('/admin/gateway/update-stripe', [GatewayController::class, 'updateStripe'])->name('admin.gateway.update.stripe');

        // Store the message
        Route::post('/admin/messages', [MessagesController::class, 'store'])->name('admin.messages.store');
        Route::post('/admin/messages/{id}/reply', [MessagesController::class, 'reply'])->name('admin.messages.reply');

        Route::get("types", [TypeController::class, "index"])->name("admin.types");
        Route::get("create-type", [TypeController::class, "create"])->name("admin.types.create");
        Route::get("edit-type/{id}", [TypeController::class, "edit"])->name("admin.types.edit");
        Route::post("store-type", [TypeController::class, "store"])->name("admin.type.store");
        Route::get('type/delete/{id}', [TypeController::class, 'destroy'])->name('admin.type.delete');

        Route::get("generations", [GenerationController::class, "index"])->name("admin.generations");
        Route::get("create-generations", [GenerationController::class, "create"])->name("admin.generations.create");
        Route::get("edit-generations/{id}", [GenerationController::class, "edit"])->name("admin.generations.edit");
        Route::post("store-generations", [GenerationController::class, "store"])->name("admin.generations.store");
        Route::get('generations/delete/{id}', [GenerationController::class, 'destroy'])->name('admin.generations.delete');
        Route::get("create-template", [TemplateController::class, "create"])->name("admin.template.create");
        Route::post("store-template", [TemplateController::class, "store"])->name("admin.template.store");
        Route::get("edit-template/{id}", [TemplateController::class, "edit"])->name("admin.template.edit");
        Route::get('template/delete/{id}', [TemplateController::class, 'destroy'])->name('admin.template.delete');
    });
});
