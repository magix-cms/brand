# Brand
Plugin brand for Magix CMS 3

Ajoute une gestion des marques au catalogue de votre site.

## Installation
 * Décompresser l'archive dans le dossier "plugins" de magix cms
 * Connectez-vous dans l'administration de votre site internet
 * Cliquer sur l'onglet plugins du menu déroulant pour sélectionner thematic.
 * Une fois dans le plugin, laisser faire l'auto installation
 * Il ne reste que la configuration du plugin pour correspondre avec vos données.
 * Copier le contenu du dossier skin/public dans le dossier de votre skin.

## Afficher la marque dans le produit
Ajouter la ligne suivante dans le tpl du produit où vous souhaitez afficher la marque
````
{include file="brand/brick/product-brand.tpl" brand=$product.brand}
````

## Afficher les produits de la même marque dans le produit
Ajouter la ligne suivante dans le tpl du produit où vous souhaitez afficher les produits de la même marque
````
{include file="brand/brick/same-brand.tpl" product=$product}
````