<?php

namespace App\Http\Controllers;

use App\Models\Groups;
use App\Models\GroupMembers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GroupController extends Controller
{
    function index(){
        $user_id = Auth::id();
        $groups_items = GroupMembers::select('group_id')->where('member_id', '=', $user_id)->get();
        $groups_id= $groups_items->pluck('group_id')->toArray();
        array_push($groups_id, $user_id);
        $groups = Groups::whereIn('id', $groups_id)->get();
    return response([
        'groups'=>$groups,
    ], 201);
    }
    function store(Request $request){
        $fields= $request->validate([
            "title"=> 'required',
            "members_id"=>"required",
            ] );

            $user_id = Auth::id();

           $group= Groups::create(
              [
                'user_id'=>$user_id,
                'title'=>$fields['title']
              ]  
            );
            $members_id = $fields['members_id'];

            foreach($members_id as $member_id){
GroupMembers::create(
    [
        'group_id'=>$group->id,
        'member_id'=>$member_id
    ]
    );}
    GroupMembers::create(
        [
            'group_id'=>$group->id,
            'member_id'=>$user_id
        ]
        );


    $groups_items = GroupMembers::select('group_id')->where('member_id', '=', $user_id)->get();
    $groups_id= $groups_items->pluck('group_id')->toArray();
    array_push($groups_id, $user_id);
    $groups = Groups::whereIn('id', $groups_id)->get();

    return response([
        'groups'=>$groups,
    ], 201);

    }
}
