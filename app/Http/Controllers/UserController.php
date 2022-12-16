<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;



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
        return User::create($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return User::find($id);
    }

    public function favorites($id)
    {
        return User::find($id)->favorites;
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
        ]);
    }

    public function addFavorite(Request $request)
    {
        $user = User::find($request->input('user_id'));
        $user->favorites()->attach($request->input('fav_id'));

        return User::find($request->input('user_id'))->favorites;
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
