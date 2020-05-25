<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Messages;
use App\Members;
use App\MessageImgs;
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
        $request = json_decode($req->msgDetails, 1);
        $message = $data->messages()->create([
            'message' => $request["message"],
            'user_id' => $data->id,
            'room_id' => $request["room_id"]
        ]);
        if($req->files){
            $upload_path = public_path('images/MsgAttachments/Images');
            //$file_name = $req->photo->getClientOriginalName();
            foreach($req->files as $img){
                $newImg = new MessageImgs();
                $generated_new_name = rand() . time() . '.' . $img->getClientOriginalExtension();
                $img->move($upload_path, $generated_new_name);
                $newImg->path = $generated_new_name;
                $newImg->message_id = $message->id;
                $newImg->save();
            }
        }
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
        broadcast(new MessageSend($message, $data));

        return response()->json([
            "status" => "success"
        ]);
    }
}
