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

# Core
- leverage iron.io queues for emails, logging and backups.
- add sweetalert library (https://laracasts.com/series/build-project-flyer-with-me/episodes/9)
- implement cloudflare
- look at gtmetrix.com, use caching library? varnish?
- improve translation organization
- setup email errors
- Implement login throttling
- Cache routes and views. Clear cache on product update (use model events?) Base each cache key on md5 hash of all product ID's to reduce over-clearing of cache.

# Todo
- copy of outgoing emails to: copy@123friluft.no
- make superadmin and customer roles uneditable.
- add store name for emails. + kundenr og ordrenr.
- dashboard stats & reports
- Compound Products
- rabatt popup ting
- product receivals

Order statuses
- ubehandlet
- klar til sending
- skrevet ut
- levert
- kansellert
- restordre | ikke på lager

Shipping
99,- service pakke sporing
under tusen gram: 39,- brevpost
over 800kr: fri frakt.

# design
http://www.tights.no/
gsport
xxl