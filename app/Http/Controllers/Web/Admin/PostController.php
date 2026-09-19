<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PostStoreRequest;
use App\Http\Requests\Admin\PostUpdateRequest;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Tag;
use App\Models\PostTag;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Fetch filtered posts data sorted by date updated
        $posts = Post::with('tags')->filter(request(['search']))->orderBy('updated_at', 'desc')->paginate(10)->withQueryString();

        // Return admin posts index page with data
        return view("AdminInformasi.AdminPageInformasi", [
            'posts' => $posts
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        /*
        // Fetch tags data for input form
        $tags = Tag::all();

        // Return admin create post form with data
        return view("AdminInformasi.AdminPageTambahInformasi", [
            'tags' => $tags
        ]);
        */
        if ($request->input('viewGenerated') == true) {
            if ($request->input('selected')) {
                $tags = Tag::where('name', '!=', 'S1 Informatika')
                ->where('name', 'like', '%' . $request->input('query') . '%')
                ->whereNotIn('id', $request->input('selected'))->get();
                
                $selectedTags = Tag::whereIn('id', $request->input('selected'))->get();
                return view('AdminInformasi.TagLiveSearchResult', compact('tags', 'selectedTags'));
            }

            $tags = Tag::where('name', '!=', 'S1 Informatika')
                ->where('name', 'like', '%' . $request->input('query') . '%')->get();
            return view('AdminInformasi.TagLiveSearchResult', compact('tags'));
        }

        // Fetch tags data for input form
        $tags = Tag::where('name', '!=', 'S1 Informatika')->get();

        // Return admin create post form with data
        return view("AdminInformasi.AdminPageTambahInformasi", [
            'tags' => $tags
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PostStoreRequest $request)
    {
        $validated = $request->validated();

        $tags = array_unique($validated['tags']);

        $tagDefault = Tag::where('name', "S1 Informatika")->first();

        // Store inputted post data in the database
        $post = Post::create([
            'title' => $validated['title'],
            'subtitle' => $validated['subtitle'],
            'body' => $validated['body'],
        ]);

        PostTag::create([
            'post_id' => $post->id,
            'tag_id' => $tagDefault->id
        ]);

        // Iterate inputted post's tags
        foreach($tags as $tag) {
            if (count(PostTag::where('post_id', $post->id)->where('tag_id', $tag)->get()) == 0) {
            // Store PostTag data in the database
                PostTag::create([
                    'post_id' => $post->id,
                    'tag_id' => $tag
                ]);
            }
        }

        $image = $validated['image'] ?? null;
        if ($image) {
            $post->image = Storage::disk('public')->putFile('posts', $image);
        } else {
            $post->image = null;
        }

        // Update record in database
        $post->save();

        // Check if inputted post exists in the database
        // If yes, redirect to admin posts index page with success
        // If not, return back with error
        $data = Post::where('id','=',$post->id)->get();
        if ($data) {
            $request->session()->flash('success', 'Post berhasil ditambahkan!');
            return redirect()->route('admin.posts.index');
        } else {
            return back()->withErrors([
                'message' => 'Terdapat kesalahan'
            ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        $post->load('tags');
        $tags = Tag::where('name', '!=', 'S1 Informatika')->get();

        // Return admin edit post form view with data
        return view("AdminInformasi.AdminPageEditInformasi", [
            'post' => $post,
            'tags' => $tags
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PostUpdateRequest $request, Post $post)
    {
        $validated = $request->validated();

        $tags = array_unique($validated['tags']);

        $tagDefault = Tag::where('name', "S1 Informatika")->first();

        // Update targeted post
        $post->title = $validated['title'];
        $post->subtitle = $validated['subtitle'];
        $post->body = $validated['body'];

        $image = $validated['image'] ?? null;
        if ($image) {
            if ($post->hasImage()) {
                Storage::disk('public')->delete($post->image);
            }
            $post->image = Storage::disk('public')->putFile('posts', $image);
        } else if ($request->has('deleteGambar')) {
            if ($post->hasImage()) {
                Storage::disk('public')->delete($post->image);
            }

            $post->image = null;
        }

        // Delete previous PostTag data 
        PostTag::where('post_id', $post->id)->delete();

        PostTag::create([
            'post_id' => $post->id,
            'tag_id' => $tagDefault->id
        ]);

        // Iterate updated post's tags
        foreach($tags as $tag) {
            if (count(PostTag::where('post_id', $post->id)->where('tag_id', $tag)->get()) == 0) {
                // Store updated PostTag data in the database
                PostTag::create([
                    'post_id' => $post->id,
                    'tag_id' => $tag
                ]);
            }
        }

        // Update record in database
        $post->updated_at = now();
        $post->save();

        // Check if inputted post exists in the database
        // If yes, redirect to admin posts index page with success
        // If not, return back with error
        $data = Post::where('id','=',$post->id)->get();
        if ($data) {
            $request->session()->flash('success', 'Post berhasil diupdate!');
            return redirect()->route('admin.posts.index');
        } else {
            return back()->withErrors([
                'message' => 'Terdapat kesalahan'
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        if ($post->hasImage()) {
            Storage::disk('public')->delete($post->image);
        }
        
        // Delete PostTags data with targeted post id
        PostTag::where('post_id', $post->id)->delete();

        // Delete targeted post from database
        $post->delete();

        // Redirect to admin post index page with success
        request()->session()->flash('success', 'Post berhasil dihapus!');
        return redirect()->route('admin.posts.index');
    }
}
