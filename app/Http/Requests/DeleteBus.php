<?php

namespace App\Http\Requests;

use App\Bus;
use Illuminate\Foundation\Http\FormRequest;

class DeleteBus extends FormRequest
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
        return [
            //
        ];
    }
}
