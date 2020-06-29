<?php

class VisitorTracking
{
    /** @var null|\stdClass $browser */
    public $browser         = null;
    /** @var \Closure|null  */
    private $error_handler  = null;

    /**
     * Stalk constructor.
     *
     * @param \Closure|null $error_handler
     * @param string|null   $ip
     */
    public function __construct(Closure $error_handler = null, string $ip = null)
    {
        $this->ip = $ip == null ? $this->getIp() : $ip;
        $this->error_handler = $error_handler;
        $this->browser = (object) $this->getBrowser();
    }

    /**
     * Gets clients IP address.
     *
     * @return string
     */
    private function getIp(): string
    {
        $client  = @$_SERVER['HTTP_CLIENT_IP'];
        $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
        $remote  = $_SERVER['REMOTE_ADDR'];

        if (filter_var($client, FILTER_VALIDATE_IP)) {
            $ip = $client;
        }
        elseif (filter_var($forward, FILTER_VALIDATE_IP)) {
            $ip = $forward;
        }
        else {
            $ip = $remote;
        }

        return $ip;
    }

    /**
     * Gets clients browser's information.
     *
     * @return array
     */
    private function getBrowser(): array
    {
        $u_agent      = $_SERVER['HTTP_USER_AGENT'];
        $platform     = 'Unknown';
        $browser_name = 'Unknown';
        $version      = null;

        if (preg_match('/linux/i', $u_agent)) {
            $platform = 'linux';
        }
        elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
            $platform = 'mac';
        }
        elseif (preg_match('/windows|win32/i', $u_agent)) {
            $platform = 'windows';
        }
        if (preg_match('/MSIE/i', $u_agent) && ! preg_match('/Opera/i', $u_agent)) {
            $browser_name = 'Internet Explorer';
            $ub = "MSIE";
        }
        elseif (preg_match('/Firefox/i', $u_agent)) {
            $browser_name = 'Mozilla Firefox';
            $ub = "Firefox";
        }
        elseif (preg_match('/Chrome/i', $u_agent)) {
            $browser_name = 'Google Chrome';
            $ub = "Chrome";
        }
        elseif (preg_match('/Safari/i', $u_agent)) {
            $browser_name = 'Apple Safari';
            $ub = "Safari";
        }
        elseif (preg_match('/Opera/i', $u_agent)) {
            $browser_name = 'Opera';
            $ub = "Opera";
        }
        elseif (preg_match('/Netscape/i', $u_agent)) {
            $browser_name = 'Netscape';
            $ub = "Netscape";
        }

        $known   = ['Version', $ub, 'other'];
        $join    = implode('|', $known);
        $pattern = '#(?<browser>' . $join . ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';

        preg_match_all($pattern, $u_agent, $matches);
        $i = count($matches['browser']);

        if ($i != 1) {
            if (strripos($u_agent, 'Version') < strripos($u_agent, $ub)) {
                $version = $matches['version'][0];
            }
            else {
                $version = $matches['version'][1];
            }
        }
        else {
            $version = $matches['version'][0];
        }

        if (! $version) {
            $version = '?';
        }

        return ['name' => $browser_name, 'version' => $version, 'OS' => $platform];
    }

    /**
     * Key value pair of all stack attribute.
     *
     * @return array
     */
    public function __toArray(): array
    {
        $properties = get_object_vars($this);
        $properties['browser'] = (array) $properties['browser'];

        return $properties;
    }
}
