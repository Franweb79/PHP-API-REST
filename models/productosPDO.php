
<?php

require_once('ConnectPDO.php');


class ProductosPDO{

    //public $id_producto;

    //better make then private and access though setters and getters
    public $nombre;
    public $descripcion;
    public $precio;
    public $imagen;

    function __construct()
    {
        $this->nombre=null;
        $this->descripcion="";
        $this->precio=null;
        $this->imagen =null;
    }

    //LISTAR TODOS

    public function getProducts()
    {

        $conexion=new ConnectionDataBasePDO();

        $conexion->openConnectionPDO();

        $sql="SELECT * FROM productos";
        
        $sth=$conexion->connection->prepare($sql);

        

        //execute statement

        $sth->execute();


        // Set fetch mode to FETCH_ASSOC to return an array indexed by column name

        $sth->setFetchMode(PDO::FETCH_ASSOC);

       
         /*fetchall() returns all rows; fetch() only one*/
        $result=$sth->fetchall();


        if($result ==null)
        {
           
            $result=Array(

                'status' => 'error',
                'responseCode' => 404,
                'message'=>'No products yet!!!!'
            );
            
            

        }
     

        $conexion->closeConnectionPDO();


        return $result;
    }

    //LISTAR UNO

    public function getProductById($p_idProducto)
    {
        $conexion=new ConnectionDataBasePDO();

        $conexion->openConnectionPDO();

        $sql="SELECT * FROM productos WHERE id_producto=:id_producto";

        //echo $sql;

        $sth=$conexion->connection->prepare($sql);



        $sth->bindParam(':id_producto',$p_idProducto);

      
        
        //execute statement

        $sth->execute();

        $sth->setFetchMode(PDO::FETCH_ASSOC);

        /*fetchall() returns all rows; fetch() only one*/
        $result=$sth->fetch();
        



        if($result==null)
        {
          
            $toShow=Array(

                'status' => 'error',
                'responseCode' => 404,
                'message'=>'No product with this ID'
            );
           

        }
        else
        {
            
            $toShow=Array(

                'status' => 'success',
                'responseCode' => 200,
                'message'=>$result
            );
        }

        $conexion->closeConnectionPDO();


        return $toShow;

    }

    //ELIMINAR PRODUCTO

    public function deleteProductById($p_idProducto)
    {
        try{



            $conn=new ConnectionDataBasePDO();

            $conn-> openConnectionPDO();

           
    
    
            $sql="DELETE FROM productos where id_producto=:id_producto";
    
            $sth=$conn->connection->prepare($sql);
    
            
            $sth->bindParam(':id_producto',$p_idProducto);
            
            //execute statement
           
             $sth->execute();

             /*I can check affected rows by the execute method with rowCount()

             https://stackoverflow.com/questions/44305738/deleting-record-if-it-exists-in-php-pdo

             */

             $count = $sth->rowCount();// check affected rows using rowCount
             if ($count > 0) {
                 echo 'Success - The record has been deleted.';
             } else {
                 echo "No product with that ID, so could not delete";
             }

          
             $conn->closeConnectionPDO();
        }

        catch(PDOException $e){
    
            echo "deletion failed: " . $e->getMessage();

        }
        
    }




    //METER PRODUCTO
    public function insertProduct($p_nombre,$p_descripcion,$p_precio,$p_imagen)
    {
        try{



                $conn=new ConnectionDataBasePDO();

                $conn-> openConnectionPDO();
        
        
                $sql="INSERT INTO productos (nombre,descripcion,precio,imagen) VALUES (:nombre,:descripcion,:precio,:imagen)";
        
                $sth=$conn->connection->prepare($sql);
        
                
                $sth->bindParam(':nombre',$p_nombre);
        
                $sth->bindParam(':descripcion',$p_descripcion);
        
                $sth->bindParam(':precio',$p_precio);
        
                $sth->bindParam(':imagen',$p_imagen);
        
        
            //execute statement
    
            if($sth->execute())//execute returns true if everithing is ok
            {
                
                
                $result=array(

                    'status' => 'Created',
                    'responseCode' => 201,//succesfull connection and created new resource
                    'message'=> 'successfully inserted'
                );
            }
            else
            {
               
                
                $result=array(

                    'status' => 'Not found',
                    'responseCode' => 404,
                    'message'=>'couldn´t create the product'
                );
            }
    
           
           


            return $result;

            
    
    
    
            $conn->closeConnectionPDO();
        }

        catch(PDOException $e){
    
            echo "insertion failed: " . $e->getMessage();

        }
        
        
    }

    //ACTUALIZAR PRODUCTO

