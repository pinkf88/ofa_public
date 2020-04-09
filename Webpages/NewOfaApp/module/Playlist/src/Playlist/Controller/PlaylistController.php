<?php
namespace Playlist\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class PlaylistController extends AbstractActionController
{
    public function indexAction()
    {
        return new ViewModel(array());
    }
}
