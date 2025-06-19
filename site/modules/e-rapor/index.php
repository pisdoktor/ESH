<?php 
// no direct access
defined( 'ERISIM' ) or die( 'Bu alanı görmeye yetkiniz yok!' );

switch($task) {
    
    default:
    case 'listele': 
    hastaListele(); 
    break;
    
    case 'new':
    hastaDuzenle();
    break;
    
    case 'edit':
    hastaDuzenle($id);
    break;
    
    case 'save':
    hastaKaydet();
    break;
    
    case 'delete':
    hastaSil();
    break;
}

function hastaListele() {
    


}