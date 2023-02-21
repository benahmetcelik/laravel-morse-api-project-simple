<?php

namespace App\Http\Controllers\API;

use App\Classes\MorseCodeTranslator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class MorseApiController extends Controller
{

    /**
     * Index function
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function index(Request $request)
    {

        $method = $request->method();
        if ($method != 'POST') {
            return 'Cannot ' . $method . ' /morse-api/';
        } else {

            if ($request->has('command')) {
                return self::command($request->post('command'));
            } else {
                return self::checksum($request->post('checksum'));
            }

        }
    }

    /**
     * Checksum function
     * @param $param
     * @return \Illuminate\Http\JsonResponse
     */
    public function checksum($param)
    {
        $checksum = Cache::get('checksum');
        $checksum = self::removeSpacesAndLines($checksum);
        $param = self::removeSpacesAndLines($param);
        if ($checksum == $param) {
            return response()->json(['checksum' => $param, 'data' => []]);
        } else {
            return response()->json(['error_msj' => self::str_to_morse('error. no key.')], 400);
        }
    }

    /**
     * Run Command Function
     * @param $param
     * @return \Illuminate\Http\JsonResponse
     */
    public function command($param)
    {
        switch ($param) {
            //CPU
            case '-.-. .--. ..-':
                return self::cpu();
                break;
            //ARCH
            case '.- .-. -.-. ....':
                return self::arch();
                break;
            //FREEMEM
            case '..-. .-. . . -- . --':
                return self::freemem();
                break;
            //HOSTNAME
            case '.... --- ... - -. .- -- .':
                return self::hostname();
                break;
            //PLATFORM
            case '.--. .-.. .- - ..-. --- .-. --':
                return self::platform();
                break;
            //TOTALMEM
            case '- --- - .- .-.. -- . --':
                return self::totalmem();
                break;
            //TYPE
            case '- -.-- .--. .':
                return self::type();
                break;
            //UPTIME
            case '..- .--. - .. -- .':
                return self::uptime();
                break;
            default:
                return response()->json(['error_msj' => self::str_to_morse('error. no command.')], 400);

                break;

        }
    }

    /**
     * Remove spaces and lines
     * @param $str
     * @return string|string[]
     */
    private function removeSpacesAndLines($str)
    {
        $str = str_replace(' ', '', $str);
        $str = str_replace("\n", '', $str);
        return $str;
    }

    /**
     * Result function
     * @param $str
     * @return string
     */
    private function result($data)
    {
        $data = self::str_to_morse(self::removeSpacesAndLines($data));
        $checksum = self::str_to_morse(self::generate_checksum($data));

        Cache::put('checksum', $checksum, 60);
        $result = [
            'checksum' => $checksum,
            'data' => [
                [
                    'model' => $data,
                    'speed' => self::str_to_morse(shell_exec('cat /proc/cpuinfo | grep "cpu MHz" | uniq'))
                    ,
                    'times' => [
                        'user' => self::str_to_morse(shell_exec('cat /proc/stat | grep "cpu " | awk \'{print $2}\'')),
                        'nice' => self::str_to_morse(shell_exec('cat /proc/stat | grep "cpu " | awk \'{print $3}\'')),
                        'sys' => self::str_to_morse(shell_exec('cat /proc/stat | grep "cpu " | awk \'{print $4}\'')),
                        'idle' => self::str_to_morse(shell_exec('cat /proc/stat | grep "cpu " | awk \'{print $5}\'')),
                        'irq' => self::str_to_morse(shell_exec('cat /proc/stat | grep "cpu " | awk \'{print $6}\'')),

                    ],
                ],

            ],

        ];

        return $result;

    }

    /**
     * CPU
     * @return string
     */
    public function cpu()
    {
        $cpu = shell_exec('cat /proc/cpuinfo | grep "model name" | uniq');
        $cpu = str_replace('model name	: ', '', $cpu);
        return self::result($cpu);
    }

    /**
     * Arch
     * @return string
     */
    public function arch()
    {
        $arch = shell_exec('uname -m');
        return self::result($arch);
    }

    /**
     * Free Memory
     * @return string
     */
    public function freemem()
    {
        $freemem = shell_exec('free -m | grep Mem | awk \'{print $4}\'');
        return self::result($freemem);

    }

    /**
     * Hostname
     * @return string
     */
    public function hostname()
    {
        $hostname = shell_exec('hostname');
        return self::result($hostname);
    }

    /**
     * Platform
     * @return string
     */
    public function platform()
    {
        $platform = shell_exec('uname -o');
        return self::result($platform);
    }

    /**
     * Total Memory
     * @return string
     *
     */
    public function totalmem()
    {
        $totalmem = shell_exec('free -m | grep Mem | awk \'{print $2}\'');
        return self::result($totalmem);
    }

    /**
     * Type
     * @return string
     *
     */
    public function type()
    {
        $type = shell_exec('uname -o');
        return self::result($type);
    }

    /**
     * Uptime
     * @return string
     */
    public function uptime()
    {
        $uptime = shell_exec('uptime -p');
        return self::result($uptime);
    }

    /**
     * Convert a string to integer bytes
     * @param string $string
     * @return array
     */
    public function str_to_bytes(string $string)
    {
        $bytes = [];
        foreach (str_split($string) as $char) {
            $bytes[] = ord($char);
        }
        return $bytes;
    }

    /**
     * Generate a checksum from a string
     * @param string $string
     * @return int
     */
    public function generate_checksum(string $string)
    {
        $bytes = self::str_to_bytes($string);
        $checksum = 0;
        foreach ($bytes as $byte) {
            $checksum += $byte;
        }
        return $checksum;
    }

    /**
     * Convert a string to morse code
     * @param string $string
     * @return string
     */
    private function str_to_morse(string $string)
    {
        $morse = new MorseCodeTranslator();

        return $morse->latinToMorse($string);

    }

}
