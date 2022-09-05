<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Dashboard;
use App\Models\BillSplitter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class BillSplitterController extends Controller
{

    public function index( Request $request){
        $fields= $request->validate([
            'group_id'=>'required']);

        $user_id = Auth::id();
       $lent= Dashboard::select('debtor_id' , DB::raw('SUM(amount) as amount'))->
where('creditor_id', '=', $user_id)->where('group_id', '=', $fields['group_id'])
->groupBy('debtor_id')->get()->map(function ($l){
$user = User::where('id', '=', $l->debtor_id)->first();

return [
    'id'=>$l->debtor_id,
    'name'=>$user->name,
    'amount'=>$l->amount
];
});

$owed= Dashboard::select('creditor_id' , DB::raw('SUM(amount) as amount'))->
where('debtor_id', '=', $user_id)->where('group_id', '=', $fields['group_id'])
->groupBy('creditor_id')->get()->map(function ($o){
$user = User::where('id', '=', $o->creditor_id)->first();
return [
    'id'=>$o->creditor_id,
    'name'=>$user->name,
    'amount'=>$o->amount
];
});

$common_collection = $lent->whereIn('id', $owed->pluck('id')->toArray());

$collections = $common_collection->pluck('id')->toArray();

    foreach($collections as $collection){
        $owed_item = $owed->where('id', '=', $collection)->first();
        $lent_item = $lent->where('id', '=', $collection)->first();
        $searchl = $lent->pluck('id')->search($collection);
        $searcho = $owed->pluck('id')->search($collection);
       
        if(
            $lent_item['amount'] > $owed_item['amount']){
$lent_item['amount'] = $lent_item['amount']-$owed_item['amount'];

$lent = $lent->replace([$searchl=>$lent_item]);
$owed = $owed->forget($searcho)->values();

        }
        elseif($lent_item['amount'] < $owed_item['amount'])
        { $owed_item['amount'] = $owed_item['amount']-$lent_item['amount'];
            $owed = $owed->replace([$searcho=>$owed_item]);
        $lent = $lent->forget($searchl)->values();
                    }
                    else{
                        $lent = $lent->forget([$searchl+1]);
                        $owed = $owed->forget([$searcho+1]);
                    }
    }

// $common_collection->map(function($item){
//     $owed_item = $owed->get('id', 
//         $item->id);
//         $lent = $lent->get('id', 
//         $item->id);
// });

$billsplitter= BillSplitter::where('group_id', '=', $fields['group_id'])->get()->map(function($item){
    $user = User::where('id', '=', $item->user_id)->first();
    $item->name = $user->name;
return $item;});

        $response = [
            'billsplitter'=> $billsplitter,
            'lent'=> $lent,
            'owed'=>$owed,
            'totalowedamount'=>$owed->sum('amount'),
            'totalloanedamount'=>$lent->sum('amount'),
        ];
        
        return response($response, 201);

    }




    public function store(Request $request){
        $fields= $request->validate([
            "title"=> 'required',
            "paid"=>'required',
            "lent"=>"required",
            "friends_id"=>"required",
            "amount"=>"required",
            'group_id'=>'required'
            ]
            );
            $user_id = Auth::id();

     $bill = BillSplitter::create([
'user_id'=> $user_id,
'title'=>$fields['title'],
'paid'=>$fields['paid'],
'lent'=>$fields['lent'],
'group_id'=>$fields['group_id']]);

$billsplitter= BillSplitter::where('group_id', '=', $fields['group_id'])->get()->map(function($item){
    $user = User::where('id', '=', $item->user_id)->first();
    $item->name = $user->name;
return $item;
});
                 
$friends_id = $fields["friends_id"];

foreach($friends_id as $friend_id){
Dashboard::create([
'creditor_id'=>$user_id,
'debtor_id'=>$friend_id,
'bill_id'=>$bill->id,
'amount'=>$fields['amount'],
'group_id'=>$fields['group_id']
]);
}

$lent= Dashboard::select('debtor_id' , DB::raw('SUM(amount) as amount'))->
where('creditor_id', '=', $user_id)->groupBy('debtor_id')->get()->map(function ($l){
$user = User::where('id', '=', $l->debtor_id)->first();

return [
    'id'=>$l->debtor_id,
    'name'=>$user->name,
    'amount'=>$l->amount
];
});

$owed= Dashboard::select('creditor_id' , DB::raw('SUM(amount) as amount'))->
where('debtor_id', '=', $user_id)->groupBy('creditor_id')->get()->map(function ($o){
$user = User::where('id', '=', $o->creditor_id)->first();
return [
    'id'=>$o->debtor_id,
    'name'=>$user->name,
    'amount'=>$o->amount
];
});


$response = [
    'billsplitter'=> $billsplitter,
    'lent'=> $lent,
    'owed'=>$owed,
    'totalowedamount'=>$owed->sum('amount'),
    'totalloanedamount'=>$lent->sum('amount'),

];

return response($response, 201);
    }
}
