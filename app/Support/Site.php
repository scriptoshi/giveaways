<?php

namespace App\Support;

use App\Models\Giveaway;
use App\Models\Project;
use Watson\Sitemap\Facades\Sitemap;

class Site
{
    public static function map()
    {
        Sitemap::addTag(route('home'), now(), 'daily', '1');
        Sitemap::addTag(route('giveaways.index'), now(), 'daily', '0.8');
        Sitemap::addTag(route('projects.index'), now(), 'daily', '0.8');
        $giveaways = Giveaway::query()->latest()->take(200)->get();
        $giveaways->each(function (Giveaway $ga) {
            Sitemap::addTag(route('giveaways.show', ['giveaway' => $ga->slug]), $ga->created_at, 'daily', '0.8');
        });
        $projects = Project::query()->latest()->take(200)->get();
        $projects->each(function (Project $project) {
            Sitemap::addTag(route('projects.show', ['project' => $project->slug]), $project->created_at, 'daily', '0.8');
        });
        //create
        $xml = Sitemap::render();
        file_put_contents(public_path('sitemap.xml'), $xml);
    }
}
