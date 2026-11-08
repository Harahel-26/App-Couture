# Atelier — Stylisme au Bénin · V3 MVP fonctionnel

Cette version conserve l’accueil mode de la V2 et ajoute un premier produit fonctionnel :

- choix de rôle et entrée dans un espace applicatif ;
- espace Styliste : dashboard, commandes, clients/mesures, production Kanban, stock, planning ;
- espace Client : commandes, suivi de production, profils de mesures, découverte d’ateliers ;
- espace Admin : supervision, comptes stylistes, commandes et modération ;
- données démo persistantes dans `localStorage` ;
- schéma MySQL cible dans `database/schema.sql` ;
- contrat des endpoints futurs dans `docs/API_CONTRACT.md`.

## Tester

Ouvrir `index.html` dans un navigateur moderne. Pour tester la PWA correctement, lancer un serveur statique :

```bash
python3 -m http.server 8080
```

Puis ouvrir `http://localhost:8080` depuis ce dossier.

## Prochaine connexion backend

Le backend cible reste Laravel + MySQL. Il devra fournir les endpoints décrits dans `docs/API_CONTRACT.md`, puis remplacer progressivement le stockage local par des appels API authentifiés.
