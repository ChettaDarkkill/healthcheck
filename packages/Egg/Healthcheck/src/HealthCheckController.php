<?php namespace Egg\Healthcheck;

use App\Http\Controllers\Controller;
use Config;
use Illuminate\Support\Facades\Redis;

class HealthcheckController extends Controller
{

    public function __construct()
    {

    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $checkListResult = array();
        $config = Config::get('healthcheck');
        if (count($config) > 0) {
            foreach ($config as $key => $config_list) {
                switch ($config_list['healthcheck_type']) {
                    case 'database':
                        $result = $this->checkDBMysqlMariadb($config_list['config']);
                        $result['type_get'] = $config_list['healthcheck_type'];
                        $checkListResult[$key] = $result;
                        break;
                    case 'memcache':
                        $result = $this->checkMemcache($config_list['config']);
                        $result['type_get'] = $config_list['healthcheck_type'];
                        $checkListResult[$key] = $result;
                        break;
                    case 'api':
                        $result = $this->checkApiCurl($config_list['config']);
                        $result['type_get'] = $config_list['healthcheck_type'];
                        $checkListResult[$key] = $result;
                        break;
                    case 'redis':
                        $result = $this->checkRedis($config_list['config']);
                        $result['type_get'] = $config_list['healthcheck_type'];
                        $checkListResult[$key] = $result;
                        break;
                    case 'soap':
                        $result = $this->checkSoap($config_list['config']);
                        $result['type_get'] = $config_list['healthcheck_type'];
                        $checkListResult[$key] = $result;
                        break;
                    default:
                }
            }
        }
        return view('healthcheck::index')->with('data', $checkListResult);
    }
    public function checkRedis($config)
    {
        $startTime = $this->slog_time();
        $checkResult['checkType'] = 'Check Redis';
        $checkResult['checkService'] = 'Connect Redis';
        $checkResult['type'] = 'Redis';
        $checkResult['url'] = Config::get('database.redis.' . $config['redis'] . '.host');
        try {
            $redis = Redis::connection();
            $ping = $redis->ping();
            if (empty($redis)) {
                $checkResult['setStatus'] = false;
                $checkResult['setMessage'] = 'fail';
                $checkResult['setTime'] = $this->elog_time($startTime);
            } else {
                $checkResult['setStatus'] = true;
                $checkResult['setMessage'] = 'Success';
                $checkResult['setTime'] = $this->elog_time($startTime);
            }

        } catch (\Exception $e) {
            $checkResult['setStatus'] = false;
            $checkResult['setMessage'] = $e->getMessage();
            $checkResult['setTime'] = $this->elog_time($startTime);
        }

        return $checkResult;
    }

    public function checkSoap($config)
    {
        $checkResult = [];
        $startTime = $this->slog_time();
        try {
            $checkResult['checkType'] = 'Check Soap';
            $checkResult['checkService'] = 'Connect Soap :' . $config['name'];
            $checkResult['type'] = 'SOAP';

            $checkResult['method'] = $config['method'];
            $soapClient = new \nusoap_client($config['wsdl'], true);
            $soapClient->soap_defencoding = 'UTF-8'; // TH8TISASCII
            $soapClient->decode_utf8 = false;
            $soapClient->response_timeout = 10;
            $soapClient->timeout = 10;
            $link = $soapClient->call($config['method'], $config['params']);
            $checkResult['url'] = $soapClient->endpoint . '[' . $config['method'] . ']';
            if (!$link) {
                $checkResult['setStatus'] = false;
                $checkResult['setMessage'] = 'Not connected : ';
                $checkResult['setTime'] = $this->elog_time($startTime);
            } else {
                $checkResult['setStatus'] = true;
                $checkResult['setMessage'] = 'Success';
                $checkResult['setTime'] = $this->elog_time($startTime);
            }
        } catch (Exception $e) {
            $checkResult['setStatus'] = false;
            $checkResult['setMessage'] = $e->getMessage();
            $checkResult['setTime'] = $this->elog_time($startTime);
        }

        return $checkResult;
    }

