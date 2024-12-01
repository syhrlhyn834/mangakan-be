<?php

use App\Http\Controllers\Api\Admin\AuthorController;
use App\Http\Controllers\Api\Admin\CharacterController;
use App\Http\Controllers\Api\Admin\GenreController;
use App\Http\Controllers\Api\Admin\GroupController;
use App\Http\Controllers\Api\Admin\MangaController;
use App\Http\Controllers\Api\Admin\SeriesController;
use Illuminate\Support\Facades\Route;


//group route with prefix "admin"
Route::prefix('admin')->group(function () {

    //route login
    Route::post('/login', [App\Http\Controllers\Api\Admin\LoginController::class, 'index']);

    //group route with middleware "auth"
    Route::group(['middleware' => 'auth:api'], function() {

        //data user
        Route::get('/user', [App\Http\Controllers\Api\Admin\LoginController::class, 'getUser']);

        Route::apiResource('/users', App\Http\Controllers\Api\Admin\UserController::class);


        //refresh token JWT
        Route::get('/refresh', [App\Http\Controllers\Api\Admin\LoginController::class, 'refreshToken']);

        //logout
        Route::post('/logout', [App\Http\Controllers\Api\Admin\LoginController::class, 'logout']);



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

        //show genres
        Route::get('/authorsView', [AuthorController::class, 'authorView']);
        Route::get('/charactersView', [CharacterController::class, 'characterView']);
        Route::get('/groupsView', [GroupController::class, 'groupView']);
        Route::get('/mangasView', [MangaController::class, 'mangaView']);
        Route::get('/seriesView', [SeriesController::class, 'seriesView']);
        Route::get('/genresView', [GenreController::class, 'genresView']);


        //chapters
        Route::apiResource('/chapters', App\Http\Controllers\Api\Admin\ChapterController::class);

        //headers
        Route::apiResource('/headers', App\Http\Controllers\Api\Admin\HeaderController::class);

        //footers
        Route::apiResource('/footers', App\Http\Controllers\Api\Admin\FooterController::class);

        //dashboard
        Route::get('/dashboard', [App\Http\Controllers\Api\Admin\DashboardController::class, 'index']);

    });

});
//group route with prefix "web"
Route::prefix('web')->group(function () {

    //index headers
    Route::get('/headers', [App\Http\Controllers\Api\Web\HeaderController::class, 'index']);

    //index headers
    Route::get('/footers', [App\Http\Controllers\Api\Web\FooterController::class, 'index']);

    //index authors
    Route::get('/authors', [App\Http\Controllers\Api\Web\AuthorController::class, 'index']);

    //show authors
    Route::get('/authors/{slug}', [App\Http\Controllers\Api\Web\AuthorController::class, 'show']);

    //index character
    Route::get('/characters', [App\Http\Controllers\Api\Web\CharacterController::class, 'index']);

    //show character
    Route::get('/characters/{slug}', [App\Http\Controllers\Api\Web\CharacterController::class, 'show']);

    //index genres
    Route::get('/genres', [App\Http\Controllers\Api\Web\GenreController::class, 'index']);

    //show genres
    Route::get('/genres/{slug}', [App\Http\Controllers\Api\Web\GenreController::class, 'show']);

    //index groups
    Route::get('/groups', [App\Http\Controllers\Api\Web\GroupController::class, 'index']);

    //show groups
    Route::get('/groups/{slug}', [App\Http\Controllers\Api\Web\GroupController::class, 'show']);

    //index mangas
    Route::get('/mangas', [App\Http\Controllers\Api\Web\MangaController::class, 'index']);

    //index mangas
    Route::get('/filterSearch', [App\Http\Controllers\Api\Web\MangaController::class, 'filterSearch']);

    //show mangas
    Route::get('/mangas/{slug}', [App\Http\Controllers\Api\Web\MangaController::class, 'show']);

    Route::get('/manhwaHome', [App\Http\Controllers\Api\Web\MangaController::class, 'manhwaHome']);

    Route::get('/doujinHome', [App\Http\Controllers\Api\Web\MangaController::class, 'doujinHome']);

    Route::get('/mangaHome', [App\Http\Controllers\Api\Web\MangaController::class, 'mangaHome']);

    Route::get('/mangaPublishing', [App\Http\Controllers\Api\Web\MangaController::class, 'mangaPublishing']);

    Route::get('/mangaFinished', [App\Http\Controllers\Api\Web\MangaController::class, 'mangaFinished']);

    Route::get('/doujinmangaHome', [App\Http\Controllers\Api\Web\MangaController::class, 'doujinmangaHome']);

    //index series
    Route::get('/series', [App\Http\Controllers\Api\Web\SeriesController::class, 'index']);

    //show series
    Route::get('/series/{slug}', [App\Http\Controllers\Api\Web\SeriesController::class, 'show']);




    //index chapter
    Route::get('/chapters', [App\Http\Controllers\Api\Web\ChapterController::class, 'index']);

    //show chapter
    Route::get('/chapters/{slug}', [App\Http\Controllers\Api\Web\ChapterController::class, 'show']);

});
