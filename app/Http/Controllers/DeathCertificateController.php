<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Member;
use App\Models\FamilyTree;
use Illuminate\Support\Facades\Auth;

class DeathCertificateController extends Controller
{
    public function index()
    {
        $userId = Auth::guard('web')->user()->id;

        $userFamilyIds = FamilyTree::where('ownerId', $userId)
            ->where('Status', '!=', 2)
            ->pluck('id')
            ->toArray();

        $deceasedMembers = Member::with('family')
            ->whereIn('family_id', $userFamilyIds)
            ->where('death', 0)
            ->orderBy('deathdate', 'desc')
            ->paginate(10);

        return view('user-view.death-certificate.index', compact('deceasedMembers'));
    }

    public function certificate($id)
    {
        $userId = Auth::guard('web')->user()->id;

        $member = Member::with('family')->findOrFail($id);

        $familyOwner = FamilyTree::where('id', $member->family_id)
            ->where('ownerId', $userId)
            ->first();

        if (!$familyOwner) {
            return redirect()->route('user.deceased')->withErrors('Unauthorized access.');
        }

        if ($member->death != 0) {
            return redirect()->route('user.deceased')->withErrors('This member is not marked as deceased.');
        }

        $membersDict = Member::where('family_id', $member->family_id)
            ->get()
            ->keyBy('id');

        $parentMember = null;
        if ($member->parent_id && isset($membersDict[$member->parent_id])) {
            $parentMember = $membersDict[$member->parent_id];
        }

        $spouseMember = Member::where('family_id', $member->family_id)
            ->where('parent_id', $member->id)
            ->where('type', 2)
            ->first();

        if (!$spouseMember) {
            $spouseMember = Member::where('family_id', $member->family_id)
                ->where('id', $member->parent_id)
                ->whereIn('type', [2, 3])
                ->first();
        }

        return view('user-view.death-certificate.certificate', compact(
            'member', 'parentMember', 'spouseMember', 'familyOwner'
        ));
    }
}
