<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Interfaces\UserRepositoryInterface;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Laravel\Socialite\Facades\Socialite;

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
    protected $redirectTo = RouteServiceProvider::HOME;


    private UserRepositoryInterface $userRepository;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->middleware('guest')->except('logout');
        $this->userRepository = $userRepository;
    }

    /**
     * Redirect the user to the Google authentication page.
    *
    * @return \Illuminate\Http\Response
    */
    public function redirectToProvider()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from Google.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleProviderCallback()
    {
        try {
            $user = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect('/login');
        }
        // only allow people with @company.com to login
        // if(explode("@", $user->email)[1] !== 'company.com'){
        //     return redirect()->to('/home');
        // }
        // check if they're an existing user
        $existingUser =  $this->userRepository->getUserByfilter('email', $user->email);
        if ($existingUser )
        {
            // log them in
            auth()->login($existingUser, true);
        } else {
            // dd($user);
            // create a new user
            $newUser = [];
            $newUser['first_name']      = $user->user['given_name'];
            $newUser['last_name']       = $user->user['family_name'];
            $newUser['email']           = $user->email;
            $newUser['google_id']       = $user->id;
            $newUser['profile']         = $user->avatar_original;
            $newUser['status']          = "Active";
            $data = $this->userRepository->createUser($newUser);
            auth()->login($data, true);
        }
        return redirect()->to('/home');
    }
}
