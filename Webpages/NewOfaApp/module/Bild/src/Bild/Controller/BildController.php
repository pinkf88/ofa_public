<?php

namespace Bild\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Db\Sql\Select;
use Zend\Session\Container;
use Bild\Model\Bild;
use Bild\Model\Land;
use Bild\Model\Motiv;
use Bild\Form\BildForm;
use Bild\Form\BildSelectForm;

class BildController extends AbstractActionController
{
    protected $bildTable;
    protected $serieTable;
    protected $ortTable;
    protected $landTable;
    protected $motivTable;
    protected $bildMotivTable;
    protected $infoTable;
    protected $session;

    public function __construct()
    {
        $this->session = new Container('ofa_bild');
    }

    public function indexAction()
    {
        $order_by = 'nummer';
        $bildtyp = 0; // 0: alle, 1: nur Bilder, 2: nur Tickets, 3: nur Videos
        $jahr = date('Y');
        $ortid = 0;
        $landid = 0;
        $nummer_von = '';
        $nummer_bis = '';
        $suchtext = '';
        $wertung_min = 0;
        $countperpage = 250;
        $serieid = 0;

        if ($this->session->offsetExists('bildtyp')) {
            $bildtyp = intval($this->session->offsetGet('bildtyp'));
        }

        if ($this->session->offsetExists('jahr')) {
            $jahr = intval($this->session->offsetGet('jahr'));
        }

        if ($this->session->offsetExists('ortid')) {
            $ortid = intval($this->session->offsetGet('ortid'));
        }

        if ($this->session->offsetExists('landid')) {
            $landid = intval($this->session->offsetGet('landid'));
        }

        if ($this->session->offsetExists('nummer_von')) {
            $nummer_von = $this->session->offsetGet('nummer_von');
        }

        if ($this->session->offsetExists('nummer_bis')) {
            $nummer_bis = $this->session->offsetGet('nummer_bis');
        }

        if ($this->session->offsetExists('suchtext')) {
            $suchtext = $this->session->offsetGet('suchtext');
        }

        if ($this->session->offsetExists('wertung_min')) {
            $wertung_min = intval($this->session->offsetGet('wertung_min'));
        }

        if ($this->session->offsetExists('countperpage')) {
            $countperpage = intval($this->session->offsetGet('countperpage'));
        }

        if ($this->session->offsetExists('serieid')) {
            $serieid = intval($this->session->offsetGet('serieid'));
        }

        if ($this->getRequest()) {
            if ($this->getRequest()->isPost()) {
                if ($this->getRequest()->getPost('bildtyp')) {
                    $bildtyp = intval($this->getRequest()->getPost('bildtyp'));
                } else {
                    $bildtyp = 0;
                }

                if ($this->getRequest()->getPost('jahr')) {
                    $jahr = intval($this->getRequest()->getPost('jahr'));
                } else {
                    $jahr = 0;
                }

                if ($this->getRequest()->getPost('ortid')) {
                    $ortid = intval($this->getRequest()->getPost('ortid'));
                } else {
                    $ortid = 0;
                }

                if ($this->getRequest()->getPost('landid')) {
                    $landid = intval($this->getRequest()->getPost('landid'));
                } else {
                    $landid = 0;
                }

                if ($this->getRequest()->getPost('nummer_von')) {
                    $nummer_von = $this->getRequest()->getPost('nummer_von');
                } else {
                    $nummer_von = "";
                }

                if ($this->getRequest()->getPost('nummer_bis')) {
                    $nummer_bis = $this->getRequest()->getPost('nummer_bis');
                } else {
                    $nummer_bis = "";
                }

                if ($this->getRequest()->getPost('suchtext')) {
                    $suchtext = $this->getRequest()->getPost('suchtext');
                } else {
                    $suchtext = "";
                }

                if ($this->getRequest()->getPost('wertung_min')) {
                    $wertung_min = intval($this->getRequest()->getPost('wertung_min'));
                } else {
                    $wertung_min = 0;
                }

                if ($this->getRequest()->getPost('countperpage')) {
                    $countperpage = intval($this->getRequest()->getPost('countperpage'));
                } else {
                    $countperpage = 250;
                }

                if ($this->getRequest()->getPost('serieid')) {
                    $serieid = intval($this->getRequest()->getPost('serieid'));
                } else {
                    $serieid = 0;
                }
            }
        }

        $this->session->offsetSet('bildtyp', $bildtyp);
        $this->session->offsetSet('jahr', $jahr);
        $this->session->offsetSet('ortid', $ortid);
        $this->session->offsetSet('landid', $landid);
        $this->session->offsetSet('nummer_von', $nummer_von);
        $this->session->offsetSet('nummer_bis', $nummer_bis);
        $this->session->offsetSet('suchtext', $suchtext);
        $this->session->offsetSet('wertung_min', $wertung_min);
        $this->session->offsetSet('countperpage', $countperpage);
        $this->session->offsetSet('serieid', $serieid);

        if ($this->params()->fromRoute('order_by')) {
            $order_by = $this->params()->fromRoute('order_by');
        } else if ($this->session->offsetExists('order_by')) {
            $order_by = $this->session->offsetGet('order_by');
        } else {
            $order_by = 'nummer';
        }

        $this->session->offsetSet('order_by', $order_by);

        $order = Select::ORDER_ASCENDING;

        $select = new Select();

        if ($jahr > 0) {
            $select->where('YEAR(ofa_bild.datum)="' . $jahr . '"');
        }

        if ($bildtyp == 1) {
            $select->where('ticket="0"');
        } else if ($bildtyp == 2) {
            $select->where('ticket="1"');
        } else if ($bildtyp == 3) {
            $select->where('beschreibung LIKE "YOUTUBE=%"');
        }

        if ($ortid > 0) {
            $select->where('ortid=' . $ortid);
        }

        if ($landid > 0) {
            $select->where('landid=' . $landid);
        }

        if ($wertung_min > 0) {
            $select->where('wertung>=' . $wertung_min);
        }

        if (strlen($suchtext) > 0) {
            $pos = strpos($suchtext, 'DATUM');
            $where = '';

            if ($pos === false) {
                $pos = strpos($suchtext, ' AND ');

                if ($pos === false) {
                    $pos = strpos($suchtext, ' OR ');

                    if ($pos === false) {
                        $where = '(beschreibung LIKE "%' . $suchtext . '%" OR bemerkung LIKE "%' . $suchtext . '%" OR info LIKE "%' . $suchtext . '%")';
                    } else {
                        $splits = explode(' OR ', $suchtext);
                        $where = '(';

                        for ($i = 0; $i < count($splits); $i++) {
                            if ($i > 0) {
                                $where .= ' OR ';
                            }

                            $where .= 'beschreibung LIKE "%' . $splits[$i] . '%" OR bemerkung LIKE "%' . $splits[$i] . '%" OR info LIKE "%' . $splits[$i] . '%"';
                        }

                        $where .= ')';
                    }
                } else {
                    $splits = explode(' AND ', $suchtext);
                    $where = '(';

                    for ($i = 0; $i < count($splits); $i++) {
                        if ($i > 0) {
                            $where .= ' AND ';
                        }

                        $where .= '(beschreibung LIKE "%' . $splits[$i] . '%" OR bemerkung LIKE "%' . $splits[$i] . '%" OR info LIKE "%' . $splits[$i] . '%")';
                    }

                    $where .= ')';
                }
            } else {
                $datum_text = substr($suchtext, 5);
                $datum = explode('-', $datum_text);

                if (count($datum) < 2) {
                    $datum = explode('.', $datum_text);
                }

                if (count($datum) >= 2) {
                    $where = 'DAY(ofa_bild.datum)="' . $datum[0] . '" AND MONTH(ofa_bild.datum)="' . $datum[1] . '"';

                    if (count($datum) == 3) {
                        $where .= ' AND YEAR(ofa_bild.datum)="' . $datum[2] . '"';
                    }
                }
            }

            if ($where != '') {
                $select->where($where);
            }
        }

        if (strlen($nummer_von) > 0 && strlen($nummer_bis) == 0) {
            $select->where('ofa_bild.nummer=' . $nummer_von);
        } else if (strlen($nummer_von) > 0 && strlen($nummer_bis) > 0) {
            $select->where('(ofa_bild.nummer>=' . $nummer_von . ' AND ofa_bild.nummer<=' . $nummer_bis . ')');
        }

        if ($order_by == 'nummer') {
            $select->order('jahr DESC')->order($order_by . ' ' . $order);
        } else {
            $select->order($order_by . ' ' . $order);
        }

        $paginator = $this->getBildTable()->fetchAll($select);
        $paginator->setCurrentPageNumber((int) $this->params()->fromQuery('page', 1));
        $paginator->setItemCountPerPage($countperpage);

        $this->session->offsetSet('page', $this->params()->fromQuery('page', 1));

        $selectform = new BildSelectForm($this->getBildTable()
            ->fetchAllYears(), $this->getOrtTable()
            ->fetchAll(), $this->getLandTable()
            ->fetchAll(), $this->getSerieTable()
            ->fetchAll());

        if ($bildtyp > 0) {
            $selectform->get('bildtyp')->setValue($bildtyp);
        }

        if ($jahr > 0) {
            $selectform->get('jahr')->setValue($jahr);
        }

        if ($ortid > 0) {
            $selectform->get('ortid')->setValue($ortid);
        }

        if ($landid > 0) {
            $selectform->get('landid')->setValue($landid);
        }

        if (strlen($nummer_von) > 0) {
            $selectform->get('nummer_von')->setValue($nummer_von);
        }

        if (strlen($nummer_bis) > 0) {
            $selectform->get('nummer_bis')->setValue($nummer_bis);
        }

        if (strlen($suchtext) > 0) {
            $selectform->get('suchtext')->setValue($suchtext);
        }

        $selectform->get('wertung_min')->setValue($wertung_min);

        $selectform->get('countperpage')->setValue($countperpage);

        if ($serieid > 0) {
            $selectform->get('serieid')->setValue($serieid);
        }

        $key = 'bildinfo';
        $bildinfo = $this->getInfoTable()->getValue($key);

        return new ViewModel(array(
                'paginator' => $paginator,
                'selectform' => $selectform,
                'bildinfo' => $bildinfo
        ));
    }

