<?php

namespace App\Services;

use App\Models\BouteilleCatalogue;
use App\Models\Pays;
use App\Models\TypeVin;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

// Service pour scraper et importer le catalogue de la SAQ
// Utilise l'API GraphQL de la SAQ pour récupérer les données des produits
// et les stocker dans la base de données locale
class SaqScraper
{
    
    private $client;
    private $baseUrl = 'https://www.saq.com';
    private $graphqlEndpoint = 'https://catalog-service.adobe.io/graphql';
    private $delaiRequete;
    private $derniereRequete = 0;
    private $clientId;

    public function __construct($delaiRequete = 2, $clientId = null)
    {
        $this->clientId = $clientId ?? env('SAQ_CLIENT_ID', env('SAQ_X_API_KEY'));
        
        $headers = [
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Origin' => 'https://www.saq.com',
            'Referer' => 'https://www.saq.com/',
            'Accept-Language' => 'fr-FR,fr;q=0.9,en;q=0.8',
        ];

        $headers['X-Api-Key'] = $this->clientId ?? env('SAQ_X_API_KEY', '7a7d7422bd784f2481a047e03a73feaf');

        $magentoStoreCode = env('SAQ_MAGENTO_STORE_CODE', 'main_website_store');
        $magentoStoreViewCode = env('SAQ_MAGENTO_STORE_VIEW_CODE', 'fr');
        $magentoWebsiteCode = env('SAQ_MAGENTO_WEBSITE_CODE', 'base');
        $magentoCustomerGroup = env('SAQ_MAGENTO_CUSTOMER_GROUP', '');
        $magentoEnvironmentId = env('SAQ_MAGENTO_ENVIRONMENT_ID', '2ce24571-9db9-4786-84a9-5f129257ccbb');

        $headers['Magento-Store-Code'] = $magentoStoreCode;
        $headers['Magento-Store-View-Code'] = $magentoStoreViewCode;
        $headers['Magento-Website-Code'] = $magentoWebsiteCode;
        $headers['Magento-Environment-Id'] = $magentoEnvironmentId;
        
        if ($magentoCustomerGroup !== '') {
            $headers['Magento-Customer-Group'] = $magentoCustomerGroup;
        }

        $this->client = new Client([
            'timeout' => 30,
            'verify' => false,
            'headers' => $headers,
        ]);
        $this->delaiRequete = $delaiRequete;
    }

    private function genererRequestId()
    {
        return bin2hex(random_bytes(16));
    }

