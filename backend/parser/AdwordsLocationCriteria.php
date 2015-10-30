<?php

namespace Adsnouncing\backend\parser;

require_once 'CsvTools.php';

use Adsnouncing\backend\parser\CsvTools;


class AdwordsLocationCriteria extends CsvTools
{
    protected $config = [
	'delimiter' => ',',
	'enclosure' => '"',
	'headers' => [
	    'id'	    => 'Criteria ID',
	    'name'	    => 'Name',
	    'canonical'	    => 'Canonical Name',
	    'parent_id'	    => 'Parent ID',
	    'country_code'  => 'Country Code',
	    'type'	    => 'Target Type',
	    'status'	    => 'Status'
	]
    ];
    
    protected $types = [];
    
    /**
     * Constructor
     * 
     * @param string$filename
     */
    public function __construct($file =  null)
    {
	if ($file) {
	    echo "Using file '{$file}'\n";
	    $this->setFile($file);
	}
	$this->setConfig($this->config);
    }
    
    
    /**
     * 
     * @param string[] $fields
     */
    public function processCsvDataFields($fields)
    {
	$data = array_combine(
		array_keys($this->headers),
		$fields
	    );
	
	if ($data) {
	    $type = $data['type'];
	    if (isset($this->types[$type])) {
		$this->types[$type]++;
	    } else {
		$this->types[$type] = 1;
	    }
	}
	
	return $data;
    }
    
    public function showTypesStats()
    {
	print_r($this->types);
    }
}
