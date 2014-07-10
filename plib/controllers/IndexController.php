<?php

class IndexController extends pm_Controller_Action
{

    protected $_accessLevel = 'admin';

    public function indexAction()
    {
        $this->view->pageTitle = $this->lmsg('indexPageTitle');
        $this->view->pageHint = $this->lmsg('indexPageHint');
        $this->view->list = $this->_getIpsList();
    }

    public function indexDataAction()
    {
        $this->_helper->json($this->_getIpsList()->fetchData());
    }

    public function updateAddressAction()
    {
        $ips = Modules_Nat_NatManager::getIpAddresses();
        $mainIp = $this->_request->getParam('ip');

        if (!isset($ips[$mainIp])) {
            $this->_redirect('index');
        }

        $publicIp = $ips[$mainIp];

        $this->view->pageTitle = $this->lmsg('updateAddressPageTitle');

        $form = new pm_Form_Simple();
        $form->addElement('simpleText', 'mainIpText', array(
            'label' => $this->lmsg('mainIp'),
            'value' => $mainIp,
        ));
        $form->addElement('text', 'publicIp', array(
            'label' => $this->lmsg('publicIp'),
            'value' => $publicIp,
            'required' => true,
            'validators' => array(
                array('NotEmpty', true),
                array('Ip', true),
            ),
        ));
        $form->addElement('hidden', 'mainIp', array(
            'value' => $mainIp,
        ));
        $form->addControlButtons(array(
            'cancelLink' => pm_Context::getBaseUrl(),
        ));

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            Modules_Nat_NatManager::updateAddress($form->getValue('mainIp'), $form->getValue('publicIp'));

            $this->_status->addMessage('info', $this->lmsg('ipUpdated'));
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
                'title' => $this->lmsg('mainIp'),
                'noEscape' => true,
            ),
            'column-2' => array(
                'title' => $this->lmsg('publicIp'),
            ),
        ));
        $list->setDataUrl(array('action' => 'list-data'));

        return $list;
    }

}
