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

# Architecture
- Move complicated controller logic to commands, for extendability?
- Architecture: Requests, Events, Commands etc.
- improve translation organization

# Bugs
- Fix front-end for safari
- Handlekurven viser ikke sum før man har oppdatert sida.
- Dersom man endrer antall av et produkt i kassen, forsvinner frakten.
- Klarna checkout må scrolles sideveis på mobil.
- Pris i handlekurv/kasse skal være uten desimaler.

# Todo
- Core: Refactor the permissions system to use middleware parameters.
- Core: Setup emailing of errors and logs
- Core: implement login throttling
- Core: Cache routes and pages. Clear cache on product update (use model events?) Base each cache key on md5 hash of all product ID's to reduce over-clearing of cache.
- Core: rename "administrator" role to "Superadmin".
- Variants: enable/disable filtering on each variant.
- Add "Free Shipping" tag.
- protect proteria API routes with permissions instead of role.
- Images: Implement thumbnail generation and resizing for all images.
- Bannere må resizes, justeres automatisk.
- admin.products: På varer med varianter er det ønskelig at beholdningen summeres, og vises i feltet Stock.
- front.product-view: Få inn 100% fornøydgaranti-merke.
- front.product-view, tabs: reorder to (about > info > ...)
- dashboard stats & reports
- mini-banner on top: configurable background color and content.
- edit checkout & footer text
- product receivals
- related products appears when buying.
- rabatt popup ting
- performance: gtmetrix.com, use cloudflare? varnish cache?
- add iron.io queues for emails, logging and backups.

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