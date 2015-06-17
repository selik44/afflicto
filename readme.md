## 123Friluft Multi-Store
Built on Laravel 5, leveraging GentleStyle SASS Framework.

## Install

1. "git clone"
2. "cp .env.example .env" and fill in the blanks.
3. "composer install --no-scripts"
4. php artisan migrate
5. php artisan db:seed
8. To import products from MyStore API, run "php artisan mystore:import".


## Pushing to the staging server
To push to the staging server, simply run "envoy run staging".
To configure envoy, see "Envoy.blade.php" in the project root.

# multi-store
- views can be overriden per-store, using the view method of the Controller class. The default views are the ones used by 123friluft


## Ideas

- Figure out a way to cache routes while still allowing translated routes.
- Move complicated controller logic to commands, for extendability?
- Leverage Laravel Requests more
- Leverage Laravel Events

- Order status, green/red.
- manual orders, user management.
- Banners, images etc.
- Related products, tabbed view, appears when buying.

- under tusen gram: 39,- brevpost
- over 1000kr: 99,- service pakke sporing
- over 800kr: fri frakt.


ubehandlet
klar til sending
skrevet ut
levert
kansellert
restordre | ikke på lager

generate PDF for packlists with multiple orders