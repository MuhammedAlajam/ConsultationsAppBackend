<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use Illuminate\Http\Request;
use App\Models\Expert;
use App\Models\User;
use Psy\Command\WhereamiCommand;

class ExpertController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $expertInfo = Expert::create([
            'description' => $request->input('description'),
            'session_duration' => $request->input('session_duration'),
            'fee' => $request->input('fee'),
        ]);

        $ids = json_decode($request->input('consultatinosIds'));

        foreach ($ids as $id)
            Consultation::find($id)->experts()->attach($expertInfo->id);

        return User::create([
            'name' => $request->input('name'),
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'profile_photo' => $request->input('profile_photo'),
            'phone_number' => $request->input('phone_number'),
            'country' => $request->input('country'),
            'city' => $request->input('city'),
            'wallet' => $request->input('wallet'),
            'password' => $request->input('password'),
            'expert_id' => $expertInfo->id,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showAll()
    {
        return User::all()->where('expert_id');
    }


    public function searchByName($name)
    {
        return User::all()->where('name', $name)->where('expert_id', '!=', null);
    }

    public function searchByConsultation($name)
    {
        $response = [];
        foreach (Consultation::where('name', $name)->first()->experts as $ex)
            $response[] = $ex->user;

        return $response;
    }


    public function getBookedTimes($id)
    {
        return User::find($id)->expert->booked_times;
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function rate(Request $request, $id)
    {
        $user = User::find($id);
        $user->expert->number_of_ratings++;
        $user->expert->sum_of_ratings += $request->input('rate');
        $user->expert->save();

        return $user->expert;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
