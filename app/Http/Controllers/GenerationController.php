<?php

namespace App\Http\Controllers;

use App\Models\Generations;
use Illuminate\Http\Request;

class GenerationController extends Controller
{
        public function index()
    {
        // Logic to display types
        $types = Generations::paginate(10);
        return view('admin-view.generations.index', compact('types'));
    }

    public function create()
    {
        // Logic to show the form for creating a new type
        return view('admin-view.generations.create');
    }
    public function edit($id)
    {
        // Logic to show the form for editing a type
        $type = Generations::find($id);
        return view('admin-view.generations.create', compact('type'));
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
            $type = Generations::findOrFail($request->id);
            $type->update($data);

            return redirect()->route('admin.generations')->with('success', 'Type updated successfully.');
        } else {
            // Create new type
            Generations::create($data);

            return redirect()->route('admin.generations')->with('success', 'Type created successfully.');
        }
    }
    public function destroy($id)
    {
        $user = Generations::findOrFail($id);
        $user->delete();
        
        return redirect()->route('admin.generations')->with('success', 'User deleted successfully.');
    }
}
