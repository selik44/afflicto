@servers(['staging' => 'root@188.166.86.110'])

@task('deploy')
    cd /usr/share/nginx/html
    git pull origin master
    composer update
    php artisan migrate
@endtask