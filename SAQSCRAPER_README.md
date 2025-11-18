# 📚 Service SaqScraper

Le service **SaqScraper** permet d'importer automatiquement le catalogue de produits de la SAQ dans la base de données locale via l'API GraphQL d'Adobe Commerce utilisée par le site web de la SAQ.

## 🏗️ Architecture

Le service est structuré en plusieurs composants :

- **Service principal** : `App\Services\SaqScraper` - Gère les requêtes GraphQL et le traitement des données
- **Commande Artisan** : `php artisan saq:import` - Point d'entrée pour lancer l'importation
- **Modèles Eloquent** : `BouteilleCatalogue`, `Pays`, `TypeVin` - Stockage des données importées
- **Migrations** : Création des tables nécessaires dans la base de données

## 🔧 Fonctionnement

### 1. Communication avec l'API GraphQL

Le service utilise l'endpoint GraphQL d'Adobe Commerce (`https://catalog-service.adobe.io/graphql`) pour récupérer les produits. Il envoie des requêtes de type `productSearch` avec :
- Pagination automatique (24 produits par page)
- Filtres sur les produits disponibles
- Tri par prix décroissant
- Support des catégories spécifiques via recherche par phrase

**⚠️ Limite de pagination de l'API** : L'API SAQ GraphQL impose une limite de **10 000 produits** maximum par requête de recherche. Lorsque cette limite est atteinte, l'importation s'arrête automatiquement avec le message d'erreur "Pagination is limited to 10000 products". Pour importer l'intégralité du catalogue (~12 600 produits), il faut utiliser des filtres de catégorie pour diviser l'importation en plusieurs requêtes plus petites.

### 2. Traitement des données

Pour chaque produit récupéré, le service :
- **Extrait les informations principales** : nom, SKU (code SAQ), prix, description
- **Détermine le type de vin** : Rouge, Blanc, Rosé, Champagne, Spiritueux (basé sur les attributs couleur et identité)
- **Identifie le pays et la région** : À partir des attributs `pays_origine` et `region_origine`
- **Extrait les métadonnées** : Millésime, volume, images
- **Télécharge et normalise les images** : 
  - Téléchargement depuis l'URL SAQ avec gestion des erreurs HTTP
  - Normalisation automatique des URLs (correction des doublons de domaine)
  - Optimisation des images swatch (remplacement 30x30 → 500x500)
  - **Optimisation** : Ignore le téléchargement des images qui existent déjà localement pour éviter les téléchargements inutiles lors des mises à jour du catalogue
  - Stockage local dans `storage/app/public/products/`
  - Logging détaillé pour le débogage

### 3. Sauvegarde en base de données

Les données sont organisées dans trois tables liées :
- **`pays`** : Liste des pays d'origine (création automatique si inexistant)
- **`type_vin`** : Liste des types de vin (création automatique si inexistant)
- **`bouteille_catalogue`** : Détails complets des bouteilles avec relations

La méthode `updateOrCreate` assure qu'un produit avec le même code SAQ sera mis à jour plutôt que dupliqué.

### 4. Gestion des erreurs et rate limiting

