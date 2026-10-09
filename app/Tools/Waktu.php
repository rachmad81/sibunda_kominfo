<?php

namespace App\Tools;
use Carbon\Carbon;

class Waktu{
	protected static $bulan = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];

	public static function getFullTimeID(){
		return date('d').' '.self::$bulan[date('m')-1].' '.date('Y');
	}

	public static function getMonthID($month){
		//from format m
		if (is_null($month)) {
			return '-';
		}
		return self::$bulan[$month-1];
	}

	public static function parseMonthYear($monthYear){
		//from format m-Y
		if (is_null($monthYear)) {
			return '-';
		}
		$date = explode('-', $monthYear);
		$month = self::$bulan[(int)$date[0]-1];
		return $month.' '.$date[1];
	}

	public static function parseFullDate($data){
		//from format Y-m-d
		if (is_null($data)) {
			return '-';
		}
		$date = explode('-', $data);
		$month = self::$bulan[(int)$date[1]-1];
		return $date[2].' '.$month.' '.$date[0];
	}

	public static function parseFullDatetime($data){
		//from format Y-m-d H:i:s
		if (is_null($data)) {
			return '-';
		}
		$time = explode(' ', $data);
		$date = explode('-', $time[0]);
		$month = self::$bulan[(int)$date[1]-1];
		return $date[2].' '.$month.' '.$date[0].' ('.$time[1].')';
	}

	public static function isInDateRange($value, $start, $end){
		if (strtotime($value) >= strtotime($start) && strtotime($value) <= strtotime($end)) {
			return true;
		}
		return false;
	}

	public static function usia_angka($date, $date_current=null){
        if ($date) {
            if ($date_current) {
                $now = $date_current;
            } else {
                $now = Carbon::now(); // Tanggal sekarang
            }

            $date = Carbon::parse($date);
            $y = $date->diffInYears($now);
            
            return (int) $y;
        } else {
            return "-";
        }        
    }
}


?>