<?php

namespace App\Http\Controllers;

use App\Mail\MemberEmail;
use App\Models\Member;
use App\Models\Messages;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\FamilyTree;
class MessagesController extends Controller
{
    public function messagefrom()
    {
        return view("admin-view.messages.messagefrom");
    }

    public function messageto()
    {

        $messages = Messages::where("sender_id", 0)->paginate(10);
        return view("admin-view.messages.messageto", compact('messages'));
    }
    public function usermessages()
    {
        $auth = Auth::user();

        $userFamilies = FamilyTree::where('ownerId', $auth->id)
            ->where('Status', '!=', 2)
            ->pluck('id')
            ->toArray();

        $members = Member::whereIn('family_id', $userFamilies)
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->get();

        $sentMessages = Messages::where('sender_id', $auth->id)
            ->whereNull('parent_id')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('user-view.messages.messagefrom', compact('members', 'sentMessages'));
    }

    public function sendEmailToMembers(Request $request)
    {
        $request->validate([
            'member_ids'   => 'required|array|min:1',
            'member_ids.*' => 'exists:tbl_members,id',
            'subject'      => 'required|string|max:255',
            'body'         => 'required|string',
        ]);

        $auth    = Auth::user();
        $members = Member::whereIn('id', $request->member_ids)
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->get();

        $sent   = 0;
        $failed = 0;

        foreach ($members as $member) {
            try {
                Mail::to($member->email)->send(
                    new MemberEmail($request->subject, $request->body, $auth->name ?? 'Family Tree Admin')
                );
                $sent++;
            } catch (\Exception $e) {
                $failed++;
            }

            $recipientUser = User::where('email', $member->email)->first();

            Messages::create([
                'sender_id'    => $auth->id,
                'recipient_id' => $recipientUser ? $recipientUser->id : 0,
                'subject'      => $request->subject,
                'body'         => $request->body,
                'status'       => 'sent',
            ]);
        }

        $msg = "Email sent to {$sent} member(s).";
        if ($failed > 0) {
            $msg .= " {$failed} failed (check email addresses).";
        }

        return redirect()->route('user.messageboard')->with('success', $msg);
    }
    public function usermessagesto()
    {
        $auth = Auth::user();
        $messages = Messages::where('recipient_id', $auth->id)
            ->whereNull('parent_id')
            ->with('sender')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        return view("user-view.messages.messageto", compact('messages'));
    }
    public function create()
    {
        $members = User::all();
        return view('admin-view.messages.createmessage', compact('members'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'recipient_id' => 'required|exists:tbl_user,id',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        Messages::create([
            'sender_id' => 0,
            'recipient_id' => $request->recipient_id,
            'subject' => $request->subject,
            'body' => $request->message,
        ]);

        return redirect()->route('admin.messages.create')
         ->with(__('messages.success'), __('messages.Message sent successfully.'));
    }
    public function usersstore(Request $request)
    {
        // dd($request->all());
        $auth = Auth::user();
        
        try {
            // Get all families owned by the current user
            $userFamilies = FamilyTree::where('ownerId', $auth->id)
                ->where('Status', '!=', 2) // Exclude deleted families
                ->pluck('id')
                ->toArray();
            
            // Validate that the recipient is a member of one of the user's families
            $member = Member::where('id', $request->category_id)
                ->whereIn('family_id', $userFamilies)
                ->firstOrFail();
            
            $message = new Messages();
            $message->recipient_id = $request->category_id;
            $message->subject = $request->subject;
            $message->body = $request->message;
            $message->sender_id = auth()->id();
            $message->save();

            return redirect()->back()
                ->with(__('messages.success'), __('messages.Message sent successfully.'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->back()
                ->with('error', __('messages.Invalid recipient or member not found in your families.'));
        }
    }
    public function sendmessage()
    {
        $auth = Auth::user();
        
        // Get all families owned by the current user
        $userFamilies = FamilyTree::where('ownerId', $auth->id)
            ->where('Status', '!=', 2) // Exclude deleted families
            ->pluck('id')
            ->toArray();
        
        // Get all members from those families
        $members = Member::whereIn('family_id', $userFamilies)->get();
        
        return view("user-view.messages.createmessage", compact("members"));
    }
    public function show($id)
    {
        $message = Messages::with(['replies.sender'])->findOrFail($id);

        // Mark as read if not already
        if ($message->status === 'unread') {
            $message->update(['status' => 'read']);
        }
        return view('admin-view.messages.show', compact('message'));
    }
    public function usershow($id)
    {
        $message = Messages::with(['replies.sender'])->findOrFail($id);

        // Mark as read if not already
        if ($message->status === 'unread') {
            $message->update(['status' => 'read']);
        }
        return view('user-view.messages.show', compact('message'));
    }
    public function userReply(Request $request, $id)
    {
        $request->validate([
            'reply_body' => 'required|string|max:5000',
        ]);

        $original = Messages::findOrFail($id);

        Messages::create([
            'sender_id' => Auth::id(),
            'recipient_id' => 0, // to admin
            'subject' => 'RE: ' . $original->subject,
            'body' => $request->reply_body,
            'parent_id' => $original->id,
            'status' => 'unread',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('user.messages.show', $id)
            ->with('success', __('messages.Reply sent successfully.'));
    }

    public function reply(Request $request, $id)
    {
        $request->validate([
            'reply_body' => 'required|string|max:5000',
        ]);

        $original = Messages::findOrFail($id);

        Messages::create([
            'sender_id' => 0, // admin
            'recipient_id' => $original->sender_id ?: $original->recipient_id,
            'subject' => 'RE: ' . $original->subject,
            'body' => $request->reply_body,
            'parent_id' => $original->id,
            'status' => 'unread',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.messages.show', $id)
            ->with(__('messages.success'), __('messages.Reply sent successfully.'));
    }
}
