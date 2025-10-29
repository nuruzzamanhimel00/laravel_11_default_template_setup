<?php

namespace App\Http\Controllers\Api\V1\Auth;

use Carbon\Carbon;
use App\Models\User;
use App\Models\UserVerify;
use App\Traits\ApiResponse;
use Illuminate\Support\Str;
use Laravel\Passport\Token;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\RefreshToken;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Notifications\EmailVerifyNotifyMail;

class RegisterController
{
    use ApiResponse;

    public function forgetPasswordOtpUpdate(Request $request){
        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['required', 'string', 'min:8'],
            'forget_opt' => 'required|digits:6|exists:users,forget_opt', // Ensures exactly 6 digits and OTP exists
        ]);
        // Find the user with the given email and their associated OTP
        $user = User::where('forget_opt', $request->forget_opt)
                    ->first();
            // Check if the user exists
        if (!$user) {
            return $this->sendError('Forget OTP not found.', ['error' => 'Forget OTP not found'], 200);
        }
            // Check if the OTP has expired
        if (!$user->forget_expires_at || Carbon::now()->greaterThan(Carbon::parse($user->forget_expires_at))) {
            return $this->sendError('Forget OTP expired.', ['error' => 'Forget OTP has expired'], 400);
        }


        $user->password = Hash::make($request->password);
        $user->forget_opt = null;
        $user->forget_expires_at = null;
        $user->save();

        return response()->json(['message' => 'Password reset successful','success'=> true]);

    }
    public function forgetPasswordOtpVerify(Request $request){
        $request->validate([
            'forget_opt' => 'required|digits:6|exists:users,forget_opt', // Ensures exactly 6 digits and OTP exists
        ]);
        // Find the user with the given email and their associated OTP
        $user = User::where('forget_opt', $request->forget_opt)
                    ->first();
            // Check if the user exists
        if (!$user) {
            return $this->sendError('Forget OTP not found.', ['error' => 'Forget OTP not found'], 200);
        }

            // Check if the OTP has expired
        if (!$user->forget_expires_at || Carbon::now()->greaterThan(Carbon::parse($user->forget_expires_at))) {
            return $this->sendError('Forget OTP expired.', ['error' => 'Forget OTP has expired'], 400);
        }

        return response()->json(
                [
                    'success' => true,
                    'message' => 'Forget OTP verified successfully',

                ]
            );
        // dd($request->all());

    }
    public function forgetPassword(Request $request){

        $request->validate([
            'email' => [
                'required',
                'string',
                'email',
                Rule::exists('users', 'email')->where(function ($query) {
                    $query->whereIn('type', [User::TYPE_REGULAR_USER, User::TYPE_RESTAURANT]);
                }),
            ],
        ]);
        $email = $request->email;

        try {
            DB::beginTransaction();
            $user = User::where('email', $email)->first();
            // Check if the user exists
            if (!$user) {
                return $this->sendError('User not found.', ['error' => 'User not found'], 404);
            }

            if ($user) {
                $otp = generateOTP();
                $this->emailForgetOtp($user, $otp);

                $user->forget_opt = $otp;
                $user->forget_expires_at = Carbon::now()->addMinutes(5);
                $user->save();
            }

            DB::commit();
            return response()->json(
                [
                    'success' => true,
                    'message' => 'Forget password email sent successfully.',
                    'forget_opt' => $user->forget_opt
                ]
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage());
            //throw $th;
        }

    }
    public function forgetPasswordOtpAgainGenerate(Request $request){
        $request->validate([
            'forget_opt' => [
                'required',
                'digits:6',
                Rule::exists('users', 'forget_opt'),
            ],
        ]);

        $forget_opt = $request->forget_opt;

        try {
            DB::beginTransaction();
            $user = User::where('forget_opt', $forget_opt)->first();
            // Check if the user exists
            if (!$user) {
                return $this->sendError('User not found.', ['error' => 'User not found'], 404);
            }

            if ($user) {
                $otp = generateOTP();
                $this->emailForgetOtp($user, $otp);

                $user->forget_opt = $otp;
                $user->forget_expires_at = Carbon::now()->addMinutes(5);
                $user->save();
            }

            DB::commit();
            return response()->json(
                [
                    'success' => true,
                    'message' => 'Forget password OTP sent successfully.',
                    'forget_opt' => $user->forget_opt
                ]
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage());
            //throw $th;
        }

    }

    public function emailForgetOtp($user, $otp){
        try {
                Mail::send('admin.email.forget-password-with-otp', ['user' => $user, 'otp' => $otp], function ($message) use ($user) {
                    $message->to($user->email);
                    $message->subject('Email Forget Password Mail');
                });
            } catch (\Throwable $th) {
                // Log error if needed
            }
    }

    public function emailForgetOtpR($user){
        $user->forget_opt = '';
        $user->forget_expires_at = '';
        $user->save();
    }
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users',
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['required', 'string', 'min:8'],
            'type' => ['required', 'string', Rule::in([User::TYPE_RESTAURANT, User::TYPE_REGULAR_USER])],
            'phone' => ['required', 'max:25', Rule::unique('users')],
            'manager_phone' => $request->type == User::TYPE_RESTAURANT ? ['required', 'max:25']:['nullable', 'max:25'],
            'username' => ['required', 'string', 'max:255', Rule::unique('users')],
        ]);
        // return $request->all();

        try {
            DB::beginTransaction();
            $user = User::create([
                'first_name' => $request->name,
                'username' => $request->username,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'type' => $request->type,
                'phone' => $request->phone,
            ]);
            if($request->type == User::TYPE_RESTAURANT){
                $user->restaurant()->create([
                    'manager_phone' => $request->manager_phone
                ]);
            }

            // $token = Str::random(64);
            // UserVerify::create([
            //     'user_id' => $user->id,
            //     'token' => $token

            // ]);
            // // $user->notify(new EmailVerifyNotifyMail($token));
            // Mail::send('admin.email.emailVerificationEmail', ['token' => $token], function($message) use($request){

            //     $message->to($request->email);

            //     $message->subject('Email Verification Mail');

            // });

            $this->sendOTPToEmail($request->email);

            // $user->notify(new EmailVerifyNotifyMail($token));

            DB::commit();
            return $this->sendResponse('', 'User register successfully and Verification email send.');
        } catch (\Exception $e) {
            DB::rollBack();
            logger($e->getMessage());
            return $this->error($e->getMessage());
        }


        //  // Generate tokens using the helper function
        // $tokens = $this->generateTokens($user);

        // $success = [
        //     'token' => $tokens['access_token'],
        //     'refresh_token' => $tokens['refresh_token'],
        //     "token_type" => $tokens['token_type'],
        // ];

        // return $this->sendResponse($success, 'User login successfully.');
    }

    public function sendOTPToEmailAgain(Request $request){
        $request->validate([
            'email' => 'required|string|email|exists:users,email',
        ]);
        try {
            DB::beginTransaction();
            $user = User::where('email', $request->email)->first();
            // Check if the user exists
            if (!$user) {
                return $this->sendError('User not found.', ['error' => 'User not found'], 404);
            }

            // Check if the email is already verified
            if ($user->email_verified_at) {
                return $this->sendError('Email already verified.', ['error' => 'Email already verified'], 400);
            }

            // Check if the OTP is valid
            if (!$user->user_verify) {
                return $this->sendError('Invalid OTP.', ['error' => 'Invalid OTP'], 400);
            }


            $this->sendOTPToEmail($request->email);
            DB::commit();
            return $this->sendResponse('', 'Verification email again send.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage());
            //throw $th;
        }
    }

    public function sendOTPToEmail($email)
    {
        $user = User::where('email', $email)->first();

        if ($user) {
            $otp = generateOTP();
            $user->user_verify()->delete();

            UserVerify::create([
                'user_id' => $user->id,
                'otp' => $otp,
                'expires_at' => Carbon::now()->addMinutes(180), // Ensure timezone is applied
            ]);

            try {
                Mail::send('admin.email.emailVerificationEmail', ['user' => $user, 'otp' => $otp], function ($message) use ($user) {
                    $message->to($user->email);
                    $message->subject('Email Verification Mail');
                });
            } catch (\Throwable $th) {
                // Log error if needed
            }
        }
    }

    public function emailVerification(Request $request) {
        // Validate the request
        $request->validate([
            'email' => 'required|string|email|exists:users,email',
            'otp' => 'required|digits:6|exists:user_verifies,otp', // Ensures exactly 6 digits and OTP exists
        ]);

        // Find the user with the given email and their associated OTP
        $user = User::where('email', $request->email)
                    ->with(['user_verify' => function($query) use ($request) {
                        $query->where('otp', $request->otp);
                    }])
                    ->first();


        // Check if the user exists
        if (!$user) {
            return $this->sendError('User not found.', ['error' => 'User not found'], 404);
        }

        // Check if the email is already verified
        if ($user->email_verified_at) {
            return $this->sendError('Email already verified.', ['error' => 'Email already verified'], 400);
        }

        // Check if the OTP is valid
        if (!$user->user_verify) {
            return $this->sendError('Invalid OTP.', ['error' => 'Invalid OTP'], 400);
        }

            // Check if the OTP has expired
        if (!$user->user_verify || Carbon::now()->greaterThan(Carbon::parse($user->user_verify->expires_at))) {
            return $this->sendError('OTP expired.', ['error' => 'OTP has expired'], 400);
        }
        // Mark the email as verified
        $user->email_verified_at = now();
        $user->save();

        // Clear the OTP from the user_verifies table (optional)
        $user->user_verify()->delete();

        return $this->sendResponse('', 'Email verified successfully.');
    }


    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string', // Accepts both email or phone
            'password' => 'required|string',
        ]);

        $login = $request->login;
        $isEmail = filter_var($login, FILTER_VALIDATE_EMAIL);
        $loginField = $isEmail ? 'email' : 'username';

        $user = User::query()
            // ->where(function($query) use ($login, $isEmail, $loginField) {
            //     if ($isEmail) {
            //         $query->where($loginField, $login);
            //     } else {
            //         // For phone numbers, use LIKE if partial matching is needed
            //         // Or use exact match if phone numbers are stored uniformly
            //         $query->where($loginField, 'like', '%' . $login . '%');
            //     }
            // })
            ->where($loginField, $login)
            ->active()
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->sendError('Unauthorized.', ['error' => 'Invalid credentials'], 401);
        }

        // Ensure email is verified before allowing login
        if (!$user->hasVerifiedEmail()) {
            return $this->sendError('Unauthorized.', ['error' => 'Please verify your email before logging in'], 403);
        }

        // Log in the user
        Auth::login($user);

        // Generate tokens using the helper function
        $tokens = $this->generateTokens($user);

        $success = [
            'token' => $tokens['access_token'],
            'refresh_token' => $tokens['refresh_token'],
            'token_type' => $tokens['token_type'],
            'user'  => $user
        ];

        return $this->sendResponse($success, 'User login successfully.');
    }


    public static function generateTokens($user)
    {
        // Create a new access token with Passport
        $tokenResult = $user->createToken('API Token');
        $accessToken = $tokenResult->accessToken;
        $refreshToken = $tokenResult->token->id; // Passport automatically generates a refresh token

        return [
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'token_type' => 'Bearer',
            // 'expires_in' => now()->addHours(1)->timestamp, // Token expires in 1 hour
        ];
    }

    /**
     * Logout User
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh Token
     */
    public function refreshToken(Request $request)
    {
        $request->validate([
            'refresh_token' => 'required|string',
        ]);

        $token = Token::where('id', $request->refresh_token)->first();


        if (!$token) {
            return $this->sendError('TokenNotFound.', ['error' => 'Invalid refresh token'], 401);
            // return response()->json(['message' => 'Invalid refresh token'], 401);
        }

        // Revoke old tokens
        RefreshToken::where('access_token_id', $token->id)->delete();
        Token::where('id', $token->id)->delete();

        // Generate a new token
        $user = User::find($token->user_id);
        // Generate tokens using the helper function
        $tokens = $this->generateTokens($user);

        $success = [
            'token' => $tokens['access_token'],
            'refresh_token' => $tokens['refresh_token'],
            "token_type" => $tokens['token_type'],
        ];

        return $this->sendResponse($success, 'Token and Refresh Token Generated Successfully successfully.');
    }

    public function sendResponse($result, $message)
    {
        $response = [
            'success' => true,
            'data' => $result ?? '',
            'message' => $message ?? '',
        ];

        return response()->json($response, 200);
    }

    /**
     * return error response.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendError($error, $errorMessages = [], $code = 404)
    {
        $response = [
            'success' => false,
            'message' => $error,
        ];

        if (!empty($errorMessages)) {
            $response['data'] = $errorMessages;
        }

        return response()->json($response, $code);
    }
}
