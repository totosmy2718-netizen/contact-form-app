<?php

namespace App\Http\Controllers;
use App\Http\Requests\ContactRequest;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Contact;

use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        $tags = Tag::all();
        return view('contact.index', compact('categories', 'tags'));
    }

    public function confirm(ContactRequest $request)
    {
        $validated = $request->validated();

        $validated['tel'] =
            $validated['tel1'] .
            $validated['tel2'] .
            $validated['tel3'];
        $category = Category::find($validated['category_id']);

        $tags = Tag::whereIn(
            'id',
            $validated['tag_ids'] ?? []
        )->get();

        return view(
            'contact.confirm',
            compact('validated', 'category', 'tags')
        );
    }

    public function store(ContactRequest $request)
    {
        $validated = $request->validated();

        $contact = Contact::create([
            'category_id' => $validated['category_id'],
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'gender' => $validated['gender'],
            'email' => $validated['email'],
            'tel' => $validated['tel1'] . $validated['tel2'] . $validated['tel3'],
            'address' => $validated['address'],
            'building' => $validated['building'],
            'detail' => $validated['detail'],
        ]);

        $contact->tags()->attach($validated['tag_ids'] ?? []);

        return redirect('/thanks');
    }

    public function thanks()
    {
        return view('contact.thanks');
    }
}