<?php

class DataConverter {

    function __construct()
    {
    }

    public function convertData(string|array|null $inputData, string $outputFormat = "", string $id = "", string $class = "", bool $numberedRows = false): string|array|null
    {       
        if (is_null($inputData))
        {
            exit("Nebyla vložena žádná data!");
        }
        
        if (is_string($inputData))
        {
            $isJson = json_decode($inputData, true);

            if (json_last_error() == JSON_ERROR_NONE) {
                $inputData = $isJson;
            }else{
                if (strpos($inputData,'.txt'))
                {
                    $table = [];

                    $fileToRead = fopen($inputData, "r") or exit("Není možné otevřít soubor!");
                    $row = fgets($fileToRead);
                    $row = str_replace("\t\r\n", "", $row);
                    $row = str_replace("\r\n", "", $row);
                    $keys = explode("\t", $row);
        
                    do
                    {
                        $row = fgets($fileToRead);
                        $row = str_replace("\t\r\n", "", $row);
                        $row = str_replace("\r\n", "", $row);
                        if (strlen($row))
                        {
                            $rowValues = explode("\t", $row);
                        }else{
                            $rowValues = [0,0,0,0,0];
                        }
                        
                        try
                        {
                            $array = array_combine($keys,$rowValues);
                            array_push($table, $array);
                        }catch(Error $e)
                        {
                            echo "Nastala chyba: " . $e->getMessage() . "<br>";
                        }
                    }while(!feof($fileToRead));
        
                    fclose($fileToRead);
                    $inputData = $table; 
                }else{
                    return null;
                }
            }
        }

        if ($outputFormat == 'ARRAY')
        {
            return $inputData;
        }

        if ($outputFormat == 'HTML')
        {
            if(is_null($inputData))
            {
                return "Nebyla načtena databázová tabulka.";
            }else{
                if(count($inputData))
                {
                    foreach($inputData as $rowToHtml)
                    {
                        $inputValuesArrayKeys = array_keys($rowToHtml);
                        break;
                    }
            
                    $htmlTable = "<table><thead><tr>";
                
                    if($class)
                    {
                        $htmlTable = substr_replace($htmlTable, ' class="' . $class . '"', 6, 0);  
                    }
                
                    if($id)
                    {
                        $htmlTable = substr_replace($htmlTable, ' id="' . $id . '"', 6, 0);  
                    }
            
                    if($numberedRows)
                    {
                        $htmlTable .= '<th></th>';
                    }
            
                    for ($i = 0; $i < count($inputValuesArrayKeys); $i++)
                    {
                        $htmlTable .= '<th>' . $inputValuesArrayKeys[$i] . '</th>';
                    }  
                
                    $htmlTable .= "</thead></tr><tbody>";
            
                    if($numberedRows)
                    {
                        $rowNumber = 1;
                    }
            
                    foreach ($inputData as $tableRow)
                    {
                        $htmlTable .= "<tr>";
                        if($numberedRows)
                        {
                            $htmlTable .= '<td>' . $rowNumber . '</td>';
                            $rowNumber++;
                        }
                        for ($i = 0; $i < count($inputValuesArrayKeys); $i++)
                        {
                            $htmlTable .= '<td>' . $tableRow[$inputValuesArrayKeys[$i]] . '</td>';
                        }                     
                        $htmlTable .= "</tr>";
                    }
                    $htmlTable .= "</tbody></table><br>";
                    
                    if($numberedRows)
                    {
                        $rowNumber = 1;
                    }
                
                    return $htmlTable;
                }else{
                    return "Databázová tabulka je prázdná.";
                }
            }
        }

        if ($outputFormat == "JSON")
        {
            return json_encode($inputData, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE); 
        }
    }
}