    public function checkDBMysql($config)
    {
        $checkResult = array();
        $startTime = $this->slog_time();
        try {
            $checkResult['checkType'] = 'Check Database Mysql';
            $checkResult['checkService'] = 'Connect MySQL';
            $checkResult['type'] = 'DATABASE';
            $checkResult['url'] = $config['hostname'] . '::[' . $config['username'] . ',' . $config['password'] . ']';
            $link = mysqli_connect($config['hostname'], $config['username'], $config['password'], $config['db']);
            if (!$link) {
                $checkResult['setStatus'] = false;
                $checkResult['setMessage'] = 'Not connected : ' . mysqli_error($link);
                $checkResult['setTime'] = $this->elog_time($startTime);
            } else {
                $checkResult['setStatus'] = true;
                $checkResult['setMessage'] = 'Success';
                $checkResult['setTime'] = $this->elog_time($startTime);
            }
        } catch (Exception $e) {
            $checkResult['setStatus'] = false;
            $checkResult['setMessage'] = $e->getMessage();
            $checkResult['setTime'] = $this->elog_time($startTime);
        }

        return $checkResult;
    }
    public function checkDBMysqlMariadb($config)
    {
        $checkResult = [];
        $startTime = $this->slog_time();
        try {
            $checkResult['checkType'] = 'Check Database DB';
            $checkResult['checkService'] = 'Connect DB';
            $checkResult['type'] = 'DATABASE';
            $checkResult['url'] = $config['hostname'] . '::[' . $config['username'] . ',' . $config['password'] . ']';

            $manage = \App::make('Illuminate\Database\DatabaseManager');
            /* @var Connection */
            $link = $manage->connection();
            if (!$link) {
                $checkResult['setStatus'] = false;
                $checkResult['setMessage'] = 'Not connected : ' . mysqli_error($link);
                $checkResult['setTime'] = $this->elog_time($startTime);
            } else {
                $checkResult['setStatus'] = true;
                $checkResult['setMessage'] = 'Success';
                $checkResult['setTime'] = $this->elog_time($startTime);
            }
        } catch (\Exception $e) {
            $checkResult['setStatus'] = false;
            $checkResult['setMessage'] = $e->getMessage();
            $checkResult['setTime'] = $this->elog_time($startTime);
        }

        return $checkResult;
    }

    public function checkMemcache($config)
    {
        $checkResult = array();
        $startTime = $this->slog_time();
        try {
            $checkResult['checkType'] = 'Check Connect Memcache';
            $checkResult['checkService'] = 'Connect Memcache';
            $checkResult['type'] = 'MEMCACHE';
            $checkResult['url'] = $config['host'] . '::' . $config['port'];
            $memcache = new \Memcache;
            if ($memcache->connect($config['host'], $config['port'])) {
                $checkResult['setStatus'] = true;
                $checkResult['setMessage'] = 'Success';
                $checkResult['setTime'] = $this->elog_time($startTime);
            } else {
                $checkResult['setStatus'] = false;
                $checkResult['setMessage'] = 'Not connected Memcache';
                $checkResult['setTime'] = $this->elog_time($startTime);
            }
        } catch (Exception $e) {
            $checkResult['setStatus'] = false;
            $checkResult['setMessage'] = $e->getMessage();
            $checkResult['setTime'] = $this->elog_time($startTime);
        }
        return $checkResult;
    }

    public function checkApiCurl($config)
    {
        $checkResult = array();
        $startTime = $this->slog_time();
        try {
            $checkResult['checkType'] = 'Check API';
            $checkResult['type'] = 'API';
            $checkResult['checkService'] = 'Connect API : ' . $config['name'];
            $checkResult['url'] = $config['url'];
            $info = $this->Curl($config['url'], $config['params'], $config['method']);

            if ($info['http_code'] == '200') {
                $checkResult['setStatus'] = true;
                $checkResult['setMessage'] = 'Success';
                $checkResult['setTime'] = $this->elog_time($startTime);
            } else {
                $checkResult['setStatus'] = false;
                $checkResult['setMessage'] = 'Could not connect api.';
                $checkResult['setTime'] = $this->elog_time($startTime);
            }
        } catch (Exception $e) {
            $checkResult['setStatus'] = false;
            $checkResult['setMessage'] = $e->getMessage();
            $checkResult['setTime'] = $this->elog_time($startTime);
        }
        return $checkResult;
    }

    protected function Curl($url, $params = array(), $service)
    {
        $ch = curl_init($url);
        $service = strtolower($service);
        if ($service == 'POST') {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        }

        if ($service == 'DELETE' or $service == 'PUT') {
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $service);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_exec($ch);
        $info = curl_getinfo($ch);
        return $info;
    }

    // Determine Start Time
    private function slog_time()
    {
        $mtime = microtime();
        $mtime = explode(" ", $mtime);
        $mtime = $mtime[1] + $mtime[0];
        $starttime = $mtime;

        // Return our time
        return $starttime;
    }

// Determine end time
    private function elog_time($starttime)
    {
        $mtime = microtime();
        $mtime = explode(" ", $mtime);
        $mtime = $mtime[1] + $mtime[0];
        $endtime = $mtime;
        $totaltime = ($endtime - $starttime);

        // Return our display
        return $totaltime;
    }

}
