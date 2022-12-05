<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Location;
use Illuminate\Support\Facades\Redis;

class HomeController extends Controller
{
    //

    public function getWeatherDump(Request $request)
    {
        if(auth()){
            $client_ip = $request->ip() == "127.0.0.1" ? "103.24.180.44": $request->ip();
            $user = auth()->user();
            $position = Location::get($client_ip);
            $weather = $this->fetchweather($position->latitude,$position->longitude,$user);

            return response()->json(['user'=>$user,'main'=>json_decode($weather)]);
        }else {
            return response()->json(['message'=>'User Unauthorized.'],401);
        }
    }

    public function fetchweather($lat,$lon,$user)
    {
        $minute_to_expire = 60;
        $url = "https://api.openweathermap.org/data/2.5/weather?lat=$lat&lon=$lon&appid=".env('OPENWEATHER_API')."";
        $cachedWeather = Redis::get('weather_dump_'.$user->id);
        $weather_dump = null;
        if (isset($cachedWeather)) {
            $weather_dump = $cachedWeather;
        } else {
            $data = Http::get($url)->json()['main'];
            $weather_dump = json_encode($data);
            Redis::set('weather_dump_'.$user->id, $weather_dump);
        }
        Redis::expire('weather_dump_'.$user->id,($minute_to_expire*60));
        return $weather_dump;
    }
}
