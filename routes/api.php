<?php

Route::post('login', 'AuthController@login');
Route::post('logout', 'AuthController@logout');
Route::post('register', 'AuthController@register');


Route::post('/productoventa','CajaController@productoventa');

Route::post('/buscar','CajaController@buscar');

Route::post('/sentData','CajaController@sentData'); 


Route::post('/facturador','CajaController@facturador'); 

Route::get('documento-formato/{radio}','CajaController@documento_formato');

Route::get('/facturados/{type}','CajaController@facturados');

Route::post('/baja','CajaController@baja');