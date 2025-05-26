<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductsController extends Controller
{
     /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('pages.products.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $error = false;
        $message = null;
        $image_url = null;

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $image_url = Storage::url($path);
        }

        $created = Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'image_url' => $image_url
        ]);
        if($created){
            $message = 'Producto agregado exitosamente';
        } else {
            $error = true;
            $message = 'Ocurrió un error en el registro, intente nuevamente';
        }

        return [
            'error' => $error,
            'message' => $message
        ];
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $error = false;
        $message = null;
        $record = Product::find($id);
        if($record){
            $image_url = $record->image_url;
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('products', 'public');
                $image_url = Storage::url($path);
            }
            $record->update([
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
                'image_url' => $image_url
            ]);
            $message = 'Producto editado exitosamente';
        } else {
            $error = true;
            $message = 'Producto no encontrado o eliminado';
        }

        return [
            'error' => $error,
            'message' => $message
        ];
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $error = false;
        $message = null;
        $record = Product::find($id);
        $record_to_deleted = $record;
        if($record){
            if($record->delete()){
                $message = 'Producto eliminado exitosamente';
            }
        } else {
            $error = true;
            $message = 'Producto no encontrado o ya eliminado';
        }

        return [
            'error' => $error,
            'message' => $message
        ];
    }

    /**
     * Get all data
     */
    public function getAll(){
        $request = request();
        $data = Product::orderByDesc('id');
        // Filters
        if(!is_null($request->filters)){
            $filters = $request->filters;
            if(isset($filters['text']) && !is_null($filters['text']) && $filters['text'] != ''){
                $text = $filters['text'];
                $data->where('name','LIKE','%'.$text.'%')
                    ->orWhere('description','LIKE','%'.$text.'%');
            }
        }
        return ($request->paginated) ? $data->paginate(5) : $data->get();
    }

}
