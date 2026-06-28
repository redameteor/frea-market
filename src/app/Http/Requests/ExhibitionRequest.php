<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExhibitionRequest extends FormRequest
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
            'name'        => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'item_image'  => 'required|image|mimes:jpeg,png|max:4096',
            'category_id' => 'required',
            'condition'   => 'required|string',
            'price'       => 'required|integer|min:0',
        ];
    }

    public function messages()
    {
        return [
            'name.required'        => '商品名を入力してください',
            'description.required' => '商品説明を入力してください',
            'description.max'      => '商品説明は255文字以内で入力してください',
            'item_image.required'  => '商品画像をアップロードしてください',
            'item_image.mimes'     => '画像は.jpegか.png形式でアップロードしてください',
            'category_id.required' => '商品のカテゴリーを選択してください',
            'condition.required'   => '商品の状態を入力してください',
            'price.required'       => '商品価格を入力してください',
            'price.integer'        => '商品価格は数字で入力してください',
            'price.min'            => '商品価格は0円以上で入力してください',
        ];
    }
}
