<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{User,Favorite};
use Illuminate\Support\Facades\Auth;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response     

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $request->validate([
            'name'=>'required',
            'password'=>'required',
            'first_name'=>'required',
            'last_name'=>'required',
            'country'=>'required',
            'city'=>'required',
            'phone_number'=>'required'
        ]);
        $user=User::create([
            'name' => $request->input('name'),
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'profile_photo' => $request->input('profile_photo'),
            'phone_number' => $request->input('phone_number'),
            'country' => $request->input('country'),
            'city' => $request->input('city'),
            'wallet' => $request->input('wallet'),
            'password' =>bcrypt($request->input('password')),
            'role_type' =>'user'
        ]);
        return response()->json([
            'user'=>$user,
            'token'=>$user->createToken($user->name)->plainTextToken
        ],200);
    }


    public function login(Request $request)
    {
        $request->validate([
            'name'=>'required',
            'password'=>'required|min:8'
        ]);
        if(!Auth::attempt($request->only('name','password')))
        {
            return response()->json([
                'userInfo'=>'',
                'token'=>''
            ],403);
        }
        $userInfo=User::where('name',$request->name)->first();

        return response()->json([
            'userInfo'=>$userInfo,
            'token'=>$userInfo->createToken($userInfo->name)->plainTextToken
        ],200);
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::find($id);
        return response()->json($user,200);
    }

    public function favorites($id)
    {
        $experts_ids = User::find($id)->favorites;
        $experts;

        foreach ($experts_ids as $expert_id)
        {
            $experts= User::find($expert_id);
        }

        return response()->json($experts,200);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function transfairMoney(Request $request)
    {
        $transfair = $request->input('transfair');
        $first =  User::find($request->input('id1'));
        $second =  User::find($request->input('id2'));

        $first->wallet -=  $transfair;
        $second->wallet +=  $transfair;
        $first->save();
        $second->save();

        return response()->json([
            'user wallet is now : ' => $first->wallet,
            'expert wallet is now : ' => $second->wallet,
        ],200);
    }

    public function addFavorite(Request $request)
    {
        $user= User::find($request->input('user_id'));
        $favorite= Favorite::create([
            'user_id'=>$request->input('user_id'),
            'fav_id'=>$request->input('fav_id')
        ]);
        return response()->json($favorite,200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return User::destroy($id);
    }
}
