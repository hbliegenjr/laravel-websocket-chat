
<?php


use App\Events\Chat;
use App\Events\MessageSent;





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

Route::get('/', function() {
    broadcast(new Chat('some data'));

    return view('welcome');

});

Route::get('/chats', 'App\Http\Controllers\ChatsController@index');

Route::get('/messages', 'App\Http\Controllers\ChatsController@fetchMessages');

Route::post('/messages', 'App\Http\Controllers\ChatsController@sendMessage');

Auth::routes();

Route::get('/home', 'App\Http\Controllers\HomeController@index')->name('home');
