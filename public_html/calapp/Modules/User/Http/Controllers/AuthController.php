<?php

namespace Modules\User\Http\Controllers;

use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Modules\Helper\Helpers\EmailsHelper;
use Modules\Helper\Repositories\HelperRepository;
use Modules\User\Http\Requests\LoginUserRequest;
use Modules\User\Http\Requests\RegisterUserRequest;
use Modules\User\Models\User\User;
use Modules\User\Models\Department\DepartmentRoleUser;
use Modules\User\Repositories\UserRepository;

/**
 * Class AuthController
 *
 * User Register and login.
 *
 * PHP version 7.1.3
 *
 * @category  Administration
 * @package   Modules\User
 * @author    Vipul Patel <vipul@chetsapp.com>
 * @author    Another Author <another@example.com>
 * @copyright 2019 Chetsapp Group
 * @license   Chetsapp Private Limited
 * @version   Release: @1.0@
 * @link      http://chetsapp.com
 * @since     Class available since Release 1.0
 */
class AuthController extends Controller
{
	use AuthenticatesUsers;

	protected $helper;
	protected $userRepo;
	protected $helperRepo;

	/**
	 * Create a new AuthController instance.
	 *
	 * @param EmailsHelper     $helper     [Object]
	 * @param UserRepository   $userRepo   [Object]
	 * @param HelperRepository $helperRepo [Object]
	 *
	 * @return void
	 */
	public function __construct(
		EmailsHelper $helper,
		UserRepository $userRepo,
		HelperRepository $helperRepo
	) {
		$this->middleware(
			'auth:api',
			['except' => ['login','register','logout','getVerifyUser']]
		);

		$this->helper = $helper;
		$this->userRepo = $userRepo;
		$this->helperRepo = $helperRepo;
	}

	/**
	 * Unauthorized.
	 *
	 * @return Response
	 */
	public function index()
	{
		return response()->json(['error' => 'Unauthorized'], 401);
	}

	/**
	 * Register a user via JWT.
	 *
	 * @param RegisterUserRequest $request [request value for registration]
	 *
	 * @return Response
	 */
	public function register(RegisterUserRequest $request)
	{
		$userData = [
			'user_generated_id' => $this->userRepo->getUserGeneratedId(),
			'firstname' => ucwords($request->get('firstname')),
			'lastname' => ucwords($request->get('lastname')),
			'username' => $request->get('username'),
			'email' => $request->get('email'),
			'password' => $request->get('password'),
			'email_verification_code' => str_random(40),
		];

		// --
		// Create user
		User::unguard();
		$user = User::create($userData);
		User::reguard();

		// --
		// Send email verification link
		if (!empty($user)) {
			// --
			// Add departments roles to register user
			$departmentRoleUsers = new DepartmentRoleUser();
			$departmentRoleUsers->department_id = 1;
			$departmentRoleUsers->role_id = 2;
			$departmentRoleUsers->user_id = $user->id;
			$departmentRoleUsers->save();

			try {
				$this->helper->sendActivateEmail($user, $request->get('password'));
			} catch (\Exception $e) {
				return response()->json(['error' => $e->getMessage()], 500);
			}
			return response()->json('success');
		} else {
			return response()->json(['error' => 'User has not been created.'], 400);
		}
	}

	/**
	 * Get a JWT via given credentials.
	 *
	 * @param LoginUserRequest $request [Request for login user]
	 *
	 * @return Response
	 */
	public function login(LoginUserRequest $request)
	{
		// --
		// Credentials
		$credentials = $this->credentials($request);

		/* If the class is using the ThrottlesLogins trait,
		we can automatically throttle */
		/* the login attempts for this application.
		We'll key this by the username and */
		// the IP address of the client making these requests into this application.
		if ($this->hasTooManyLoginAttempts($request)) {
			$this->fireLockoutEvent($request);
			return $this->sendLockoutResponse($request);
		}

		// --
		// Validate user
		if ($this->guard()->validate($this->credentials($request))) {
			$user = $this->guard()->getLastAttempted();
				
			// --
			// Make sure the user is active
			if ($user->is_active && $this->attemptLogin($request)) {
				// --
				// Email unverified
				if (isset($user->email_verified) && $user->email_verified == 0) {
					$this->incrementLoginAttempts($request);
					return response()->json(
						['error' => 'Your email is not verified yet, please check your inbox and verify your email.'],
						400
					);
				}

				// --
				// Authenticate user
				try {
					if (!$token = auth()->attempt($credentials)) {
						$this->incrementLoginAttempts($request);
						return response()->json(
							['error' => 'Incorrect username/email or password.'],
							400
						);
					}
				} catch (\Exception $e) {
					return response()->json(['error' => $e->getMessage()], 400);
				}

				try {
					if (Auth::attempt($credentials)) {
						// --
						// Save user
						$user = Auth::user();
						$user->last_ip = $request->ip();
						$user->last_login = date('Y-m-d H:i:s');
						$user->online_status = true;
						$user->save();
						return $this->respondWithToken($token);
					} else {
						return response()->json(['error' => 'Incorrect username/email or password'], 400);
					}
				} catch (\Exception $e) {
					return response()->json(['error' => $e->getMessage()], 400);
				}
			} else {
				// Increment the failed login attempts and redirect back to the
				// login form with an error message.
				$this->incrementLoginAttempts($request);
				return response()->json(
					['error' => 'Your account is not activated yet, contact to site admin.'],
					400
				);
			}
		} else {
			$this->incrementLoginAttempts($request);
			return response()->json(['error' => 'Incorrect username/email or password'], 400);
		}
	}

	/**
	 * Get the needed authorization credentials from the request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return array
	 */
	protected function credentials(Request $request)
	{
		$field = $this->field($request);
		return [
			$field => $request->get($this->username()),
			'password' => $request->get('password'),
		];
	}

	/**
	 * Determine if the request field is email or username.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return string
	 */
	public function field(Request $request)
	{
		$email = $this->username();
		return filter_var($request->get($email), FILTER_VALIDATE_EMAIL) ? $email : 'username';
	}

	/**
	 * Log the user out (Invalidate the token).
	 *
	 * @return Response
	 */
	public function logout()
	{
		$user = auth()->user();
		if (!empty($user)) {
			$user->online_status = false;
			$user->save();
			auth()->logout();
		}
		return response()->json('success');
	}

	/**
	 * Verify user email.
	 *
	 * @param String $code  [Email verification code]
	 * @param String $email [Email]
	 *
	 * @return Response
	 */
	public function getVerifyUser($code, $email = null)
	{
		$user = User::whereEmailVerificationCode($code)->first();
		if (!$user) {
			return response()->json(
				['error' => 'User or verification code has not been found.'],
				400
			);
		}

		if ($email) {
			$user->email = decrypt($email);
		}
		$user->email_verified = 1;
		$user->save();

		if (!is_null(config('app.front_url'))) {
			return redirect(config('app.front_url'));
		}
		return response()->json('Success');
	}

	/**
	 * Get the token array structure.
	 *
	 * @param String $token [JWT token]
	 *
	 * @return Response
	 */
	protected function respondWithToken($token)
	{
		return response()->json(
			[
			'token' => $token,
			'token_type' => 'bearer',
			'expires_in' => auth()->factory()->getTTL() * 90
			]
		);
	}
}
