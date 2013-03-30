<?php

class parser
{
    // Пул прокси (array)
    private $proxy;

    // Логин прокси
    private $proxy_login;

    // Пароль прокси
    private $proxy_password;

    //--------------------------------------------------------------------------------------------------
    // Получает содержимое страницы
    //--------------------------------------------------------------------------------------------------
    public function get_content($url, $proxy)
    {
        if ($proxy) {
            // Если прокси с авторизацией
            if (!empty($this->proxy_login) && !empty($this->proxy_password)) {
                $auth = base64_encode($this->proxy_login . ':' . $this->proxy_password);
            }

            $aContext = array(
                'http' => array(
                    'proxy' => $proxy,
                    'request_fulluri' => true,
                    'header' => "Proxy-Authorization: Basic $auth",
                ),
            );

            $cxContext = stream_context_create($aContext);
        }

        if ($sFile = file_get_contents($url, False, $cxContext)) return $sFile;
        else return false;
    }

    //--------------------------------------------------------------------------------------------------
    // Рекурсивно обходит все прокси, пока не получит содержимое, или пока кончится пул проксей
    //--------------------------------------------------------------------------------------------------
    public function get_content_recursive($url)
    {
        for($i=0; $i < count($this->proxy); $i++) {
            if($content = $this->get_content($url, $this->proxy[$i])) return $content;
        }
    }


    //--------------------------------------------------------------------------------------------------
    // Получает список прокси
    //--------------------------------------------------------------------------------------------------
    public
    function set_proxy($path)
    {
        // читаем файл
        if ($file_handle = fopen($path, "r")) {
            while (!feof($file_handle)) {
                $line[] = fgets($file_handle);
            }
            fclose($file_handle);
        }

        if (!empty($line)) $this->proxy = $line;
        else return false;
    }

    //--------------------------------------------------------------------------------------------------
    // Устанавливает логин прокси
    //--------------------------------------------------------------------------------------------------
    public
    function set_login($login)
    {
        $this->proxy_login = $login;
    }

    //--------------------------------------------------------------------------------------------------
    // Устанавливает пароль прокси
    //--------------------------------------------------------------------------------------------------
    public
    function set_password($password)
    {
        $this->proxy_password = $password;
    }

    //--------------------------------------------------------------------------------------------------
    // Предостовляем случайный прокси сервер
    //--------------------------------------------------------------------------------------------------
    public
    function get_random_proxy()
    {
        $count_proxy = count($this->proxy);
        return $this->proxy[rand(0, $count_proxy-1)];
    }

    //--------------------------------------------------------------------------------------------------
    // Предостовляем прокси сервер по его номеру в массиве
    //--------------------------------------------------------------------------------------------------
    public
    function get_proxy($id)
    {
        return $this->proxy[$id];
    }


}


