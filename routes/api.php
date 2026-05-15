<?php

use App\Http\Controllers\V1\AuthController;
use App\Http\Controllers\V1\CategoryController;
use App\Http\Controllers\V1\EstablishmentController;
use App\Http\Controllers\V1\ProductController;
use App\Http\Controllers\V1\ProductPriceController;
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

    function modelNotFound(string $model)
    {
        return fn() => response()->json(
            ['error' => ucfirst($model) . ' not found.'],
            404,
        );
    }

    Route::middleware(['auth:sanctum'])->group(function () {
        // Product Routes
        Route::controller(ProductController::class)
            ->missing(modelNotFound('Product'))
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
                Route::patch('/products/{product}', 'update')
                    ->whereNumber('product')
                    ->can('update', 'product');

                // Product Price Routes
                Route::controller(ProductPriceController::class)
                    ->name('prices.')
                    ->scopeBindings()
                    ->group(function () {
                        // NOTE: GET should be accessible to GUEST users
                        Route::withoutMiddleware(['auth:sanctum'])->group(
                            function () {
                                Route::get(
                                    '/products/{product}/prices',
                                    'index',
                                );
                                Route::get(
                                    '/products/{product}/prices/{price}',
                                    'show',
                                )
                                    ->scopeBindings()
                                    ->missing(modelNotFound('productPrice'))
                                    ->whereNumber('product')
                                    ->whereNumber('price')
                                    ->name('show');
                            },
                        );
                        Route::post('/products/{product}/prices', 'store');
                    });
            });

        Route::controller(CategoryController::class)
            ->missing(modelNotFound('Category'))
            ->name('category.')
            ->group(function () {
                Route::withoutMiddleware(['auth:sanctum'])->group(function () {
                    Route::get('/categories', 'index');
                    Route::get('/categories/{category}', 'show')
                        ->whereNumber('category')
                        ->name('show');
                });
                Route::post('/categories', 'store');
                Route::patch('/categories/{category}', 'update')
                    ->whereNumber('category')
                    ->can('update', 'category');
            });

        Route::controller(EstablishmentController::class)
            ->missing(modelNotFound('Establishment'))
            ->name('establishment.')
            ->group(function () {
                Route::withoutMiddleware(['auth:sanctum'])->group(function () {
                    Route::get('/establishments', 'index');
                    Route::get('/establishments/{establishment}', 'show')
                        ->whereNumber('establishment')
                        ->name('show');
                });
                Route::post('/establishments', 'store');
                Route::patch('/establishments/{establishment}', 'update')
                    ->whereNumber('establishment')
                    ->can('update', 'establishment');
            });
    });

    Route::fallback(function () {
        return response()->json(['message' => 'are you lost?'], 404);
    });
});
