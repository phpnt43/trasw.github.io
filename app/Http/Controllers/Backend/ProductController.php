<?php

namespace App\Http\Controllers\Backend;


use App\Models\Shop\Category;
use App\Models\Shop\Product;
use App\Serializers\PaginateSerialize;
use App\Serializers\ProductSerialize;
use App\Services\Editors\ProductEditService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Serializers\CategorySerialize;

class ProductController extends BackendController
{
    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $serializer = new PaginateSerialize(ProductSerialize::class, ['categories']);

        $data = $serializer->serializeData(Product::allPaginate($request));

        return new JsonResponse($data);
    }

    /**
     * @param Request $request
     *
     * @return array|null
     */
    public function store(Request $request)
    {
        $product = Product::newDraft();

        DB::transaction(function () use ($product, $request) {
            (new ProductEditService($product))->fill($request);
        });

        return ProductSerialize::serialize($product);
    }


    /**
     * @param Request $request
     * @param Product $product
     *
     * @return array|null
     */
    public function update(Request $request, Product $product)
    {

        DB::transaction(function () use ($product, $request) {
            (new ProductEditService($product))->fill($request);
        });

        return ProductSerialize::serialize($product);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Shop\Product  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $category)
    {
        //
    }

    /**
     * @param $query
     *
     * @return JsonResponse
     */
    public function categories($query)
    {
        $category = Category::giveOnRequest($query);

        return response()->json($category, 200);
    }

}