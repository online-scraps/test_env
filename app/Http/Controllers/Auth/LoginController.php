<?php

namespace App\Http\Controllers\Auth;


use App\Models\User;
use Illuminate\Http\Request;
use App\Utils\SessionActivityLog;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Artisan;
use Backpack\CRUD\app\Library\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    protected $data = []; // the information we send to the view

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
    use AuthenticatesUsers {
        logout as defaultLogout;
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $guard = backpack_guard_name();

        $this->middleware("guest:$guard", ['except' => 'logout']);

        // ----------------------------------
        // Use the admin prefix in all routes
        // ----------------------------------

        // If not logged in redirect here.
        $this->loginPath = property_exists($this, 'loginPath') ? $this->loginPath
            : backpack_url('login');
        // Redirect here after successful login.
        $this->redirectTo = property_exists($this, 'redirectTo') ? $this->redirectTo
            : backpack_url('dashboard');

        // Redirect here after logout.
        $this->redirectAfterLogout = property_exists($this, 'redirectAfterLogout') ? $this->redirectAfterLogout
            : backpack_url('login');
    }


    public function login(Request $request)
    {
        $this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.

        $userEmail = $request->email;
        $user = User::where('email', $userEmail)->first();

        if (!$user) {
            return back()->with(['message' => 'User doest not exists']);
        }

        if (!Hash::check($request->password, $user->password)) {
            return back()->with(['message' => 'Incorrect Password']);
        }

        if (method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            return $this->sendLockoutResponse($request);
        }

        if ($user->is_active) {
            //if email is give
            if ($this->guard()->attempt(['email' => $userEmail, 'password' => $request->password])) {
                return $this->sendLoginResponse($request);
            }
        } else {
            return view('auth.verification_code', ['user_id' => $user->id]);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    /**
     * Return custom username for authentication.
     *
     * @return string
     */
    public function username()
    {
        return backpack_authentication_column();
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $this->guard()->logout();

        //update the login-status in session log and update logout time
        $time = date("h:i:sa");
        $time = SessionActivityLog::englishToNepali($time);

        $session_id = $request->session()->get('sessionId');

        DB::connection('pgsql2')
            ->table('session_logs')
            ->where('id',$session_id)
            ->update(['is_currently_logged_in' => false, 'logout_time' => $time]);

         // And redirect to custom location
        return redirect($this->redirectAfterLogout);
    }

    /**
     * The user has logged out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    protected function loggedOut(Request $request)
    {
        return redirect($this->redirectAfterLogout);
    }

    /**
     * Get the guard to be used during logout.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return backpack_auth();
    }

    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        if($user->sup_org_id){
            Artisan::call('barcode-list:generate', [
                'super_org_id' => $user->sup_org_id
            ]);
        }
    }

    protected function sendLoginResponse(Request $request)
    {
        $user = backpack_user();
        $request->session()->regenerate();

        $sessionInfo = $request->getSession();

        $session_id = $sessionInfo->getId();
        $session_name = $sessionInfo->getName();

        $session = new SessionActivityLog();
        $currentSession  = $session->addSessionLog($session_id, $session_name , $is_currently_logged_in = True);

        // dd($currentSession);

        $request->session()->put('sessionId', $currentSession->id);      // Store sessionId to current session

        $this->clearLoginAttempts($request);

        return $this->authenticated($request, $this->guard()->user())
                ?: redirect()->intended($this->redirectPath());
    }
}
