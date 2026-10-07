# Site de la Ligue Martiniquaise de Volley-Ball (LMVB)

Laravel 12 · PHP 8.2+ · Blade + Tailwind (CDN) · MySQL/MariaDB ou PostgreSQL.

## Fonctionnalités

- **Résultats automatiques FFVolley** : poules, calendriers, scores (avec le détail des sets), arbitres, salles et classements de la ligue (code FFVolley `LIMART`), importés depuis la plateforme officielle de la FFVolley.
  - Les clubs sont créés automatiquement à partir de leur numéro FFVolley.
  - Un match corrigé à la main dans l'admin est **verrouillé** : l'import ne l'écrase plus.
- **Deux déclencheurs de mise à jour** :
  1. **cron** (recommandé) : toutes les 30 min de 7h à minuit, plus chaque nuit à 3h30 ;
  2. **à la visite** : si les données ont plus de 30 min, la première visite lance l'import **après** l'envoi de la page, sans ralentir le visiteur. Un verrou garantit qu'un seul import tourne à la fois.
- **Rôles**
  - **Super admin** : gère tout le site.
  - **Admin de club** : gère uniquement la fiche, les actualités, la galerie et les messages de son club.
  - L'inscription publique est désactivée.
- **Admin** : actualités (éditeur riche, HTML filtré), galerie (photos converties en WebP, vidéos YouTube), clubs, matchs, compétitions, sélections et joueurs, beach-volley avec inscriptions en ligne, comité directeur, documents, partenaires, newsletter (export CSV), réglages (mot de la présidente, coordonnées, réseaux).
- **SEO** : balises meta et Open Graph, données structurées schema.org, `sitemap.xml`, `robots.txt`.

## Installation locale (Laragon)

```bash
composer install
cp .env.example .env && php artisan key:generate
php artisan migrate
php artisan lmvb:sync-ffvb --season=2025/2026   # historique de la saison précédente
php artisan lmvb:sync-ffvb                      # saison en cours
php artisan db:seed                             # contenus, sélections, réglages, super admin
php artisan test
```

## Mise en production sur o2switch

1. **Fichiers** : envoyer le projet dans `~/lmvb` (hors `public_html`), par Git ou SFTP. Inclure `vendor/`, ou lancer `composer install --no-dev -o` en SSH.
2. **Domaine** : dans cPanel > *Domaines*, faire pointer la racine du domaine vers `~/lmvb/public`.
3. **Base de données** : cPanel > *Bases de données MySQL*. Créer la base et l'utilisateur, puis renseigner `.env` :
   ```dotenv
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://votre-domaine.fr
   DB_CONNECTION=mysql
   DB_HOST=localhost
   DB_DATABASE=xxx_lmvb
   DB_USERNAME=xxx_lmvb
   DB_PASSWORD=...
   MAIL_MAILER=smtp        # compte email cPanel, pour les notifications de contact et les mots de passe oubliés
   ```
4. **Commandes SSH** (terminal cPanel) :
   ```bash
   cd ~/lmvb
   php artisan key:generate   # si APP_KEY est vide
   php artisan migrate --force
   php artisan lmvb:sync-ffvb --season=2025/2026
   php artisan lmvb:sync-ffvb
   php artisan db:seed --force
   php artisan config:cache && php artisan route:cache && php artisan view:cache
   ```
   Le seeder affiche le mot de passe du super admin `admin@lmvb972.fr` s'il n'en existe pas encore.
5. **Cron** (cPanel > *Tâches Cron*, toutes les minutes) :
   ```
   * * * * * cd /home/VOTRE_USER/lmvb && /usr/local/bin/php artisan schedule:run >> /dev/null 2>&1
   ```
   Vérifiez le chemin de PHP avec `which php` en SSH. La version de PHP se règle dans cPanel > *Sélecteur de version PHP* (8.2 minimum, extensions `gd`, `intl`, `pdo_mysql`).
6. Dans `robots.txt`, remplacer `https://lmvb972.fr` par le domaine définitif.

## Commandes utiles

| Commande | Rôle |
|---|---|
| `php artisan lmvb:sync-ffvb` | Synchronise la saison en cours |
| `php artisan lmvb:sync-ffvb --season=2024/2025` | Synchronise une saison précédente |
| `php artisan lmvb:sync-ffvb --poule=PUM` | Synchronise une seule poule |
| `php artisan schedule:list` | Affiche les tâches planifiées |

La synchronisation se règle dans `.env` :
- `FFVB_SYNC_ENABLED` : active ou coupe la synchronisation ;
- `FFVB_SYNC_ON_VISIT` : active ou coupe la synchro déclenchée par les visites ;
- `FFVB_SYNC_INTERVAL` : nombre de minutes entre deux synchros à la visite.

L'annuaire des clubs (noms accentués, communes, couleurs, réseaux) se trouve dans `config/lmvb.php`. Toute fiche reste modifiable dans l'admin.

## Crédits médias

- **Logo** : LMVB.
- **Photos d'illustration** : Unsplash (licence Unsplash) et Wikimedia Commons (domaine public), dans `public/images/photos`.
- **Vidéos de fond** : Pexels (licence Pexels).
- **Photo de la présidente** : publiée par Martinique la 1ère (03/12/2024, crédit « DR »). Son utilisation doit être confirmée par la ligue.
"# lmvb" 
