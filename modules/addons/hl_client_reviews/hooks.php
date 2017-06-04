<?php if (!defined("WHMCS")) die("This file cannot be accessed directly");
use WHMCS\Database\Capsule;
use WHMCS\View\Menu\Item as MenuItem;

function hl_client_reviews_reviews($vars) {
	
	$values = array();
	$values['hl_reviews'] = Capsule::table('mod_hl_client_reviews')->where('active',1)->get();
	$values['hl_reviews'] = array_rand($values['hl_reviews'] , 10);

	return $values;
}
add_hook('ClientAreaPageHome', 1, 'hl_client_reviews_reviews');

add_hook('ClientAreaSecondarySidebar', 1, function (MenuItem $secondarySidebar){
    if (!is_null($secondarySidebar->getChild('Client Shortcuts'))){
        $secondarySidebar->getChild('Client Shortcuts')
            ->addChild('Add New Client Review')
                ->setLabel('شاركنا برأيك عن الخدمة')
                ->setUri('index.php?m=hl_client_reviews')
				->setIcon('fa-thumbs-up')
                ->setOrder(100);
		$secondarySidebar->getChild('Client Shortcuts')->getChild('Add New Client Review')->moveUp();
    }
});