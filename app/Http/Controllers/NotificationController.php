<?php

namespace App\Http\Controllers;

use App\Constants\HeartStatus;

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
        $count = $this->user->heartsToMe()->where([
            'status'    => HeartStatus::PENDING,
            'have_read' => false,
        ])->count();

        return response()->json(['count' => $count]);
    }
}
