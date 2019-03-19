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
        $pending = ' AND status = ' . HeartStatus::PENDING;
        $haveNotRead = ' AND have_read = 0';

        $heartIds = 'heart_id IN (' . implode(',', $this->user->heartsToMe()->orWhere(function () {
            return $this->user->heartsToPartner()->get();
        })
            ->where('status', HeartStatus::APPROVED)
            ->pluck('id')
            ->toArray()) . ')';

        $hearts   = 'SELECT COUNT(hearts.id) FROM hearts WHERE ' . $me . $pending . $haveNotRead;
        $messages = 'SELECT COUNT(messages.id) FROM messages WHERE ' . $heartIds . $haveNotRead;

        $count = DB::select('SELECT (' . $hearts . ') AS hearts, (' . $messages . ') AS messages')[0];

        return response()->json(['count' => $count]);
    }
}
