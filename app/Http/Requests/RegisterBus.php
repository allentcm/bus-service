<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class RegisterBus extends FormRequest
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
        $user = $this->user();
        return [
            'name' => [
                'required', 
                'max:255',
                'regex:/^[a-zA-Z0-9\s]+$/',
                Rule::unique('buses')->where(function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
            ],
            'bus_stop_code' => ['required', 'alpha_num'],
            'service_no' => ['required', 'alpha_num'],
            'operator' => ['required', 'alpha_num'],
            'origin_code' => ['required', 'alpha_num'],
            'destination_code' => ['required', 'alpha_num']
        ];
    }
}
