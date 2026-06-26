<?php

use App\Http\Controllers\V1\AuthController;
use App\Http\Controllers\V1\BrandController;
use App\Http\Controllers\V1\CategoryController;
use App\Http\Controllers\V1\EstablishmentController;
use App\Http\Controllers\V1\GroceryListController;
use App\Http\Controllers\V1\ProductController;
use App\Http\Controllers\V1\ProductPriceController;
use App\Http\Controllers\V1\StoreTypeController;
use App\Http\Controllers\V1\TagController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

if (! function_exists('modelNotFound')) {
    function modelNotFound(string $model)
    {
        return fn () => response()->json(['error' => ucfirst($model).' not found.'], 404);
    }
}

Route::get('/ping', function () {
    return response()->json(['message' => 'pong']);
});

Route::prefix('/v1')->group(function () {
    // SPA should request to GET sanctum/csrf-cookie
    // and save the values in XSRF-TOKEN and include XSRF-TOKEN in response

    Route::controller(AuthController::class)->group(function () {
        Route::post('/register', 'register')->name('register');
        Route::post('/login', 'login')->name('login');
        Route::post('/logout', 'logout')->middleware('auth:sanctum');
    });

    Route::middleware(['auth:sanctum'])->group(function () {
        // Product Routes
        Route::controller(ProductController::class)
            ->missing(modelNotFound('Product'))
            ->name('product.')
            ->group(function () {
                // NOTE: GET should be accessible to GUEST users
                Route::withoutMiddleware(['auth:sanctum'])->group(function () {
                    Route::get('/products', 'index');
                    Route::get('/products/{product}', 'show')->name('show');
                });
                Route::post('/products', 'store');
                Route::patch('/products/{product}', 'update')->can('update', 'product');

                // Product Price Routes
                Route::controller(ProductPriceController::class)
                    ->name('prices.')
                    ->scopeBindings()
                    ->group(function () {
                        // NOTE: GET should be accessible to GUEST users
                        Route::withoutMiddleware(['auth:sanctum'])->group(function () {
                            Route::get('/products/{product}/prices', 'index');
                            Route::get('/products/{product}/prices/{price}', 'show')
                                ->scopeBindings()
                                ->missing(modelNotFound('productPrice'))
                                ->name('show');
                        });
                        Route::post('/products/{product}/prices', 'store');
                    });

                // Product Tags
                Route::controller(TagController::class)
                    ->name('tags.')
                    ->group(function () {
                        // NOTE: GET should be accessible to GUEST users
                        Route::withoutMiddleware(['auth:sanctum'])->group(function () {
                            Route::get('/products/{product}/tags', 'index');
                        });
                        Route::put('/products/{product}/tags', 'update')->can('update', 'product');
                    });
            });

        Route::controller(BrandController::class)
            ->missing(modelNotFound('Brand'))
            ->name('brand.')
            ->group(function () {
                Route::withoutMiddleware(['auth:sanctum'])->group(function () {
                    Route::get('/brands', 'index');
                    Route::get('/brands/{brand}', 'show')->name('show');
                });
                Route::post('/brands', 'store');
                Route::patch('/brands/{brand}', 'update')->can('update', 'brand');
            });

        Route::controller(CategoryController::class)
            ->missing(modelNotFound('Category'))
            ->name('category.')
            ->group(function () {
                Route::withoutMiddleware(['auth:sanctum'])->group(function () {
                    Route::get('/categories', 'index');
                    Route::get('/categories/{category}', 'show')->name('show');
                });
                Route::post('/categories', 'store');
                Route::patch('/categories/{category}', 'update')->can('update', 'category');
            });

        Route::controller(EstablishmentController::class)
            ->missing(modelNotFound('Establishment'))
            ->name('establishment.')
            ->group(function () {
                Route::withoutMiddleware(['auth:sanctum'])->group(function () {
                    Route::get('/establishments', 'index');
                    Route::get('/establishments/{establishment}', 'show')->name('show');
                });
                Route::post('/establishments', 'store');
                Route::patch('/establishments/{establishment}', 'update')->can('update', 'establishment');
            });

        Route::controller(StoreTypeController::class)
            ->missing(modelNotFound('StoreType'))
            ->name('storetype.')
            ->group(function () {
                Route::withoutMiddleware(['auth:sanctum'])->group(function () {
                    Route::get('/store_types', 'index');
                    Route::get('/store_types/{storeType}', 'show')->name('show');
                });
            });

        Route::controller(GroceryListController::class)
            ->missing(modelNotFound('Grocery list'))
            ->name('grocery.')
            ->group(function () {
                Route::withoutMiddleware(['auth:sanctum'])->group(function () {
                    // This route is used for accessing specific private or public grocery list
                    Route::get('/grocery-lists/{groceryList:slug}', 'show')->name('show');
                });
                // this is used to fetch the grocery lists of the authenticated user
                Route::get('/grocery-lists', 'index');
                Route::post('/grocery-lists', 'store');
                Route::patch('/grocery-lists/{groceryList:slug}', 'update')->can('update', 'groceryList');
                Route::delete('/grocery-lists/{groceryList:slug}', 'delete')->can('delete', 'groceryList');
            });

        Route::missing(modelNotFound('User'))
            ->name('user.')
            ->scopeBindings()
            ->group(function () {
                // Grocery Lists
                Route::controller(GroceryListController::class)
                    ->name('grocery.')
                    ->group(function () {
                        Route::withoutMiddleware(['auth:sanctum'])->group(function () {
                            // This route is used for visiting the user profile to see its public grocery lists
                            Route::get('/users/{user:username}/grocery-lists', 'publicIndex');
                        });
                    });
            });
    });

    Route::fallback(function () {
        return response()->json(['message' => 'are you lost?'], 404);
    });
});
