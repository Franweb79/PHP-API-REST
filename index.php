<?php


    require_once 'models/productosPDO.php';

    require_once 'vendor/autoload.php';

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

     $app->post("/crear-producto",function() use($app,$objProducto){

        //jason= $app->request->post("json");
        $json=$app->request->getBody();

        //var_dump($json);

        //die();

       /* var_dump($json);*/

        //$json=json_encode($json);

        //var_dump($json);

       
        
        $dataDecoded=json_decode($json,true);
        
        //var_dump($dataDecoded);

      // echo $stra=implode(" ",$dataDecoded ); 

     

        
        //json_decode($json,true);//el true hace que se nos convierta de un objeto a un array

      // die();
        
        if(!isset($dataDecoded['nombre']))
        {
            $dataDecoded['nombre']=NULL;
        }
        
        if(!isset($dataDecoded['descripcion']))
        {
            $dataDecoded['descripcion']="";
        }

        
        if(!isset($dataDecoded['precio']))
        {
            $dataDecoded['precio']=NULL;
        }


        if(!isset($dataDecoded['imagen']))
        {
            $dataDecoded['imagen']=NULL;
        }

        $objProducto->insertProduct($dataDecoded["nombre"],$dataDecoded["descripcion"],$dataDecoded["precio"], $dataDecoded['imagen']);


    
     });

     //PATH TO UPDATE PRODUCTS

     $app->post("/productos/:id_producto",function($id_producto) use($app,$objProducto){

        $newJSONData=$app->request->post("json");
        
        $objProducto->updateProductById($id_producto,$newJSONData);


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

     $app->post("/upload-file/:id_producto",function($id_producto) use($app,$objProducto){

        
        
        
        //first, we upload it and we will pass the name to the database with the updateFile method
        
        
        
        
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