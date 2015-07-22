<?php namespace Friluft\Http\Controllers;

use Friluft\Http\Requests\RegistrationRequest;
use Friluft\Http\Requests\LoginRequest;
use Friluft\Http\Requests\ResetPasswordRequest;
use Friluft\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Redirect;
use Friluft\User;
use Friluft\Role;
use Input;
use Mail;
use DB;
use \Carbon\Carbon;
use Friluft\Store;

class AuthController extends Controller {

	public function get_login() {
		return view('auth.login');
	}

	public function post_login(LoginRequest $request) {
		if (Auth::attempt(Input::only('email', 'password'), Input::has('remember') , true)) {
			return Redirect::intended('/');
		}

		return Redirect::back()->withInput()->with('error', 'Authentication Failed.');
	}

	public function get_logout() {
		Auth::logout();
		return Redirect::to('/');
	}

	public function get_register() {
		return view('auth.register');
	}

	public function post_register(RegistrationRequest $request) {
		$user = new User(Input::only('firstname', 'lastname', 'email'));
		$user->password = bcrypt(Input::get('password'));
		$user->role()->associate(Role::where('machine', '=', 'regular')->first());
		$user->save();

		Auth::login($user);

		return Redirect::home()->with('success', 'Thank you for signing up.');
	}

	public function get_forgot() {
		return view('auth.forgot');
	}

	public function post_forgot() {
		if (!Input::has('email')) return Redirect::back()->with('error', 'Email is required.');
		$user = User::where('email', '=', Input::get('email'))->first();

		# find user
		if ($user) {
			# generate token
			$token = str_random(60);

			# store the token
			DB::table('password_resets')->insert([
				'email' => $user->email,
				'token' => $token,
				'created_at' => new Carbon,
			]);

			# mail the token to the user
			Mail::send('emails.password', ['token' => $token, 'title' => 'Forgot Password'], function($mail) use($user) {
				$mail->to($user->email)->subject('Forgotten Password');
			});
		}

		return Redirect::back()->with('success', 'Check your email inbox for the next step in resetting your password.');
	}

	public function get_reset($token) {
		return view('auth.reset')->with('token', $token);
	}

	public function post_reset(ResetPasswordRequest $request) {
		# get token
		$token = Input::get('token');

		$password = Input::get('password');

		# exists?
		$reset = DB::table('password_resets')->where('token', '=', $token)->first();
		if ($reset) {
			$created = new Carbon($reset->created_at);

			# a token is only valid for 1 hour.
			if ($created->diffInMinutes() <= 60) {

				# the token is valid, now find the user.
				$user = DB::table('users')
					->where('email', '=', $reset->email)
					->update(['password' => bcrypt($password)]);

				# now delete the token
				DB::table('password_resets')->where('token', '=', $reset->token)->delete();

				return Redirect::to('user/login')->with('success', 'You can now login with your new password.');
			}else {
				# delete the token, it's not valid any longer.
				DB::table('password_resets')->where('token', '=', $reset->token)->delete();
			}
		}

		return Redirect::back()->with('error', "Authentication Failed.");
	}

}
