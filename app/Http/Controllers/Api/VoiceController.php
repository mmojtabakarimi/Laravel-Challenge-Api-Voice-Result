<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\VoiceRequest;
use App\Models\Question;
use App\Models\Voice;
use Illuminate\Http\Request;

class VoiceController extends Controller
{
    public function voice(VoiceRequest $request)
    {

        $question = Question::findOrFail($request->post('question_id'));

        abort_if($question->user_id == auth()->id(),
            500,
            'The user is not allowed to vote to your question'
        );

        $voice = Voice::firstOrCreate(
            ['user_id' => auth()->id(),
                'question_id' => $request->post('question_id')]
            ,
            [
                'value' => $request->post('value')]
        );

        if ($voice->wasRecentlyCreated) {
            return ([
                'message' => 'Voting completed successfully'
            ]);
        }

        if ($voice->value === $request->post('value')) {
            abort(
                500,
                'The user is not allowed to vote more than once'
            );
        } else {
            $voice->update([
                'value' => $request->post('value')
            ]);
            return response()->json([
                'message' => 'update your voice'
            ], 201);
        }

    }
}
