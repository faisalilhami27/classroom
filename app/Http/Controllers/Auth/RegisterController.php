<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Jobs\VerifyEmailRegisterJob;
use App\Mail\VerifyMail;
use App\Models\Student;
use App\Models\UserStudent;
use Exception;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

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
   * Where to redirect users after registration.
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

  public function showRegistrationForm()
  {
    return view('auth.register');
  }

  /**
   * Create a new user instance after a valid registration.
   *
   * @param RegisterRequest $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function store(RegisterRequest $request)
  {
    $checkEmail = Student::where('email', $request->email)->first();
    $checkPhoneNumber = Student::where('phone_number', $request->phone_number)->first();
    $checkUsername = UserStudent::where('username', $request->username)->first();
    $checkStudentIdentityNumber = Student::where('student_identity_number', $request->student_identity_number)->first();
    $color = $this->randomColor();

    // check email
    if (!is_null($checkEmail)) {
      return response()->json(['status' => 500, 'message' => 'Email sudah digunakan.']);
    }

    // check phone number
    if (!is_null($checkPhoneNumber)) {
      return response()->json(['status' => 500, 'message' => 'Nomor Handphone sudah digunakan.']);
    }

    // check username
    if (!is_null($checkUsername)) {
      return response()->json(['status' => 500, 'message' => 'Username sudah digunakan.']);
    }

    // check student identity number
    if (!is_null($checkStudentIdentityNumber)) {
      return response()->json(['status' => 500, 'message' => 'NIPD sudah digunakan.']);
    }

    $insert = DB::transaction(function () use($request, $color) {
      $student = Student::create([
        'student_identity_number' => $request->student_identity_number,
        'name' => $request->name,
        'email' => $request->email,
        'phone_number' => $request->phone_number,
        'color' => $color
      ]);

      // insert data user student to database
      $student->userStudent()->create([
        'student_id' => $student->id,
        'username' => $request->username_register,
        'password' => Hash::make($request->password_register),
        'status' => 0,
        'email_verified' => 0
      ]);

      // send email to student
      dispatch(new VerifyEmailRegisterJob($request->email, $student));

      try {
        DB::commit();
        return true;
      } catch (Exception $e) {
        DB::rollBack();
        return response()->json(['status' => 500, 'errors' => $e->getMessage()]);
      }
    });

    if ($insert) {
      return response()->json(['status' => 200, 'message' => 'Registrasi berhasil silahkan cek email anda.']);
    } else {
      return response()->json(['status' => 500, 'message' => 'Gagal melakukan registrasi.']);
    }
  }

  /** random color
   *
   */
  private function randomColor()
  {
    return sprintf('#%06X', mt_rand(0, 0xFFFFFF));
  }

  /**
   * method for email verification after students register
   * @param $token
   * @return \Illuminate\Http\RedirectResponse
   */
  public function emailVerification($token)
  {
    // check if token empty
    if (empty($token)) {
      return redirect()->route('login')->with('error', 'Token tidak ditemukan.');
    }

    // decrypt token as email
    $id = Crypt::decrypt($token);

    // update status user by email
    $update = UserStudent::where('student_id', $id)->update([
      'email_verified' => 1,
      'status' => 1
    ]);

    if ($update) {
      return redirect()->route('login')->with('success', 'Email berhasil diverifikasi silahkan login.');
    } else {
      return redirect()->route('login')->with('error', 'Email gagal diverifikasi silahkan hubungi admin.');
    }
  }
}
