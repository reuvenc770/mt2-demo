## Running Console Commands & Queues

All these commands shouold be done on the VM itself

###Run Queue Workers
The queue is a general command and will try and work any jobs.
`php artisan queue:listen --sleep=3 --tries=3`

### Firing Command Line Jobs

all console commands are fired the same
`php artisan reports:downloadESP BlueHornet`
you can see  the list by running `php artisan list`

