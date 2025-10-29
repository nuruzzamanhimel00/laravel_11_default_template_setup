<?php

namespace App\Http\Controllers\Auth;

use Carbon\Carbon;
use App\Models\User;
use App\Models\UserVerify;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;


class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        setPageMeta('Register');
        return view('admin.auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect()->intended(route('dashboard', absolute: false));
    }



    public function verifyAccount($token)
    {
        // Find the verification record by token
        $verifyUser = UserVerify::where('token', $token)->first();

        // Default error message
        $message = 'Sorry, your email cannot be identified.';

        // If the verification record exists
        if ($verifyUser) {
            $user = $verifyUser->user;

            // Check if the email is already verified
            if (is_null($user->email_verified_at)) {
                // Mark the email as verified
                $user->email_verified_at = now();
                $user->save();

                $message = "Your email has been verified. You can now login.";
            } else {
                $message = "Your email is already verified. You can now login.";
            }
        }

        // Redirect to the appropriate URL
        $externalUrl = env('EXTERNAL_URL');
        if ($externalUrl) {
            return redirect()->away($externalUrl)->with('success', $message);
        }

        return redirect('/')->with('success', $message);
    }


    public function emailVerification(Request $request)
    {

        try {

            // Validate the incoming request
            $request->validate([
                'email' => 'required|string|email|exists:users,email',
                'otp'   => 'required|digits:6',
            ]);
            // dd($request->all());
            DB::beginTransaction();

            // Retrieve the user with their OTP record
            $user = User::where('email', $request->email)
                ->with(['user_verify' => function ($query) use ($request) {
                    $query->where('otp', $request->otp);
                }])
                ->first();

            // Check if user with matching email and OTP exists
            if (!$user || !$user->user_verify) {
                return redirect()->route('thank.you.confirmation', ['error' => 'Invalid email or OTP.']);
            }

            // Check if already verified
            if (!is_null($user->email_verified_at)) {
                return redirect()->route('thank.you.confirmation', ['error' => 'Email already verified.']);
            }

            // Check if OTP is expired
            if (Carbon::now()->greaterThan(Carbon::parse($user->user_verify->expires_at))) {
                return redirect()->route('thank.you.confirmation', ['error' => 'OTP has expired.']);
            }

            // Mark email as verified
            $user->email_verified_at = now();
            $user->save();

            // Delete the used OTP
            $user->user_verify()->delete();

            DB::commit();

            // return redirect()->route('thank.you.confirmation', ['success' => 'Email verified successfully.']);
            return redirect()->away('https://grozaarbd.com/successful/');
        } catch (\Exception $e) {
            DB::rollBack();
            // return redirect()->route('thank.you.confirmation', ['error' => 'Something went wrong.']);
            return redirect()->away('https://grozaarbd.com/unsuccessful/');
        }
    }

}
