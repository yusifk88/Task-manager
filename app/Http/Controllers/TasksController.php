<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Repositories\TasksUtilityRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TasksController extends Controller
{


    /**
     * @param CreateTaskRequest $request
     * @return JsonResponse
     */

    public function store(CreateTaskRequest $request): JsonResponse
    {


        $savedTask = new Task([
            "name" => $request->name,
            "project_id" => $request->project_id,
            "description" => $request->description,
            "priority" => TasksUtilityRepository::getPriority(),
            "dane" => true
        ]);

        $savedTask->save();

        return response()->json([
            "success" => true,
            "message" => "Task saved",
            "data" => new TaskResource($savedTask)
        ]);


    }


    /**
     * @param int $project_id
     * @return JsonResponse
     */

    public function index(int $project_id): JsonResponse
    {
        $tasks = Task::where("project_id", $project_id)->orderBy("priority")->get();

        return response()->json([
            "success" => true,
            "message" => "Tasks retrieved",
            "data" => TaskResource::collection($tasks)
        ]);

    }


    /**
     * @param int $task_id
     * @return JsonResponse
     */
    public function delete(int $task_id): JsonResponse
    {
        $task = Task::find($task_id);

        if (!$task) {

            return response()->json([
                "success" => false,
                "message" => "Task not found",
                "data" => []
            ],
                Response::HTTP_NOT_FOUND
            );

        }

        $task->delete();

        return response()->json([
            "success" => true,
            "message" => "Task delete",
            "data" => new TaskResource($task)
        ]);

    }


    /**
     * @param int $task_id
     * @param CreateTaskRequest $request
     * @return JsonResponse
     */

    public function update(int $task_id, CreateTaskRequest $request): JsonResponse
    {

        $task = Task::find($task_id);

        if (!$task) {

            return response()->json([
                "success" => false,
                "message" => "Task not found",
                "data" => []
            ],
                Response::HTTP_NOT_FOUND
            );

        }


        $task->update([
            "name" => $request->name,
            "description" => $request->description,
            "project_id" => $request->project_id
        ]);

        return response()->json([
            "success" => true,
            "message" => "Task delete",
            "data" => new TaskResource($task)
        ]);

    }


    /**
     * @param int $task_id
     * @return JsonResponse
     */

    public function done(int $task_id): JsonResponse
    {

        $task = Task::find($task_id);

        if (!$task) {

            return response()->json([
                "success" => false,
                "message" => "Task not found",
                "data" => []
            ], Response::HTTP_NOT_FOUND);

        }


        $task->done = true;
        $task->update();

        return response()->json([
            "success" => true,
            "message" => "Task marked as done",
            "data" => new TaskResource($task)
        ]);


    }

    /**
     * @param int $task_id
     * @param Request $request
     * @return JsonResponse
     */


    public function changerPriority(int $task_id, Request $request): JsonResponse
    {
        $request->validate([
            "priority" => "required|numeric"
        ]);

        $task = Task::find($task_id);
        if (!$task) {

            return \response()->json([
                "success" => false,
                "message" => "Task not found",
                "data" => []
            ], Response::HTTP_NOT_FOUND);

        }


        $task->priority = $request->priority;

        $task->update();


        return \response()->json([
            "success" => true,
            "message" => "Task priority changed",
            "data" => new TaskResource($task)
        ]);


    }


    /**
     * @param int $task_id
     * @return JsonResponse
     */

    public function undone(int $task_id): JsonResponse
    {

        $task = Task::find($task_id);

        if (!$task) {

            return response()->json([
                "success" => false,
                "message" => "Task not found",
                "data" => []
            ], Response::HTTP_NOT_FOUND);

        }


        $task->done = false;
        $task->update();

        return response()->json([
            "success" => true,
            "message" => "Task marked as undone",
            "data" => new TaskResource($task)
        ]);


    }


}
