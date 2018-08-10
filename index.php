<?php


    require_once 'models/productosPDO.php';

    require_once 'vendor/autoload.php';

    require_once ('validations.php');

    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    header("Allow: GET, POST, OPTIONS, PUT, DELETE");
    $method = $_SERVER['REQUEST_METHOD'];
    if($method == "OPTIONS") {
            die();
    }

    

    $app=new \Slim\Slim();

    
   // $conn=new ConnectionDataBasePDO();

   $objProducto=new  ProductosPDO();

   $imageData="";




    $app->get("/prueba",function()  use($app){

    

      
       // echo "hola mundo desde slim";

        
    });

    $app->get("/prueba2",function()  use($app){

       // echo "p2";
     });

    $app->get("/",function()  use($app){

        //echo "hola index";
     });




     //PATH TO INSERT PRODUCTS

     $app->post("/crear-producto",function() use($app,$objProducto,$imageData){

        
        
        
        
      $json=$app->request->getBody();

       

       
        // $json= $app->request->post("json");

       


       
        
      $dataDecoded=json_decode($json,true);

    
      /*if no image set when creating product, we load default image before we insert*/
      if(!isset($dataDecoded['imagen']))
      {
        $dataDecoded['imagen']="default.png";
      }

        
      //this time is different than in updateProduct. Name can´t be left empty this time, so is only valid if length is bet.3 and 15.

      //this returns 1 when true, and nothing when false
      
      $isNameValid=(strlen($dataDecoded['nombre'])>=3) && (strlen($dataDecoded['nombre'])<=15);


     

      //the other text field can be empty, so we set a default text in case empty is true inside insertProduct,
      //and here we consider it valid (we have to use the isTextInputFieldEmpty method) since we have default values to insert in case this field is empty "(no description available")



      $isPriceValid=(  isTextInputFieldEmpty( $dataDecoded['precio'] )  ) ||  ( $dataDecoded['precio'] >=0 );

      $isDescriptionValid=(  isTextInputFieldEmpty( $dataDecoded['descripcion'] )  ) ||  (  (strlen($dataDecoded['descripcion'])>=10) && ( strlen($dataDecoded['descripcion'])<=300 ) );

      $isImageValid=( (($dataDecoded['imagen'] != "") && ($dataDecoded['imagen'] != NULL)) );

      
      
     
      if( $isNameValid==1 && $isPriceValid==1 && $isDescriptionValid==1 && $isImageValid==1)
      {

        $result= $objProducto->insertProduct($dataDecoded["nombre"],$dataDecoded["descripcion"],$dataDecoded["precio"], $dataDecoded['imagen']);

        echo json_encode($result);//need to catch the response, por que return no vale y echo si
      }



      else{ /*if data is not valid*/

         $result=Array(

            'status'=>'No Content',
            'responseCode' => 204,
            'message'=>'Connection with server established, but data sent by the client was not valid'

         );

         echo json_encode($result);
      }
      







    
     });

     //PATH TO UPDATE PRODUCTS

     $app->post("/productos/:id_producto",function($id_producto) use($app,$objProducto){

        //$newJSONData=$app->request->post("json");

        $newJSONData=$app->request->getBody();

        $dataDecoded=json_decode($newJSONData,true);

       

        /*we check if fields are empty to set a default text (thats why "empty" would be considered valid here) or valid length to store user given text*/

        $isNameValid=(  isTextInputFieldEmpty( $dataDecoded['nombre'] )  ) ||  (  (strlen($dataDecoded['nombre'])>=3) && (strlen($dataDecoded['nombre'])<=15) );

        $isPriceValid=(  isTextInputFieldEmpty( $dataDecoded['precio'] )  ) ||  ( $dataDecoded['precio'] >=0 );

        $isDescriptionValid=(  isTextInputFieldEmpty( $dataDecoded['descripcion'] )  ) ||  (  (strlen($dataDecoded['descripcion'])>=10) && ( strlen($dataDecoded['descripcion'])<=300 ) );

        $isImageValid=(!isset($dataDecoded['imagen']) || (($dataDecoded['imagen'] != "") && ($dataDecoded['imagen'] != NULL)) );

       
       
        if($isDescriptionValid==1 && $isNameValid==1 && $isPriceValid==1 && $isImageValid==1)
        {
            /*inside this update, we will check again conditions above to set default text/values or user given text/vañues*/
            
            $objProducto->updateProductById($id_producto,$dataDecoded);

        }

       
        


     });


     //PATH TO GET PRODUCTS

     $app->get("/productos",function() use($app,$objProducto){


        $resultados=$objProducto->getProducts();

        $resjson=json_encode($resultados);

      
        echo $resjson;
     });




     //PATH TP GET ONLY ONE PRODUCT

     $app->get("/productos/:id_producto",function($id_producto) use($app,$objProducto){

      /*in slim 2, we can embed route parameters like so:

      https://docs.slimframework.com/routing/params/


      <?php
            $app = new \Slim\Slim();
            $app->get('/books/:one/:two', function ($one, $two) {
                echo "The first parameter is " . $one;
                echo "The second parameter is " . $two;
        
            }); 
        ?>

      */

        
        $resultados=$objProducto->getProductById($id_producto);

        $resjson=json_encode($resultados);

        echo $resjson;
     });


     /*PATH TO DELETE PRODUCTS. We won´t use DELETE HTTP verb because it could give configuration problems
     with Apache, and in real life mostly GET and POST are only used*/

     $app->get("/delete-productos/:id_producto",function($id_producto) use($app,$objProducto){

           $objProducto->deleteProductById($id_producto);

            
        }

     );

     //path to upload images

     $app->post("/upload-file",function() use($app,$objProducto,$imageData){

        
        
        
        //first, we upload it and we will pass the name to the database with the updateProduct method
        
        
        
        
        $imageData=$objProducto->uploadFile();

       // echo $imageData["complete_name"];

        $imageDataNameArray= Array(


                                'imagen' => $imageData["complete_name"]
                            );

       
        
        $imageDataNameJSON=json_encode($imageDataNameArray);

       return $imageDataNameJSON;



     });


     $app->post("/upload-file/:id_producto",function($id_producto) use($app,$objProducto){

        
        
        
        //first, we upload it and we will pass the name to the database with the updateProduct method
        
        
        
        
        $imageData=$objProducto->uploadFile();

       // echo $imageData["complete_name"];

        $imageDataNameArray= Array(


                                'imagen' => $imageData["complete_name"]
                            );

       
        
        $imageDataNameJSON=json_encode($imageDataNameArray);

        $objProducto->updateProductById($id_producto,$imageDataNameJSON);



     });

     
    $app->run();


?>