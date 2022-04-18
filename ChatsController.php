<?php
namespace App\Http\Controllers;

use App\User;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Events\MessageSent;
use App\Events\Chat;

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

    public function sendMessage(Request $request)
	 
    {  

            $message = auth()->user()->messages()->create([
	            'message' => $request->message
	        ]);

	    broadcast(new MessageSent($message->load('user')))->toOthers();
	        return ['status' => 'success'];
	    
    }  
    

}


