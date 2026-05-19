<?php

namespace App\Http\Controllers;

use App\Models\Blogs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class BlogController extends Controller
{
   public function destroy(Blogs $blog)
{
    $blog->delete();

    return redirect()->route('user.blog')
        ->with(__('messages.success'), __('messages.Blog deleted successfully.'));
}

    public function index()
    {
        $auth = Auth::user()->id;
        $blogs = Blogs::where("user_id", $auth)->get();
        return view("user-view.blogs.index", compact("blogs"));
    }
    public function edit($id)
    {

        $blogs = Blogs::find($id);
        // dd($blogs);
        return view("user-view.blogs.create", compact("blogs"));
    }
    public function create()
    {
        return view("user-view.blogs.create");
    }
public function store(Request $request)
{
    $request->validate([
        'title' => 'required|string|max:255',
        'content' => 'required|string',
        'featured_image' => 'nullable|image|mimes:jpeg,jpg,png,gif,bmp,webp,svg|max:5120',
        'status' => 'required|in:draft,published,archived',
    ]);

    $imagePath = null;

    if ($request->hasFile('featured_image')) {
        $image = $request->file('featured_image');
        $filename = time() . '_' . $image->getClientOriginalName();

        $destination = public_path('blogs');
        if (!file_exists($destination)) {
            mkdir($destination, 0755, true);
        }

        $image->move($destination, $filename);

        // Relative path to store in the DB
        $imagePath = 'blogs/' . $filename;
    }

    $slug = $request->slug ?? Str::slug($request->title);

    if ($request->filled('blog_id')) {
        // UPDATE flow
        $blog = Blogs::find($request->blog_id);
        if (!$blog) {
            return redirect()->back()->with('error', 'Blog not found.');
        }

        $blog->update([
            'title' => $request->title,
            'slug' => $slug,
            'content' => $request->content,
            'excerpt' => $request->excerpt,
            'status' => $request->status,
            'published_at' => $request->published_at,
            'featured_image' => $imagePath ?? $blog->featured_image,
        ]);

        return redirect()->route("user.blog")
            ->with(__('messages.success'), __('messages.Blog updated successfully.'));
    } else {
        // CREATE flow
        Blogs::create([
            'user_id' => auth()->id(),
            'title' => $request->title,
            'slug' => $slug,
            'content' => $request->content,
            'excerpt' => $request->excerpt,
            'featured_image' => $imagePath,
            'status' => $request->status,
            'published_at' => $request->published_at,
        ]);

        return redirect()->route("user.blog")
            ->with(__('messages.success'), __('messages.Blog post created successfully!'));
    }
}

}
