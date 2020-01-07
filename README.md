# dolibar-stock-manager
Inventory manager for Dolibarr

## Objectives
* Make a tool to easily add or remove products from a stock on Dolibarr.
* Randomly this tool has to ask the remaining quantity for one product.

## Technical point of view 

This project uses [Symfony 5](http://symfony.com/), everything is not yet clean. Feel free to help on the project.  

### Storage
There is no DB, instead small data are stored in JSON files.
* The number of modifications by products
* The next random check.

The name of those files are generated with the index of the element and the name of the object (eg. products_42). 
The extension is always ".json".

Like that we will have two kind of names: 
* products_ _{the id of the product}_ : stores the number of modifications for one product.
* inventories_  _{the id of the product}_ : stores the number of modifications before asking the number of remaining products.

### Unit testing
I didn't add any test yet! I know it's bad but I will add them as soon as possible. 

## Contributing

### Gitmoji
To directly see what does one commit, I use [Gitmoji](https://gitmoji.carloscuesta.me/).

