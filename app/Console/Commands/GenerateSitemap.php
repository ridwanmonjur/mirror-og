<?php

namespace App\Console\Commands;

use App\Models\EventDetail;
use App\Models\Team;
use App\Models\User;
use Illuminate\Console\Command;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;
class GenerateSitemap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tasks:sitemap';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
  
    
    public function handle()
    {
        $sitemap = Sitemap::create()
            ->add(Url::create('/'))
            ->add(Url::create('/home'))
            ->add(Url::create('/about'))
            ->add(Url::create('/contact'));

        $sitemap->add(Url::create('/feeds/events')
            ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
            ->setPriority(0.9));
        
        EventDetail::whereNotIn('status', ['DRAFT', 'PENDING', 'PREVIEW'])
            ->chunk(50, function ($events) use (&$sitemap) {
            foreach ($events as $event) {
                $sitemap->add(Url::create("/event/{$event->id}/{$event->eventName}")
                    ->setLastModificationDate($event->updated_at)
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
                    ->setPriority(0.8));
            }
        });

        Team::chunk(50, function ($teams) use (&$sitemap) {
            foreach ($teams as $team) {
                $teamTitle = $team->teamName ;
                $sitemap->add(Url::create("/view/team/{$team->id}/{$teamTitle}")
                    ->setLastModificationDate($team->updated_at)
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                    ->setPriority(0.7));
            }
        });

        User::where('role', 'PARTICIPANT')
            ->chunk(50, function ($participants) use (&$sitemap) {
                foreach ($participants as $participant) {
                    $participantName = $participant->name ;
                    $sitemap->add(Url::create("/view/participant/{$participant->id}/{$participantName}")
                        ->setLastModificationDate($participant->updated_at)
                        ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                        ->setPriority(0.6));
                }
            });

        User::where('role', 'ORGANIZER')
            ->chunk(50, function ($organizers) use (&$sitemap) {
                foreach ($organizers as $organizer) {
                    $organizerName = $organizer->name ;
                    $sitemap->add(Url::create("/view/organizer/{$organizer->id}/{$organizerName}")
                        ->setLastModificationDate($organizer->updated_at)
                        ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                        ->setPriority(0.6));
                }
            });


        $sitemap->writeToFile(public_path('sitemap.xml'));
    }
}
