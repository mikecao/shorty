<?php
/**
 * Shorty: A simple url shortener.
 *
 * @copyright Copyright (c) 2011, Mike Cao <mike@mikecao.com>
 * @license   MIT, http://www.opensource.org/licenses/mit-license.php
 */
class Shorty {
    /**
     * Default characters to use for shortening.
     *
     * @var string
     */
    private $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

    /**
     * Hostname
     */
    private $hostname = '';

    /**
     * PDO database connection.
     *
     * @var object
     */
    private $connection = null;

    /**
     * Whitelist of IPs allowed to save URLs.
     * If the list is empty, then any IP is allowed.
     *
     * @var array
     */
    private $whitelist = array();

    /**
     * Constructor
     *
     * @param string $hostname Hostname
     * @param object $connection Database connection
     */
    public function __construct($hostname, $connection, $chars = null) {
        $this->hostname = $hostname;
        $this->connection = $connection;
    }

    /**
     * Sets the character set for encoding.
     *
     * @param string $chars Set of characters
     */
    public function set_chars($chars) {
        if (!empty($chars)) {
            $this->chars = $chars;
        }
    }

    /**
     * Converts a number to an alpha-numeric string.
     *
     * @param int $num Number to convert
     * @param string $s String of characters for conversion
     * @return string Alpha-numeric string
     */
    public static function num_to_alpha($n, $s) {
        // This adds more randomness for consecutive numbers.
        $n = '1'.strrev($n + 12345);

        $b = strlen($s);
        $m = $n % $b;

        if ($n - $m == 0) return substr($s, $n, 1);

        $a = '';

        while ($m > 0 || $n > 0) {
            $a = substr($s, $m, 1).$a;
            $n = ($n - $m) / $b;
            $m = $n % $b;
        }

        return $a;
    }

    /**
     * Converts an alpha numberic string to a number.
     *
     * @param string $a Alpha-numeric string to convert
     * @param string $s String of characters for conversion
     * @return int Converted number
     */
    public static function alpha_to_num($a, $s) {
        $b = strlen($s);
        $l = strlen($a);

        for ($n = 0, $i = 0; $i < $l; $i++) {
            $n += strpos($s, substr($a, $i, 1)) * pow($b, $l - $i - 1);
        }

        return ((int)strrev(substr($n,1))) - 12345;
    }

    /**
     * Looks up a url in the database by id.
     *
     * @param string $id URL id
     * @return array URL record
     */
    public function fetch($id) {
        $statement = $this->connection->prepare(
            'SELECT * FROM urls WHERE id = ?'
        );
        $statement->execute(array($id));

        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Attempts to locate a URL in the database.
     *
     * @param string $url URL
     * @return array URL record
     */
    public function find($url) {
        $statement = $this->connection->prepare(
            'SELECT * FROM urls WHERE url = ?'
        );
        $statement->execute(array($url));

        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Stores a url in the database.
     *
     * @param string $url URL to store
     * @return int Insert id
     */
    public function store($url) {
        $datetime = date('Y-m-d H:i:s');

        $statement = $this->connection->prepare(
            'INSERT INTO urls (url, created) VALUES (?,?)'
        );
        $statement->execute(array($url, $datetime));

        return $this->connection->lastInsertId();
    }

    /**
     * Updates statistics for a URL.
     *
     * @param int $id URL id
     */
    public function update($id) {
        $datetime = date('Y-m-d H:i:s');

        $statement = $this->connection->prepare(
            'UPDATE urls SET hits = hits + 1, accessed = ? WHERE id = ?'
        );
        $statement->execute(array($datetime, $id));
    }

    /**
     * Sends a redirect to a URL.
     *
     * @param string $url URL
     */
    public function redirect($url) {
        header("Location: $url", true, 301);
        exit();
    }

    /**
     * Sends a 404 response.
     */
    public function not_found() {
        header('Status: 404 Not Found');
        exit(
            '<h1>404 Not Found</h1>'.
            '<h3>The page you have requested could not be found.</h3>'.
            str_repeat(' ', 512)
        );
    }

    /**
     * Sends an error message.
     *
     * @param string $message Error message
     */
    public function error($message) {
        exit("<h1>$message</h1>");
    }

    /**
     * Adds an IP to allow saving URLs.
     */
    public function allow($ip) {
        if (is_array($ip)) {
            $this->whitelist = array_merge($this->whitelist, $ip);
        }
        else {
            array_push($this->whitelist, $ip);
        }
    }

    /**
     * Starts the program.
     *
     * @param string $connection Connection string
     */
    public function run() {
        $q = str_replace('/', '', $_GET['q']);
        $url = urldecode($_GET['url']);
        $format = strtolower($_GET['format']);

        // If adding a new url
        if (!empty($url)) {
            if (!empty($this->whitelist) && !in_array($_SERVER['REMOTE_ADDR'], $this->whitelist)) {
                $this->error('Not allowed.');
            }

            if (preg_match('/^http[s]?\:\/\//', $url)) {
                $result = $this->find($url);

                // Not found, so save it
                if (empty($result)) {

                    $id = $this->store($url);

                    $url = $this->hostname.'/'.self::num_to_alpha($id, $this->chars);
                }
                else {
                    $url = $this->hostname.'/'.self::num_to_alpha($result['id'], $this->chars);
                }

                // Display the shortened url
                switch ($format) {
                    case 'text':
                        exit($url);

                    case 'json':
                        header('Content-Type: application/json');
                        exit(json_encode(array('url' => $url)));

                    case 'xml':
                        header('Content-Type: application/xml');
                        exit(implode("\n", array(
                            '<?xml version="1.0"?'.'>',
                            '<response>',
                            '  <url>'.htmlentities($url).'</url>',
                            '</response>'
                        )));

                    default:
                        exit('<a href="'.$url.'">'.$url.'</a>');
                }
            }
            else {
                $this->error('Bad input.');
            }
        }
        // Lookup by id
        else {
            if (preg_match('/^([a-zA-Z0-9]+)$/', $q, $matches)) {
                $id = self::alpha_to_num($matches[1], $this->chars);

                $result = $this->fetch($id);

                if (!empty($result)) {
                    $this->update($id);

                    $this->redirect($result['url']);
                }
                else {
                    $this->not_found();
                }
            }
        }
    }
}
