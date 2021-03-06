<?php
// route group "/user" 
$router->get("/user", 'userController@index'); // get jwt
$router->get("/users.*?", 'userController@list');
$router->get("/user/get.*?", 'userController@getUser');
$router->post("/user/add", 'userController@postAdd');
$router->get("/user/edit", 'userController@getEdit'); //req[id]
$router->post("/user/edit", 'userController@postEdit'); // req[id]
$router->post("/user/update", 'userController@update'); // admin update
$router->post("/user/updateInfo", 'userController@updateInfo'); // req[id]
$router->post('/user/updatePassword', 'userController@updatePassword');
$router->post("/user/delete", 'userController@delete'); // req[id]
$router->post('/user/forget', 'userController@forget');
$router->get("/user/pages.*?", 'userController@page');
// wishlist
$router->post("/wishlist/toggle", 'wishlistController@toggle'); // @param id_place
$router->post("/wishlist/delete", 'wishlistController@getEdit'); // @param id_wishlist
$router->get("/wishlist/isInWishList.*?", 'wishlistController@isInWishList'); // @param id_user
$router->get("/wishlist.*?", 'wishlistController@index'); // @param id_user
// authentication
$router->post('/login', 'userController@login');
$router->post('/register', 'userController@register');
// route group "/country"
$router->get("/countries.*", 'countryController@index');
$router->post("/country/add", 'countryController@postAdd');
$router->get("/country/cities.*", 'countryController@getCities');
$router->get("/country/edit", 'countryController@getEdit'); //req[id]
$router->post("/country/edit", 'countryController@postEdit'); //req[id]
$router->post("/country/delete", 'countryController@delete'); //req[id]
$router->get("/country/pages.*", 'countryController@page');
// route group city
$router->post("/city/add", 'cityController@postAdd');
$router->post("/city/edit", 'cityController@postEdit'); //req[id]
$router->post("/city/delete", 'cityController@delete'); //req[id]
$router->get("/city/pages.*?", 'cityController@page');
$router->get("/city/search.*?", 'cityController@search');
$router->get("/cities.*?", 'cityController@index');
$router->get("/city.*?", 'cityController@get');
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
$router->post("/transaction/edit", 'transactionController@postEdit'); // @param  id_transaction
$router->get("/transaction/get.*?", 'transactionController@get');
$router->get("/transaction/user.*?", 'transactionController@user');
$router->get("/transactions.*?", 'transactionController@index');
// route group wishlist
// route group review
$router->get("/review/user.*?", 'reviewController@user');
$router->get("/review/place.*?", 'reviewController@getByPlace'); // @param id_place
$router->post("/review/add", 'reviewController@postAdd'); // @param id_place
$router->get('/review/check.*?', 'reviewController@check');
$router->post('/review/delete', 'reviewController@delete');
$router->get('/reviews.*?', 'reviewController@index');
// route group notify
// search 
$router->post("/search", 'searchController@search');
$router->post("/city/search", 'searchController@searchInCity');
//pages
$router->get("/pages", "pageController@index");
$router->post("/page/update", "pageController@update");
//app
$router->post("/app/contact", "appController@contact");
$router->get("/app/stats.*?", "appController@stats");
