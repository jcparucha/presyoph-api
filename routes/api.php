<?php

use App\Http\Controllers\V1\AuthController;
use App\Http\Controllers\V1\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/ping', function () {
    return response()->json(['message' => 'pong']);
});

Route::prefix('/v1')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    })->middleware('auth:sanctum');

    // SPA should request to GET sanctum/csrf-cookie
    // and save the values in XSRF-TOKEN and include XSRF-TOKEN in response

    Route::controller(AuthController::class)->group(function () {
        Route::post('/register', 'register')->name('register');
        Route::post('/login', 'login')->name('login');
        Route::post('/logout', 'logout')->middleware('auth:sanctum');
    });

    // ERRORS FOUND WHEN USING IT TOGETHER ELOQUENT RESOURCE
    //Unresolvable dependency resolving [Parameter #0 [ <required> $resource ]] in class Illuminate\\Http\\Resources\\Json\\JsonResource
    // Route::middleware(['auth:sanctum'])->apiResource(
    //     'products',
    //     ProductResource::class,
    // );

    function modelNotFound(string $model)
    {
        return fn() => response()->json(
            ['error' => ucfirst($model) . ' not found.'],
            404,
        );
    }

    Route::middleware(['auth:sanctum'])
        ->controller(ProductController::class)
        ->missing(modelNotFound('product'))
        ->name('products.')
        ->group(function () {
            // NOTE: GET should be accessible to GUEST users
            Route::withoutMiddleware(['auth:sanctum'])->group(function () {
                Route::get('/products', 'index');
                Route::get('/products/{product}', 'show')
                    ->whereNumber('product')
                    ->name('show');
            });
            Route::post('/products', 'store');
            Route::patch('/products/{product}', 'update')->whereNumber(
                'product',
            );
        });

    Route::fallback(function () {
        return response()->json(['message' => 'are you lost?'], 404);
    });
});
