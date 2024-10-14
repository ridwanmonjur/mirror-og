#!/bin/bash

# Start ChromeDriver and capture its output
output=$(./vendor/laravel/dusk/bin/chromedriver-linux 2>&1)

# Extract the port number from the output
port=$(echo "$output" | grep -oP 'ChromeDriver was started successfully on port \K\d+')

# If a port was found, write it to .env.dusk
if [ ! -z "$port" ]; then
    echo "DUSK_PORT=$port" >> .env.dusk
    echo "ChromeDriver started on port $port"
else
    echo "Failed to start ChromeDriver or determine its port"
    exit 1
fi

# Keep ChromeDriver running
./vendor/laravel/dusk/bin/chromedriver-linux