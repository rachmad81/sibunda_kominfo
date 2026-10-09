<?php 
namespace App\Http\Controllers\Penilai;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
	function index(){
		return view('penilai.dashboard.index');
	}
}
?>