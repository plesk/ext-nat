<?php

class IndexController extends pm_Controller_Action
{

    public function indexAction()
    {
        $this->view->pageTitle = $this->lmsg('indexPageTitle');
        $this->view->list = $this->_getIpsList();
    }

    public function indexDataAction()
    {
        $this->_helper->json($this->_getIpsList()->fetchData());
    }

    public function updateAddressAction()
    {
        $ips = Modules_Nat_NatManager::getIpAddresses();
        // TODO: add validation
        $mainIp = $this->_request->getParam('ip');
        $publicIp = $ips[$mainIp];

        $this->view->pageTitle = $this->lmsg('updateAddressPageTitle');

        // TODO: add localization
        $form = new pm_Form_Simple();
        $form->addElement('text', 'mainIp', array(
            'label' => 'Main (or private IP)',
            'value' => $mainIp,
            'required' => true,
            'validators' => array(
                array('NotEmpty', true),
            ),
        ));
        $form->addElement('text', 'publicIp', array(
            'label' => 'Public IP',
            'value' => $publicIp,
        ));
        $form->addControlButtons(array(
            'cancelLink' => pm_Context::getBaseUrl(),
        ));

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            Modules_Nat_NatManager::updateAddress($form->getValue('mainIp'), $form->getValue('publicIp'));

            $this->_status->addMessage('info', 'IP address was successfully updated.');
            $this->_helper->json(array('redirect' => pm_Context::getBaseUrl()));
        }

        $this->view->form = $form;
    }

    private function _getIpsList()
    {
        $ips = Modules_Nat_NatManager::getIpAddresses();
        $data = array();
        foreach (Modules_Nat_NatManager::getIpAddresses() as $mainIp => $publicIp) {
            $data[] = array(
                'column-1' => '<a href="' . pm_Context::getActionUrl('index', 'update-address') . '?ip=' . $mainIp . '">' . $mainIp . '</a>',
                'column-2' => $publicIp,
            );
        }

        $list = new pm_View_List_Simple($this->view, $this->_request);
        $list->setData($data);
        $list->setColumns(array(
            'column-1' => array(
                'title' => 'Main (or private IP)',
                'noEscape' => true,
            ),
            'column-2' => array(
                'title' => 'Public IP',
            ),
        ));
        $list->setDataUrl(array('action' => 'list-data'));

        return $list;
    }

}
