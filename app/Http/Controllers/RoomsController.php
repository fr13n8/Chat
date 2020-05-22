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
		$request = json_decode($req->newRoom, 1);
		//dd($request);
        $validator =  Validator::make($request, [
            'name' => ['required', 'string', 'min:5', 'max:40', 'unique:rooms,name'],
            'description' => ['string', 'min:10']
        ]);



        if($validator->fails())
        {
            return response()->json(['message' => 'error',
									 'body' => $validator->messages(),
									]);
        }
        else
        {
			$upload_path = public_path('images/RoomImgs');
			//$file_name = $req->photo->getClientOriginalName();
			$generated_new_name = time() . '.' . $req->photo->getClientOriginalExtension();
			$req->photo->move($upload_path, $generated_new_name);
            // return response()->json($user);
            $user = $this->getUser();
            // dd($user);
            $room = new Rooms();
            $room->name = $request["name"];
            $room->description = $request["description"];
            $room->admin_id = $user->id;
			$room->photo = $generated_new_name;
            $room->save();
            $member = new Members();
            $member->user_id = $user->id;
            $member->room_id = $room->id;
            $member->save();
            return response()->json(['message' => 'success']);
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
	
	public function deleteRoom(Request $req)
	{
		Rooms::where("id", $req->roomId)->delete();
		return response()->json(['message' => 'success']);
	}
}
