<?php

namespace App\Http\Controllers;

use App\Events\NewChattingMessage;
use App\Models\ChatRoom;
use App\Models\ConversationChatRoom;
use App\Models\StudentClass;
use App\Models\StudentClassTransaction;
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
    $chat = ChatRoom::find($chatId);
    $conversations = ConversationChatRoom::where('chat_id', $chatId)->get();
    return response()->json([
      'status' => 200,
      'conversation' => $conversations,
      'chat' => $chat
    ]);
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
   * list chat
   */
  public function listChat()
  {
    $user = Auth::user();
    $data = [];

    if (Auth::guard('employee')->check()) {
      $chats = ChatRoom::with('student')
        ->where('employee_id', $user->employee_id)
        ->get();

      foreach ($chats as $chat) {
        $conversation = ConversationChatRoom::where('chat_id', $chat->id)
          ->orderBy('id', 'desc')
          ->first();

        $data[] = [
          'id' => $chat->id,
          'photo' => (is_null($chat->student->photo)) ? null : $chat->student->photo,
          'name' => $chat->student->name,
          'message' => $conversation->message
        ];
      }
    } else {
      $chats = ChatRoom::with('employee')
        ->where('student_id', $user->student_id)
        ->get();

      foreach ($chats as $chat) {
        $conversation = ConversationChatRoom::where('chat_id', $chat->id)
          ->orderBy('id', 'desc')
          ->first();

        $data[] = [
          'id' => $chat->id,
          'photo' => (is_null($chat->employee->photo)) ? null : $chat->employee->photo,
          'name' => $chat->employee->name,
          'message' => $conversation->message
        ];
      }
    }
    return response()->json(['status' => 200, 'chat' => $data]);
  }

  /**
   * make or reply chat
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function makeOrReplyChat(Request $request)
  {
    $userId = $request->user_id;
    $message = $request->message;
    $data = $this->checkData($userId, $message);
    $chat = ConversationChatRoom::find($data);
    if (Auth::guard('employee')->check()) {
      event(new NewChattingMessage($chat, $chat->chat->student_id));
    } else {
      event(new NewChattingMessage($chat, $chat->chat->employee_id));
    }
    return response()->json(['status' => 200, 'chat' => $chat]);
  }

  /**
   * check data
   * @param $userId
   * @param $message
   * @return  |null |null
   */
  private function checkData($userId, $message)
  {
    $insert = null;
    $user = Auth::user();

    if (Auth::guard('employee')->check()) {
      $studentIdForConversation = null;
      $employeeId = $user->employee_id;
      $studentId = $userId;
      $chat = ChatRoom::where('employee_id', $employeeId)
        ->where('student_id', $studentId)
        ->first();
    } else {
      $employeeIdForConversation = null;
      $studentId = $user->student_id;
      $employeeId = $userId;
      $chat = ChatRoom::where('student_id', $studentId)
        ->where('employee_id', $employeeId)
        ->first();
    }

    if (is_null($chat)) {
      $insert = ChatRoom::create([
        'student_id' => $studentId,
        'employee_id' => $employeeId
      ]);
      $conversation = $this->storeConversation($insert, $insert->id, $message);
    } else {
      $conversation = $this->storeConversation($chat, $chat->id, $message);
    }
    return $conversation->id;
  }

  /**
   * insert data to conversation table
   * @param $chat
   * @param $chatId
   * @param $message
   * @return
   */
  private function storeConversation($chat, $chatId, $message)
  {
    $user = Auth::user();
    if (Auth::guard('employee')->check()) {
      $employeeIdForConversation = $user->employee_id;
      $studentIdForConversation = null;
    } else {
      $studentIdForConversation = $user->student_id;
      $employeeIdForConversation = null;
    }

    return $chat->conversation()->create([
      'chat_id' => $chatId,
      'employee_id' => $employeeIdForConversation,
      'student_id' => $studentIdForConversation,
      'message' => $message
    ]);
  }
}
