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
        // Arrange
        Category::factory()->create();
        Tag::factory()->create();

        // Act
        $response = $this->get('/');

        // Assert
        $response->assertStatus(200);
    }

    /** @test */
    public function お問い合わせ入力画面にカテゴリとタグが渡される(): void
    {
        // Arrange
        Category::factory()->create();
        Tag::factory()->create();

        // Act
        $response = $this->get('/');

        // Assert
        $response->assertViewHas('categories');
        $response->assertViewHas('tags');
    }

    /** @test */
    public function お問い合わせ入力画面にカテゴリ名とタグ名が表示される(): void
    {
        // Arrange
        $category = Category::factory()->create([
            'content' => '商品のお届けについて',
        ]);

        $tag = Tag::factory()->create([
            'name' => '質問',
        ]);

        // Act
        $response = $this->get('/');

        // Assert
        $response->assertSee($category->content);
        $response->assertSee($tag->name);
    }

    /** @test */
    public function サンクス画面を表示できる(): void
    {
        // Act
        $response = $this->get('/thanks');

        // Assert
        $response->assertStatus(200);
    }

    /** @test */
    public function ログイン画面を表示できる(): void
    {
        // Act
        $response = $this->get('/login');

        // Assert
        $response->assertStatus(200);
    }

    /** @test */
    public function 会員登録画面を表示できる(): void
    {
        // Act
        $response = $this->get('/register');

        // Assert
        $response->assertStatus(200);
    }

    /** @test */
    public function ログイン済みなら管理画面を表示できる(): void
    {
        // Arrange
        $user = User::factory()->create();

        // Act
        $response = $this
            ->actingAs($user)
            ->get('/admin');

        // Assert
        $response->assertStatus(200);
    }

    /** @test */
    public function 未ログインでは管理画面にアクセスできない(): void
    {
        // Act
        $response = $this->get('/admin');

        // Assert
        $response->assertRedirect('/login');
    }
}
