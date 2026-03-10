# 🏨 Booking Platform — API Backend

API REST complète pour une plateforme de réservation (hôtels, salles, services).

**Stack :** Laravel 12 · PostgreSQL · Redis · Stripe · Laravel Sanctum

---

## 📋 Prérequis

- PHP 8.2+
- PostgreSQL 14+
- Redis (Memurai sur Windows)
- Composer
- Compte Stripe (mode test)
- Compte Mailtrap (emails dev)

---

## 🚀 Installation

### 1. Clone et install
```bash
git clone https://github.com/ton-user/booking-api.git
cd booking-api
composer install
```

### 2. Configuration
```bash
cp .env.example .env
php artisan key:generate
```

Remplis `.env` :
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=booking_db
DB_USERNAME=postgres
DB_PASSWORD=ton_mot_de_passe

REDIS_CLIENT=predis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379

STRIPE_KEY=pk_test_xxx
STRIPE_SECRET=sk_test_xxx

MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=ton_username
MAIL_PASSWORD=ton_password
MAIL_SCHEME=smtp
MAIL_FROM_ADDRESS=noreply@booking.com
MAIL_FROM_NAME="Booking Platform"
```

### 3. Base de données
```bash
php artisan migrate --seed
```

### 4. Storage
```bash
php artisan storage:link
```

### 5. Lance les serveurs
```bash
# Terminal 1 — API
php artisan serve

# Terminal 2 — Queue workers (emails, tâches async)
php artisan queue:work

# Terminal 3 — Scheduler (expiration réservations, rappels)
php artisan schedule:work
```

---

## 🔑 Comptes de démo

| Rôle | Email | Mot de passe |
|------|-------|--------------|
| Admin | admin@booking.com | password |
| Manager | manager@booking.com | password |
| Client | client@booking.com | password |

---

## 📡 API Endpoints

Documentation interactive disponible sur `/docs` après `php artisan scribe:generate`.

### Authentification
```
POST /api/register     Inscription
POST /api/login        Connexion (retourne token Sanctum)
POST /api/logout       Déconnexion
GET  /api/user         Profil utilisateur connecté
```

### Ressources
```
GET  /api/categories                    Liste des catégories
GET  /api/resources                     Liste avec filtres
GET  /api/resources/{id}                Détail
GET  /api/resources/{id}/availability   Disponibilités par mois
```

### Réservations (auth requise)
```
POST   /api/bookings        Créer une réservation
GET    /api/bookings        Mes réservations
GET    /api/bookings/{id}   Détail
PATCH  /api/bookings/{id}   Modifier
DELETE /api/bookings/{id}   Annuler
```

### Paiements (auth requise)
```
POST /api/payments          Créer un Payment Intent Stripe
POST /api/payments/confirm  Confirmer après paiement
```

### Admin (auth + rôle admin/manager)
```
GET   /api/admin/stats                  Dashboard stats
GET   /api/admin/resources              Liste ressources
POST  /api/admin/resources              Créer ressource
PUT   /api/admin/resources/{id}         Modifier ressource
DELETE /api/admin/resources/{id}        Désactiver ressource
POST  /api/admin/resources/{id}/images  Upload images
GET   /api/admin/bookings               Liste réservations
PATCH /api/admin/bookings/{id}          Modifier statut
GET   /api/admin/users                  Liste utilisateurs
PATCH /api/admin/users/{id}/role        Modifier rôle
```

---

## 💳 Paiement Stripe (mode test)

Carte de test :
```
Numéro   : 4242 4242 4242 4242
Expiration: 12/29
CVC      : 123
```

---

## 🧪 Tests
```bash
# Crée la base de test
psql -U postgres -c "CREATE DATABASE booking_db_test;"

# Lance les tests
php artisan test

# Avec couverture
php artisan test --coverage
```

**Suite de tests :**
- `AuthTest` — Inscription, connexion, déconnexion
- `ResourceTest` — Listing, filtres, détail
- `BookingTest` — Création, conflit, annulation, prix
- `AvailabilityServiceTest` — Logique disponibilités

---

## 🏗️ Architecture
```
app/
├── Http/
│   ├── Controllers/
│   │   ├── AuthController.php
│   │   ├── ResourceController.php
│   │   ├── BookingController.php
│   │   ├── PaymentController.php
│   │   └── Admin/
│   │       ├── AdminController.php
│   │       ├── AdminResourceController.php
│   │       ├── AdminBookingController.php
│   │       └── ImageController.php
│   └── Resources/          # API Resources (transformateurs)
├── Models/                 # Eloquent Models
├── Services/
│   └── AvailabilityService.php
├── Mail/                   # Mailables (confirmation, annulation, rappel)
└── Console/Commands/       # Scheduler commands
```

### Modèles et relations
```
User          → hasMany Bookings
Resource      → belongsTo Category
              → hasMany Bookings
              → hasMany Availabilities
              → hasMany ResourceImages
Booking       → belongsTo User, Resource
              → hasOne Payment
Payment       → belongsTo Booking
Availability  → belongsTo Resource
```

---

## 📧 Notifications Email

| Événement | Template |
|-----------|----------|
| Paiement confirmé | `emails.booking-confirmed` |
| Réservation annulée | `emails.booking-cancelled` |
| Rappel J-1 | `emails.booking-reminder` |

Toutes les notifications sont envoyées via **Laravel Queue** (asynchrone).

---

## ⚙️ Commandes utiles
```bash
# Expirer les réservations non payées
php artisan bookings:expire-pending

# Envoyer les rappels J-1
php artisan bookings:send-reminders

# Régénérer la doc API
php artisan scribe:generate

# Vider les caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

---

## 🚀 Déploiement production
```bash
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan migrate --force
```

Variables d'env supplémentaires en prod :
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://ton-domaine.com
```
