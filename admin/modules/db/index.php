<?php
// no direct access
defined( 'ERISIM' ) or die( 'Bu alanı görmeye yetkiniz yok!' );

$cid = getParam($_REQUEST, 'cid'); 

include(dirname(__FILE__). '/html.php');

switch($task) {
	default:
	getDBTables();
	break;
	
	case 'backup':
	saveBackup($cid);
	break;
	
	case 'repair':
	repairTables($cid);
	break;
	
	case 'check':
	checkTables($cid);
	break;
	
	case 'optimize':
	optimizeTables($cid);
	break;
	
	case 'analyze':
	analyzeTables($cid);
	break;
}

function analyzeTables($cid) {
	global $dbase;
	
	$html = '';
	
	foreach($cid as $key=>$value) {
		$dbase->setQuery('ANALYZE TABLE '.$value);
		if ($dbase->_errorNum) {
		$html .= $value." tablosu analiz edilemedi! Mesaj:".$dbase->_errorMsg;    
		} else {
		$html .= $value." tablosu analiz edildi! Sonuç:OK!";    
		}
	}
	
	Redirect('index.php?option=admin&bolum=db', $html);
}
function optimizeTables($cid) {
	global $dbase;
    
    $html = '';
	
	foreach($cid as $key=>$value) {
		$dbase->setQuery('OPTIMIZE TABLE '.$value);
		
        if ($dbase->_errorNum) {
		$html .= $value.' tablosu uyarlanamadı! Mesaj:'.$dbase->_errorMsg.'<br />';    
		} else {
		$html .= $value.' tablosu uyarlandı! Sonuç:OK!<br />';    
		}
	}
    
    Redirect('index.php?option=admin&bolum=db', $html);
}
function checkTables($cid) {
	global $dbase;
    
    $html = '';
	
	foreach($cid as $key=>$value) {
		$dbase->setQuery('CHECK TABLE '.$value);
		if ($dbase->_errorNum) {
		$html .= $value.' tablosu kontrol edilemedi! Mesaj:'.$dbase->_errorMsg.'<br />';    
		} else {
		$html .= $value.' tablosu kontrol edildi! Sonuç:OK!<br />';    
		}
	}
    
    Redirect('index.php?option=admin&bolum=db', $html);
}

function repairTables($cid) {
	global $dbase;
    
    $html = '';
	
	foreach($cid as $key=>$value) {
		$dbase->setQuery('REPAIR TABLE '.$value);
		if ($dbase->_errorNum) {
		$html .= $value.' tablosu onarılamadı! Mesaj:'.$dbase->_errorMsg.'<br />';    
		} else {
		$html .= $value.' tablosu onarıldı! Sonuç:OK!<br />';    
		}
	}
    
    Redirect('index.php?option=admin&bolum=db', $html);
}

function getDBTables() {
	global $dbase;
	
	$tables = $dbase->getTableList();
	$total = count($tables);
	$pageNav = new pageNav( $total, 0, 10);
	
	BackupHTML::getDBTableList($tables, $pageNav);
}

function saveBackup($cid) {
	global $dbase;
	
	$filename = 'backup-';
	$filename.= date('Y-m-d-h-i-s');
	$filename.= '.sql';
	
	$file = PathName(ABSPATH).'backups/'.$filename;
	
	$file = fopen($file, 'x');
	
		$tables = $dbase->getTableCreate($cid);
	
		$data = '';
		
		//tabloları alalım
		foreach ($tables as $table) {
			$data .= $table.";\n\n";    
		}
		
		//tabloların içeriğini alalım
		$total = count($cid);
		
		for ($i=0; $i<$total; $i++)	{
			$table = $cid[$i];
			
			$dbase->setQuery("SELECT * FROM ". $dbase->getEscaped( $table));
			$alanlar = $dbase->query();
					
			$nf = $dbase->getNumFields($alanlar);  //alan sayısı
			
			$nr = $dbase->getNumRows($alanlar);  //row sayısı
		  
		  for ($c=0; $c<$nr; $c++) { //her row için 
		  
		  $data .= "INSERT INTO `$table` VALUES (";
		  
		  $row = mysql_fetch_row($alanlar);
				
			//alan adlarını ' karakterleriyle yazdır
		  for ($d=0; $d<$nf; $d++) {
			  
			  $s = $dbase->getEscaped($row[$d]);
			  
			  $data .="'".$s."'";  // ' i kontrol için
			  
			  if ($d<($nf-1)) {
			  $data .=", ";  //her alan için araya virgül koy
			} #if
		  } #for
				$data .=");\n"; // parantezi kapat
		} #for 
		$data .= "\n";
		}
		
		
				
		fwrite($file, $data);
		
	fclose($file);
	
Redirect('index.php?option=admin&bolum=db', 'Seçilen '.$total.' tablo '.$filename.' dosyasına kaydedildi');
		
}

function tabloBoyutu($table) {
	global $dbase;
	
	$dbase->setQuery("SELECT SUM(DATA_LENGTH + INDEX_LENGTH) as total FROM information_schema.TABLES WHERE TABLE_SCHEMA = '".DB."' and TABLE_NAME='".$table."'");
	$result = $dbase->loadResult();
	
	$result = round((($result) / 1024), 1);
	
	return $result;
}
