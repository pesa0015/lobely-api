<?php

namespace App\Http\Controllers\Auth;

use Auth;
use App\User;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $request)
    {
        $file = Input::file('file');
        $file->move(storage_path() . '/app/public/', '1.png');
        dd($file);
        return Input::file('file')->move('storage/app/',Input::file('file')->getClientOriginalName());
        $file = Input::file('file');
        $file->move(storage_path() . '/' . Auth::user()->id . '/', uniqid());
        return;
        $file = array_get($request, 'file');
        $extension = $file->getClientOriginalExtension(); 
        // RENAME THE UPLOAD WITH RANDOM NUMBER 
        $fileName = rand(11111, 99999) . '.' . $extension; 
        $upload_success = $file->move('storage/app/public/', $fileName);
        dd($upload_success);
        $request->file->move();
        dd($path);
        return $request->file->move(__DIR__.'/storage/app/public/' . Auth::user()->id . '/',Input::file('file')->getClientOriginalName());
        // dd($request);
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }
}
