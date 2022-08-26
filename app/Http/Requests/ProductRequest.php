<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
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
        $product = $this->route()->parameter('name');

        return [
            'name' => "required|
                       min:3|
                       max:20|", 
                       Rule::unique('products')->ignore($product),
            'description' => 'max:1000',
            'image' => 'image',
            'category_id' => 'required|exists:categories,id'
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
            'category_id.required' => 'O campo Categoria é obrigatório',
            'category_id.exists' => 'O campo Categoria não existe',
        ];
    }
}
