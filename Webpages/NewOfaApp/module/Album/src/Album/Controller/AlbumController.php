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
        $roomid = 1;
        $albumartist = '';
        $suchtext = '';
        $countperpage = 200;

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
                    $countperpage = 200;
                }

                if ($this->getRequest()->getPost('ownerid'))
                {
                    $ownerid = intval($this->getRequest()->getPost('ownerid'));
                }
                else
                {
                    $ownerid = 0;
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
            $select->where('albumartist="' . $albumartist . '"');
        }

        if (strlen($suchtext) > 0)
        {
            $select->where('album LIKE "%' . $suchtext . '%"');
        }

        if ($ownerid > 0) {
            $select->where('ownerid="' . $ownerid . '"');
        }

        $select->order(array('albumartistsort ASC', 'album ASC', 'year ASC'));

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
        $selectform->get('roomid')->setValue($roomid);
        $selectform->get('countperpage')->setValue($countperpage);

        return new ViewModel(array(
                'paginator' => $paginator,
                'selectform' => $selectform
        ));
    }

    public function addAction()
    {
        $dbAdapter = $this->getServiceLocator()
            ->get('Zend\Db\Adapter\Adapter');

        $form = new AlbumForm(null, $dbAdapter);
        $form->get('submit')
            ->setValue('Ok');

        $request = $this->getRequest();

        if ($request->isPost())
        {
            $Album = new Album($dbAdapter);
            $form->setInputFilter($Album->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid())
            {
                $Album->exchangeArray($form->getData());

                $validator = new NoRecordExists(array(
                        'adapter' => $dbAdapter,
                        'table' => 'ofa_tracks',
                        'field' => 'musicbrainz_albumid'
                ));

                if ($validator->isValid($Album->Album))
                {
                    $this->getAlbumTable()
                        ->saveAlbum($Album);

                    // Redirect to list of Albums
                    return $this->redirect()
                        ->toRoute('Album');
                }
            }
        }

        return array(
                'form' => $form
        );
    }

    public function editAction()
    {
        $musicbrainz_albumid = $this->params()
            ->fromRoute('musicbrainz_albumid', 0);

        if (! $musicbrainz_albumid)
        {
            return $this->redirect()
                ->toRoute('Album', array(
                    'action' => 'add'
            ));
        }

        $Album = '';

        // Get the Album with the specified id. An exception is thrown
        // if it cannot be found, in which case go to the index page.
        try
        {
            $Album = $this->getAlbumTable()
                ->getAlbum($musicbrainz_albumid);
        }
        catch (\Exception $ex)
        {
            return $this->redirect()
                ->toRoute('Album', array(
                    'action' => 'index'
            ));
        }

        $dbAdapter = $this->getServiceLocator()
            ->get('Zend\Db\Adapter\Adapter');

        $form = new AlbumForm(null, $dbAdapter);
        $form->bind($Album);

        $form->get('submit')
            ->setValue('Ändern');

        $request = $this->getRequest();

        if ($request->isPost())
        {
            $form->setInputFilter($Album->getInputFilter());
            $form->setData($request->getPost());

            // print_r($request->getPost());

            if ($form->isValid())
            {
                $this->getAlbumTable()
                    ->saveAlbum($Album);

                // Redirect to list of Albums
                return $this->redirect()
                    ->toRoute('Album');
            }
        }

        return array(
                'musicbrainz_albumid' => $musicbrainz_albumid,
                'form' => $form
        );
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
