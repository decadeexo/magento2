<?php
/**
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
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * An aggregate of a fallback rule that propagates it to every theme according to a hierarchy
 */
class Mage_Core_Model_Design_Fallback_Rule_Theme implements Mage_Core_Model_Design_Fallback_Rule_RuleInterface
{
    /**
     * @var Mage_Core_Model_Design_Fallback_Rule_RuleInterface
     */
    private $_rule;

    /**
     * Constructor
     *
     * @param Mage_Core_Model_Design_Fallback_Rule_RuleInterface $rule
     */
    public function __construct(Mage_Core_Model_Design_Fallback_Rule_RuleInterface $rule)
    {
        $this->_rule = $rule;
    }

    /**
     * Propagate an underlying fallback rule to every theme in a hierarchy: parent, grandparent, etc.
     *
     * {@inheritdoc}
     * @throws InvalidArgumentException
     */
    public function getPatternDirs(array $params)
    {
        if (!array_key_exists('theme', $params) || !($params['theme'] instanceof Mage_Core_Model_ThemeInterface)) {
            throw new InvalidArgumentException(
                'Parameter "theme" should be specified and should implement the theme interface.'
            );
        }
        $result = array();
        /** @var $theme Mage_Core_Model_ThemeInterface */
        $theme = $params['theme'];
        unset($params['theme']);
        while ($theme) {
            if ($theme->getThemePath()) {
                $params['theme_path'] = $theme->getThemePath();
                $result = array_merge($result, $this->_rule->getPatternDirs($params));
            }
            $theme = $theme->getParentTheme();
        }
        return $result;
    }
}
