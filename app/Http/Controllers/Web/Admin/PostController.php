<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PostStoreRequest;
use App\Http\Requests\Admin\PostUpdateRequest;
use App\Support\PostBodySanitizer;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Tag;

class PostController extends Controller
{
    public function __construct(private readonly PostBodySanitizer $bodies)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Fetch filtered posts data sorted by date updated
        $posts = Post::with('tags')->filter(request(['search']))->orderBy('updated_at', 'desc')->paginate(10)->withQueryString();

        // Return admin posts index page with data
        return view('admin.posts.index', [
            'posts' => $posts
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        if ($request->input('viewGenerated') == true) {
            if ($request->input('selected')) {
                $tags = Tag::where('name', '!=', 'S1 Informatika')
                ->where('name', 'like', '%' . $request->input('query') . '%')
                ->whereNotIn('id', $request->input('selected'))->get();
                
                $selectedTags = Tag::whereIn('id', $request->input('selected'))->get();
                return view('admin.posts.tag-live-search-result', compact('tags', 'selectedTags'));
            }

            $tags = Tag::where('name', '!=', 'S1 Informatika')
                ->where('name', 'like', '%' . $request->input('query') . '%')->get();
            return view('admin.posts.tag-live-search-result', compact('tags'));
        }

        // Fetch tags data for input form
        $tags = Tag::where('name', '!=', 'S1 Informatika')->get();

        // Return admin create post form with data
        return view('admin.posts.create', [
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

        // Store inputted post data in the database
        $post = Post::create([
            'title' => $validated['title'],
            'subtitle' => $validated['subtitle'],
            'body' => $this->bodies->sanitize($validated['body']) ?? '',
        ]);

        $post->syncTags($tags);
        $post->replaceImage($validated['image'] ?? null);

        // Update record in database
        $post->save();

        // Check if inputted post exists in the database
        // If yes, redirect to admin posts index page with success
        // If not, return back with error
        $data = Post::where('id','=',$post->id)->get();
        if ($data) {
            $request->session()->flash('success', 'Informasi berhasil ditambahkan!');
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
        return view('admin.posts.edit', [
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

        // Update targeted post
        $post->title = $validated['title'];
        $post->subtitle = $validated['subtitle'];
        $post->body = $this->bodies->sanitize($validated['body']) ?? '';

        $post->replaceImage($validated['image'] ?? null, $request->has('deleteGambar'));
        $post->syncTags($tags);

        // Update record in database
        $post->updated_at = now();
        $post->save();

        // Check if inputted post exists in the database
        // If yes, redirect to admin posts index page with success
        // If not, return back with error
        $data = Post::where('id','=',$post->id)->get();
        if ($data) {
            $request->session()->flash('success', 'Informasi berhasil diubah!');
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
        // Delete targeted post from database
        $post->delete();

        // Redirect to admin post index page with success
        request()->session()->flash('success', 'Informasi berhasil dihapus!');
        return redirect()->route('admin.posts.index');
    }
}
