<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Service;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function profile(){
        $user = Auth::user();
        $services = Service::all();
        $orders = Order::where('isActive',true)->orderBy('id','DESC')->get();

        return view('profile',compact('user','services','orders'));
    }

    public function auth(Request $request){
        if (Auth::attempt(['login' => $request->login, 'password' => $request->password], true)) {
            // Authentication passed...
            return redirect()->route('profile');
        }
        return redirect()->route('welcome')->withErrors('Неудачная попытка аутентификации');
    }

    public function logout(){
        Auth::logout();

        return redirect()->route('welcome');
    }

    public function addUser(Request $request){  
        User::create([
            'login' => 'TheGreatAdmin23',
            'password' => Hash::make('admin123123'),
            'role' => "админ",
        ]);

        return 'админ создан';
    }
}
