<?php

namespace MvArticleDetail\Subscriber;

use Doctrine\Common\Collections\ArrayCollection;
use Enlight\Event\SubscriberInterface;
use Enlight_Controller_ActionEventArgs;
use Enlight_Event_EventArgs as EventArgs;
use Shopware\Components\Theme\LessDefinition;

class TemplateRegistration implements SubscriberInterface
{
    /**
     * @var string
     */
    private $pluginDirectory;

    /**
     * @param $pluginDirectory
     */
    public function __construct($pluginDirectory)
    {
        $this->pluginDirectory = $pluginDirectory;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
			'Enlight_Controller_Action_PreDispatch' => 'addTemplateDir',			//Template Verzeichnis hinzufügen.
			'Enlight_Controller_Action_PostDispatch_Frontend_Detail' => 'onFrontendDetail',
            'Enlight_Controller_Action_PostDispatchSecure_Frontend' => 'onFrontend',
            'Enlight_Controller_Action_PostDispatchSecure_Frontend_Detail' => 'onFrontendDetailSecure',
            'Theme_Compiler_Collect_Plugin_Less' => 'onCollectLessFiles',
            'Theme_Compiler_Collect_Plugin_Javascript' => 'addJsFiles'
        ];
    }
		
		/**
     * Event Handler
     * @param \Enlight_Event_EventArgs $args
     */
    public function onFrontend(\Enlight_Event_EventArgs $args) {
        $controller = $args->get('subject');

        $view = $controller->View();
        $view->addTemplateDir($this->pluginDirectory  . '/Resources/views');
				
		// Attribut auslesen.				
        $request = $controller->Request();
        $shopId = Shopware()->Shop()->getId();
		$controllerName = $request->getControllerName();
				
        // Artikel-Detailseite
        if($controllerName == 'detail') {
            $sArticle = $view->sArticle;
            $articleId = $view->sArticle['articleID'];
			$mvShortDescription = $this->getArticleAttributes($shopId, $articleId);
						
			$view->assign('mvShortDescription', $mvShortDescription['mv_short_description']);
        
				
            // Tab-Konfiguration laden.
            $tabConfiguration = $this->loadTabConfiguration();

            // Zähle Tabs auf der rechten Seite
            $tabsRightSide = 0;

            foreach($tabConfiguration as $tab) {
                if($tab['side'] == 'right') {
                    if($this->hasTabContent($tab, $sArticle, $view)) {
                        $tabsRightSide++;
                    }
                }
            }
            
            // Werte an den View übergeben.
            $view->assign('mvTabsRightSide', $tabsRightSide);				
            $view->assign('mvTabConfiguration', $tabConfiguration);
        }
	}
    
    /*
     * Prüft, ob ein bestimmtes Tab auf der rechten Seite über Content verfügt.
     * Falls kein Tab auf der rechten Seite Content hat, wird dadurch auch kein tab angezeigt.
     */
    private function hasTabContent($tab, $sArticle, $view) {
        switch($tab['fieldname']) {
            case 'default_tab:description':
                if($sArticle['description_long'] || $sArticle['sProperties'] || $sArticle['sDownloads']) {
                    return true;
                }
                break;
            case 'default_tab:rating':
                // Könnte hier noch abfragen, ob Rating überhautp aktiv ist,
                // ist gerade aber gar nicht wichtig.
                // Template macht das so hier: {if !{config name=VoteDisable}}
                return true;
            case 'extension_tab:mvspecialforyou':
                // Hier prüfen wir gerade auch nicht auf verfügbaren Content..
                return true;
            case 'extension_tab:mvcrossselling':
                $qb = Shopware()->Container()->get('dbal_connection')->createQueryBuilder();
                $crosselling = $this->loadCrossSellingProducts($view, $qb);
                
                if(is_array($crosselling) && count($crosselling) > 0) {
                    return true;
                }
				
                break;
            case 'extension_tab:mvupselling':
                $qb = Shopware()->Container()->get('dbal_connection')->createQueryBuilder();
                $upselling = $this->loadUpSellingProducts($view, $qb);
                
                if(is_array($upselling) && count($upselling) > 0) {
                    return true;
                }
                break;
            default: 
                break;
        }
        
        return false;
    }
		
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // Templates registrieren
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    public function onFrontendDetailSecure(\Enlight_Event_EventArgs $args) {
        $view = $args->getSubject()->View();

        $view->addTemplateDir(
            $this->pluginDirectory . '/Resources/views/'
        );

        //Gratis-Artikel laden..
        $premiums = $this->getPremiums();
        $view->assign('mvPremiums', $premiums);
    }
		
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // Tab-Konfiguration laden.
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    public function loadTabConfiguration() {
        // Lade alle Einträge in der Tabelle mv_article_tabs
        $db = Shopware()->Db();
        $data = $db->fetchAll(
            'SELECT * FROM mv_article_tabs ORDER BY cast(sortorder as unsigned)', 
            array()
        );

        return $data;
    }
		
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // Artikel-Seo-Attributes laden.
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    public function getArticleAttributes($shopId, $articleId) {
        $retval = array(
            'mv_short_description' => "",
        );

        $db = Shopware()->Db();

        //Artikel-Detail-ID laden, damit wir die Daten für den richtigen Artikel laden.
        $article_detail_id = $db->fetchOne(
            'SELECT id FROM s_articles_details ' .
            'WHERE articleID = :articleID ' .
            'AND kind = 1', 
            array(
                'articleID' => $articleId
            )
        );

        $data = $db->fetchRow(
            "SELECT * FROM s_articles_attributes
            WHERE articledetailsID = :articledetailsID", 
            array(
                'articledetailsID' => $article_detail_id
            )
        );

        //Canonical Tag
        if(isset($data['mv_short_description'])) {
            $retval['mv_short_description'] = trim($data['mv_short_description']);
        }

        return $retval;
    }
		
    /**
     * Javascript Dateien hinzufügen
     */
    public function addJsFiles(\Enlight_Event_EventArgs $args) {
        $jsFiles = array(
            $this->pluginDirectory . '/Resources/views/frontend/_public/src/js/articleDetailTabs.js',
        );
        return new ArrayCollection($jsFiles);
    }
		
    /**
     * Add Less Files in plugin folder
     */
    public function onCollectLessFiles(EventArgs $args) {
        $shop = false;

        if (Shopware()->Container()->initialized('shop')) {
            $shop = Shopware()->Container()->get('shop');
        }

        if (!$shop) {
            $shop = Shopware()->Container()->get('models')->getRepository(\Shopware\Models\Shop\Shop::class)->getActiveDefault();
        }

        $config = Shopware()->Container()->get('shopware.plugin.cached_config_reader')->getByPluginName('MvArticleDetail', $shop);

        $tabButtonBackgroundColor = '#efefef';
        $tabButtonTextColor = '#163B64';
        $tabActiveBackgroundColor = '#fabb00';
        $tabActiveTextColor = '#ffffff';

        if(isset($config['tabBackgroundColor'])) {
            $tabButtonBackgroundColor = $config['tabBackgroundColor'];
        }

        if(isset($config['tabTextColor'])) {
            $tabButtonTextColor = $config['tabTextColor'];
        } 

        if(isset($config['tabActiveBackgroundColor'])) {
            $tabActiveBackgroundColor = $config['tabActiveBackgroundColor'];
        }

        if(isset($config['tabActiveTextColor'])) {
            $tabActiveTextColor = $config['tabActiveTextColor'];
        }

        $less = new LessDefinition(
            array( //Configuration
                'tabButtonBackgroundColor' => $tabButtonBackgroundColor,
                'tabButtonTextColor' => $tabButtonTextColor,
                'tabActiveBackgroundColor' => $tabActiveBackgroundColor,
                'tabActiveTextColor' => $tabActiveTextColor
            ),
            array($this->pluginDirectory . '/Resources/views/frontend/_public/src/less/all.less'),
            $this->pluginDirectory		//import directory
        );

        return new ArrayCollection(array($less));
    }
		
    ///////////////////////////////////////////////////////////////////////
    // Template Verzeichnis hinzufügen!
    ///////////////////////////////////////////////////////////////////////
    public function addTemplateDir(\Enlight_Event_EventArgs $args) {
	    $controller = $args->get('subject');
        $view = $controller->View();
				
        $view->addTemplateDir(
            $this->pluginDirectory  . '/Resources/views/'
        );
    }
		
    //////////////////////////////////////////////////////////////////////////////////////////////////////
    // Premium Artikel abrufen.
    // Diese Funktion ist entnommen aus der Datei:
    //		/shopware/engine/Shopware/Controllers/Frontend/Checkout.php
    //////////////////////////////////////////////////////////////////////////////////////////////////////
	public function getPremiums() {
        $sql = 'SELECT `id` FROM `s_order_basket` WHERE `sessionID`=? AND `modus`=1';
        $result = Shopware()->Db()->fetchOne($sql, [Shopware()->Session()->get('sessionId')]);
        
		if (!empty($result)) {
            return [];
        }

        return Shopware()->Modules()->Marketing()->sGetPremiums();
    }
		
    //////////////////////////////////////////////////////////////////////////////////////////////////////
    // Wenn die Detail-Seite geladen wird.
    //////////////////////////////////////////////////////////////////////////////////////////////////////
    public function onFrontendDetail(\Enlight_Event_EventArgs $args) {
        $controller = $args->get('subject');
        $view = $controller->View();

        //Artikel-ID des aktuellen Artikels:
        $articleID = $view->sArticle['articleID'];

        $qb = Shopware()->Container()->get('dbal_connection')->createQueryBuilder();

        $this->loadCrossSelling($view, $qb);
        $this->loadUpSelling($view, $qb);
    }
    
    /*
     * Up-Selling Artikel laden.
     */
    private function loadUpSellingProducts($view, $qb) {
        //Artikel-ID des aktuellen Artikels:
        $articleID = $view->sArticle['articleID'];

        //Artikel-Detail-ID des Hauptartikels:
        $article_detail_id = (int)$this->getMainArticleDetailIdByArticleId($articleID);

        if(0 === $article_detail_id) {
                return array();
        }

        //Load cross-Selling settings from article attributes database.
        $qb->select('mv_upselling')
            ->from('s_articles_attributes')
            ->where('s_articles_attributes.articledetailsID = :article_detail_id')
            ->setParameter('article_detail_id', $article_detail_id);

        $single = $qb->execute()->fetch();

        if(!isset($single) || !isset($single['mv_upselling'])) {
            return array();
        }

        //Analyse results
        $cs = $single['mv_upselling'];

        $cs_parts = explode("\n", $cs);

        if(!is_array($cs_parts) || count($cs_parts) < 1) {
            return array();
        }

        $mv_up_selling_products = array();

        foreach($cs_parts as $line) {
            $line_parts = explode('|', $line, 2);

            $products_model = $line_parts[0];
            $image_url = $line_parts[1];

            //Load products data by products model.
            $products_data = $this->loadProductsDataByProductsModel($products_model);

            if(false === $products_data) {
                    continue;
            }

            //Load products title or short title..
            $products_title = $this->loadProductsTitleOrShortTitleByProductsId($products_data['articleID']);

            $products_data['image_url'] = $image_url;
            $products_data['products_title'] = $products_title;

            if(false !== $products_data) {
                    $mv_up_selling_products[] = $products_data;
            }
        }

        return $mv_up_selling_products;
    }
		
    //////////////////////////////////////////////////////////////////////////////////////////////////////
    // Up-Selling Artikel laden und an View übergeben.
    //////////////////////////////////////////////////////////////////////////////////////////////////////
    private function loadUpSelling($view, $qb) {
        $mv_up_selling_products = $this->loadUpSellingProducts($view, $qb);
        $view->assign('mv_up_selling_products', $mv_up_selling_products);
    }
    
    /*
     * Cross-Selling Daten laden.
     */
    private function loadCrossSellingProducts($view, $qb) {
        //Artikel-ID des aktuellen Artikels:
        $articleID = $view->sArticle['articleID'];

        //Artikel-Detail-ID des Hauptartikels:
        $article_detail_id = (int)$this->getMainArticleDetailIdByArticleId($articleID);

        if(0 === $article_detail_id) {
        return array();
        }

        //Load cross-Selling settings from article attributes database.
        $qb->select('mv_crosselling')
            ->from('s_articles_attributes')
            ->where('s_articles_attributes.articledetailsID = :article_detail_id')
            ->setParameter('article_detail_id', $article_detail_id);

        $single = $qb->execute()->fetch();

        if(!isset($single) || !isset($single['mv_crosselling'])) {
                return array();
        }

        //Analyse results
        $cs = $single['mv_crosselling'];

        $cs_parts = explode("\n", $cs);

        if(!is_array($cs_parts) || count($cs_parts) < 1) {
                return array();
        }

        $mv_cross_selling_products = array();

        foreach($cs_parts as $line) {
            $line_parts = explode('|', $line, 2);

            $products_model = $line_parts[0];
            $image_url = $line_parts[1];

            //Load products data by products model.
            $products_data = $this->loadProductsDataByProductsModel($products_model);

            if(false === $products_data) {
                continue;
            }

            //Load products title or short title..
            $products_title = $this->loadProductsTitleOrShortTitleByProductsId($products_data['articleID']);

            $products_data['image_url'] = $image_url;
            $products_data['products_title'] = $products_title;

            if(false !== $products_data) {
                    $mv_cross_selling_products[] = $products_data;
            }
        }

        return $mv_cross_selling_products;
    }
		
    //////////////////////////////////////////////////////////////////////////////////////////////////////
    // Cross-Selling Daten laden und an View übergeben.
    //////////////////////////////////////////////////////////////////////////////////////////////////////
    private function loadCrossSelling($view, $qb) {
        $mv_cross_selling_products = $this->loadCrossSellingProducts($view, $qb);
        $view->assign('mv_cross_selling_products', $mv_cross_selling_products);
    }
		
    //////////////////////////////////////////////////////////////////////////////////////////////////////
    // Artikelname laden
    //////////////////////////////////////////////////////////////////////////////////////////////////////
    public function loadProductsTitleOrShortTitleByProductsId($articleID) {
        $b_load_main_title = false;
        $article_details_id = 0;

        $qb = Shopware()->Container()->get('dbal_connection')->createQueryBuilder();

        //Load the "main" products description(, if this product has options for example, the main contains the attributes values we are looking for)
        $qb->select('id')
            ->from('s_articles_details')
            ->where('articleID = :articleID')
            ->andWhere('kind = 1')
            ->setParameter(':articleID', $articleID);
        $single = $qb->execute()->fetch();

        if(!isset($single) || !isset($single['id']) || strlen($single['id']) < 1 || 0 == (int)$single['id']) {
            $b_load_main_title = true;
        } else {
            $article_details_id = $single['id'];
        }

        //Load cross-Selling settings from article attributes database.
        if(false === $b_load_main_title) {
            $qb = Shopware()->Container()->get('dbal_connection')->createQueryBuilder();

            $qb->select('mv_short_title')
                ->from('s_articles_attributes')
                ->Where('articledetailsID = :articledetailsID')
                ->setParameter('articledetailsID', $article_details_id);
            $single = $qb->execute()->fetch();

            if(!isset($single) || !isset($single['mv_short_title']) || strlen($single['mv_short_title']) < 1) {
                $b_load_main_title = true;
            }
        }

        //Try to load the default title..
        if(true === $b_load_main_title) {
            $qb = Shopware()->Container()->get('dbal_connection')->createQueryBuilder();

            $qb->select('name')
                ->from('s_articles')
                ->where('id = :articleID')
                ->setParameter('articleID', $articleID);

            $single = $qb->execute()->fetch();			

            if(!isset($single) || !isset($single['name'])) {
                return '';
            }

            return $single['name'];
        }

        return $single['mv_short_title'];
    }
		
    //////////////////////////////////////////////////////////////////////////////////////////////////////
    // Load products data by products model.
    //////////////////////////////////////////////////////////////////////////////////////////////////////
    public function loadProductsDataByProductsModel($products_model) {
        //Get products id by products_model.
        $products_id = 0;

        $qb = Shopware()->Container()->get('dbal_connection')->createQueryBuilder();

        //Load cross-Selling settings from article attributes database.
        $qb->select('articleID')
            ->from('s_articles_details')
            ->where('s_articles_details.ordernumber = :ordernumber')
            ->setParameter('ordernumber', $products_model);

        $single = $qb->execute()->fetch();

        if(!isset($single) || !isset($single['articleID'])) {
            return false;
        }

        //get Article Detail data..
        $article = Shopware()->Container()->get('models')->getRepository( \Shopware\Models\Article\Article::class)->find($single['articleID']);

        //////////////////////////////////////////////////////////////////////////////////////////////
        //get article price (or lowest price, if it has blockPrices)
        //////////////////////////////////////////////////////////////////////////////////////////////
        $formated_price = '';
        $article_price = Shopware()->Container()->get('models')->getRepository( \Shopware\Models\Article\Price::class)->find($single['articleID']);
        $customer = null;
        $userId = Shopware()->Container()->get('session')->get('sUserId');
        $number = $products_model;

        /// @var \Shopware\Bundle\StoreFrontBundle\Struct\ShopContextInterface $sourceContext
        $sourceContext = Shopware()->Container()->get('shopware_storefront.context_service')->getShopContext();

        // Kundengruppe ermitteln.
        $kundengruppe = $sourceContext->getCurrentCustomerGroup()->getKey();

        // @var \Shopware\Bundle\StoreFrontBundle\Service\ListProductServiceInterface $listProductService 
        $listProductService = Shopware()->Container()->get('shopware_storefront.list_product_service');

        // @var \Shopware\Models\Customer\Group $customerGroup 
        $customerGroup = Shopware()->Container()->get('models')->getRepository( \Shopware\Models\Customer\Group::class )->findOneBy(array('key' => $kundengruppe));

        // Kundengruppendaten laden.
        // @var Shopware\Bundle\StoreFrontBundle\Struct\Customer\Group $customerGroupStruct 
        $customerGroupStruct = new \Shopware\Bundle\StoreFrontBundle\Struct\Customer\Group();
        $customerGroupStruct->setKey($customerGroup->getKey());
        $customerGroupStruct->setId($customerGroup->getId());
        $customerGroupStruct->setName($customerGroup->getName());
        $customerGroupStruct->setDisplayGrossPrices($customerGroup->getTax());
        $customerGroupStruct->setInsertedGrossPrices($customerGroup->getTaxInput());
        $customerGroupStruct->setMinimumOrderValue($customerGroup->getMinimumOrder());
        $customerGroupStruct->setPercentageDiscount($customerGroup->getDiscount());
        $customerGroupStruct->setSurcharge($customerGroup->getMinimumOrderSurcharge());
        $customerGroupStruct->setUseDiscount($customerGroup->getMode());

        // Shop-Kontext laden.
        $targetShopContext = new \Shopware\Bundle\StoreFrontBundle\Struct\ShopContext(
            $sourceContext->getBaseUrl(),
            $sourceContext->getShop(),
            $sourceContext->getCurrency(),
            $customerGroupStruct,
            $sourceContext->getFallbackCustomerGroup(),
            $sourceContext->getTaxRules(),
            $sourceContext->getPriceGroups(),
            $sourceContext->getArea(),
            $sourceContext->getCountry(),
            $sourceContext->getState()
        );

        //////////////////////////////////////////////////////////////////////////////////
        // @var \Shopware\Bundle\StoreFrontBundle\Struct\ListProduct $product
        //////////////////////////////////////////////////////////////////////////////////
        $product = $listProductService->get($number, $targetShopContext);

        if(NULL === $product) {
                return false;
        }

        $price = $product->getCheapestPrice()->getCalculatedPrice();
        $formated_price = Shopware()->Modules()->Articles()->sFormatPrice($price);
        //////////////////////////////////////////////////////////////////////////////////////////////
        // EOF - get article price (or lowest price, if it has blockPrices)
        //////////////////////////////////////////////////////////////////////////////////////////////
        //sArticle
        $router = Shopware()->Container()->get('router');
        $assembleParams = array(
                'module' => 'frontend',
                'sViewport' => 'detail',
                'sArticle' => $article->getId()
        );
        $link = $router->assemble($assembleParams);

        $products_data = array(
                'articleID' => $article->getId(),
                'link' => $link,
                'formated_price' => $formated_price
        );

        return $products_data;
    }
		
	//////////////////////////////////////////////////////////////////////////////////////////////////////
    // @inheritdoc
    // Da es in der ArticleDetail Tabelle mehrere Artikel mit der selben ID geben kann
    // (welche dann Varianten sind), laden wir hier die Artikel-Detail Id des Hauptartikels
    // aus der Tabelle s_articles_details.
    // Der sollte als "kind=1" gespeichert sein.
    //////////////////////////////////////////////////////////////////////////////////////////////////////
    private function getMainArticleDetailIdByArticleId($articleID) {
        $qb = Shopware()->Container()->get('dbal_connection')->createQueryBuilder();

        $qb->select('id')
            ->from('s_articles_details')
            ->where('articleID = :articleID AND kind = 1')
            ->setParameter('articleID', $articleID);

        $single = $qb->execute()->fetch();

        if(!isset($single) || !isset($single['id'])) {
            return false;
        }

        return $single['id'];
    }
}