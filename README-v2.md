# ATELIER — Prototype PWA

Prototype frontend du système d'information pour stylistes au Bénin.

## Lancer
Aucun build n'est nécessaire pour cette première version.

- Ouvrir `index.html` dans un navigateur pour une démonstration simple.
- Pour tester le Service Worker/PWA, utiliser un serveur HTTP local, par exemple :
  `python -m http.server 8080`
  puis ouvrir `http://localhost:8080`.

## Direction produit
- Vitrine : créateurs et collections
- Atelier : mesures, production, rentabilité, stock, planning
- Client : profils de mesures, commande, paiement, suivi

## Prochaine étape technique
Le prototype est volontairement indépendant du backend. Il peut ensuite être intégré à :
- Laravel
- MySQL
- Vue.js
- API Kkiapay/FedaPay
- Web Push
- stockage offline IndexedDB

## Architecture Laravel proposée
backend/
  app/
  database/
  routes/
  resources/
frontend/
  Vue components / PWA
