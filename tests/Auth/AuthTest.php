<?php
namespace EricHansen\HydraConfigure\Tests;

use EricHansen\HydraConfigure\HydraConfigure as Hydra;

class AuthTest extends \PHPUnit_Framework_TestCase {
    public function setUp(){
        $this->elt = new Hydra("", "", dirname(dirname(dirname(__FILE__))) . "/src/EricHansen/HydraConfigure/config.json");
    }

    public function testCanAuthenticate(){
        $this->assertNotNull($this->elt->getToken(), "Unable to authenticate.");
    }
}
 