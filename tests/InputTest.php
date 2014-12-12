<?php namespace Frozennode\XssInput;
use Mockery\Mock;
/**
 * Class InputTest
 *
 * Sample inputs from https://www.owasp.org/index.php/XSS_Filter_Evasion_Cheat_Sheet
 */

class InputTest extends \PHPUnit_Framework_TestCase
{
    public function testInputGet()
    {

    }

    public function testNoXss()
    {
        $xss = 'nothing bad in here';
        $mock = \Mockery::mock('Input');
        $mock->shouldReceive('input')->andReturn($xss);
        XssInput::setFacadeApplication(array('request'=>$mock));
        // Our simple mock here returns the $xss no matter which key is requested
        $this->assertFalse(XssInput::hasXss('ignored'));
    }


    public function testXssLocator()
    {
        $xss = '\';alert(String.fromCharCode(88,83,83))//\';alert(String.fromCharCode(88,83,83))//";
alert(String.fromCharCode(88,83,83))//";alert(String.fromCharCode(88,83,83))//--
></SCRIPT>">\'><SCRIPT>alert(String.fromCharCode(88,83,83))</SCRIPT>';
        $mock = \Mockery::mock('Input');
        $mock->shouldReceive('input')->andReturn($xss);
        XssInput::setFacadeApplication(array('request'=>$mock));
        $this->assertTrue(XssInput::hasXss('ignored'));
    }

    public function testXssLocator2()
    {
        $xss = '\'\';!--"<XSS>=&{()}';
        $mock = \Mockery::mock('Input');
        $mock->shouldReceive('input')->andReturn($xss);
        XssInput::setFacadeApplication(array('request'=>$mock));
        $this->assertTrue(XssInput::hasXss('ignored'));
    }

    public function testXssNoFilterEvasion()
    {
        $xss = '<SCRIPT SRC=http://ha.ckers.org/xss.js></SCRIPT>';
        $mock = \Mockery::mock('Input');
        $mock->shouldReceive('input')->andReturn($xss);
        XssInput::setFacadeApplication(array('request'=>$mock));
        $this->assertTrue(XssInput::hasXss('ignored'));
    }


    public function testXssImageUsingJavascriptDirective()
    {
        $xss = '<IMG SRC="javascript:alert(\'XSS\');">';
        $mock = \Mockery::mock('Input');
        $mock->shouldReceive('input')->andReturn($xss);
        XssInput::setFacadeApplication(array('request'=>$mock));
        $this->assertTrue(XssInput::hasXss('ignored'));
    }

    public function testXssImageNoQuotesNoSemicolon()
    {
        $xss = '<IMG SRC=javascript:alert(\'XSS\')>';
        $mock = \Mockery::mock('Input');
        $mock->shouldReceive('input')->andReturn($xss);
        XssInput::setFacadeApplication(array('request'=>$mock));
        $this->assertTrue(XssInput::hasXss('ignored'));
    }

    public function testXssImageCaseInsensitive()
    {
        $xss = '<IMG SRC=JaVaScRiPt:alert(\'XSS\')>';
        $mock = \Mockery::mock('Input');
        $mock->shouldReceive('input')->andReturn($xss);
        XssInput::setFacadeApplication(array('request'=>$mock));
        $this->assertTrue(XssInput::hasXss('ignored'));
    }

    public function testXssHtmlEntities()
    {
        $xss = '<IMG SRC=javascript:alert("XSS")>';
        $mock = \Mockery::mock('Input');
        $mock->shouldReceive('input')->andReturn($xss);
        XssInput::setFacadeApplication(array('request'=>$mock));
        $this->assertTrue(XssInput::hasXss('ignored'));
    }

    public function testXssGraveAccentObfuscation()
    {
        $xss = '<IMG SRC=`javascript:alert("RSnake says, \'XSS\'")`>';
        $mock = \Mockery::mock('Input');
        $mock->shouldReceive('input')->andReturn($xss);
        XssInput::setFacadeApplication(array('request'=>$mock));
        $this->assertTrue(XssInput::hasXss('ignored'));
    }

    public function testXssMalformedAnchorTags()
    {
        $xss = '<a onmouseover="alert(document.cookie)">xxs link</a>';
        $mock = \Mockery::mock('Input');
        $mock->shouldReceive('input')->andReturn($xss);
        XssInput::setFacadeApplication(array('request'=>$mock));
        $this->assertTrue(XssInput::hasXss('ignored'));

        $xss = '<a onmouseover=alert(document.cookie)>xxs link</a>';
        $mock = \Mockery::mock('Input');
        $mock->shouldReceive('input')->andReturn($xss);
        XssInput::setFacadeApplication(array('request'=>$mock));
        $this->assertTrue(XssInput::hasXss('ignored'));

    }

    public function testXssMalformedImageTags()
    {
        $xss = '<IMG """><SCRIPT>alert("XSS")</SCRIPT>">';
        $mock = \Mockery::mock('Input');
        $mock->shouldReceive('input')->andReturn($xss);
        XssInput::setFacadeApplication(array('request'=>$mock));
        $this->assertTrue(XssInput::hasXss('ignored'));
    }

    // TODO: etc...

    public function testAllNoXss()
    {
        $xss = array(
            'key1' => 'nothing bad in here',
            'key2' => 'harmless'
        );
        $mock = \Mockery::mock('Input');
        $mock->shouldReceive('all')->andReturn($xss);
        XssInput::setFacadeApplication(array('request'=>$mock));
        // Our simple mock here returns the $xss no matter which key is requested
        $this->assertFalse(XssInput::hasXss());
    }

    public function testAllXss()
    {
        $xss = array(
            'key1' => '\';alert(String.fromCharCode(88,83,83))//\';alert(String.fromCharCode(88,83,83))//";
alert(String.fromCharCode(88,83,83))//";alert(String.fromCharCode(88,83,83))//--
></SCRIPT>">\'><SCRIPT>alert(String.fromCharCode(88,83,83))</SCRIPT>',
            'key2' => 'harmless'
        );
        $mock = \Mockery::mock('Input');
        $mock->shouldReceive('all')->andReturn($xss);
        XssInput::setFacadeApplication(array('request'=>$mock));
        // Our simple mock here returns the $xss no matter which key is requested
        $this->assertTrue(XssInput::hasXss());
    }
}