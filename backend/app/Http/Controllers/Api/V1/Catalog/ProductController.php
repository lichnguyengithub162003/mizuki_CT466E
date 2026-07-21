<?php

namespace App\Http\Controllers\Api\V1\Catalog;

use App\Http\Controllers\Api\V1\BaseController;
use App\Http\Requests\Catalog\ProductIndexRequest;
use App\Http\Resources\Catalog\ProductListResource;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

class ProductController extends BaseController
{
    public function __construct(
        private readonly ProductService $products,
    ) {
    }

    /**
     * @OA\Get(
     *     path="/api/v1/products",
     *     operationId="listProducts",
     *     tags={"Catalog"},
     *     summary="Danh sách sản phẩm đang hiển thị",
     *     @OA\Parameter(name="page", in="query", @OA\Schema(type="integer", minimum=1)),
     *     @OA\Parameter(name="per_page", in="query", @OA\Schema(type="integer", default=20, maximum=100)),
     *     @OA\Parameter(name="category_id", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="brand_id", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="price_min", in="query", @OA\Schema(type="integer", minimum=0)),
     *     @OA\Parameter(name="price_max", in="query", @OA\Schema(type="integer", minimum=0)),
     *     @OA\Parameter(
     *         name="sort",
     *         in="query",
     *         @OA\Schema(type="string", enum={"newest", "best_selling", "price_asc", "price_desc"}, default="newest")
     *     ),
     *     @OA\Parameter(name="keyword", in="query", @OA\Schema(type="string")),
     *     @OA\Response(
     *         response=200,
     *         description="Danh sách sản phẩm có phân trang",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="message", type="string", example="Lấy danh sách sản phẩm thành công!"),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     ),
     *     @OA\Response(response=422, description="Query params không hợp lệ")
     * )
     */
    public function index(ProductIndexRequest $request): JsonResponse
    {
        $paginator = $this->products->getActiveProducts($request->validated());

        return $this->paginatedResponse(
            request: $request,
            resource: ProductListResource::collection($paginator->getCollection()),
            paginator: $paginator,
            message: 'Lấy danh sách sản phẩm thành công!',
        );
    }
}
