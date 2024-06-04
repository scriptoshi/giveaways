<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\Request;

class MessagesController extends Controller
{



    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Request $request, Message $message)
    {
        $request->validate([
            'message' => 'nullable|string',
            //'rating' => 'nullable|numeric'
        ]);
        $message->message = $request->message;
        //$message->rating = $request->rating;
        $message->is_reply = false;
        $message->save();
        return back();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function reply(Request $request, Message $message)
    {
        $request->validate([
            'message' => 'nullable|string',
            //'rating' => 'nullable|numeric'
        ]);
        $message->msg()->create([
            'user_id' => $request->user()->id,
            'message' => $request->message,
            //'rating' => $request->rating,
            'is_reply' => true
        ]);
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy(Request $request, Message $message)
    {
        $this->authorize('delete', $message);
        $message->delete();
        return back();;
    }
}
