<?php

  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);
  error_reporting(E_ALL);

/**
 * POC permettant de parcourir le site www.yggtorrent.com et d'en extraire les torrents recherchés
 * User: Anthony Saugrain
 * Date: 26/07/2016
 * Time: 18:08
 * Last update : 14/09/2017
 */

require_once('lib/simple_html_dom.php');

class Ygg
{
    const BASE_URL = "https://ww1.yggtorrent.is/";
    const CATEGORY_MOVIES = 2145;
    const CATEGORY_AUDIO = 2139;
    const CATEGORY_APPS = 2144;
    const CATEGORY_VIDEOGAMES = 2142;
    const CATEGORY_EBOOKS = 2140;
    const CATEGORY_EMULATION = 2141;
    const CATEGORY_GPS = 2143;
    const CATEGORY_ADULT = 2188;

    private $up;
    private $down;
    private $torrents;
    private $html;
    private $search;
    private $pagination;
    private $login;
    private $password;
    private $order;
    private $url;
    private $checkWithoutPagination;

    /**
     * Ygg constructor.
     * @param string $search
     * @param int $pagination
     */
    public function __construct($search = '', $pagination = 1)
    {
        $this->up = '';
        $this->down = '';
        $this->torrents = array();
        $this->search = $search;
        $this->pagination = $pagination;
        $this->login = '****';
        $this->password = '****';
        $this->order = 'seeds';
        $this->checkWithoutPagination = false;

        // Create needed directories if needed
        $this->checkForDirectories('tmp');
        $this->checkForDirectories('dl');
    }

    public function getLogin(){
      return $this->login;
    }
    /**
     * Get category id by category name
     * @param $category
     * @return bool|int
     */
    public static function getCategoryId($category)
    {
        switch ($category) {
            case 'movies':
                $category_id = self::CATEGORY_MOVIES;
                break;
            case 'audio':
                $category_id = self::CATEGORY_AUDIO;
                break;
            case 'apps':
                $category_id = self::CATEGORY_APPS;
                break;
            case 'videogames':
                $category_id = self::CATEGORY_VIDEOGAMES;
                break;
            case 'ebooks':
                $category_id = self::CATEGORY_EBOOKS;
                break;
            case 'emulation':
                $category_id = self::CATEGORY_EMULATION;
                break;
            case 'gps':
                $category_id = self::CATEGORY_GPS;
                break;
            case 'adult':
                $category_id = self::CATEGORY_ADULT;
                break;
            default:
                $category_id = false;
                break;
        }

        return $category_id;
    }

    /**
     * @return string ratio up
     */
    public function getUp()
    {
        return $this->up;
    }

    /**
     * @return string ratio down
     */
    public function getDown()
    {
        return $this->down;
    }

    /**
     * @return array torrents
     */
    public function getTorrents()
    {
        return $this->torrents;
    }

