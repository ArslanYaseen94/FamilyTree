<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use App\Models\Member;
use App\Models\FamilyTree;
use App\Exports\MembersExport;
use App\Exports\MembersImport;
use App\Imports\MembersImport as ImportsMembersImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;

class ImportController extends Controller
{
    public function index()
    {
        $userId = Auth::guard('web')->id();
        $families = FamilyTree::where('ownerId', $userId)
            ->where('Status', '!=', 2)
            ->orderBy('id', 'desc')
            ->get(['id', 'familyid']);

        return view("user-view.import.index", compact('families'));
    }

    public function export()
    {
        $userId = Auth::guard('web')->id();
        $families = FamilyTree::where('ownerId', $userId)
            ->where('Status', '!=', 2)
            ->orderBy('id', 'desc')
            ->get(['id', 'familyid']);

        return view("user-view.import.export", compact('families'));
    }
    public function exportMembers(Request $request)
    {
        $userId = Auth::guard('web')->id();
        $ownedFamilyIds = FamilyTree::where('ownerId', $userId)
            ->where('Status', '!=', 2)
            ->pluck('id')
            ->toArray();

        // Scope: one family or all families
        $request->validate([
            'export_scope' => 'required|in:one,all',
            'family_tree_id' => 'nullable|integer',
        ]);

        $query = Member::query()->whereIn('family_id', $ownedFamilyIds);

        if ($request->export_scope === 'one') {
            $request->validate([
                'family_tree_id' => 'required|integer',
            ]);

            $familyId = (int) $request->family_tree_id;
            if (!in_array($familyId, $ownedFamilyIds, true)) {
                return response()->json(['message' => __('messages.Unauthorized')], 403);
            }
            $query->where('family_id', $familyId);
        }

        $filterType = $request->input('filter_type');

        if ($filterType === 'id') {
            $request->validate([
                'from_id' => 'required|integer',
                'to_id' => 'required|integer',
            ]);
            $query->whereBetween('id', [$request->from_id, $request->to_id]);
        } elseif ($filterType === 'date') {
            $request->validate([
                'from_date' => 'required|date',
                'to_date' => 'required|date',
            ]);
            $query->whereBetween('created_at', [$request->from_date, $request->to_date]);
        } elseif ($filterType === 'all') {
            // no extra filters
        }

        $members = $query->get();

        $filename = 'members_export_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(new MembersExport($members), $filename);
    }
    public function import(Request $request)
    {
        $request->validate([
            'family_id' => 'required|integer|exists:tbl_familytree,id',
            'excel_file' => 'required|file|mimes:csv,txt',
        ], [
            'family_id.required' => __('messages.Please select a family tree.'),
            'excel_file.mimes' => __('messages.Only CSV files are allowed.'),
        ]);

        $userId = Auth::guard('web')->id();
        $familyId = (int) $request->family_id;

        $ownsFamily = FamilyTree::where('id', $familyId)
            ->where('ownerId', $userId)
            ->where('Status', '!=', 2)
            ->exists();

        if (!$ownsFamily) {
            return back()->with(__('messages.error'), __('messages.You do not own this family tree.'));
        }

        $file = $request->file('excel_file');
        $extension = strtolower($file->getClientOriginalExtension());

        if ($extension !== 'csv') {
            return back()->with(__('messages.error'), __('messages.Only CSV files are allowed.'));
        }

        $handle = fopen($file->getRealPath(), 'r');
        $header = fgetcsv($handle);
        fclose($handle);

        if (!$header) {
            return back()->with(__('messages.error'), __('messages.The CSV file is empty or cannot be read.'));
        }

        $requiredColumns = [
            'parent_id', 'first_name', 'last_name', 'type',
            'gender', 'death', 'birthdate', 'marriage_date', 'deathdate', 'user',
            'photo', 'avatar', 'facebook', 'twitter', 'instagram', 'email',
            'tel', 'mobile', 'site', 'birthplace', 'deathplace', 'profession',
            'company', 'interests', 'bio', 'images', 'created_at'
        ];

        $header = array_map('trim', $header);
        $header = array_map('strtolower', $header);

        $missingColumns = array_diff($requiredColumns, $header);

        if (!empty($missingColumns)) {
            return back()->with(__('messages.error'), __('messages.CSV is missing required columns:') . ' ' . implode(', ', $missingColumns));
        }

        try {
            Excel::import(new ImportsMembersImport($familyId), $file);
        } catch (\Exception $e) {
            return back()->with(__('messages.error'), __('messages.Import failed. Please check your CSV data.'));
        }

        return back()->with(__('messages.success'), __('messages.Members imported successfully.'));
    }

    public function downloadSample()
    {
        $headers = [
            'parent_id', 'first_name', 'last_name', 'type',
            'gender', 'death', 'birthdate', 'marriage_date', 'deathdate', 'user',
            'photo', 'avatar', 'facebook', 'twitter', 'instagram', 'email',
            'tel', 'mobile', 'site', 'birthplace', 'deathplace', 'profession',
            'company', 'interests', 'bio', 'images', 'created_at'
        ];

        $sampleRow = [
            '0', 'John', 'Doe', '1',
            '2', '0', '1990-01-15', '2015-06-20', '', '',
            '', '1', 'https://facebook.com/johndoe', '@johndoe', '@johndoe', 'john@example.com',
            '123-456-7890', '098-765-4321', 'https://johndoe.com', 'New York', '', 'Engineer',
            'Tech Corp', 'Music, Travel', 'A short bio here.', '', '2026-01-01 00:00:00'
        ];

        $callback = function () use ($headers, $sampleRow) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);
            fputcsv($file, $sampleRow);
            fclose($file);
        };

        return Response::stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="members_sample_template.csv"',
        ]);
    }
}
