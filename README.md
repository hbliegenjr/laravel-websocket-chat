# laravel-websocket-chat
CMSC 207

	A. LARAVEL WEBSOCKET SERVER INSTALLATION & CONFIGURATION
	1. Create the project
	sudo compose create-project --prefer-dist laravel/laravel laravel-websocket
	2. Change directory to the project
	cd laravel-websocket
	3. Install Websockets package
	composer require beyondcode/laravel-websockets
	4. Publish migration file
	php artisan vendor:publish --provider="BeyondCode\LaravelWebSockets\WebSocketsServiceProvider"--tag="migrations"
	5. Run the migration command
	php artisan migrate
	6. Create a database, localhost username, and password (may use Php MyAdmin)
	7. Input database in the .env file (may use VSCode)
	
	DB_CONNECTION=mysql
	DB_HOST=127.0.0.1
	DB_PORT=3308
	DB_DATABASE=websocket
	DB_USERNAME=hbliegenjr
	DB_PASSWORD=Password*123
	
	8. Publish Websocket config file
	php artisan vendor:publish --provider="BeyondCode\LaravelWebSockets\WebSocketsServiceProvider"--tag="config"
	9. Configure .env file
	PUSHER_APP_ID=anyID
	PUSHER_APP_KEY=anyKey
	PUSHER_APP_SECRET=anySecret
	PUSHER_APP_CLUSTER=mt1
	
	10. Run Laravel server
	php artisan serve
	11. Enter IP in the browser to test installation success (127.0.0.1:8000)
	12. Enter the following in the browser to register necessary routes for the package
	127.0.0.1:8000/laravel-websockets
	13. Install Laravel Pusher (if not installed)
	composer require pusher/pusher-php-server "~7.0"
	14. Configure .env file
	BROADCAST_DRIVER=pusher
	15. Configure Pusher (config/broadcasting.php)
	'pusher'=> [
    'driver'=> 'pusher',
    'key'=> env('PUSHER_APP_KEY'),
    'secret'=> env('PUSHER_APP_SECRET'),
    'app_id'=> env('PUSHER_APP_ID'),
    'options'=> [
        'cluster'=> env('PUSHER_APP_CLUSTER'),
        'encrypted'=> true,
        'host'=> '127.0.0.1',
        'port'=> 6001,
        'scheme'=> 'http'],
],
	16. Setup Laravel Echo
	npm install
	npm install laravel-echo pusher-js
	17. Configure/Uncomment Laravel Echo (resources/js/bootstrap.js) (bottom part)
	
	 import Echo from 'laravel-echo';
	 window.Pusher = require('pusher-js');
	 window.Echo = new Echo({
	     broadcaster: 'pusher',
	     key: process.env.MIX_PUSHER_APP_KEY,
	     wsHost: window.location.hostname,
	     wsPort: 6001,
	     disableStats: true,
	  forceTLS: false
		
	 });
	 window.Echo.channel('DemoChannel')
	 .listen('Chat', (e) => {
	     console.log(e);
	 });
	
	18. Compile config files
	npm run watch
	19. Run in another terminal
	php artisan websocket:serve
	20. Run in another terminal
	php artisan serve
	21. Open in browser
	http://127.0.0.1:8000/laravel-websockets
	22. Click connect

	22. Create event
	php artisan make:event (nameofevent)
	23. Edit event (app/events/nameofevent
	
	<?php
	namespace App\Events;
	use Illuminate\Broadcasting\Channel;
	use Illuminate\Broadcasting\InteractsWithSockets;
	use Illuminate\Broadcasting\PresenceChannel;
	use Illuminate\Broadcasting\PrivateChannel;
	use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
	use Illuminate\Foundation\Events\Dispatchable;
	use Illuminate\Queue\SerializesModels;
	class nameofevent implements ShouldBroadcast
	{
	    use Dispatchable, InteractsWithSockets, SerializesModels;
	    public $somedata;
	    /**
	     * Create a new event instance.
	     *
	     * @return void
	     */
	    public function __construct($somedata) {
	        $this->somedata = $somedata;
	    
	    }
	    /**
	     * Get the channels the event should broadcast on.
	     *
	     * @return \Illuminate\Broadcasting\Channel|array
	     */
	    public function broadcastOn()
	    {
	        return new Channel('DemoChannel');
	    }
	 
	}
	
	24. Edit web.php to broadcast event
	Route::get('/', function() {
	    broadcast(new nameofevent('some data'));
	
	25. Edit bootsrap.js
	
	window.Echo.channel('DemoChannel')
	 .listen('nameofevent', (e) => {
	     console.log(e);
	});
	
	26. Edit resources/views/welcome.blade.php (before </body> tag)
	<script src="js/app.js"> </script>
	
	27. Edit resources/views/welcome.blade.php (add meta above)
	        <meta name = "csrf-token" content ="{{csrf_token() }}" />

	28. Edit resources/views/welcome.blade.php (add app id)
	 <body>
	<div id="app" class="relative flex items-top justify-center min-h-screen bg-gray-100 dark:bg-gray-900 sm:items-center py-4 sm:pt-0">
	
	29. Compile config files
	npm run watch
	
	30. To test broadcast, open two browsers and open each of the following respectively:
		a. http://127.0.0.1:8000/
		b. http://127.0.0.1:8000/admin/websocket (then connect)
		
	31. Refresh a. above which will be logged in b. above
	
	
	
	B. CHAT CONFIGURATION & IMPLEMENTATION (LARAVEL WEBSOCKETS, VUE.JS, AND LARAVEL-ECHO
	
	1. Make a message model in the previous project (migration file for messages table)
	php artisan make:model Message -m

	2. Edit message table (database\migrations\_create_message_table.php
	
	Schema::create('messages', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned(); 
            $table->timestamps();
            $table->text('message');

	3. Run migration
	php artisan migrate
	
	4. Create user and messages eloquent relationship functions (app/models/user.php)
	
	 /**
	     * The attributes that should be hidden for serialization.
	     *
	     * @var array<int, string>
	     */
	    protected $hidden = [
	        'password',
	        'remember_token',
	    ];
	    public function messages()
	    {
	        return $this->hasMany(Message::class);
	    }
	
	5. Create user and messages eloquent relationship functions (app/models/message.php)
	
	class Message extends Model
	{
	    protected $fillable = ['message'];
	 
	    public function user()
	    {
	        return $this->belongsTo(User::class);
	    }
	}
	
	6. Create a chats route (routes/web.php)
	
	<?php
	
	use App\Events\Chat;
	
	Route::get('/', function () {
	    broadcast(new Chat('some data'));
	    return view('welcome');
	});
	
	Route::get('/chats', 'App\Http\Controllers\ChatsController@index');
	
	
	7. Create Chats controller
	php artisan make:controller ChatsController
	
	8. Edit Chats controller (App/Http/Controllers/ChatsController.php)
	
	<?php
	namespace App\Http\Controllers;
	use Illuminate\Http\Request;
	class ChatsController extends Controller
	{
	    public function __construct()
	    {
	        $this->middleware('auth');
	    }
	    public function index()
	    {
	        return view('chats');
	    }
	
	         public function fetchMessages()
	        {
	                 return Message::with('user')->get();
	        }
	 }
	
	
	9. Create chats blade file inside resources/views (resources/views/chats.blade.php)
	
	10. Generate laravel inbuilt authentication system
	composer require laravel/ui --dev
	php artisan ui vue --auth
	
	11. Copy content of app/resources/auth/login.blade.php
	
	12. Paste to chat.blade.php and delete lines 5 to 71 and edit
	
	@extends('layouts.app')
	@section('content')
	<div class="container">
	 
	<chats></chats>
	
	</div>
	@endsection
	
	13. Compile files
	npm run watch
	
	14. If error, run
	npm install laravel-mix@latest
	npm clean-install
	
	15. Rename js/component/ExampleComponent.vue to ChatsComponent.vue
	
	16. Edit filename in app.js
	
	Vue.component('chats', require('./components/ChatsComponent.vue').default);
	
	17. Run php artisan serve
	
	18. Go to the browser and enter the ff:
	http://127.0.0.1:8000/chats (sign in if needed)
	
	19. Edit ChatsComponent.vue (remove lines 2 to 14)
	
	<template>
	    <div class="row">
	        <div class="col-8">
	            <div class="card card-default">
	                <div class="card-header"> Messages </div>
	                <div class="card-body p-0"> 
	                
	                    <ul class="list-unstyled" style ="height:300px; overflow-y:scroll"> 
	                        
	                        <li class="p-2">
	                          <strong> Jun </strong>
	                          message text      
	                        </li>
	                    </ul>     
	                </div>        
	                <input 
	                    type="text" 
	                    name="message" 
	                    placeholder="Enter your message:" 
	                    class="form-control">
	            </div>
	            <span class="text-muted">User is typing...</span>
	        </div>
	        <div class="col-4">
	            <div class="card card-default">
	                <div class="card-header">Active Users</div>
	                <div class="card-body">
	                    <ul>
	                        <li class="py-2">Jun</li>
	                    </ul>
	                </div>
	            </div>
	        </div>
	    </div>        
	
	</template>
	<script>
	    export default {
	        mounted() {
	            console.log('Component mounted.')
	        }
	    }
	</script>
	
	20. Edit web.php (to fetch and send message)
	
	Route::get('/chats', 'App\Http\Controllers\ChatsController@index');
	Route::get('/messages', 'App\Http\Controllers\ChatsController@fetchMessages');
	Route::post('/messages', 'App\Http\Controllers\ChatsController@sendMessage');
	Auth::routes();
	
	21. Edit ChatsController.php
	
	public function index()
	    {
	        return view('chats');
	    }
	    public function fetchMessages()
	    {
	        return Message::with('user')->get();
	    }
	
	 public function sendMessage(Request $request)
	    {
	        auth()->user()->messages()->create([
	            'message' => $request->message
	        ]);
	        return ['status' => 'success'];
	    }
	 }
	
	22. Edit ChatsComponent.vue
	
	<template>
	    <div class="row">
	        <div class="col-8">
	            <div class="card card-default">
	                <div class="card-header"> Messages </div>
	                <div class="card-body p-0"> 
	                
	                    <ul class="list-unstyled" style ="height:300px; overflow-y:scroll"> 
	                        
	                        <li class="p-2" v-for="(message, index) in messages" :key="index">
	                          <strong>{{ message.user.name }} </strong>
	                          {{ message.message }}
	                                
	                        </li>
	                    </ul>     
	                </div>        
	                <input 
	                    @keyup.enter="sendMessage"
	                    v-model="newMessage"
	                    type="text" 
	                    name="message" 
	                    placeholder="Enter your message:" 
	                    class="form-control">
	            </div>
	            <!-- <span class="text-muted">User is typing...</span> -->
	        </div>
	        <div class="col-4">
	            <div class="card card-default">
	                <div class="card-header">Active Users</div>
	                <div class="card-body">
	                    <ul>
	                        <li class="py-2" v-for="(user, index) in users" :key="index" >
	                            {{ user.name }}
	                        </li>
	                    </ul>
	                </div>
	            </div>
	        </div>
	    </div>        
	
	</template>
	<script>
	    export default {
	        props:['user'],
	        data() {
	            return {
	                messages: [],
	                newMessage: '',
	                users:[]
	            }
	        },
	        created() {
	            this.fetchMessages();
	            Echo.join('chat')
	                .here(user => {
	                    this.users = user;
	                })
	                .joining(user => {
	                    this.users.push(user);
	                })
	                .leaving(user => {
	                    this.users = this.users.filter(u => u.id != user.id);
	                })
	                .listen('MessageSent', (event) => {
	                    this.messages.push(event.message);
	                });
	        },
	        methods: {
	            fetchMessages() {
	                 axios.get('messages').then(response=> {
	                this.messages = response.data;
	            })
	            },
	            sendMessage() {
	                this.messages.push({ 
	                    user: this.user,
	                    message: this.newMessage
	                })
	                axios.post('messages', {message: this.newMessage});
	                this.newMessage = '';
	            }
	        }
	    }
	</script>
	
	23. Edit chats.blade.php
	
	@extends('layouts.app')
	@section('content')
	<div class="container">
	 
	<chats :user="{{ auth()->user() }}"> </chats>
	</div>
	@endsection
	
	24. Make new event for broadcasting
	php artisan make:event MessageSent

	25. Edit MessageSent.php
	
	<?php
	namespace App\Events;
	use Illuminate\Broadcasting\Channel;
	use Illuminate\Broadcasting\InteractsWithSockets;
	use Illuminate\Broadcasting\PresenceChannel;
	use Illuminate\Broadcasting\PrivateChannel;
	use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
	use Illuminate\Foundation\Events\Dispatchable;
	use Illuminate\Queue\SerializesModels;
	class MessageSent implements ShouldBroadcast
	{
	    use Dispatchable, InteractsWithSockets, SerializesModels;
	    public $message;
	    /**
	     * Create a new event instance.
	     *
	     * @return void
	     */
	    public function __construct(Message $message)
	    {
	        $this->message = $message;
	    }
	    /**
	     * Get the channels the event should broadcast on.
	     *
	     * @return \Illuminate\Broadcasting\Channel|array
	     */
	    public function broadcastOn()
	    {
	        return new PresenceChannel('chat');
	    }
	}
	
	26. Create a channel route (apps\route\channels.php)
	
	<?php
	use Illuminate\Support\Facades\Broadcast;
	/*
	|--------------------------------------------------------------------------
	| Broadcast Channels
	|--------------------------------------------------------------------------
	|
	| Here you may register all of the event broadcasting channels that your
	| application supports. The given channel authorization callbacks are
	| used to check if an authenticated user can listen to the channel.
	|
	*/
	Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
	    return (int) $user->id === (int) $id;
	});
	Broadcast::channel('chat', function ($user) {
	    return $user;
	});
	
	27. Broadcast event (add to ChatsController.php)
	
	 public function sendMessage(Request $request)
	    {
	        $message = auth()->user()->messages()->create([
	            'message' => $request->message
	        ]);

	    broadcast(new MessageSent($message->load('user')))->toOthers();
	        return ['status' => 'success'];
	    }
	}
	
	
	28. Edit App\Http\Providers\BroadcastServiceProvider.php
	
	<?php
	namespace App\Providers;
	use Illuminate\Support\Facades\Broadcast;
	use Illuminate\Support\ServiceProvider;
	class BroadcastServiceProvider extends ServiceProvider
	{
	    /**
	     * Bootstrap any application services.
	     *
	     * @return void
	     */
	    public function boot()
	    {
	        Broadcast::routes();
	       /* Broadcast::routes(['middleware' => ['auth:admin']]);
	        */
	       require base_path('routes/channels.php');
	        
	    }
	}
	
	29. Run websockets server
	php artisan websocket:serve
	
	30. Open new incognito browser (http://127.0.0.1:8000/chats) then register new account
	
Navigate to chats page (http://127.0.0.1:8000/chats)![image](https://user-images.githubusercontent.com/91530882/163828799-dfd5e82c-c8ad-4a93-bb3c-54c8f27cccdc.png)