    /**
     * Login and store cookie
     */
    public function login()
    {
        try {
            if ($this->call('login', '/user/login') == "") {
                if (($page = $this->call('basic', '')) !== false) {
                    $this->html = $this->open($page);
                    if ($this->findLink('/user/account')) {
                        if (!$this->findRatio()) {
                            throw new Exception('Unable to find ratio');
                        }
                        return true;
                    } else {
                        throw new Exception('Unable to login');
                    }
                }
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Generic cURL call
     * @param $type
     * @param $path
     * @return mixed
     * @throws Exception
     */
    private function call($type, $path)
    {
        try {
            // create curl resource
            $ch = curl_init();

            // Check if we already have FQDN
            $url = ((substr(self::BASE_URL, 0, 4) == 'http') ? self::BASE_URL : '') . $path;
            //var_dump($url);die();

            if ($type == 'login') {
                $datas = "id=" . urlencode($this->login) . "&pass=" . urlencode($this->password);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $datas);
                curl_setopt($ch, CURLOPT_POST, true);
              
            }

            // extra headers
            $headers[] = "Accept: */*";
            $headers[] = "Connection: Keep-Alive";

            $cookie_file_path = "./tmp/cookies.txt";

            //return the transfer as a string
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file_path);
            curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file_path);


            // $output contains the output string
          $output = curl_exec($ch);
//           echo $output;
            if ($type == 'download') {
                
                preg_match('/(([0-9]){3,8})/', $path, $matches);
                if(isset($matches[1]))
                  $idygg=$matches[1];
                else                  
                  $idygg=null;
              
                $destination = "./dl/".$idygg.".torrent";
                $file = fopen($destination, "w+");
                fputs($file, $output);
                fclose($file);
            }

            // close curl resource to free up system resources
            curl_close($ch);

            return $output;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Open a html file with simple html dom
     * @param $file
     * @return simple_html_dom
     * @throws Exception
     */
    private function open($file)
    {
        try {
            $html = new simple_html_dom();
            $html->load($file);

            return $html;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Find a particular link
     * @param $term
     * @return bool
     * @throws Exception
     */
    private function findLink($term)
    {
//       echo $this->html;
        try {
            $links = $this->html->find('a');
            foreach ($links as $link) {
                if (strpos($link->href, $term) !== false) {
                    return true;
                }
            }
            return false;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Extract ratios Up / Down
     * @return bool
     * @throws Exception
     */
    private function findRatio()
    {
      
        try {
            $links = $this->html->find('#top_panel .ct ul li strong');

            foreach ($links as $link) {
                if (strpos($link->innertext, 'GB') !== false || strpos($link->innertext, 'TB') !== false) {
                    if (strpos($link->children(0)->getAttribute('class'), 'ico_upload') !== false) {
                        $this->up = str_replace("GB", '', trim($link->innertext));
                    } else {
                        $this->down = str_replace("GB", '', trim($link->innertext));
                    }
                }
            }
            if ($this->up != '' && $this->down != '') {
                return true;
            }
            return false;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Search a torrent
     * @throws Exception
     */
    public function searchTorrent()
    {
        try {
            $term = urlencode($this->search);
            $this->url = '/engine/search?name='. $term.'&description=&file=&uploader=&category=2145&sub_category=all&do=search';
          $this->loopForTorrent();
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Loop and find torrent in given url
     * @param $url
     */
    private function loopForTorrent()
    {
        for ($i = 1; $i <= $this->pagination; $i++) {
            $page = $this->call('basic', $this->url . '&page=' . $i);
            $this->html = $this->open($page);
          

            $this->extractTorrents();
        }
    }

    /**
     * Extract founded torrents
     * @return bool
     * @throws Exception
     */
    private function extractTorrents()
    {
        try {
            $lines = $this->html->find('.results table tbody tr');
            foreach ($lines as $key => $line) {

                // Do not handle first line (header)
                if ($key > 0) {
                  // Extract torrent link & name
                    $links = $line->children(1)->find('a');
                    foreach ($links as $key => $link) {

                        if ($key == 0) {
                            $name = $link->innertext;
                        }
                        preg_match('/\/(([0-9]){3,8})/', $link->href, $matches);
                        if(isset($matches[1]))
                          $idygg=$matches[1];
                        else                  
                          $idygg=null;
                        $href="/engine/download_torrent?id=".$idygg;
                    }

                    if (is_null($href) || is_null($name)) {
                        continue;
                    }

                    // Extract size
                    $age = $line->children(4)->innertext;
                    $size = $line->children(5)->innertext;
                    // Extract seeds
                    $completed = $line->children(6)->innertext;
                    $seeds = $line->children(7)->innertext;

                    // Extract leechs
                    $leechs = $line->children(8)->innertext;

                    $this->torrents[] = array(
                        'name' => $name,
                        'href' => $href,
                        'size' => $size,
                        'age' => $age,
                        'completed' => $completed,
                        'seeds' => $seeds,
                        'leechs' => $leechs
                    );
                }
            }

            if (count($this->torrents) > 0) {
                if ($this->orderBy($this->order)) {
                    return true;
                }
            } else {
                // If no result with pagination, check witout pagination (just one time to avoid loop..)
                if (!$this->checkWithoutPagination) {
                    $this->checkWithoutPagination = true;
                    $page = $this->call('basic', $this->url);
                    $this->html = $this->open($page);
                    $this->extractTorrents();
                }
            }

            return false;

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Sort torrents
     * @return bool
     * @throws Exception
     */
    public function orderBy()
    {
        try {
            $order = $this->order;
            usort($this->torrents, function ($a, $b) use ($order) {
                return $b[$order] - $a[$order];
            });

            return true;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Search torrent of the moment in given category
     * @param $category
     * @throws Exception
     */
    public function searchMoment($category)
    {
        try {
            $this->loopForTorrent('/torrents/popular?category=' . $category);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Search torrent of the last day in given category
     * @param $category
     * @throws Exception
     */
    public function searchYesterday($category)
    {
        try {
            $this->loopForTorrent('/torrents/yesterday?category=' . $category);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Search torrent of the day in given category
     * @param $category
     * @throws Exception
     */
    public function searchToday($category)
    {
        try {
            $this->loopForTorrent('/torrents/today?category=' . $category);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Download given torrent
     * @param $url
     * @return bool
     * @throws Exception
     */
    public function download($url)
    {
        try {
            if ($this->call('download', $url) !== false) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
    * Check and create given directory
    * @param $dirname
    * @throws Exception
    **/
    public function checkForDirectories($dirname)
    {
        try{
            if(!is_dir($dirname)) {
                mkdir($dirname, 0755);
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
