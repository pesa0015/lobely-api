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
        $notMe = ' AND user_id != ' . $this->user->id;

        $approvedHeartsIds = 'SELECT id FROM hearts WHERE'
            . ' heart_user_id = ' . $this->user->id
            . ' OR user_id = ' . $this->user->id
            . ' AND status = ' . HeartStatus::APPROVED;

        $heartIds = 'heart_id IN (' . $approvedHeartsIds . ')';

        $heartsCount   = 'COUNT(hearts.id)';
        $messagesCount = 'COUNT(DISTINCT(messages.heart_id))';

        $hearts   = 'SELECT ' . $heartsCount . ' FROM hearts WHERE ' . $me . $pending . $haveNotRead;
        $messages = 'SELECT ' . $messagesCount . ' FROM messages WHERE ' . $heartIds . $haveNotRead . $notMe;

        $count = DB::select('SELECT (' . $hearts . ') AS hearts, (' . $messages . ') AS messages')[0];

        return response()->json(['count' => $count]);
    }
}
