<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Establishment\PaginationRequest;
use App\Http\Requests\Establishment\StoreEstablishmentRequest;
use App\Http\Requests\Establishment\UpdateEstablishmentRequest;
use App\Http\Resources\EstablishmentResource;
use App\Models\Establishment;
use App\Services\EstablishmentService;

class EstablishmentController extends Controller
{
    public function __construct(
        private EstablishmentService $establishmentService,
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(PaginationRequest $request)
    {
        return EstablishmentResource::collection(
            $this->establishmentService->all($request->validated()),
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEstablishmentRequest $request)
    {
        $newEstablishment = $this->establishmentService->create(
            $request->validated(),
        );

        $newResourceLink = route('establishment.show', [
            'establishment' => $newEstablishment->id,
        ]);

        return $newEstablishment
            ->toResource()
            ->additional(['links' => ['related' => $newResourceLink]])
            ->response()
            ->header('Location', $newResourceLink)
            ->setStatusCode($newEstablishment->wasRecentlyCreated ? 201 : 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Establishment $establishment)
    {
        return $this->establishmentService->show($establishment)->toResource();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        UpdateEstablishmentRequest $request,
        Establishment $establishment,
    ) {
        return $this->establishmentService
            ->update($request->validated(), $establishment)
            ->toResource();
    }

    /**
     * Remove the specified resource from storage.
     */
    //     public function destroy(Establishment $establishment)
    //     {
    //         //
    //     }
}
