<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class RoleAPITest extends TestCase
{
    use DatabaseTransactions;
    protected $user;
    public function setUp(){
        parent::setUp();
        $this->user = Sentinel::findByCredentials($credentials = [
            'login' => "admin@mt2.com",
        ]);
        Sentinel::login($this->user);
    }
    /**
     * A basic test example.
     *
     * @return void
     */

    public function testRoleList()
    {
        $data = $this->call('GET', '/api/role');
        $result = json_decode($data->getContent());
        //Idealy we will have keys eventually.
        $this->assertEquals("1",$result[0][0]);
        $this->assertEquals("gtdev",$result[0][1]);
        $this->assertEquals("Global Tech Devs",$result[0][2]);


     }

    public function testNewRole()
    {
        $this->json('POST', '/api/role', ['name' => 'TestRole', 'permissions' => ['login']])
            ->seeJsonEquals([
                'success' => true,
            ]);
    }

    public function testRoleRequiredFields(){
        $this->json('POST', '/api/role')
            ->seeJson([
                'name' => array('The name field is required.'),
                'permissions' => array('The permissions field is required.'),

            ]);
    }

    public function testRoleUpdate(){
        $this->json('PATCH', '/api/role/1',['name'=> "admin2", "slug" => "admin2", "permissions" => ['login']])
            ->seeJsonEquals([
                'success' => true,
            ]);
    }
    public function testRoleUpdateRequiredFields(){
        $this->json('PATCH', '/api/role/1')
            ->seeJsonEquals([
                'name' => array("The name field is required."),
                "permissions" => array("The permissions field is required."),
                "slug" => array("The slug field is required."),
            ]);

    }

    public function testAccess(){
        Sentinel::logout($this->user);
       $this->json("GET", "/api/role/")->assertResponseStatus("401");
       $this->json("POST", "/api/role/")->assertResponseStatus("401");
       $this->json("PATCH", "/api/role/1")->assertResponseStatus("401");

    }
}
