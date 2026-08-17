<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;
use App\Models\User;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /*
     * 　会員登録機能
     */

    // 名前が入力されていない場合、バリデーションメッセージが表示される
    public function test_name_is_required(): void
    {
        $response = $this->post('/register', [
            'name' => '',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors(['name']);
    }

    // メールアドレスが入力されていない場合、バリデーションメッセージが表示される
    public function test_email_is_required(): void
    {
        $response = $this->post('/register', [
            'name' => '西園寺公望',
            'email' => '',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    // パスワードが入力されていない場合、バリデーションメッセージが表示される
    public function test_password_is_required(): void
    {
        $response = $this->post('/register', [
            'name' => '西園寺公望',
            'email' => 'test@example.com',
            'password' => '',
            'password_confirmation' => '',
        ]);

        $response->assertSessionHasErrors(['password']);
    }

    // パスワードが7文字以下の場合、バリデーションメッセージが表示される
    public function test_password_must_be_at_least_8_characters(): void
    {
        $response = $this->post('/register', [
            'name' => '西園寺公望',
            'email' => 'test@example.com',
            'password' => 'pass123', // 7文字
            'password_confirmation' => 'pass123',
        ]);

        $response->assertSessionHasErrors(['password']);
    }

    // パスワードが確認用パスワードと一致しない場合、バリデーションメッセージが表示される
    public function test_password_must_match_confirmation(): void
    {
        $response = $this->post('/register', [
            'name' => '西園寺公望',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'different123', // 不一致
        ]);

        $response->assertSessionHasErrors(['password']);
    }

    // 全ての項目が入力されている場合、会員情報が登録され、プロフィール設定画面に遷移される
    public function test_user_can_register_and_redirects_to_profile_setting(): void
    {
        $registerData = [
            'name' => '西園寺公望',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->post('/register', $registerData);

        // 1. プロフィール設定画面（または設定したルート）にリダイレクトされるか
        $response->assertRedirect('/email/verify'); 

        // 2. データベースにユーザーが登録されたか
        $this->assertDatabaseHas('users', [
            'name' => '西園寺公望',
            'email' => 'test@example.com',
        ]);

        // 3. 登録後にログイン状態になっているか
        $this->assertAuthenticated();
    }

    /*
     * 　ログイン機能
     */

    // ログイン時メールアドレスが入力されていない場合、バリデーションメッセージが表示される
    public function test_email_is_required_for_login(): void
    {
        $response = $this->post('/login', [
            'email' => '',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    // ログイン時パスワードが入力されていない場合、バリデーションメッセージが表示される
    public function test_password_is_required_for_login(): void
    {
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => '',
        ]);

        $response->assertSessionHasErrors(['password']);
    }

    // ログイン時に正しい認証情報が入力されなかった場合、バリデーションメッセージが表示される
    public function test_user_cannot_login_with_incorrect_credentials(): void
    {
        // 事前にユーザーを作成
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'example@test.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors();
    }

    // ログイン時に正しい認証情報が入力された場合、ユーザーはログインできる
    public function test_user_can_login_with_correct_credentials(): void
    {
        // 事前にユーザーを作成
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/');
        $this->assertAuthenticatedAs($user);
    }

    /*
     * 　ログアウト機能
     */

    // ログアウトが正常に機能するかをテスト
    public function test_user_can_logout(): void
    {
        // 事前にユーザーを作成してログイン状態にする
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post('/logout');

        $response->assertRedirect('/');
        $this->assertGuest();
    }

    /*
    *    メール認証機能
    */

    // 会員登録後、認証メールが送信される
    public function test_verification_email_is_sent_after_registration():void
    {
        Notification::fake();

        $response = $this->post(route('register'), [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password1',
            'password_confirmation' => 'password1',
        ]);
        $user = User::where('email', 'test@example.com')->first();
        $this->assertNotNull($user);

        Notification::assertSentTo($user, VerifyEmail::class);
    }

    // メール認証誘導画面で「認証はこちらから」ボタンを押すとメール認証サイトに遷移する
    public function test_user_can_access_verification_link_from_email():void
    {
        $user = User::factory()->unverified()->create();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->getEmailForVerification())]
        );

        $response = $this->actingAs($user)->get($verificationUrl);
        $response->assertStatus(302);
    }

    // メール認証サイトのメール認証を完了すると、プロフィール設定画面に遷移する
    public function test_user_is_redirected_to_profile_setting_after_email_verification():void
    {
        $user = User::factory()->unverified()->create();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->getEmailForVerification())]
        );
        
        $response = $this->actingAs($user)->get($verificationUrl);
        $this->assertTrue($user->fresh()->hasVerifiedEmail());
        $response->assertRedirect(route('prof-edit'));
    }
}