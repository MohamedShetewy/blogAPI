<?php

namespace App\Http\Controllers;

use App\Models\Post as Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\Post as ResourcePost;
use Exception;
use Illuminate\Support\Facades\Auth;

class PostController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = Post::all();
        return $this->sendResponse(ResourcePost::collection($posts),"Posts retrieved successfully");
    }

    public function userPosts()
    {
        $id = Auth::user()->id;
        $posts = Post::where('user_id',$id)->get();
        return $this->sendResponse(ResourcePost::collection($posts),"Posts retrieved successfully");
    }

    

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $input = $request->all();

        $validate = Validator::make($input,[
            'title' => 'required',
            'description' => 'required'
            
        ]);

        if($validate->fails()){
            return $this->sendError("please validate error",$validate->errors());
        }

        $input['user_id']= Auth::user()->id;
        try{
            $post = Post::create($input);
        } catch (Exception $e) {
            // Log the error message
            //error_log('Error creating user: ' . $e->getMessage());
            return $this->sendError("please validate error",$e->getMessage());
        }

        return $this->sendResponse(new ResourcePost($post),"Post Created successfully");




    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $post = Post::find($id);
        if(is_null($post)){
            return $this->sendError("Post Not Found");
        }
        return $this->sendResponse(new ResourcePost($post),"Post retrieved successfully");
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        $input = $request->all();

        $validate = Validator::make($input,[
            'title' => 'required',
            'description' => 'required'
            
        ]);

        if($validate->fails()){
            return $this->sendError("please validate error",$validate->errors());
        }

        if($post->user_id != Auth::id()){
            return $this->sendError("You don't have rights");
        }

        $post->title = $input['title'];
        $post->description = $input['description'];
        $post->save();
        return $this->sendResponse(new ResourcePost($post),"Post updated successfully");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        if($post->user_id != Auth::id()){
            return $this->sendError("You don't have rights");
        }
        
        $post->delete();
        return $this->sendResponse(new ResourcePost($post),"Post deleted successfully");
    }
}