    public function updateProductById($p_idProducto,$p_newJSONDataToInsert)
    {
        try{

                //check if product exists
           
                $objProductoToCheckIfExistsThisOne=new ProductosPDO();

                $resultOfCheckingIfProductExists=$objProductoToCheckIfExistsThisOne->getProductById($p_idProducto);

           

            
                // $resultOfCheckingIfProductExists=json_encode($resultOfCheckingIfProductExists);

                //can´t do json_encode and access it through php! php can´t do that! we read directly the array returned by getProductById
            
                if($resultOfCheckingIfProductExists['responseCode']==404)
                {
                    echo "no existe";
                }
                if($resultOfCheckingIfProductExists['responseCode']==200)
                {
                //if product exists, we will check if fields are empty on new data,
                //then those fields on new data will be the ones from old data

                    $jsonDataDecoded=json_decode($p_newJSONDataToInsert,true);


                    /*conditions on variables to make more readable code*/

                    /*if not set data by any reason, or set (form comes) but are only empty spaces -ctype_space==true- or string is empty (0 length), then we take old values */
                    $isNameValid=!isset($jsonDataDecoded['nombre']) || (isset($jsonDataDecoded["nombre"]) && ctype_space($jsonDataDecoded["nombre"])  ) || (isset($jsonDataDecoded["nombre"]) && strlen($jsonDataDecoded["nombre"])==0 );
                    
                    
                    $isDescriptionValid= !isset($jsonDataDecoded['descripcion']) || (isset($jsonDataDecoded["descripcion"]) && ctype_space($jsonDataDecoded["descripcion"]) ) || (isset($jsonDataDecoded["descripcion"]) && strlen($jsonDataDecoded["descripcion"])==0 );


                    $isPriceValid=!isset($jsonDataDecoded['precio']) || (isset($jsonDataDecoded["precio"]) && ctype_space($jsonDataDecoded["precio"]) ) || (isset($jsonDataDecoded["precio"]) && strlen($jsonDataDecoded["precio"])==0 );





                    
                    if( $isNameValid==1 )
                    {

                            $jsonDataDecoded['nombre']=$resultOfCheckingIfProductExists["message"]["nombre"];
                    }

                    if( $isDescriptionValid==1 )
                    {

                            $jsonDataDecoded['descripcion']= "No description available";
                            
                            //$resultOfCheckingIfProductExists["message"]["descripcion"];
                    }

                    if($isPriceValid==1)
                    {

                            $jsonDataDecoded['precio']=0;
                            
                            //$resultOfCheckingIfProductExists["message"]["precio"];
                    }

                    if(!isset($jsonDataDecoded['imagen']))
                    {

                            $jsonDataDecoded['imagen']=$resultOfCheckingIfProductExists["message"]["imagen"];
                    }

                    //var_dump($jsonDataDecoded);

                    /*now with all data properly set, we can update:*/

                    $conn=new ConnectionDataBasePDO();

                    $conn-> openConnectionPDO();

                    $sql="UPDATE productos
                    SET nombre=:nombre,
                        descripcion=:descripcion,
                        precio=:precio,
                        imagen=:imagen
                    WHERE id_producto=:id_producto;";

                    
                    $sth=$conn->connection->prepare($sql);

                    //bind parameters to statement variables
 
                    $sth->bindParam(':nombre', $jsonDataDecoded['nombre']);

                    $sth->bindParam(':descripcion',$jsonDataDecoded['descripcion']);

                    $sth->bindParam(':precio', $jsonDataDecoded['precio']);

                    $sth->bindParam(':imagen',  $jsonDataDecoded['imagen']);

                    $sth->bindParam(':id_producto',$p_idProducto);

                    //execute statement
 
                    //if everything is ok
                    if($sth->execute())
                    {
                        /*we get it again with new data to show it*/
                        
                        $newProduct=$objProductoToCheckIfExistsThisOne->getProductById($p_idProducto);
                        
                        $toShow=Array(

                            'status' => 'ok',
                            'responseCode' => 200,
                            'message'=>$newProduct["message"]
                        );
                    }
                    else{

                        $toShow=array(

                            'status' => 'error',
                            'responseCode' => 404,
                            'message'=>'couldn´t update the product'
                        );
                    }

        
                    echo json_encode($toShow);
        
                    $conn->closeConnectionPDO();
 

                    
                }
            



          


        }
        catch(PDOException $e){
    
            echo "update failed: " . $e->getMessage();

        }
    }

    //subir imagen

    public function uploadFile()
    {
        
        //var_dump($_FILES);
        //die();
        if(isset($_FILES['filesUploaded']))
        {
            //echo "existe el archivo";

            $piramideUploader=new PiramideUploader();

            $response=$piramideUploader->upload("image-curso",'filesUploaded',"assets/uploads/images",array("image/jpeg","image/png","image/gif"));
        
            $fileInfo=$piramideUploader->getInfoFile();

            //echo $fileInfo["name"];

           

            //echo json_encode($fileInfo);

            echo json_encode($fileInfo);  
        
        }
        else
        {
            $toShow= array('status' => 'FAIL' ,
                            'responseCode' => 404,
                            'messagee' => 'no file uploaded'
        
                            );

            echo json_encode($toShow);
        }
    }

}


?>