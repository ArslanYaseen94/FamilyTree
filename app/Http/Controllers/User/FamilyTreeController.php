<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest\FamilyTreeStoreRequest;
use App\Http\Requests\UserRequest\FamilyTreeUpdateRequest;
use App\Http\Requests\UserRequest\MemberStoreRequest;
use App\Http\Requests\UserRequest\MemberUpdateRequest;
use App\Support\MemberLifeDateValidator;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use App\Models\FamilyTree;
use App\Models\Member;
use App\Models\User;
use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\App;
class FamilyTreeController extends Controller
{
    public function index()
    {
        $userId = Auth::guard("web")->user()->id;
        // Get only families owned by the current user
        // Exclude deleted families (Status = 2)
        $userFamilies = FamilyTree::where('ownerId', $userId)
            ->where('Status', '!=', 2)
            ->pluck('id')
            ->toArray();
        // Get members from only those families
        $FamilyTreeInfo = Member::with("family")->where("parent_id", 0)->whereIn('family_id', $userFamilies)->get();
        // dd($FamilyTreeInfo);
        return view("user-view.familytree.index", compact("FamilyTreeInfo"));
    }
    public function create()
    {

        return view("user-view.familytree.create");
    }
    public function store(FamilyTreeStoreRequest $request)
    {
        $familyid = $request->familyid;

        $now = date('Y-m-d H:i:s');
        $update_date = date('Y-m-d H:i:s');


        try {
            $values = array('ownerId' => Auth::guard("web")->user()->id, 'familyid' => $familyid, 'created_at' => $now, 'updated_at' => $update_date);
            FamilyTree::insert($values);
            return redirect()->back()->with(__('messages.success'), __('messages.FamilyTree Created Successfully'));
        } catch (\Illuminate\Database\QueryException $ex) {
            return Response()->json([]);
        }
    }

    public function deleteFamilyTree(Request $request)
    {
        try {
            // Find the family tree by ID
            $familyTree = FamilyTree::findOrFail($request->id);
            
            // Check if the current user owns this family
            if ($familyTree->ownerId != Auth::guard("web")->user()->id) {
                return response()->json(['message' => __('messages.Unauthorized')], 403);
            }

            // Update the status of the family tree to 2 (soft delete)
            // NOTE: DB column name is `Status` (capital S)
            $familyTree->Status = 2;
            $familyTree->save();

            // Return a JSON response
            return response()->json(['message' => __('messages.Family tree status updated successfully.')], 200);
        } catch (Exception $e) {
            // If something goes wrong, return an error message
            return response()->json(['message' => __('messages.An error occurred while updating the family tree status.')], 500);
        }
    }

