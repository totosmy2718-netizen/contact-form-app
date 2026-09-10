<?php

namespace Tests\Feature\Api\V1;

use App\Models\Category;
use App\Models\Contact;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function お問い合わせ一覧を取得できる(): void
    {
        // Arrange（準備）
        $category = Category::factory()->create();

        Contact::factory()->count(3)->create([
            'category_id' => $category->id,
        ]);

        // Act（実行）
        $response = $this->getJson('/api/v1/contacts');

        // Assert（確認）
        $response->assertStatus(200)
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'first_name',
                        'last_name',
                        'gender',
                        'email',
                        'tel',
                        'address',
                        'building',
                        'detail',
                        'category',
                        'tags',
                        'created_at',
                        'updated_at',
                    ],
                ],
                'meta' => [
                    'current_page',
                    'last_page',
                    'per_page',
                    'total',
                ],
            ]);
    }

    /**
     * @test
     */
    public function お問い合わせ一覧をキーワードで検索できる(): void
    {
        // Arrange（準備）
        $category = Category::factory()->create();

        Contact::factory()->create([
            'category_id' => $category->id,
            'first_name' => '太郎',
            'last_name' => '山田',
            'email' => 'taro@example.com',
        ]);

        Contact::factory()->create([
            'category_id' => $category->id,
            'first_name' => '花子',
            'last_name' => '佐藤',
            'email' => 'hanako@example.com',
        ]);

        // Act（実行）
        $response = $this->getJson('/api/v1/contacts?keyword=太郎');

        // Assert（確認）
        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.first_name', '太郎');
    }

    /**
     * @test
     */
    public function お問い合わせ一覧を性別で検索できる(): void
    {
        // Arrange（準備）
        $category = Category::factory()->create();

        Contact::factory()->create([
            'category_id' => $category->id,
            'gender' => 1,
        ]);

        Contact::factory()->create([
            'category_id' => $category->id,
            'gender' => 2,
        ]);

        // Act（実行）
        $response = $this->getJson('/api/v1/contacts?gender=2');

        // Assert（確認）
        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.gender', 2);
    }

    /**
     * @test
     */
    public function お問い合わせ一覧をカテゴリで検索できる(): void
    {
        // Arrange（準備）
        $category1 = Category::factory()->create();
        $category2 = Category::factory()->create();

        Contact::factory()->create([
            'category_id' => $category1->id,
        ]);

        Contact::factory()->create([
            'category_id' => $category2->id,
        ]);

        // Act（実行）
        $response = $this->getJson(
            "/api/v1/contacts?category_id={$category1->id}"
        );

        // Assert（確認）
        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.category.id', $category1->id);
    }

    /**
     * @test
     */
    public function お問い合わせ一覧を日付で検索できる(): void
    {
        // Arrange（準備）
        $category = Category::factory()->create();

        Contact::factory()->create([
            'category_id' => $category->id,
            'created_at' => '2026-09-01 10:00:00',
        ]);

        Contact::factory()->create([
            'category_id' => $category->id,
            'created_at' => '2026-09-02 10:00:00',
        ]);

        // Act（実行）
        $response = $this->getJson(
            '/api/v1/contacts?date=2026-09-01'
        );

        // Assert（確認）
        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    /**
     * @test
     */
    public function お問い合わせ一覧をページネーションできる(): void
    {
        // Arrange（準備）
        $category = Category::factory()->create();

        Contact::factory()->count(5)->create([
            'category_id' => $category->id,
        ]);

        // Act（実行）
        $response = $this->getJson(
            '/api/v1/contacts?per_page=2&page=1'
        );

        // Assert（確認）
        $response->assertStatus(200)
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('meta.per_page', 2)
            ->assertJsonPath('meta.current_page', 1)
            ->assertJsonPath('meta.total', 5);
    }

    /**
     * @test
     */
    public function 一覧検索で不正な性別を指定すると422になる(): void
    {
        // Act（実行）
        $response = $this->getJson(
            '/api/v1/contacts?gender=9'
        );

        // Assert（確認）
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['gender']);
    }

    /**
     * @test
     */
    public function お問い合わせ詳細を取得できる(): void
    {
        // Arrange（準備）
        $category = Category::factory()->create();
        $tags = Tag::factory()->count(2)->create();

        $contact = Contact::factory()->create([
            'category_id' => $category->id,
        ]);

        $contact->tags()->sync($tags->pluck('id')->all());

        // Act（実行）
        $response = $this->getJson(
            "/api/v1/contacts/{$contact->id}"
        );

        // Assert（確認）
        $response->assertStatus(200)
            ->assertJsonPath('data.id', $contact->id)
            ->assertJsonPath('data.category.id', $category->id)
            ->assertJsonCount(2, 'data.tags');
    }

    /**
     * @test
     */
    public function 存在しないお問い合わせ詳細は404になる(): void
    {
        // Act（実行）
        $response = $this->getJson(
            '/api/v1/contacts/99999'
        );

        // Assert（確認）
        $response->assertStatus(404)
            ->assertExactJson([
                'error' => 'お問い合わせが見つかりませんでした。',
            ]);
    }

    /**
     * @test
     */
    public function お問い合わせを登録できる(): void
    {
        // Arrange（準備）
        $category = Category::factory()->create();
        $tags = Tag::factory()->count(2)->create();

        $data = [
            'first_name' => '太郎',
            'last_name' => '山田',
            'gender' => 1,
            'email' => 'taro@example.com',
            'tel' => '09012345678',
            'address' => '東京都新宿区',
            'building' => 'テストマンション101',
            'category_id' => $category->id,
            'detail' => 'APIからのお問い合わせです。',
            'tag_ids' => $tags->pluck('id')->all(),
        ];

        // Act（実行）
        $response = $this->postJson(
            '/api/v1/contacts',
            $data
        );

        // Assert（確認）
        $response->assertStatus(201)
            ->assertJsonPath('data.first_name', '太郎')
            ->assertJsonPath('data.category.id', $category->id)
            ->assertJsonCount(2, 'data.tags');

        $this->assertDatabaseHas('contacts', [
            'first_name' => '太郎',
            'email' => 'taro@example.com',
        ]);
    }

    /**
     * @test
     */
    public function お問い合わせ登録で不正な値を送ると422になる(): void
    {
        // Arrange（準備）
        $data = [
            'first_name' => '太郎',
            'last_name' => '山田',
            'gender' => 9,
            'email' => 'taro@example.com',
            'tel' => '123',
            'address' => '東京都',
            'category_id' => 99999,
            'detail' => 'テスト',
            'tag_ids' => [99999],
        ];

        // Act（実行）
        $response = $this->postJson(
            '/api/v1/contacts',
            $data
        );

        // Assert（確認）
        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'gender',
                'tel',
                'category_id',
                'tag_ids.0',
            ]);
    }

    /**
     * @test
     */
    public function お問い合わせを更新できる(): void
    {
        // Arrange（準備）
        $category = Category::factory()->create();
        $newCategory = Category::factory()->create();

        $oldTag = Tag::factory()->create();
        $newTags = Tag::factory()->count(2)->create();

        $contact = Contact::factory()->create([
            'category_id' => $category->id,
        ]);

        $contact->tags()->sync([$oldTag->id]);

        $data = [
            'first_name' => '太郎',
            'last_name' => '山田',
            'gender' => 1,
            'email' => 'updated@example.com',
            'tel' => '09012345678',
            'address' => '大阪府大阪市',
            'building' => '更新テスト202',
            'category_id' => $newCategory->id,
            'detail' => 'APIから更新しました。',
            'tag_ids' => $newTags->pluck('id')->all(),
        ];

        // Act（実行）
        $response = $this->putJson(
            "/api/v1/contacts/{$contact->id}",
            $data
        );

        // Assert（確認）
        $response->assertStatus(200)
            ->assertJsonPath('data.email', 'updated@example.com')
            ->assertJsonPath('data.category.id', $newCategory->id)
            ->assertJsonCount(2, 'data.tags');

        $this->assertDatabaseHas('contacts', [
            'id' => $contact->id,
            'email' => 'updated@example.com',
            'category_id' => $newCategory->id,
        ]);

        $this->assertDatabaseMissing('contact_tag', [
            'contact_id' => $contact->id,
            'tag_id' => $oldTag->id,
        ]);
    }

    /**
     * @test
     */
    public function 存在しないお問い合わせを更新すると404になる(): void
    {
        // Arrange（準備）
        $category = Category::factory()->create();

        $data = [
            'first_name' => '太郎',
            'last_name' => '山田',
            'gender' => 1,
            'email' => 'updated@example.com',
            'tel' => '09012345678',
            'address' => '大阪府大阪市',
            'building' => null,
            'category_id' => $category->id,
            'detail' => '更新テスト',
            'tag_ids' => [],
        ];

        // Act（実行）
        $response = $this->putJson(
            '/api/v1/contacts/99999',
            $data
        );

        // Assert（確認）
        $response->assertStatus(404)
            ->assertExactJson([
                'error' => 'お問い合わせが見つかりませんでした。',
            ]);
    }

    /**
     * @test
     */
    public function お問い合わせ更新で不正な値を送ると422になる(): void
    {
        // Arrange（準備）
        $category = Category::factory()->create();

        $contact = Contact::factory()->create([
            'category_id' => $category->id,
        ]);

        $data = [
            'first_name' => '太郎',
            'last_name' => '山田',
            'gender' => 9,
            'email' => 'updated@example.com',
            'tel' => '123',
            'address' => '大阪府',
            'category_id' => 99999,
            'detail' => '更新テスト',
            'tag_ids' => [99999],
        ];

        // Act（実行）
        $response = $this->putJson(
            "/api/v1/contacts/{$contact->id}",
            $data
        );

        // Assert（確認）
        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'gender',
                'tel',
                'category_id',
                'tag_ids.0',
            ]);
    }

    /**
     * @test
     */
    public function お問い合わせを削除できる(): void
    {
        // Arrange（準備）
        $category = Category::factory()->create();

        $contact = Contact::factory()->create([
            'category_id' => $category->id,
        ]);

        // Act（実行）
        $response = $this->deleteJson(
            "/api/v1/contacts/{$contact->id}"
        );

        // Assert（確認）
        $response->assertStatus(204);

        $this->assertDatabaseMissing('contacts', [
            'id' => $contact->id,
        ]);
    }

    /**
     * @test
     */
    public function 存在しないお問い合わせを削除すると404になる(): void
    {
        // Act（実行）
        $response = $this->deleteJson(
            '/api/v1/contacts/99999'
        );

        // Assert（確認）
        $response->assertStatus(404)
            ->assertExactJson([
                'error' => 'お問い合わせが見つかりませんでした。',
            ]);
    }
}
