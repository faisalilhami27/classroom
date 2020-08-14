<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\UserEmployee;
use App\Models\UserStudent;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
  /*
  |--------------------------------------------------------------------------
  | Login Controller
  |--------------------------------------------------------------------------
  |
  | This controller handles authenticating users for the application and
  | redirecting them to your home screen. The controller uses a trait
  | to conveniently provide its functionality to your applications.
  |
  */

  use AuthenticatesUsers;

  /**
   * Where to redirect users after login.
   *
   * @var string
   */
//    protected $redirectTo = RouteServiceProvider::HOME;

  /**
   * Create a new controller instance.
   *
   * @return void
   */
  public function __construct()
  {
    $this->middleware('guest', ['except' => 'logout']);
    $this->middleware('guest:student', ['except' => 'logout']);
    $this->middleware('guest:employee', ['except' => 'logout']);
  }

  /**
   * Show the application's login form.
   *
   * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
   */
  public function showLoginForm()
  {
    return view('auth.login');
  }

  /**
   * process login for student or employee
   * @param LoginRequest $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function login(LoginRequest $request)
  {
    $username = $request->username;
    $password = $request->password;
    $loginAs = $request->loginAs;

    if ($loginAs == 'student') {
      $checkUser = UserStudent::where('username', $username)->first();
    } else {
      $checkUser = UserEmployee::where('username', $username)->first();
    }

    // check user exist or not
    if (is_null($checkUser)) {
      return response()->json(['status' => 500, 'message' => 'Akun tidak ditemukan']);
    }

    // check user active or inactive (1 for active and 0 for inactive)
    if ($checkUser->status == 0) {
      return response()->json(['status' => 500, 'message' => 'Akun anda di nonaktifkan silahkan hubungi Administrator']);
    }

    // check user login success or fail
    if (Auth::guard($loginAs)->attempt([
      'username' => $username,
      'password' => $password
    ],
      $request->get('remember')
    )) {
      return response()->json(['status' => 200, 'message' => 'Login berhasil akan dialihkan ke halaman utama', 'loginAs' => $loginAs]);
    } else {
      return response()->json(['status' => 500, 'message' => 'Username atau Password salah']);
    }
  }
}
