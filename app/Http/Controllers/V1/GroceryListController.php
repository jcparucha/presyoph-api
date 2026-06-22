<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\GroceryList\IndexGroceryListRequest;
use App\Http\Requests\GroceryList\StoreGroceryListRequest;
use App\Http\Requests\GroceryList\UpdateGroceryListRequest;
use App\Http\Resources\GroceryListResource;
use App\Models\GroceryList;
use App\Models\User;
use App\Services\GroceryListService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;

class GroceryListController extends Controller
{
    public function __construct(private GroceryListService $groceryListService) {}

    /**
     * Display a listing of the resource.
     */
    public function index(IndexGroceryListRequest $request): JsonResource
    {
        return GroceryListResource::collection(
            $this->groceryListService->all($request->published ?? null, $request->user()),
        );
    }

    /**
     * Display a listing of a publicly available resource
     */
    public function publicIndex(User $user): JsonResource
    {
        return GroceryListResource::collection($this->groceryListService->all(true, $user));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreGroceryListRequest $request, User $user): JsonResponse
    {
        $newGroceryList = $this->groceryListService->create($request->validated(), $user);

        $newResourceLink = route('user.grocery.show', [
            'user' => $user->id,
            'groceryList' => $newGroceryList->slug,
        ]);

        return $newGroceryList
            ->toResource()
            ->additional(['links' => ['related' => $newResourceLink]])
            ->response()
            ->header('Location', $newResourceLink)
            ->setStatusCode($newGroceryList->wasRecentlyCreated ? 201 : 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(GroceryList $groceryList)
    {
        return $this->groceryListService->get($groceryList)->toResource();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateGroceryListRequest $request, User $user, GroceryList $groceryList): JsonResource
    {
        return $this->groceryListService->update($request->validated(), $groceryList)->toResource();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
