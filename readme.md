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
- Implement login throttling
- Cache routes and views. Clear cache on product update (use model events?) Base each cache key on md5 hash of all product ID's to reduce over-clearing of cache.

# Bugs
- admin.products.store: fix saving manufacturers and enabled fields.
- fix discount rate in the orders items array (2000 should be 20 etc) 

# SEO
- fix mass activatino of orders.
- contact proteria regarding export to URL
- research SEO
- add meta description fields for categories & products
- add meta description settings for homepage
- add manufacturer to title on product view

# Todo
- implement DB backups with: chickling\Backup\BackupServiceProvider
- user.orders.show: fix shipping text and show discount
- make discounts always round the prices.
- fix footer background
- make superadmin and customer roles uneditable to streamline UI and prevent errors.
- fix the name parsing when generating a user from a klarna order
- implement default sorting settings for laratables (admin.orders.index shoudl sort by created_at, ASC.
- move summary field and make it use ckeditor
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