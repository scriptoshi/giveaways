<?php

namespace App\Http\Controllers;

use App\Enums\QuesterStatus;
use App\Enums\QuestStatus;
use App\Enums\QuestType;
use App\Enums\TaskStatus;
use App\Http\Controllers\Controller;
use App\Models\Giveaway;
use App\Models\Task;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Models\Quest;
use App\Support\Galxe;
use App\Support\Utils;
use Illuminate\Http\Request;

class TasksController extends Controller
{

    /**
     * Store a newly created resource in storage.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function check(Request $request, Quest $quest)
    {
        $quest->load(['giveaway']);
        if ($quest->type == QuestType::TWEET && $quest->groupId === 'comment') {
            $request->validate(['response' => 'url|required']);
        }
        $quester =  $quest->giveaway->questers()->firstOrCreate([
            'user_id' => $request->user()->id,
            'giveaway_id' =>  $quest->giveaway->id,
        ], [
            'percent' => 0,
            'qid' => Utils::uniqidID(16),
            'pump' => 0,
            'status' => QuesterStatus::PENDING,
            'comment' => '',
            'completed_at' => null,
            'last_pump_at' => null,
        ]);
        $task = Task::query()->firstOrCreate([
            'giveaway_id' => $quest->giveaway_id,
            'quest_id' => $quest->id,
            'quester_id' => $quester->id,
            'user_id' => $request->user()->id,
            'type' =>  $quest->type,
        ], [
            'status' =>  TaskStatus::PENDING,
            'gas' => 0,
            'response' => $request->response,
        ]);
        // verify contribution
        if ($task->status == TaskStatus::COMPLETE) return back()->with('message', __("Task was marked as complete :timeAgo", ['timeAgo' => $task->updated_at->diffForHumans()]));
        $task->load(['giveaway.project.launchpad', 'quest']);
        $verified = $task->type->verifier()->verify($task);
        if (!$verified) {
            return back()->with('error', __("We could not verify this task was completed. Give it a few minutes or contact support"));
        }
        $task->status =  TaskStatus::COMPLETE;
        $task->gas = $quest->gas;
        $task->save();
        $giveaway = $task->giveaway;
        if ($giveaway->gas_balance >= $quest->gas) {
            $giveaway->gas_balance  -=  $quest->gas;
            $giveaway->save();
            $quester->gas += $quest->gas;
            $quester->save();
        }
        return back()->with('message', __("Task has been marked as complete"));
    }

    /**
     * Store a newly created resource in storage.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function checkAll(Request $request, Giveaway $giveaway)
    {
        $giveaway->load([
            'quests' => fn (HasMany $q) => $q->where('status',  QuestStatus::ACTIVE)
        ]);
        $quester =  $giveaway->questers()->firstOrCreate([
            'user_id' => $request->user()->id,
            'giveaway_id' =>  $giveaway->id,
        ], [
            'percent' => 0,
            'qid' => Utils::uniqidID(16),
            'pump' => 0,
            'gas' => 0,
            'address' => null,
            'status' => QuesterStatus::PENDING,
            'comment' => '',
            'completed_at' => null,
            'last_pump_at' => null,
        ]);
        if ($quester->status == QuesterStatus::COMPLETED) {
            if ($giveaway->galxe) {
                Galxe::giveaway($quester);
            }
            return back()->with('message', __("All tasks marked as completed :timeAgo", ['timeAgo' => $quester->completed_at->diffForHumans()]));
        }

        foreach ($giveaway->quests as $quest) {
            $task = Task::query()->firstOrCreate([
                'giveaway_id' => $quest->giveaway_id,
                'quest_id' => $quest->id,
                'quester_id' => $quester->id,
                'user_id' => $request->user()->id,
                'type' =>  $quest->type,
            ], [
                'status' =>  TaskStatus::PENDING,
                'gas' => 0
            ]);
            // verify contribution
            if ($task->status == TaskStatus::COMPLETE) continue;
            $task->load(['giveaway.project.launchpad', 'quest']);
            $verified = $task->type->verifier()->verify($task);
            if (!$verified) {
                return back()->with('error', __("Some tasks are still incomplete"));
            }
            $task->status =  TaskStatus::COMPLETE;
            $task->gas = $quest->gas;
            $task->save();
            $giveaway = $task->giveaway;
            if ($giveaway->gas_balance  >=  $quest->gas) {
                $giveaway->gas_balance  -=  $quest->gas;
                $giveaway->save();
                $quester->gas += $quest->gas;
                $quester->save();
            }
        }
        $quester->status = QuesterStatus::COMPLETED;
        $quester->completed_at = now();
        $quester->save();
        $quester->load('giveaway');
        if ($giveaway->galxe) {
            Galxe::giveaway($quester);
        }
        return back()->with('message', __("All Tasks completed. Your GAS Token was allocated"));
    }
}
