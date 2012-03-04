<?php
namespace OOSSH\SSH2;

/**
 * Test class for Connection.
 * Generated by PHPUnit on 2011-09-22 at 19:40:41.
 */
class ConnectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Connection
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new Connection(TEST_HOST);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    protected function connectAndAuth()
    {
        $this->object->connect();
        $this->object->authenticate(new \OOSSH\SSH2\Authentication\Password(TEST_USER, TEST_PASSWORD));
    }

    public function testConnect()
    {
        $this->object->connect();
        $this->assertTrue($this->object->isConnected());
    }

    public function testCheck()
    {
        $this->object->connect();
        $this->object->check(TEST_FINGERPRINT);
    }

    /**
     * @expectedException \OOSSH\Exception\BadFingerprint
     */
    public function testCheckFail()
    {
        $this->connectAndAuth();
        $this->object->check('0');
    }

    /**
     * @dataProvider providerAuthenticate
     * @depends testConnect
     */
    public function testAuthenticate($provider)
    {
        $this->object->connect();
        $this->object->authenticate($provider);
        $this->assertTrue($this->object->isAuthenticated());
    }

    public function testExec()
    {
        $that = $this;
        $this->connectAndAuth();
        $this->object->exec('uname -a', function($stdio) use($that) { $that->assertInternalType('string', $stdio); });
    }

    public function testBlock()
    {
        $that = $this;
        $this->connectAndAuth();
        $this->object
            ->begin()
                ->exec('pwd')
                ->exec('exit')
            ->end(function($stdio) use($that) { $that->assertInternalType('string', $stdio); })
        ;
    }

    public function providerAuthenticate()
    {
        return array(
            array(new \OOSSH\SSH2\Authentication\Password(TEST_USER, TEST_PASSWORD)),
            array(new \OOSSH\SSH2\Authentication\PublicKey(TEST_USER, TEST_PUBKEY_FILE, TEST_PRIVKEY_FILE)),
        );
    }
}
