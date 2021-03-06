<?php

namespace App\Http\Requests;

use App\Bus;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateBus extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $busId = $this->route()->parameters['id'];
        // check if bus id exist
        $bus = Bus::find($busId);
        if ($bus == null) {
            return false;
        }

        return $bus->user->id == $this->user()->id;
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
            ]
        ];
    }
}
