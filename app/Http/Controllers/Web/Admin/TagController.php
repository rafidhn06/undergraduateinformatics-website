<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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
        // Fetch tags data sorted by date created
        // $tags =  Tag::orderBy('created_at', 'desc')->get();

        // Return admin tag index page with data
        return view("AdminTag.AdminPageTag", [
            'tags' => $tags
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Return admin create tag form page
        return view("AdminTag.AdminPageTambahTag");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Check if inputs are valid
        $request->validate([
            'name' => 'required',
            'description' => 'nullable'
        ]);

        // Check if inputted tag name is unique
        // If not, return back with error
        if (count(Tag::where('name', $request->name)->get()) != 0){
            return back()->withError('Tag sudah ada');
        }

        // Store inputted tag data in the database
        $tag = Tag::create([
            'name' => $request->name,
            'description' => $request->description
        ]);

        // Placeholder for future tag's image feature
        // if ($request->image) {
        //     $imageName = time().'_'.$request->image->getClientOriginalName();  
        //     $request->image->move(public_path('images/posts'), $imageName);
        //     $post->image = "images/posts/".$imageName;
        // } else {
        //     $post->image = "images/placeholder.png";
        // }

        // $post->save();

        // Check if inputted tag exists in the database
        // If yes, redirect to admin tag index page with success
        // If not, return back with error
        $data = Tag::where('id','=',$tag->id)->get();
        if ($data) {
            $request->session()->flash('success', 'Tag berhasil ditambahkan!');
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
        return view("AdminTag.AdminPageEditTag", [
           'tag' => $tag 
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tag $tag)
    {
        // Check if updated inputs are valid
        $request->validate([
            'name' => 'required',
            'description' => 'nullable'
        ]);

        if ($tag->name == "S1 Informatika" && $request->name != "S1 Informatika") {
            return back()->withError('Nama tag default tidak dapat diubah');
        }

        // Check if updated name is unique
        // If not, return back with error
        if ($tag->name != $request->name && count(Tag::where('name', $request->name)->get()) != 0){
            return back()->withError('Tag sudah ada');
        }
        
        // Update tag data
        $tag->name = $request->name;
        $tag->description = $request->description;

        // Placeholder for future tag's image feature
        // if ($request->image) {
        //     if($post->hasImage()) {
        //         File::delete(public_path("/".$post->image));
        //     }
        //     $imageName = time().'_'.$request->image->getClientOriginalName();  
        //     $request->image->move(public_path('images/posts'), $imageName);
        //     $post->image = "images/posts/".$imageName;
        // } else {
        //     $post->image = "images/placeholder.png";
        // }

        // Update tag data in the database
        $tag->save();

        // Check if inputted tag exists in the database
        // If yes, redirect to admin tag index page with success
        // If not, return back with error
        $data = Tag::where('id','=',$tag->id)->get();
        if ($data) {
            $request->session()->flash('success', 'Tag berhasil diupdate!');
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
            return back()->withError('Tag default tidak dapat dihapus');
        }
        
        // Delete PostTags with target tag id
        PostTag::where('tag_id', $tag->id)->delete();

        // Delete targeted tag from database
        $tag->delete();

        // Redirect to admin tag index page with success
        request()->session()->flash('success', 'Tag berhasil dihapus!');
        return redirect()->route('admin.tags.index');
    }
}