    public function addAction()
    {
        $session = $this->session;

        $form = new BildForm($this->getOrtTable()->fetchAll());

        $form->get('submit1')->setValue('Ok');

        $form->get('submit2')->setValue('Ok / Neu');

        if ($session->offsetExists('input_nummer')) {
            $form->get('nummer')->setValue(intval($session->offsetGet('input_nummer')) + 1);
        }

        if ($session->offsetExists('input_datei')) {
            if ($session->offsetGet('input_datei') != '0') {
                $form->get('datei')->setValue(intval($session->offsetGet('input_datei')) + 1);
            }
        }

        if ($session->offsetExists('input_datum')) {
            $form->get('datum')->setValue($session->offsetGet('input_datum'));
        }

        if ($session->offsetExists('input_jahrflag')) {
            $form->get('jahrflag')->setValue($session->offsetGet('input_jahrflag'));
        }

        if ($session->offsetExists('input_ortid')) {
            $form->get('ortid')->setValue($session->offsetGet('input_ortid'));
        }

        if ($session->offsetExists('input_beschreibung')) {
            $form->get('beschreibung')->setValue($session->offsetGet('input_beschreibung'));
        }

        if ($session->offsetExists('input_bemerkung')) {
            $form->get('bemerkung')->setValue($session->offsetGet('input_bemerkung'));
        }

        if ($session->offsetExists('input_wertung')) {
            $form->get('wertung')->setValue(intval($session->offsetGet('input_wertung')));
        }

        if ($session->offsetExists('input_panorama')) {
            $form->get('panorama')->setValue($session->offsetGet('input_panorama'));
        }

        if ($session->offsetExists('input_ticket')) {
            $form->get('ticket')->setValue($session->offsetGet('input_ticket'));
        }

        if ($session->offsetExists('input_ohneort')) {
            $form->get('ohneort')->setValue($session->offsetGet('input_ohneort'));
        }

        if ($session->offsetExists('input_ohneland')) {
            $form->get('ohneland')->setValue($session->offsetGet('input_ohneland'));
        }

        $request = $this->getRequest();

        if ($request->isPost()) {
            $bild = new Bild($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));

            $form->setInputFilter($bild->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $bild->exchangeArray($form->getData());

                $session->offsetSet('input_nummer', $bild->nummer);
                $session->offsetSet('input_datei', $bild->datei);
                $session->offsetSet('input_datum', $bild->datum);
                $session->offsetSet('input_jahrflag', $bild->jahrflag);
                $session->offsetSet('input_ortid', $bild->ortid);
                $session->offsetSet('input_beschreibung', $bild->beschreibung);
                $session->offsetSet('input_bemerkung', $bild->bemerkung);
                $session->offsetSet('input_wertung', $bild->wertung);
                $session->offsetSet('input_panorama', $bild->panorama);
                $session->offsetSet('input_ticket', $bild->ticket);
                $session->offsetSet('input_ohneort', $bild->ohneort);
                $session->offsetSet('input_ohneland', $bild->ohneland);

                $id = $this->getBildTable()->saveBild($bild);

                if ($request->getPost('submit1')) {
                    $page = '1';

                    if ($this->session->offsetExists('page')) {
                        $page = $this->session->offsetGet('page') . '#' . $id;
                    }

                    return $this->redirect()->toUrl('/bild?page=' . $page);
                }

                $form->get('nummer')->setValue(intval($form->get('nummer')->getValue()) + 1);

                if ($form->get('datei') ->getValue() != '') {
                    $form->get('datei')->setValue(intval($form->get('datei')->getValue()) + 1);
                }
            }
        }

