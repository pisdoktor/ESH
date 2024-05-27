<?php
// no direct access
defined( 'ERISIM' ) or die( 'Bu alanı görmeye yetkiniz yok!' ); 

include(dirname(__FILE__). '/html.php');

switch($task) {
	default:
	getConfigFile();
	break;
		
	case 'save':
	saveConfig();
	break;
	
	case 'cancel':
	cancelConfig();
	break;
}

function cancelConfig() {
	Redirect('index.php');
}

function getConfigFile() {
	
	$data = readConfig(ABSPATH.'/config.php');
	
	ConfigHTML::ConfigFile($data);
}

function saveConfig() {
	
	$data = $_POST['data'];
	
	$ret = file_put_contents(ABSPATH.'/config.php', $data);
	
	Redirect('index.php', 'Yapılandırma dosyası güncellendi');
}

function readConfig($filename, $incpath = false, $amount = 0, $chunksize = 8192, $offset = 0)	{
		// Initialize variables
		$data = null;
		if($amount && $chunksize > $amount) { $chunksize = $amount; }
		if (false === $fh = fopen($filename, 'rb', $incpath)) {
			mosErrorAlert('Dosya açılamadı');
			return false;
		}
		clearstatcache();
		if($offset) fseek($fh, $offset);
		if ($fsize = @ filesize($filename)) {
			if($amount && $fsize > $amount) {
				$data = fread($fh, $amount);
			} else {
				$data = fread($fh, $fsize);
			}
		} else {
			$data = '';
			$x = 0;
			// While its:
			// 1: Not the end of the file AND
			// 2a: No Max Amount set OR
			// 2b: The length of the data is less than the max amount we want
			while (!feof($fh) && (!$amount || strlen($data) < $amount)) {
				$data .= fread($fh, $chunksize);
			}
		}
		fclose($fh);

		return $data;
}
