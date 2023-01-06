<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use Illuminate\Http\Request;
use App\Models\Expert;
use App\Models\User;
use App\Models\Availabletime;
use App\Models\Bookedtime;
use Psy\Command\WhereamiCommand;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use function Psy\debug;

class ExpertController extends Controller
{

    public function index()
    {
        //
    }

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
        $get_favorite = User::where('id',Auth::user()->id)->first()->favorites->where('fav_id',$id)->first();
        $is_favorite = true;
        if($get_favorite == null)
            $is_favorite = false;
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
            'rate' => $user->expert->rate,
            'is_favorite' => $is_favorite,
            'consultations' => $user->expert->consultations->pluck('name')
        ];
        return response()->json($expert, 200);
    }

    public function searchByName($name)
    {
        $users = [];
        foreach (User::where('role_type', 'expert')->get() as $expert) {
            if (Str::contains($expert->username, $name) || Str::contains($expert->first_name, $name) || Str::contains($expert->last_name, $name)) {
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

        debug($users);


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
                'rate' => $expert->rate,
                'hourly_rate' => $expert->hourly_rate
            ];

        return response()->json($data, 200);
    }

    public function filterByConsultation($name)
    {
        $data = [];
        foreach(Consultation::where('name',$name)->first()->experts as $expert)
        {
            $data[] = [
                'id' => $expert->user->id,
                'username' => $expert->user->username,
                'first_name' => $expert->user->first_name,
                'last_name' => $expert->user->last_name,
                'rate' => $expert->rate,
                'hourly_rate' => $expert->hourly_rate
            ];
        }
        return response()->json($data,200);
    }

    public function getBookedTimes($id)
    {
        return User::find($id)->expert->booked_times;
    }

    public function rate(Request $request)
    {
        $user = User::find($request->id);
        $user->expert->number_of_ratings++;
        $user->expert->sum_of_ratings += $request->input('new_rate');
        $user->expert->rate = $user->expert->sum_of_ratings / $user->expert->number_of_ratings;
        $user->expert->save();
        return response()->json([], 200);
    }
    public function unrate(Request $request)
    {
        $user = User::find($request->id);
        $user->expert->number_of_ratings--;
        $user->expert->sum_of_ratings -= $request->input('old_rate');
        if($user->expert->number_of_ratings == 0)
        {
            $user->expert->rate = 0;
        }
        else
        {
            $user->expert->rate = $user->expert->sum_of_ratings / $user->expert->number_of_ratings;
        }
        $user->expert->save();
        return response()->json([],200);
    }
    /* 
        @params

        string $day like sunday

        token

        array of int $hours
    */
    public function setAvailableTimes(Request $request)
    {
        $start_date = '1/1/2023';
        $date = Carbon::createFromFormat('d/m/Y',$start_date);
       // return  Carbon::createFromFormat('h','6')->format('h');
        $day = $request->input('day');
        $hours = json_decode($request->input('hours'));
        $expert_id = Auth::user()->id;
        while((int)$date->format('Y')<2025)
        {
            if($date->format('l') == $day)
            {
                foreach($hours as $hour)
                {
                    Availabletime::create([
                    'year' => $date->format('Y'),
                    'month' => $date->format('m'),
                    'day' => $date->format('d'),
                    'hour' => Carbon::createFromFormat('H',$hour)->format('H'),
                    'expert_id' => $expert_id
                    ]);
                }
            }
            $date->addDays(1);
        }
        return response()->json([],200);
    }
    /* 
        @params

        string $date foramt 1/1/2023

        int $expert_id

        @return 

        array of hours
    */
    public function getAvailableTimes(Request $request)
    {
        $expert_id = $request->input('expert_id');
        $date = Carbon::createFromFormat('d/m/Y',$request->input('date'));

        $data = User::where('id',$expert_id)->first()->expert->availabletimes->
        where('year',$date->format('Y'))->where('month',$date->format('m'))->where('day',$date->format('d'))->pluck('hour');

        return response()->json([$data],200);
    }
    /* 
        @params

        string $date format 1/1/2023

        int $expert_id
        int $user_id

        string hour 
    */
    public function book(Request $request)
    {
        $expert_id = $request->input('expert_id');
        $user_id =Auth::user()->id;
        if(User::find($user_id)->wallet < User::find($expert_id)->expert->hourly_rate)
        {
            return response()->json([],403);
        }
        $first =  User::find($user_id);
        $second =  User::find($expert_id);
        $transfair = $second->expert->hourly_rate;
        $first->wallet -=  $transfair;
        $second->wallet +=  $transfair;
        $first->save();
        $second->save();
        $date = Carbon::createFromFormat('d/m/Y',$request->input('date'));
        $hour = $request->input('hour');
        $data = User::where('id',$expert_id)->first()->expert->availabletimes->
        where('year',$date->format('Y'))->where('month',$date->format('m'))->where('day',$date->format('d'))
        ->where('hour',Carbon::createFromFormat('H',$hour)->format('H'))->first();
        Bookedtime::create([
            'year' => $data->year,
            'month' =>$data->month,
            'day' =>$data->day,
            'hour' =>$data->hour,
            'expert_id' =>$expert_id,
            'user_id' =>$user_id
        ]);
        Availabletime::destroy($data->id);
        return response()->json([],200);
    }
   
    public function getExpertBookedTimes()
    {
        $expert_id = Auth::user()->id;
        $dates = Bookedtime::where('expert_id',$expert_id)->get();
        $data = [];
        foreach($dates as $date)
        {
            $d = Carbon::createFromFormat('d/m/Y',$date->day.'/'.$date->month.'/'.$date->year)->format('d/m/Y');
            $data[] =[
                'id' => $date->id,
                'date' => $d,
                'hour' => Carbon::createFromFormat('H',$date->hour)->format('H'),
                'user_first_name' => User::find($date->user_id)->first_name,
                'user_last_name' => User::find($date->user_id)->last_name,
                'user_id' => $date->user_id,
            ];
        }
        return response()->json($data,200);
    }
    public function destroy($id)
    {
        //
    }
}
