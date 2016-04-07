@servers(['web' => 'laravel@52.72.37.133'])
@task('deploy', ['on' => 'web'])
cd /var/www/html
git pull origin {{ $branch }}
php artisan migrate
php composer update
@endtask