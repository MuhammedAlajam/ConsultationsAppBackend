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
        $request->validate([
            'name'=>'required',
            'password'=>'required|min:8',
            'first_name'=>'required',
            'last_name'=>'required',
            'country'=>'required',
            'city'=>'required',
            'phone_number'=>'required',
            'description'=>'required',
            'session_duration'=>'required',
            'fee'=>'required'
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
            'password' => bcrypt($request->input('password')),
            'role_type' =>'expert'
        ]);
        $expertInfo = Expert::create([
            'description' => $request->input('description'),
            'session_duration' => $request->input('session_duration'),
            'fee' => $request->input('fee'),
            'user_id'=>$user->id
        ]);
        
               
        //$ids = json_decode($request->input('consultatinosIds'));

        //foreach ($ids as $id)
           //s Consultation::find($id)->experts()->attach($expertInfo->id);

        $consultation_types = json_decode($request->input('consultationIds'));

        foreach($consultation_types as $consultation_type)
        {
            $expertInfo->consultations()->attach($consultation_type);
        }
        return response()->json([
            'user'=>$user,
            'token'=>$user->createToken($user->name)->plainTextToken
        ],200);
    }

   

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showAll()
    {
        $experts= User::all()->where('role_type','expert');
        
        return response()->json($experts,200);
    }

    public function show($id)
    {
        $expert= User::all()->where('id',$id)->first();
        return response()->json($expert,200);
    }

    public function searchByName($name)
    {
        $users= User::all()->where('name', $name)->where('role_type','expert');

        return response()->json($users,200);
    }

    public function searchByConsultation($name)
    {
        $experts ;
        foreach (Consultation::where('name', $name)->first()->experts as $ex)
            $experts[] = $ex->user;

        return response()->json($experts,200);
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

        return response()->json($user,200);
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
