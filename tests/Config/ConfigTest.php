<?php
namespace EricHansen\HydraConfigure\Tests;

use EricHansen\HydraConfigure\HydraConfigure as Hydra;

class ConfigTest extends \PHPUnit_Framework_TestCase {
    public function setUp(){
        $this->elt = new Hydra("", "", dirname(dirname(dirname(__FILE__))) . "/src/EricHansen/HydraConfigure/config.json");
    }

    public function testCanGetAllConfig(){
        $config = $this->elt->get("config");

        $this->assertLessThan(400, $config->http_code, "Error fetching all configs (HTTP code: ". $config->http_code .")");
    }

    public function testCantGetConfig(){
        $config = $this->elt->get("config", time());

        $this->assertGreaterThan(399, $config->http_code, "Config option found!");
    }

    public function testGet404Error(){
        $config = $this->elt->get("config", time() * time());

        $this->assertEquals(404, $config->http_code, "Non-404 error detected (retrieved ". $config->http_code . ")");
    }
}
 
