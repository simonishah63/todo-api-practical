<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\TaskRequest;
use App\Http\Resources\TaskResource;
use App\Repositories\TaskRepository;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TaskController extends Controller
{
    /**
     * Response trait to handle return responses.
     */
    use ResponseTrait;

    /**
     * Task Repository class.
     *
     * @var taskRepository
     */
    public $taskRepository;

    public function __construct(TaskRepository $taskRepository)
    {
        $this->middleware('auth:sanctum');
        $this->taskRepository = $taskRepository;
    }

    /**
     * @OA\Schema(
     *      schema="tasksList",
     *      allOf={
     *          @OA\Schema(ref="#/components/schemas/Task"),
     *          @OA\Schema(
     *
     *              @OA\Property(property="notes", type="array", @OA\Items(ref="#/components/schemas/Note")),
     *          ),
     *      }
     * )
     *
     * @OA\GET(
     *      path="/api/v1/task",
     *      tags={"Task"},
     *      summary="Fetch task with notes",
     *      description="Provide list of task along with notes",
     *      operationId="task",
     *      security={{"sanctum":{}}},
     *
     *      @OA\Parameter(name="status", description="Task status", example="New", in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="priority", description="Task priority", example="High", in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="due_date", description="Due Date of Task", example="2023-05-29", in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="notes", description="Retrieve tasks which have minimum one note attached", example="true", in="query", @OA\Schema(type="boolean")),
     *
     *      @OA\Response(response=200, description="Task fetched successfully!'",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/tasksList")),
     *          )
     *      ),
     *
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=401, description="Returns when user is not authenticated",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="message", type="string", example="Unauthenticated."),
     *          )
     *      ),
     *
     *      @OA\Response(response=404, description="Resource Not Found"),
     *      @OA\Response(response=500, description="Internal Server Error"),
     * )
     */
    public function index(Request $request)
    {
        try {
            $data = $this->taskRepository->search($request->all());

            return $this->responseSuccess(TaskResource::collection($data), 'Tasks Fetched Successfully!');
        } catch (\Exception $e) {
            return $this->responseError('Something went wrong.', $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Schema(
     *      schema="taskAdd",
     *      allOf={
     *          @OA\Schema(ref="#/components/schemas/Task"),
     *          @OA\Schema(
     *
     *              @OA\Property(property="notes", type="array", @OA\Items(
     *                   type="object",
     *                  @OA\Property(property="subject", type="string", maxLength=255, description="Note Subject", example="Note 1"),
     *                  @OA\Property(property="note", type="text", maxLength=5000, description="Note Description", example="Description of the note"),
     *              )),
     *          ),
     *      }
     * )
     *
     * @OA\POST(
     *     path="/api/v1/task",
     *     tags={"Task"},
     *     summary="Create New Task",
     *     description="Create New Task",
     *     operationId="store",
     *     security={{"sanctum":{}}},
     *
     *     @OA\RequestBody(
     *          required=true,
     *
     *          @OA\JsonContent(ref="#/components/schemas/taskAdd"),
     *      ),
     *
     *      @OA\Response(response=200, description="New Task Created Successfully!'",
     *
     *           @OA\JsonContent(
     *
     *              @OA\Property(property="data", type="object", ref="#/components/schemas/tasksList")
     *          )
     *      ),
     *
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=401, description="Returns when user is not authenticated",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="message", type="string", example="Unauthenticated."),
     *          )
     *      ),
     *
     *      @OA\Response(response=404, description="Resource Not Found"),
     *      @OA\Response(response=422, description="The given data was invalid."),
     *      @OA\Response(response=500, description="Internal Server Error"),
     * )
     */
    public function store(TaskRequest $request)
    {
        try {
            $task = $this->taskRepository->create($request->all());

            return $this->responseSuccess(new TaskResource($task), 'New Task Created Successfully!', Response::HTTP_CREATED);
        } catch (\Exception $exception) {
            return $this->responseError('Something went wrong.', $exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
