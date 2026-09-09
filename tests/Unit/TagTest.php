<?php

namespace Tests\Unit;

use App\Models\Contact;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TagTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function タグは複数のお問い合わせを持つ(): void
    {
        $tag = Tag::factory()->create();
        $contacts = Contact::factory()->count(3)->create();

        $tag->contacts()->attach($contacts->pluck('id'));

        $tagContacts = $tag->contacts;

        $this->assertCount(3, $tagContacts);
        $this->assertInstanceOf(Contact::class, $tagContacts->first());
    }
}
