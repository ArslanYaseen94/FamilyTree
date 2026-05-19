<?php

namespace App\Http\Controllers;

use App\Models\Types;
use Illuminate\Http\Request;

class TypeController extends Controller
{
    public function index()
    {
        // Logic to display types
        $types = Types::paginate(10);
        return view('admin-view.types.index', compact('types'));
    }

    public function create()
    {
        // Logic to show the form for creating a new type
        return view('admin-view.types.create');
    }
    public function edit($id)
    {
        // Logic to show the form for editing a type
        $type = Types::find($id);
        return view('admin-view.types.create', compact('type'));
    }
    public function store(Request $request)
    {
        // Validation logic (optional but recommended)
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        // Common data to save
        $data = [
            'name' => $request->name,
            'is_active' => $request->has('status') ? '1' : '0',
        ];

        if ($request->id) {
            // Update existing type
            $type = Types::findOrFail($request->id);
            $type->update($data);

            return redirect()->route('admin.types')->with('success', 'Type updated successfully.');
        } else {
            // Create new type
            Types::create($data);

            return redirect()->route('admin.types')->with('success', 'Type created successfully.');
        }
    }
    public function destroy($id)
    {
        $user = Types::findOrFail($id);
        $user->delete();
        
        return redirect()->route('admin.types')->with('success', 'User deleted successfully.');
    }
}
