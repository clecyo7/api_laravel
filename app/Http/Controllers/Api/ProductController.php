<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Http\Requests\ProductSearchRequest;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class ProductController extends Controller
{

    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    private $totalPage = 10;

    public function index(Request $request)
    {
        //$product = Product::paginate($this->totalPage); -> consulta simples 
        $product =  $this->product->search($request->all(), $this->totalPage);
        return response()->json(['data' => $product]);

        // ex: 
        //http://127.0.0.1:8000/api/products?page=2
        //http://127.0.0.1:8000/api/products?filter=aliquam
    }

    public function store(ProductRequest $request)
    {
        //busca na base se já existi 
        $productSearch = Product::Where('name', '=', "$request->name")->first();

        // se não existir será feito o cadastro
        if (!isset($productSearch->name)) {
            DB::beginTransaction();
            try {
                $product = new Product();
                $product->name = $request->name;
                $product->description = $request->description;
                if ($product->save()) {
                    DB::commit();
                    return response()->json(['status' => 'success', 'message' => 'Produto foi cadastrado.', 201]);
                }
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
            }

            //caso já exista na base devolve o erro.
        } else {
            return response()->json(['status' => 'error', 'message' => 'Produto já existente na base', 500]);
        }
    }


    public function show($id)
    {
        if (!$product = Product::find($id))
            return response()->json(['error' => 'Produto não encontrado'], 404);

        return response()->json(['data' => $product]);
    }


    public function update(ProductRequest $request, $id)
    {

        if (!$product = Product::find($id))
            return response()->json(['error' => 'Produto não encontrado']);

        DB::beginTransaction();

        try {
            $product = Product::findOrFail($id);
            $product->name = $request->name;
            $product->description = $request->description;
            if ($product->save()) {
                DB::commit();
                return response()->json(['status' => 'success', 'message' => 'Produto foi atualizado.']);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }


    public function destroy($id)
    {
        if (!$product = Product::find($id))
            return response()->json(['error' => 'Produto não encontrado'], 404);

        DB::beginTransaction();

        try {
            $product = Product::findOrFail($id);
            if ($product->delete()) {
                DB::commit();
                return response()->json(['status' => 'success', 'message' => 'Produto foi deletado.']);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 404);
        }
    }


    public function search(ProductSearchRequest $request)
    {

        $data = $request->all();
        $validate = validator($data, $this->product->rulesSearch());
        if ($validate->fails()) {
            $messages = $validate->messages();
            return response()->json(['validate.erro', $messages], 422);
        }

        $product = $this->product->search($data);
        return response()->json(['data' => $product]);
    }
}
