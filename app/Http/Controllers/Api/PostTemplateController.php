<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Journal;
use App\Models\PostTemplate;
use App\Services\CreateJournal;
use App\Services\CreatePostTemplate;
use App\Services\DestroyJournal;
use App\Services\DestroyPostTemplate;
use App\Services\UpdateJournal;
use App\Services\UpdatePostTemplate;
use App\Services\UpdatePostTemplatePosition;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group Post templates
 */
class PostTemplateController extends Controller
{
    /**
     * Create a post template
     *
     * @bodyParam label string required The name of the post template. Max 255 characters. Example: Daily meditation
     * @bodyParam position integer required The position in the list of the all the post templates, starting at 1. If you need to set the position automatically, indicate null in this field. In this case, it will take the last position. Max 1000. Example: 1
     *
     * @response 201 {
     *  "id": 4,
     *  "object": "post template",
     *  "label": "Daily meditation",
     *  "position": 1
     * }
     */
    public function create(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'label' => 'required|string|max:255',
            'position' => 'nullable|integer|digits_between:0,1000',
        ]);

        $postTemplate = (new CreatePostTemplate(
            label: $validated['label'],
            labelTranslationKey: null,
            position: $validated['position'],
            canBeDeleted: true,
        ))->execute();

        return response()->json([
            'id' => $postTemplate->id,
            'object' => 'post template',
            'label' => $postTemplate->label,
            'position' => $postTemplate->position,
        ], 201);
    }

    /**
     * Update a post template
     *
     * @urlParam template required The id of the post template. Example: 1
     *
     * @bodyParam label string required The name of the post template. Max 255 characters. Example: Daily meditation
     * @bodyParam position integer required The position in the list of the all the post templates, starting at 1. If you need to set the position automatically, indicate null in this field. In this case, it will take the last position. Max 1000. Example: 1
     *
     * @response 200 {
     *  "id": 4,
     *  "object": "post template",
     *  "label": "Daily meditation",
     *  "position": 1
     * }
     */
    public function update(Request $request, int $journalId): JsonResponse
    {
        try {
            $postTemplate = PostTemplate::where('user_id', auth()->user()->id)
                ->findOrFail($journalId);
        } catch (ModelNotFoundException) {
            abort(401);
        }

        $validated = $request->validate([
            'label' => 'required|string|max:255',
            'position' => 'nullable|integer|digits_between:0,1000',
        ]);

        $postTemplate = (new UpdatePostTemplate(
            postTemplate: $postTemplate,
            label: $validated['label'],
        ))->execute();

        if ($validated['position']) {
            (new UpdatePostTemplatePosition(
                postTemplate: $postTemplate,
                newPosition: $validated['position'],
            ))->execute();
        }

        return response()->json([
            'id' => $postTemplate->id,
            'object' => 'post template',
            'label' => $postTemplate->label,
            'position' => $postTemplate->position,
        ], 200);
    }

    /**
     * Delete a post template
     *
     * @urlParam template required The id of the post template. Example: 1
     *
     * @response 200 {
     *  "status": "success",
     * }
     */
    public function destroy(Request $request, int $journalId): JsonResponse
    {
        try {
            $postTemplate = PostTemplate::where('user_id', auth()->user()->id)
                ->findOrFail($journalId);
        } catch (ModelNotFoundException) {
            abort(401);
        }

        (new DestroyPostTemplate(
            postTemplate: $postTemplate,
        ))->execute();

        return response()->json([
            'status' => 'success',
        ], 200);
    }

    /**
     * List all post templates
     *
     * This will list all the post templates, sorted
     * alphabetically.
     *
     * @response 200 [{
     *  "id": 4,
     *  "object": "post template",
     *  "name": "New post template",
     *  "description": "This is a new post template",
     * }, {
     *  "id": 5,
     *  "object": "post template",
     *  "name": "Old post template",
     *  "description": "This is an old post template",
     * }]
     */
    public function index(): JsonResponse
    {
        $postTemplates = auth()->user()->postTemplates()
            ->orderBy('position')
            ->get()
            ->map(fn (PostTemplate $postTemplate) => [
                'id' => $postTemplate->id,
                'object' => 'post template',
                'label' => $postTemplate->label,
                'position' => $postTemplate->position,
            ]);

        return response()->json($postTemplates, 200);
    }
}
