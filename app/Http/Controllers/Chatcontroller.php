<?php

namespace App\Http\Controllers;

use App\Events\SendMessage;
use App\Models\group_user;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Events\MessageNotification;
use App\Models\Group;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ChatController extends Controller
{
    public function index()
{
    $userId = Auth::id();

    $users = User::where('id', '!=', $userId)
        ->select('id', 'name', 'profile_picture','last_seen')
        ->get()
        ->map(function ($user) {
            $user->type = 'user';
            return $user;
        });

    
    $groups = Group::whereHas('users', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
        ->orWhere('created_by', $userId) 
        ->select('id', 'name','group_img')
        ->get()
        ->map(function ($group) {
            $group->type = 'group';
            return $group;
        });


    $chatList = $users->merge($groups);

    $unreadCounts = Message::where('receiver_id', $userId)
    ->where('is_read', false)
    ->selectRaw('sender_id, COUNT(*) as unread_count')
    ->groupBy('sender_id')
    ->pluck('unread_count', 'sender_id')
    ->toArray();

  

    return view('chat', compact('chatList','unreadCounts'));
}

public function memberGroup(Request $request)
{
    $groupUser = group_user::where('group_id', $request->group_id)->first();

    if (!$groupUser) {
        return response()->json(['members' => []]);
    }

    $members = group_user::where('group_id', $groupUser->group_id)
        ->with('user:id,name,email') 
        ->get();

    return response()->json(['members' => $members]);
}


   
    public function sendMessage(Request $request)
{
    $validated = $request->validate([
        'receiver_id' => 'required', 
        'message' => 'nullable|string',
        'file' => 'nullable|file|max:10240',
    ]);

    $senderId = Auth::id();
    $receiverId = $validated['receiver_id'];
    $messageText = $validated['message'] ?? '';
    $filePath = null;
    $fileType = null;

    if ($request->hasFile('file')) {
        $file = $request->file('file');
        $filePath = $file->store('chat_files', 'public');
        $fileType = $file->getClientMimeType();
        $messageText = empty($messageText) ? $file->getClientOriginalName() : $messageText . " || " . $file->getClientOriginalName();
    }

    $isGroup = Group::where('id', $receiverId)->exists();
    $groupId = $isGroup ? $receiverId : null; 
    $encryptedMessage = Crypt::encryptString($messageText);
    $message = new Message();
    $message->sender_id = $senderId;
    $message->receiver_id = $isGroup ? null : $receiverId; 
    $message->group_id = $groupId; 
    $message->message = $encryptedMessage;
    $message->file_path = $filePath;
    $message->file_type = $fileType;
    $message->save();


    broadcast(new SendMessage($message, $isGroup, $groupId));

    if (!$isGroup) {
        broadcast(new MessageNotification($receiverId, $senderId, Auth::user()->name));
    }

    return response()->json([
        'message' => $messageText,  
        'file_url' => $filePath ? asset('storage/' . $filePath) : null,
        'file_type' => $fileType,
        'sender_name' => Auth::user()->name,
        'receiver_name' => User::find($receiverId)->name ?? 'Unknown',
        'time' => now()->format('h:i'),
    ]);
}

public function fetchMessages(Request $request)
{
    $request->validate([
        'receiver_id' => 'required'
    ]);

    $userId = Auth::id();
    $receiverId = $request->receiver_id;

    $isGroup = Group::where('id', $receiverId)->exists();

    if ($isGroup) {
        $messages = Message::with('sender')
            ->where('group_id', $receiverId)
            ->orderBy('created_at', 'asc')
            ->get();
    } else {
        $messages = Message::with('sender', 'receiver')
            ->where(function ($query) use ($userId, $receiverId) {
                $query->where('sender_id', $userId)->where('receiver_id', $receiverId);
            })
            ->orWhere(function ($query) use ($userId, $receiverId) {
                $query->where('sender_id', $receiverId)->where('receiver_id', $userId);
            })
            ->orderBy('created_at', 'asc')
            ->get();

        Message::where('receiver_id', $userId)
            ->where('sender_id', $receiverId)
            ->where('is_read', false)
            ->update(['is_read' => true]);
    }

    $messages = $messages->map(function ($msg) {
        $decryptedMessage = null;

        if (!empty($msg->message)) {
            try {
                $decryptedMessage = Crypt::decryptString($msg->message);
            } catch (\Exception $e) {
                $decryptedMessage = $msg->message; 
            }
        }

        return [
            'id' => $msg->id,
            'sender_id' => $msg->sender_id,
            'receiver_id' => $msg->receiver_id,
            'group_id' => $msg->group_id,
            'file_url' => $msg->file_path ? asset("storage/{$msg->file_path}") : null,
            'fileType' => $msg->file_type,
            'message' => $decryptedMessage,
            'original_file_name' => strpos($decryptedMessage, " || ") !== false 
                ? explode(" || ", $decryptedMessage)[1] 
                : $decryptedMessage,
            'sender' => $msg->sender ? ['name' => $msg->sender->name] : null,
            'receiver' => $msg->receiver ? ['name' => $msg->receiver->name] : null,
            'time' => $msg->created_at ? Carbon::parse($msg->created_at)->format('h:i') : 'Time not available',
        ];
    });

    return response()->json(['messages' => $messages]);
}


public function markMessagesAsRead(Request $request)
{
    $request->validate([
        'sender_id' => 'required|exists:users,id'
    ]);

    Message::where('sender_id', $request->sender_id)
        ->where('receiver_id', Auth::id())
        ->where('is_read', false)
        ->update(['is_read' => true]);

    return response()->json(['message' => 'Messages marked as read']);
}



}
