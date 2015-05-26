@servers(['staging' => 'root@188.166.86.110'])

@task('staging', ['on' => 'staging'])
    cd /usr/share/nginx/html
    git reset --hard
    git pull origin master
    composer update
    php artisan migrate
    chmod -R 755 *
    chmod -R 777 storage
    chmod -R 777 public
@endtask