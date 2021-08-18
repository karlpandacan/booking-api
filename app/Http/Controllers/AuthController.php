<?php

/**
 * @author: Karl Pandacan
 * @page: Auth Controller
 * @created: 2021-08-18
 */

namespace App\Http\Controllers;

use StdClass;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Throwable;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            $requiredFields = ['username', 'password'];
            if (
                !$request->has($requiredFields) ||
                $request->username == '' ||
                $request->password == ''
            ) {
                $this->setMessage('E-Mail and Password is required.');
                return $this->sendResponse(['required-fileds' => $requiredFields], false, 404);
            }

            $username = $request->input('username');
            $password = $request->input('password');
            $user = User::login($username, $password)->first();
            if (!$user) {
                $this->setMessage('Invalid E-Mail or password');
                return $this->sendResponse(['required-fileds' => $requiredFields], false, 404);
            }
            $hash = md5($username . time()) . md5($user->id . time());
            $user->token = $hash;
            $user->token_expired_at = Carbon::now()->addHours(3);
            $user->save();

            $this->setMessage('Login successful.');
            return $this->sendResponse($user);
        } catch (Throwable $exception) {
            $this->setStatus(500);
            $this->setSuccess(false);
            $this->setMessage('Something went wrong. Please contact the admin.');
            return $this->sendResponse([$exception->getMessage()]);
        }
    }

    public function logout(Request $request)
    {
        $requestToken = $request->header('x-custom-session-id');
        $user = User::where('token', $requestToken)->first();
        $user->token = '';
        $user->token_expired_at = date('Y-m-d 00:00:00', strtotime('-1 day'));
        $user->save();
    }

    public function register(Request $request)
    {
        try {
            $requiredFields = ['username', 'password', 'fullname'];
            if (
                !$request->has($requiredFields) ||
                $request->username == '' ||
                $request->password == '' ||
                $request->fullname == ''
            ) {
                $this->setMessage('Full Name, Username and Password is required.');
                return $this->sendResponse(['required-fileds' => $requiredFields], false, 404);
            }

            $username = $request->input('username');
            $user = User::where('username', $username)->first();
            if (null !== $user) {
                $this->setMessage('Username is already used.');
                return $this->sendResponse(['required-fileds' => $requiredFields], false, 404);
            }
            $user = new User();
            $user->fullname = $request->fullname;
            $user->username = $request->username;
            $user->password = md5($request->password);
            $user->save();

            $this->setMessage('Registration successful.');
            return $this->sendResponse($user);
        } catch (Throwable $exception) {
            $this->setStatus(500);
            $this->setSuccess(false);
            $this->setMessage('Something went wrong. Please contact the admin.');
            return $this->sendResponse([$exception->getMessage()]);
        }
    }

    public function tokenCheck(Request $request)
    {
        $requestToken = $request->header('x-custom-session-id');
        if ($requestToken === null) {
            return $this->sendResponse([], false, 401, 'You are not Authorized to make this request.');
        }

        $activeToken = User::where('token', $requestToken)
            ->where('token_expired_at', '>=', Carbon::now())->first();
        if (!$activeToken) {
            return $this->sendResponse([], false, 401, 'Session Expired or Invalid.');
        }
        $this->setMessage('Token Still Active');
        return $this->sendResponse([]);
    }
}
