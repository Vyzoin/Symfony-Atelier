# Ateliers Citoyens - Projet final

Application Symfony 7.4 livree avec front Twig, back-office securise, API documentee et commandes CLI.

## Installation et lancement

Les fichiers `.env`, `.env.dev` et `.env.test` ne sont pas versionnes (secrets locaux). Apres clone, copier les modeles :

```bash
cp .env.example .env
cp .env.dev.example .env.dev
cp .env.test.example .env.test
```

Puis definir `APP_SECRET` dans `.env.dev` (obligatoire pour Symfony), par exemple : `openssl rand -hex 16`.

Ensuite :

```bash
composer install --no-interaction
php bin/console doctrine:migrations:migrate --no-interaction
php bin/console doctrine:fixtures:load --no-interaction
symfony server:start -d
```

> Le projet utilise SQLite (`var/data.db`), donc `doctrine:database:create` n'est pas necessaire.

> Si le depot a deja ete public avec danciennes versions qui commitatient `.env.dev`, **regenere un nouveau `APP_SECRET`** dans `.env.dev` pour eviter dutiliser une cle exposee.

### Une fois pour les mainteneurs : arreter de versionner les fichiers ignores

Sans `git rm --cached`, `.gitignore` ne suffit pas : Git continue de suivre tout ce qui est **deja commite**.

**Verifier sil reste des fichiers suivis alors quils devraient etre ignores** (doit afficher vide une fois OK) :

```bash
git ls-files -ci --exclude-standard
```

1. Verifier que `.gitignore` est a jour et que `.env.example`, `.env.dev.example`, `.env.test.example` sont bien dans le depot.

2. Retirer du **suivi Git uniquement** tout ce qui correspond aux regles du projet (`vendor`, `var`, `.env`, caches, etc.). Les chemins absents de lindex ne posent pas probleme avec `--ignore-unmatch`.

```bash
# Git Bash, macOS ou Linux (grep + while read). Sous PowerShell seul : Git Bash livré avec Git for Windows.

# Environnement Symfony
git rm --cached --ignore-unmatch .env .env.dev .env.test .env.local .env.local.php
git ls-files | grep -E '^\.env\..+\.local$' | while read -r f; do git rm --cached --ignore-unmatch "$f"; done

# Cle de decryptage Symfony Secrets (si jamais commitee)
git ls-files | grep -E '^config/secrets/[^/]+/prod\.decrypt\.private\.php$' | while read -r f; do git rm --cached --ignore-unmatch "$f"; done

# Dependances et donnees generees
git rm -r --cached --ignore-unmatch vendor var public/bundles public/assets assets/vendor

# PHPUnit / cs-fixer / Composer creds / coverage
git rm -r --cached --ignore-unmatch .phpunit.cache coverage htmlcov
git rm --cached --ignore-unmatch phpunit.xml .phpunit.result.cache .php-cs-fixer.cache .php-cs-fixer.php auth.json

# IDE (si quelquun les a commits par erreur)
git rm -r --cached --ignore-unmatch .vscode .idea
```

Sur une copie **deja propre** du depot (sans `vendor/` ni `.env` dans Git), seules les lignes qui correspondent a des fichiers encore suivis feront un changement ; les autres sont sans effet.

3. Ajouter les changements et pousser :

```bash
git add .
git status   # verifier avant commit
git commit -m "Ne plus versionner fichiers locaux (.env, vendor, var, …)"
git push
```

Ensuite, les clones suivent la procedure « Installation » avec les fichiers `.example`.

### Si tu vois cette erreur Twig (`u` filter / StringExtension)

Tu as probablement sauté `composer install` ou une vieille copie sans `twig/string-extra`. Depuis la racine du projet :

```bash
composer require twig/string-extra
```

Ou refais simplement `composer install` avec un `composer.lock` a jour.

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