<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Models\Voucher;
use Illuminate\Http\Request;
use App\Services\UserService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use App\Http\Requests\StoreUserRequest;
use App\Providers\RouteServiceProvider;

use App\Jobs\NewUserNotifyAdminsJob;
use Exception;
use Illuminate\Support\Facades\DB;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(StoreUserRequest $request, UserService $userService)
    {

        // 資料庫 Transaction
        DB::transaction(function () use ($request, $userService) {
            $avatar = $userService->uploadAvatar($request);
            $user = $userService->createUser($request->validated() + ['avatar' => $avatar]);

            Auth::login($user);

            $userService->sendWelcomeEmail($user);

            // 測試丟出異常時，資料庫應該不會新增，歡迎信也不會寄出
            //throw new Exception("Something went wrong.");

            NewUserNotifyAdminsJob::dispatch($user);
        });




        return redirect(RouteServiceProvider::HOME);
    }
}
