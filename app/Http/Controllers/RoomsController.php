<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\User;
use App\Rooms;
use App\Members;

class RoomsController extends Controller
{
    public function getUser()
    {
        try {
            $user = auth()->userOrFail();
            // dd($user);
            // $user = $user->makeHidden($this->makeHidden);
        } catch (\Tymon\JWTAuth\Exceptions\UserNotDefinedException $e) {
            $user = $e;
            // dd($user);
            return response()->json(['message' => 'refresh']);
        }
        return $user;
    }

    public function addRoom(Request $req)
    {
        $validator =  Validator::make($req->all(), [
            'name' => ['required', 'string', 'min:5', 'max:40', 'unique:rooms,name'],
            'description' => ['required', 'string', 'min:5']
        ]);

        if($validator->fails())
        {
            // dd(54);
        }
        else
        {
            // return response()->json($user);
            $user = $this->getUser();
            // dd($user);
            $room = new Rooms();
            $room->name = $req->name;
            $room->description = $req->description;
            $room->admin_id = $user->id;
            $room->save();
            $member = new Members();
            $member->user_id = $user->id;
            $member->room_id = $room->id;
            $member->save();
            return "ok";
        }
    }

    public function fetchRooms(Request $req)
    {
		$rooms = Rooms::get();
		foreach($rooms as $room){
			$room->members;
		}
        return $rooms;
    }

    public function joinRoom(Request $req)
    {
        $user = $this->getUser();

        $member = Members::create([
            "user_id" => $user->id,
            "room_id" => $req->room_id
        ]);
    }
	
	public function leaveRoom(Request $req)
	{
		$user = $this->getUser();
		
		Members::where([
			"user_id" => $user->id,
			"room_id" => $req->room_id
		])->delete();
	}
}
