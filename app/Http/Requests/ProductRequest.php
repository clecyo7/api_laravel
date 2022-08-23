<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

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
            'name' => 'required|min:3|max:100',
            'description' => 'required|min:3|max:100',
        ];
    }


    public function messages()
    {
        return [
            'name.required'  => 'O campo Nome é obrigatório',
            'name.unique'  => 'Um produto com esse Nome já foi cadastrado',
            'name.min:3'  => 'O campo Nome precisa ter no mínimo 3 caracteres',
            'name.max:100'  => 'O campo Nome pode ter no máximo 100 caracteres',
            'description.min:3'  => 'O campo Descrição precisa ter no mínimo 3 caracteres',
            'description.max:100'  => 'O campo Descrição pode ter no máximo 100 caracteres',
            'description.required'  => 'O campo Descrição é obrigatório',
        ];
    }
}
