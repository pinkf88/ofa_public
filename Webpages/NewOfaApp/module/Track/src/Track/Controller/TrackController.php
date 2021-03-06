<?php

namespace Track\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Db\Sql\Select;
use Zend\Validator\Db\NoRecordExists;
use Track\Model\Track;
use Track\Form\TrackForm;
use Track\Form\TrackSelectForm;
use Zend\Session\Container;

class TrackController extends AbstractActionController
{
    protected $TrackTable;
    protected $session;

    public function __construct()
    {
        $this->session = new Container('ofa_track');
    }

    public function indexAction()
    {
        $album = '';
        $albumartist = '';
        $genre = '';
        $suchtext = '';
        $countperpage = 250;

        if ($this->session->offsetExists('albumartist'))
        {
            $albumartist = $this->session->offsetGet('albumartist');
        }

        if ($this->session->offsetExists('album'))
        {
            $album = $this->session->offsetGet('album');
        }

        if ($this->session->offsetExists('genre'))
        {
            $genre = $this->session->offsetGet('genre');
        }

        if ($this->session->offsetExists('suchtext'))
        {
            $suchtext = $this->session->offsetGet('suchtext');
        }

        if ($this->session->offsetExists('countperpage'))
        {
            $countperpage = intval($this->session->offsetGet('countperpage'));
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

                if ($this->getRequest()->getPost('album'))
                {
                    $album = $this->getRequest()->getPost('album');
                }
                else
                {
                    $album = '';
                }

                if ($this->getRequest()->getPost('genre'))
                {
                    $genre = $this->getRequest()->getPost('genre');
                }
                else
                {
                    $genre = '';
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
            }
        }

        $this->session->offsetSet('albumartist', $albumartist);
        $this->session->offsetSet('album', $album);
        $this->session->offsetSet('genre', $genre);
        $this->session->offsetSet('suchtext', $suchtext);
        $this->session->offsetSet('countperpage', $countperpage);

        $select = new Select();

        if (strlen($albumartist) > 0)
        {
            $select->where('albumartist="' . $albumartist . '"');
        }

        if (strlen($album) > 0)
        {
            $select->where('musicbrainz_albumid="' . $album . '"');
        }

        if (strlen($genre) > 0)
        {
            if ($genre == 2) {  // Live
                $select->where('studio=0');
            } else {
                $select->where('studio=1');
            }
        }

        if (strlen($suchtext) > 0)
        {
            $select->where('title LIKE "%' . $suchtext . '%"');
        }

        if ($this->params()->fromRoute('order_by') 
            && $this->params()->fromRoute('order_by') == 'title')
        {
            $select->order(array('title ASC', 'album ASC', 'discnumber ASC', 'track ASC'));
        }
        else
        {
            $select->order(array('albumartist ASC', 'originalyear ASC', 'year ASC', 'album ASC', 'discnumber ASC', 'track ASC'));
        }

        $paginator = $this->getTrackTable()->fetchAll($select);

        $paginator->setCurrentPageNumber((int) $this->params()->fromQuery('page', 1));
        $paginator->setItemCountPerPage($countperpage);
        $this->session->offsetSet('page', $this->params()->fromQuery('page', 1));

        $selectform = new TrackSelectForm($this->getTrackTable()->fetchAllArtists(), $this->getTrackTable()->fetchAllAlbums());

        if (strlen($albumartist) > 0) {
            $selectform->get('albumartist')->setValue($albumartist);
        }

        if (strlen($album) > 0) {
            $selectform->get('album')->setValue($album);
        }

        if (strlen($genre) > 0) {
            $selectform->get('genre')->setValue($genre);
        }

        if (strlen($suchtext) > 0) {
            $selectform->get('suchtext')->setValue($suchtext);
        }
        
        $selectform->get('countperpage')->setValue($countperpage);

        return new ViewModel(array(
                'paginator' => $paginator,
                'selectform' => $selectform
        ));
    }

    public function getTrackTable()
    {
        if (! $this->TrackTable)
        {
            $sm = $this->getServiceLocator();
            $this->TrackTable = $sm->get('Track\Model\TrackTable');
        }

        return $this->TrackTable;
    }
}
