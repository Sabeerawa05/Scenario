<?php

function ReplaceTurkishToEnglish($str){
    $before = array('ı', 'ğ', 'ü', 'ş', 'ö', 'ç', 'İ', 'Ğ', 'Ü', 'Ö', 'Ç','Ş'); 
    $after   = array('i', 'g', 'u', 's', 'o', 'c', 'I', 'G', 'U', 'O', 'C','S');

    $clean = str_replace($before, $after, $str);
    return $clean;
}

function GetDateFormat($date) {
    return substr($date,0,4)."-".substr($date,4,2)."-".substr($date,6,2)." ".substr($date,8,2).":".substr($date,10,2).":".substr($date,12,2);
  }


  function AppendErrorToErrorFile($text)
  {
    $fp = fopen('errors/error.txt', 'a');//opens file in append mode  
    fwrite($fp, $text."\n");  
    fclose($fp);  
      
}

function WriteDataInXMLFile($headerTitles,$headerValues,$detailTitles,$detailValues)
{
    $dom = new DOMDocument();
		$dom->encoding = 'utf-8';
		$dom->xmlVersion = '1.0';
		$dom->formatOutput = true;
	$xml_file_name = 'output/output.xml';
		$root = $dom->createElement('order');
		$header_node = $dom->createElement('header');
        for($i=0;$i<count($headerValues);$i++)
        {
              $child_node_header= $dom->createElement($headerTitles[$i], $headerValues[$i]);
              $header_node->appendChild($child_node_header);
        }
		$root->appendChild($header_node);
        $lines_node= $dom->createElement('lines');
        foreach ($detailValues as $value) {
        $detail_node = $dom->createElement('line');
        for($i=0;$i<count($detailTitles);$i++)
        {
              $child_node_details= $dom->createElement($detailTitles[$i], $value[$i]);
              $detail_node->appendChild($child_node_details);
        }
        $lines_node->appendChild($detail_node);
    }
        $root->appendChild($lines_node);
		$dom->appendChild($root);
	$dom->save($xml_file_name);
	echo "$xml_file_name has been successfully created";
}

function  ReadDataFromTxtFile()
{
	if (!file_exists("input/input.txt")) 
{
   AppendErrorToErrorFile("Input file not exists");
   return;
}

$file = fopen("input/input.txt","r");
$headerTitles=[];
$headerValues=[];
$detailTitles=[];
$detailValues = [];
$counter=1;
while(!feof($file))
  {
    $data =fgets($file);
    $dataInArray = explode(";",$data);
      if($counter==1)
      {  
        $headerTitles= array($dataInArray[0],$dataInArray[1], $dataInArray[2],$dataInArray[3], str_replace("\r\n","",$dataInArray[4]));
      }
     else if($counter==2)
     {
       $headerValues= array($dataInArray[0],$dataInArray[1], GetDateFormat($dataInArray[2]),GetDateFormat($dataInArray[3]), str_replace("\r\n","",$dataInArray[4]));
   if(count($headerValues)!=count($headerTitles))
   {
    AppendErrorToErrorFile("Header Title length is not equal to Header Values");
    fclose($file);
    return;
   }
    }
     else if($counter==3)
     {
       $detailTitles=array($dataInArray[0],$dataInArray[1],   $dataInArray[2] ,$dataInArray[3], $dataInArray[4], $dataInArray[5], $dataInArray[6], str_replace("\r\n","",$dataInArray[7]) );
     }
     else{
      if(count($dataInArray)==count( $detailTitles))
      {
           $result= ReplaceTurkishToEnglish($dataInArray[2]);
           $arrayValue=array($dataInArray[0],$dataInArray[1],   $result , $dataInArray[3], $dataInArray[4], $dataInArray[5], $dataInArray[6], str_replace("\r\n","",$dataInArray[7]) );
        array_push($detailValues,$arrayValue) ;
      }
      else{
        AppendErrorToErrorFile("Detail Title length is not equal to Details Values");
      }
    }
    $counter++;
  }
fclose($file);
WriteDataInXMLFile($headerTitles,$headerValues,$detailTitles,$detailValues);
}
ReadDataFromTxtFile();
?> 