# BBMValidator version 1
Il s'agit d'une petite librairie pour faire la validation des données.

## Installation

Importer le fichier `validator.php` dans le fichier php où 
l'on souhaite faire la validation. 

`require "chemin_vers_le_fichier/validator.php";`

## Règles 

| Règle    | Paramètres                      | Description                                                            | Exemple                                                            |
|----------|---------------------------------|------------------------------------------------------------------------|:-------------------------------------------------------------------|
| required | ...$keys                        | Valider les champs obligatoires                                        | `$validator($params)->required("champs1","champs2");`              |
| notEmpty | ...$keys                        | Valider les champs non vides                                           | `$validator($params)->notEmpty("champs1","champs2");`              |
| length   | $key,<br/>$min,<br/>$max=null   | Définir la taille d'une chaine de caractères                           | `$validator($params)->length("champs",3,4);`                       |
| number   | ...$keys                        | Valider les champs de type numérique                                   | `$validator($params)->number("champs1","champs2");`                |
| dateTime | $key,<br/>$format="Y-m-d H:i:s" | Valider les champs de type datetime en précisant<br/>le format de date | `$validator($params)->dateTime("champs","Y-m-d");`                 |
| slug     | $key                            | Valider un slug                                                        | `$validator($params)->slug("champs");`                             |
| phone    | $key                            | Valider un numéro de téléphone                                         | `$validator($params)->phone("champs");`                            |
| email    | $key                            | Valider un email                                                       | `$validator($params)->email("champs");`                            |
| enum     | $key, $values                   | Valider une enumération                                                | `$validator($params)->enum("champs",[0,1]);`                       |
| between  | $key,<br/>$min,<br/>$max=null   | Valider si champ est compris dans un  intervalle                       | `$validator($params)->between("champs",1,10);`                     |
| match    | $key,<br/>$pattern              | Valider un champ avec une expression régulière                         | `$validator($params)->match("champs","#^([-_/ ]?[0-9]{2}){4}$#");` |


**NB :** Après avoir défini les règles, on fait appel à la méthode isValid() pour effectuer la validation dans un bloc 

`try{ 
//instructions de validation 
} catch (Exception $e)
{ 
return $e->getMessage()
}`

## Cas d'utilisation


Supposons qu'on ait un formulaire qui sert à donner les informations d'un utilisateur
ayant un _**nom**_, un _**prénom**_, un **_numéro_** de téléphone, un **_email_**.

La validation de ce formulaire pourra s'effectuer de la manière suivante :

### Instanciation de la class validator

`$validator = new BBMValidator($params);`

`$params` peut être la variable `$_POST` ou `$_GET` par exemple.

### Définition des règles 
`$validator
->required('nom','prenom','numero','email')
->notEmpty('nom','prenom','numero','email')
->phone('numero')
->email('email');`

### Validation 

`try{
    $validator->isValid();
}catch(Exception $exception)
{
    return $exception->getMessage();
}`

