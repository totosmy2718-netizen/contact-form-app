<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Contact;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function お問い合わせはカテゴリーに属する(): void
    {
        $category = Category::factory()->create();

        $contact = Contact::factory()->create([
            'category_id' => $category->id,
        ]);

        $contactCategory = $contact->category;

        $this->assertInstanceOf(Category::class, $contactCategory);
        $this->assertEquals($category->id, $contactCategory->id);
    }

    /** @test */
    public function お問い合わせは複数のタグを持つ(): void
    {
        $contact = Contact::factory()->create();
        $tags = Tag::factory()->count(3)->create();

        $contact->tags()->sync($tags->pluck('id'));

        $contactTags = $contact->tags;

        $this->assertCount(3, $contactTags);
        $this->assertInstanceOf(Tag::class, $contactTags->first());
    }
}
