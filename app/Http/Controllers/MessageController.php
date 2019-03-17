<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreMessageRequest;
use App\Http\Transformer\MessageTransformer;
use App\Heart;
use App\Message;

class MessageController extends CustomController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $heart = Heart::findOrFail($request->heartId);

        $messagesRaw = $heart->messages;

        $messages = $this->transform->collection($messagesRaw, new MessageTransformer);

        return response()->json($messages, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreMessageRequest $request)
    {
        $heart = Heart::findOrFail($request->heartId);

        if (!in_array($this->user->id, $heart->only('user_id', 'heart_user_id'))) {
            throw new \Exception('user_does_not_belong_to_heart', 403);
        }

        $messageRaw = Message::create(array_merge(
            $request->all(),
            [
                'heart_id' => $heart->id,
                'user_id'  => $this->user->id,
            ]
        ));

        $message = $this->transform->item($messageRaw, new MessageTransformer);

        return response()->json($message, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $messageRaw = Message::where('id', $id)->where('user_id', $this->user->id)->firstOrFail();

        $messageRaw->update($request->all());

        $message = $this->transform->item($messageRaw, new MessageTransformer);

        return response()->json($message, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $message = Message::where('id', $id)->where('user_id', $this->user->id)->firstOrFail();

        $message->delete();

        return response()->json([], 200);
    }
}
