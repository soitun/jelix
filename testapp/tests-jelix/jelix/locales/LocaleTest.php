<?php
/**
 * @package     jelix tests
 * @author      Laurent Jouanneau
 * @contributor Julien Issler, Dominique Papin
 * @copyright   2006-2025 Laurent Jouanneau
 * @copyright   2008 Julien Issler, 2008 Dominique Papin
 * @link        http://www.jelix.org
 * @licence     GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
 */

use \Jelix\Locale\Locale;

class LocaleTest extends \Jelix\UnitTests\UnitTestCase
{

    protected $filePath;

    public static function setUpBeforeClass(): void
    {
        self::initJelixConfig();
    }

    function setUp(): void
    {
        jApp::saveContext();
        jApp::pushCurrentModule('jelix_tests');
        $this->filePath = jApp::appPath() . 'modules/jelix_tests/locales/';
        parent::setUp();
    }

    function tearDown(): void
    {
        jApp::restoreContext();
        parent::tearDown();
    }

    function testSimpleLocale()
    {
        jApp::config()->locale = 'fr_FR';
        $this->assertEquals('ceci est une phrase fr_FR', Locale::get('tests1.first.locale'));
        $this->assertEquals('ceci est une phrase fr_FR avec tiret', Locale::get('tests1.first-dash-locale'));
        $this->assertEquals('ceci est une phrase fr_FR', Locale::get('tests1.first.locale', null, 'fr_FR'));
        $this->assertEquals('ceci est une phrase fr_FR', Locale::get('tests1.first.locale', null, 'de_DE'));
        $this->assertEquals('Chaîne à tester', Locale::get('tests1.multiline.locale.with.accent'));
        $this->assertEquals('Chaîne à tester à foison', Locale::get('tests1.multiline.locale.with.accent2'));
        $this->assertEquals('ceci est une phrase fr_CA', Locale::get('tests1.first.locale', null, 'fr_CA'));
        $this->assertEquals('this is an en_US sentence', Locale::get('tests1.first.locale', null, 'en_US'));
        $this->assertEquals('this is an en_EN sentence', Locale::get('tests1.first.locale', null, 'en_EN'));
    }

    /**
     *
     */
    function testException()
    {
        jApp::config()->fallbackLocale = '';
        jApp::config()->locale = 'de_DE';
        try {
            $loc = Locale::get('tests1.first.locale', null, 'de_DE');
            self::fail('no exception (found: "' . $loc . '")');
        } catch (jException $e) {
            self::fail('wrong exception type');
        } catch (Exception $e) {
            $this->assertEquals('(212)No locale file found for the given locale key "jelix_tests~tests1.first.locale" in any languages', $e->getMessage());
        }
        jApp::config()->fallbackLocale = 'en_US';
    }

    function testWithNoAskedLocale()
    {
        jApp::config()->fallbackLocale = '';
        // all this tests are made on an existing locale file
        jApp::config()->locale = 'fr_FR';
        $this->assertEquals('ceci est une phrase 2 fr_FR', Locale::get('tests1.second.locale'));
        // no test1.second.locale in fr_CA, so we should have the fr_FR one
        //$this->assertEqual('ceci est une phrase 2 fr_FR',Locale::get('tests1.second.locale', null, 'fr_CA'));

        // no test1.third.locale in fr_FR, so we should have the en_EN one
        jApp::config()->fallbackLocale = 'en_EN';
        $this->assertEquals('this is the 3th en_EN sentence', Locale::get('tests1.third.locale', null, 'fr_FR'));

        try {
            // it doesn't exist, even in the fallback locale
            Locale::get('tests1.fourth.locale', null, 'fr_FR');
            self::fail('no exception when trying to get tests1.fourth.locale locale');
        } catch (jException $e) {
            self::fail('Bad exception when trying to get tests1.fourth.locale locale');
        } catch (Exception $e) {
            $this->assertEquals('(213)The given locale key "jelix_tests~tests1.fourth.locale" does not exists in any default languages', $e->getMessage());
        }

        jApp::config()->fallbackLocale = '';

        try {
            // it doesn't exist
            Locale::get('tests1.fourth.locale', null, 'fr_FR');
            self::fail('no exception when trying to get tests1.fourth.locale locale');
        } catch (jException $e) {
            self::fail('Bad exception when trying to get tests1.fourth.locale locale');
        } catch (Exception $e) {
            $this->assertEquals('(213)The given locale key "jelix_tests~tests1.fourth.locale" does not exists in any default languages', $e->getMessage());
        }
    }

    function testWithNoAskedLocaleFile()
    {
        // all this tests are made on an non existing locale file
        jApp::config()->locale = 'fr_FR';
        $this->assertEquals('ceci est une phrase fr_FR test2', Locale::get('tests2.first.locale'));
        // no test2.properties file for fr_CA, so we should have the fr_FR one
        $this->assertEquals('ceci est une phrase fr_FR test2', Locale::get('tests2.first.locale', null, 'fr_CA'));
        // no test3.properties file for fr_CA and fr_FR, so we should have the en_EN one
        jApp::config()->fallbackLocale = 'en_EN';
        $this->assertEquals('this is an en_EN sentence test3', Locale::get('tests3.first.locale', null, 'fr_FR'));

        jApp::config()->fallbackLocale = '';
        try {
            // it doesn't exist
            Locale::get('jelix_tests~tests3.first.locale', null, 'fr_FR');
            self::fail('no exception when trying to get tests3.first.locale');
        } catch (jException $e) {
            self::fail('Bad exception when trying to get tests3.first.locale');
        } catch (Exception $e) {
            $this->assertEquals('(212)No locale file found for the given locale key "jelix_tests~tests3.first.locale" in any languages', $e->getMessage());
        }
    }

    function testLineBreak()
    {
        $this->assertEquals("This sentence has a line break\n after the word \"break\"", Locale::get('tests4.string.with.line.break', null, 'en_EN'));
    }

    function testLineBreakWithMultiLineString()
    {
        $this->assertEquals("This multiline sentence\n has two line breaks\n after the words \"sentence\" and \"breaks\"", Locale::get('tests4.multiline.string.with.line.break', null, 'en_EN'));
    }

    function testOverload()
    {
        jApp::config()->locale = 'fr_FR';
        $this->assertEquals('bonne valeur overload', Locale::get('jelix_tests~overload.test'));
    }

    function testNewOverload()
    {
        jApp::config()->locale = 'fr_FR';
        $this->assertEquals('bonne valeur', Locale::get('jelix_tests~newoverload.test'));
    }

    function testOutsideLocalesDir()
    {
        jApp::config()->locale = 'fr_FR';
        $this->assertEquals('Bonne valeur outside', Locale::get('jelix_tests~outside.test'));
    }

    function testGetBundle()
    {
        jApp::config()->locale = 'fr_FR';
        $expected = array(
            'first.locale' => 'ceci est une phrase fr_FR',
            'second.locale' => 'ceci est une phrase 2 fr_FR',
            'multiline.locale.with.accent' => 'Chaîne à tester',
            'multiline.locale.with.accent2' => 'Chaîne à tester à foison',
            'first-dash-locale' => 'ceci est une phrase fr_FR avec tiret',
        );
        $this->assertEquals($expected, Locale::getBundle('tests1.first.locale')->getAllKeys());
    }
}
