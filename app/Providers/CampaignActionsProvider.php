<?php

namespace App\Providers;

use App\Models\CampaignActionsEntry;
use App\Repositories\CampaignActionsRepo;
use App\Services\CampaignActionsServices;
use Illuminate\Support\ServiceProvider;

class CampaignActionsProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('CampaignActionEntry', function()
        {
            return new CampaignActionsServices(new CampaignActionsRepo(new CampaignActionsEntry()));
        });
    }
}
