<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\Auth\LoginController;
use \App\Http\Controllers\Admin\ContactusController;
use \App\Http\Controllers\Admin\RolesController;
use \App\Http\Controllers\Admin\PermissionsController;
use \App\Http\Controllers\Admin\UsersController;
use \App\Http\Controllers\App\WalletsController;
use \App\Http\Controllers\App\UsersWalletsController;
use \App\Http\Controllers\App\CitiesController;
use \App\Http\Controllers\App\ProvidersController;
use \App\Http\Controllers\App\ServicesController;
use \App\Http\Controllers\App\FieldsController;
use \App\Http\Controllers\App\OrganizationController;
use \App\Http\Controllers\App\TraderController;
use \App\Http\Controllers\App\TransactionController;
use \App\Http\Controllers\App\SettingController;
use \App\Http\Controllers\App\UpdateAdminController;
use \App\Http\Controllers\App\DashboardController;
use \App\Http\Controllers\App\AdminTransactionController;
use \App\Http\Controllers\App\BranchController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//Remove Server Cache
Route::get('/clear' , function(){
   Artisan::call('cache:clear');
   return redirect()->route('dashboard');
});

Route::middleware('auth')->group(function(){

    Route::get('/', [DashboardController::class,'dashboard'])->name('dashboard');

    Route::get('/logout_user', [LoginController::class,'logout'])->name('logout_user');

    // Roles Routes
    Route::get('roles',[RolesController::class,'index'])->name('roles');
    Route::get('roles/add',[RolesController::class,'add'])->name('roles_add');
    Route::post('roles/store',[RolesController::class,'store'])->name('roles_store');
    Route::get('roles/edit/{id}',[RolesController::class,'edit'])->name('roles_edit');
    Route::get('roles/permissions/edit/{id}',[RolesController::class,'editPermissions'])->name('roles_edit_permissions');
    Route::post('roles/update/{id}',[RolesController::class,'update'])->name('roles_update');
    Route::post('roles/permissions/update/{id}',[RolesController::class,'updatePermissions'])->name('roles_update_permissions');
    Route::get('roles/delete/{id}',[RolesController::class,'delete'])->name('roles_delete');
    Route::get('roles/search',[RolesController::class,'search'])->name('roles_search');

    // Permissions Routes
    Route::get('permissions',[PermissionsController::class,'index'])->name('permissions');
    Route::get('permissions/add',[PermissionsController::class,'add'])->name('permissions_add');
    Route::post('permissions/store',[PermissionsController::class,'store'])->name('permissions_store');
    Route::get('permissions/edit/{id}',[PermissionsController::class,'edit'])->name('permissions_edit');
    Route::post('permissions/update/{id}',[PermissionsController::class,'update'])->name('permissions_update');
    Route::get('permissions/delete/{id}',[PermissionsController::class,'delete'])->name('permissions_delete');
    Route::get('permissions/search',[PermissionsController::class,'search'])->name('permissions_search');

    // Users Routes
    Route::middleware('role:system admin')->group(function(){

        Route::get('system/users',[UsersController::class,'index'])->name('system_users');
        Route::get('system/users/add',[UsersController::class,'add'])->name('system_users_add');
        Route::post('system/users/store',[UsersController::class,'store'])->name('system_users_store');
        Route::get('system/users/edit/{id}',[UsersController::class,'edit'])->name('system_users_edit');
        Route::post('system/users/update/{id}',[UsersController::class,'update'])->name('system_users_update');
        Route::get('system/users/delete/{id}',[UsersController::class,'delete'])->name('system_users_delete');
        Route::get('system/users/search',[UsersController::class,'search'])->name('system_users_search');
        Route::get('system/users/suspend/{id}',[UsersController::class,'suspend'])->name('system_users_suspend');
        Route::get('system/users/suspend',[UsersController::class,'suspendSelected'])->name('system_users_suspend_selected');
        Route::get('system/users/allow/{id}',[UsersController::class,'allow'])->name('system_users_allow');
        Route::get('system/users/privileges/{id}/edit',[UsersController::class,'editPrivileges'])->name('system_users_privileges_edit');
        Route::post('system/users/roles/{id}/update',[UsersController::class,'updateUserRoles'])->name('system_users_roles_update');
        Route::post('system/users/permissions/{id}/update',[UsersController::class,'updateUserPermissions'])->name('system_users_permissions_update');
        Route::get('system/users/suspended/display',[UsersController::class,'getSuspendedUsers'])->name('system_users_display_suspended');


        //admin_transactions
        Route::get('admin_transactions',[AdminTransactionController::class,'index'])->name('admin_transactions');
        Route::get('admin_transactions/add',[AdminTransactionController::class,'add'])->name('admin_transactions_add');
        Route::post('admin_transactions/store',[AdminTransactionController::class,'store'])->name('admin_transactions_store');
         Route::get('admin_transactions/search',[AdminTransactionController::class,'search'])->name('admin_transactions_search');

    });

    // App users routes

    Route::get('app/users',[\App\Http\Controllers\App\UsersController::class,'index'])->name('app_users');
    Route::get('app/users/add',[\App\Http\Controllers\App\UsersController::class,'add'])->name('app_users_add');
    Route::post('app/users/store',[\App\Http\Controllers\App\UsersController::class,'store'])->name('app_users_store');
    Route::get('app/users/edit/{id}',[\App\Http\Controllers\App\UsersController::class,'edit'])->name('app_users_edit');
    Route::get('app/users/view/{id}',[\App\Http\Controllers\App\UsersController::class,'view'])->name('app_users_view');
    Route::post('app/users/update/{id}',[\App\Http\Controllers\App\UsersController::class,'update'])->name('app_users_update');
    Route::get('app/users/delete/{id}',[\App\Http\Controllers\App\UsersController::class,'delete'])->name('app_users_delete');
    Route::get('app/users/search',[\App\Http\Controllers\App\UsersController::class,'search'])->name('app_users_search');
    Route::get('app/users/deactivate/{id}',[\App\Http\Controllers\App\UsersController::class,'deactivate'])->name('app_users_deactivate');
    Route::get('app/users/activate/{id}',[\App\Http\Controllers\App\UsersController::class,'activate'])->name('app_users_activate');
    Route::get('app/users/delete',[\App\Http\Controllers\App\UsersController::class,'deleteSelected'])->name('app_users_delete_selected');
    Route::get('app/users/deactivate',[\App\Http\Controllers\App\UsersController::class,'deactivateSelected'])->name('app_users_deactivate_selected');

    Route::get('app/users/send_notification/{id}',[\App\Http\Controllers\App\UsersController::class,'send_notification'])->name('app_users_send_notification');
    Route::post('app/users/send_notification/{id}',[\App\Http\Controllers\App\UsersController::class,'send_notification_post'])->name('app_users_send_notification_post');
    Route::get('app/users/send_notification_for_all_users',[\App\Http\Controllers\App\UsersController::class,'send_notification_for_all_users'])->name('send_notification_for_all_users');
    Route::post('app/users/send_notification_for_all_users_post',[\App\Http\Controllers\App\UsersController::class,'send_notification_for_all_users_post'])->name('send_notification_for_all_users_post');


    //update_profile
    Route::get('app/edit_profile',[UpdateAdminController::class,'edit'])->name('edit_profile');
    Route::post('app/update_profile',[UpdateAdminController::class,'update'])->name('update_profile');


    //Settings
    Route::get('app/settings/edit',[SettingController::class,'edit'])->name('settings_edit');
    Route::post('app/settings/update',[SettingController::class,'update'])->name('settings_update');



    // Wallets routes

    Route::get('app/wallets',[WalletsController::class,'index'])->name('wallets');
    Route::get('app/wallets/add',[WalletsController::class,'add'])->name('wallets_add');
    Route::post('app/wallets/store',[WalletsController::class,'store'])->name('wallets_store');
    Route::get('app/wallets/edit/{id}',[WalletsController::class,'edit'])->name('wallets_edit');
    Route::post('app/wallets/update/{id}',[WalletsController::class,'update'])->name('wallets_update');
    Route::get('app/wallets/delete/{id}',[WalletsController::class,'delete'])->name('wallets_delete');
    Route::get('app/wallets/search',[WalletsController::class,'search'])->name('wallets_search');
    Route::get('app/wallets/show_wallet_transactions/{id}',[WalletsController::class,'show_wallet_transactions'])->name('show_wallet_transactions');



    //Organization

    Route::get('app/organization',[OrganizationController::class,'index'])->name('organization');
    Route::get('app/organization/view/{id}',[OrganizationController::class,'view'])->name('organization_view');
    Route::get('app/organization/delete/{id}',[OrganizationController::class,'delete'])->name('organization_delete');
    Route::get('app/organization/search',[OrganizationController::class,'search'])->name('organization_search');



    //Trader

    Route::get('app/trader',[TraderController::class,'index'])->name('trader');
    Route::get('app/trader/view/{id}',[TraderController::class,'view'])->name('trader_view');
    Route::get('app/trader/delete/{id}',[TraderController::class,'delete'])->name('trader_delete');
    Route::get('app/trader/search',[TraderController::class,'search'])->name('trader_search');

    Route::get('app/users/send_notification_for_all_traders',[TraderController::class,'send_notification_for_all_traders'])->name('send_notification_for_all_traders');
    Route::post('app/users/send_notification_for_all_traders_post',[TraderController::class,'send_notification_for_all_traders_post'])->name('send_notification_for_all_traders_post');



    //Transactions
    Route::get('app/transaction',[TransactionController::class,'index'])->name('transaction');
    Route::get('app/transaction/view/{id}',[TransactionController::class,'view'])->name('transaction_view');
    Route::get('app/transaction/delete/{id}',[TransactionController::class,'delete'])->name('transaction_delete');
    Route::get('app/transaction/search',[TransactionController::class,'search'])->name('transaction_search');





    // Users Wallets routes

    Route::get('app/users/wallets',[UsersWalletsController::class,'index'])->name('users_wallets');
    Route::get('app/users/wallets/add',[UsersWalletsController::class,'add'])->name('users_wallets_add');
    Route::post('app/users/wallets/store',[UsersWalletsController::class,'store'])->name('users_wallets_store');
    Route::get('app/users/wallets/edit/{id}',[UsersWalletsController::class,'edit'])->name('users_wallets_edit');
    Route::post('app/users/wallets/update/{id}',[UsersWalletsController::class,'update'])->name('users_wallets_update');
    Route::get('app/users/wallets/delete/{id}',[UsersWalletsController::class,'delete'])->name('users_wallets_delete');
    Route::get('app/users/wallets/search',[UsersWalletsController::class,'search'])->name('users_wallets_search');

    // Cities Routes

    Route::get('cities',[CitiesController::class,'index'])->name('cities');
    Route::get('cities/add',[CitiesController::class,'add'])->name('cities_add');
    Route::post('cities/store',[CitiesController::class,'store'])->name('cities_store');
    Route::get('cities/edit/{id}',[CitiesController::class,'edit'])->name('cities_edit');
    Route::post('cities/update/{id}',[CitiesController::class,'update'])->name('cities_update');
    Route::get('cities/delete/{id}',[CitiesController::class,'delete'])->name('cities_delete');
    Route::get('cities/state/{id}',[CitiesController::class,'changeState'])->name('cities_state');
    Route::get('cities/search',[CitiesController::class,'search'])->name('cities_search');
    Route::get('cities/deactivate',[CitiesController::class,'deactivateSelected'])->name('cities_deactivate_selected');
    Route::get('cities/delete',[CitiesController::class,'deleteSelected'])->name('cities_delete_selected');


    //Branches
    Route::get('branches',[BranchController::class,'index'])->name('branches');
    Route::get('branches/add',[BranchController::class,'add'])->name('branches_add');
    Route::post('branches/store',[BranchController::class,'store'])->name('branches_store');
    Route::get('branches/edit/{id}',[BranchController::class,'edit'])->name('branches_edit');
    Route::post('branches/update/{id}',[BranchController::class,'update'])->name('branches_update');
    Route::get('branches/delete/{id}',[BranchController::class,'delete'])->name('branches_delete');
    Route::get('branches/search',[BranchController::class,'search'])->name('branches_search');



    //Pages
    Route::get('/pages/view/{type}',[FieldsController::class,'edit_page'])->name('edit_page');
    Route::post('/pages/update',[FieldsController::class,'update_page'])->name('update_page');




    // Fields Routes

    Route::get('fields',[FieldsController::class,'index'])->name('fields');
    Route::get('fields/add',[FieldsController::class,'add'])->name('fields_add');
    Route::post('fields/store',[FieldsController::class,'store'])->name('fields_store');
    Route::get('fields/edit/{id}',[FieldsController::class,'edit'])->name('fields_edit');
    Route::post('fields/update/{id}',[FieldsController::class,'update'])->name('fields_update');
    Route::get('fields/delete/{id}',[FieldsController::class,'delete'])->name('fields_delete');
    Route::get('fields/state/{id}',[FieldsController::class,'changeState'])->name('fields_state');
    Route::get('fields/search',[FieldsController::class,'search'])->name('fields_search');
    Route::get('fields/deactivate',[FieldsController::class,'deactivateSelected'])->name('fields_deactivate_selected');
    Route::get('fields/delete',[FieldsController::class,'deleteSelected'])->name('fields_delete_selected');

    // Providers Routes

    Route::get('providers',[ProvidersController::class,'index'])->name('providers');
    Route::get('providers/add',[ProvidersController::class,'add'])->name('providers_add');
    Route::post('providers/store',[ProvidersController::class,'store'])->name('providers_store');
    Route::get('providers/edit/{id}',[ProvidersController::class,'edit'])->name('providers_edit');
    Route::post('providers/update/{id}',[ProvidersController::class,'update'])->name('providers_update');
    Route::get('providers/delete/{id}',[ProvidersController::class,'delete'])->name('providers_delete');
    Route::get('providers/approval/{id}',[ProvidersController::class,'approveState'])->name('providers_approve_change');
    Route::get('providers/confirm/{id}',[ProvidersController::class,'confirmState'])->name('providers_confirm_change');
    Route::get('providers/search',[ProvidersController::class,'search'])->name('providers_search');
    Route::get('providers/delete',[ProvidersController::class,'deleteSelected'])->name('providers_delete_selected');

    Route::get('app/users/send_notification_for_all_providers',[ProvidersController::class,'send_notification_for_all_providers'])->name('send_notification_for_all_providers');
    Route::post('app/users/send_notification_for_all_providers_post',[ProvidersController::class,'send_notification_for_all_providers_post'])->name('send_notification_for_all_providers_post');




    // Services Routes

    Route::get('services',[ServicesController::class,'index'])->name('services');
    Route::get('services/add',[ServicesController::class,'add'])->name('services_add');
    Route::post('services/store',[ServicesController::class,'store'])->name('services_store');
    Route::get('services/edit/{id}',[ServicesController::class,'edit'])->name('services_edit');
    Route::post('services/update/{id}',[ServicesController::class,'update'])->name('services_update');
    Route::get('services/delete/{id}',[ServicesController::class,'delete'])->name('services_delete');
    Route::get('services/search',[ServicesController::class,'search'])->name('services_search');
    Route::get('services/deactivate/{id}',[ServicesController::class,'deactivate'])->name('services_deactivate');
    Route::get('services/activate/{id}',[ServicesController::class,'activate'])->name('services_activate');
    Route::get('services/disapproved/{id}',[ServicesController::class,'disapproved'])->name('services_disapproved');
    Route::get('services/approved/{id}',[ServicesController::class,'approved'])->name('services_approved');

    Route::get('services/send_notification_for_all_members_in_service/{id}',[ServicesController::class,'send_notification_for_all_members_in_service'])->name('send_notification_for_all_members_in_service');
    Route::post('services/send_notification_for_all_members_in_service_post/{id}',[ServicesController::class,'send_notification_for_all_members_in_service_post'])->name('send_notification_for_all_members_in_service_post');

    // Contact us routes

    Route::get('messages',[ContactusController::class,'index'])->name('contact_us');
    Route::get('messages/trash',[ContactusController::class,'displayTrash'])->name('contact_us_display_trash');
    Route::get('messages/view/{id}',[ContactusController::class,'viewMessage'])->name('contact_us_view');
    Route::get('messages/delete/{id}',[ContactusController::class,'delete'])->name('contact_us_delete');
    Route::get('messages/restore/{id}',[ContactusController::class,'restoreItem'])->name('contact_us_restore_item');
    Route::get('messages/search',[ContactusController::class,'search'])->name('contact_us_search');
    Route::get('messages/delete_all',[ContactusController::class,'delete_all'])->name('contact_us_delete_all');
    Route::get('messages/selected/change_change',[ContactusController::class,'change_state_selected'])->name('contact_us_change_state_selected');
    Route::get('messages/selected/delete',[ContactusController::class,'delete_selected'])->name('contact_us_delete_selected');
    Route::get('messages/restore_all',[ContactusController::class,'restore'])->name('contact_us_restore');
    Route::get('messages/state/change/{id}',[ContactusController::class,'changeState'])->name('contact_us_state_change');
    Route::post('messages/reply',[ContactusController::class,'reply'])->name('contact_us_reply');

});

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::get('/login_user', [LoginController::class,'login'])->name('login_user');