        return array(
            'form' => $form
        );
    }

    public function editAction()
    {
        $session = $this->session;

        $id = (int) $this->params()->fromRoute('id', 0);

        if (!$id) {
            return $this->redirect()->toRoute('bild', array(
                'action' => 'add'
            ));
        }

        $bild = '';

        try {
            $bild = $this->getBildTable()->getBild($id);
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('bild', array(
                'action' => 'index'
            ));
        }

        $form = new BildForm($this->getOrtTable()->fetchAll());

        $form->bind($bild);
        $form->get('submit1')->setValue('Ändern');
        $form->get('submit2')->setValue('');
        $form->get('ortid')->setValue($bild->ortid);

        $request = $this->getRequest();

        if ($request->isPost()) {
            $form->setInputFilter($bild->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $session->offsetSet('input_nummer', $bild->nummer);

                if ($bild->datei == '0') {
                    $bild->datei = 'null';
                }

                $session->offsetSet('input_datei', $bild->datei);
                $session->offsetSet('input_datum', $bild->datum);
                $session->offsetSet('input_jahrflag', $bild->jahrflag);
                $session->offsetSet('input_ortid', $bild->ortid);
                $session->offsetSet('input_beschreibung', $bild->beschreibung);
                $session->offsetSet('input_bemerkung', $bild->bemerkung);
                $session->offsetSet('input_wertung', $bild->wertung);
                $session->offsetSet('input_panorama', $bild->panorama);
                $session->offsetSet('input_ticket', $bild->ticket);
                $session->offsetSet('input_ohneort', $bild->ohneort);
                $session->offsetSet('input_ohneland', $bild->ohneland);

                $this->getBildTable()->saveBild($bild);

                $page = '1';

                if ($this->session->offsetExists('page')) {
                    $page = $this->session->offsetGet('page') . '#' . $id;
                }

                return $this->redirect()->toUrl('/bild?page=' . $page);
            }
        }

        return array(
            'id'        => $id,
            'form'      => $form,
            'polygon'   => '$bild->polygon'
        );
    }

    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);

        if (!$id) {
            return $this->redirect()->toRoute('bild');
        }

        $request = $this->getRequest();

        if ($request->isPost()) {
            $del = $request->getPost('del', 'Nein');

            if ($del == 'Ja') {
                $id = (int) $request->getPost('id');
                $this->getBildTable()->deleteBild($id);
            }

            // Redirect to list of bilds
            return $this->redirect()->toRoute('bild');
        }

        return array(
            'id' => $id,
            'bild' => $this->getBildTable()->getBild($id)
        );
    }

    public function getBildTable()
    {
        if (!$this->bildTable) {
            $sm = $this->getServiceLocator();
            $this->bildTable = $sm->get('Bild\Model\BildTable');
        }

        return $this->bildTable;
    }

    public function getSerieTable()
    {
        if (!$this->serieTable) {
            $sm = $this->getServiceLocator();
            $this->serieTable = $sm->get('Bild\Model\SerieTable');
        }

        return $this->serieTable;
    }

    public function getOrtTable()
    {
        if (!$this->ortTable) {
            $sm = $this->getServiceLocator();
            $this->ortTable = $sm->get('Bild\Model\OrtTable');
        }

        return $this->ortTable;
    }

    public function getLandTable()
    {
        if (!$this->landTable) {
            $sm = $this->getServiceLocator();
            $this->landTable = $sm->get('Bild\Model\LandTable');
        }

        return $this->landTable;
    }

    public function getMotivTable()
    {
        if (!$this->motivTable) {
            $sm = $this->getServiceLocator();
            $this->motivTable = $sm->get('Bild\Model\MotivTable');
        }

        return $this->motivTable;
    }

    public function getBildMotivTable()
    {
        if (!$this->bildMotivTable) {
            $sm = $this->getServiceLocator();
            $this->bildMotivTable = $sm->get('Bild\Model\BildMotivTable');
        }

        return $this->bildMotivTable;
    }

    public function getInfoTable()
    {
        if (!$this->infoTable) {
            $sm = $this->getServiceLocator();
            $this->infoTable = $sm->get('Bild\Model\InfoTable');
        }

        return $this->infoTable;
    }
}
