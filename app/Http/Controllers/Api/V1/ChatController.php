<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    /**
     * Get list of conversations with latest message.
     */
    public function index()
    {
        $userId = Auth::id();

        $subQuery = Message::where('sender_id', $userId)
            ->orWhere('receiver_id', $userId)
            ->select('id', DB::raw('CASE WHEN sender_id = ' . $userId . ' THEN receiver_id ELSE sender_id END as contact_id'), 'content', 'created_at')
            ->orderBy('created_at', 'desc');

        $latestMessages = DB::table(DB::raw("({$subQuery->toSql()}) as msgs"))
            ->mergeBindings($subQuery->getQuery())
            ->groupBy('contact_id')
            ->select('contact_id', DB::raw('MAX(id) as message_id'));

        $conversations = Message::whereIn('id', $latestMessages->pluck('message_id'))
            ->with(['sender', 'receiver'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($message) use ($userId) {
                $contact = $message->sender_id === $userId ? $message->receiver : $message->sender;
                return [
                    'contact' => [
                        'id' => $contact->id,
                        'name' => $contact->name,
                        'photo_url' => $contact->photo_url,
                    ],
                    'last_message' => [
                        'content' => $message->content,
                        'type' => $message->type ?? 'text',
                        'created_at' => $message->created_at->toIso8601String(),
                        'unread' => $message->receiver_id === $userId && is_null($message->read_at),
                    ],
                ];
            });

        return response()->json([
            'status' => 'success',
            'data' => $conversations,
        ]);
    }

    /**
     * Get chat history with a specific explorer.
     */
    public function show($userId)
    {
        $myId = Auth::id();

        // Mark messages as read
        Message::where('sender_id', $userId)
            ->where('receiver_id', $myId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $messages = Message::where(function ($query) use ($myId, $userId) {
                $query->where('sender_id', $myId)->where('receiver_id', $userId);
            })
            ->orWhere(function ($query) use ($myId, $userId) {
                $query->where('sender_id', $userId)->where('receiver_id', $myId);
            })
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($msg) {
                $replyData = null;
                if ($msg->reply_to_id) {
                    $replyMsg = Message::find($msg->reply_to_id);
                    if ($replyMsg) {
                        $replyData = [
                            'id' => $replyMsg->id,
                            'content' => $replyMsg->content,
                            'type' => $replyMsg->type ?? 'text',
                        ];
                    }
                }
                return [
                    'id' => $msg->id,
                    'sender_id' => $msg->sender_id,
                    'receiver_id' => $msg->receiver_id,
                    'content' => $msg->content,
                    'type' => $msg->type ?? 'text',
                    'media_url' => $msg->media_url,
                    'reply_to' => $replyData,
                    'read_at' => $msg->read_at?->toIso8601String(),
                    'created_at' => $msg->created_at->toIso8601String(),
                ];
            });

        $contact = User::find($userId);

        return response()->json([
            'status' => 'success',
            'data' => [
                'contact' => [
                    'id' => $contact?->id,
                    'name' => $contact?->name,
                    'photo_url' => $contact?->photo_url,
                ],
                'messages' => $messages,
            ]
        ]);
    }

    /**
     * Send a message (text or media) to a specific explorer.
     */
    public function store(Request $request, $userId)
    {
        $request->validate([
            'content' => 'nullable|string|max:5000',
            'type' => 'nullable|string|max:50',
            'media_url' => 'nullable|string|max:2048',
            'reply_to_id' => 'nullable|integer',
            'file' => 'nullable|file|max:20480',
        ]);

        $myId = Auth::id();
        $type = $request->input('type', 'text');
        $content = $request->input('content') ?? '';
        $mediaUrl = $request->input('media_url');

        // Handle file upload
        $uploadedFile = $request->file('file');
        if ($uploadedFile) {
            $path = $uploadedFile->store('chat_media', 'public');
            $mediaUrl = url('/api/v1/media/' . $path);
            if (empty($content)) {
                $content = $uploadedFile->getClientOriginalName();
            }
            if ($type === 'text') {
                $mime = $uploadedFile->getMimeType();
                if (str_contains($mime, 'image')) $type = 'image';
                elseif (str_contains($mime, 'video')) $type = 'video';
                else $type = 'file';
            }
        }

        $message = Message::create([
            'sender_id' => $myId,
            'receiver_id' => $userId,
            'content' => $content,
            'type' => $type,
            'media_url' => $mediaUrl,
            'reply_to_id' => $request->input('reply_to_id'),
        ]);

        try {
            \App\Events\MessageSent::dispatch($message);
        } catch (\Throwable $e) {}

        $replyData = null;
        if ($message->reply_to_id) {
            $replyMsg = Message::find($message->reply_to_id);
            if ($replyMsg) {
                $replyData = [
                    'id' => $replyMsg->id,
                    'content' => $replyMsg->content,
                    'type' => $replyMsg->type ?? 'text',
                ];
            }
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'id' => $message->id,
                'sender_id' => $message->sender_id,
                'receiver_id' => $message->receiver_id,
                'content' => $message->content,
                'type' => $message->type,
                'media_url' => $message->media_url,
                'reply_to' => $replyData,
                'created_at' => $message->created_at->toIso8601String(),
            ]
        ]);
    }
}
