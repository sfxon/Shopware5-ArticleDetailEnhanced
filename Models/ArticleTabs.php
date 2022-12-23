<?php

namespace MvArticleDetail\Models;

use Shopware\Components\Model\ModelEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="mv_article_tabs")
 */
class ArticleTabs extends ModelEntity
{
    /**
     * @var integer $id
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string $title
     *
     * @ORM\Column()
     */
    private $title;

    /**
     * @var string $fieldname
     *
     * @ORM\Column()
     */
    private $fieldname;

    /**
     * @var string $sortorder
     *
     * @ORM\Column()
     */
    private $sortorder;
		
		/**
     * @var string $side
     *
     * @ORM\Column()
     */
    private $side;
}