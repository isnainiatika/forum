<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\AuthUserTrait;
use App\Models\ForumComment;

class ForumCommentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
     use AuthUserTrait;
     
    public function __construct()
    {
        return auth()->shouldUse('api');

    }

    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $forumId)
    {
         $this->validateRequest();
        $user = $this->getAuthUser();
        

        $user->forumComments()->create([
            'body' => request('body'),
            'forum_id' => $forumId
        ]);

        //generate token, auto login
        return response()->json(['message' => 'Successfully comment posted']);
    }
    private function validateRequest()
    {

        $validator = Validator::make(request()->all(),[
            'body' => 'required|min:5',
        ]);

        if ($validator->fails()) {
           response()->json($validator->messages())->send();
           exit;
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $forumId, $commentId)
    {
         $this->validateRequest();
        $forumComment = ForumComment::find($commentId);        

       $this->checkOwnership($forumComment->user_id);

        $forumComment->update([
            'body' => request('body'),
        ]);

        //generate token, auto login
        return response()->json(['message' => 'Successfully comment updated']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($forumId, $commentId)
    {
         $forumComment = ForumComment::find($commentId);  
        $this->checkOwnership($forumComment->user_id);

        $forumComment->delete();

        //generate token, auto login
        return response()->json(['message' => 'Successfully comment delete']);
    }
}
