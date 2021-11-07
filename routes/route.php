<?php
// route group "/user" 
$router->get("/user",'userController@index');
$router->post("/user/add",'userController@postAdd');
$router->get("/user/edit.*?",'userController@getEdit');
$router->post("/user/edit.*?",'userController@postEdit');
$router->get("/user/delete.*?",'userController@delete');
$router->post('/login','userController@login');
$router->get('/register','userController@postAdd');
// route group "/country"
$router->get("/country",'countryController@index');
$router->post("/country/add",'countryController@postAdd');
$router->get("/country/edit.*?",'countryController@getEdit');
$router->post("/country/edit.*?",'countryController@postEdit');
$router->get("/country/delete.*?",'countryController@delete');
// route group category
$router->get("/category",'categoryController@index');
$router->post("/category/add",'categoryController@postAdd');
$router->get("/category/edit.*?",'categoryController@getEdit');
$router->post("/category/edit.*?",'categoryController@postEdit');
$router->get("/category/delete.*?",'categoryController@delete');
// route group city
$router->get("/city",'cityController@index');
$router->post("/city/add",'cityController@postAdd');
$router->get("/city/edit.*?",'cityController@getEdit');
$router->post("/city/edit.*?",'cityController@postEdit');
$router->get("/city/delete.*?",'cityController@delete');
// route group blog
$router->get("/blog",'blogController@index');
$router->post("/blog/add",'blogController@postAdd');
$router->get("/blog/edit.*?",'blogController@getEdit');
$router->post("/blog/edit.*?",'blogController@postEdit');
$router->get("/blog/delete.*?",'blogController@delete');
// route group place
$router->get("/place",'placeController@index');
$router->post("/place/add",'placeController@postAdd');
$router->get("/place/edit.*?",'placeController@getEdit');
$router->post("/place/edit.*?",'placeController@postEdit');
$router->get("/place/delete.*?",'placeController@delete');
// route group transaction
$router->get("/transaction.*?",'transactionController@index'); // @param id_user
$router->post("/transaction/add.*?",'transactionController@postAdd'); // @param id_place
$router->get("/transaction/edit.*?",'transactionController@getEdit');// @param id_transaction
$router->post("/transaction/edit.*?",'transactionController@postEdit');// @param  id_transaction
// route group wishlist
$router->get("/wishlist.*?",'wishlistController@index'); // @param id_user
$router->post("/wishlist/add.*?",'wishlistController@postAdd'); // @param id_place
$router->get("/wishlist/delete.*?",'wishlistController@getEdit');// @param id_wishlist
// route group review
$router->get("/review.*?",'reviewController@index'); // @param id_place
$router->post("/review/add.*?",'reviewController@postAdd'); // @param id_place
?>