    public function update(FamilyTreeUpdateRequest $request)
    {
        $editfamilyid = $request->editfamilyid;
        $editid = $request->editid;
        
        // Check if the current user owns this family
        $familyTree = FamilyTree::find($editid);
        if (!$familyTree || $familyTree->ownerId != Auth::guard("web")->user()->id) {
            return response()->json(['message' => __('messages.Unauthorized')], 403);
        }

        try {
            FamilyTree::where('id', $editid)
                ->update([
                    'familyid' => $editfamilyid
                ]);
            return response()->json(['message' => __('messages.Update information successfully')], 200);
        } catch (\Illuminate\Database\QueryException $ex) {
        }
    }

public function addmember($id)
{
    $Id = Crypt::decryptString($id);
    
    // Check if the current user owns this family
    $familyOwner = FamilyTree::where('id', $Id)->where('ownerId', Auth::guard("web")->user()->id)->first();
    if (!$familyOwner) {
        return redirect()->route('user.familytree')->withErrors(__('messages.Unauthorized'));
    }
    
    $hasMembers = Member::where('family_id', $Id)->exists();

    $auth = Auth::user();
    $locale = Session::get('locale', App::getLocale()); // fallback to app locale if not in session

    // Check if current language is Korean
    if ($locale === 'ko') {
        return view('user-view.familytree.korean', [
            'id' => $Id,
            'hasMembers' => $hasMembers,
        ]);
    }

    // Return view based on user template
    if ($auth->template === 'First Template') {
        return view('user-view.familytree.horizontaladdmember', [
            'id' => $Id,
            'hasMembers' => $hasMembers,
        ]);
    } else {
        return view('user-view.familytree.addmember', [
            'id' => $Id,
            'hasMembers' => $hasMembers,
        ]);
    }
}
    public function memberstore(MemberStoreRequest $request)
    {
        // Check if the current user owns this family
        $familyOwner = FamilyTree::where('id', $request->family_id)->where('ownerId', Auth::guard("web")->user()->id)->first();
        if (!$familyOwner) {
            return response()->json(['error' => __('messages.Unauthorized')], 403);
        }
        
        // Extract basic data
        $firstname = $request->firstname;
        $lastname = $request->lastname;
        $email = $request->email;
        $gender = $request->gender;
        $family_id = $request->family_id;
        $mobile = $request->mobile;

        // Random password generation
        $originalPassword = Str::random(8); // You can change the length
        $hashedPassword = Hash::make($originalPassword);

        // Upload primary photo
        $photo = null;
        if ($request->hasFile('poll_file')) {
            $imageName = $request->file('poll_file');
            $photo = rand() . '' . time() . '.' . $imageName->extension();
            $imageName->move(public_path('assets/front-end/Memberimgs'), $photo);
        }

        // Upload multiple documents
        $multiplePhotos = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                if ($image) {
                    $imageName = rand() . time() . '.' . $image->extension();
                    $image->move(public_path('assets/front-end/MemberDocuments'), $imageName);
                    $multiplePhotos[] = $imageName;
                }
            }
        }
        $multiplePhotosString = implode('**', $multiplePhotos);

        // Determine parent ID based on the chosen relation type so the tree renders genealogically
        $selfId = (int) $request->self_id;
        $type = (int) $request->type;
        $parent_id = $selfId; // default: new member is a child of the clicked card

        if ($selfId === 0 || $type === 4) {
            // First/root member, or adding a Parent above someone (handled below)
            $parent_id = 0;
        } elseif ($type === 6 && $selfId > 0) {
            // Sibling shares the same parent as the clicked member
            $selfMember = Member::find($selfId);
            $parent_id = $selfMember ? (int) $selfMember->parent_id : $selfId;
        } elseif ($type === 7 && $selfId > 0) {
            // Uncle/Aunt: sibling of the clicked member's parent → attach to grandparent
            $selfMember = Member::find($selfId);
            if ($selfMember && $selfMember->parent_id) {
                $parentOfSelf = Member::find($selfMember->parent_id);
                $parent_id = $parentOfSelf ? (int) $parentOfSelf->parent_id : 0;
            } else {
                $parent_id = 0;
            }
        } elseif ($type === 8 && $selfId > 0) {
            // Cousin: child of one of self's parent's siblings; if none recorded yet, place under
            // self's parent so the cousin is at least visible at the correct generation
            $selfMember = Member::find($selfId);
            $parent_id = $selfMember ? (int) $selfMember->parent_id : $selfId;
        }

        try {
            // Save Member
            $member = new Member();
            $member->family_id = $family_id;
            $member->parent_id = $parent_id;
            $member->firstname = $firstname;
            $member->lastname = $lastname;
            $member->type = $type;
            $member->generation = $request->generation;
            $member->gender = $gender;
            $lifeDates = MemberLifeDateValidator::normalizeDates($request);
            $member->death = $lifeDates['death'];
            $member->village = $request->village;
            $member->birthdate = $request->birthdate;
            $member->marriagedate = $request->marriagedate;
            $member->deathdate = $lifeDates['deathdate'];
            $member->user = $request->user;
            $member->photo = $photo;
            $member->avatar = $request->avatar;
            $member->facebook = $request->facebook;
            $member->twitter = $request->twitter;
            $member->instagram = $request->instagram;
            $member->email = $email;
            $member->tel = $request->tel;
            $member->mobile = $mobile;
            $member->site = $request->site;
            $member->birthplace = $request->birthplace;
            $member->deathplace = $request->deathplace;
            $member->profession = $request->profession;
            $member->company = $request->company;
            $member->interests = $request->interests;
            $member->bio = $request->bio;
            $member->images = $multiplePhotosString;
            $member->save();
            $lastInsertedId = $member->id;

            // Update parent for type 4 (adding a Parent above an existing member)
            if ($selfId !== 0 && $type === 4) {
                $existing = Member::find($selfId);
                if ($existing) {
                    $existing->parent_id = $lastInsertedId;
                    $existing->save();
                }
            }

            // Create corresponding User
            $user = new User(); // Adjust namespace if needed
            $user->name = $firstname . ' ' . $lastname;
            $user->email = $email;
            $user->password = $hashedPassword;
            $user->original_password = $originalPassword; // Ensure this is safe to store
            $user->familyId = $family_id;
            $user->gender = $gender;
            $user->phone = $mobile;
            $user->save();
            $message = __('messages.Member and user created successfully.');

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json(['message' => $message]);
            }

            return redirect()->back()->with(__('messages.success'), $message);

        } catch (Exception $e) {
            return response()->json([
                'error' => __('messages.Failed to create member or user'),
                'details' => $e->getMessage()
            ], 500);
        }
    }
    public function firstmemberstore(Request $request)
    {
        $request->validate([
            'family_id' => ['required', 'regex:/^\d{5}-\d{7}-\d{1}$/'],
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'gender' => 'required|integer',
            'birthdate' => 'nullable|date',
            'marriagedate' => 'nullable|date',
            'deathdate' => 'nullable|date',
        ]);

        $lifeValidator = \Illuminate\Support\Facades\Validator::make($request->all(), []);
        MemberLifeDateValidator::addAfterRules($lifeValidator, $request);
        if ($lifeValidator->fails()) {
            return redirect()->back()->withErrors($lifeValidator)->withInput();
        }

        $lifeDates = MemberLifeDateValidator::normalizeDates($request);

        // Check if family tree already exists
        $familyTreeExists = FamilyTree::where('familyid', $request->family_id)->first();

        if (!$familyTreeExists) {
            $familyTree = new FamilyTree();
            $familyTree->familyid = $request->family_id;
            $familyTree->ownerId = auth()->id(); // Gets currently logged-in user ID
            $familyTree->Status = 1;
            $familyTree->save();
        }

        $familyTreeExists = FamilyTree::where('familyid', $request->family_id)->first();

        // Extract data
        $firstname = $request->firstname;
        $lastname = $request->lastname;
        $email = $request->email;
        $gender = $request->gender;
        $mobile = $request->mobile;

        // Generate random password
        $originalPassword = Str::random(8);
        $hashedPassword = Hash::make($originalPassword);

        // File upload - photo
        $photo = null;
        if ($request->hasFile('poll_file')) {
            $imageName = $request->file('poll_file');
            $photo = rand() . '' . time() . '.' . $imageName->extension();
            $imageName->move(public_path('assets/front-end/Memberimgs'), $photo);
        }

        // File upload - multiple documents
        $multiplePhotos = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                if ($image) {
                    $imageName = rand() . time() . '.' . $image->extension();
                    $image->move(public_path('assets/front-end/MemberDocuments'), $imageName);
                    $multiplePhotos[] = $imageName;
                }
            }
        }
        $multiplePhotosString = implode('**', $multiplePhotos);

        // Determine parent ID
        $parent_id = ($request->self_id == '0' || $request->type == '4') ? 0 : $request->self_id;

        try {
            // Save Member
            $member = new Member();
            $member->family_id = $familyTreeExists->id;
            $member->parent_id = $parent_id;
            $member->firstname = $firstname;
            $member->lastname = $lastname;
            $member->type = 4;
            $member->gender = $gender;
            $lifeDates = MemberLifeDateValidator::normalizeDates($request);
            $member->death = $lifeDates['death'];
            $member->village = $request->village;
            $member->birthdate = $request->birthdate;
            $member->marriagedate = $request->marriagedate;
            $member->deathdate = $lifeDates['deathdate'];
            $member->user = $request->user;
            $member->photo = $photo;
            $member->avatar = $request->avatar;
            $member->facebook = $request->facebook;
            $member->twitter = $request->twitter;
            $member->instagram = $request->instagram;
            $member->email = $email;
            $member->tel = $request->tel;
            $member->mobile = $mobile;
            $member->site = $request->site;
            $member->birthplace = $request->birthplace;
            $member->deathplace = $request->deathplace;
            $member->profession = $request->profession;
            $member->company = $request->company;
            $member->interests = $request->interests;
            $member->bio = $request->bio;
            $member->home_town = $request->home_town;
            $member->school = $request->school;
            $member->background = $request->background_details;
            $member->business_info = $request->business_info;
            $member->images = $multiplePhotosString;
            $member->save();

            // Create User
            $user = new User(); // Adjust namespace if different
            $user->name = $firstname . ' ' . $lastname;
            $user->email = $email;
            $user->password = $hashedPassword;
            $user->original_password = $originalPassword; // Optional: store securely or send via email
            $user->familyId = $familyTreeExists->id;
            $user->gender = $gender;
            $user->phone = $mobile;
            $user->save();

            return redirect()->back()
                ->with(__('messages.success'), __('messages.Member and user added successfully'));
        } catch (Exception $e) {
            return response()->json([
                'error' => __('messages.Failed to create member or user'),
                'details' => $e->getMessage()
            ], 500);
        }
    }
    public function editModal($id)
    {
        $member = Member::find($id);
        if (!$member) {
            return response()->json(['error' => __('messages.Member not found')], 404);
        }
        // Return the modal view with member data
        return view('user-view.familytree.editmember', compact('member'));
    }

