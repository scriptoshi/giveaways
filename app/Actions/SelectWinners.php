<?php

namespace App\Actions;

use App\Enums\GiveawayType;
use App\Enums\QuesterStatus;
use App\Models\Giveaway;
use App\Models\Quester;
use Illuminate\Support\Collection;

class SelectWinners
{
    private static function update(Collection $gvs, QuesterStatus $status)
    {
        Quester::query()
            ->whereIn('id', $gvs->pluck('id')->all())
            ->update(['status' => $status]);
    }

    public function selectWinnerFor(Giveaway $giveaway)
    {
        if ($giveaway->winner_selected_at) return;
        $drawn =  $giveaway->questers()
            ->where('status', QuesterStatus::COMPLETED)
            ->inRandomOrder()
            ->take($giveaway->num_winners)
            ->get();

        if ($giveaway->type == GiveawayType::DRAW) {
            static::update($drawn, QuesterStatus::WINNER);
            $giveaway->winner_selected_at = now();
            $giveaway->save();
            return;
        }
        if ($giveaway->type == GiveawayType::FCFS) {
            $winners =  $giveaway->questers()
                ->where('status', QuesterStatus::COMPLETED)
                ->latest('completed_at')
                ->take($giveaway->num_winners)
                ->get();
            static::update($winners, QuesterStatus::WINNER);
            $giveaway->winner_selected_at = now();
            $giveaway->save();
            return;
        }

        if ($giveaway->type == GiveawayType::DRAW_FCFS) {
            $take = $giveaway->num_winners > $giveaway->draw_size ? $giveaway->num_winners * 5 : $giveaway->draw_size;
            $selection =  $giveaway->questers()
                ->where('status', QuesterStatus::COMPLETED)
                ->latest('completed_at')
                ->take($take)
                ->get();
            static::update($selection, QuesterStatus::DRAWN);
            $winners = $giveaway->questers()
                ->where('status', QuesterStatus::DRAWN)
                ->inRandomOrder()
                ->take($giveaway->num_winners)
                ->get();
            static::update($winners, QuesterStatus::WINNER);
            $giveaway->winner_selected_at = now();
            $giveaway->save();
            return;
        }

        if ($giveaway->type == GiveawayType::LEADERBOARD) {
            $winners =  $giveaway->questers()
                ->withSum('pumps as totalSleep', 'weight')
                ->where('status', QuesterStatus::COMPLETED)
                ->latest('totalSleep')
                ->take($giveaway->num_winners)
                ->get();
            static::update($winners, QuesterStatus::WINNER);
            $giveaway->winner_selected_at = now();
            $giveaway->save();
            return;
        }

        if ($giveaway->type == GiveawayType::DRAW_LEADERBOARD) {
            $take = $giveaway->num_winners > $giveaway->draw_size ? $giveaway->num_winners * 5 : $giveaway->draw_size;
            $selection =  $giveaway->questers()
                ->withSum('pumps as totalSleep', 'weight')
                ->where('status', QuesterStatus::COMPLETED)
                ->latest('totalSleep')
                ->take($take)
                ->get();
            static::update($selection, QuesterStatus::DRAWN);
            $winners = $giveaway->questers()
                ->where('status', QuesterStatus::DRAWN)
                ->inRandomOrder()
                ->take($giveaway->num_winners)
                ->get();
            static::update($winners, QuesterStatus::WINNER);
            $giveaway->winner_selected_at = now();
            $giveaway->save();
            return;
        }

        if ($giveaway->type == GiveawayType::FCFS_LEADERBOARD) {
            $take = $giveaway->num_winners > $giveaway->draw_size ? $giveaway->num_winners * 5 : $giveaway->draw_size;
            $selection =  $giveaway->questers()
                ->withSum('pumps as totalSleep', 'weight')
                ->where('status', QuesterStatus::COMPLETED)
                ->latest('totalSleep')
                ->take($take)
                ->get();
            static::update($selection, QuesterStatus::DRAWN);
            $winners =  $giveaway->questers()
                ->where('status', QuesterStatus::DRAWN)
                ->latest('completed_at')
                ->take($giveaway->num_winners)
                ->get();
            static::update($winners, QuesterStatus::WINNER);
            $giveaway->winner_selected_at = now();
            $giveaway->save();
            return;
        }
        $winnersCount =  $giveaway->questers()
            ->where('status', QuesterStatus::WINNER)
            ->count();
        if ($winnersCount < 1 && $drawn->count() > 0) { // force draw winner if none was found
            static::update($drawn, QuesterStatus::WINNER);
            $giveaway->winner_selected_at = now();
            $giveaway->save();
        }
    }
}
