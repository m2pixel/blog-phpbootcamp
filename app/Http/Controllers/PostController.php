<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Post;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts = Post::all();

        return view('posts.index')->with('posts',$posts);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::all();

        return view('posts.create',[
            'categories'=>$categories
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'=>'required|max:255',
            'content'=>'required',
            'image'=>'required|image',
        ]);

        $filename = $request->title."_".time().'.'.$request->image->extension();

        $data['title'] = $request->title;
        $data['content'] = $request->content;
        $data['image'] = $filename;
        $category = Category::find($request->categories);
        

        if($post = Post::create($data)){
            //$post->categories()->attach($category->id);
            
            $category->posts()->attach($post->id);
            // $post->attach($request->categories)

            $request->image->move(public_path('images'), $filename);

            return redirect()->route('posts.index')->with('success', 'Post created successfuly.');
        }else{
            return redirect()->back()->with('error', 'Something went wrong.');
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
        $post = Post::find($id);

        return view('posts.show')->with('post',$post);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $post = Post::find($id);
        $categories = Category::all();

        return view('posts.edit')->with(['post' => $post, 'categories' => $categories]);
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
        $request->validate([    
            'title'=>'required|max:255',
            'content'=>'required',
            'image'=>'required|image',
        ]);

        $filename = $request->title."_".time().'.'.$request->image->extension();
        
        $post = Post::find($id);

        $data['title'] = $request->title;
        $data['content'] = $request->content;
        $data['image'] = $filename;
        $category = Category::find($request->categories);
        

        if($post->update($data)){
            //$post->categories()->attach($category->id);
            
            $category->posts()->attach($post->id);
            // $post->attach($request->categories)

            $request->image->move(public_path('images'), $filename);

            return redirect()->route('posts.index')->with('success', 'Post created successfuly.');
        }else{
            return redirect()->back()->with('error', 'Something went wrong.');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $post = Post::find($id);

        foreach($post->categories as $category){
            $post->categories()->detach($category->id); 
        }
        
        if($post->delete()){

            return redirect()->route('posts.index')->with('success', 'Post deleted successfuly.');
        }
        else
            return redirect()->back()->with('error', 'Something went wrong.');
    }
}