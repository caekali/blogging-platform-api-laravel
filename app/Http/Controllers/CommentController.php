<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use App\Helpers\ApiResponse;
use Illuminate\Support\Facades\Validator;

class CommentController extends BaseController
{
    //
    public function getAllByPost($postId)
    {
        $post = Post::find($postId);
        if (is_null($post)) {
            return ApiResponse::error('Post not found', 404);
        }
        return $this->sendResponse($post->comments);
    }

    public function makeComment(Request $request, $postId)
    {
        $post = Post::find($postId);
        if (is_null($post)) {
            return ApiResponse::error('Post not found', 404);
        }
        $comment =  Comment::create(['user_id' => auth()->user()->id, 'content' => $request->content, 'post_id' =>  $post->id]);
        return response()->noContent(201);
    }

    public function destroy($commentId)
    {
        $comment = Comment::find($commentId);

        if (is_null($comment)) {
            return ApiResponse::error('Comment not found', 404);
        }
        $comment->delete();
        return response()->noContent();
    }

    public function update(Request $request, $postId, $commentId)
    {
        $validator = Validator::make($request->all(), [
            'content' => 'required:string'
        ]);

        if ($validator->fails()) {
            return ApiResponse::validationError($validator->errors());
        }

        $post = Post::find($postId);

        if (is_null($post)) {
            return ApiResponse::error('Post not found', 404);
        }

        $comment = Comment::find($commentId);
        if (is_null($comment)) {
            return ApiResponse::error('Comment not found', 404);
        }

        $comment->content = $request->content;
        $comment->save();

        return response()->noContent(204);
    }
}
