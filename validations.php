<?php

    function isDescriptionEmpty($p_description)//jsondatadecodes[descriptcon]will be parameter passed
    {
        if( (!isset($p_description) || ( isset($p_description) && ctype_space($p_description) ) || (isset($p_description) && strlen($p_description)==0 ))  )
        {
            
            
            return true;
        }
        else
        {   
           
           
            return false;
        }
        
    }

    

    


?>