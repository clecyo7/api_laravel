<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Database\Factories\ProductsFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];


    public function rulesSearch(){
        return [
            'key-search' => 'required',
        ];
    }

    public function search($data){

       return $this->where('name',  'LIKE', "%{$data['key-search']}%")
                    ->orWhere('description', 'LIKE', "%{$data['key-search']}%")
                    ->paginate(15);
    }

}
