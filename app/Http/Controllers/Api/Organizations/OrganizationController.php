<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Organizations;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrganizationResource;
use App\Actions\CreateOrganization;
use App\Traits\ApiResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class OrganizationController extends Controller
{
    use ApiResponses;

    public function index(): AnonymousResourceCollection
    {
        $organizations = Auth::user()->organizations;

        return OrganizationResource::collection($organizations);
    }

    public function create(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $organization = new CreateOrganization(
            user: Auth::user(),
            organizationName: $validated['name'],
        )->execute();

        return new OrganizationResource($organization)
            ->response()
            ->setStatusCode(201);
    }

    public function show(Request $request): JsonResponse
    {
        $organization = $request->attributes->get('organization');

        return new OrganizationResource($organization)
            ->response()
            ->setStatusCode(200);
    }
}
