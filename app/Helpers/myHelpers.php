<?php

use App\Models\Configuration;
use App\Models\Navigation;
use App\Models\RoleUser;
use App\Models\SchoolYear;
use App\Models\UserNavigation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

if (!function_exists('navigation')) {
  function navigation()
  {
    $config = optional(configuration())->type_school;
    $roleId = Session::get('role_id');
    $navigation = [];
    $i = 0;
    $j = 0;

    if ($config == 1) {
      /* if type school is university */
      $userNavigation = Navigation::with('userNavigation')
        ->whereHas('userNavigation', function ($query) use ($roleId) {
          $query->where('role_id', $roleId);
        })
        ->whereNotIn('id', [10, 7])
        ->get();
    } elseif ($config == 2) {
      /* if type school is senior high school or vocational senior high school */
      $userNavigation = Navigation::with('userNavigation')
        ->whereHas('userNavigation', function ($query) use ($roleId) {
          $query->where('role_id', $roleId);
        })
        ->whereNotIn('id', [8, 11])
        ->get();
    } else {
      /* if type school is junior high school or elementary school */
      $userNavigation = Navigation::with('userNavigation')
        ->whereHas('userNavigation', function ($query) use ($roleId) {
          $query->where('role_id', $roleId);
        })
        ->whereNotIn('id', [4, 8, 11])
        ->get();
    }

    foreach ($userNavigation as $route) {
      $userNav = $route->userNavigation;

      if ($route->parent_id == 0) {
        $navigation[$i] = [
          'index' => $i,
          'id' => $route->id,
          'title' => $route->title,
          'url' => $route->url,
          'icon' => $route->icon,
          'order_num' => $route->order_num,
          'order_sub' => $route->order_sub,
          'child' => [],
          'crud' => [
            'create' => $userNav->create,
            'update' => $userNav->update,
            'delete' => $userNav->delete
          ]
        ];
      }
      $i++;
    }

    foreach ($userNavigation as $route) {
      $userNav = $route->userNavigation;
      if ($route->parent_id != 0) {
        foreach ($navigation as $parent) {
          if ($route->parent_id == $parent['id']) {
            $navigation[$parent['index']]['child'][] = [
              'id' => $route->id,
              'title' => $route->title,
              'url' => $route->url,
              'icon' => $route->icon,
              'order_num' => $route->order_num,
              'crud' => [
                'create' => $userNav->create,
                'update' => $userNav->update,
                'delete' => $userNav->delete
              ]
            ];
          }
        }
      }
      $j++;
    }
    return collect($navigation)
      ->sortBy('order_num')
      ->sortBy('order_sub');
  }
}

if (!function_exists('isActiveRoute')) {
  function isActiveRoute($route, $output = 'active')
  {
    if (Route::currentRouteName() == $route) {
      return $output;
    }
  }
}

if (!function_exists('activeSchoolYear')) {
  function activeSchoolYear()
  {
    return SchoolYear::where('status_active', 1)->first();
  }
}

if (!function_exists('getNavigationId')) {
  function getNavigationId()
  {
    $roleId = Session::get('role_id');
    $segment = Route::currentRouteName();
    $userNavigation = Navigation::with('userNavigation')
      ->where('url', $segment)
      ->whereHas('userNavigation', function ($query) use ($roleId) {
        $query->where('role_id', $roleId);
      })->first();

    if (!empty($userNavigation) || !is_null($userNavigation)) {
      if ($userNavigation->url == $segment) {
        Session::put('navigation_id', $userNavigation->id);
      }
    }
    return Session::get('navigation_id');
  }
}

if (!function_exists('checkPermission')) {
  function checkPermission()
  {
    $navigationId = getNavigationId();
    $roleId = Session::get('role_id');
    $create = false;
    $update = false;
    $delete = false;
    $updateAndDelete = false;
    $userNavigation = UserNavigation::select("delete", "create", "update")
      ->where('role_id', $roleId)
      ->where('navigation_id', $navigationId)
      ->first();

    if (!is_null($userNavigation)) {
      if ($userNavigation->create == 1) {
        $create = true;
      }

      if ($userNavigation->update == 1 && $userNavigation->delete == 1) {
        $updateAndDelete = true;
      }

      if ($userNavigation->update == 1) {
        $update = true;
      }

      if ($userNavigation->delete == 1) {
        $update = true;
      }
    }

    return (object) [
      'create' => $create,
      'update_delete' => $updateAndDelete,
      'update' => $update,
      'delete' => $delete
    ];
  }
}

if (!function_exists('checkRouteExist')) {
  function checkRouteExist($route)
  {
    if (Route::has($route)) {
      return route($route);
    } else {
      return route('blank.index');
    }
  }
}

if (!function_exists('configuration')) {
  function configuration()
  {
    return Configuration::first();
  }
}

if (!function_exists('subjectName')) {
  function subjectName()
  {
    $config = optional(configuration())->type_school;
    if ($config == 1) {
      return 'Mata Kuliah';
    } else {
      return 'Mata Pelajaran';
    }
  }
}

if (!function_exists('identityNumber')) {
  function identityNumber()
  {
    $config = optional(configuration())->type_school;
    if ($config == 1) {
      return 'Nomor Pokok Mahasiswa (NPM)';
    } else {
      return 'Nomor Induk Peserta Didik (NIPD)';
    }
  }
}

if (!function_exists('sortIdentityNumber')) {
  function sortIdentityNumber()
  {
    $config = optional(configuration())->type_school;
    if ($config == 1) {
      return 'NPM';
    } else {
      return 'NIPD';
    }
  }
}

if (!function_exists('studentName')) {
  function studentName()
  {
    $config = optional(configuration())->type_school;
    if ($config == 1) {
      return 'Mahasiswa';
    } else {
      return 'Siswa';
    }
  }
}

if (!function_exists('level')) {
  function level()
  {
    $config = optional(configuration())->type_school;
    if ($config == 1) {
      return 'Semester';
    } else {
      return 'Tingkat Kelas';
    }
  }
}

if (!function_exists('countRole')) {
  function countRole()
  {
    $user = Auth::id();
    return RoleUser::where('user_employee_id', $user)->count();
  }
}

if (!function_exists('getRoleUser')) {
  function getRoleUser()
  {
    $user = Auth::id();
    return RoleUser::with('role')
      ->where('user_employee_id', $user)
      ->get();
  }
}

if (!function_exists('convertMonthName')) {
  function convertMonthName($month)
  {
    $name = null;
    switch ($month) {
      case '01':
        $name = 'Januari';
        break;
      case '02':
        $name = 'Februari';
        break;
      case '03':
        $name = 'Maret';
        break;
      case '04':
        $name = 'April';
        break;
      case '05':
        $name = 'Mei';
        break;
      case '06':
        $name = 'Juni';
        break;
      case '07':
        $name = 'Juli';
        break;
      case '08':
        $name = 'Agustus';
        break;
      case '09':
        $name = 'September';
        break;
      case '10':
        $name = 'Oktober';
        break;
      case '11':
        $name = 'November';
        break;
      case '12':
        $name = 'Desember';
        break;
    }
    return $name;
  }
}
