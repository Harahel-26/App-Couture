# Branchement V3 → Laravel

Le front reste utilisable en mode démo. Pour activer le backend, définir avant `app.js` :

```html
<script>window.ATELIER_API_URL='http://localhost:8000/api';</script>
<script src="./api-client.js"></script>
<script src="./app.js"></script>
```

Le fichier `api-client.js` fournit l'adaptateur pour l'authentification, commandes, mesures, stock, rendez-vous et catalogue.

La prochaine itération peut remplacer progressivement les fonctions `localStorage` de `app.js` par cet adaptateur, écran par écran, sans refaire le design.
