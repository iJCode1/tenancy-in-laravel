<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
// use Illuminate\Foundation\Auth\User;
// use Illuminate\Foundation\Auth\AuthenticatesUsers;

// MultiTenancy
use Hyn\Tenancy\Models\Hostname;
use Hyn\Tenancy\Models\Website;
use Hyn\Tenancy\Repositories\HostnameRepository;
use Hyn\Tenancy\Repositories\WebsiteRepository;
use Stringable;

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
    protected $redirectTo = RouteServiceProvider::HOME;

    protected $tenantName = null;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
        $hostname = app(\Hyn\Tenancy\Environment::class)->hostname();
        if($hostname){
            $fqdn = $hostname->fqdn;
            $this->tenantName = explode('.', $fqdn)[0];
        }
    }

    /**
     * Show the aplication registration form
     *
     * @return \Illuminate\Http\Response
     */
    public function showRegistrationForm(){
        return view('auth.register')->with('tenantName', $this->tenantName);    
    }


    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        if(!$this->tenantName){
            // Se concatena: hostname.crm_multi_tenancy_dev.test
            $fqdn = sprintf('%s.%s', $data['fqdn'], env('APP_DOMAIN'));
            return Validator::make($data, [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'fqdn' => ['required', 'string', 'max:20', Rule::unique('hostnames')->where(function ($query) use ($fqdn){
                    return $query->where('fqdn', $fqdn);
                })],
                'password' => ['required', 'string', 'min:8', 'confirmed'],
            ]);
        }
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:tenant.users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        $user = [
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password'])
        ];

        if(!$this->tenantName){
            return User::create($user);
        }
        return \App\Models\Tenant\User::create($user);
    }

    /**
     * The user has been registered
     *
     * @param  \Illuminate\Http\Request $request
     * @return mixed $user
     * @return mixed
     */
    protected function registered(Request $request, $user){
        if(!$this->tenantName){
            $fqdn = sprintf('%s.%s', request('fqdn'), env('APP_DOMAIN'));
            $website = new Website;
            $website->uuid = Str::random(10); // No pasarse de 32
            app(WebsiteRepository::class)->create($website);
            $hostname = new Hostname();
            $hostname->fqdn = $fqdn;
            $hostname = app(HostnameRepository::class)->create($hostname);
            app(HostnameRepository::class)->attach($hostname, $website);
        }
    }
}
