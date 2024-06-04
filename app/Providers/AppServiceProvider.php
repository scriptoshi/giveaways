<?php

namespace App\Providers;

use Closure;
use Illuminate\Container\Container;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Notifications\ChannelManager;
use Illuminate\Support\Facades\Notification;
use NotificationChannels\Telegram\TelegramChannel;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        Notification::resolved(static function (ChannelManager $service) {
            $service->extend('telegram2', static fn ($app) => $app->make(TelegramChannel::class));
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        JsonResource::withoutWrapping();
        Collection::macro('paginate', function ($perPage = null, $columns = ['*'], $pageName = 'page', $page = null) {
            $page = $page ?: Paginator::resolveCurrentPage($pageName);
            $total = func_num_args() === 5 ? value(func_get_arg(4)) : $this->count();
            $perPage = $perPage instanceof Closure ? $perPage($total) : $perPage;
            $results = $total
                ? $this->forPage($page, $perPage)
                : $this->toArray();
            return Container::getInstance()->makeWith(LengthAwarePaginator::class, [
                'items' => $results,
                'total' => $total,
                'perPage' => $perPage,
                'currentPage' => $page,
                'options' => [
                    'path' => Paginator::resolveCurrentPath(),
                    'pageName' => $pageName,
                ]
            ]);
        });
    }
}
