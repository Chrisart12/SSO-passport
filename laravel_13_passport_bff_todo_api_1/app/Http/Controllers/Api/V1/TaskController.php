<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreTaskRequest;
use App\Http\Requests\Api\V1\UpdateTaskRequest;
use App\Http\Resources\Api\V1\TaskResource;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Task::class);

        $perPage = config('custom.pagination.per_page');

        return TaskResource::collection(
            Task::with('user')
                ->where('user_id', $request->user()->id)
                ->orderBy('created_at', 'desc')
                ->paginate($perPage)
        );
    }

    public function store(StoreTaskRequest $request)
    {
        $this->authorize('create', Task::class);

        $task = $request->user()->tasks()->create($request->validated());

        return new TaskResource($task->load('user'));
    }

    public function show(Request $request, Task $task)
    {
        $task = $request->user()->tasks()->with('user')->findOrFail($task->id);
        $this->authorize('view', $task);

        return new TaskResource($task);
    }

    public function update(UpdateTaskRequest $request, Task $task)
    {
        $task = $request->user()->tasks()->findOrFail($task->id);
        $this->authorize('update', $task);

        $task->update($request->validated());

        return new TaskResource($task->load('user'));
    }

    public function destroy(Request $request, Task $task)
    {
        $task = $request->user()->tasks()->findOrFail($task->id);
        $this->authorize('delete', $task);

        $task->delete();

        return response()->json(null, 204);
    }
}
