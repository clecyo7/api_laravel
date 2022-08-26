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
        'image', 
        'category_id'
    ];


    public function rulesSearch()
    {
        return [
            'key-search' => 'required',
        ];
    }

    public function search($data)
    {
        return $this->where('name',  'LIKE', "%{$data['key-search']}%")
            ->orWhere('description', 'LIKE', "%{$data['key-search']}%")
            ->paginate(15);
    }

    public function getResults($data, $total)
    {
        if (!isset($data['filter']) && !isset($data['name']) && !isset($data['description']))
            return $this->paginate($total);

        return  $this->where(function ($query) use ($data) {
            if (isset($data['filter'])) {
                $filter = $data['filter'];
                $query->where('name', $filter);
                $query->orWhere('description', 'LIKE', "%{$filter}%");
            }

            if (isset($data['name'])) 
                $query->where('name', $data['name']);
            

            if (isset($data['description'])) {
                $description = $data['description'];
                $query->where('description', 'LIKE', "%{$description}%");
            }
        })
            ->paginate($total);
    }
}
