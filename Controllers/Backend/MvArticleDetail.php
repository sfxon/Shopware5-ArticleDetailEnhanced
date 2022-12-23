<?php

use Doctrine\DBAL\Driver\PDOStatement;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Query\Expr\Join;
use Shopware\Components\CSRFWhitelistAware;
use Shopware\Models\Form\Repository as FormRepo;

class Shopware_Controllers_Backend_MvArticleDetail extends Enlight_Controller_Action implements CSRFWhitelistAware {        
		var $formRepository = null;
		
		public function preDispatch(): void
    {
        $this->get('template')->addTemplateDir(__DIR__ . '/../../Resources/views/');
    }
		
    
		public function postDispatch(): void
    {
        $csrfToken = $this->container->get('backendsession')->offsetGet('X-CSRF-Token');
        $this->View()->assign([ 'csrfToken' => $csrfToken ]);
    }
				
    /**
     * @return FormRepo
     */
    private function getFormRepository()
    {
        if ($this->formRepository === null) {
            $this->formRepository = $this->getModelManager()->getRepository('Shopware\Models\Config\Form');
        }
        return $this->formRepository;
    }
   
	  //////////////////////////////////////////////////////////////////////////////////////////////
		// Daten für die Übersichtsliste laden.
		//////////////////////////////////////////////////////////////////////////////////////////////
    public function indexAction(): void
    {
				$data = array();
				
				// Lade alle Einträge in der Tabelle mv_article_tabs
				$qb = Shopware()->Container()->get('dbal_connection')->createQueryBuilder()
						->select('mvat.id, mvat.title, mvat.fieldname, mvat.sortorder, mvat.side')
            ->from('mv_article_tabs', 'mvat')
        		->orderBy('mvat.sortorder', 'ASC');
				
				$result = $qb->execute();
				
				while($tmp = $result->fetch()) {
						$data[] = $tmp;
				}
				
				$fieldname_values = $this->loadFieldnameValues();
								
				$this->View()->assign(
						array(
								'tabs' => $data,
								'fieldname_values' => $fieldname_values
						)
				);
    }
		
		//////////////////////////////////////////////////////////////////////////////////////////////
		// Feldnamen-Werte laden.
		//////////////////////////////////////////////////////////////////////////////////////////////
		public function loadFieldnameValues() {
				// Lade alle verfügbaren Artikel-Freitextfelder
				$article_freetextfields = array();
				
				$connection = $this->container->get('dbal_connection');
				$sql = 'SHOW COLUMNS FROM s_articles_attributes;';

				$columns = $connection->fetchAll($sql);
				
				$columns_data = array(
						array(
								'field' => 'Standard-Tab: Beschreibung', 
								'internal_fieldname' => 'default_tab:description'
						),
						array(
								'field' => 'Standard-Tab: Kundenbewertung', 
								'internal_fieldname' => 'default_tab:rating'
						),
						array(
								'field' => 'Erweiterung: Special4You', 
								'internal_fieldname' => 'extension_tab:mvspecialforyou'
						),
						array(
								'field' => 'Erweiterung: Frei definierbares Cross-Selling', 
								'internal_fieldname' => 'extension_tab:mvcrossselling'
						),
						array(
								'field' => 'Erweiterung: Frei definierbares Up-Selling', 
								'internal_fieldname' => 'extension_tab:mvupselling'
						),
                        array(
                                'field' => 'Erweiterung: Youtube-Video',
                                'internal_fieldname' => 'extension_tab:youtube'
                        )
				);
				
				foreach($columns as $col) {
						// Skip unwanted fields.
						if($col['Field'] == 'id') {
								continue;
						}
						
						$columns_data[] = array(
								'field' => 'Freitextfeld: ' . $col['Field'],
								'internal_fieldname' => 'field:' . $col['Field']
						);
				}
				
				return $columns_data;
		}

		//////////////////////////////////////////////////////////////////////////////////////////////
		// Sub-Fenster darstellen (Bearbeiten der Artikel-Details)
		//////////////////////////////////////////////////////////////////////////////////////////////
    public function createSubWindowAction(): void
    {
				if(!isset($_GET['tab_id'])) {
						die('ID is missing');
				}
				
				$id = (int)$_GET['tab_id'];
				
				// Tab-Daten laden/initialisieren
				$tab_data = false;
				
				if(0 === $id) {
						$tab_data = array(
								'id' => 0,
								'title' => '',
								'fieldname' => '',
								'sortorder' => '',
								'side' => ''
						);
				} else {
						// Lade Tab-Daten
						$qb = Shopware()->Container()->get('dbal_connection')->createQueryBuilder()
								->select('mvat.id, mvat.title, mvat.fieldname, mvat.sortorder, mvat.side')
								->from('mv_article_tabs', 'mvat')
								->where('mvat.id = :id')
								->setParameter('id', $id);
						
						$result = $qb->execute();
						
						while($tmp = $result->fetch()) {
								$tab_data = $tmp;
								break;
						}
				}
				
				// Feldnamen für Freitextfeld-Auswahl laden.
				$fieldname_values = $this->loadFieldnameValues();
				
				// Daten an View übergeben.				
				$this->View()->assign(
						array(
								'tab_data' => $tab_data,
								'fieldname_values' => $fieldname_values
						)
				);
    }
		
