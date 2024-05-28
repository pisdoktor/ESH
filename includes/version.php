<?php
// no direct access
defined( 'ERISIM' ) or die( 'Bu alanı görmeye yetkiniz yok!' ); 

class Version {
	var $PRODUCT     = 'Evde Sağlık Hizmetleri Sistemi';
	
	var $RELEASE     = '1.4';
	
	var $DEV_STATUS  = 'Stabil';
	
	var $DEV_LEVEL   = '4';
	
	var $CODENAME    = 'Yozgat';
	
	var $RELDATE     = '14 Mart 2024';
	
	var $RELTIME     = '20:00';
	
	var $COPYRIGHT   = "Copyright © 2024 Soner Ekici. Tüm hakları saklıdır.";
	
	var $URL         = 'Coded by <a href="" target="_blank">Soner Ekici</a>';

	/**
	 * @return string Long format version
	 */
	function getLongVersion() {
		return $this->PRODUCT .' '. $this->RELEASE .'.'. $this->DEV_LEVEL .' '
			. $this->DEV_STATUS
			.' [ '.$this->CODENAME .' ] '. $this->RELDATE .' '
			. $this->RELTIME;
	}

	/**
	 * @return string Short version format
	 */
	function getShortVersion() {
		return $this->PRODUCT .' '. $this->RELEASE .'.'. $this->DEV_LEVEL;
	}
	
	function getCopy() {
		return $this->COPYRIGHT;
	}
}