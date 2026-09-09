<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexContactRequest;
use App\Models\Category;
use App\Models\Contact;
use App\Models\Tag;

class AdminController extends Controller
{
    public function index(IndexContactRequest $request)
    {

        $query = Contact::with(['category', 'tags']);
        // 検索フォーム
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;

            $query->where(function ($q) use ($keyword) {
                $q->where('first_name', 'like', "%{$keyword}%")
                    ->orWhere('last_name', 'like', "%{$keyword}%")
                    ->orWhere('email', 'like', "%{$keyword}%");
            });
        }

        // 性別が選択されていて、「全て（0）」ではない場合
        if ($request->filled('gender') && $request->gender != 0) {
            $query->where('gender', $request->gender);
        }

        // お問い合わせ種類が選択されている場合
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // 日付が入力されている場合
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        // 検索結果を7件ずつ表示
        $contacts = $query->paginate(7);

        $categories = Category::all();
        $tags = Tag::all();

        return view(
            'admin.index',
            compact('contacts', 'categories', 'tags')
        );
    }

    // お問い合わせ詳細画面を表示
    public function show(Contact $contact)
    {
        $contact->load(['category', 'tags']);

        return view('admin.show', compact('contact'));
    }

    // お問い合わせを削除
    public function destroy(Contact $contact)
    {
        $contact->delete();

        return redirect('/admin');
    }
}