		//////////////////////////////////////////////////////////////////////////////////////////////
		// Speichern.
		//////////////////////////////////////////////////////////////////////////////////////////////
    public function saveAction() {
				if(!isset($_GET['id'])) {
						die('ID is missing');
				}
				
				$id = (int)$_GET['id'];
				
				if(0 === $id) {
						$id = $this->createTab();
				} else {
						$this->updateTab($id);
				}
						
				die('done');
		}
		
		//////////////////////////////////////////////////////////////////////////////////////////////
		// Speichern.
		//////////////////////////////////////////////////////////////////////////////////////////////
    public function deleteAction() {
				if(!isset($_GET['id'])) {
						die('ID is missing');
				}
				
				$id = (int)$_GET['id'];
				
				$this->deleteTab($id);
						
				die('done');
		}
		
		//////////////////////////////////////////////////////////////////////////////////////////////
		// Aktualisieren eines Datenbankeintrages
		//////////////////////////////////////////////////////////////////////////////////////////////
		public function updateTab($id) {
				if(
						!isset($_POST['title']) || 
						!isset($_POST['fieldname']) ||
						!isset($_POST['sortorder']) ||
						!isset($_POST['side'])
				) {
						die('Cannot update. Missing fields in ' . __FILE__ . ', Line: ' . __LINE__);
				}
				
				$title = $_POST['title'];
				$fieldname = $_POST['fieldname'];
				$sortorder = $_POST['sortorder'];
				$side = $_POST['side'];
				
				$connection = $this->container->get('dbal_connection');
				$sql = 
						'UPDATE mv_article_tabs SET ' . 
								'title = :title, ' .
								'fieldname = :fieldname, ' .
								'sortorder = :sortorder, ' .
								'side = :side ' .
						'WHERE ' .
								'id= :id';

				$statement = $connection->prepare($sql);
				$statement->execute(
						array(
								'title' => $title,
								'fieldname' => $fieldname,
								'sortorder' => $sortorder,
								'side' => $side,
								'id' => $id
						)
				);
		}
		
		//////////////////////////////////////////////////////////////////////////////////////////////
		// Erstellen eines Datenbankeintrages
		//////////////////////////////////////////////////////////////////////////////////////////////
		public function createTab() {
				if(
						!isset($_POST['title']) || 
						!isset($_POST['fieldname']) ||
						!isset($_POST['sortorder']) ||
						!isset($_POST['side'])
				) {
						die('Cannot create. Missing fields in ' . __FILE__ . ', Line: ' . __LINE__);
				}
				
				$title = $_POST['title'];
				$fieldname = $_POST['fieldname'];
				$sortorder = $_POST['sortorder'];
				$side = $_POST['side'];
				
				// Datenbank-Eintrag erstellen.
				$connection = Shopware()->Container()->get('dbal_connection');
				$qb = $connection->createQueryBuilder();
				$qb
						->insert('mv_article_tabs')
						->values(
								array(
										'title' => ':title',
										'fieldname' => ':fieldname',
										'sortorder' => ':sortorder',
										'side' => ':side'
								)
						)
						->setParameter('title', $title)
						->setParameter('fieldname', $fieldname)
						->setParameter('sortorder', $sortorder)
						->setParameter('side', $side);
				$qb->execute();
				$insert_id = $connection->lastInsertId();
				
				return $insert_id;
		}
		
		//////////////////////////////////////////////////////////////////////////////////////////////
		// Löschen eines Eintrages
		//////////////////////////////////////////////////////////////////////////////////////////////
		public function deleteTab($id) {
				//Delete from comparison_features_to_products
				$qb = Shopware()->Container()->get('dbal_connection')->createQueryBuilder()
						->delete('mv_article_tabs')
						->where('id = :id')
						->setParameter('id', $id);
				$qb->execute();
		}
		
		//////////////////////////////////////////////////////////////////////////////////////////////
		// White-List für unsere Actions in diesem Controller erstellen.
		//////////////////////////////////////////////////////////////////////////////////////////////
    public function getWhitelistedCSRFActions()
    {
        return array(
						'index',
						'createSubWindow',
						'save',
						'delete'
						//['index'];
				);
    }
}