    // Importe le catalogue de la SAQ avec options de catégorie, limite et délai entre les requêtes
    public function importerCatalogue($categorie = null, $limite = null, $delai = 2)
    {
        $this->delaiRequete = $delai;
        $compteur = 0;
        $page = 1;
        $pageSize = 24;
        $totalCount = null;

        try {
            do {
                $produits = $this->obtenirProduitsGraphQL($page, $pageSize, $categorie, $totalCount);

                if (empty($produits)) {
                    break;
                }

                foreach ($produits as $produit) {
                    if ($limite && $compteur >= $limite) {
                        break 2;
                    }

                    try {
                        $produitTraite = $this->traiterProduitGraphQL($produit);
                        if ($produitTraite) {
                            $this->sauvegarderProduit($produitTraite);
                            $compteur++;
                            Log::info("Produit importé: {$produitTraite['nom']}");
                        }
                    } catch (\Exception $e) {
                        Log::error("Erreur lors du traitement du produit: " . $e->getMessage());
                    }
                }

                if ($totalCount && ($page * $pageSize) >= $totalCount) {
                    break;
                }

                if (count($produits) < $pageSize) {
                    break;
                }

                $page++;
                $this->attendreDelai();
            } while (true);

            return $compteur;
        } catch (\Exception $e) {
            Log::error("Erreur lors de l'importation: " . $e->getMessage());
            throw $e;
        }
    }
// Récupère les produits via l'API GraphQL de la SAQ avec pagination et filtres
    private function obtenirProduitsGraphQL($page = 1, $pageSize = 24, $categorie = null, &$totalCount = null)
    {
        try {
            $query = $this->construireQueryProduits();
            
            $filters = [
                [
                    'attribute' => 'categoryPath',
                    'eq' => $categorie ?? 'produits'
                ],
                [
                    'attribute' => 'availability_front',
                    'in' => [
                        'En ligne',
                        'En succursale',
                        'Disponible bientôt',
                        'Bientôt en loterie',
                        'En loterie'
                    ]
                ],
                [
                    'attribute' => 'visibility',
                    'in' => [
                        'Catalog',
                        'Catalog, Search'
                    ]
                ]
            ];

            // Si la catégorie contient des mots-clés de couleur/type, utiliser une recherche par phrase
            // car l'API SAQ pourrait ne pas supporter les chemins de catégorie spécifiques comme "produits/vin-rouge"
            $phrase = '';
            $categoryPathValue = $categorie ?? 'produits';
            
            if ($categorie) {
                $categorieLower = strtolower($categorie);
                
                // Si la catégorie contient des mots-clés, utiliser une recherche par phrase
                // et remettre le categoryPath à "produits" car les chemins spécifiques ne fonctionnent pas
                if (strpos($categorieLower, 'rouge') !== false || strpos($categorieLower, 'vin-rouge') !== false) {
                    $phrase = 'vin rouge';
                    $categoryPathValue = 'produits';
                } elseif (strpos($categorieLower, 'blanc') !== false || strpos($categorieLower, 'vin-blanc') !== false) {
                    $phrase = 'vin blanc';
                    $categoryPathValue = 'produits';
                } elseif (strpos($categorieLower, 'rosé') !== false || strpos($categorieLower, 'rose') !== false || strpos($categorieLower, 'vin-rosé') !== false || strpos($categorieLower, 'vin-rose') !== false) {
                    $phrase = 'vin rosé';
                    $categoryPathValue = 'produits';
                } elseif (strpos($categorieLower, 'champagne') !== false) {
                    $phrase = 'champagne';
                    $categoryPathValue = 'produits';
                } elseif (strpos($categorieLower, 'spiritueux') !== false) {
                    $phrase = 'spiritueux';
                    $categoryPathValue = 'produits';
                }
            }
            
            // Mettre à jour le filtre categoryPath avec la valeur correcte
            $filters[0]['eq'] = $categoryPathValue;
            
            $variables = [
                'phrase' => $phrase,
                'pageSize' => $pageSize,
                'currentPage' => $page,
                'filter' => $filters,
                'sort' => [
                    [
                        'attribute' => 'price',
                        'direction' => 'DESC'
                    ]
                ],
                'context' => [
                    'customerGroup' => 'b6589fc6ab0dc82cf12099d1c2d40ab994e8410c',
                    'userViewHistory' => []
                ]
            ];

            $response = $this->faireRequeteGraphQL($query, $variables);
            $data = json_decode($response, true);

            if (isset($data['errors'])) {
                Log::error("Erreurs GraphQL", ['errors' => $data['errors'], 'full_response' => $data]);
                return [];
            }
            // Vérifier la structure de la réponse
            if (isset($data['data']['productSearch'])) {
                $productSearch = $data['data']['productSearch'];
                if (isset($productSearch['total_count'])) {
                    $totalCount = (int) $productSearch['total_count'];
                }
                if (isset($productSearch['items']) && is_array($productSearch['items'])) {
                    Log::info("Produits récupérés", [
                        'count' => count($productSearch['items']),
                        'total_count' => $productSearch['total_count'] ?? null,
                        'first_item_keys' => !empty($productSearch['items'][0]) ? array_keys($productSearch['items'][0]) : []
                    ]);
                    return $productSearch['items'];
                }
            }

            Log::warning("Structure de réponse GraphQL inattendue", [
                'response' => $data, 
                'keys' => isset($data['data']) ? array_keys($data['data']) : [],
                'errors' => $data['errors'] ?? null
            ]);
            return [];
        } catch (\Exception $e) {
            Log::error("Erreur lors de la requête GraphQL: " . $e->getMessage());
            return [];
        }
    }

