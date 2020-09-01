<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Models\Employee;
use App\Models\Student;
use App\Models\UserEmployee;
use App\Models\UserStudent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
  public function index()
  {
    $id = Auth::user()->employee_id;
    $user = UserEmployee::with('employee')
      ->where('id', $id)
      ->first();

    $title = "Halaman Profile";
    return view('backend.profile.index', compact('title', 'user'));
  }

  public function resetPassword(ResetPasswordRequest $request)
  {
    $id = Auth::user()->employee_id;
    $password = $request->password;
    $confirmPassword = $request->password_confirmation;

    if (empty($password) || empty($confirmPassword)) {
      $json = ["status" => 500, "message" => "Password dan Konfirmasi Password harus diisi"];
    } elseif ($password != $confirmPassword) {
      $json = ["status" => 500, "message" => "Password dan Konfirmasi Password harus sama"];
    } elseif (strlen($password) < 8) {
      $json = ["status" => 500, "message" => "Password minimal 8 karakter"];
    } else {
      UserEmployee::where('employee_id', $id)->update([
        'password' => Hash::make($password),
      ]);
      $json = ["status" => 200, "message" => "Password berhasil diubah"];
    }
    return response()->json($json);
  }

  /**
   * get data user
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function getUserData(Request $request)
  {
    $userId = $request->user_id;

    if (Auth::guard('student')->check()) {
      $user = Student::with('userStudent')
        ->where('id', $userId)
        ->first();

      $data = [
        'id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
        'username' => $user->userStudent->username,
        'phone_number' => $user->phone_number
      ];
    } else {
      $user = Employee::with('userEmployee')
        ->where('id', $userId)
        ->first();

      $data = [
        'id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
        'username' => $user->userEmployee->username,
        'phone_number' => $user->phone_number
      ];
    }

    if ($user) {
      $json = ['status' => 200, 'list' => $data];
    } else {
      $json = ['status' => 500, 'message' => 'Data tidak ditemukan'];
    }

    return response()->json($json);
  }

  /**
   * update student profile
   * @param ProfileRequest $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function updateUserProfile(ProfileRequest $request)
  {
    $userId = $request->user_id;
    $name = $request->name;
    $username = $request->username;
    $phoneNumber = $request->phone_number;
    $password = $request->password;
    $email = $request->email;
    $photo = $request->file('photo');

    /* check data */
    $checkData = $this->checkExistingData($userId, $email, $username, $phoneNumber);

    if (!is_null($checkData)) {
      return response()->json($checkData);
    }

    /* save data */
    $data = $this->saveData($userId, $email, $username, $phoneNumber, $password, $photo, $name);

    if ($data) {
      $json = ['status' => 200, 'message' => 'Data berhasil diubah', 'user' => $data];
    } else {
      $json = ['status' => 500, 'message' => 'Data gagal diubah'];
    }

    return response()->json($json);
  }

  /**
   * check whether data already exists or not
   * @param $userId
   * @param $email
   * @param $username
   * @param $phoneNumber
   * @return array
   */
  private function checkExistingData($userId, $email, $username, $phoneNumber)
  {
    $json = null;

    if (Auth::guard('student')->check()) {
      $user = Student::where('id', $userId)->first();
      $checkEmail = Student::where('email', $email)->first();
      $checkPhoneNumber = Student::where('phone_number', $phoneNumber)->first();
      $checkUsername = UserStudent::where('username', $username)->first();
      $getUsername = $user->userStudent->username;
    } else {
      $user = Employee::where('id', $userId)->first();
      $checkEmail = Employee::where('email', $email)->first();
      $checkPhoneNumber = Employee::where('phone_number', $phoneNumber)->first();
      $checkUsername = UserEmployee::where('username', $username)->first();
      $getUsername = $user->userEmployee->username;
    }

    if ($user->email != $email) {
      if (!is_null($checkEmail)) {
        $json = ['status' => 500, 'message' => 'Email sudah digunakan'];
      }
    }

    if ($getUsername != $username) {
      if (!is_null($checkUsername)) {
        $json = ['status' => 500, 'message' => 'Username sudah digunakan'];
      }
    }

    if ($user->phone_number != $phoneNumber) {
      if (!is_null($checkPhoneNumber)) {
        $json = ['status' => 500, 'message' => 'Nomor Handphone sudah digunakan'];
      }
    }

    return $json;
  }

  /**
   * save data
   * @param $userId
   * @param $email
   * @param $username
   * @param $phoneNumber
   * @param $password
   * @param $photo
   * @param $name
   * @return object
   */
  private function saveData($userId, $email, $username, $phoneNumber, $password, $photo, $name)
  {
    if (Auth::guard('student')->check()) {
      $userStudent = UserStudent::where('student_id', $userId)->first();

      if (is_null($password) || empty($password)) {
        $userStudent->update([
          'username' => $username,
        ]);
      } else {
        $userStudent->update([
          'username' => $username,
          'password' => Hash::make($password)
        ]);
      }

      if (is_null($photo) || empty($photo)) {
        $userStudent->student()->update([
          'name' => $name,
          'email' => $email,
          'phone_number' => $phoneNumber
        ]);
      } else {
        Storage::disk('public')->delete($userStudent->student->photo);
        $userStudent->student()->update([
          'name' => $name,
          'email' => $email,
          'photo' => $photo->store('profile/siswa/' . $username, 'public'),
          'phone_number' => $phoneNumber
        ]);
      }

      $user = UserStudent::with(['student'])->where('student_id', $userId)->first();

      $data = (object)[
        'id' => $user->id,
        'username' => $user->username,
        'user_id' => $user->employee_id,
        'name' => $user->student->name,
        'email' => $user->student->email,
        'phone' => $user->student->phone_number,
        'identity_number' => $user->student->student_identity_number,
        'photo' => $user->student->photo,
        'guard' => 'student'
      ];
    } else {
      $userEmployee = UserEmployee::with('employee')
        ->where('employee_id', $userId)
        ->first();

      if ($userEmployee->employee->email != $email) {
        $userEmployee->update([
          'status_generate' => 0,
          'user_id_zoom' => null
        ]);
      }

      if (is_null($password) || empty($password)) {
        $userEmployee->update([
          'username' => $username,
        ]);
      } else {
        $userEmployee->update([
          'username' => $username,
          'password' => Hash::make($password)
        ]);
      }

      if (is_null($photo) || empty($photo)) {
        $userEmployee->employee()->update([
          'name' => $name,
          'email' => $email,
          'phone_number' => $phoneNumber
        ]);
      } else {
        Storage::disk('public')->delete($userEmployee->employee->photo);
        $userEmployee->employee()->update([
          'name' => $name,
          'email' => $email,
          'photo' => $photo->store('profile/karyawan/' . $username, 'public'),
          'phone_number' => $phoneNumber
        ]);
      }

      $user = UserEmployee::with(['employee'])->where('employee_id', $userId)->first();

      $data = (object)[
        'id' => $user->id,
        'username' => $user->username,
        'user_id' => $user->employee_id,
        'name' => $user->employee->name,
        'email' => $user->employee->email,
        'phone' => $user->employee->phone_number,
        'identity_number' => $user->employee->employee_identity_number,
        'photo' => $user->employee->photo,
        'guard' => 'employee'
      ];
    }

    return $data;
  }
}
