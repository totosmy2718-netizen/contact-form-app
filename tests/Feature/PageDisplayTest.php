<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PageDisplayTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function お問い合わせ入力画面を表示できる(): void
    {
        Category::factory()->create();
        Tag::factory()->create();


        $response = $this->get('/');

        $response->assertStatus(200);
    }

    /** @test */
    public function ログイン画面を表示できる(): void
    {

        $response = $this->get('/login');


        $response->assertStatus(200);
    }

    /** @test */
    public function 会員登録画面を表示できる(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    /** @test */
    public function ログイン済みなら管理画面を表示できる(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/admin');

        $response->assertStatus(200);
    }

    /** @test */
    public function 未ログインでは管理画面にアクセスできない(): void
    {
        $response = $this->get('/admin');

        $response->assertRedirect('/login');
    }
}