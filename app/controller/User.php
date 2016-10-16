<?php
namespace Acd\Controller;

use \Acd\Model\UserLoader;
use \Acd\Model\Query;
use \Acd\Model\UserDo;
use \Acd\Model\SessionNavigation;
use \Acd\View\HeaderMenu;

// Output
class User
{
    const VIEW_LIST = 'list'; // List of all users
    const VIEW_DETAIL = 'edit'; // Detail (values) of a user
    const VIEW_DETAIL_NEW = 'new'; // New user
    private $id;
    private $view;
    private $sessionNavigation;
    private $requestUrl;

    public function __construct()
    {
        $this->initializeSessionNavigation();
    }
    /* Setters and getters attributes */
    public function setId($id)
    {
        $this->id = (string)$id;
    }
    public function getId()
    {
        return $this->id;
    }
    public function setView($view)
    {
        $this->view = (string)$view;
    }
    public function getView()
    {
        return $this->view;
    }
    // back button
    private function initializeSessionNavigation()
    {
        $this->sessionNavigation = new SessionNavigation();
        $this->sessionNavigation->load();
    }
    public function setRequestUrl($url)
    {
        $this->requestUrl = $url;
    }
    private function setContent($content)
    {
        $this->content = $content;
    }
	public function getContent() {
		return $this->content;
	}
    public function load()
    {
        $userLoader = new UserLoader();
        $query = new Query();
        if ($this->getId()) {
            $query->setType('id');
            $query->setCondition(['id' => $this->getId()]);
        } else {
            if (!$this->getView()) {
                $query->setType('all');
                $this->setView($this::VIEW_LIST);
            }
        }
        $this->setContent($userLoader->load($query));
    }
    public function getTitle()
    {
        switch ($this->getView()) {
            case $this::VIEW_LIST:
                return 'Users';
                break;
            case $this::VIEW_DETAIL:
                return 'User '.$this->getContent()->getId();
                break;
            case $this::VIEW_DETAIL_NEW:
                return 'New user';
                break;
        }
    }
    public function getHeaderMenuOu()
    {
        $headerMenuOu = new HeaderMenu();
        $headerMenuOu->setBack(!$this->sessionNavigation->isEmpty());
        return $headerMenuOu;
    }
    public function render()
    {
        switch ($this->getView()) {
            case $this::VIEW_LIST:
                $ou = new \ACD\View\UserList();
                $ou->setUserList($this->getContent());
                break;
            case $this::VIEW_DETAIL:
                if ($this->getContent()->getId()) {
                    $ou = new \ACD\View\UserDetail();
                    $ou->setUserElement($this->getContent());
                } else {
                    throw new \Exception('User collection not found', 404);
                }
                break;
            case $this::VIEW_DETAIL_NEW:
                $ou = new \ACD\View\UserDetail();
                $emptyCollection = new UserDo();
                $ou->setUserElement($emptyCollection);
                break;
            default:
                throw new \Exception("View (".$this->getView().") not defined", 1);
                break;
        }

        $this->sessionNavigation->push([
            'hash' => 'user - '.$this->getView().' - '.$this->getId(), // Page hash, consecutive same hash no add navigation
            'url' => $this->requestUrl,
            'title' => $this->getTitle()
        ]);
        $this->sessionNavigation->save();

        return $ou->render();
    }
}
