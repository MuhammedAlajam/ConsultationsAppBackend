<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use Illuminate\Http\Request;
use App\Models\Expert;
use App\Models\User;
use Psy\Command\WhereamiCommand;
use Illuminate\Support\Str;

use function Psy\debug;

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
            'username' => 'required|unique:users,username',
            'password' => 'required|min:8',
            'first_name' => 'required',
            'last_name' => 'required',
            'country' => 'required',
            'city' => 'required',
            'phone_number' => 'required|unique:users,phone_number',
            'description' => 'required',
            'hourly_rate' => 'required'
        ]);
        $user = User::create([
            'username' => $request->input('username'),
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'profile_photo' => 'path',
            'phone_number' => $request->input('phone_number'),
            'country' => $request->input('country'),
            'city' => $request->input('city'),
            'wallet' => $request->input('wallet'),
            'password' => bcrypt($request->input('password')),
            'role_type' => 'expert'
        ]);
        $expert = Expert::create([
            'description' => $request->input('description'),
            'hourly_rate' => $request->input('hourly_rate'),
            'user_id' => $user->id
        ]);



        //$ids = json_decode($request->input('consultatinosIds'));

        //foreach ($ids as $id)
        //s Consultation::find($id)->experts()->attach($expertInfo->id);

        $consultation_types = json_decode($request->input('consultationsNames'));

        foreach ($consultation_types as $consultation_type) {
            $new_consultation = Consultation::all()->where('name', $consultation_type);
            if ($new_consultation->isEmpty()) {
                Consultation::create([
                    'name' => $consultation_type
                ]);
            }

            $expert->consultations()->attach(Consultation::where('name', $consultation_type)->get('id'));
        }
        $expert = User::where('username', $request->username)->first();

        $user_response = [
            'id' => $expert->id,
            'username' => $expert->username,
            'first_name' => $expert->first_name,
            'last_name' => $expert->last_name,
            'country' => $expert->country,
            'city' => $expert->city,
            'phone_number' => $expert->phone_number,
            'wallet' => $expert->wallet,
            'description' => $expert->expert->description,
            'rate' => $expert->expert->rate,
            'hourly_rate' => $expert->expert->hourly_rate,
            'role_type' => 'expert',
        ];
        return response()->json([
            'user' => $user_response,
            'token' => $user->createToken($user->username)->plainTextToken
        ], 200);
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showAll()
    {
        $experts = [];
        foreach (Expert::all() as $expert) {
            $experts[] = [
                'id' => $expert->user->id,
                'username' => $expert->user->username,
                'first_name' => $expert->user->first_name,
                'last_name' => $expert->user->last_name,
                'rate' => $expert->rate,
                'hourly_rate' => $expert->hourly_rate

            ];
        }
        return response()->json($experts, 200);
    }

    public function show($id)
    {
        $user = User::where('id', $id)->get()->first();
        $expert = [
            'id' => $user->id,
            'username' => $user->username,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'country' => $user->country,
            'city' => $user->city,
            'phone_number' => $user->phone_number,
            'description' => $user->expert->description,
            'hourly_rate' => $user->expert->hourly_rate,
            'rate' => $user->expert->rate

        ];
        return response()->json($expert, 200);
    }

    public function searchByName($name)
    {
        $users = [];
        foreach (User::where('role_type', 'expert')->get() as $expert) {
            if (Str::contains($expert->username, $name) || Str::contains($expert->first_name, $name) || Str::contains($expert->last_name, $name)) {
                $ex = $expert;
                $users[] = [
                    'id' => $expert->id,
                    'username' => $expert->username,
                    'first_name' => $expert->first_name,
                    'last_name' => $expert->last_name,
                    'rate' => $expert->expert->rate,
                    'hourly_rate' => $expert->expert->hourly_rate
                ];
            }
        }
        return response()->json($users, 200);
    }

    public function searchByConsultation($id)
    {
        $data = [];

        foreach (Consultation::find($id)->experts as $expert)
            $data[] = [
                'id' => $expert->user->id,
                'username' => $expert->user->username,
                'first_name' => $expert->user->first_name,
                'last_name' => $expert->user->last_name,
                'rate' => ($expert->number_of_ratings == 0) ? 0 : $expert->sum_of_ratings / $expert->number_of_ratings,
                'hourly_rate' => $expert->hourly_rate
            ];

        return response()->json($data, 200);
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
        $user->expert->rate = $user->expert->sum_of_ratings / $user->expert->number_of_ratings;
        $user->expert->save();

        return response()->json([], 200);
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
