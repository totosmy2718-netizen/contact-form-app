<?php

namespace Tests\Unit;

use App\Http\Requests\ContactRequest;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class ContactRequestTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 正しいお問い合わせ情報はバリデーションを通過する(): void
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
            'tel' => '09012345678',
            'address' => '東京都渋谷区',
            'building' => 'テストビル101',
            'category_id' => $category->id,
            'tag_ids' => [$tag->id],
            'detail' => 'お問い合わせ内容です。',
        ];

        $request = new ContactRequest;

        $validator = Validator::make(
            $data,
            $request->rules(),
            $request->messages()
        );

        $this->assertTrue($validator->passes());
    }

    /** @test */
    public function 電話番号は10桁ならバリデーションを通過する(): void
    {
        $data = [
            'tel' => '0312345678',
        ];

        $request = new ContactRequest;

        $validator = Validator::make($data, [
            'tel' => $request->rules()['tel'],
        ]);

        $this->assertTrue($validator->passes());
    }

    /** @test */
    public function 電話番号は11桁ならバリデーションを通過する(): void
    {
        $data = [
            'tel' => '09012345678',
        ];

        $request = new ContactRequest;

        $validator = Validator::make($data, [
            'tel' => $request->rules()['tel'],
        ]);

        $this->assertTrue($validator->passes());
    }

    /** @test */
    public function 電話番号が9桁だとバリデーションエラーになる(): void
    {
        $data = [
            'tel' => '031234567',
        ];

        $request = new ContactRequest;

        $validator = Validator::make($data, [
            'tel' => $request->rules()['tel'],
        ]);

        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('tel'));
    }

    /** @test */
    public function 電話番号が12桁だとバリデーションエラーになる(): void
    {
        $data = [
            'tel' => '090123456789',
        ];

        $request = new ContactRequest;

        $validator = Validator::make($data, [
            'tel' => $request->rules()['tel'],
        ]);

        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('tel'));
    }

    /** @test */
    public function 必須項目が空だとバリデーションエラーになる(): void
    {
        $data = [
            'first_name' => '',
            'last_name' => '',
            'gender' => '',
            'email' => '',
            'tel1' => '',
            'tel2' => '',
            'tel3' => '',
            'address' => '',
            'category_id' => '',
            'detail' => '',
        ];

        $request = new ContactRequest;

        $validator = Validator::make(
            $data,
            $request->rules(),
            $request->messages()
        );

        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('first_name'));
        $this->assertTrue($validator->errors()->has('last_name'));
        $this->assertTrue($validator->errors()->has('gender'));
        $this->assertTrue($validator->errors()->has('email'));
        $this->assertTrue($validator->errors()->has('tel1'));
        $this->assertTrue($validator->errors()->has('tel2'));
        $this->assertTrue($validator->errors()->has('tel3'));
        $this->assertTrue($validator->errors()->has('address'));
        $this->assertTrue($validator->errors()->has('category_id'));
        $this->assertTrue($validator->errors()->has('detail'));
    }

    /** @test */
    public function 姓は255文字まで入力できる(): void
    {
        $data = [
            'first_name' => str_repeat('あ', 255),
        ];

        $request = new ContactRequest;

        $validator = Validator::make($data, [
            'first_name' => $request->rules()['first_name'],
        ]);

        $this->assertTrue($validator->passes());
    }

    /** @test */
    public function 姓が256文字以上だとバリデーションエラーになる(): void
    {
        $data = [
            'first_name' => str_repeat('あ', 256),
        ];

        $request = new ContactRequest;

        $validator = Validator::make($data, [
            'first_name' => $request->rules()['first_name'],
        ]);

        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('first_name'));
    }

    /** @test */
    public function 名は255文字まで入力できる(): void
    {
        $data = [
            'last_name' => str_repeat('あ', 255),
        ];

        $request = new ContactRequest;

        $validator = Validator::make($data, [
            'last_name' => $request->rules()['last_name'],
        ]);

        $this->assertTrue($validator->passes());
    }

    /** @test */
    public function 名が256文字以上だとバリデーションエラーになる(): void
    {
        $data = [
            'last_name' => str_repeat('あ', 256),
        ];

        $request = new ContactRequest;

        $validator = Validator::make($data, [
            'last_name' => $request->rules()['last_name'],
        ]);

        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('last_name'));
    }

    /** @test */
    public function 性別が1から3以外だとバリデーションエラーになる(): void
    {
        $data = [
            'gender' => 4,
        ];

        $request = new ContactRequest;

        $validator = Validator::make($data, [
            'gender' => $request->rules()['gender'],
        ]);

        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('gender'));
    }

    /** @test */
    public function メールアドレスがメール形式でないとバリデーションエラーになる(): void
    {
        $data = [
            'email' => 'test',
        ];

        $request = new ContactRequest;

        $validator = Validator::make($data, [
            'email' => $request->rules()['email'],
        ]);

        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('email'));
    }

    /** @test */
    public function メールアドレスは255文字まで入力できる(): void
    {
        $email = str_repeat('a', 243).'@example.com';

        $data = [
            'email' => $email,
        ];

        $request = new ContactRequest;

        $validator = Validator::make($data, [
            'email' => $request->rules()['email'],
        ]);

        $this->assertEquals(255, strlen($email));
        $this->assertTrue($validator->passes());
    }

    /** @test */
    public function メールアドレスが256文字以上だとバリデーションエラーになる(): void
    {
        $email = str_repeat('a', 244).'@example.com';

        $data = [
            'email' => $email,
        ];

        $request = new ContactRequest;

        $validator = Validator::make($data, [
            'email' => $request->rules()['email'],
        ]);

        $this->assertEquals(256, strlen($email));
        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('email'));
    }

    /** @test */
    public function 電話番号に数字以外が含まれるとバリデーションエラーになる(): void
    {
        // Arrange
        $data = [
            'tel1' => 'abc',
            'tel2' => '1234',
            'tel3' => '5678',
        ];

        $request = new ContactRequest;

        // Act
        $validator = Validator::make($data, [
            'tel1' => $request->rules()['tel1'],
            'tel2' => $request->rules()['tel2'],
            'tel3' => $request->rules()['tel3'],
        ]);

        // Assert
        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('tel1'));
    }

    /** @test */
    public function 住所は255文字まで入力できる(): void
    {
        // Arrange
        $data = [
            'address' => str_repeat('あ', 255),
        ];

        $request = new ContactRequest;

        // Act
        $validator = Validator::make($data, [
            'address' => $request->rules()['address'],
        ]);

        // Assert
        $this->assertTrue($validator->passes());
    }

    /** @test */
    public function 住所が256文字以上だとバリデーションエラーになる(): void
    {
        // Arrange
        $data = [
            'address' => str_repeat('あ', 256),
        ];

        $request = new ContactRequest;

        // Act
        $validator = Validator::make($data, [
            'address' => $request->rules()['address'],
        ]);

        // Assert
        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('address'));
    }

    /** @test */
    public function 建物名は255文字まで入力できる(): void
    {
        // Arrange
        $data = [
            'building' => str_repeat('あ', 255),
        ];

        $request = new ContactRequest;

        // Act
        $validator = Validator::make($data, [
            'building' => $request->rules()['building'],
        ]);

        // Assert
        $this->assertTrue($validator->passes());
    }

    /** @test */
    public function 建物名が256文字以上だとバリデーションエラーになる(): void
    {
        // Arrange
        $data = [
            'building' => str_repeat('あ', 256),
        ];

        $request = new ContactRequest;

        // Act
        $validator = Validator::make($data, [
            'building' => $request->rules()['building'],
        ]);

        // Assert
        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('building'));
    }

    /** @test */
    public function 存在しないカテゴリー_i_dだとバリデーションエラーになる(): void
    {
        // Arrange
        $data = [
            'category_id' => 99999,
        ];

        $request = new ContactRequest;

        // Act
        $validator = Validator::make($data, [
            'category_id' => $request->rules()['category_id'],
        ]);

        // Assert
        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('category_id'));
    }

    /** @test */
    public function タグ_i_dが配列でないとバリデーションエラーになる(): void
    {
        // Arrange
        $data = [
            'tag_ids' => '1',
        ];

        $request = new ContactRequest;

        // Act
        $validator = Validator::make($data, [
            'tag_ids' => $request->rules()['tag_ids'],
        ]);

        // Assert
        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('tag_ids'));
    }

    /** @test */
    public function 存在しないタグ_i_dだとバリデーションエラーになる(): void
    {
        // Arrange
        $data = [
            'tag_ids' => [99999],
        ];

        $request = new ContactRequest;

        // Act
        $validator = Validator::make($data, [
            'tag_ids' => $request->rules()['tag_ids'],
            'tag_ids.*' => $request->rules()['tag_ids.*'],
        ]);

        // Assert
        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('tag_ids.0'));
    }

    /** @test */
    public function お問い合わせ内容は120文字まで入力できる(): void
    {
        // Arrange
        $data = [
            'detail' => str_repeat('あ', 120),
        ];

        $request = new ContactRequest;

        // Act
        $validator = Validator::make($data, [
            'detail' => $request->rules()['detail'],
        ]);

        // Assert
        $this->assertTrue($validator->passes());
    }

    /** @test */
    public function お問い合わせ内容が121文字以上だとバリデーションエラーになる(): void
    {
        // Arrange
        $data = [
            'detail' => str_repeat('あ', 121),
        ];

        $request = new ContactRequest;

        // Act
        $validator = Validator::make($data, [
            'detail' => $request->rules()['detail'],
        ]);

        // Assert
        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('detail'));
    }
}
