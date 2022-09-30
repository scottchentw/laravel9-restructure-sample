<?php

namespace App\Services;

use App\Models\User;
use App\Models\Voucher;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Notifications\NewUserWelcomeNotification;

class UserService
{
  public function uploadAvatar(Request $request): ?string
  {
    return ($request->hasFile('avatar'))
      ? $request->file('avatar')->store('public/avatars')
      : NULL;
  }

  public function createUser(array $userData): User
  {
    // Create user
    return User::create([
      'name' => $userData['name'],
      'email' => $userData['email'],
      'password' => Hash::make($userData['password']),
      'avatar' => $userData['avatar']
    ]);
  }

  public function createVoucherForUser(int $userId, int $percent = 10): string
  {
    $voucher = Voucher::create([
      'code' => Str::random(8),
      'discount_percent' => $percent,
      'user_id' => $userId
    ]);

    return $voucher->code;
  }

  // 發送歡迎郵件
  public function sendWelcomeEmail(User $user)
  {
    // 優待券
    $voucherCode = $this->createVoucherForUser($user->id);
    $user->notify(new NewUserWelcomeNotification($voucherCode));
  }
}
