<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\GroupMembers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GroupMembersController extends Controller
{
    function members(Request $request){
        $fields= $request->validate([
            'group_id'=> 'required',
            ] );

            $members = GroupMembers::select('*')->where('group_id', '=', $fields['group_id'])
            ->where('member_id','!=', Auth::id())->get()->map(function($item){
                $user = User::where('id', '=', $item->member_id)->first();

                return[
'group_id'=> $item->group_id,
'id'=>$item->member_id,
'name'=>$user->name
                ];
            });
            return response(['members'=>$members], 201);

    }
}
