@servers(['staging' => 'root@188.166.86.110', 'production' => 'root@178.62.200.93'])

@setup
    $servers = ['staging', 'production'];
    if (isset($server)) $servers = [$server];
@endsetup

@macro('deploy')
    pull
    composer
    migrate
@endmacro

@task('pull', ['on' => $servers, 'parallel' => true])
    cd /usr/share/nginx/html
    git reset --hard
    git pull origin master
    chmod -R 755 *
    chmod -R 777 storage
    chmod -R 777 public
    chmod -R 777 bootstrap/cache
    php artisan route:cache
@endtask

@task('composer', ['on' => $servers, 'parallel' => true])
    cd /usr/share/nginx/html
    composer update -n
    chmod -R 755 *
    chmod -R 777 storage
    chmod -R 777 public
    chmod -R 777 bootstrap/cache
    php artisan route:cache
@endtask

@task('migrate', ['on' => $servers, 'parallel' => true])
    cd /usr/share/nginx/html
    php artisan migrate
    chmod -R 755 *
    chmod -R 777 storage
    chmod -R 777 public
    chmod -R 777 bootstrap/cache
    php artisan route:cache
@endtask