public function updateMember(MemberUpdateRequest $request, $id)
{
    $member = Member::findOrFail($id);

    $familyOwner = FamilyTree::where('id', $member->family_id)->where('ownerId', Auth::guard("web")->user()->id)->first();
    if (!$familyOwner) {
        if ($request->ajax() || $request->expectsJson()) {
            return response()->json(['message' => __('messages.Unauthorized')], 403);
        }
        return redirect()->back()->withErrors(__('messages.Unauthorized'));
    }

    $data = $request->only([
        'firstname',
        'lastname',
        'type',
        'generation',
        'gender',
        'village',
        'birthdate',
        'marriagedate',
        'user',
        'facebook',
        'twitter',
        'instagram',
        'email',
        'tel',
        'mobile',
        'site',
        'birthplace',
        'deathplace',
        'profession',
        'company',
        'interests',
        'bio',
        'avatar',
        'home_town',
        'school',
        'background_details',
        'business_info',
    ]);

    if (array_key_exists('background_details', $data)) {
        $data['background'] = $data['background_details'];
        unset($data['background_details']);
    }

    $lifeDates = MemberLifeDateValidator::normalizeDates($request);
    $data['death'] = $lifeDates['death'];
    $data['deathdate'] = $lifeDates['deathdate'];

    $member->update($data);

    $message = __('messages.Update information successfully');

    if ($request->ajax() || $request->expectsJson()) {
        return response()->json(['message' => $message]);
    }

    return redirect()->route('user.addmember', ['id' => Crypt::encryptString($member->family_id)])
        ->with(__('messages.success'), $message);
}


    public function destroy($id)
    {
        $member = Member::find($id);
        if (!$member) {
            return response()->json([
                'status' => 'error',
                'message' => __('messages.Member not found')
            ], 404);
        }
        
        // Check if the current user owns this family
        $familyOwner = FamilyTree::where('id', $member->family_id)->where('ownerId', Auth::guard("web")->user()->id)->first();
        if (!$familyOwner) {
            return response()->json([
                'status' => 'error',
                'message' => __('messages.Unauthorized')
            ], 403);
        }
        
        try {
            $member->delete();
            return response()->json([
                'status' => "success",
                'message' => __('messages.Member deleted successfully.')
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => __('messages.An error occurred while deleting the member.')
            ], 500);
        }
    }

    public function getFamilyTree($id)
    {
        // Check if the current user owns this family
        $familyOwner = FamilyTree::where('id', $id)->where('ownerId', Auth::guard("web")->user()->id)->first();
        if (!$familyOwner) {
            return response()->json(['error' => __('messages.Unauthorized')], 403);
        }

        // Fetch all members for this family. We deliberately return a flat list:
        // the front-end builds the hierarchy from parent_id so NO member is ever dropped
        // because of an unexpected `type` value.
        $members = Member::where('family_id', $id)
            ->orderBy('id')
            ->get()
            ->map(function ($member) {
                return [
                    'id' => (int) $member->id,
                    'parent_id' => (int) $member->parent_id,
                    'family_id' => (int) $member->family_id,
                    'firstname' => $member->firstname,
                    'lastname' => $member->lastname,
                    'type' => (int) $member->type,
                    'generation' => $member->generation,
                    'gender' => $member->gender,
                    'death' => $member->death,
                    'birthdate' => $member->birthdate,
                    'marriagedate' => $member->marriagedate,
                    'deathdate' => $member->deathdate,
                    'photo' => $member->photo,
                    'avatar' => $member->avatar,
                ];
            })
            ->values();

        return response()->json($members);
    }

    public function listing()
    {

        return view("user-view.familytree.familylisting");
    }
}
