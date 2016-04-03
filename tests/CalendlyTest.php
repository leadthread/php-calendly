<?php

namespace Zenapply\Calendly\Tests;

use Zenapply\Calendly\Calendly;
use Zenapply\Request\HttpRequest;
use Zenapply\Calendly\Exceptions\CalendlyException;

class CalendlyTest extends TestCase
{
    protected $calendly;

    public function setUp(){
        $this->calendly = new Calendly("Token");
        parent::setUp();
    }

    public function tearDown(){
        unset($this->calendly);
        parent::tearDown();
    }

    public function testBuildPostFieldsReturnsCorrectString(){
        $result = $this->invokeMethod($this->calendly, 'buildPostFields', array(['url'=>'http://foo.com/bar']));
        $this->assertEquals($result,"url=http%3A%2F%2Ffoo.com%2Fbar");
    }

    public function testBuildPostFieldsWithArrayReturnsCorrectString(){
        $result = $this->invokeMethod($this->calendly, 'buildPostFields', array(['url'=>'http://foo.com/bar','events'=>['invitee.created']]));
        $this->assertEquals($result,"url=http%3A%2F%2Ffoo.com%2Fbar&events%5B%5D=invitee.created");
    }

    public function testBuildUrlReturnsCorrectString(){
        $result = $this->invokeMethod($this->calendly, 'buildUrl', array("foobar"));
        $this->assertEquals($result,"https://calendly.com/api/v1/foobar");
    }

    public function testHandleResponseThrowsException(){
        $this->setExpectedException(CalendlyException::class);
        $result = $this->invokeMethod($this->calendly, 'handleResponse', array('{foo: "bar"',400));
    }

    public function testHandleResponseReturnsArrayFromJsonString(){
        $result = $this->invokeMethod($this->calendly, 'handleResponse', array('{"foo": "bar"}',200));
        $this->assertInternalType('array',$result);
        $this->assertArrayHasKey("foo",$result);
    }

    protected function getCalendlyWithMockedHttpRequest($code,$data){
        $http = $this->getMock(HttpRequest::class);

        $http->expects($this->any())
             ->method('execute')
             ->will($this->returnValue($data));

        $http->expects($this->any())
             ->method('getInfo')
             ->will($this->returnValue($code));

        // create class under test using $http instead of a real CurlRequest
        return new Calendly("Token","v1","foo.com",$http);
    }

    public function testRegisterInviteeCreated(){
        $fixture = $this->getCalendlyWithMockedHttpRequest(200,'{"test":"value"}');
        $result = $fixture->registerInviteeCreated("bar.com");
        $this->assertInternalType('array',$result);
        $this->assertArrayHasKey("test",$result);
    }

    public function testRegisterInviteeCanceled(){
        $fixture = $this->getCalendlyWithMockedHttpRequest(200,'{"test":"value"}');
        $result = $fixture->registerInviteeCanceled("bar.com");
        $this->assertInternalType('array',$result);
        $this->assertArrayHasKey("test",$result);
    }

    public function testUnregister(){
        $fixture = $this->getCalendlyWithMockedHttpRequest(200,'NULL');
        $result = $fixture->unregister(1234);
        $this->assertInternalType('null',$result);
    }

}