    private function construireQueryProduits()
    {
        return <<<'GRAPHQL'
query productSearch(
  $phrase: String!
  $pageSize: Int
  $currentPage: Int = 1
  $filter: [SearchClauseInput!]
  $sort: [ProductSearchSortInput!]
  $context: QueryContextInput
) {
  productSearch(
    phrase: $phrase
    page_size: $pageSize
    current_page: $currentPage
    filter: $filter
    sort: $sort
    context: $context
  ) {
    total_count
    items {
      product {
        sku
        name
        canonical_url
        image {
          url
        }
        small_image {
          url
        }
        price_range {
          minimum_price {
            regular_price {
              value
              currency
            }
          }
        }
        description {
          html
        }
        short_description {
          html
        }
      }
      productView {
        sku
        name
        urlKey
        images {
          url
          label
          roles
        }
        attributes {
          name
          value
        }
      }
    }
    page_info {
      current_page
      page_size
      total_pages
    }
  }
}
GRAPHQL;
    }

    private function faireRequeteGraphQL($query, $variables = [])
    {
        $this->attendreDelai();

        try {
            $payload = [
                'query' => $query,
                'variables' => $variables,
            ];

            $headers = [
                'X-Request-Id' => $this->genererRequestId(),
            ];

            Log::debug("Requête GraphQL", ['endpoint' => $this->graphqlEndpoint, 'query' => $query, 'variables' => $variables]);

            $response = $this->client->post($this->graphqlEndpoint, [
                'json' => $payload,
                'headers' => $headers,
            ]);

            $corps = (string) $response->getBody();
            $this->derniereRequete = time();

            Log::debug("Réponse GraphQL", ['status' => $response->getStatusCode(), 'body_length' => strlen($corps)]);

            return $corps;
        } catch (\Exception $e) {
            $message = $e->getMessage();
            if (method_exists($e, 'getResponse') && $e->getResponse()) {
                $body = (string) $e->getResponse()->getBody();
                Log::error("Erreur lors de la requête GraphQL", [
                    'message' => $message,
                    'response_body' => $body,
                    'status_code' => $e->getResponse()->getStatusCode(),
                ]);
            } else {
                Log::error("Erreur lors de la requête GraphQL: " . $message);
            }
            throw $e;
        }
    }

    private function traiterProduitGraphQL($produitData)
    {
        $produit = $produitData['product'] ?? [];
        $productView = $produitData['productView'] ?? [];
        
        $codeSaq = $produit['sku'] ?? $productView['sku'] ?? null;
        if (!$codeSaq) {
            return null;
        }

        $nom = $produit['name'] ?? $productView['name'] ?? 'Produit sans nom';
        
        $prix = 0;
        if (isset($produit['price_range']['minimum_price']['regular_price']['value'])) {
            $prix = (float) $produit['price_range']['minimum_price']['regular_price']['value'];
        } elseif (isset($productView['price']['regular']['amount']['value'])) {
            $prix = (float) $productView['price']['regular']['amount']['value'];
        }
        
        $description = '';
        if (isset($produit['description']['html'])) {
            $description = strip_tags($produit['description']['html']);
        } elseif (isset($produit['short_description']['html'])) {
            $description = strip_tags($produit['short_description']['html']);
        }
        
        $urlImage = null;
        if (isset($produit['image']['url'])) {
            $urlImage = $produit['image']['url'];
        } elseif (isset($produit['small_image']['url'])) {
            $urlImage = $produit['small_image']['url'];
        } elseif (isset($productView['images'][0]['url'])) {
            $urlImage = $productView['images'][0]['url'];
        }
        
        $attributes = [];
        if (isset($productView['attributes']) && is_array($productView['attributes'])) {
            foreach ($productView['attributes'] as $attr) {
                if (isset($attr['name']) && isset($attr['value'])) {
                    $attributes[$attr['name']] = $attr['value'];
                }
            }
        }
        
        $volume = $attributes['format_contenant_ml'] ?? '750';
        $volume = $volume ? $volume . ' ml' : '750 ml';
        
        $millesime = null;
        if (isset($attributes['millesime_produit']) && is_numeric($attributes['millesime_produit'])) {
            $millesime = (int) $attributes['millesime_produit'];
        }
        
        $region = $attributes['region_origine'] ?? $attributes['appellation'] ?? null;
        $pays = $attributes['pays_origine'] ?? null;
        
        $typeVin = null;
        if (isset($attributes['couleur'])) {
            $couleur = strtolower($attributes['couleur']);
            if (strpos($couleur, 'rouge') !== false) {
                $typeVin = 'Rouge';
            } elseif (strpos($couleur, 'blanc') !== false) {
                $typeVin = 'Blanc';
            } elseif (strpos($couleur, 'rosé') !== false || strpos($couleur, 'rose') !== false) {
                $typeVin = 'Rosé';
            } elseif (isset($attributes['identite_produit'])) {
                $identite = strtolower($attributes['identite_produit']);
                if (strpos($identite, 'champagne') !== false) {
                    $typeVin = 'Champagne';
                } elseif (strpos($identite, 'spiritueux') !== false) {
                    $typeVin = 'Spiritueux';
                }
            }
        }

        $cheminImage = null;
        if ($urlImage) {
            $cheminImage = $this->telechargerImage($urlImage, $codeSaq);
        }

        return [
            'code_saQ' => (string) $codeSaq,
            'nom' => $nom,
            'prix' => $prix,
            'description' => $description,
            // Stocker le chemin avec un slash initial pour la compatibilité avec asset()
            // Stocker le chemin relatif sans /storage/ au début (ex: 'products/produit_xxx.jpg')
            // Le composant utilisera asset('storage/' . $urlImage) pour générer l'URL complète
            'url_image' => $cheminImage ?: ($urlImage ?: null),
            'volume' => $volume,
            'millesime' => $millesime,
            'region' => $region,
            'pays_nom' => $pays,
            'type_vin_nom' => $typeVin,
            'date_import' => now(),
        ];
    }

