<?php

class Shopware_Controllers_Frontend_GeschenkeLoader extends Enlight_Controller_Action {
		public function preDispatch() {
				$this->view->addTemplateDir(__DIR__ . '/../../Resources/views/');
		}

		public function indexAction() {
				$id1 = $this->Request()->getParam('id1');
				$id2 = $this->Request()->getParam('id2');

				$this->view->assign('id1', $id1);
				$this->view->assign('id2', $id2);
		}

		//////////////////////////////////////////////////////////////////////////////////////////////////////
		// Premium Artikel abrufen.
		// Diese Funktion ist entnommen aus der Datei:
		//		/shopware/engine/Shopware/Controllers/Frontend/Checkout.php
		//////////////////////////////////////////////////////////////////////////////////////////////////////
		public function getPremiums() {
        return Shopware()->Modules()->Marketing()->sGetPremiums();
    }
}