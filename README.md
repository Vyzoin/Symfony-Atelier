# Ateliers Citoyens - Projet final

Application Symfony 7.4 livree avec front Twig, back-office securise, API documentee et commandes CLI.

## Installation et lancement

Ce depot versionne `.env`, `.env.dev` et `.env.test` (projet de cours). Apres un clone, enchainez :

```bash
composer install --no-interaction
php bin/console doctrine:migrations:migrate --no-interaction
php bin/console doctrine:fixtures:load --no-interaction
symfony server:start
```

Prerequis : [Symfony CLI](https://symfony.com/download) pour la commande `symfony`, PHP 8.2+ avec les extensions usuelles (sqlite, intl, etc.).

> Le projet utilise SQLite (`var/data.db`), donc `doctrine:database:create` n'est pas necessaire.


### Une fois pour les mainteneurs : arreter de versionner les fichiers ignores

Sans `git rm --cached`, `.gitignore` ne suffit pas : Git continue de suivre tout ce qui est **deja commite**.

## Comptes de test

- Admin back-office : `admin` / `admin` (Roles admin)
- Participant : `participant` / `participant` (roles user)

Les fixtures creent aussi : 2 themes, 2 intervenants, 3 ateliers, 2 sessions (1 passee, 1 a venir).

## URL importantes

- Back-office : `/backoffice`
- Front utilisateur : `/ateliers`
- API prefixee : `/api`
- Documentation API : `/api/docs` (et raccourci `/docs`)

## Fonctionnalites principales

- Back-office CRUD : ateliers, themes, intervenants, sessions
- Front utilisateur : liste ateliers, detail atelier, inscription a une session
- API :
    - `GET /api/ateliers`
    - `GET /api/ateliers/{id}/detail`
    - `GET /api/ateliers/{id}/sessions?period=past|upcoming`
    - `POST /api/inscriptions`
- Securite coherente :
    - `/backoffice` reserve ROLE_ADMIN
    - ecritures API protegees
- Commandes CLI :
    - `app:ateliers:cleanup --dry-run --date=YYYY-MM-DD --force`
    - `app:ateliers:stats --format=table|json --export=...`

## Qualite

Scripts Composer disponibles :

- `composer test`
- `composer phpstan`
- `composer cs-check`
- `composer qualite`