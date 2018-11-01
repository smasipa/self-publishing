<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['404_override'] = 'errors/show_404';

$route['nothing_found'] = 'pages/nothing_found';
$route['about'] = 'pages/about';
$route['terms'] = 'pages/terms';
$route['help'] = 'pages/help';
$route['contact'] = 'pages/contact';

$route['notify/purchase'] = 'purchases/notify';
$route['notify/membership'] = 'memberships/notify';
$route['purchase/cancel'] = 'purchases/cancel';
$route['membership/cancelled'] = 'memberships/cancelled';
$route['account/geeWallet'] = 'geewallet_controller/index';
$route['account/geeWallet/insufficient_funds'] = 'geewallet_controller/insufficientFunds';
$route['buy/book/(.*)/(\d+)'] = 'purchases/geeWalletPurchase/$1/$2';


$route['account/stats'] = 'writers/get_stats';
$route['account/stats/(\w+)'] = 'writers/get_stats/$1';
$route['monitor'] = 'admin/admins/index';
$route['monitor/search'] = 'admin/search_results/index';
$route['monitor/ban/(\w+)/(\d+)'] = 'admin/admins/ban_item/$1/$2';
$route['monitor/banned'] = 'admin/admins/get_banned_list';
$route['monitor/menu'] = 'admin/admins/menu';
$route['monitor/geeWallet/recharge'] = 'geewallet_controller/recharge';
$route['monitor/geeWallet/all_recharges'] = 'geewallet_controller/allRecharges';

$route['monitor/activity'] = 'admin/admins/get_activity';
$route['monitor/logins'] = 'admin/admins/get_login_stats';
$route['monitor/signups'] = 'admin/admins/get_signups';
$route['monitor/payments'] = 'admin/admins/payments';
$route['monitor/premium_members'] = 'admin/admins/premium_members';
$route['monitor/purchases'] = 'admin/admins/purchases';
$route['monitor/payments/pay/(\w+)'] = 'admin/admins/make_payments/$1';
$route['monitor/writers'] = 'admin/admins/writers_view';
$route['monitor/writers/approve/(\w+)/(\d+)'] = 'admin/admins/writers_approve/$1/$2';
$route['monitor/writers/details/(\w+)'] = 'admin/admins/writer_details_view/$1';

$route['check_out/(\w+)/(.*)/(\d+)'] = 'purchases/checkout/$1/$2/$3';
$route['check_out/(\w+)/(.*)'] = 'memberships/checkout/$1/$2';

$route['book/test/(.*)/(\d+)'] = 'controller_books/view/$1/$2';

$route['search'] = 'search_results/index';

$route['verify_email'] = 'users/verify_email';

$route['get_verified'] = 'users/get_verified';
$route['get_verified/info'] = 'writers/index';

$route['authors'] = 'users/view_all_users';
$route['books'] = 'books/view_all';
$route['book/(.*)/(\d+)'] = 'books/view/$1/$2';
$route['books/edit/(.*)/(\d+)'] = 'books/edit/$1/$2';
$route['books/upload'] = 'books/save';

$route['account'] = 'users/account';
$route['profile'] = 'users/get_profile';

$route['register'] = 'users/register';
$route['login'] = 'users/login';
$route['access'] = 'access/redirect_state';
$route['access/(\w+)'] = 'access/redirect_state/$1';

$route['download/(\w+)/(.*)/(\d+)'] = 'downloads/get/$1/$2/$3';

// Premium
$route['premium'] = 'memberships/get_offers';
$route['purchases'] = 'purchases/view';
$route['purchases/notify'] = 'purchases/notify';

$route['cart'] = 'carts/get_cart';
$route['cart/edit'] = 'carts/get_cart';
$route['cart/add/(\w+)/(.*)/(\d+)'] = 'carts/add_item/$1/$2/$3';
$route['cart/remove'] = 'carts/remove_items';

$route['recent'] = 'recents/index';
$route['recent/remove/(.*)/(\d+)/(.*)'] = 'recents/remove_item/$1/$2/$3';

$route['favourites'] = 'favourites/index';
$route['favourites/add/(.*)/(\d+)/(.*)'] = 'favourites/add_remove_item/$1/$2/$3';
$route['favourites/remove/(.*)/(\d+)/(.*)'] = 'favourites/add_remove_item/$1/$2/$3';

$route['publications/create'] = 'publications/save';
$route['publications'] = 'folders/get_folders';
$route['publications/(\d+)/(.*)'] = 'folders/get_folder_items';
$route['publications/page'] = 'folders/get_folders';
$route['publications/page/(\d+)'] = 'folders/get_folders';

$route['folders/add_item/(.*)/(\d+)'] = 'folders/add_item/$1/$2/';
$route['folders/(\d+)/remove_item/(.*)/(\d+)'] = 'folders/modify_folder_item/$1/$2/';

$route['folders/(\d+)/edit/(.*)/(\d+)/'] = 'folders/save_folder/$1/$2/';

$route['folders/edit/(\d+)'] = 'folders/save/$1/$2/';
$route['folders/create'] = 'folders/save';

$route['folders/edit/(.*)/(\d+)/'] = 'folders/save_folder/$1/$2/';
$route['folders'] = 'folders/save_folder/$1/$2/';

$route['password_reset'] = 'passwords/view';
$route['password_reset/(\d+)'] = 'passwords/reset_password/$1';

$route['(.*)/(\d+)/folder'] = 'folders/save_folder/$1/$2/';
$route['(.*)/(\d+)/folder/(.*)'] = 'folders/save_folder/$1/$2/';

$route['(\w+)/publications'] = 'folders/get_folders/$1';
$route['(\w+)/publications/page/(\d+)'] = 'folders/get_folders/$1';

$route['publications/edit/(.*)/(\d+)'] = 'publications/edit/$1/$2/';
$route['publications/delete/(.*)/(\d+)'] = 'publications/delete/$1';
$route['publications/delete/(.*)/(\d+)/(.*)'] = 'publications/delete/$1/$2';
$route['publications/delete/(.*)/(\d+)/(.*)'] = 'publications/delete/$1/$2';
$route['(\w+)/(\d+)/edit'] = 'publications/edit/$1/$2/';

$route['settings'] = 'users/settings';
$route['settings/edit/(\w+)'] = 'users/edit_settings/$1';

$route['(.*)/(\d+)'] = 'publications/get/$1/$2/';

// User profile
$route['(\w+)'] = 'users/get_profile/$1';
$route['profile/edit/(\w+)'] = 'users/edit_profile/$1';

//$route['default_controller'] = 'books/view_all';

$route['default_controller'] = 'pages/home';
//$route['default_controller'] = 'folders/get_folders';

// $route['404_override'] = '';
$route['(.*)'] = 'errors/show_404';
$route['translate_uri_dashes'] = FALSE;
