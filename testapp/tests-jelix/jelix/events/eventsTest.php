<?php
/**
 * @package     testapp
 * @subpackage  jelix_tests module
 * @author      Laurent Jouanneau
 * @contributor
 * @copyright   2006-2023 Laurent Jouanneau
 * @link        http://www.jelix.org
 * @licence     GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
 */
use Jelix\Event\Event;
use Testapp\Tests\EventForTest;

class eventResponseToReturn
{

    static $responses = array();
}


class eventsTest extends \Jelix\UnitTests\UnitTestCase
{

    function setUp(): void
    {
        self::initJelixConfig();
        jFile::removeDir(jApp::tempPath(), false);

        $warmup = new \Jelix\Event\EventWarmup(jApp::app());
        $warmup->launch(jApp::getEnabledModulesPaths(), 0);
        jApp::reloadServices();
        parent::setUp();
    }

    function testBasics()
    {
        $response = Event::notify('TestEvent');
        $response = serialize($response->getResponse());
        $expected = serialize([
            array('module' => 'jelix_tests', 'ok' => true),
            array('module' => 'jelix_tests2', 'ok' => true)
        ]);
        $this->assertEquals($expected, $response, 'simple event');

        $temoin = array('hello' => 'world');
        $response = Event::notify('TestEventWithParams', $temoin);
        $response = $response->getResponse();
        $this->assertEquals('world', $response[0]['params'], 'event with parameters');
        $this->assertEquals('world', $response[1]['params2'], 'event with parameters');
    }

    function testResponseItem()
    {
        eventResponseToReturn::$responses = array(
            'testapp' => array('foo' => 'bar'),
            'jelix_tests' => array('foo' => '123'),
        );
        $response = Event::notify('TestEventResponse');
        $response = $response->getResponseByKey('foo');
        $this->assertNotNull($response);
        sort($response);
        $this->assertEquals(array('123', 'bar'), $response);
    }

    function testNoResponseItem()
    {
        eventResponseToReturn::$responses = array();
        $response = Event::notify('TestEventResponse');
        $response = $response->getResponseByKey('foo');
        $this->assertNull($response);
    }


    function testBoolItemAllTrue()
    {
        eventResponseToReturn::$responses = array(
            'testapp' => array('foo' => true),
            'jelix_tests' => array('foo' => true),
        );
        $response = Event::notify('TestEventResponse');
        $this->assertTrue($response->allResponsesByKeyAreTrue('foo'));
        $this->assertFalse($response->allResponsesByKeyAreFalse('foo'));
    }

    function testBoolItemNotAllTrue()
    {
        eventResponseToReturn::$responses = array(
            'testapp' => array('foo' => false),
            'jelix_tests' => array('foo' => true),
        );
        $response = Event::notify('TestEventResponse');
        $this->assertFalse($response->allResponsesByKeyAreTrue('foo'));
        $this->assertFalse($response->allResponsesByKeyAreFalse('foo'));
    }

    function testBoolItemAllFalse()
    {
        eventResponseToReturn::$responses = array(
            'testapp' => array('foo' => false),
            'jelix_tests' => array('foo' => false),
        );
        $response = Event::notify('TestEventResponse');
        $this->assertFalse($response->allResponsesByKeyAreTrue('foo'));
        $this->assertTrue($response->allResponsesByKeyAreFalse('foo'));
    }

    function testBoolItemNoValues()
    {
        eventResponseToReturn::$responses = array();
        $response = Event::notify('TestEventResponse');
        $this->assertNull($response->allResponsesByKeyAreTrue('foo'));
        $this->assertNull($response->allResponsesByKeyAreFalse('foo'));
    }

    function testDisabledListener()
    {
        jApp::config()->disabledListeners['TestEvent'] = array('\JelixTests\Tests\Listener\TestEventsListener');

        $response = Event::notify('TestEvent');
        $response = serialize($response->getResponse());
        $expected = serialize([
            array('module' => 'jelix_tests2', 'ok' => true)
        ]);

        $this->assertEquals($expected, $response);
    }

    function testSingleDisabledListener()
    {
        jApp::config()->disabledListeners['TestEvent'] = '\JelixTests\Tests\Listener\TestEventsListener';

        $response = Event::notify('TestEvent');
        $response = $response->getResponse();
        $expected = [
            array('module' => 'jelix_tests2', 'ok' => true)
        ];

        $this->assertEquals($expected, $response);
    }

    function testEventObject()
    {
        $event = new EventForTest();
        Event::notify($event);
        $this->assertEquals('onTestEventObject called', $event->getDummyValue());
        $this->assertEquals('TestAttrEventsListener called', $event->getDummy2Value());
    }

    function testEventHavingNoListener()
    {
        $response = Event::notify('eventhavingnolistener');
        $this->assertEquals(array(), $response->getResponse());
    }

}
