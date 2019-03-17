<?php

namespace App\Http\Controllers;

use App\Constants\HeartStatus;
use DB;

class NotificationController extends CustomController
{
    public function index()
    {
        $hearts = $this->user->heartsToMe()->where([
            'status'    => HeartStatus::PENDING,
            'have_read' => false,
        ])->with('user', 'book')->get();

        $transformer = new \App\Http\Transformer\HeartTransformer;

        $notifications = $this->transform->collection($hearts, $transformer, ['user', 'book']);

        return response()->json($notifications);
    }

    public function count()
    {
        $me = 'heart_user_id = ' . $this->user->id;
        $status = ' AND status = ' . HeartStatus::PENDING;
        $haveRead = ' AND have_read = 0';

        $heartIds = 'heart_id IN (' . implode(',', \App\Heart::where('user_id', $this->user->id)
            ->orWhere('heart_user_id', $this->user->id)
            ->where('status', HeartStatus::APPROVED)
            ->pluck('id')
            ->toArray()) . ')';

        $hearts   = 'SELECT COUNT(hearts.id) FROM hearts WHERE ' . $me . $status . $haveRead;
        $messages = 'SELECT COUNT(messages.id) FROM messages WHERE ' . $heartIds . $haveRead;

        $count = DB::select('SELECT (' . $hearts . ') AS hearts, (' . $messages . ') AS messages')[0];

        return response()->json(['count' => $count]);
    }
}
