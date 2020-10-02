
## Simple news parser

1. Clone this rep.

2. Run `composer install`

3. Run `symfony server:start -d` (remember address for sterted server - you should use it at 7)

4. Create Databes with settings stored at file `.env` (`php bin/console doctrine:database:create`).

5. Run ` php bin/console doctrine:migrations:migrate`

6. For importing data run `php bin/console app:import-articles`

7. Go to 127.0.0.1:8000/article/ to see list of imported news.
