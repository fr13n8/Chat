<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Messages;
use App\Members;
use App\Events\MessageSend;

class MessagesController extends Controller
{

    // public $makeHidden;

    public function __construct()
    {
        // $this->makeHidden = ['password', 'email_verified_at', 'remember_token', 'created_at', 'updated_at', 'verified'];
    }

    public function fetchMessages(Request $req)
    {
        $member = Messages::where([
            'room_id' => $req->room_id
        ])->get();
		/* dd($member); */
        foreach ($member as $value) {
            $value->user;
            $value->photos;
        }
        // dd($member);
        return response()->json($member);
    }

    public function sendMessage(Request $req)
    {
        try {
            $data = auth()->userOrFail();
            // $data = $data->makeHidden($this->makeHidden);
        } catch (\Tymon\JWTAuth\Exceptions\UserNotDefinedException $e) {
            $data = $e;
        }
        // $data = $data->joinedRooms;
        // dd($data->messages);
        // dd($req->all());
        //dd($req->all());
        $message = $data->messages()->create([
            'message' => $req->message,
            'user_id' => $data->id,
            'room_id' => $req->room_id
        ]);
        // $message->user();
        // $message = Messages::create([
        //     'message' => $req->message,
        //     'member_id' => Members::where([
        //         "user_id" => $data->id,
        //         "room_id" => $req->room_id
        //     ])->value("id")
        // ]);
        // dd($message);
        $message->user;
        $message->photos;
        // $data = [
        //     "message" => $message,
        //     "room_id" => $req->room_id
        // ];
        broadcast(new MessageSend($message, $data))->toOthers();

        return response()->json([
            "status" => "success"
        ]);
    }
}
