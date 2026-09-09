<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Contact;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminContactTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function ログイン済みならお問い合わせ詳細を表示できる(): void
    {
        $user = User::factory()->create();

        $category = Category::factory()->create([
            'content' => '商品のお届けについて',
        ]);

        $tag = Tag::factory()->create([
            'name' => '質問',
        ]);

        $contact = Contact::factory()->create([
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

        $contact->tags()->attach($tag->id);

        $response = $this
            ->actingAs($user)
            ->get('/admin/contacts/'.$contact->id);

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
    public function お問い合わせを削除できる(): void
    {
        $user = User::factory()->create();
        $contact = Contact::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete('/admin/contacts/'.$contact->id);

        $response->assertRedirect('/admin');

        $this->assertDatabaseMissing('contacts', [
            'id' => $contact->id,
        ]);
    }

    /** @test */
    public function 未ログインではお問い合わせ詳細画面にアクセスできない(): void
    {
        $contact = Contact::factory()->create();

        $response = $this->get('/admin/contacts/'.$contact->id);

        $response->assertRedirect('/login');
    }

    /** @test */
    public function 未ログインではお問い合わせを削除できない(): void
    {
        $contact = Contact::factory()->create();

        $response = $this->delete('/admin/contacts/'.$contact->id);

        $response->assertRedirect('/login');

        $this->assertDatabaseHas('contacts', [
            'id' => $contact->id,
        ]);
    }
}
