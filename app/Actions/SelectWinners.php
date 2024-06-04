<?php

namespace App\Actions;

use App\Enums\GiveawayType;
use App\Enums\QuesterStatus;
use App\Models\Giveaway;

class SelectWinners
{
    public function selectWinnerFor(Giveaway $giveaway, $percent = 100)
    {
        $drawn =  $giveaway->questers()
            ->where('percent', '>=', $percent)
            ->inRandomOrder()
            ->take($giveaway->num_winners)
            ->get();

        if ($giveaway->type == GiveawayType::DRAW) {
            $drawn->update(['status' => QuesterStatus::WINNER]);
            return;
        }

        if ($giveaway->type == GiveawayType::FCFS) {
            $winners =  $giveaway->questers()
                ->where('percent', '>=', $percent)
                ->latest('completed_at')
                ->take($giveaway->num_winners)
                ->get();
            $winners->update(['status' => QuesterStatus::WINNER]);
            return;
        }

        if ($giveaway->type == GiveawayType::DRAW_FCFS) {
            $take = $giveaway->num_winners > $giveaway->draw_size ? $giveaway->num_winners * 5 : $giveaway->draw_size;
            $selection =  $giveaway->questers()
                ->where('percent', '>=', $percent)
                ->latest('completed_at')
                ->take($take)
                ->get();
            $selection->update(['status' => QuesterStatus::DRAWN]);
            $winners = $giveaway->questers()
                ->where('status', QuesterStatus::DRAWN)
                ->inRandomOrder()
                ->take($giveaway->num_winners)
                ->get();
            $winners->update(['status' => QuesterStatus::WINNER]);
            return;
        }

        if ($giveaway->type == GiveawayType::LEADERBOARD) {
            $winners =  $giveaway->questers()
                ->withSum('pumps as totalSleep', 'weight')
                ->where('percent', '>=', $percent)
                ->latest('totalSleep')
                ->take($giveaway->num_winners)
                ->get();
            $winners->update(['status' => QuesterStatus::WINNER]);
            return;
        }

        if ($giveaway->type == GiveawayType::DRAW_LEADERBOARD) {
            $take = $giveaway->num_winners > $giveaway->draw_size ? $giveaway->num_winners * 5 : $giveaway->draw_size;
            $selection =  $giveaway->questers()
                ->withSum('pumps as totalSleep', 'weight')
                ->where('percent', '>=', $percent)
                ->latest('totalSleep')
                ->take($take)
                ->get();
            $selection->update(['status' => QuesterStatus::DRAWN]);
            $winners = $giveaway->questers()
                ->where('status', QuesterStatus::DRAWN)
                ->inRandomOrder()
                ->take($giveaway->num_winners)
                ->get();
            $winners->update(['status' => QuesterStatus::WINNER]);
            return;
        }

        if ($giveaway->type == GiveawayType::FCFS_LEADERBOARD) {
            $take = $giveaway->num_winners > $giveaway->draw_size ? $giveaway->num_winners * 5 : $giveaway->draw_size;
            $selection =  $giveaway->questers()
                ->withSum('pumps as totalSleep', 'weight')
                ->where('percent', '>=', $percent)
                ->latest('totalSleep')
                ->take($take)
                ->get();
            $selection->update(['status' => QuesterStatus::DRAWN]);
            $winners =  $giveaway->questers()
                ->where('status', QuesterStatus::DRAWN)
                ->latest('completed_at')
                ->take($giveaway->num_winners)
                ->get();
            $winners->update(['status' => QuesterStatus::WINNER]);
            return;
        }
        $winnersCount =  $giveaway->questers()
            ->where('status', QuesterStatus::WINNER)
            ->count();
        if ($winnersCount < 1) { // force draw winner if none was found
            $drawn->update(['status' => QuesterStatus::WINNER]);
        }
    }
}
