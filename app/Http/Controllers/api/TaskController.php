<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use Illuminate\Http\JsonResponse;

class TaskController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $user_id = auth()->user()->id;
            if (!$user_id) {
                return response()->error('User not found', 404);
            }
            $tasks = Task::where('user_id', $user_id)->get();

            return response()->success(TaskResource::collection($tasks));
        } catch (\Exception $e) {
            return response()->error($e->getMessage(), 500);
        }
    }

    public function store(TaskRequest $request): JsonResponse
    {
        try {
            $request->validated();
            $user_id = auth()->user()->id;
            if (!$user_id) {
                return response()->error('User not found', 404);
            }
            $task = Task::create([
                'content' => $request->content,
                'complete' => false,
                'user_id' => $user_id,
            ]);
            return response()->success(new TaskResource($task));
        } catch (\Exception $e) {
            return response()->error($e->getMessage(), 500);
        }
    }

    public function delete(int $id): JsonResponse
    {
        try {
            $user_id = auth()->user()->id;
            if (!$user_id) {
                return response()->error('User not found', 404);
            }
            $task = Task::where('user_id', $user_id)->find($id);

            if (!$task) {
                return response()->error('Task not found', 404);
            }
            $task->delete();
            return response()->success('Task deleted');
        } catch (\Exception $e) {
            return response()->error($e->getMessage(), 500);
        }
    }

    public function completed(int $id): JsonResponse
    {
        try {
            $user_id = auth()->user()->id;
            if (!$user_id) {
                return response()->error('User not found', 404);
            }
            $task = Task::where('user_id', $user_id)->find($id);

            if (!$task) {
                return response()->error('Task not found', 404);
            }
            $task->complete = !$task->complete;
            $task->save();
            return response()->success(new TaskResource($task));
        } catch (\Exception $e) {
            return response()->error($e->getMessage(), 500);
        }
    }
}
