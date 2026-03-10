# 🏨 Booking Platform — Frontend

Interface utilisateur SPA pour la plateforme de réservation.

**Stack :** Vue.js 3 · Vite · Tailwind CSS v4 · Pinia · Vue Router 4 · Stripe.js

---

## 🚀 Installation
```bash
git clone https://github.com/ton-user/booking-frontend.git
cd booking-frontend
npm install
```

### Configuration `.env`
```env
VITE_API_URL=http://localhost:8000/api
VITE_STRIPE_KEY=pk_test_xxx
```

### Lancer en développement
```bash
npm run dev
```

### Build production
```bash
npm run build
```

---

## 📁 Structure
```
src/
├── pages/
│   ├── HomePage.vue           Page d'accueil
│   ├── ResourcesPage.vue      Catalogue avec filtres
│   ├── ResourceDetailPage.vue Détail + calendrier + réservation
│   ├── CheckoutPage.vue       Tunnel de paiement Stripe
│   ├── DashboardPage.vue      Profil utilisateur
│   ├── BookingsPage.vue       Historique réservations
│   ├── auth/
│   │   ├── LoginPage.vue
│   │   └── RegisterPage.vue
│   └── admin/
│       └── AdminPage.vue      Panel administrateur
├── components/
│   ├── ui/                    Header, Footer
│   ├── resource/              ResourceCard
│   ├── booking/               BookingCard
│   └── admin/                 ResourceModal, ImageUploader
├── stores/
│   ├── auth.js                Authentification (Pinia)
│   ├── resources.js           Ressources + disponibilités
│   ├── booking.js             Réservations + paiement
│   └── admin.js               Panel admin
├── services/
│   └── api.js                 Axios + intercepteurs
└── router/
    └── index.js               Routes + guards
```

---

## 🔐 Rôles et accès

| Page | Client | Admin/Manager |
|------|--------|---------------|
| Catalogue | ✅ | ✅ |
| Réservation | ✅ | ✅ |
| Dashboard | ✅ | ✅ |
| Panel Admin | ❌ | ✅ |

---

## 💳 Paiement Stripe

Le tunnel de paiement suit 3 étapes :
1. **Récapitulatif** — Vérification des dates et du prix
2. **Paiement** — Formulaire Stripe Elements sécurisé
3. **Confirmation** — Email envoyé automatiquement

Carte de test : `4242 4242 4242 4242` · `12/29` · `123`