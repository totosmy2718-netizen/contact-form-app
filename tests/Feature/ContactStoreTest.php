<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactStoreTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function お問い合わせを送信するとデータベースに保存される(): void
    {
        $category = Category::factory()->create();

        $tag = Tag::factory()->create();

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

        $response = $this->post('/contacts', $data);

        $response->assertRedirect('/thanks');

        $this->assertDatabaseHas('contacts', [
            'category_id' => $category->id,
            'first_name' => '山田',
            'last_name' => '太郎',
            'gender' => 1,
            'email' => 'test@example.com',
            'tel' => '09012345678',
            'address' => '東京都渋谷区',
            'building' => 'テストビル101',
            'detail' => 'お問い合わせ内容です。',
        ]);
    }

    /** @test */
    public function お問い合わせに選択したタグが関連付けられる(): void
    {
        $category = Category::factory()->create();

        $tags = Tag::factory()->count(2)->create();

        $data = [
            'first_name' => '山田',
            'last_name' => '太郎',
            'gender' => 1,
            'email' => 'test@example.com',
            'tel1' => '090',
            'tel2' => '1234',
            'tel3' => '5678',
            'address' => '東京都渋谷区',
            'building' => '',
            'category_id' => $category->id,
            'tag_ids' => $tags->pluck('id')->toArray(),
            'detail' => 'お問い合わせ内容です。',
        ];

        $this->post('/contacts', $data);

        $this->assertDatabaseHas('contact_tag', [
            'tag_id' => $tags[0]->id,
        ]);

        $this->assertDatabaseHas('contact_tag', [
            'tag_id' => $tags[1]->id,
        ]);
    }
}