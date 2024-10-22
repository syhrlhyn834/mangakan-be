<?php

use Illuminate\Support\Facades\Route;

//group route with prefix "admin"
Route::prefix('admin')->group(function () {

    //route login
    Route::post('/login', [App\Http\Controllers\Api\Admin\LoginController::class, 'index']);

    //group route with middleware "auth"
    Route::group(['middleware' => 'auth:api'], function() {

        //data user
        Route::get('/user', [App\Http\Controllers\Api\Admin\LoginController::class, 'getUser']);

        //refresh token JWT
        Route::get('/refresh', [App\Http\Controllers\Api\Admin\LoginController::class, 'refreshToken']);

        //logout
        Route::post('/logout', [App\Http\Controllers\Api\Admin\LoginController::class, 'logout']);

        //Type
        Route::apiResource('/types', App\Http\Controllers\Api\Admin\TypeController::class);

        //Author
        Route::apiResource('/authors', App\Http\Controllers\Api\Admin\AuthorController::class);

        //Series
        Route::apiResource('/series', App\Http\Controllers\Api\Admin\SeriesController::class);

        //Group
        Route::apiResource('/groups', App\Http\Controllers\Api\Admin\GroupController::class);

        //Character
        Route::apiResource('/characters', App\Http\Controllers\Api\Admin\CharacterController::class);

        //Manga
        Route::apiResource('/mangas', App\Http\Controllers\Api\Admin\MangaController::class);

        //Genre
        Route::apiResource('/genres', App\Http\Controllers\Api\Admin\GenreController::class);

        //chapters
        Route::apiResource('/chapters', App\Http\Controllers\Api\Admin\ChapterController::class);


    });

});
