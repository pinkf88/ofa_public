<?php

namespace Album\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Db\Sql\Select;
use Zend\Validator\Db\NoRecordExists;
use Album\Model\Album;
use Album\Form\AlbumForm;
use Album\Form\AlbumSelectForm;
use Zend\Session\Container;

class AlbumController extends AbstractActionController
{
    protected $AlbumTable;
    protected $session;

    public function __construct()
    {
        $this->session = new Container('ofa_album');
    }

    public function indexAction()
    {
        $ownerid = 0;
        $rating = 0;
        $roomid = 1;
        $albumartist = '';
        $suchtext = '';
        $countperpage = 250;

        if ($this->session->offsetExists('albumartist'))
        {
            $albumartist = $this->session->offsetGet('albumartist');
        }

        if ($this->session->offsetExists('suchtext'))
        {
            $suchtext = $this->session->offsetGet('suchtext');
        }

        if ($this->session->offsetExists('countperpage'))
        {
            $countperpage = intval($this->session->offsetGet('countperpage'));
        }

        if ($this->session->offsetExists('ownerid'))
        {
            $ownerid = intval($this->session->offsetGet('ownerid'));
        }

        if ($this->session->offsetExists('rating'))
        {
            $rating = intval($this->session->offsetGet('rating'));
        }

        if ($this->session->offsetExists('roomid'))
        {
            $roomid = intval($this->session->offsetGet('roomid'));
        }

        if ($this->getRequest())
        {
            if ($this->getRequest()->isPost())
            {
                if ($this->getRequest()->getPost('albumartist'))
                {
                    $albumartist = $this->getRequest()->getPost('albumartist');
                }
                else
                {
                    $albumartist = '';
                }

                if ($this->getRequest()->getPost('suchtext'))
                {
                    $suchtext = $this->getRequest()->getPost('suchtext');
                }
                else
                {
                    $suchtext = "";
                }

                if ($this->getRequest()->getPost('countperpage'))
                {
                    $countperpage = intval($this->getRequest()->getPost('countperpage'));
                }
                else
                {
                    $countperpage = 250;
                }

                if ($this->getRequest()->getPost('ownerid'))
                {
                    $ownerid = intval($this->getRequest()->getPost('ownerid'));
                }
                else
                {
                    $ownerid = 0;
                }

                if ($this->getRequest()->getPost('rating'))
                {
                    $rating = intval($this->getRequest()->getPost('rating'));
                }
                else
                {
                    $rating = 0;
                }

                if ($this->getRequest()->getPost('roomid'))
                {
                    $roomid = intval($this->getRequest()->getPost('roomid'));
                }
                else
                {
                    $roomid = 1;
                }
            }
        }

        $this->session->offsetSet('albumartist', $albumartist);
        $this->session->offsetSet('suchtext', $suchtext);
        $this->session->offsetSet('countperpage', $countperpage);
        $this->session->offsetSet('ownerid', $ownerid);
        $this->session->offsetSet('roomid', $roomid);

        $select = new Select();

        if (strlen($albumartist) > 0)
        {
            $select->where('albumartist LIKE "' . preg_replace('/"/', '\"', $albumartist) . '%"');
        }

        if (strlen($suchtext) > 0)
        {
            $select->where('album LIKE "%' . preg_replace('/"/', '\"', $suchtext) . '%"');
        }

        if ($ownerid > 0) {
            $select->where('ownerid="' . $ownerid . '"');
        }

        if ($rating > 0) {
            $select->where('rating>="' . $rating . '"');
        }

        if ($this->params()->fromRoute('order_by')) {
            if ($this->params()->fromRoute('order_by') == 'album') {
                $select->order(array('album ASC', 'albumartistsort ASC', 'year ASC'));
            } else if ($this->params()->fromRoute('order_by') == 'jahr') {
                $select->order(array('originalyear ASC', 'year ASC', 'album ASC', 'albumartistsort ASC'));
            }
        }
        else {
            $select->order(array('albumartistsort ASC', 'originalyear ASC', 'album ASC', 'year ASC'));
        }

        $paginator = $this->getAlbumTable()
            ->fetchAll($select);

        $paginator->setCurrentPageNumber((int) $this->params()->fromQuery('page', 1));
        $paginator->setItemCountPerPage($countperpage);
        $this->session->offsetSet('page', $this->params()->fromQuery('page', 1));

        $selectform = new AlbumSelectForm($this->getAlbumTable()->fetchAllArtists());

        if (strlen($albumartist) > 0) {
            $selectform->get('albumartist')->setValue($albumartist);
        }

        if (strlen($suchtext) > 0) {
            $selectform->get('suchtext')->setValue($suchtext);
        }
        
        $selectform->get('ownerid')->setValue($ownerid);
        $selectform->get('rating')->setValue($rating);
        $selectform->get('roomid')->setValue($roomid);
        $selectform->get('countperpage')->setValue($countperpage);

        return new ViewModel(array(
                'paginator' => $paginator,
                'selectform' => $selectform
        ));
    }

    public function deleteAction()
    {
        $musicbrainz_albumid = $this->params()
            ->fromRoute('id', 0);

        // echo '$id ' . $musicbrainz_albumid;

        if (! $musicbrainz_albumid)
        {
            return $this->redirect()
                ->toRoute('album');
        }

        $request = $this->getRequest();

        if ($request->isPost())
        {
            $del = $request->getPost('del', 'Nein');

            if ($del == 'Ja')
            {
                $musicbrainz_albumid = $request->getPost('id');
                $this->getAlbumTable()
                    ->deleteAlbum($musicbrainz_albumid);
            }

            // Redirect to list of Albums
            return $this->redirect()
                ->toRoute('album');
        }

        return array(
            'musicbrainz_albumid' => $musicbrainz_albumid,
            'album' => $this->getAlbumTable()->getAlbum($musicbrainz_albumid)
        );
    }

    public function getAlbumTable()
    {
        if (! $this->AlbumTable)
        {
            $sm = $this->getServiceLocator();
            $this->AlbumTable = $sm->get('Album\Model\AlbumTable');
        }

        return $this->AlbumTable;
    }
}
