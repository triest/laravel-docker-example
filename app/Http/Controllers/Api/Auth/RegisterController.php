<?php

namespace App\Http\Controllers\Api\Auth;

use App\Core\Entity\Log\LogInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Register\LoginRequest;
use App\Http\Requests\Register\RegisterRequest;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $validated = $request->validated();

        if (User::query()->where('phone', $validated['phone'])->orWhere('email', $validated['email'])->exists()) {
            throw new Exception("Пользователь уже существует", 422);
        }

        $user = new User();

        $user->email = $validated['email'];
        $user->phone = $validated['phone'];
        $user->password = Hash::make($validated['password']);
        $user->save();

        return response()->json($user);
    }

    public function login(LoginRequest $request)
    {
        $user = User::query()->orderBy('id', 'desc')->where('email', $request['email'])->first();

        $user = $user->first();

        if (!$user) {
            throw new Exception(
                "Пользователь с " . (!empty($request->email) ? 'email ' . $request->email : 'номером телефона ' . $request->phone) . " не найден",
                422
            );
        }

        //auth attempt
        $auth = Auth::attempt(['id' => $user->id, 'password' => $request->password]);

        if (!$auth) {
            throw new Exception("Неверный логин или пароль", 401);
        }

        $user = Auth::user();


        $token = $user->createToken('token-name', ['server:update'])->plainTextToken;;

        $resultMessage = 'Успешно авторизован';

        return response()->json(
            [
                'message' => $resultMessage,
                'token' => $token,
            ]
        );
    }

    public function logout(Request $request)
    {
        return response()->json(["result" => $request->user()->currentAccessToken()->delete()]);
    }
}
