<?php
//CREATING API REQUESTS AND FILTERING OUT DETAILS BASED ON BESINESS NEEDS. SOME PRIVATE INFORMATION MAY BE BLURED OUT FOR SECURITY REASONS.
//Perrin Joseph
//Sources: https://www.php.net/manual/en/function.curl-setopt.php
//sources: https://www.php.net/manual/en/function.number-format.php
//sources: https://www.php.net/manual/en/function.array-map.php 


/* 
            {
                "Name": "Jane",
                "Amount": 3136.2599999999998
            }
            {
                "Name": "Mark",
                "Amount": 2931.3
            }

*/
//blurd Key for privacy. If key is set output is as follows:
    $token = '**********';
    $ch = curl_init('********************');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
    curl_setopt($ch, CURLOPT_XOAUTH2_BEARER,$token);
    curl_setopt($ch, CURLOPT_HTTPHEADER,array(
        'Authorization: Bearer '.$token
    ));
    $data = curl_exec($ch);

    curl_close($ch);
    $decode = json_decode($data,true);

    //This callback function returns the "Name" of the current array element used togenerate unique names
    function uniNames($key)
    {   
        //$key is the array element
        try
        {
            if($key==null || $key=='')
            {
                throw new Exception('The array element value name might be empty');
            }
            return $key['fields']['Name'];
        }
        catch(Exception $e)
        {
            echo 'Caught Exception: ', $e->getMessage();
        }
        
    }
    //A call is made to the call back function that with each elements in the array using array map. this array map will return all the names in the array. These names are then filtered out with array unique. 
    $uniqueNames = array_unique(array_map('uniNames',$decode['records']));

    //this function is a call back function to calculate the total for each employee
    function amount_calc($key)
    {
        //assigning these variables as global so that We can calculate the totals and we need the name so that we can check if the current element is in the array of unique names.
        try
        {
            global $totalPayRoll, $name;
            if($key==null || $key=='')
            {
                throw new Exception('The array element value name might be empty');
            }
            if($key['fields']['Name']==$name)
            {
                $totalPayRoll += $key['fields']['Amount'];
            }
           
        }
        catch(Exception $e)
        {
            echo 'Caught Exception: ', $e->getMessage();
        }
    }

    //go through each unique name and populate a holder array. 
    //also format the amount to be 2 digits in cents
    foreach($uniqueNames as $name)
    {
        $totalPayRoll =0;
        $test = array_map('amount_calc',$decode['records']);
        $payroll_fixed = $test;
        $details = array(
            "Name"=>"$name",
            "Amount"=> number_format($totalPayRoll,2,'.','')
        );
        $encodedJsonDetailsOfEmployees = json_encode($details,JSON_PRETTY_PRINT);
        print_r($encodedJsonDetailsOfEmployees);
    }
?>  
