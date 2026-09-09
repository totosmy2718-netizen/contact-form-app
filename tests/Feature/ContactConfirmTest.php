<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactConfirmTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 正しい入力内容でお問い合わせ確認画面を表示できる(): void
    {
        // Arrange
        $category = Category::factory()->create([
            'content' => '商品のお届けについて',
        ]);

        $tag = Tag::factory()->create([
            'name' => '質問',
        ]);

        $data = [
            'first_name' => '山田',
            'last_name' => '太郎',
            'gender' => 1,
            'email' => 'test@example.com',
            'tel1' => '090',
            'tel2' => '1234',
            'tel3' => '5678',
            'address' => '東京都渋谷区',
            'building' => 'テストビル101',
            'category_id' => $category->id,
            'tag_ids' => [$tag->id],
            'detail' => 'お問い合わせ内容です。',
        ];

        // Act
        $response = $this->post('/contacts/confirm', $data);

        // Assert
        $response->assertStatus(200);

        $response->assertSee('山田');
        $response->assertSee('太郎');
        $response->assertSee('男性');
        $response->assertSee('test@example.com');
        $response->assertSee('09012345678');
        $response->assertSee('東京都渋谷区');
        $response->assertSee('テストビル101');
        $response->assertSee('商品のお届けについて');
        $response->assertSee('質問');
        $response->assertSee('お問い合わせ内容です。');
    }

    /** @test */
    public function 入力内容にエラーがある場合はお問い合わせ入力画面に戻る(): void
    {
        // Arrange
        $category = Category::factory()->create();

        $data = [
            'first_name' => '',
            'last_name' => '太郎',
            'gender' => 1,
            'email' => 'test@example.com',
            'tel1' => '090',
            'tel2' => '1234',
            'tel3' => '5678',
            'address' => '東京都渋谷区',
            'category_id' => $category->id,
            'detail' => 'お問い合わせ内容です。',
        ];

        $response = $this
            ->from('/')
            ->post('/contacts/confirm', $data);

        $response->assertRedirect('/');
        $response->assertSessionHasErrors('first_name');
    }
}
