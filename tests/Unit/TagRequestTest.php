<?php

namespace Tests\Unit;

use App\Http\Requests\TagRequest;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class TagRequestTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 正しいタグ名はバリデーションを通過する(): void
    {
        $data = [
            'name' => '質問',
        ];

        $request = new TagRequest();

        $validator = Validator::make(
            $data,
            $request->rules(),
            $request->messages()
        );

        $this->assertTrue($validator->passes());
    }

    /** @test */
    public function タグ名が空だとバリデーションエラーになる(): void
    {
        $data = [
            'name' => '',
        ];

        $request = new TagRequest();

        $validator = Validator::make(
            $data,
            $request->rules(),
            $request->messages()
        );

        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('name'));
    }

    /** @test */
    public function タグ名は50文字まで入力できる(): void
    {
        $data = [
            'name' => str_repeat('あ', 50),
        ];

        $request = new TagRequest();

        $validator = Validator::make(
            $data,
            $request->rules(),
            $request->messages()
        );

        $this->assertTrue($validator->passes());
    }

    /** @test */
    public function タグ名が51文字以上だとバリデーションエラーになる(): void
    {
        $data = [
            'name' => str_repeat('あ', 51),
        ];

        $request = new TagRequest();

        $validator = Validator::make(
            $data,
            $request->rules(),
            $request->messages()
        );

        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('name'));
    }

    /** @test */
    public function すでに存在するタグ名だとバリデーションエラーになる(): void
    {
        Tag::factory()->create([
            'name' => '質問',
        ]);

        $data = [
            'name' => '質問',
        ];

        $request = new TagRequest();

        $validator = Validator::make(
            $data,
            $request->rules(),
            $request->messages()
        );

        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('name'));
    }

    /** @test */
    public function 更新時は自分自身と同じタグ名でもバリデーションを通過する(): void
    {
        $tag = Tag::factory()->create([
            'name' => '質問',
        ]);

        $data = [
            'name' => '質問',
        ];

        $request = new TagRequest();

        $request->setRouteResolver(function () use ($tag) {
            return new class ($tag) {
                public function __construct(private Tag $tag)
                {
                }

                public function parameter($key, $default = null)
                {
                    return $key === 'tag' ? $this->tag : $default;
                }
            };
        });

        $validator = Validator::make(
            $data,
            $request->rules(),
            $request->messages()
        );

        $this->assertTrue($validator->passes());
    }

    /** @test */
    public function 更新時に他のタグと同じ名前にするとバリデーションエラーになる(): void
    {
        $tag = Tag::factory()->create([
            'name' => '質問',
        ]);

        Tag::factory()->create([
            'name' => '要望',
        ]);

        $data = [
            'name' => '要望',
        ];

        $request = new TagRequest();

        $request->setRouteResolver(function () use ($tag) {
            return new class ($tag) {
                public function __construct(private Tag $tag)
                {
                }

                public function parameter($key, $default = null)
                {
                    return $key === 'tag' ? $this->tag : $default;
                }
            };
        });

        $validator = Validator::make(
            $data,
            $request->rules(),
            $request->messages()
        );

        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('name'));
    }

}