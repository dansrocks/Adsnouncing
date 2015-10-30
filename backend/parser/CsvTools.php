<?php

namespace Adsnouncing\backend\parser;



/**
 * 
 */
abstract class CsvTools
{
    protected $file = null;
    
    protected $headers = [];
    
    protected $delimiter = ',';
    protected $enclosure = '"';
    

    /**
     * 
     * @param type $config
     */
    public function setConfig($config = null)
    {
	if (isset($config['headers'])) {
	    $this->headers = $config['headers'];
	}
	
	if (isset($config['delimiter'])) {
	    $this->delimiter = $config['delimiter'];
	}

	if (isset($config['enclosure'])) {
	    $this->enclosure = $config['enclosure'];
	}
    }
    
    /**
     * 
     * @param type $config
     */
    public function parser()
    {
	$file = $this->getFile();
	$fh = fopen($file, 'r');
	if (! $fh) {
	    throw new \Exception("Cannot open file for reading (file: {$file})");
	}
	
	$nline = 1;
	while (($line = fgets($fh)) !== false) {
	    
	    $line = rtrim(rtrim($line, "\n"), "\r");
	    if ($nline == 1) {
		$line = $this->removeUtf8Bom(trim($line));
	    }
	    if (empty($line)) {
		++$nline;
		continue;
	    }
	    $fields = str_getcsv($line, $this->delimiter, $this->enclosure);
	    if ($nline == 1) {
		if (! $this->verifyHeaders($fields)) {
		    throw new \Exception("Csv headers is not compatible.");
		}
	    } else {
		if (! empty($fields)) {
		    $data = $this->processCsvDataFields($fields);
		    if ($data == false) {
			throw new \Exception("Invalid line {$nline}: '{$line}'");
		    }
		}
	    }

	    $nline++;
	}
	
	fclose($fh);
    }
    
    /**
     * Verify header from configuration
     * 
     * @param type $fields
     * @return boolean
     */
    protected function verifyHeaders($fields)
    {
	$valid = count($fields) == count($this->headers);

	if ($valid) {
	    $verify = array_combine($fields, array_values($this->headers));
	    foreach ($verify as $csv_field => $header) {
		if ($csv_field != $header) {
		    $valid = false;
		    break;
		}
	    }
	}
	    
	return $valid;
    }
    
    
    abstract public function processCsvDataFields($fields);
    
    /**
     * 
     * @param string $file  Set file to parser
     * @throws \Exception
     */
    public function setFile($file)
    {
	if (! file_exists($file)) {
	    throw new \Exception("File not exists (file: {$file})");
	}
	
	$this->file = $file;
    }

    /**
     * Returns file to parser
     * 
     * @return string
     */
    protected function getFile()
    {
	return $this->file;
    }
    
    /**
     * 
     * @param string $text
     * 
     * @return string
     */
    protected function removeUtf8Bom($text)
    {
	$bom = pack('H*','EFBBBF');
	return preg_replace("/^$bom/", '', $text);
    }
}