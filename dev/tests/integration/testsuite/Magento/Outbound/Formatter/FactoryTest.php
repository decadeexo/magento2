<?php
/**
 * Magento_Outbound_Formatter_Factory
 *
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @copyright          Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license            http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Magento_Outbound_Formatter_FactoryTest extends PHPUnit_Framework_TestCase
{
    /** @var Magento_Outbound_Formatter_Factory */
    protected $_formatterFactory;

    public function setUp()
    {
        $this->_formatterFactory = Mage::getObjectManager()->get('Magento_Outbound_Formatter_Factory');
    }

    public function testGetFormatter()
    {
        $formatter = $this->_formatterFactory->getFormatter(Magento_Outbound_EndpointInterface::FORMAT_JSON);
        $this->assertInstanceOf('Magento_Outbound_Formatter_Json', $formatter);
    }

    public function testGetFormatterIsCached()
    {
        $formatter = $this->_formatterFactory->getFormatter(Magento_Outbound_EndpointInterface::FORMAT_JSON);
        $formatter2 = $this->_formatterFactory->getFormatter(Magento_Outbound_EndpointInterface::FORMAT_JSON);
        $this->assertSame($formatter, $formatter2);
    }
}