    private function extrairePrixProduit($produitData)
    {
        $prix = $produitData['price'] ?? $produitData['prix'] ?? $produitData['currentPrice'] ?? null;

        if (is_array($prix)) {
            $prix = $prix['value'] ?? $prix['amount'] ?? null;
        }

        if ($prix === null) {
            return 0;
        }

        return (float) $prix;
    }

    private function determinerTypeVin($produitData)
    {
        $texte = strtolower(($produitData['name'] ?? '') . ' ' . ($produitData['description'] ?? '') . ' ' . ($produitData['type'] ?? ''));

        if (strpos($texte, 'rouge') !== false || strpos($texte, 'red') !== false) {
            return 'Rouge';
        }
        if (strpos($texte, 'blanc') !== false || strpos($texte, 'white') !== false) {
            return 'Blanc';
        }
        if (strpos($texte, 'rosé') !== false || strpos($texte, 'rose') !== false) {
            return 'Rosé';
        }
        if (strpos($texte, 'champagne') !== false) {
            return 'Champagne';
        }
        if (strpos($texte, 'spiritueux') !== false || strpos($texte, 'spirit') !== false) {
            return 'Spiritueux';
        }

        $typeExplicite = $produitData['type'] ?? $produitData['category'] ?? null;
        if ($typeExplicite) {
            return ucfirst(strtolower($typeExplicite));
        }

        return null;
    }

