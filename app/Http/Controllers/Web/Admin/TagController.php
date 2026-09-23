<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TagRequest;
use App\Models\Tag;
use App\Models\PostTag;
use App\Models\Post;

class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Fetch tags data sorted by name
        $tags = Tag::filter(request(['search']))->orderBy('name', 'asc')->paginate(10)->withQueryString();

        // Return admin tag index page with data
        return view('admin.tags.index', [
            'tags' => $tags
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Return admin create tag form page
        return view('admin.tags.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TagRequest $request)
    {
        $validated = $request->validated();

        // Check if inputted tag name is unique
        // If not, return back with error
        if (count(Tag::where('name', $validated['name'])->get()) != 0){
            return back()->withError('Topik sudah ada');
        }

        // Store inputted tag data in the database
        $tag = Tag::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null
        ]);

        // Check if inputted tag exists in the database
        // If yes, redirect to admin tag index page with success
        // If not, return back with error
        $data = Tag::where('id','=',$tag->id)->get();
        if ($data) {
            $request->session()->flash('success', 'Topik berhasil ditambahkan!');
            return redirect()->route('admin.tags.index');
        } else {
            return back()->withErrors([
                'message' => 'Terdapat kesalahan'
            ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tag $tag)
    {
        // Return admin edit tag form page with data
        return view('admin.tags.edit', [
           'tag' => $tag 
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TagRequest $request, Tag $tag)
    {
        $validated = $request->validated();

        if ($tag->name == "S1 Informatika" && $validated['name'] != "S1 Informatika") {
            return back()->withError('Nama topik default tidak dapat diubah');
        }

        // Check if updated name is unique
        // If not, return back with error
        if ($tag->name != $validated['name'] && count(Tag::where('name', $validated['name'])->get()) != 0){
            return back()->withError('Topik sudah ada');
        }
        
        // Update tag data
        $tag->name = $validated['name'];
        $tag->description = $validated['description'] ?? null;

        // Update tag data in the database
        $tag->save();

        // Check if inputted tag exists in the database
        // If yes, redirect to admin tag index page with success
        // If not, return back with error
        $data = Tag::where('id','=',$tag->id)->get();
        if ($data) {
            $request->session()->flash('success', 'Topik berhasil diperbarui!');
            return redirect()->route('admin.tags.index');
        } else {
            return back()->withErrors([
                'message' => 'Terdapat kesalahan'
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tag $tag)
    {
        if ($tag->name == "S1 Informatika") {
            return back()->withError('Topik default tidak dapat dihapus');
        }
        
        // Delete PostTags with target tag id
        PostTag::where('tag_id', $tag->id)->delete();

        // Delete targeted tag from database
        $tag->delete();

        // Redirect to admin tag index page with success
        request()->session()->flash('success', 'Topik berhasil dihapus!');
        return redirect()->route('admin.tags.index');
    }
}
