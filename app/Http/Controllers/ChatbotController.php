<?php

namespace App\Http\Controllers;

use App\Models\FamilyTree;
use App\Models\Member;
use App\Support\MemberLifeDateValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatbotController extends Controller
{
    private const ACTION_START = 'ACTION_JSON';
    private const ACTION_END = 'END_ACTION_JSON';

    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:4000',
            'conversation' => 'nullable|array',
        ]);

        $userMessage = trim($request->input('message'));
        $conversation = $request->input('conversation', []);

        $conversation[] = ['role' => 'user', 'content' => $userMessage];

        $quickNav = $this->tryQuickNavigation($userMessage);
        if ($quickNav && !empty($quickNav['success'])) {
            $pageLabel = ucfirst(str_replace('_', ' ', (string) ($quickNav['page'] ?? 'page')));
            $displayResponse = "Opening {$pageLabel} for you now.";
            $conversation[] = ['role' => 'assistant', 'content' => $displayResponse];

            return response()->json([
                'response' => $displayResponse,
                'conversation' => $conversation,
                'action_result' => $quickNav,
            ]);
        }

        $systemPrompt = $this->buildSystemPrompt();
        $aiResult = $this->callCerebras($systemPrompt, $conversation);

        if (isset($aiResult['error'])) {
            return response()->json(['error' => $aiResult['error']]);
        }

        $rawResponse = $aiResult['response'];
        $displayResponse = $this->stripActionBlocks($rawResponse);
        $actionResult = $this->processActionBlock($rawResponse);

        $conversation[] = ['role' => 'assistant', 'content' => $displayResponse];

        $payload = [
            'response' => $displayResponse,
            'conversation' => $conversation,
        ];

        if ($actionResult) {
            $payload['action_result'] = $actionResult;
            if (!empty($actionResult['member_added'])) {
                $payload['member_added'] = $actionResult['member_added'];
            }
        }

        return response()->json($payload);
    }

    private function getUserFamilies()
    {
        return FamilyTree::where('ownerId', Auth::id())->get();
    }

    private function buildFamilyContext(): string
    {
        $families = $this->getUserFamilies();
        if ($families->isEmpty()) {
            return "The user has no family trees yet.";
        }

        $lines = [];
        foreach ($families as $family) {
            $members = Member::where('family_id', $family->id)
                ->orderBy('id')
                ->get();

            $label = $family->familyid ?? ('Tree #' . $family->id);
            $lines[] = "Family: {$label} (family_id: {$family->id})";
            foreach ($members as $m) {
                $status = $m->death == 1 ? 'alive' : 'deceased';
                $rel = $this->typeLabel((int) $m->type);
                $lines[] = "  - ID {$m->id}: {$m->firstname} {$m->lastname} | {$rel} | gender " . ($m->gender == 1 ? 'male' : 'female') . " | {$status}"
                    . ($m->birthdate ? " | born {$m->birthdate}" : '')
                    . ($m->death == 0 && $m->deathdate ? " | died {$m->deathdate}" : '')
                    . ($m->email ? " | {$m->email}" : '')
                    . ($m->profession ? " | {$m->profession}" : '');
            }
        }

        return implode("\n", $lines);
    }

    private function typeLabel(int $type): string
    {
        return match ($type) {
            1 => 'child',
            2 => 'partner',
            3 => 'ex-partner',
            4 => 'parent',
            6 => 'sibling',
            7 => 'uncle/aunt',
            8 => 'cousin',
            default => 'member',
        };
    }

    private function buildSystemPrompt(): string
    {
        $familyContext = $this->buildFamilyContext();
        $siteMap = $this->siteMapText();

        return <<<PROMPT
You are the Family AI Assistant for NEXTCOME — a private family-tree website. Help the logged-in user manage their families and navigate the site.

RULES:
- Never say you are Gemini, ChatGPT, Cerebras, Qwen, or any AI vendor.
- Call yourself "Family AI Assistant" only.
- Use the family data below to answer questions accurately.
- death field: 1 = alive, 0 = deceased (this is inverted in the database).
- gender: 1 = male, 2 = female.
- member type: 1=child, 2=partner, 3=ex-partner, 4=parent, 6=sibling, 7=uncle/aunt, 8=cousin.
- For destructive actions (delete), always confirm with the user first, then set "confirm": true in the JSON.
- When performing an action, put a short friendly message in your visible reply AND include the machine block below (user does not see the block).

SITE PAGES (suggest these when user wants to go somewhere):
{$siteMap}

USER'S FAMILY DATA:
{$familyContext}

ACTIONS — when the user wants you to DO something (add/update/delete/mark deceased/navigate), after collecting required info append exactly one block:

ACTION_JSON
{"action":"add_member","confirm":true,"family_id":1,"firstname":"...","lastname":"...","gender":1,"type":1,"self_id":5,"birthdate":null,"email":null,"mobile":null,"bio":null,"death":1,"deathdate":null}
END_ACTION_JSON

Other actions (use member_id from data above):
- update_member: {"action":"update_member","confirm":true,"member_id":12,"firstname":"...","bio":"..."}
- mark_deceased: {"action":"mark_deceased","confirm":true,"member_id":12,"deathdate":"2020-01-15"}
- mark_alive: {"action":"mark_alive","confirm":true,"member_id":12}
- delete_member: {"action":"delete_member","confirm":true,"member_id":12}
- navigate: {"action":"navigate","confirm":true,"page":"deceased"} — page keys: dashboard, familytree, familylisting, deceased, messages, messageto, sendmessage, photos, blog, import, export, memberships, profile, settings

For add_member: required firstname, lastname, gender, type, family_id, self_id (0 for root). Optional: birthdate, deathdate, email, mobile, bio, village, profession, etc. Set death=1 if alive, death=0 if deceased; if deceased, deathdate is required.

Only output ACTION_JSON when you have enough information and user confirmed (or clearly said yes/proceed).
PROMPT;
    }

    private function siteMapText(): string
    {
        $pages = [
            'dashboard' => route('user.dashboard'),
            'familytree' => route('user.familytree'),
            'familylisting' => route('user.familylisting'),
            'deceased' => route('user.deceased'),
            'messages' => route('user.messageboard'),
            'messageto' => route('user.messageto'),
            'sendmessage' => route('user.send.message'),
            'photos' => route('user.photos'),
            'blog' => route('user.blog'),
            'import' => route('user.import'),
            'export' => route('user.export'),
            'memberships' => route('user.memberships'),
            'profile' => route('user.profile'),
            'settings' => route('user.setting'),
        ];

        $lines = [];
        foreach ($pages as $key => $url) {
            $lines[] = "- {$key}: {$url}";
        }

        return implode("\n", $lines);
    }

    private function callCerebras(string $systemPrompt, array $conversationHistory): array
    {
        $apiKey = env('CEREBRAS_API_KEY');
        if (!$apiKey) {
            return ['error' => __('messages.Chatbot API key is not configured. Add CEREBRAS_API_KEY to your .env file.')];
        }

        $model = env('CEREBRAS_MODEL', 'qwen-3-235b-a22b-instruct-2507');
        $messages = [['role' => 'system', 'content' => $systemPrompt]];

        foreach ($conversationHistory as $turn) {
            $role = ($turn['role'] ?? '') === 'assistant' ? 'assistant' : 'user';
            $content = $turn['content'] ?? '';
            if ($content !== '') {
                $messages[] = ['role' => $role, 'content' => $content];
            }
        }

        try {
            $response = Http::timeout(90)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post('https://api.cerebras.ai/v1/chat/completions', [
                    'model' => $model,
                    'messages' => $messages,
                    'temperature' => 0.4,
                    'max_tokens' => 2048,
                ]);

            if ($response->successful()) {
                $aiText = $response->json('choices.0.message.content');
                if (!$aiText) {
                    return ['error' => __('messages.Sorry, something went wrong. Please try again.')];
                }

                return ['response' => $aiText];
            }

            $body = $response->json();
            $apiMessage = $body['error']['message'] ?? $body['message'] ?? $response->body();
            Log::error('Cerebras API error', ['status' => $response->status(), 'body' => $response->body()]);

            if ($response->status() === 401 || str_contains((string) $apiMessage, 'API key')) {
                return ['error' => __('messages.Chatbot API key is invalid. Check CEREBRAS_API_KEY in .env.')];
            }

            if ($response->status() === 429) {
                return ['error' => __('messages.Chatbot rate limit reached. Please try again in a moment.')];
            }

            return ['error' => __('messages.Sorry, something went wrong. Please try again.')];
        } catch (\Throwable $e) {
            Log::error('Cerebras request failed: ' . $e->getMessage());

            return ['error' => __('messages.Sorry, something went wrong. Please try again.')];
        }
    }

    private function stripActionBlocks(string $text): string
    {
        $text = preg_replace('/ACTION_JSON\s*[\s\S]*?\s*END_ACTION_JSON/i', '', $text) ?? $text;
        $text = preg_replace('/ADD_MEMBER_JSON\s*[\s\S]*?\s*END_ADD_MEMBER_JSON/i', '', $text) ?? $text;

        return trim($text);
    }

    private function extractActionJson(string $text): ?array
    {
        if (preg_match('/ACTION_JSON\s*(\{[\s\S]*?\})\s*END_ACTION_JSON/i', $text, $m)) {
            $data = json_decode($m[1], true);

            return is_array($data) ? $data : null;
        }

        if (preg_match('/ADD_MEMBER_JSON\s*(\{[\s\S]*?\})\s*END_ADD_MEMBER_JSON/i', $text, $m)) {
            $data = json_decode($m[1], true);
            if (is_array($data)) {
                $data['action'] = 'add_member';
                $data['confirm'] = $data['confirm'] ?? true;

                return $data;
            }
        }

        return null;
    }

    private function processActionBlock(string $rawResponse): ?array
    {
        $action = $this->extractActionJson($rawResponse);
        if (!$action || empty($action['action'])) {
            return null;
        }

        if (empty($action['confirm'])) {
            return ['success' => false, 'message' => 'Action not confirmed yet.'];
        }

        return match ($action['action']) {
            'add_member' => $this->actionAddMember($action),
            'update_member' => $this->actionUpdateMember($action),
            'mark_deceased' => $this->actionMarkDeceased($action),
            'mark_alive' => $this->actionMarkAlive($action),
            'delete_member' => $this->actionDeleteMember($action),
            'navigate' => $this->actionNavigate($action),
            default => ['success' => false, 'message' => 'Unknown action.'],
        };
    }

    private function userOwnsFamily(int $familyId): bool
    {
        return FamilyTree::where('id', $familyId)->where('ownerId', Auth::id())->exists();
    }

    private function userOwnsMember(int $memberId): ?Member
    {
        $member = Member::find($memberId);
        if (!$member || !$this->userOwnsFamily((int) $member->family_id)) {
            return null;
        }

        return $member;
    }

    private function actionAddMember(array $action): array
    {
        $familyId = (int) ($action['family_id'] ?? 0);
        if (!$familyId || !$this->userOwnsFamily($familyId)) {
            return ['success' => false, 'message' => __('messages.Unauthorized')];
        }

        $firstname = trim($action['firstname'] ?? '');
        $lastname = trim($action['lastname'] ?? '');
        if ($firstname === '' || $lastname === '') {
            return ['success' => false, 'message' => 'First and last name are required.'];
        }

        $type = (int) ($action['type'] ?? 1);
        $selfId = (int) ($action['self_id'] ?? 0);
        $parentId = $this->resolveParentId($selfId, $type);

        $death = isset($action['death']) ? (int) $action['death'] : 1;
        $deathdate = $action['deathdate'] ?? null;
        if ($death === 0 && empty($deathdate)) {
            return ['success' => false, 'message' => __('messages.Death date is required when the member is deceased.')];
        }
        if ($death === 1) {
            $deathdate = null;
        }

        $member = new Member();
        $member->family_id = $familyId;
        $member->parent_id = $parentId;
        $member->firstname = $firstname;
        $member->lastname = $lastname;
        $member->type = $type;
        $member->gender = (int) ($action['gender'] ?? 1);
        $member->generation = $action['generation'] ?? null;
        $member->death = $death;
        $member->birthdate = $action['birthdate'] ?? null;
        $member->deathdate = $deathdate;
        $member->email = $action['email'] ?? null;
        $member->mobile = $action['mobile'] ?? null;
        $member->bio = $action['bio'] ?? null;
        $member->village = $action['village'] ?? null;
        $member->birthplace = $action['birthplace'] ?? null;
        $member->profession = $action['profession'] ?? null;
        $member->company = $action['company'] ?? null;
        $member->interests = $action['interests'] ?? null;
        $member->home_town = $action['home_town'] ?? null;
        $member->school = $action['school'] ?? null;
        $member->save();

        if ($selfId !== 0 && $type === 4) {
            $existing = Member::find($selfId);
            if ($existing) {
                $existing->parent_id = $member->id;
                $existing->save();
            }
        }

        return [
            'success' => true,
            'action' => 'add_member',
            'message' => __('messages.Member added successfully.'),
            'member_added' => [
                'success' => true,
                'id' => $member->id,
                'name' => $member->firstname . ' ' . $member->lastname,
            ],
        ];
    }

    private function resolveParentId(int $selfId, int $type): int
    {
        if ($selfId === 0 || $type === 4) {
            return 0;
        }

        if ($type === 6) {
            $self = Member::find($selfId);

            return $self ? (int) $self->parent_id : $selfId;
        }

        if ($type === 7) {
            $self = Member::find($selfId);
            if ($self && $self->parent_id) {
                $parent = Member::find($self->parent_id);

                return $parent ? (int) $parent->parent_id : 0;
            }

            return 0;
        }

        if ($type === 8) {
            $self = Member::find($selfId);

            return $self ? (int) $self->parent_id : $selfId;
        }

        return $selfId;
    }

    private function actionUpdateMember(array $action): array
    {
        $member = $this->userOwnsMember((int) ($action['member_id'] ?? 0));
        if (!$member) {
            return ['success' => false, 'message' => __('messages.Unauthorized')];
        }

        $allowed = [
            'firstname', 'lastname', 'gender', 'birthdate', 'marriagedate', 'email', 'mobile', 'tel',
            'bio', 'village', 'birthplace', 'profession', 'company', 'interests', 'home_town', 'school',
            'background', 'business_info', 'facebook', 'twitter', 'instagram', 'site',
        ];

        foreach ($allowed as $field) {
            if (array_key_exists($field, $action) && $action[$field] !== null) {
                $member->{$field} = $action[$field];
            }
        }

        if (array_key_exists('death', $action)) {
            $fakeRequest = new Request([
                'death' => (int) $action['death'] === 1 ? '1' : null,
                'deathdate' => $action['deathdate'] ?? $member->deathdate,
            ]);
            $life = MemberLifeDateValidator::normalizeDates($fakeRequest);
            $member->death = $life['death'];
            $member->deathdate = $life['deathdate'];
        }

        $member->save();

        return [
            'success' => true,
            'action' => 'update_member',
            'message' => __('messages.Update information successfully'),
        ];
    }

    private function actionMarkDeceased(array $action): array
    {
        $member = $this->userOwnsMember((int) ($action['member_id'] ?? 0));
        if (!$member) {
            return ['success' => false, 'message' => __('messages.Unauthorized')];
        }

        $deathdate = $action['deathdate'] ?? null;
        if (empty($deathdate)) {
            return ['success' => false, 'message' => __('messages.Death date is required when the member is deceased.')];
        }

        $member->death = 0;
        $member->deathdate = $deathdate;
        $member->save();

        return [
            'success' => true,
            'action' => 'mark_deceased',
            'message' => $member->firstname . ' ' . $member->lastname . ' marked as deceased.',
        ];
    }

    private function actionMarkAlive(array $action): array
    {
        $member = $this->userOwnsMember((int) ($action['member_id'] ?? 0));
        if (!$member) {
            return ['success' => false, 'message' => __('messages.Unauthorized')];
        }

        $member->death = 1;
        $member->deathdate = null;
        $member->save();

        return [
            'success' => true,
            'action' => 'mark_alive',
            'message' => $member->firstname . ' ' . $member->lastname . ' marked as alive.',
        ];
    }

    private function actionDeleteMember(array $action): array
    {
        $member = $this->userOwnsMember((int) ($action['member_id'] ?? 0));
        if (!$member) {
            return ['success' => false, 'message' => __('messages.Unauthorized')];
        }

        $name = $member->firstname . ' ' . $member->lastname;
        $member->delete();

        return [
            'success' => true,
            'action' => 'delete_member',
            'message' => __('messages.Member deleted successfully.') . " ({$name})",
        ];
    }

    private function actionNavigate(array $action): array
    {
        $routes = [
            'dashboard' => 'user.dashboard',
            'familytree' => 'user.familytree',
            'familylisting' => 'user.familylisting',
            'deceased' => 'user.deceased',
            'messages' => 'user.messageboard',
            'messageto' => 'user.messageto',
            'sendmessage' => 'user.send.message',
            'photos' => 'user.photos',
            'blog' => 'user.blog',
            'import' => 'user.import',
            'export' => 'user.export',
            'memberships' => 'user.memberships',
            'profile' => 'user.profile',
            'settings' => 'user.setting',
        ];

        $page = strtolower((string) ($action['page'] ?? ''));
        if (!isset($routes[$page])) {
            return ['success' => false, 'message' => 'Unknown page.'];
        }

        return [
            'success' => true,
            'action' => 'navigate',
            'page' => $page,
            'url' => route($routes[$page], [], false),
            'message' => 'Opening page…',
        ];
    }

    /**
     * Navigate without calling the AI API (works when Cerebras is rate-limited).
     */
    private function tryQuickNavigation(string $message): ?array
    {
        $lower = strtolower(trim($message));

        $navPhrases = preg_match(
            '/\b(go to|take me to|open|show me|navigate to|bring me to|visit)\b/',
            $lower
        );
        $shortCommand = strlen($lower) <= 35;

        if (!$navPhrases && !$shortCommand) {
            return null;
        }

        $pageKeywords = [
            'messageto' => ['messages to you', 'inbox', 'received messages'],
            'sendmessage' => ['send message', 'compose message', 'email members'],
            'messages' => ['message board', 'messageboard', 'messages'],
            'photos' => ['photo upload', 'photos', 'photo', 'pictures', 'picture'],
            'blog' => ['blog'],
            'deceased' => ['deceased', 'death certificate', 'death certificates'],
            'dashboard' => ['dashboard', 'home'],
            'familytree' => ['family tree', 'familytree', 'members listing', 'members list'],
            'familylisting' => ['family listing', 'family list', 'families'],
            'import' => ['import'],
            'export' => ['export'],
            'memberships' => ['membership', 'memberships', 'plan'],
            'profile' => ['profile', 'my profile'],
            'settings' => ['settings', 'setting'],
        ];

        foreach ($pageKeywords as $page => $keywords) {
            foreach ($keywords as $keyword) {
                if (str_contains($lower, $keyword)) {
                    return $this->actionNavigate([
                        'action' => 'navigate',
                        'confirm' => true,
                        'page' => $page,
                    ]);
                }
            }
        }

        return null;
    }
}
