<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Contact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function カテゴリーは複数のお問い合わせを持つ(): void
    {
        $category = Category::factory()->create();

        Contact::factory()->count(3)->create([
            'category_id' => $category->id,
        ]);

        $contacts = $category->contacts;

        $this->assertCount(3, $contacts);
        $this->assertInstanceOf(Contact::class, $contacts->first());
    }
}
