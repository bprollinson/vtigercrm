<?php

require_once(dirname(__FILE__).'/../../../../include/ListView/ListViewSession.php');

class ListViewSessionMockLog {
    public function debug($message) {
    }
}

class ListViewSessionTest extends PHPUnit_Framework_TestCase {
    public function testConstructor() {
        $session = $this->getListViewSession();

        $this->assertSame('mymodule', $session->module);
        $this->assertSame('ASC', $session->sortby);
        $this->assertSame(1, $session->start);
        $this->assertSame(NULL, $session->viewname);
        $this->assertSame(NULL, $session->sortorder);
        $this->assertSame(NULL, $session->page_view);
    }

    public function testGetCurrentPageReturnsSessionValue() {
        $session = $this->getListViewSession();
        $_SESSION['lvs']['mymodule'][1]['start'] = 2;

        $this->assertSame(2, $session->getCurrentPage('mymodule', 1));
    }

    public function testGetCurrentPageReturnsDefaultValue() {
        $session = $this->getListViewSession();
        $_SESSION['lvs']['mymodule'][1] = [];

        $this->assertSame(1, $session->getCurrentPage('mymodule', 1));
    }

    public function testGetRequestStartPageReturnsCeilOfStartValue() {
        $session = $this->getListViewSession();
        $_REQUEST['start'] = 1.5;

        $this->assertSame(2.0, $session->getRequestStartPage());
    }

    public function testGetRequestStartPageReturnsOneForNonNumericValue() {
        $session = $this->getListViewSession();
        $_REQUEST['start'] = 'startpage';

        $this->assertSame(1.0, $session->getRequestStartPage());
    }

    public function testGetRequestStartPageReturnsOneForNumberLessThanOne() {
        $session = $this->getListViewSession();
        $_REQUEST['start'] = 0.5;

        $this->assertSame(1.0, $session->getRequestStartPage());
    }

    public function testSetSessionQueryCachesQuery() {
        $session = $this->getListViewSession();
        $_SESSION['mymodule_listquery'] = NULL;
        $session->setSessionQuery('mymodule', 'query', 1);

        $this->assertSame('query', $_SESSION['mymodule_listquery']);
    }

    public function setSessionQueryUnsetsDetailViewNavigation() {
        $session = $this->getListViewSession();
        $_SESSION['mymodule_DetailView_Navigation1'] = 'mynavigation';
        $session->setSessionQuery('mymodule', 'query', 1);

        $this->assertSame(false, isset($_SESSION['mymodule_DetailView_Navigation1']));
    }

    public function testHasViewChangedReturnsTrueForUnsetSessionViewName() {
        $session = $this->getListViewSession();
        $_SESSION['lvs']['mymodule'] = [];

        $this->assertSame(true, $session->hasViewChanged('mymodule'));
    }

    public function testHasViewChangedReturnsFalseForUnsetRequestViewName() {
        $session = $this->getListViewSession();
        $_SESSION['lvs']['mymodule']['viewname'] = 'view1';
        $_REQUEST['viewname'] = NULL;

        $this->assertSame(false, $session->hasViewChanged('mymodule'));
    }

    public function testHasViewChangedReturnsTrueForChangedViewName() {
        $session = $this->getListViewSession();
        $_SESSION['lvs']['mymodule']['viewname'] = 'view1';
        $_REQUEST['viewname'] = 'view2';

        $this->assertSame(true, $session->hasViewChanged('mymodule'));
    }

    public function testHasViewChangedReturnsFalseForUnchangedViewName() {
        $session = $this->getListViewSession();
        $_SESSION['lvs']['mymodule']['viewname'] = 'view1';
        $_REQUEST['viewname'] = 'view1';

        $this->assertSame(false, $session->hasViewChanged('mymodule'));
    }

    public function testSetCurrentView() {
        $_SESSION['lvs']['mymodule']['viewname'] = NULL;
        ListViewSession::setCurrentView('mymodule', 'view1');

        $this->assertSame('view1', $_SESSION['lvs']['mymodule']['viewname']);
    }

    public function testGetCurrentViewReturnsStoredValue() {
        $_SESSION['lvs']['mymodule']['viewname'] = 'view1';
        $this->assertSame('view1', ListViewSession::getCurrentView('mymodule'));
    }

    public function testGetCurrentViewReturnsDefaultValue() {
        $_SESSION['lvs']['mymodule'] = [];
        $this->assertSame(NULL, ListViewSession::getCurrentView('mymodule'));
    }

    private function getListViewSession() {
        global $log, $currentModule;
        $log = new ListViewSessionMockLog();
        $currentModule = 'mymodule';

        return new ListViewSession();
    }
}
