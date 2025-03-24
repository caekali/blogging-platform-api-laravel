<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PostController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index($userId)
    {

        $posts = Post::where('author_id', $userId)->get();
        // $posts = Post::with([
        //     'comments' => fn($comments) => $comments->chaperone(),
        // ])->whire();

        return ApiResponse::success($posts);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $userId)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'bail|required|string',
            'content' => 'required:string'
        ]);

        if ($validator->fails()) {
            return ApiResponse::validationError($validator->errors());
        }

        if (!User::where('id', $userId)->first()) {
            return ApiResponse::error('User not found', 404);
        }

        $post = Post::create(['title' => $request->title, 'content' => $request->content, 'author_id' => $userId]);
        if (!$post) {
            return ApiResponse::error('An excepted error occured while processing the request', 500);
        }
        return response()->noContent(201);
    }

    /**
     * Display the specified resource.
     */
    public function show($userId, $postId)
    {
        $post = Post::find($postId);
        if (is_null($post)) {
            return ApiResponse::error('Post not found', 404);
        }
        return ApiResponse::success(new PostResource($post));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $userId, $postId)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'bail|required|string',
            'content' => 'required:string'
        ]);

        if ($validator->fails()) {
            return ApiResponse::validationError($validator->errors());
        }

        $post = Post::find($postId);
        if (is_null($post)) {
            return ApiResponse::error('Post not found', 404);
        }

        $post->title = $request->title;
        $post->content = $request->content;

        $post->save();
        return response()->noContent(204);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($postId)
    {

        $post = Post::find($postId);
        if (is_null($post)) {
            return ApiResponse::error('Post not found', 404);
        }
        $post->delete();
        return response()->json([], 204);
    }
}
