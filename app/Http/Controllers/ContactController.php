<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactRequest;
use App\Http\Requests\ExportContactRequest;
use App\Models\Category;
use App\Models\Contact;
use App\Models\Tag;

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
            $validated['tel1'].
            $validated['tel2'].
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
            'tel' => $validated['tel1'].$validated['tel2'].$validated['tel3'],
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

    // お問い合わせ一覧をCSV出力
    public function export(ExportContactRequest $request)
    {
        $query = Contact::with('category');

        if ($request->filled('keyword')) {
            $keyword = $request->keyword;

            $query->where(function ($q) use ($keyword) {
                $q->where('first_name', 'like', "%{$keyword}%")
                    ->orWhere('last_name', 'like', "%{$keyword}%")
                    ->orWhere('email', 'like', "%{$keyword}%");
            });
        }

        if ($request->filled('gender') && $request->gender != 0) {
            $query->where('gender', $request->gender);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $contacts = $query->orderBy('created_at', 'desc')->get();

        return response()->streamDownload(function () use ($contacts) {

            $stream = fopen('php://output', 'w');

            fwrite($stream, "\xEF\xBB\xBF");

            fputcsv($stream, [
                'ID',
                '氏名',
                '性別',
                'メール',
                '電話',
                '住所',
                '建物',
                'カテゴリ',
                '内容',
                '作成日時',
            ]);

            foreach ($contacts as $contact) {
                fputcsv($stream, [
                    $contact->id,
                    $contact->last_name.' '.$contact->first_name,
                    match ($contact->gender) {
                        1 => '男性',
                        2 => '女性',
                        3 => 'その他',
                        default => '',
                    },
                    $contact->email,
                    $contact->tel,
                    $contact->address,
                    $contact->building,
                    $contact->category->content,
                    $contact->detail,
                    $contact->created_at,
                ]);
            }
            fclose($stream);

        }, 'contacts.csv');
    }
}
