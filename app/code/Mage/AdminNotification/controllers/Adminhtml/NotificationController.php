<?php
/**
 * Adminhtml AdminNotification controller
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
 * @category    Mage
 * @package     Mage_AdminNotification
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Mage_AdminNotification_Adminhtml_NotificationController extends Mage_Backend_Controller_ActionAbstract
{
    public function indexAction()
    {
        $this->_title($this->__('Notifications'));

        $this->loadLayout()
            ->_setActiveMenu('Mage_AdminNotification::system_adminnotification')
            ->_addBreadcrumb(
                Mage::helper('Mage_AdminNotification_Helper_Data')->__('Messages Inbox'),
                Mage::helper('Mage_AdminNotification_Helper_Data')->__('Messages Inbox')
            )->renderLayout();
    }

    public function markAsReadAction()
    {
        $notificationId = (int)$this->getRequest()->getParam('id');
        if ($notificationId) {
            try {
                $this->_objectManager->create('Mage_AdminNotification_Model_NotificationService')
                    ->markAsRead($notificationId);
                $this->_session->addSuccess(
                    Mage::helper('Mage_AdminNotification_Helper_Data')->__('The message has been marked as Read.')
                );
            } catch (Mage_Core_Exception $e) {
                $this->_session->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_session->addException($e,
                    Mage::helper('Mage_AdminNotification_Helper_Data')
                        ->__("We couldn't mark the notification as Read because of an error.")
                );
            }

            $this->_redirectReferer();
            return;
        }
        $this->_redirect('*/*/');
    }

    /**
     * Mark notification as read (AJAX action)
     */
    public function ajaxMarkAsReadAction()
    {
        if (!$this->getRequest()->getPost()) {
            return;
        }
        $notificationId = (int)$this->getRequest()->getPost('id');
        $responseData = array();
        try {
            $this->_objectManager->create('Mage_AdminNotification_Model_NotificationService')
                ->markAsRead($notificationId);
            $responseData['success'] = true;
        } catch (Exception $e) {
            $responseData['success'] = false;
        }
        $this->getResponse()->setBody(
            $this->_objectManager->create('Mage_Core_Helper_Data')->jsonEncode($responseData)
        );
    }

    public function massMarkAsReadAction()
    {
        $ids = $this->getRequest()->getParam('notification');
        if (!is_array($ids)) {
            $this->_session->addError(Mage::helper('Mage_AdminNotification_Helper_Data')->__('Please select messages.'));
        } else {
            try {
                foreach ($ids as $id) {
                    $model = Mage::getModel('Mage_AdminNotification_Model_Inbox')
                        ->load($id);
                    if ($model->getId()) {
                        $model->setIsRead(1)
                            ->save();
                    }
                }
                $this->_getSession()->addSuccess(
                    Mage::helper('Mage_AdminNotification_Helper_Data')
                        ->__('A total of %d record(s) have been marked as Read.', count($ids))
                );
            } catch (Mage_Core_Exception $e) {
                $this->_session->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_session->addException($e,
                    Mage::helper('Mage_AdminNotification_Helper_Data')
                        ->__("We couldn't mark the notification as Read because of an error.")
                );
            }
        }
        $this->_redirect('*/*/');
    }

    public function removeAction()
    {
        if ($id = $this->getRequest()->getParam('id')) {
            $model = Mage::getModel('Mage_AdminNotification_Model_Inbox')
                ->load($id);

            if (!$model->getId()) {
                $this->_redirect('*/*/');
                return ;
            }

            try {
                $model->setIsRemove(1)
                    ->save();
                $this->_session->addSuccess(
                    Mage::helper('Mage_AdminNotification_Helper_Data')->__('The message has been removed.')
                );
            } catch (Mage_Core_Exception $e) {
                $this->_session->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_session->addException($e,
                    Mage::helper('Mage_AdminNotification_Helper_Data')
                        ->__("We couldn't remove the messages because of an error.")
                );
            }

            $this->_redirect('*/*/');
            return;
        }
        $this->_redirect('*/*/');
    }

    public function massRemoveAction()
    {
        $ids = $this->getRequest()->getParam('notification');
        if (!is_array($ids)) {
            $this->_session->addError(
                Mage::helper('Mage_AdminNotification_Helper_Data')->__('Please select messages.')
            );
        } else {
            try {
                foreach ($ids as $id) {
                    $model = Mage::getModel('Mage_AdminNotification_Model_Inbox')
                        ->load($id);
                    if ($model->getId()) {
                        $model->setIsRemove(1)
                            ->save();
                    }
                }
                $this->_getSession()->addSuccess(
                    Mage::helper('Mage_AdminNotification_Helper_Data')
                        ->__('Total of %d record(s) have been removed.', count($ids))
                );
            } catch (Mage_Core_Exception $e) {
                $this->_session->addError($e->getMessage());
            } catch (Exception $e) {
                $session->addException($e,
                    Mage::helper('Mage_AdminNotification_Helper_Data')
                        ->__("We couldn't remove the messages because of an error."));
            }
        }
        $this->_redirectReferer();
    }

    protected function _isAllowed()
    {
        switch ($this->getRequest()->getActionName()) {
            case 'markAsRead':
                $acl = 'Mage_AdminNotification::mark_as_read';
                break;

            case 'massMarkAsRead':
                $acl = 'Mage_AdminNotification::mark_as_read';
                break;

            case 'remove':
                $acl = 'Mage_AdminNotification::adminnotification_remove';
                break;

            case 'massRemove':
                $acl = 'Mage_AdminNotification::adminnotification_remove';
                break;

            default:
                $acl = 'Mage_AdminNotification::show_list';
        }
        return $this->_authorization->isAllowed($acl);
    }
}
