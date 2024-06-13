<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PostTemplate;
use App\Models\PostTemplateSection;
use App\Services\CreatePostTemplateSection;
use App\Services\DestroyPostTemplateSection;
use App\Services\UpdatePostTemplateSection;
use App\Services\UpdatePostTemplateSectionPosition;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group Post template sections
 */
class PostTemplateSectionController extends Controller
{
    /**
     * Create a post template section
     *
     * @urlParam template required The id of the post template. Example: 1
     *
     * @bodyParam label string required The name of the section. Max 255 characters. Example: Daily meditation
     * @bodyParam position integer required The position in the list of the all the sections, starting at 1. If you need to set the position automatically, indicate null in this field. In this case, it will take the last position. Max 1000. Example: 1
     *
     * @response 201 {
     *  "id": 4,
     *  "object": "post template section",
     *  "label": "Daily meditation",
     *  "position": 1
     * }
     */
    public function create(Request $request, int $postTemplateId): JsonResponse
    {
        try {
            $postTemplate = PostTemplate::where('user_id', auth()->user()->id)
                ->findOrFail($postTemplateId);
        } catch (ModelNotFoundException) {
            abort(401);
        }

        $validated = $request->validate([
            'label' => 'required|string|max:255',
            'position' => 'nullable|integer|digits_between:0,1000',
        ]);

        $postTemplateSection = (new CreatePostTemplateSection(
            postTemplate: $postTemplate,
            label: $validated['label'],
            labelTranslationKey: null,
            position: $validated['position'],
            canBeDeleted: true,
        ))->execute();

        return response()->json([
            'id' => $postTemplateSection->id,
            'object' => 'post template section',
            'label' => $postTemplateSection->label,
            'position' => $postTemplateSection->position,
        ], 201);
    }

    /**
     * Update a post template section
     *
     * @urlParam template required The id of the post template. Example: 1
     * @urlParam section required The id of the post template section. Example: 1
     *
     * @bodyParam label string required The name of the section. Max 255 characters. Example: Daily meditation
     * @bodyParam position integer required The position in the list of the all the sections, starting at 1. If you need to set the position automatically, indicate null in this field. In this case, it will take the last position. Max 1000. Example: 1
     *
     * @response 200 {
     *  "id": 4,
     *  "object": "post template section",
     *  "label": "Daily meditation",
     *  "position": 1
     * }
     */
    public function update(Request $request, int $postTemplateId, int $sectionId): JsonResponse
    {
        try {
            $postTemplate = PostTemplate::where('user_id', auth()->user()->id)
                ->findOrFail($postTemplateId);
        } catch (ModelNotFoundException) {
            abort(401);
        }

        try {
            $postTemplateSection = PostTemplateSection::where('post_template_id', $postTemplate->id)
                ->findOrFail($sectionId);
        } catch (ModelNotFoundException) {
            abort(401);
        }

        $validated = $request->validate([
            'label' => 'required|string|max:255',
            'position' => 'nullable|integer|digits_between:0,1000',
        ]);

        $postTemplateSection = (new UpdatePostTemplateSection(
            postTemplateSection: $postTemplateSection,
            label: $validated['label'],
        ))->execute();

        if ($validated['position']) {
            (new UpdatePostTemplateSectionPosition(
                postTemplateSection: $postTemplateSection,
                newPosition: $validated['position'],
            ))->execute();
        }

        return response()->json([
            'id' => $postTemplateSection->id,
            'object' => 'post template section',
            'label' => $postTemplateSection->label,
            'position' => $postTemplateSection->position,
        ], 200);
    }

    /**
     * Delete a post template section
     *
     * @urlParam template required The id of the post template. Example: 1
     * @urlParam section required The id of the post template section. Example: 1
     *
     * @response 200 {
     *  "status": "success",
     * }
     */
    public function destroy(Request $request, int $postTemplateId, int $sectionId): JsonResponse
    {
        try {
            $postTemplate = PostTemplate::where('user_id', auth()->user()->id)
                ->findOrFail($postTemplateId);
        } catch (ModelNotFoundException) {
            abort(401);
        }

        try {
            $postTemplateSection = PostTemplateSection::where('post_template_id', $postTemplate->id)
                ->findOrFail($sectionId);
        } catch (ModelNotFoundException) {
            abort(401);
        }

        (new DestroyPostTemplateSection(
            postTemplateSection: $postTemplateSection,
        ))->execute();

        return response()->json([
            'status' => 'success',
        ], 200);
    }

    /**
     * List all post template sections
     *
     * This will list all the sections, sorted
     * alphabetically.
     *
     * @response 200 [{
     *  "id": 4,
     *  "object": "post template section",
     *  "name": "New post template section",
     *  "description": "This is a new post template section",
     * }, {
     *  "id": 5,
     *  "object": "post template section",
     *  "name": "Old post template section",
     *  "description": "This is an old post template section",
     * }]
     */
    public function index(Request $request, int $postTemplateId): JsonResponse
    {
        try {
            $postTemplate = PostTemplate::where('user_id', auth()->user()->id)
                ->findOrFail($postTemplateId);
        } catch (ModelNotFoundException) {
            abort(401);
        }

        $sections = $postTemplate->postTemplateSections()
            ->orderBy('position')
            ->get()
            ->map(fn (PostTemplateSection $section) => [
                'id' => $section->id,
                'object' => 'post template section',
                'label' => $section->label,
                'position' => $section->position,
            ]);

        return response()->json($sections, 200);
    }
}
