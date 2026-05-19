<?php

namespace App\Http\Controllers;

use App\Models\Template;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TemplateController extends Controller
{
    public function templates()
    {
        $templates = Template::paginate(10);
        return view("admin-view.template.index", compact('templates'));
    }
    public function create()
    {
        return view("admin-view.template.create");
    }
    public function store(Request $request)
    {
        // Validate the request data

        $data = [
            'name' => $request->input('name'),
        ];

        // Handle image upload if exists
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('templates'), $imageName);
            $data['image'] = 'templates/' . $imageName;
        }

        // If ID is present, update the existing template
        if ($request->id) {
            $template = Template::findOrFail($request->id);
            $template->update($data);
            $message = __('messages.Template Updated Successfully');
        } else {
            // Create a new template
            Template::create($data);
            $message = __('messages.Template Created Successfully');
        }

        return redirect()->route('admin.templates')
            ->with(__('messages.success'), $message);
    }
    public function edit($id)
    {
        $template = Template::findOrFail($id);
        return view("admin-view.template.create", compact('template'));
    }
    public function templateuser()
    {
        $templates = Template::all();
        return view("user-view.configuration.templates",compact("templates"));
    }
    public function destroy($id)
    {
        $template = Template::findOrFail($id);
        $template->delete();
        return redirect()->route('admin.templates')
            ->with(__('messages.success'), __('messages.Template Deleted Successfully'));
    }
    public function storetemplate(Request $request)
    {
        // dd($request->all());
        $user = Auth::user(); // Get the authenticated user
        $user = User::findOrFail($user->id); // Ensure the user exists
        // Update the 'template' attribute of the user
        $user->template = $request->input("template");
        $user->has_image = $request->has("contains_user_image") ? 1 : 0;
        $user->save(); // Save the changes to the user model

        return redirect()->back()
            ->with(__('messages.success'), __('messages.Template Updated Successfully'));
    }
}
