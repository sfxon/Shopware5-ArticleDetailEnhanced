<?php

class Shopware_Controllers_Widgets_GeschenkeLoader extends Enlight_Controller_Action {
		public function preDispatch() {
				$this->view->addTemplateDir(__DIR__ . '/../../Resources/views/');
		}

		public function indexAction() {
				$premiums = $this->getPremiums();		//$premiums enthält hier ein Array mit allen Premium Artikeln die vorhanden sind. Das kann auch mit var_dump ausgegeben werden.
				$cheapest_premium = false;
				$second_cheapest_premium = false;
				$cheapest_premium_difference = 99999.99;
				$premiums_count = 0;

				//Wir lesen aus, welches das günstigste Premium Produkt ist, also jenes, dass für den niedrigsten Zuzahlungspreis zu erhalten ist.
				foreach($premiums as $premium) {
						$tmp_premium = str_replace(',', '.', $premium['sDifference']);
						$tmp_premium = (float)$tmp_premium;

						if($tmp_premium < $cheapest_premium_difference) {
								$cheapest_premium_difference = $tmp_premium;
								$cheapest_premium = $premium;
						}
						
						$premiums_count++;
				}

				//find second most expensive item
				$second_cheapest_premium_difference = 99999.99;

				foreach($premiums as $premium) {
						$tmp_premium = str_replace(',', '.', $premium['sDifference']);
						$tmp_premium = (float)$tmp_premium;

						//Wir überspringen das Teil, wenn es das günstigste ist, damit wir nicht zweimal das günstigste in der Auswahl haben. Clever, wa?
						if($cheapest_premium == $premium) {
								continue;
						}

						if($tmp_premium < $second_cheapest_premium_difference) {
								$second_cheapest_premium_difference = $tmp_premium;
								$second_cheapest_premium = $premium;
						}
				}

				$this->view->assign('mv_special_for_you_cheapest_premium', $cheapest_premium);
				$this->view->assign('mv_special_for_you_second_cheapest_premium', $second_cheapest_premium);
				$this->view->assign('mv_special_for_you_premiums_count', $premiums_count);
		}

		//////////////////////////////////////////////////////////////////////////////////////////////////////
		// Loading additional premium articles.
		//////////////////////////////////////////////////////////////////////////////////////////////////////
		public function morepremiumsAction() {
				$id1 = $this->Request()->getParam('id1');
				$id2 = $this->Request()->getParam('id2');

				//Lade alle Sonder-Artikel
				$premiums = $this->getPremiums();
				$final_premiums = array();

				foreach($premiums as $premium) {
						if($premium['articleID'] == $id1 || $premium['articleID'] == $id2) {
								continue;
						}	

						$final_premiums[] = $premium;
				}

				$this->view->assign('MV_MORE_PREMIUMS', $final_premiums);
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