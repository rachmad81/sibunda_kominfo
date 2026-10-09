<?php

namespace App\Http\Controllers\Bunda;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardIframeController extends Controller
{
  function main(Request $request)
  {
    return view('bunda.dashboard_iframe.main');
  }
}
