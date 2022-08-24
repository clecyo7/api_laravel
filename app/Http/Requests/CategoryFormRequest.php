<?php

namespace App\Http\Requests;

use App\Models\Category;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoryFormRequest extends FormRequest
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
 
        // quando for acionado pelo metodo update ele irá ignorar o id podendo assim recebi o mesmo dado
        $category = $this->route()->parameter('name');
        
        return [
            'name' => "required|min:3|max:50|",
            Rule::unique('categories')->ignore($category),

        ];
    }

    public function messages()
    {
      
        return [
            'name.required'  => 'O campo Nome é obrigatório',
            'name.unique'  => 'Uma Categoria com esse Nome já foi cadastrado',
            'name.min:3'  => 'O campo Nome precisa ter no mínimo 3 caracteres',
            'name.max:100'  => 'O campo Nome pode ter no máximo 100 caracteres',
        ];
    }
}
