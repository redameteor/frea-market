<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'        => 'required|string|max:20',
            'img_url'     => 'nullable|image|mimes:jpeg,png|max:2048',
            'postal_code' => 'required|string|size:8|regex:/^\d{3}-d{4}$/',
            'address'     => 'required|string|max:255',
            'building'    => 'nullable|string|max:255',
        ];
    }

    public function messages()
    {
        return[
            'name.required'        => 'ユーザー名を入力してください',
            'name.max'             => 'ユーザー名は20文字以内で入力してください',
            'img_url.image'        => '指定されたファイルは画像ではありません',
            'img_url.mimes'        => '画像は.jpegか.png形式でアップロードしてください',
            'postal_code.required' => '郵便番号を入力してください',
            'postal_code.size'     => '郵便番号はハイフンを含めた8文字で入力してください',
            'postal_code.regex'    => '郵便番号の形式で入力してください',
            'address.required'     => '住所を入力してください',
        ];
    }
}
