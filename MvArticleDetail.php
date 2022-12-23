<?php

namespace MvArticleDetail;

use Doctrine\ORM\Tools\SchemaTool;
use MvArticleDetail\Models\ArticleTabs;
use Shopware\Components\Plugin;
use Shopware\Components\Plugin\Context\ActivateContext;
use Shopware\Components\Plugin\Context\DeactivateContext;
use Shopware\Components\Plugin\Context\InstallContext;
use Shopware\Components\Plugin\Context\UninstallContext;
use Shopware\Components\Plugin\Context\UpdateContext;
use Shopware\Components\Model\ModelManager;
use Shopware\Models\Menu\Menu;
use Shopware\Components\Theme\LessDefinition;
use Shopware\Components\Api\Manager;

/////////////////////////////////////////////////////////////
// Mindfav Animated Boxes
/////////////////////////////////////////////////////////////
class MvArticleDetail extends Plugin
{
    public static function getSubscribedEvents()
    {
        return [
            'Enlight_Controller_Dispatcher_ControllerPath_Backend_MvArticleDetail' => 'onGetBackendController',
        ];
    }
    
    /**
     * @return string
     */
    public function onGetBackendController()
    {
        return __DIR__ . '/Controllers/Backend/MvArticleDetail.php';
    }
    
    
    
    public function install(InstallContext $installContext) {
				parent::install($installContext);
				
				// 1. Freitextfelder hinzufügen
				$service = $this->container->get('shopware_attribute.crud_service');
				
				// 1.a) Attribut "SEO-URL" hinzufügen.
				$service->update(
						's_articles_attributes', 
						'mv_short_description', 
						'html', 
						array(
								'label' => 'MvArtikelDetail - Kurzbeschreibung für Artikeldetail',
								'translatable' => true,
								'displayInBackend' => true,
								'entity' => 'Shopware\Models\Article\Article',
								'position' => 500,
								'custom' => false,
        		)
				);
				
				// 1.b) Attribut "Cross-Selling" hinzufügen.
				$service->update(
						's_articles_attributes', 
						'mv_crosselling', 
						'text', 
						array(
								'label' => 'Cross-Selling (Unsere Empfehlung für Sie)',
								'supportText' => 'Eingabe: Artikel-Nr|Bild-URL    je Box eine neue Zeile',
		                        'helpText' => 'Eingabe: Artikel-Nr|Bild-URL    je Box eine neue Zeile',
								'translatable' => true,
								'displayInBackend' => true,
								'entity' => 'Shopware\Models\Article\Article',
								'position' => 501,
								'custom' => false,
        		)
				);
				
				// 1.c) Attribut "Up-Selling" hinzufügen.
				$service->update(
						's_articles_attributes', 
						'mv_upselling', 
						'text', 
						array(
								'label' => 'Up-Selling (Wird oft zusammen gekauft)',
								'supportText' => 'Eingabe: Artikel-Nr|Bild-URL    je Box eine neue Zeile',
		                        'helpText' => 'Eingabe: Artikel-Nr|Bild-URL    je Box eine neue Zeile',
								'translatable' => true,
								'displayInBackend' => true,
								'entity' => 'Shopware\Models\Article\Article',
								'position' => 502,
								'custom' => false,
        		)
				);
				
				// 1.d) Attribut "Kurztitel" hinzufügen.
				$service->update(
						's_articles_attributes', 
						'mv_short_title', 
						'string', 
						array(
								'label' => 'Kurztext für Banner und Seitenboxen',
								'translatable' => true,
								'displayInBackend' => true,
								'entity' => 'Shopware\Models\Article\Article',
								'position' => 500,
								'custom' => false,
        		)
				);
				
				// 2. Datenbank-Tabellen anlegen (Models)
				$this->createDatabase();
				
				$installContext->scheduleClearCache(InstallContext::CACHE_LIST_DEFAULT);
		}
		
		public function update(UpdateContext $updateContext) {
				parent::update($updateContext);
				
				// 1. Datenbank-Tabellen anlegen (Models)
				$this->createDatabase();
				
				$updateContext->scheduleClearCache(UpdateContext::CACHE_LIST_FRONTEND);
		}
		
		public function uninstall(UninstallContext $uninstallContext) {
				parent::uninstall($uninstallContext);
				$uninstallContext->scheduleClearCache(UninstallContext::CACHE_LIST_FRONTEND);
		}
	
		public function activate(ActivateContext $activateContext) {
				parent::activate($activateContext);
				$activateContext->scheduleClearCache(ActivateContext::CACHE_LIST_FRONTEND);
		}
	
		public function deactivate(DeactivateContext $deactivateContext) {
				$deactivateContext->scheduleClearCache(DeactivateContext::CACHE_LIST_FRONTEND);
		}
		
		private function createDatabase() {
		        $modelManager = $this->container->get('models');
                $tool = new SchemaTool($modelManager);

                $classes = $this->getClasses($modelManager);

                $tool->updateSchema($classes, true); // make sure to use the save mode
		}
		
		/**
         * @param ModelManager $modelManager
         * @return array
         */
        private function getClasses(ModelManager $modelManager)
        {
                return [
                        $modelManager->getClassMetadata(ArticleTabs::class)
                ];
        }
}