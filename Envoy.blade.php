@servers( [
    'web' => 'laravel@52.72.37.133' ,
    'localhost' => '127.0.0.1'
] )

@task('deploy', ['on' => 'web'])
cd /var/www/html
git pull origin {{ $branch }}
php artisan migrate
php composer update
@endtask

@task( 'createClientFtpUsers' , [ 'on' => 'local' ] )
    php artisan ftp:admin -s Client
@endtask
