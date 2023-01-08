<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Consultation, User, Favorite, Bookedtime};
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Error;

class UserController extends Controller
{

    public function register(Request $request)
    {
        $request->validate([
            'username' => 'required|unique:users,username',
            'password' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'country' => 'required',
            'city' => 'required',
            'phone_number' => 'required|unique:users,phone_number'
        ]);
        User::create([
            'username' => $request->input('username'),
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'profile_photo' => 'path',
            'phone_number' => $request->input('phone_number'),
            'country' => $request->input('country'),
            'city' => $request->input('city'),
            'wallet' => $request->input('wallet'),
            'password' => bcrypt($request->input('password')),
            'role_type' => 'user'
        ]);
        $user = User::where('username', $request->username)->get([
            'id',
            'username',
            'first_name',
            'last_name',
            'country',
            'city',
            'phone_number',
            'wallet',
            'role_type',
        ])->first();
        return response()->json([
            'user' => $user,
            'token' => $user->createToken($user->username)->plainTextToken
        ], 200);
    }


    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required|min:8'
        ]);
        if (!Auth::attempt($request->only('username', 'password'))) {
            return response()->json([
                'user' => '',
                'token' => ''
            ], 403);
        }
        $user = User::where('username', $request->username)->first();
        $user_response = User::where('username', $request->username)->get([
            'id',
            'username',
            'first_name',
            'last_name',
            'country',
            'city',
            'phone_number',
            'wallet',
            'role_type',
        ])->first();

        if ($user->role_type != 'user') {
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
        }

        return response()->json([
            'user' => $user_response,
            'token' => $user->createToken($user->username)->plainTextToken
        ], 200);
    }

    public function show($id)
    {
        $user = User::find($id);
        return response()->json($user, 200);
    }

    public function consultations()
    {
        return response()->json(
            Consultation::all()->pluck('name'),
            200
        );
    }

    public function favorites()
    {
        $id = Auth::user()->id;
        $experts_ids = User::find($id)->favorites;
        $experts = [];
        foreach ($experts_ids as $expert) {
            $expert_fav = User::find($expert->fav_id);
            $experts[] = [
                'id' => $expert_fav->id,
                'username' => $expert_fav->username,
                'first_name' => $expert_fav->first_name,
                'last_name' => $expert_fav->last_name,
                'rate' => $expert_fav->expert->rate,
                'hourly_rate' => $expert_fav->expert->hourly_rate
            ];
        }
        return response()->json($experts, 200);
    }

    public function flip_favorite(Request $request)
    {
        $id = Auth::user()->id;
        $user = User::find($id);
        $fav_id = $request->input('fav_id');
        if ($user->favorites->where('fav_id', $fav_id)->first() == null) {
            Favorite::create([
                'user_id' => $user->id,
                'fav_id' => $fav_id
            ]);
        } else {
            Favorite::destroy(Favorite::where('user_id', $user->id)->where('fav_id', $fav_id)->pluck('id'));
        }
        return response()->json([], 200);
    }
    public function transfairMoney(Request $request)
    {
        $transfair = $request->input('transfair');
        $first =  User::find($request->input('id1'));
        $second =  User::find($request->input('id2'));

        $first->wallet -=  $transfair;
        $second->wallet +=  $transfair;
        $first->save();
        $second->save();

        return response()->json([], 200);
    }

    public function getUserBookedTimes()
    {
        error_log('dalfkdjf;lkajsdf');
        $user_id = Auth::user()->id;
        error_log($user_id);
        $dates = User::find($user_id)->bookedtimes;
        $data = [];
        foreach ($dates as $date) {
            $d = Carbon::createFromFormat('d/m/Y', $date->day . '/' . $date->month . '/' . $date->year)->format('d/m/Y');
            $data[] = [
                'id' => $date->id,
                'date' => $d,
                'hour' => Carbon::createFromFormat('H', $date->hour)->format('H'),
                'expert_first_name' => User::find($date->expert_id)->first_name,
                'expert_last_name' => User::find($date->expert_id)->last_name,
                'expert_id' => $date->expert_id,
            ];
        }
        return response()->json($data, 200);
    }

    public function setPhoto(Request $request)
    {
        $image_name = $request->file('photo')->getClientOriginalName();
        $path = $request->file('photo')->storeAs('users',$image_name,'public');
        $user_id = Auth::user()->id;
        User::find($user_id)->update([
            'profile_photo' => $path
        ]);
        return response()->json([],200);
    }

    public function getPhoto()
    {
        $user_id = Auth::user()->id;
        $image_name = User::find($user_id)->profile_photo;
        return response()->file(public_path('storage/'.$image_name));
    }

    public function getPhotoById($id)
    {
        $image_name = User::find($id)->profile_photo;
        return response()->file(public_path('storage/'.$image_name));
    }
    public function wallet()
    {
        $user_id = Auth::user()->id;
        $wallet = User::find($user_id)->wallet;
        return response()->json($wallet,200);
    }
    public function destroy($id)
    {
        return User::destroy($id);
    }
}
