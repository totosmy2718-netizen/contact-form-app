<?php

namespace App\Http\Controllers;

use App\Http\Requests\TagRequest;
use App\Models\Tag;

class TagController extends Controller
{
    public function store(TagRequest $request)
    {
        $validated = $request->validated();

        $tag = Tag::create([
            'name' => $validated['name'],
        ]);

        return redirect('/admin');
    }

    public function update(TagRequest $request, Tag $tag)
    {
        $validated = $request->validated();

        $tag->update([
            'name' => $validated['name'],
        ]);

        return redirect('/admin');
    }

    public function edit(Tag $tag)
    {
        return view('admin.tags.edit', compact('tag'));
    }

    public function destroy(Tag $tag)
    {
        $tag->delete();

        return redirect('/admin');
    }
}
