<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\NoteResource;
use App\Models\Image;
use App\Models\Note;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class NoteController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $user_id = auth()->user()->id;

            if (!$user_id) {
                return response()->error('Unauthorized', 401);
            }

            $notes = Note::where('user_id', $user_id)
                ->with('images')
                ->get();

            $format = NoteResource::collection($notes);

            return response()->success($format);
        } catch (\Exception $e) {
            return response()->error($e->getMessage(), 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $user_id = auth()->user()->id;
            if (!$user_id) {
                return response()->error('Unauthorized', 401);
            }

            $note = new Note();
            $note->title = $request->title;
            $note->content = $request->content;
            $note->pinged = false;
            $note->user_id = $user_id;
            $note->save();

            return response()->success(new NoteResource($note));
        } catch (\Exception $e) {
            return response()->error($e->getMessage(), 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $user_id = auth()->user()->id;
            if (!$user_id) {
                return response()->error('Unauthorized', 401);
            }
            $note = Note::where('user_id', $user_id)
                ->where('id', $id)
                ->with('images')
                ->first();

            if (!$note) {
                return response()->error('Not found', 404);
            }
            return response()->success(new NoteResource($note));
        } catch (\Exception $e) {
            return response()->error($e->getMessage(), 500);
        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $user_id = auth()->user()->id;
            if (!$user_id) {
                return response()->error('Unauthorized', 401);
            }
            $note = Note::where('user_id', $user_id)
                ->where('id', $id)
                ->first();

            if (!$note) {
                return response()->error('Not found', 404);
            }
            $note->title = $request->title;
            $note->content = $request->content;
            $note->save();
            return response()->success(new NoteResource($note));
        } catch (\Exception $e) {
            return response()->error($e->getMessage(), 500);
        }
    }

    public function delete(int $id): JsonResponse
    {
        try {
            $user_id = auth()->user()->id;
            if (!$user_id) {
                return response()->error('Unauthorized', 401);
            }

            $note = Note::where('user_id', $user_id)
                ->where('id', $id)
                ->first();

            if (!$note) {
                return response()->error('Not found', 404);
            }

            $images = Image::where('imageable_id', $note->id)
                ->where('imageable_type', Note::class)
                ->get();

            if ($images) {
                foreach ($images as $image) {
                    Storage::delete('public/' . $image->path);
                    $image->delete();
                }
            }

            $note->delete();
            return response()->success('Deleted');
        } catch (\Exception $e) {
            return response()->error($e->getMessage(), 500);
        }
    }

    public function pinged(int $id): JsonResponse
    {
        try {
            $user_id = auth()->user()->id;

            if (!$user_id) {
                return response()->error('Unauthorized', 401);
            }

            $note = Note::where('user_id', $user_id)
            ->where('id', $id)
            ->first();

            if (!$note) {
                return response()->error('Not found', 404);
            }
            $note->pinged = !$note->pinged;
            $note->save();
            return response()->success(new NoteResource($note));
        } catch (\Exception $e) {
            dd($e);
            return response()->error($e->getMessage(), $e->getCode());
        }
    }
}
