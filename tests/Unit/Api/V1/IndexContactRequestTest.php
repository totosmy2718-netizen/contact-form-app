<?php

namespace Tests\Unit\Api\V1;

use App\Http\Requests\Api\V1\IndexContactRequest;
use PHPUnit\Framework\TestCase;

class IndexContactRequestTest extends TestCase
{
    /**
     * @test
     */
    public function 一覧検索用のバリデーションルールが設定されている(): void
    {
        // Arrange（準備）
        $request = new IndexContactRequest;

        // Act（実行）
        $rules = $request->rules();

        // Assert（確認）
        $this->assertSame('nullable|string|max:255', $rules['keyword']);
        $this->assertSame('nullable|integer|in:1,2,3', $rules['gender']);
        $this->assertSame('nullable|integer|exists:categories,id', $rules['category_id']);
        $this->assertSame('nullable|date', $rules['date']);
        $this->assertSame('nullable|integer|min:1|max:100', $rules['per_page']);
        $this->assertSame('nullable|integer|min:1', $rules['page']);
    }
}
