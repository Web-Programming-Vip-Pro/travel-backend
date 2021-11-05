<?php
// route group "/user" 
$router->get("/user",'userController@index');
$router->post("/user/add",'userController@postAdd');
$router->get("/user/edit.*?",'userController@getEdit');
$router->post("/user/edit.*?",'userController@postEdit');
$router->get("/user/delete.*?",'userController@delete');
$router->post('/login','userController@login');
$router->get('/logout','userController@logout');
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
?>