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

## Known limitations
The stock manager doesn't support multiple warehouses. Currently, it's only in the config file that we hardcode the stock id.
  
The system doesn't support the serial numbers and the end date of a product.


## Installation

### Configuration
The configuration of the application is present in the _config/services.yaml_ file. 

```
parameters:

    dolibarr_uri: 'docker.for.mac.localhost/api/index.php/' # Define the URI to API.

    inventory_min: 1 #The min value for the random number before a new inventory check
    inventory_max: 1 #The max value for the random number before a new inventory check
    app_inventory_label: 'Correction du stock'  #Label for an inventory check movement
    app_mouvement_label: 'Mouvement'  #Default label for a movement
    app_stock_id: 1  #Warehouse ID
    app_barcode_type: 'code_128_reader' #The type of the barcode to choose between one of the supported type

    db_basepath: '../../data/nosql'  #The path for the storage data.
```

#### Supported barcode type
Currently, the library ([QuaggaJS](https://github.com/serratus/quaggaJS)) supports: 
* code_128_reader (default)
* ean_reader
* ean_8_reader
* code_39_reader
* code_39_vin_reader
* codabar_reader
* upc_reader
* upc_e_reader
* i2of5_reader
* 2of5_reader
* code_93_reader

### Production
To make the application production ready. Run install the dependencies:

```
composer install --no-dev
```

Change the `APP_ENV from the _.env_ file 
```
APP_ENV=prod
```


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

### QuaggaJS
Library that allows to scan the barcode from the UI. This library and its documentation are available on [github](https://github.com/serratus/quaggaJS).

## Contributions
If you have any idea to make the application better don't hesitate to propose it.

### Gitmoji
To directly see what does one commit, I use [Gitmoji](https://gitmoji.carloscuesta.me/).
