#!/bin/bash

# Kill any existing processes
killall php 2>/dev/null
killall node 2>/dev/null

# Start services in separate terminals
gnome-terminal --tab -- bash -c "php artisan serve; exec bash"
gnome-terminal --tab -- bash -c "npm run dev; exec bash"
gnome-terminal --tab -- bash -c "php artisan queue:work; exec bash"