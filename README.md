# JSON Web Token POC

## Contexte :

- Générer un JWT si un utilisateur est bien connecté.
- Mettre des données dans le payload.

## Déploiement :

### Avec Docker (sous une distribution Linux ou macOS) 😀 :

- Executez `start.bash`.
- Rendez-vous sur `localhost:80` pour accéder au projet.

### Sans Docker 😞 :

- Copier les fichiers contenus dans `./public` dans un repertoire écouté par un service (Nginx,Apache,IIS,autre)+PHP.
- Rendez-vous sur `localhost:80` (+ éventuelle le nom d'un sous répertoire où a été placé ce projet) pour accéder au projet.