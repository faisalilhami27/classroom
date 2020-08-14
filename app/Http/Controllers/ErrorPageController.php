<?php

namespace App\Http\Controllers;

class ErrorPageController extends Controller
{
  public function blank()
  {
    return view('errors.underConstruct');
  }

  public function forbidden()
  {
    return view('errors.403');
  }
}
