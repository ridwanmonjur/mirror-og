<?php

namespace App\Console\Commands;

use App\Models\EventDetail;
use App\Models\Team;
use App\Models\User;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
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
        try {
            $sitemap = Sitemap::create()
                ->add(Url::create('/'))
                ->add(Url::create('/home'))
                ->add(Url::create('/about'))
                ->add(Url::create('/contact'));

            $sitemap->add(Url::create('/feeds/events')
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
                ->setPriority(0.9));

            try {
                EventDetail::whereNotIn('status', ['DRAFT', 'PENDING', 'PREVIEW'])
                    ->chunk(50, function ($events) use (&$sitemap) {
                        foreach ($events as $event) {
                            try {
                                $sitemap->add(Url::create("/event/{$event->id}/{$event->eventName}")
                                    ->setLastModificationDate($event->created_at)
                                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
                                    ->setPriority(0.8));
                            } catch (Exception $e) {
                                Log::error("Error adding event to sitemap. Event ID: {$event->id}, Error: ".$e->getMessage());
                            }
                        }
                    });
                Log::info('Events successfully added to sitemap');
            } catch (Exception $e) {
                Log::error('Error processing events for sitemap: '.$e->getMessage());
            }

            try {
                Team::chunk(50, function ($teams) use (&$sitemap) {
                    foreach ($teams as $team) {
                        try {
                            $teamTitle = $team->teamName;
                            $sitemap->add(Url::create("/view/team/{$team->id}/{$teamTitle}")
                                ->setLastModificationDate($team->created_at)
                                ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                                ->setPriority(0.7));
                        } catch (Exception $e) {
                            Log::error("Error adding team to sitemap. Team ID: {$team->id}, Error: ".$e->getMessage());
                        }
                    }
                });
                Log::info('Teams successfully added to sitemap');
            } catch (Exception $e) {
                Log::error('Error processing teams for sitemap: '.$e->getMessage());
            }

            try {
                User::where('role', 'PARTICIPANT')
                    ->chunk(50, function ($participants) use (&$sitemap) {
                        foreach ($participants as $participant) {
                            try {
                                $participantName = $participant->name;
                                $sitemap->add(Url::create("/view/participant/{$participant->id}/{$participantName}")
                                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                                    ->setPriority(0.6));
                            } catch (Exception $e) {
                                Log::error("Error adding participant to sitemap. Participant ID: {$participant->id}, Error: ".$e->getMessage());
                            }
                        }
                    });
                Log::info('Participants successfully added to sitemap');
            } catch (Exception $e) {
                Log::error('Error processing participants for sitemap: '.$e->getMessage());
            }

            try {
                User::where('role', 'ORGANIZER')
                    ->chunk(50, function ($organizers) use (&$sitemap) {
                        foreach ($organizers as $organizer) {
                            try {
                                $organizerName = $organizer->name;
                                $sitemap->add(Url::create("/view/organizer/{$organizer->id}/{$organizerName}")
                                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                                    ->setPriority(0.6));
                            } catch (Exception $e) {
                                Log::error("Error adding organizer to sitemap. Organizer ID: {$organizer->id}, Error: ".$e->getMessage());
                            }
                        }
                    });
                Log::info('Organizers successfully added to sitemap');
            } catch (Exception $e) {
                Log::error('Error processing organizers for sitemap: '.$e->getMessage());
            }

            try {
                $sitemap->writeToFile(public_path('sitemap.xml'));
                Log::info('Sitemap successfully generated and written to file');
            } catch (Exception $e) {
                Log::error('Error writing sitemap to file: '.$e->getMessage());
                throw $e;
            }

        } catch (Exception $e) {
            Log::error('Critical error in sitemap generation: '.$e->getMessage());

        }
    }
}
