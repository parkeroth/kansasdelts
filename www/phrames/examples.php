<?php

  require_once("phrames/Model.class.php");
  
  /**
   * CREATE TABLE `manufacturers` (
   * `id` int(11) NOT NULL AUTO_INCREMENT,
   * `name` varchar(250) DEFAULT NULL,
   * PRIMARY KEY (`id`)
   * );
   */
  class Manufacturer extends Model {
    
    const products = "Product:";

    public function __toString() {
      return "{$this->name}";
    }

  }

  /**
   *  CREATE TABLE `products` (
   * `id` int(11) NOT NULL AUTO_INCREMENT,
   * `manufacturer` int(11) DEFAULT NULL,
   * `name` varchar(250) DEFAULT NULL,
   * `weight` int(11) DEFAULT NULL,
   * `cost` int(11) DEFAULT NULL,
   * PRIMARY KEY (`id`)
   * );
   */
  class Product extends Model {
    
    const manufacturer = "Manufacturer";
    
    public function __toString() {
      return "{$this->name}";
    }

  }
  
  $products = Product::objects()->filter(manufacturer__name__contains("s"));
  
  foreach($products as $product) {
    print $product->name . "<br />";
  }
  
  // etc, see andrewfreiday.com blog entry for more examples
  // or feel free to browse the phrames/ directory source for
  // possible methods and functionality