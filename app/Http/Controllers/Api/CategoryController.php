<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\CategoryFormRequest;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class CategoryController extends Controller
{
    protected $category;

    public function __construct(Category $category)
    {
        $this->category = $category;
    }

    public function index(Request $request)
    {
        $category = $this->category->getResults($request->name);
        return response()->json($category);
    }


    // -> store sem validação
    // public function store(CategoryFormRequest $request){
    //     $category = $this->category->create($request->all());
    //     return response()->json($category, 201);
    // }

    public function store(CategoryFormRequest $request)
    {

        $categorySearch  = Category::Where('name', '=', "$request->name")->first();

        if (!isset($categorySearch->name)) {
            DB::beginTransaction();
            try {
                $category = new Category();
                $category->name = $request->name;
                if ($category->save()) {
                    DB::commit();
                    return response()->json(['status' => 'success', 'message' => 'Categoria foi cadastrada.', 201]);
                }
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
            }

            //caso já exista na base devolve o erro.
        } else {
            return response()->json(['status' => 'error', 'message' => 'Categoria já existente na base', 500]);
        }
    }

    public function show($id)
    {
        if (!$category = $this->category->find($id))
            return response()->json(['error' => 'Not found', 404]);

        return response()->json($category);
    }

    // -> update sem validação
    // public function update(Request $request, $id)
    // {
    //     $category = $this->category->find($id);
    //     $category->update($request->all());
    //     return response()->json($category);
    // }

    public function update(CategoryFormRequest $request, $id)
    {
        if (!$category = Category::find($id))
            return response()->json(['error' => 'Categoria não encontrado']);

        DB::beginTransaction();

        try {
            $category = Category::findOrFail($id);
            $category->name = $request->name;
            if ($category->save()) {
                DB::commit();
                return response()->json(['status' => 'success', 'message' => 'Categoria foi atualizado.']);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }


    // public function destroy($id)
    // {
    //     if (!$category = $this->category->find($id))
    //         return response()->json(['error' => 'Not found'], 400);
    //     $category->delete();
    //     return response()->json(['status' => 'success', 'message' => 'Categoria foi deletado.']);
    // }

    public function destroy1($id)
    {

        if (!$category = Category::find($id))
            return response()->json(['error' => 'Categoria não encontrado'], 404);

        DB::beginTransaction();

        try {
            $category = Category::findOrFail($id);
            if ($category->delete()) {
                DB::commit();
                return response()->json(['status' => 'success', 'message' => 'Categoria foi deletado.']);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 404);
        }
    }
}
