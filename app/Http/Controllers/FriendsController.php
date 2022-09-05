<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Friends;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FriendsController extends Controller
{
function alluser(){
    $user = User:: select('name', 'email', 'id')->where('id', '!=', Auth::id())
    ->get();
    $friends = $this->index();
    $friends_id =[];
    foreach($friends as $friend){
        array_push($friends_id, $friend->id);
    }
    $users= $user->whereNotIn('id', $friends_id)->values();

    return response(['user'=>$user, 'users'=>$users], 201);
}

public function index(){
    $user_id = Auth::id();

    $friends1= Friends::where('user_id', '=', $user_id)->get();
        $allYourFriends1 = [];
               foreach(
                $friends1 as $friend1
               ){
            array_push($allYourFriends1,User::select('id', 'name')->where('id', '=', $friend1->friend_id)->first());
               }

$friends2= Friends::where('friend_id', '=', $user_id)->get();
$allYourFriends2 = [];
               foreach(
                $friends2 as $friend2
               ){
            array_push($allYourFriends2,User::select('id', 'name')->where('id', '=', $friend2->user_id)->first());
               }               
                   $allYourFriends = array_merge($allYourFriends1,$allYourFriends2);

                   return $allYourFriends;
}


    public function store(Request $request){
        $fields= $request->validate([
            "friend_id"=> 'required',]);
    
            $user_id = Auth::id();

            Friends::create([
'user_id'=> $user_id,
'friend_id'=>$fields['friend_id']]);

$friends1= Friends::where('user_id', '=', $user_id)->get();
        $allYourFriends1 = [];
               foreach(
                $friends1 as $friend1
               ){
            array_push($allYourFriends1,User::select('id', 'name')->where('id', '=', $friend1->friend_id)->first());
               }

$friends2= Friends::where('friend_id', '=', $user_id)->get();
        $allYourFriends2 = [];
               foreach(
                $friends2 as $friend2
               ){
            array_push($allYourFriends2,User::select('id', 'name')->where('id', '=', $friend2->user_id)->first());
               }               
                   $allYourFriends = array_merge($allYourFriends1,$allYourFriends2);

                   return $allYourFriends;
    }

    function search(Request $request){
        $fields= $request->validate([
        "name"=> 'required']);
        $friends = $this->index();
        $friends_id =[];
        foreach($friends as $friend){
            array_push($friends_id, $friend->id);
        }

        $users = User::select('name', 'email','id')->where('id', '!=', Auth::id())
        ->where('name', 'like', '%'.$fields['name'].'%')->get()
        ->whereNotIn('id', $friends_id)->values();

        return response([
            'users'=>$users
        ],201);
    }
}
