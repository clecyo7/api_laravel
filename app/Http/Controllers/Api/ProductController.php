<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Http\Requests\ProductSearchRequest;
use App\Models\Product;
use Illuminate\Http\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

use function GuzzleHttp\Promise\all;

class ProductController extends Controller
{

    private $totalPage = 20;
    private $product;
    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    public function index(Request $request)
    {
        //$product = Product::paginate($this->totalPage); -> consulta simples 
        $product =  $this->product->getResults($request->all(), $this->totalPage);
        return response()->json(['data' => $product]);

        // ex: 
        //http://127.0.0.1:8000/api/products?page=2
        //http://127.0.0.1:8000/api/products?filter=aliquam
    }

    public function store(ProductRequest $request)
    {
        //busca na base se já existi 
        $productSearch = Product::Where('name', '=', "$request->name")->first();

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $name = Str::kebab($request->name);
            $extension = $request->image->extension();
            $nameFile = "{$name}.{$extension}";
        }

        // se não existir será feito o cadastro
        if (!isset($productSearch->name)) {
            DB::beginTransaction();
            try {
                $product = new Product();
                $product->name = $request->name;
                $product->description = $request->description;
                $product->category_id = $request->category_id;
                $product->image = $nameFile;
                if ($product->save()) {
                    $upload = $request->image->storeAs('products', $nameFile);
                    if (!$upload)
                        return response()->json(['error' => 'Fail_Upload'], 500);

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


    public function update(Request $request, $id)
    {

        if (!$product = Product::find($id))
            return response()->json(['error' => 'Produto não encontrado'], 404);

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            //DELETE NA IMAGE ANTERIOR
            if ($product->image) {
                if (Storage::exists("products/{$product->image}")) //VERIFICA SE EXISTE
                    Storage::delete("products/{$product->image}"); //SE EXISTER SERÁ DELETEDO 
            }
            $name = Str::kebab($request->name); //MUDA O NOME
            $extension = $request->image->extension(); //PEGA EXTENSION DA IMAGE
            $nameFile = "{$name}.{$extension}"; // SALV O NOVO NOME
        }

        DB::beginTransaction();
        try {
            $product = Product::findOrFail($id);
            $product->name = $request->name;
            $product->description = $request->description;
            $product->category_id = $request->category_id;
            $product->image = $request->image;
            if ($product->save()) {
                $upload = $request->image->storeAs('products', $nameFile);
                if (!$upload)
                    return response()->json(['error' => 'Fail_Upload'], 500);
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

        if ($product->image) {
            if (Storage::exists("products/{$product->image}")) //VERIFICA SE EXISTE
                Storage::delete("products/{$product->image}"); //SE EXISTER SERÁ DELETEDO 
        }

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
