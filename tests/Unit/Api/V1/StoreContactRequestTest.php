<?php

namespace Tests\Unit\Api\V1;

use App\Http\Requests\Api\V1\StoreContactRequest;
use PHPUnit\Framework\TestCase;

class StoreContactRequestTest extends TestCase
{
    /**
     * @test
     */
    public function 登録用のバリデーションルールが設定されている(): void
    {
        // Arrange（準備）
        $request = new StoreContactRequest;

        // Act（実行）
        $rules = $request->rules();

        // Assert（確認）
        $this->assertSame('required|string|max:255', $rules['first_name']);
        $this->assertSame('required|string|max:255', $rules['last_name']);
        $this->assertSame('required|integer|in:1,2,3', $rules['gender']);
        $this->assertSame('required|string|email|max:255', $rules['email']);

        $this->assertSame(
            ['required', 'string', 'regex:/^\d{10,11}$/'],
            $rules['tel']
        );

        $this->assertSame('required|string|max:255', $rules['address']);
        $this->assertSame('nullable|string|max:255', $rules['building']);
        $this->assertSame('required|integer|exists:categories,id', $rules['category_id']);
        $this->assertSame('required|string|max:120', $rules['detail']);
        $this->assertSame('nullable|array', $rules['tag_ids']);
        $this->assertSame('integer|exists:tags,id', $rules['tag_ids.*']);
    }
}
