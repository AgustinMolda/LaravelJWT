<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * CONTROLADOR DE PRODUCTOS
 * 
 * Este controlador maneja todas las operaciones CRUD de productos.
 * Todas las rutas requieren autenticación y algunas requieren rol de administrador.
 */
class ProductController extends Controller
{
    /**
     * CREAR NUEVO PRODUCTO (SOLO ADMIN)
     * 
     * Crea un nuevo producto en la base de datos.
     * Requiere autenticación y rol de administrador.
     */
    public function addProduct(Request $request){
        // VALIDACIÓN DE DATOS DE ENTRADA
        $validator = Validator::make($request->all(),[
            'name'=>'required|string|min:10|max:100',
            'price' => 'required|numeric',
        ]);

        // SI LA VALIDACIÓN FALLA
        if($validator->fails()){
            return response()->json(['error'=>$validator->errors()], 422);
        }

        // CREACIÓN DEL PRODUCTO
        Product::create([
            'name'=> $request->get('name'),
            'price'=> $request->get('price'),
        ]);

        // RESPUESTA EXITOSA
        return response()->json(['message'=>'product added successfully'], 201);
    }

    /**
     * OBTENER TODOS LOS PRODUCTOS (USUARIOS AUTENTICADOS)
     * 
     * Devuelve una lista de todos los productos disponibles.
     * Requiere autenticación (cualquier usuario logueado).
     */
    public function getProducts(){
        // OBTENER TODOS LOS PRODUCTOS
        $products = Product::all();

        // VERIFICAR SI HAY PRODUCTOS
        if($products->isEmpty()){
            return response()->json(['message'=>'No products found'], 404);
        }

        // DEVOLVER PRODUCTOS
        return response()->json($products, 200);
    }

    /**
     * OBTENER PRODUCTO POR ID (SOLO ADMIN)
     * 
     * Devuelve un producto específico por su ID.
     * Requiere autenticación y rol de administrador.
     */
    public function getProductById($id){
        // BUSCAR PRODUCTO POR ID
        $product = Product::find($id);

        // VERIFICAR SI EXISTE EL PRODUCTO
        if(!$product){
            return response()->json(['message'=>'Product not found'], 404);
        }

        // DEVOLVER PRODUCTO
        return response()->json($product, 200);
    }

    /**
     * ACTUALIZAR PRODUCTO POR ID (SOLO ADMIN)
     * 
     * Actualiza un producto existente por su ID.
     * Requiere autenticación y rol de administrador.
     */
    public function updateProductById(Request $request, $id){
        // BUSCAR PRODUCTO POR ID
        $product = Product::find($id);

        // VERIFICAR SI EXISTE EL PRODUCTO
        if(!$product){
            return response()->json(['message'=>'Product not found'], 404);
        }

        // VALIDACIÓN DE DATOS DE ACTUALIZACIÓN
        $validator = Validator::make($request->all(),[
            'name' => 'sometimes|string|min:10|max:100',
            'price' => 'sometimes|numeric'
        ]);

        // SI LA VALIDACIÓN FALLA
        if($validator->fails()){
            return response()->json(['error'=> $validator->errors()], 422);
        }

        // ACTUALIZAR CAMPOS SI SE PROPORCIONAN
        if($request->has('name')){
            $product->name = $request->name;
        }

        if($request->has('price')){
            $product->price = $request->price;
        }

        // GUARDAR CAMBIOS
        $product->save();

        // RESPUESTA EXITOSA
        return response()->json(['message'=> 'Product updated successfully'], 200);
    }

    /**
     * ELIMINAR PRODUCTO POR ID (SOLO ADMIN)
     * 
     * Elimina un producto de la base de datos por su ID.
     * Requiere autenticación y rol de administrador.
     */
    public function deleteProductById($id){
        // BUSCAR PRODUCTO POR ID
        $product = Product::find($id);

        // VERIFICAR SI EXISTE EL PRODUCTO
        if(!$product){
            return response()->json(['message'=>'Product not found'], 404);
        }

        // ELIMINAR PRODUCTO
        $product->delete();

        // RESPUESTA EXITOSA
        return response()->json(['message'=>'Product deleted successfully'], 200);
    }
}
