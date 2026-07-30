#!/bin/bash

echo "🚀 Starting BeyondTrails Laravel Web Server & Reverb WebSocket Server..."

# Kill any previous instance running on 8000 or 8080
fuser -k 8000/tcp > /dev/null 2>&1
fuser -k 8080/tcp > /dev/null 2>&1

# Start Reverb WebSocket server in background
php artisan reverb:start --host=0.0.0.0 --port=8080 &
REVERB_PID=$!

echo "⚡ Reverb WebSocket Server running on ws://0.0.0.0:8080 (PID: $REVERB_PID)"

# Trap SIGINT / SIGTERM to clean up Reverb process
trap "kill $REVERB_PID 2>/dev/null" EXIT

# Run Laravel Artisan Serve on port 8000
php artisan serve --host=0.0.0.0 --port=8000
