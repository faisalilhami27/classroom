<?php

namespace App\Http\Controllers;

use App\Events\NewChattingMessage;
use App\Http\Requests\ConversationRequest;
use App\Models\ChatRoom;
use App\Models\ConversationChatRoom;
use App\Models\FileConversationChat;
use App\Models\StudentClass;
use App\Models\StudentClassTransaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatRoomController extends Controller
{
  /**
   * get chat
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function getChat(Request $request)
  {
    $chatId = $request->chat_id;
    $chat = ChatRoom::with(['student', 'employee'])->where('id', $chatId)->first();
    $conversations = ConversationChatRoom::with('files')
      ->where('chat_id', $chatId);

    if (Auth::guard('employee')->check()) {
      $conversations->where('status_conversation_employee', 0);
    } else {
      $conversations->where('status_conversation_student', 0);
    }

    $conversations = $conversations->get();
    $this->updateStatusRead($chatId);
    return response()->json([
      'status' => 200,
      'conversation' => $conversations,
      'chat' => $chat,
    ]);
  }

  /**
   * update status read
   * @param $chatId
   */
  private function updateStatusRead($chatId)
  {
    $user = Auth::user();
    if (Auth::guard('employee')->check()) {
      ConversationChatRoom::where('receiver_employee', $user->employee_id)
        ->where('chat_id', $chatId)
        ->update(['status_read' => 1]);
    } else {
      ConversationChatRoom::where('receiver_student', $user->student_id)
        ->where('chat_id', $chatId)
        ->update(['status_read' => 1]);
    }
  }

  /**
   * get all chat
   */
  public function getAllChat()
  {
    $chats = $this->countChat();
    return response()->json(['status' => 200, 'count' => $chats]);
  }

  /**
   * count chat
   */
  private function countChat()
  {
    $user = Auth::user();
    if (Auth::guard('employee')->check()) {
      $chats = ConversationChatRoom::where('receiver_employee', $user->employee_id)
        ->where('status_read', 0)
        ->count();
    } else {
      $chats = ConversationChatRoom::where('receiver_student', $user->student_id)
        ->where('status_read', 0)
        ->count();
    }
    return $chats;
  }

  /**
   * check teacher or student
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function checkTeacherOrStudent(Request $request)
  {
    $user = Auth::user();
    $userId = $request->user_id;
    if (Auth::guard('employee')->check()) {
      $chat = ChatRoom::where('employee_id', $user->employee_id)
        ->where('student_id', $userId)
        ->first();
    } else {
      $chat = ChatRoom::where('student_id', $user->student_id)
        ->where('employee_id', $userId)
        ->first();
    }
    return response()->json(['status' => 200, 'chat' => $chat]);
  }

  /**
   * get teacher or student
   * @return \Illuminate\Http\JsonResponse
   */
  public function getTeacherOrStudent()
  {
    $data = [];
    if (Auth::guard('employee')->check()) {
      $persons = StudentClassTransaction::with(['student'])
        ->whereHas('studentClass', function ($query) {
          $query->where('school_year_id', activeSchoolYear()->id);
          $query->where('employee_id', Auth::user()->employee_id);
        })
        ->get();

      foreach ($persons as $person) {
        $data[] = [
          'id' => $person->student_id,
          'name' => $person->student->student_identity_number . ' - ' . $person->student->name
        ];
      }
    } else {
      $persons = StudentClass::with('employee')
        ->where('school_year_id', activeSchoolYear()->id)
        ->whereHas('classTransaction', function ($query) {
          $query->where('school_year_id', activeSchoolYear()->id);
          $query->where('student_id', Auth::user()->student_id);
        })
        ->get();

      foreach ($persons as $person) {
        $data[] = [
          'id' => $person->employee_id,
          'name' => $person->employee->employee_identity_number . ' - ' . $person->employee->name
        ];
      }
    }
    return response()->json(['status' => 200, 'person' => $data]);
  }

  /**
   * get chat by user
   */
  public function getChatByUser()
  {
    if (Auth::guard('employee')->check()) {
      $chats = ChatRoom::where('employee_id', Auth::user()->employee_id)->get();
    } else {
      $chats = ChatRoom::where('student_id', Auth::user()->student_id)->get();
    }
    return response()->json(['status' => 200, 'chat' => $chats]);
  }

  /**
   * list chat
   */
  public function listChat()
  {
    $user = Auth::user();
    $data = [];

    if (Auth::guard('employee')->check()) {
      $chats = ChatRoom::with('student')
        ->where('employee_id', $user->employee_id)
        ->where('status_delete_employee', 0)
        ->get();

      foreach ($chats as $chat) {
        $countConversation = $this->countChatUnread($chat->id);
        $conversation = ConversationChatRoom::where('chat_id', $chat->id)
          ->orderBy('id', 'desc')
          ->first();

        $data[] = [
          'id' => $chat->id,
          'photo' => (is_null($chat->student->photo)) ? null : $chat->student->photo,
          'name' => $chat->student->name,
          'color' => $chat->student->color,
          'student_id' => $chat->student_id,
          'employee_id' => $chat->employee_id,
          'message' => ($conversation->files->isEmpty()) ? $conversation->message : 'Mengirim file',
          'count' => $countConversation
        ];
      }
    } else {
      $chats = ChatRoom::with('employee')
        ->where('student_id', $user->student_id)
        ->where('status_delete_student', 0)
        ->get();

      foreach ($chats as $chat) {
        $countConversation = $this->countChatUnread($chat->id);
        $conversation = ConversationChatRoom::where('chat_id', $chat->id)
          ->orderBy('id', 'desc')
          ->first();

        $data[] = [
          'id' => $chat->id,
          'photo' => (is_null($chat->employee->photo)) ? null : $chat->employee->photo,
          'name' => $chat->employee->name,
          'color' => $chat->employee->color,
          'employee_id' => $chat->employee_id,
          'student_id' => $chat->student_id,
          'message' => ($conversation->files->isEmpty()) ? $conversation->message : 'Mengirim file',
          'count' => $countConversation
        ];
      }
    }
    return response()->json([
      'status' => 200,
      'chat' => $data,
    ]);
  }

  /**
   * count chat
   * @param $chatId
   * @return
   */
  private function countChatUnread($chatId)
  {
    $user = Auth::user();
    if (Auth::guard('employee')->check()) {
      $chats = ConversationChatRoom::where('status_read', 0)
        ->where('chat_id', $chatId)
        ->where('receiver_employee', $user->employee_id)
        ->count();
    } else {
      $chats = ConversationChatRoom::where('status_read', 0)
        ->where('chat_id', $chatId)
        ->where('receiver_student', $user->student_id)
        ->count();
    }
    return $chats;
  }

  /**
   * make or reply chat
   * @param ConversationRequest $request
   * @param ConversationChatRoom $conversation
   * @return \Illuminate\Http\JsonResponse
   * @throws \Pusher\PusherException
   */
  public function makeOrReplyChat(ConversationRequest $request, ConversationChatRoom $conversation)
  {
    $userId = $request->user_id;
    $message = htmlspecialchars($request->message);
    $type = $request->type;
    $files = $request->file('file');
    $data = $this->checkData($userId, $message, $type);
    $conversationChat = ConversationChatRoom::with('files')
      ->where('id', $data)
      ->first();

    /* if type is not text then store file */
    if ($type != 'text') {
      $this->storeFile($conversationChat->id, $files);
    }

    $chat = ConversationChatRoom::with('files')
      ->where('id', $data)
      ->first();

    if (Auth::guard('employee')->check()) {
      event(new NewChattingMessage($chat, $chat->chat->student_id));
    } else {
      event(new NewChattingMessage($chat, $chat->chat->employee_id));
    }

    /* call pusher configuration for push notification */
    $conversation->pusherConfig();
    return response()->json(['status' => 200, 'chat' => $chat]);
  }

  /**
   * check data
   * @param $userId
   * @param $message
   * @param $type
   * @return  |null |null
   */
  private function checkData($userId, $message, $type)
  {
    $insert = null;
    $user = Auth::user();

    if (Auth::guard('employee')->check()) {
      $employeeId = $user->employee_id;
      $studentId = $userId;
      $chat = ChatRoom::where('employee_id', $employeeId)
        ->where('student_id', $studentId)
        ->where('status_delete_employee', 0)
        ->first();
    } else {
      $studentId = $user->student_id;
      $employeeId = $userId;
      $chat = ChatRoom::where('student_id', $studentId)
        ->where('employee_id', $employeeId)
        ->where('status_delete_student', 0)
        ->first();
    }

    if (is_null($chat)) {
      $insert = ChatRoom::create([
        'student_id' => $studentId,
        'employee_id' => $employeeId,
        'status_delete_student' => 0,
        'status_delete_employee' => 0,
      ]);
      $conversation = $this->storeConversation($insert, $insert->id, $message, $userId, $type);
    } else {
      $conversation = $this->storeConversation($chat, $chat->id, $message, $userId, $type);
    }
    return $conversation->id;
  }

  /**
   * insert data to conversation table
   * @param $chat
   * @param $chatId
   * @param $message
   * @param $receiverId
   * @param $type
   * @return
   */
  private function storeConversation($chat, $chatId, $message, $receiverId, $type)
  {
    $user = Auth::user();
    if (Auth::guard('employee')->check()) {
      $employeeIdForConversation = $user->employee_id;
      $receiverStudent = $receiverId;
      $receiverEmployee = null;
      $studentIdForConversation = null;
      if (optional($chat)->status_delete_student == 1) {
        $chat->update(['status_delete_student' => 0]);
      }
    } else {
      $studentIdForConversation = $user->student_id;
      $receiverEmployee = $receiverId;
      $employeeIdForConversation = null;
      $receiverStudent = null;
      if (optional($chat)->status_delete_employee == 1) {
        $chat->update(['status_delete_employee' => 0]);
      }
    }

    return $chat->conversation()->create([
      'chat_id' => $chatId,
      'employee_id' => $employeeIdForConversation,
      'student_id' => $studentIdForConversation,
      'message' => $message,
      'type' => $type,
      'status_read' => 0,
      'receiver_employee' => $receiverEmployee,
      'receiver_student' => $receiverStudent,
      'status_conversation_student' => 0,
      'status_conversation_employee' => 0,
    ]);
  }

  /**
   * store file
   * @param $id
   * @param $files
   */
  private function storeFile($id, $files)
  {
    $data = [];
    foreach ($files as $file) {
      $filename = $file->getClientOriginalName();
      $replaceFileName = pathinfo($filename, PATHINFO_FILENAME) . '_' . time() . '.' . $file->getClientOriginalExtension();
      $data[] = [
        'conversation_id' => $id,
        'file' => $file->storeAs('chat', $replaceFileName),
        'filename' => $filename,
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
      ];
    }
    FileConversationChat::insert($data);
  }

  /**
   * delete chat
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function destroyChat(Request $request)
  {
    $chatId = $request->chat_id;
    $user = Auth::user();

    if (Auth::guard('employee')->check()) {
      $userId = $user->employee_id;
      $data = ChatRoom::where('id', $chatId)
        ->where('employee_id', $userId)
        ->first();
      $data->update(['status_delete_employee' => 1]);
      $data->conversation()->update(['status_conversation_employee' => 1]);
    } else {
      $userId = $user->student_id;
      $data = ChatRoom::where('id', $chatId)
        ->where('student_id', $userId)
        ->first();
      $data->update(['status_delete_student' => 1]);
      $data->conversation()->update(['status_conversation_student' => 1]);
    }

    if ($data->status_delete_student == 1 && $data->status_delete_employee == 1) {
      $data->delete();
      foreach ($data->conversation()->get() as $item) {
        FileConversationChat::where('conversation_id', $item->id)->delete();
      }
      $data->conversation()->delete();
    }

    if ($data) {
      return response()->json(['status' => 200]);
    } else {
      return response()->json(['status' => 500, 'message' => 'Terjadi kesalahan pada server']);
    }
  }
}
