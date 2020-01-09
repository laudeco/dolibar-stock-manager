# dolibar-stock-manager
Inventory manager for Dolibarr.

## Objectives
the projects has two main objectives:
* Make a tool to easily add or remove products from a stock on Dolibarr.
* Randomly this tool has to ask the remaining quantity for one product.

The project does not require any user access, if you are known on the Dolibarr instance it's enough.
That being said, this project can be public on the internet since it does not expose any sensitive data to the user.
If you do not have any account on the Dolibarr instance you cannot have access to the products.

The project include one native barcode scanner. Like that from a mobile device it's possible to scan the barcode. Currently only the code 128 barcode.

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

### quaggaJS
Library that allows to scan the barcode (code 128) from the UI. This library and its documentation are available on [github](https://github.com/serratus/quaggaJS).

## Contributing

### Gitmoji
To directly see what does one commit, I use [Gitmoji](https://gitmoji.carloscuesta.me/).

