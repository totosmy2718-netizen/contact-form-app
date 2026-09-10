<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactExportTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 認証済みユーザーはお問い合わせをcsvでエクスポートできる()
    {
        $user = User::factory()->create();

        $category = Category::create([
            'content' => '商品のお届けについて',
        ]);

        Contact::create([
            'category_id' => $category->id,
            'first_name' => '太郎',
            'last_name' => '山田',
            'gender' => 1,
            'email' => 'taro@example.com',
            'tel' => '09012345678',
            'address' => '東京都渋谷区',
            'building' => 'テストビル101',
            'detail' => 'お問い合わせ内容です',
        ]);

        $response = $this
            ->actingAs($user)
            ->get('/contacts/export');

        $response->assertStatus(200);

        $response->assertDownload('contacts.csv');

        $content = $response->streamedContent();

        $this->assertStringStartsWith("\xEF\xBB\xBF", $content);

        $this->assertStringContainsString(
            'ID,氏名,性別,メール,電話,住所,建物,カテゴリ,内容,作成日時',
            $content
        );

        // 登録したデータが含まれている
        $this->assertStringContainsString('山田 太郎', $content);
        $this->assertStringContainsString('男性', $content);
        $this->assertStringContainsString('商品のお届けについて', $content);
        $this->assertStringContainsString('taro@example.com', $content);
    }

    /** @test */
    public function 検索条件に一致するお問い合わせだけをcsvでエクスポートできる()
    {
        $user = User::factory()->create();

        $category = Category::create([
            'content' => '商品のお届けについて',
        ]);

        Contact::create([
            'category_id' => $category->id,
            'first_name' => '太郎',
            'last_name' => '山田',
            'gender' => 1,
            'email' => 'taro@example.com',
            'tel' => '09012345678',
            'address' => '東京都渋谷区',
            'building' => 'テストビル101',
            'detail' => '男性のお問い合わせ',
        ]);

        Contact::create([
            'category_id' => $category->id,
            'first_name' => '花子',
            'last_name' => '佐藤',
            'gender' => 2,
            'email' => 'hanako@example.com',
            'tel' => '08012345678',
            'address' => '東京都新宿区',
            'building' => 'サンプルビル202',
            'detail' => '女性のお問い合わせ',
        ]);

        $response = $this
            ->actingAs($user)
            ->get('/contacts/export?gender=2');

        $response->assertStatus(200);
        $response->assertDownload('contacts.csv');

        $content = $response->streamedContent();

        // gender=2 のデータは含まれる
        $this->assertStringContainsString('佐藤 花子', $content);
        $this->assertStringContainsString('hanako@example.com', $content);

        // gender=1 のデータは含まれない
        $this->assertStringNotContainsString('山田 太郎', $content);
        $this->assertStringNotContainsString('taro@example.com', $content);
    }

    /** @test */
    public function 未認証ユーザーはcsvをエクスポートできない()
    {
        $response = $this->get('/contacts/export');

        $response->assertRedirect('/login');
    }

    /** @test */
    public function 検索条件がない場合は全件を新着順でcsvに出力する()
    {
        $user = User::factory()->create();

        $category = Category::create([
            'content' => '商品のお届けについて',
        ]);

        $oldContact = Contact::create([
            'category_id' => $category->id,
            'first_name' => '太郎',
            'last_name' => '山田',
            'gender' => 1,
            'email' => 'old@example.com',
            'tel' => '09012345678',
            'address' => '東京都渋谷区',
            'building' => 'テストビル101',
            'detail' => '古いお問い合わせ',
        ]);

        $oldContact->created_at = now()->subDay();
        $oldContact->save();

        $newContact = Contact::create([
            'category_id' => $category->id,
            'first_name' => '花子',
            'last_name' => '佐藤',
            'gender' => 2,
            'email' => 'new@example.com',
            'tel' => '08012345678',
            'address' => '東京都新宿区',
            'building' => 'サンプルビル202',
            'detail' => '新しいお問い合わせ',
        ]);

        $newContact->created_at = now();
        $newContact->save();

        $response = $this
            ->actingAs($user)
            ->get('/contacts/export');

        $response->assertStatus(200);

        $content = $response->streamedContent();

        $newPosition = strpos($content, 'new@example.com');
        $oldPosition = strpos($content, 'old@example.com');

        $this->assertNotFalse($newPosition);
        $this->assertNotFalse($oldPosition);
        $this->assertTrue($newPosition < $oldPosition);
    }
}
