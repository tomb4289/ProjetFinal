# 🍷 Projet Web 2 – Vino

**Vino** est une application web permettant à chaque utilisateur de gérer un ou plusieurs celliers de vin.  
Elle intègre le catalogue officiel de la SAQ via une API GraphQL, permet d’ajouter des bouteilles personnalisées, de noter ses dégustations et de trier/rechercher facilement.  
Développée en équipe selon la méthode Agile/Scrum dans le cadre du cours **582-41W** au Collège de Maisonneuve. 

---

## 📌 Objectif du projet

Permettre à un utilisateur de :
- Gérer un ou plusieurs celliers de vin.
- Ajouter, modifier et supprimer des bouteilles.
- Importer et consulter le catalogue officiel de la SAQ.

---

## 🚀 Fonctionnalités clés

### ✅ Fonctionnalités implémentées

- ✅ Maquettes mobiles (Accueil, Cellier, Authentification)
- ✅ Base technique Laravel avec support MySQL/SQLite
- ✅ **Import automatisé du catalogue SAQ** via GraphQL (Adobe Commerce API)
- ✅ **Authentification complète** (connexion / inscription / déconnexion)
- ✅ **Gestion multi-celliers par utilisateur** (création, modification, suppression)
- ✅ **CRUD complet sur les bouteilles de cellier** (ajout, modification, suppression, affichage)
- ✅ **Système de notes de dégustation** (notes textuelles et notation par étoiles 0-10)
- ✅ **Tri des bouteilles** (par nom, pays, type, quantité, format, prix, date d'ajout)
- ✅ **Ajout de bouteilles depuis le catalogue SAQ** au cellier
- ✅ **Ajout manuel de bouteilles** (pour les vins non répertoriés à la SAQ)
- ✅ **Système de notifications toast** (succès/erreur)
- ✅ **Affichage détaillé des bouteilles** (avec images, informations complètes)
- ✅ **Gestion des quantités** (augmentation/diminution rapide)

### ⏳ Fonctionnalités à venir

- ⏳ Recherche & filtres avancés (nom, type, pays, millésime, région…)
- ⏳ Liste d'achat
- ⏳ Partage social
- ⏳ Normalisation des régions (table `regions` séparée)

---

## ⚙️ Stack technique

| Couche        | Technologie                        |
|---------------|------------------------------------|
| **Backend**   | Laravel 12, PHP 8.2                |
| **Frontend**  | Blade, Tailwind CSS v4, Vite       |
| **Base de données** | SQLite (migrations incluses) |
| **API externe** | GraphQL (Adobe Commerce – SAQ)   |
| **HTTP client** | Guzzle 7.10                      |
| **Tests**     | PHPUnit 11.5                       |
| **Design**    | Figma (mobile-first)               |
| **Gestion projet** | Jira (Scrum/Agile)            |
| **JavaScript** | ES6 Modules, Vanilla JS          |

---

## 📚 Service SaqScraper

Le service **SaqScraper** permet d'importer automatiquement le catalogue de produits de la SAQ dans la base de données locale via l'API GraphQL d'Adobe Commerce.

Pour une documentation complète sur le service, consultez [SAQSCRAPER_README.md](SAQSCRAPER_README.md).

**Utilisation rapide** :
```bash
# Importer 10 produits pour tester
php artisan saq:import --limite=10
```

---

## 🎯 Fonctionnalités détaillées

### Authentification
- Inscription avec validation des données
- Connexion avec gestion de session
- Déconnexion sécurisée
- Protection des routes par middleware `auth`

### Gestion des celliers
- Création de plusieurs celliers par utilisateur
- Modification et suppression de celliers
- Affichage en grille avec compteur de bouteilles
- Navigation cliquable vers les détails d'un cellier

### Gestion des bouteilles
- **Depuis le catalogue SAQ** : Ajout rapide avec sélection de quantité
- **Manuelle** : Création de bouteilles personnalisées avec tous les détails
- **Modification** : Édition des bouteilles manuelles (les bouteilles SAQ sont en lecture seule)
- **Suppression** : Retrait de bouteilles du cellier
- **Affichage détaillé** : Page complète avec image, informations, notes de dégustation
- **Gestion des quantités** : Boutons +/- pour ajuster rapidement

### Notes de dégustation
- Notes textuelles libres (jusqu'à 5000 caractères)
- Notation par étoiles (0 à 10)
- Modification des notes à tout moment
- Affichage dans la page de détails de la bouteille

### Catalogue SAQ
- Import automatisé via API GraphQL
- Affichage paginé des bouteilles
- Recherche dans le catalogue
- Téléchargement automatique des images
- Normalisation des données (pays, type de vin)

### Système de notifications
- Toasts de succès (vert) et d'erreur (rouge)
- Affichage automatique en bas à droite
- Disparition automatique après 2.5 secondes
- Support de plusieurs toasts simultanés

## 🔗 Liens utiles
- Maquettes Figma
- Backlog & Sprint Board (Jira)
- Dépôt GitHub

---

## 👥 Équipe de développement
Samaneh Mahboudi
Philippe Cossette
Adil El Amrani
Tommy Bourgeois

---

## 🛠️ Installation & démarrage

### Prérequis
- PHP 8.2+
- Composer
- Node.js 
- MySQL

### Étapes

1. **Cloner le dépôt**
   ```bash
   git clone https://github.com/ProjetFinal-Maisonneuve/ProjetFinal.git
   cd ProjetFinal
   ```

2. **Installer les dépendances PHP**
   ```bash
   composer install
   ```

3. **Configurer l'environnement**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Configurer la base de données**
   
   Modifiez le fichier `.env` pour configurer votre base de données (SQLite recommandé pour le développement) :
   ```env
   DB_CONNECTION=sqlite
   SESSION_DRIVER=file
   ```

   Créez le fichier de base de données SQLite :
   ```bash
   touch database/database.sqlite
   ```

5. **Exécuter les migrations**
   ```bash
   php artisan migrate
   ```

6. **Installer les dépendances frontend**
   ```bash
   npm install
   ```

7. **Créer le lien symbolique pour le stockage**
   ```bash
   php artisan storage:link
   ```

8. **Lancer le serveur de développement**
   ```bash
   php artisan serve
   ```

   L'application sera accessible à `http://localhost:8000`

9. **Compiler les assets frontend**
   ```bash
   npm run build
   # ou pour le développement avec hot-reload
   npm run dev
   ```

10. **Importer le catalogue SAQ (optionnel)**
    ```bash
    php artisan saq:import --limite=10
    ```

    Voir [SAQSCRAPER_README.md](SAQSCRAPER_README.md) pour la documentation complète du service.

---

## 📝 Notes de développement

### Structure de la base de données
- **Tables principales** : `users`, `celliers`, `bouteilles`, `bouteille_catalogue`
- **Tables de référence** : `pays`, `type_vin`
- **Relations** : Un utilisateur peut avoir plusieurs celliers, un cellier contient plusieurs bouteilles

### Conventions de code
- **Backend** : Code en français (commentaires, variables, fonctions)
- **Frontend** : Code JavaScript en français lorsque possible
- **Routes** : Noms en anglais (convention Laravel)
- **Vues** : Blade templates avec composants réutilisables

### Améliorations futures
- Normalisation de la table `regions` (actuellement stockée comme string)
- Filtres avancés par région, millésime, prix
- Export/import de celliers
- Partage de celliers entre utilisateurs
