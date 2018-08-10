<?php

    function isTextInputFieldNotEmpty($p_stringToCheck)//jsondatadecodes[descriptcon]will be parameter passed
    {
        if( (!isset($p_stringToCheck) || ( isset($p_stringToCheck) && ctype_space($p_stringToCheck) ) || (isset($p_stringToCheck) && strlen($p_stringToCheck)==0 ))  )
        {
            
            
            return true;
        }
        else
        {   
           
           
            return false;
        }
        
    }

    function isFileInputFieldNotEmptyOrNull($p_fileDataToCheck)
    {
        
        if( (!isset($p_fileDataToCheck)) || ( isset($p_fileDataToCheck) &&  ($p_fileDataToCheck != "") && ($p_fileDataToCheck != NULL)) )
        {
            
            
            return true;
        }
        else
        {   
           
           
            return false;
        }

    }


    

    


?>