    /**
     * Télécharge une image depuis une URL et la sauvegarde dans le stockage public.
     * 
     * Gère la normalisation des URLs (ajout du domaine de base si nécessaire,
     * suppression des doublons de domaine) et optimise les images swatch pour
     * obtenir une meilleure résolution.
     * 
     * Si l'image existe déjà dans le stockage local, elle n'est pas re-téléchargée
     * pour éviter les téléchargements inutiles lors des mises à jour du catalogue.
     * 
     * @param string|null $urlImage URL de l'image à télécharger
     * @param string $codeSaq Code SAQ du produit pour générer le nom de fichier
     * @return string|null Chemin relatif de l'image sauvegardée (ex: 'products/produit_12345.jpg') 
     *                     ou null en cas d'échec
     */
    private function telechargerImage($urlImage, $codeSaq)
    {
        try {
            if (empty($urlImage)) {
                Log::debug("Image URL vide pour produit {$codeSaq}");
                return null;
            }

            // Sauvegarder l'URL originale pour le débogage
            $originalUrl = $urlImage;
            
            // Normaliser l'URL - gérer les cas où l'URL pourrait déjà être complète ou avoir des doublons
            if (!preg_match('/^https?:\/\//', $urlImage)) {
                // Pas une URL complète, ajouter l'URL de base
                if (strpos($urlImage, '/media/') === 0) {
                    $urlImage = $this->baseUrl . $urlImage;
                } else {
                    $urlImage = $this->baseUrl . '/' . ltrim($urlImage, '/');
                }
            }
            
            // Supprimer les doublons de domaine si ils ont été ajoutés par erreur
            // (corrige le bug: https://www.saq.com/www.saq.com/...)
            $urlImage = preg_replace('#https?://www\.saq\.com/www\.saq\.com/#', 'https://www.saq.com/', $urlImage);
            
            // Extraire l'extension du fichier depuis le chemin de l'URL
            $cheminUrl = parse_url($urlImage, PHP_URL_PATH);
            $extension = pathinfo($cheminUrl, PATHINFO_EXTENSION) ?: 'jpg';
            
            // Optimiser les images swatch pour obtenir une meilleure résolution
            // Remplace les miniatures 30x30 par des versions 500x500
            if (strpos($urlImage, '/media/attribute/swatch/swatch_image/') !== false) {
                $urlImage = str_replace('/30x30/', '/500x500/', $urlImage);
            }

            // Générer un nom de fichier sécurisé basé sur le code SAQ
            $nomFichier = 'produit_' . preg_replace('/[^a-zA-Z0-9]/', '_', $codeSaq) . '.' . $extension;
            $chemin = 'products/' . $nomFichier;

            // Vérifier si l'image existe déjà dans le stockage local
            // Si elle existe, on retourne le chemin sans re-télécharger pour optimiser les performances
            if (Storage::disk('public')->exists($chemin)) {
                Log::debug("Image déjà existante pour produit {$codeSaq}, téléchargement ignoré: {$chemin}");
                return $chemin;
            }

            Log::debug("Téléchargement image pour produit {$codeSaq}", [
                'original_url' => $originalUrl,
                'final_url' => $urlImage,
                'filename' => $nomFichier
            ]);

            // Télécharger l'image via HTTP avec un timeout de 15 secondes
            $reponseImage = $this->client->get($urlImage, [
                'timeout' => 15,
            ]);
            
            $contenuImage = $reponseImage->getBody()->getContents();
            
            // Vérifier que le contenu téléchargé n'est pas vide
            if (strlen($contenuImage) > 0) {
                // Sauvegarder l'image dans le disque public
                Storage::disk('public')->put($chemin, $contenuImage);
                Log::info("Image téléchargée avec succès pour produit {$codeSaq}: {$chemin}");
                return $chemin;
            }

            Log::warning("Image vide téléchargée pour produit {$codeSaq} depuis {$urlImage}");
            return null;
        } catch (\Exception $e) {
            // Logger les erreurs de téléchargement avec le contexte complet
            Log::error("Erreur lors du téléchargement de l'image pour produit {$codeSaq}", [
                'url' => $urlImage ?? $originalUrl ?? 'inconnue',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    private function sauvegarderProduit($donneesProduit)
    {
        $pays = null;
        if (!empty($donneesProduit['pays_nom'])) {
            $pays = Pays::firstOrCreate(['nom' => $donneesProduit['pays_nom']]);
        }

        $typeVin = null;
        if (!empty($donneesProduit['type_vin_nom'])) {
            $typeVin = TypeVin::firstOrCreate(['nom' => $donneesProduit['type_vin_nom']]);
        }

        BouteilleCatalogue::updateOrCreate(
            ['code_saQ' => $donneesProduit['code_saQ']],
            [
                'nom' => $donneesProduit['nom'],
                'prix' => $donneesProduit['prix'],
                'url_image' => $donneesProduit['url_image'],
                'volume' => $donneesProduit['volume'],
                'millesime' => $donneesProduit['millesime'],
                'region' => $donneesProduit['region'],
                'id_pays' => $pays ? $pays->id : null,
                'id_type_vin' => $typeVin ? $typeVin->id : null,
                'date_import' => $donneesProduit['date_import'],
            ]
        );
    }

    private function attendreDelai()
    {
        $tempsActuel = time();
        $tempsDepuisDerniereRequete = $tempsActuel - $this->derniereRequete;

        if ($tempsDepuisDerniereRequete < $this->delaiRequete) {
            sleep($this->delaiRequete - $tempsDepuisDerniereRequete);
        }

        $this->derniereRequete = time();
    }
}
