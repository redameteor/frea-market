<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseRequest extends FormRequest
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
            'payment_method'   => 'required|string',
            'delivery_address' => 'required|string|max:255',
        ];
    }

    public function messages()
    {
        return [
            'payment_method.required'   => '支払方法を選択してください',
            'delivery_address.required' => '配送先を入力してください',
        ];
    }
}
