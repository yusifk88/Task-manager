<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class ProjectsController extends Controller
{


    /**
     * @param CreateProjectRequest $request
     * @return JsonResponse
     */

    public function store(CreateProjectRequest $request): JsonResponse
    {

        $savedProject = new Project([
            "name" => $request->name,
            "description" => $request->description,
            "uuid" => Str::uuid()
        ]);

        $savedProject->save();
        return response()->json([
            "success" => true,
            "message" => "Project saved",
            "data" => new ProjectResource($savedProject)]);
    }


    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {

        $projects = Project::with("tasks")->orderBy("name")->get();

        return response()->json([
            "success" => true,
            "message" => "Request processed successfully",
            "data" => ProjectResource::collection($projects)]);

    }

    /**
     * @param int $project_id
     * @param Request $request
     * @return JsonResponse|ValidationException
     */


    public function update(int $project_id, Request $request): JsonResponse|ValidationException
    {
        $request->validate([
            "name" => "required"
        ]);


        $project = Project::find($project_id);

        /**
         * if project cannot be found
         */

        if (!$project) {

            return response()->json([
                "success" => false,
                "message" => "Project not found",
                "data" => []
            ], Response::HTTP_NOT_FOUND);

        }


        if (Project::where("id", '<>', $project_id)->where("name", $request->name)->exists()) {

            return ValidationException::withMessages([
                "error" => ['Project name already exists']
            ]);

        }


        $project->update([
            "name" => $request->name,
            "description" => $request->description
        ]);

        return \response()->json([
            "success" => true,
            "message" => "Project updated",
            "data" => new ProjectResource($project)
        ]);


    }


    /**
     * @param int $project_id
     * @return JsonResponse|ValidationException
     */

    public function delete(int $project_id): JsonResponse|ValidationException
    {

        $project = Project::with("tasks")->find($project_id);

        if (!$project) {

            return \response()->json([
                "success" => false,
                "message" => "Project not found",
                "data" => []
            ], Response::HTTP_NOT_FOUND);


        }


        if (Task::where("project_id", $project_id)->exists()) {

            return ValidationException::withMessages([
                "error" => ["This project has some tasks, remove all tasks before you delete this project"]
            ]);

        }


        $project->delete();


        return \response()->json(new ProjectResource($project));

    }


}
