<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ShopStoreRequest extends FormRequest
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
            'location_id'     => ['required', 'numeric'],
            'area_id'         => ['required', 'numeric'],
            'name'            => ['required', 'string', Rule::unique("shops", "name")->ignore($this->shop), 'max:191'],
            'description'     => ['nullable', 'string'],
            'delivery_charge' => ['nullable', 'numeric'],
            'lat'             => ['nullable'],
            'long'            => ['nullable'],
            'opening_time'    => ['nullable'],
            'closing_time'    => ['nullable'],
            'current_status'  => ['required', 'numeric'],
            'shopaddress'     => ['required', 'max:200'],
            'image'           => 'image|mimes:jpeg,png,jpg|max:5098',
        ];
    }

    public function attributes()
    {
        return [
            'location_id'     => trans('validation.attributes.location_id'),
            'area_id'         => trans('validation.attributes.area_id'),
            'name'            => trans('validation.attributes.name'),
            'description'     => trans('validation.attributes.description'),
            'delivery_charge' => trans('validation.attributes.delivery_charge'),
            'lat'             => trans('validation.attributes.lat'),
            'long'            => trans('validation.attributes.long'),
            'opening_time'    => trans('validation.attributes.opening_time'),
            'closing_time'    => trans('validation.attributes.closing_time'),
            'shopaddress'     => trans('validation.attributes.address'),
            'image'           => trans('validation.attributes.image'),
        ];
    }
}
