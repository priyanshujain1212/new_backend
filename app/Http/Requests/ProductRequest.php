<?php

namespace App\Http\Requests;

use App\Rules\IniAmount;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
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
            'categories.*' => 'required',
            'name'         => ['required', 'string', Rule::unique("products", "name")->ignore($this->product), 'max:255'],
            'description'  => 'nullable|string|max:1000',
            'unit_price'   => ['required', new IniAmount],
            'status'       => 'required|numeric',
            'image'        => 'image|mimes:jpeg,png,jpg|max:5098',
        ];
    }

    public function attributes()
    {
        return [
            'name'        => trans('validation.attributes.name'),
            'image'       => trans('validation.attributes.image'),
            'location_id' => trans('validation.attributes.location_id'),
            'status'      => trans('validation.attributes.status'),
        ];
    }

}
