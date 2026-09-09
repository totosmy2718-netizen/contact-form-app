<?php

namespace Tests\Feature;

use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TagManagementTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function ログイン済みならタグを新規登録できる(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post('/admin/tags', [
                'name' => '新しいタグ',
            ]);

        $response->assertRedirect('/admin');

        $this->assertDatabaseHas('tags', [
            'name' => '新しいタグ',
        ]);
    }

    /** @test */
    public function ログイン済みならタグ編集画面を表示できる(): void
    {
        $user = User::factory()->create();

        $tag = Tag::factory()->create([
            'name' => '質問',
        ]);

        $response = $this
            ->actingAs($user)
            ->get('/admin/tags/' . $tag->id . '/edit');

        $response->assertStatus(200);
        $response->assertSee('質問');
    }

    /** @test */
    public function ログイン済みならタグを更新できる(): void
    {
        $user = User::factory()->create();

        $tag = Tag::factory()->create([
            'name' => '質問',
        ]);

        $response = $this
            ->actingAs($user)
            ->put('/admin/tags/' . $tag->id, [
                'name' => '重要',
            ]);

        $response->assertRedirect('/admin');

        $this->assertDatabaseHas('tags', [
            'id' => $tag->id,
            'name' => '重要',
        ]);
    }

    /** @test */
    public function ログイン済みならタグを削除できる(): void
    {
        $user = User::factory()->create();

        $tag = Tag::factory()->create([
            'name' => '質問',
        ]);

        $response = $this
            ->actingAs($user)
            ->delete('/admin/tags/' . $tag->id);

        $response->assertRedirect('/admin');

        $this->assertDatabaseMissing('tags', [
            'id' => $tag->id,
        ]);
    }

    /** @test */
    public function 未ログインではタグを新規登録できない(): void
    {
        $response = $this->post('/admin/tags', [
            'name' => '新しいタグ',
        ]);

        $response->assertRedirect('/login');

        $this->assertDatabaseMissing('tags', [
            'name' => '新しいタグ',
        ]);
    }

    /** @test */
    public function 未ログインではタグを更新できない(): void
    {
        $tag = Tag::factory()->create([
            'name' => '質問',
        ]);

        $response = $this->put('/admin/tags/' . $tag->id, [
            'name' => '重要',
        ]);

        $response->assertRedirect('/login');

        $this->assertDatabaseHas('tags', [
            'id' => $tag->id,
            'name' => '質問',
        ]);
    }

    /** @test */
    public function 未ログインではタグを削除できない(): void
    {
        $tag = Tag::factory()->create([
            'name' => '質問',
        ]);

        $response = $this->delete('/admin/tags/' . $tag->id);

        $response->assertRedirect('/login');

        $this->assertDatabaseHas('tags', [
            'id' => $tag->id,
        ]);
    }
}