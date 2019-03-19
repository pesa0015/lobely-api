<?php

namespace App\Http\Controllers;

use App\Http\Transformer\HeartTransformer;

class ChatController extends CustomController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $messagesRaw = $this->user->heartsToMe()->orWhere(function () {
            return $this->user->heartsToPartner();
        })->with(['user', 'messages' => function ($query) {
            return $query->orderBy('id', 'DESC')->take(1)->get();
        }])->get();

        $messages = $this->transform->collection($messagesRaw, new HeartTransformer, ['user', 'messages']);

        return response()->json($messages);
    }
}
