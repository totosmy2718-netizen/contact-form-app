<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'gender' => 'required|integer|in:1,2,3',
            'email' => 'required|email',
            'tel1' => 'required|numeric',
            'tel2' => 'required|numeric',
            'tel3' => 'required|numeric',
            'address' => 'required|string|max:255',
            'building' => 'nullable|string|max:255',
            'category_id' => 'required|integer|exists:categories,id',
            'detail' => 'required|string|max:120',
        ];
    }

    /**
     * エラーメッセージ
     */
    public function messages(): array
    {
        return [
            'first_name.required' => '姓を入力してください',
            'first_name.max' => '姓は255文字以内で入力してください',

            'last_name.required' => '名を入力してください',
            'last_name.max' => '名は255文字以内で入力してください',

            'gender.required' => '性別を選択してください',
            'gender.in' => '性別を正しく選択してください',

            'email.required' => 'メールアドレスを入力してください',
            'email.email' => 'メールアドレスの形式で入力してください',

            'tel1.required' => '電話番号を入力してください',
            'tel2.required' => '電話番号を入力してください',
            'tel3.required' => '電話番号を入力してください',

            'tel1.numeric' => '電話番号は数字で入力してください',
            'tel2.numeric' => '電話番号は数字で入力してください',
            'tel3.numeric' => '電話番号は数字で入力してください',

            'address.required' => '住所を入力してください',
            'address.max' => '住所は255文字以内で入力してください',

            'building.max' => '建物名は255文字以内で入力してください',

            'category_id.required' => 'お問い合わせの種類を選択してください',
            'category_id.exists' => 'お問い合わせの種類を正しく選択してください',

            'detail.required' => 'お問い合わせ内容を入力してください',
            'detail.max' => 'お問い合わせ内容は120文字以内で入力してください',
        ];
    }
}
