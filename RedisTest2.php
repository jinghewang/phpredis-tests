<?php defined('PHPREDIS_TESTRUN') or die("Use TestRedis.php to run tests!\n");

require_once(dirname($_SERVER['PHP_SELF'])."/TestSuite.php");

class Redis_Test2 extends TestSuite
{
    const PORT = 6379;
    const AUTH = NULL; //replace with a string to use Redis authentication

    /* City lat/long */
    protected $cities = Array(
        'Chico'         => Array(-121.837478, 39.728494),
        'Sacramento'    => Array(-121.494400, 38.581572),
        'Gridley'       => Array(-121.693583, 39.363777),
        'Marysville'    => Array(-121.591355, 39.145725),
        'Cupertino'     => Array(-122.032182, 37.322998)
    );

    /**
     * @var Redis
     */
    public $redis;

    public function setUp() {
        $this->redis = $this->newInstance();
        $info = $this->redis->info();
        $this->version = (isset($info['redis_version'])?$info['redis_version']:'0.0.0');
    }

    protected function minVersionCheck($version) {
        return version_compare($this->version, $version, "ge");
    }

    protected function newInstance() {
        $r = new Redis();

        $r->connect($this->getHost(), self::PORT);

        if(self::AUTH) {
            $this->assertTrue($r->auth(self::AUTH));
        }
        return $r;
    }

    public function tearDown() {
        if($this->redis) {
            $this->redis->close();
        }
    }

    public function reset()
    {
        $this->setUp();
        $this->tearDown();
    }

    /* Helper function to determine if the clsas has pipeline support */
    protected function havePipeline() {
        $str_constant = get_class($this->redis) . '::PIPELINE';
        return defined($str_constant);
    }

    public function testMinimumVersion()
    {
        // Minimum server version required for tests
        $this->assertTrue(version_compare($this->version, "2.4.0", "ge"));
    }

    public function testPing()
    {
        $this->assertEquals('+PONG', $this->redis->ping());

        $count = 1000;
        while($count --) {
            $this->assertEquals('+PONG', $this->redis->ping());
        }
    }




}
?>
