<?php

namespace App\Http\Controllers;

use App\Models\Forum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use PhpParser\Node\Stmt\TryCatch;

class ForumController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        return auth()->shouldUse('api');

    }

    public function index()
    {
        return Forum::with('user:id,username')->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       $validator = Validator::make(request()->all(), [
            'title' => 'required|min:3',
            'body' => 'required|min:5',
            'category' => 'required',
        ]);

        if ($validator->fails()) {
           return response()->json($validator->messages());
        }

        // $user = auth()->user();
        try{
            $user = auth()->userOrFail();            
        } catch (\Tymon\JWTAuth\Exceptions\UserNotDefinedException $e){
            return response()->json(['message' => 'not authenticated, you have login first']);
        }

        $user->forums()->create([
            'title' => request('title'),
            'body' => request('body'),
            'slug' => Str::slug(request('title'), '-') . '-' . time(),
            'category' => request('category')
        ]);

        //generate token, auto login
        return response()->json(['message' => 'Successfully posted']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Forum::with('user:id,username')->find($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
