<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stevebauman\Location\Facades\Location;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Http;
use Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        // if ($position = Location::get()) {
        //     // Successfully retrieved position.
        //     // echo $position->countryName;
        // } else {
        //     // Failed retrieving position.
        // }
        $position = Location::get('103.24.180.44');
        $data = $this->fetchweather($position->latitude,$position->longitude);
        return view('home',compact('position','data'));
    }

    public function fetchweather($lat,$lon)
    {
        $minute_to_expire = 60;
        $url = "https://api.openweathermap.org/data/2.5/weather?lat=$lat&lon=$lon&appid=".env('OPENWEATHER_API')."";
        $cachedWeather = Redis::get('weather_dump_'.Auth::user()->id);
        $weather_dump = null;
        if (isset($cachedWeather)) {
            $weather_dump = $cachedWeather;
        } else {
            $data = Http::get($url)->json()['main'];
            // $data = Http::get($url)->json();
            $weather_dump = json_encode($data);
            Redis::set('weather_dump_'.Auth::user()->id, $weather_dump);
        }
        Redis::expire('weather_dump_'.Auth::user()->id,($minute_to_expire*60));
        return $weather_dump;
    }
}
