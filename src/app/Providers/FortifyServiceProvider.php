<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Contracts\RegisterResponse;
use Laravel\Fortify\Contracts\LoginResponse;
use Laravel\Fortify\Contracts\VerifyEmailResponse;
use Illuminate\Http\JsonResponse;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);

        Fortify::registerView(function () {
            return view('auth.register');
        });

        Fortify::loginView(function () {
            return view('auth.login');
        });

        Fortify::verifyEmailView(function () {
            return view('auth.verify-email');
        });

        // 🚀 流れ①：会員登録ボタンを押した直後 ➡️ メール認証待ち画面へ強制移動
        $this->app->instance(RegisterResponse::class, new class implements RegisterResponse {
            public function toResponse($request) {
                return $request->wantsJson()
                    ? new JsonResponse('', 201)
                    : redirect()->route('verification.notice');
            }
        });

        // 🚀 流れ②：Mailtrapのリンクをクリックして認証成功した直後 ➡️ プロフィール設定画面へ移動
        $this->app->instance(VerifyEmailResponse::class, new class implements VerifyEmailResponse {
            public function toResponse($request) {
                return $request->wantsJson()
                    ? new JsonResponse('', 204)
                    : redirect()->route('profile.edit');
            }
        });

        // 🚀 その他：通常のログインをした直後 ➡️ 商品一覧ページ（トップ）へ移動
        $this->app->instance(LoginResponse::class, new class implements LoginResponse {
            public function toResponse($request) {
                return $request->wantsJson()
                    ? new JsonResponse('', 204)
                    : redirect()->route('index');
            }
        });
    }
}
