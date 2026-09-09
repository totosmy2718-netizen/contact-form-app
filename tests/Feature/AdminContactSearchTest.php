<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminContactSearchTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function キーワードでお問い合わせを検索できる(): void
    {
        $user = User::factory()->create();

        Contact::factory()->create([
            'first_name' => '山田',
            'last_name' => '太郎',
            'email' => 'yamada@example.com',
        ]);

        Contact::factory()->create([
            'first_name' => '佐藤',
            'last_name' => '花子',
            'email' => 'sato@example.com',
        ]);

        $response = $this
            ->actingAs($user)
            ->get('/admin?keyword=山田');

        $response->assertStatus(200);
        $response->assertSee('山田');
        $response->assertDontSee('佐藤');
    }

    /** @test */
    public function 性別でお問い合わせを検索できる(): void
    {
        $user = User::factory()->create();

        Contact::factory()->create([
            'first_name' => '山田',
            'gender' => 1,
        ]);

        Contact::factory()->create([
            'first_name' => '佐藤',
            'gender' => 2,
        ]);

        $response = $this
            ->actingAs($user)
            ->get('/admin?gender=1');

        $response->assertStatus(200);
        $response->assertSee('山田');
        $response->assertDontSee('佐藤');
    }

    /** @test */
    public function カテゴリでお問い合わせを検索できる(): void
    {
        $user = User::factory()->create();

        $category1 = Category::factory()->create();
        $category2 = Category::factory()->create();

        Contact::factory()->create([
            'first_name' => '山田',
            'category_id' => $category1->id,
        ]);

        Contact::factory()->create([
            'first_name' => '佐藤',
            'category_id' => $category2->id,
        ]);

        $response = $this
            ->actingAs($user)
            ->get('/admin?category_id='.$category1->id);

        $response->assertStatus(200);
        $response->assertSee('山田');
        $response->assertDontSee('佐藤');
    }

    /** @test */
    public function 日付でお問い合わせを検索できる(): void
    {
        $user = User::factory()->create();

        Contact::factory()->create([
            'first_name' => '山田',
            'created_at' => '2026-09-09 10:00:00',
        ]);

        Contact::factory()->create([
            'first_name' => '佐藤',
            'created_at' => '2026-09-08 10:00:00',
        ]);

        $response = $this
            ->actingAs($user)
            ->get('/admin?date=2026-09-09');

        $response->assertStatus(200);
        $response->assertSee('山田');
        $response->assertDontSee('佐藤');
    }

    /** @test */
    public function お問い合わせ一覧は7件ずつ表示される(): void
    {
        $user = User::factory()->create();

        Contact::factory()->count(8)->create();

        $response = $this
            ->actingAs($user)
            ->get('/admin');

        $response->assertStatus(200);
        $response->assertViewHas('contacts', function ($contacts) {
            return $contacts->count() === 7
                && $contacts->total() === 8
                && $contacts->perPage() === 7;
        });
    }
}
