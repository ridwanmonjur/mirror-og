<?php

namespace Database\Seeders;

use App\Models\NotificationCounter;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\NotifcationsUser;
use Carbon\Carbon;

class NotificationSeeder extends Seeder
{
    public function run()
    {
        // Get all users
        NotificationCounter::truncate();
        NotifcationsUser::truncate();
        $users = User::all();

        foreach ($users as $user) {
            // Social NotificationUsers
            $this->createNotificationUser($user, [
                'type' => 'social',
                'html' => '<a data-href="https://www.youtube.com/shorts/92ADmrIskFs" class="text-primary gear-icon-btn">Zuck</a> sent you a friend request.',
                'link' => 'https://www.youtube.com/shorts/92ADmrIskFs',
                'is_read' => false,
                'created_at' => '2024-02-08 09:42:00',
                'img_src' => 'https://upload.wikimedia.org/wikipedia/commons/1/18/Mark_Zuckerberg_F8_2019_Keynote_%2832830578717%29_%28cropped%29.jpg'
            ]);

            $this->createNotificationUser($user, [
                'type' => 'social',
                'html' => 'The Royal Prince of Saudi is now following you.',
                'link' => 'https://cdn.britannica.com/43/207843-050-792E9358/Mohammed-bin-Salman-policy-maker-Saudi-king-2015.jpg',
                'is_read' => true,
                'created_at' => '2024-02-08 09:58:00',
                'img_src' => 'https://cdn.britannica.com/43/207843-050-792E9358/Mohammed-bin-Salman-policy-maker-Saudi-king-2015.jpg'
            ]);

            // Team NotificationUsers
            $this->createNotificationUser($user, [
                'type' => 'teams',
                'icon_type' => 'confirm',
                'html' => 'Awesome Team has confirmed registration for <a data-href="https://www.youtube.com/watch?v=4XepH7Q-8rA" class="text-primary gear-icon-btn">The Super Duper Dota League</a>.',
                'link' => 'https://cdn.britannica.com/43/207843-050-792E9358/Mohammed-bin-Salman-policy-maker-Saudi-king-2015.jpg',
                'is_read' => true,
                'created_at' => '2024-02-08 10:00:00'
            ]);

            $this->createNotificationUser($user, [
                'type' => 'teams',
                'icon_type' => 'vote',
                'html' => 'Awesome Team has voted to STAY in <a data-href="https://www.youtube.com/watch?v=4XepH7Q-8rA" class="text-primary gear-icon-btn">The Super Duper Dota League</a>.',
                'link' => 'https://cdn.britannica.com/43/207843-050-792E9358/Mohammed-bin-Salman-policy-maker-Saudi-king-2015.jpg',
                'is_read' => false,
                'created_at' => '2024-02-08 09:00:00'
            ]);

            $this->createNotificationUser($user, [
                'type' => 'teams',
                'icon_type' => 'quit',
                'html' => 'A vote to quit <a data-href="https://www.youtube.com/watch?v=4XepH7Q-8rA" class="text-primary gear-icon-btn">The Super Duper Dota League</a> has been called for Awesome Team.',
                'link' => 'https://cdn.britannica.com/43/207843-050-792E9358/Mohammed-bin-Salman-policy-maker-Saudi-king-2015.jpg',
                'is_read' => false,
                'created_at' => '2024-02-07 22:00:00'
            ]);

            $this->createNotificationUser($user, [
                'type' => 'teams',
                'icon_type' => 'follow',
                'html' => 'The Royal Prince of Saudi has followed your team.',
                'link' => 'https://cdn.britannica.com/43/207843-050-792E9358/Mohammed-bin-Salman-policy-maker-Saudi-king-2015.jpg',
                'is_read' => true,
                'created_at' => '2024-02-08 09:58:00'
            ]);

            // Event NotificationUsers
            $this->createNotificationUser($user, [
                'type' => 'event',
                'icon_type' => 'calendar',
                'html' => '<a data-href="https://www.youtube.com/watch?v=4XepH7Q-8rA" class="text-primary gear-icon-btn">The Super Duper Dota League</a> has been rescheduled.',
                'link' => 'https://cdn.britannica.com/43/207843-050-792E9358/Mohammed-bin-Salman-policy-maker-Saudi-king-2015.jpg',
                'is_read' => false,
                'created_at' => '2024-02-08 09:00:00'
            ]);

            $this->createNotificationUser($user, [
                'type' => 'event',
                'icon_type' => 'live',
                'html' => '<a data-href="https://www.youtube.com/watch?v=4XepH7Q-8rA" class="text-primary gear-icon-btn">The Great CNY Dota Bash</a> has gone live!',
                'link' => 'https://cdn.britannica.com/43/207843-050-792E9358/Mohammed-bin-Salman-policy-maker-Saudi-king-2015.jpg',
                'is_read' => true,
                'created_at' => '2024-02-07 23:00:00'
            ]);
        }
    }

    private function createNotificationUser($user, $data)
    {
        NotifcationsUser::create([
            'user_id' => $user->id,
            'type' => $data['type'],
            'icon_type' => $data['icon_type'] ?? null,
            'img_src' => $data['img_src'] ?? null,
            'html' => $data['html'],
            'link' => $data['link'],
            'is_read' => $data['is_read'],
            'created_at' => $data['created_at'],
            'updated_at' => $data['created_at'],
        ]);
    }
}