- **Délai entre requêtes** : Configurable (défaut : 2 secondes) pour respecter les limites de l'API
- **Gestion des erreurs** : Logging détaillé des erreurs sans interrompre l'importation
- **Retry logic** : Gestion automatique des échecs temporaires
- **Logging des images** : 
  - Logs de niveau `debug` : URLs originale et finale, nom de fichier
  - Logs de niveau `info` : Succès de téléchargement
  - Logs de niveau `warning` : Images vides détectées
  - Logs de niveau `error` : Erreurs de téléchargement avec contexte complet (URL, message d'erreur, trace)

## 📋 Configuration

### Variables d'environnement (`.env`)

```env
# Clé API pour l'authentification GraphQL (optionnel, une clé par défaut est fournie)
SAQ_X_API_KEY=7a7d7422bd784f2481a047e03a73feaf
SAQ_CLIENT_ID=7a7d7422bd784f2481a047e03a73feaf

# Configuration Magento/Adobe Commerce
SAQ_MAGENTO_STORE_CODE=main_website_store
SAQ_MAGENTO_STORE_VIEW_CODE=fr
SAQ_MAGENTO_WEBSITE_CODE=base
SAQ_MAGENTO_CUSTOMER_GROUP=
SAQ_MAGENTO_ENVIRONMENT_ID=2ce24571-9db9-4786-84a9-5f129257ccbb
```

### Préparation de la base de données

Avant d'utiliser le service, assurez-vous que les migrations sont exécutées :

```bash
php artisan migrate
```

Cela créera les tables nécessaires :
- `pays`
- `type_vin`
- `bouteille_catalogue`

## 🚀 Utilisation

### Commande de base

```bash
php artisan saq:import
```

Cette commande importera tous les produits disponibles du catalogue SAQ avec les paramètres par défaut :
- Pas de limite sur le nombre de produits
- Délai de 2 secondes entre les requêtes
- Toutes les catégories

### Options disponibles

#### Limiter le nombre de produits

Pour tester ou importer un nombre limité de produits :

```bash
php artisan saq:import --limite=10
```

#### Importer une catégorie spécifique

Pour importer uniquement les produits d'une catégorie particulière :

```bash
php artisan saq:import --categorie=produits/vin-rouge
```

**Comment ça fonctionne** : L'API SAQ GraphQL ne supporte pas les chemins de catégorie spécifiques (comme `produits/vin-rouge`) via le filtre `categoryPath`. À la place, le service utilise une **recherche par phrase** dans le champ `phrase` de l'API :
- `produits/vin-rouge` → recherche avec la phrase `"vin rouge"`
- `produits/vin-blanc` → recherche avec la phrase `"vin blanc"`
- `produits/vin-rose` → recherche avec la phrase `"vin rosé"`
- `produits/champagne` → recherche avec la phrase `"champagne"`
- `produits/spiritueux` → recherche avec la phrase `"spiritueux"`

Le filtre `categoryPath` reste à `"produits"` pour toutes les recherches, et la catégorisation est effectuée via la recherche par phrase. Cette approche permet de contourner la limitation de l'API qui retourne 0 produits avec des chemins de catégorie spécifiques.

Les catégories disponibles incluent :
- `produits/vin-rouge`
- `produits/vin-blanc`
- `produits/vin-rose`
- `produits/champagne`
- `produits/spiritueux`
- etc.

#### Ajuster le délai entre requêtes

Pour respecter les limites de l'API ou accélérer l'importation :

```bash
# Délai plus long (plus sûr)
php artisan saq:import --delai=5

# Délai plus court (plus rapide, mais risque de blocage)
php artisan saq:import --delai=1
```

**Note** : Le délai minimum est de 1 seconde pour éviter la surcharge de l'API.

#### Utiliser une clé API personnalisée

Si vous avez votre propre clé API :

```bash
php artisan saq:import --client-id=votre_cle_api
```

### Exemples combinés

```bash
# Importer 50 vins rouges avec un délai de 3 secondes
php artisan saq:import --categorie=produits/vin-rouge --limite=50 --delai=3

# Import rapide pour test (10 produits, 1 seconde de délai)
php artisan saq:import --limite=10 --delai=1
```

## 📊 Données importées

Pour chaque bouteille, les informations suivantes sont importées :

| Champ | Description | Source |
|-------|-------------|--------|
| `code_saQ` | Code SKU unique de la SAQ | `product.sku` |
| `nom` | Nom complet du produit | `product.name` |
| `prix` | Prix en dollars canadiens | `product.price_range` |
| `type_vin` | Type (Rouge, Blanc, Rosé, etc.) | Attributs `couleur` / `identite_produit` |
| `pays` | Pays d'origine | Attribut `pays_origine` |
| `region` | Région ou appellation | Attributs `region_origine` / `appellation` |
| `millesime` | Année de récolte | Attribut `millesime_produit` |
| `volume` | Taille de la bouteille | Attribut `format_contenant_ml` |
| `url_image` | Chemin local de l'image (format: `/storage/products/produit_XXXXX.ext`) | Téléchargée depuis `product.image.url` ou `product.small_image.url`, normalisée et stockée localement |
| `date_import` | Date et heure d'importation | Timestamp automatique |

## 🔍 Vérification des données importées

Pour vérifier les produits importés, vous pouvez utiliser Tinker :

```bash
php artisan tinker
```

```php
// Compter le nombre de bouteilles importées
App\Models\BouteilleCatalogue::count();

// Afficher les 10 dernières bouteilles
App\Models\BouteilleCatalogue::with(['pays', 'typeVin'])->latest('date_import')->take(10)->get();

// Compter par type de vin
App\Models\BouteilleCatalogue::join('type_vin', 'bouteille_catalogue.id_type_vin', '=', 'type_vin.id')
    ->select('type_vin.nom', DB::raw('count(*) as total'))
    ->groupBy('type_vin.nom')
    ->get();
```

## ⚠️ Notes importantes

1. **Limite de pagination de l'API** : L'API SAQ GraphQL impose une limite stricte de **10 000 produits maximum** par requête de recherche. Si vous tentez d'importer tous les produits sans filtre de catégorie (~12 600 produits), l'importation s'arrêtera automatiquement à la page 417 (environ 9 984 produits) avec l'erreur "Pagination is limited to 10000 products". Pour importer l'intégralité du catalogue, vous devez diviser l'importation en plusieurs commandes par catégorie :
   ```bash
   php artisan saq:import --categorie=produits/vin-rouge
   php artisan saq:import --categorie=produits/vin-blanc
   php artisan saq:import --categorie=produits/vin-rose
   php artisan saq:import --categorie=produits/champagne
   php artisan saq:import --categorie=produits/spiritueux
   ```

2. **Respect des limites de l'API** : Utilisez un délai approprié (minimum 2 secondes recommandé) pour éviter d'être bloqué par l'API de la SAQ.

3. **Images** : 
   - Les images sont téléchargées et stockées localement dans `storage/app/public/products/`
   - Le service normalise automatiquement les URLs (corrige les doublons de domaine, optimise les miniatures)
   - **IMPORTANT** : Assurez-vous que le lien symbolique `storage` est créé pour permettre l'accès public aux images :
     ```bash
     php artisan storage:link
     ```
   - Les chemins sont stockés au format `/storage/products/produit_XXXXX.ext` pour compatibilité avec `asset()`
   - En cas d'échec de téléchargement, l'URL originale SAQ est conservée comme fallback
   - Consultez les logs (`storage/logs/laravel.log`) pour diagnostiquer les problèmes de téléchargement d'images
   - **Optimisation** : Les images déjà téléchargées sont ignorées lors des mises à jour pour éviter les téléchargements inutiles

4. **Performance** : L'importation complète du catalogue peut prendre plusieurs heures. Utilisez l'option `--limite` pour tester d'abord.

5. **Mises à jour** : Relancer la commande mettra à jour les produits existants (basé sur le `code_saQ`) plutôt que de créer des doublons. Les images existantes ne seront pas re-téléchargées grâce à l'optimisation.

6. **Erreurs** : Consultez les logs Laravel (`storage/logs/laravel.log`) pour diagnostiquer les problèmes d'importation. Les logs incluent :
   - Erreurs de requêtes GraphQL
   - Erreurs de téléchargement d'images (avec URL et contexte)
   - Produits importés avec succès
   - Messages de débogage pour le traitement des images

7. **Affichage des images** : Pour afficher les images dans les vues Blade, utilisez `asset($bouteille->url_image)`. Les vues normalisent automatiquement les chemins pour gérer les anciens formats (`storage/products/...` → `/storage/products/...`).

## 🛠️ Développement

Pour modifier ou étendre le service :

- **Service** : `app/Services/SaqScraper.php`
- **Commande** : `app/Console/Commands/ImporterProduitsSaq.php`
- **Modèles** : `app/Models/BouteilleCatalogue.php`, `app/Models/Pays.php`, `app/Models/TypeVin.php`

## 📝 Exemples de code

### Utiliser le service directement dans le code

```php
use App\Services\SaqScraper;

// Créer une instance avec délai de 2 secondes
$scraper = new SaqScraper(2);

// Importer 10 produits
$nombreImportes = $scraper->importerCatalogue(null, 10, 2);

echo "Produits importés : {$nombreImportes}";
```

### Accéder aux données importées

```php
use App\Models\BouteilleCatalogue;

// Récupérer toutes les bouteilles avec leurs relations
$bouteilles = BouteilleCatalogue::with(['pays', 'typeVin'])->get();

// Rechercher par type de vin
$vinsRouges = BouteilleCatalogue::whereHas('typeVin', function($query) {
    $query->where('nom', 'Rouge');
})->get();

// Filtrer par pays
$vinsFrance = BouteilleCatalogue::whereHas('pays', function($query) {
    $query->where('nom', 'France');
})->get();
```

### Afficher les images dans les vues Blade

```blade
{{-- Dans une vue Blade (ex: welcome.blade.php) --}}
@if($bouteille->url_image)
    @php
        // Normaliser le chemin pour compatibilité avec les anciens formats
        $imageUrl = $bouteille->url_image;
        if (strpos($imageUrl, 'storage/') === 0 && strpos($imageUrl, '/storage/') !== 0) {
            $imageUrl = '/' . $imageUrl;
        }
    @endphp
    <img src="{{ asset($imageUrl) }}" 
         alt="{{ $bouteille->nom }}" 
         class="max-w-full max-h-full object-contain"
         onerror="this.src='data:image/svg+xml,...'">
@else
    <div>Aucune image</div>
@endif
```

**Note** : Les vues incluses dans le projet normalisent automatiquement les chemins, mais cette normalisation manuelle peut être nécessaire pour des vues personnalisées.

