<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Giveaway extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'project_id' => $this->project_id,
            'uuid' => $this->uuid,
            'slug' => $this->slug,
            'brief' => $this->brief,
            'duration' => $this->duration,
            'period' => $this->period,
            'starts_at' => $this->starts_at,
            'ends_at' => $this->ends_at,
            'winner_selected_at' => $this->winner_selected_at,
            'prize' => $this->prize * 1,
            'totalPrize' => $this->prize * $this->num_winners * 2,
            'fee' => $this->fee * 1,
            'gas' => $this->sleep * 1,
            'gas_balance' => $this->gas_balance * 1,
            'symbol' => $this->symbol,
            'hash' => $this->hash,
            'status' => $this->status,
            'errors' => $this->errors,
            'chainId' => $this->chainId,
            'account' => $this->account,
            'num_winners' => $this->num_winners,
            'num_claimed' => $this->num_claimed,
            'type' => $this->type,
            'draw_size' => $this->draw_size,
            'live' => $this->live,
            'ref_paid' => $this->ref_paid,
            'is_topup' => $this->is_topup,
            'paid' => $this->paid * 1,
            'summary' => value(function () {
                $prize = $this->prize * 1;
                $total = $this->prize * $this->num_winners * 2;
                $tasks = $this->totalTasks ?? 0;
                return  "Total {$total} USDT | {$this->num_winners} winners | {$prize} USDT each | {$tasks} Tasks";
            }),
            'short_summary' => value(function () {
                $total = $this->prize * $this->num_winners * 2;
                return  "{$total} USDT | {$this->num_winners} WINS";
            }),
            'startTime' => $this->starts_at,
            'startTimeFormatted' => $this->starts_at->toDateTimeString(),
            'startTimeTs' => $this->starts_at->timestamp,
            'hasStarted' => now()->gt($this->starts_at),
            'endTime' => $this->ends_at,
            'endTimeFormatted' => $this->ends_at->toDateTimeString(),
            'hasEnded' =>  now()->gt($this->ends_at),
            'isToday' => $this->starts_at->isToday(),
            'timeAgo' => $this->starts_at->diffForHumans(),
            'endTimeTs' => $this->ends_at->timestamp,
            'totalParticipants' => $this->totalParticipants ?? 0,
            'created_at' => $this->created_at->toDateTimeString(),
            'project' => new Project($this->whenLoaded('project')),
            'questers' => Quester::collection($this->whenLoaded('questers')),
        ];
    }
}
