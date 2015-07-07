<?php

require_once(dirname(__FILE__).'/../../../../include/ListView/RelatedListViewSession.php');

class RelatedListViewSessionMockLog {
    public function debug($message) {
    }
}

class RelatedListViewSessionTest extends PHPUnit_Framework_TestCase {
    public function testConstructor() {
        global $log, $currentModule;
        $log = new RelatedListViewSessionMockLog();
        $currentModule = 'mymodule';

        $session = new RelatedListViewSession();
        $this->assertSame('mymodule', $session->module);
        $this->assertSame(1, $session->start);
        $this->assertSame(NULL, $session->sorder);
        $this->assertSame(NULL, $session->sorrtby);
        $this->assertSame(NULL, $session->page_view);
    }

    public function testAddRelatedModuleToSessionStoresHeader() {
        global $currentModule;
        $currentModule = 'mymodule';

        RelatedListViewSession::addRelatedModuleToSession(1, 'myheader');
        $this->assertSame('myheader', $_SESSION['relatedlist']['mymodule'][1]);
    }

    public function testAddRelatedModuleToSessionStoresStartPage() {
        global $currentModule;
        $currentModule = 'mymodule';
        $_REQUEST['start'] = 2;

        RelatedListViewSession::addRelatedModuleToSession(1, 'myheader');
        $this->assertSame(2.0, $_SESSION['rlvs']['mymodule'][1]['start']);
    }

    public function testRemoveRelatedModuleFromSession() {
        global $currentModule;
        $currentModule = 'mymodule';

        $_SESSION['relatedlist']['mymodule'][1] = 'myheader';
        RelatedListViewSession::removeRelatedModuleFromSession(1, 'bogus');
        $this->assertSame(false, isset($_SESSION['relatedlist']['mymodule'][1]));
    }

    public function testSaveRelatedModuleStartPage() {
        global $currentModule;
        $currentModule = 'mymodule';

        $_SESSION['rlvs']['mymodule'][1]['start'] = NULL;
        RelatedListViewSession::saveRelatedModuleStartPage(1, 2);
        $this->assertSame(2, $_SESSION['rlvs']['mymodule'][1]['start']);
    }

    public function testGetCurrentPageReturnsStoredValue() {
        global $currentModule;
        $currentModule = 'mymodule';

        $_SESSION['rlvs']['mymodule'][1]['start'] = 2;
        $this->assertSame(2, RelatedListViewSession::getCurrentPage(1));
    }

    public function testGetCurrentPageReturnsDefaultValue() {
        global $currentModule;
        $currentModule = 'mymodule';

        $_SESSION['rlvs']['mymodule'][1] = [];
        $this->assertSame(1, RelatedListViewSession::getCurrentPage(1));
    }

    public function testGetRequestStartPageReturnsCeilOfStartValue() {
        $_REQUEST['start'] = 1.5;

        $this->assertSame(2.0, RelatedListViewSession::getRequestStartPage());
    }

    public function testGetRequestStartPageReturnsOneForNonNumericValue() {
        $_REQUEST['start'] = 'bogus';

        $this->assertSame(1.0, RelatedListViewSession::getRequestStartPage());
    }

    public function testGetRequestStartPageReturnsOneForNumberLessThanOne() {
        $_REQUEST['start'] = 0.5;

        $this->assertSame(1.0, RelatedListViewSession::getRequestStartPage());
    }

    public function testGetRequestCurrentPageReturnsCeilOfStartValue() {
        $_REQUEST['start'] = 1.5;

        $this->assertSame(2.0, RelatedListViewSession::getRequestCurrentPage(1, ''));
    }

    public function testGetRequestCurrentPageReturnsOneForNonNumericValue() {
        $_REQUEST['start'] = 'bogus';

        $this->assertSame(1.0, RelatedListViewSession::getRequestCurrentPage(1, ''));
    }

    public function testGetRequestCurrentPageReturnsOneForNumberLessThanOne() {
        $_REQUEST['start'] = 0.5;

        $this->assertSame(1.0, RelatedListViewSession::getRequestCurrentPage(1, ''));
    }

    public function testGetRequestCurrentPageReturnsStartPage() {
        global $currentModule;
        $currentModule = 'mymodule';
        $_REQUEST['start'] = NULL;
        $_SESSION['rlvs']['mymodule'][1]['start'] = 2.0;

        $this->assertSame(2.0, RelatedListViewSession::getRequestCurrentPage(1, ''));
    }
}
