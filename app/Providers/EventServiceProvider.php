<?php

namespace App\Providers;

use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\ProposalWasCreated' => [
            'App\Listeners\EmailProposalCreator',
        ],
        'App\Events\ProposalApproved' => [
            'App\Listeners\EmailProposalApproved',
        ],
        'App\Events\ProposalClosed' => [         // AQUI
            'App\Listeners\EmailProposalClosed',
        ],
        'App\Events\ProposalReachedApprovalGoal' => [
            'App\Listeners\EmailProposalReachedApprovalGoal',
        ],
        'App\Events\ProposalTimeLimit' => [
            'App\Listeners\EmailProposalTimeLimit',
        ],
        'App\Events\ProposalApprovedByCommittee' => [
            'App\Listeners\EmailProposalApprovedByCommittee',
        ],
        'App\Events\ProposalClosedByCommittee' => [
            'App\Listeners\EmailProposalClosedByCommittee',
        ],

        \SocialiteProviders\Manager\SocialiteWasCalled::class => [
            // add your listeners (aka providers) here
            'SocialiteProviders\YouTube\YouTubeExtendSocialite@handle',
            'SocialiteProviders\Instagram\InstagramExtendSocialite@handle',
        ],
    ];

    /**
     * Register any other events for your application.
     *
     * @param \Illuminate\Contracts\Events\Dispatcher $events
     *
     * @return void
     */
    public function boot(DispatcherContract $events)
    {
        parent::boot($events);

        //
    }
}
