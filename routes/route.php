<?php
// route group "/user" 
$router->get("/user", 'userController@index'); // get jwt
$router->get("/users.*?", 'userController@list');
$router->post("/user/add", 'userController@postAdd');
$router->get("/user/edit", 'userController@getEdit'); //req[id]
$router->post("/user/edit", 'userController@postEdit'); // req[id]
$router->post("/user/update", 'userController@update'); // admin update
$router->post("/user/updateInfo", 'userController@updateInfo'); // req[id]
$router->post('/user/updatePassword', 'userController@updatePassword');
$router->post("/user/delete", 'userController@delete'); // req[id]
// get pages user
$router->get("/user/pages.*?", 'userController@page');
// wishlist
$router->post("/wishlist/toggle", 'wishlistController@toggle'); // @param id_place
$router->post("/wishlist/delete", 'wishlistController@getEdit'); // @param id_wishlist
$router->get("/wishlist/isInWishList.*?", 'wishlistController@isInWishList'); // @param id_user
$router->get("/wishlist.*?", 'wishlistController@index'); // @param id_user

$router->post('/login', 'userController@login');
$router->post('/register', 'userController@register');
$router->post('/forget', 'userController@forget');
// route group "/country"
$router->get("/countries.*", 'countryController@index');
$router->post("/country/add", 'countryController@postAdd');
$router->get("/country/cities.*", 'countryController@getCities');
$router->get("/country/edit", 'countryController@getEdit'); //req[id]
$router->post("/country/edit", 'countryController@postEdit'); //req[id]
$router->post("/country/delete", 'countryController@delete'); //req[id]
$router->get("/country/pages.*", 'countryController@page');
// route group category
$router->get("/categories", 'categoryController@index');
$router->post("/category/add", 'categoryController@postAdd');
$router->get("/category/edit", 'categoryController@getEdit'); //req[id]
$router->post("/category/edit", 'categoryController@postEdit'); //req[id]
$router->get("/category/delete", 'categoryController@delete'); //req[id]
$router->get("/category/pages", 'categoryController@page');
// route group city
$router->post("/city/add", 'cityController@postAdd');
$router->post("/city/edit", 'cityController@postEdit'); //req[id]
$router->post("/city/delete", 'cityController@delete'); //req[id]
$router->get("/city/pages.*?", 'cityController@page');
$router->get("/city/search.*?", 'cityController@search');
$router->get("/cities.*?", 'cityController@index');
$router->get("/city.*?", 'cityController@get');
// route group blog
$router->get("/blogs", 'blogController@index');
$router->post("/blog/add", 'blogController@postAdd');
$router->get("/blog/edit", 'blogController@getEdit'); //req[id]
$router->post("/blog/edit", 'blogController@postEdit'); //req[id]
$router->get("/blog/delete", 'blogController@delete'); //req[id]
$router->get("/blog/pages", 'blogController@page');
// route group place
$router->post("/place/add", 'placeController@postAdd');
$router->post("/place/edit", 'placeController@postEdit'); //req[id]
$router->post("/place/delete", 'placeController@delete'); //req[id]
$router->get("/place/search.*?", 'placeController@search'); //req[id]
$router->get("/place/pages.*?", 'placeController@pages');
$router->get("/place/statistics.*?", 'placeController@getStatistic');
$router->get("/places.*?", 'placeController@index'); // list all place
$router->get("/place.*?", 'placeController@index'); // list all place
// route group transaction
$router->post("/transaction/add", 'transactionController@postAdd'); // @param id_place
$router->get("/transaction/edit", 'transactionController@getEdit'); // @param id_transaction
$router->post("/transaction/edit", 'transactionController@postEdit'); // @param  id_transaction
$router->get("/transaction/get.*?", 'transactionController@get');
$router->get("/transaction/user.*?", 'transactionController@user');
$router->get("/transactions", 'transactionController@index');
// route group wishlist
// route group review
$router->get("/review/user.*?", 'reviewController@user');
$router->get("/review/place.*?", 'reviewController@index'); // @param id_place
$router->post("/review/add", 'reviewController@postAdd'); // @param id_place
// route group report
$router->get("/reports", 'reportController@index');
$router->post("/report/add", 'reportController@postAdd'); // @param id_agency
// route group notify
$router->get("/notifies", 'notifyController@index');
// search 
$router->post("/search", 'searchController@search');
$router->post("/city/search", 'searchController@searchInCity');
//pages
$router->get("/pages", "pageController@index");
$router->post("/page/update", "pageController@update");
