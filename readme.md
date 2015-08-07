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
- improve translation organization
- performance: gtmetrix.com, use cloudflare? varnish cache?
- email errors
- Implement login throttling
- Cache routes and views. Clear cache on product update (use model events?) Base each cache key on md5 hash of all product ID's to reduce over-clearing of cache.

# Bugs
- Fix front-end for safari

# Todo
- Et par ting til på ordregreiene:  mulighet til å legge igjen melding på ordren.
  Både for kunden og for oss i admin. Og at vi kan velge  å krysse av for
  "Send oppdatering til kunde" slik at kunden får epost når ordren er oppdatert, med meldingen som vi evt har skrevet.
- Add "Free Shipping" tag.
- front.product-view: Få inn 100% fornøydgaranti-merke.
- dashboard stats & reports
- make checkout & footer text configurable
- related products appears when buying.
- add buy button for related products
- rabatt popup ting
- Compound Products
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