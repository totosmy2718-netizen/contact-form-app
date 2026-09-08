<?php

namespace Tests\Unit;

use App\Http\Requests\IndexContactRequest;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class IndexContactRequestTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 正しい検索条件はバリデーションを通過する(): void
    {
        $category = Category::factory()->create();

        $data = [
            'keyword' => '山田',
            'gender' => 1,
            'category_id' => $category->id,
            'date' => '2026-09-08',
        ];

        $request = new IndexContactRequest();

        $validator = Validator::make($data, $request->rules());

        $this->assertTrue($validator->passes());
    }

    /** @test */
    public function 性別が不正な値だとバリデーションエラーになる(): void
    {
        $data = [
            'gender' => 4,
        ];

        $request = new IndexContactRequest();

        $validator = Validator::make($data, $request->rules());

        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('gender'));
    }

    /** @test */
    public function キーワードは255文字まで入力できる(): void
    {
        $data = [
            'keyword' => str_repeat('あ', 255),
        ];

        $request = new IndexContactRequest();

        $validator = Validator::make($data, $request->rules());

        $this->assertTrue($validator->passes());
    }

    /** @test */
    public function キーワードが256文字以上だとバリデーションエラーになる(): void
    {
        $data = [
            'keyword' => str_repeat('あ', 256),
        ];

        $request = new IndexContactRequest();

        $validator = Validator::make($data, $request->rules());

        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('keyword'));
    }

    /** @test */
    public function 存在するカテゴリーIDはバリデーションを通過する(): void
    {
        $category = Category::factory()->create();

        $data = [
            'category_id' => $category->id,
        ];

        $request = new IndexContactRequest();

        $validator = Validator::make($data, $request->rules());

        $this->assertTrue($validator->passes());
    }

    /** @test */
    public function 存在しないカテゴリーIDだとバリデーションエラーになる(): void
    {
        $data = [
            'category_id' => 99999,
        ];

        $request = new IndexContactRequest();

        $validator = Validator::make($data, $request->rules());

        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('category_id'));
    }

    /** @test */
    public function 正しい日付はバリデーションを通過する(): void
    {
        $data = [
            'date' => '2026-09-08',
        ];

        $request = new IndexContactRequest();

        $validator = Validator::make($data, $request->rules());

        $this->assertTrue($validator->passes());
    }

    /** @test */
    public function 不正な日付だとバリデーションエラーになる(): void
    {
        $data = [
            'date' => 'invalid-date',
        ];

        $request = new IndexContactRequest();

        $validator = Validator::make($data, $request->rules());

        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('date'));
    }
}