<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Api\V1\BaseController;
use App\Http\Requests\Auth\CustomerLoginRequest;
use App\Http\Requests\Auth\CustomerRegisterRequest;
use App\Http\Resources\Auth\AuthenticatedUserResource;
use App\Services\Auth\CustomerAuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerAuthController extends BaseController
{
    public function __construct(
        private readonly CustomerAuthService $auth,
    ) {
    }

    public function register(CustomerRegisterRequest $request): JsonResponse
    {
        $user = $this->auth->register($request->validated(), $request);

        return $this->successResponse(
            request: $request,
            resource: new AuthenticatedUserResource($user),
            message: 'Đăng ký tài khoản thành công.',
            status: 201,
        );
    }

    public function login(CustomerLoginRequest $request): JsonResponse
    {
        $user = $this->auth->login($request->validated(), $request);

        return $this->successResponse(
            request: $request,
            resource: new AuthenticatedUserResource($user),
            message: 'Đăng nhập thành công.',
        );
    }

    public function me(Request $request): JsonResponse
    {
        /** @var \App\Models\User $authenticatedUser */
        $authenticatedUser = $request->user();
        $user = $this->auth->currentCustomer($authenticatedUser);

        return $this->successResponse(
            request: $request,
            resource: new AuthenticatedUserResource($user),
            message: 'Lấy thông tin tài khoản thành công.',
        );
    }

    public function logout(Request $request): JsonResponse
    {
        /** @var \App\Models\User $authenticatedUser */
        $authenticatedUser = $request->user();
        $this->auth->logout($authenticatedUser, $request);

        return $this->successResponse(
            request: $request,
            resource: new AuthenticatedUserResource($authenticatedUser),
            message: 'Đăng xuất thành công.',
        );
    }
}
