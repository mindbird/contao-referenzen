<?php

namespace Mindbird\Contao\Person;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Mindbird\Contao\Reference\ReferenceBundle;

class ContaoManagerPlugin implements BundlePluginInterface
{
    /**
     * {@inheritdoc}
     */
    public function getBundles(ParserInterface $parser)
    {
        return [
            BundleConfig::create(ReferenceBundle::class)->setLoadAfter([ContaoCoreBundle::class])->setReplace(['person'])
        ];
    }
}