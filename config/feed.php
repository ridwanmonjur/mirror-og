<?php

return [
    'feeds' => [
        'events' => [
            'items' => [App\Models\EventDetail::class, 'getFeedItems'],
            'url' => '/feeds/events',
            'title' => 'Latest Esports Events',
            'description' => 'Stay updated with the newest tournaments and competitions',
            'language' => 'en-US',
            'format' => 'atom',  // Specify Atom format
            'view' => 'feed::atom', // Use Atom view
        ],
    ],
];
