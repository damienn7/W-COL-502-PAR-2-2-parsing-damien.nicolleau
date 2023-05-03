<?php

foreach ($argv as $key => $arg) {
    if ($key>0) {
        echo $arg;
        if ($arg == "fiche_h2g2") {
            $html = get_web_page("http://www.parsing.fr/fiche_exemple_h2g2.html");
        } elseif ($arg == "fiche_batman") {
            $html = get_web_page("http://www.parsing.fr/fiche_exemple_batman.html");
        } else {
            die("        Vous essayer de parser une source inconnue.        \n        Merci de reessayer avec la syntaxe suivante :        \n        php parsing.php fiche_h2g2        \n        OU BIEN        \n        php parsing.php fiche_batman");
        }
    }
    }

// echo htmlentities(get_web_page("http://www.parsing.fr/fiche_exemple_h2g2.html"));

// match du title
preg_match("/((<h2(.*)>)(.*)<\/h2>)/",$html,$match);
// match de la date de publication
preg_match("/((<span(.*))\((.*([0-9]).*)\)<\/span>)/",$html,$match2);

$release_date = $match2[4];

$title = $match[4]; 

// exemple de sous-groupes strong :
// (<font><strong>(.*)<\/strong>(\((.*([0-9]).*)\))<\/font>)

// bloque au match du resume
// preg_match("/<div class=\"overview\" dir=\"auto\"[^>]*>(.*?)<\/div[^>]*>(?=<!--overview-->)/",$html,$match3);

// var_dump($match3);

$json = result(["movie"=>["title"=>$title,"releaseDate"=>$release_date]]);

create_json($json);

// echo $json;

function get_web_page($url)
{
        $uagent = "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0";

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);   // возвращает веб-страницу
        curl_setopt($ch, CURLOPT_HEADER, 0);           // не возвращает заголовки
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);   // переходит по редиректам
        curl_setopt($ch, CURLOPT_ENCODING, "");        // обрабатывает все кодировки
        curl_setopt($ch, CURLOPT_USERAGENT, $uagent);  // useragent
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 120); // таймаут соединения
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);        // таймаут ответа
        curl_setopt($ch, CURLOPT_MAXREDIRS, 50);       // останавливаться после 10-ого редиректа
        curl_setopt($ch, CURLOPT_COOKIEJAR, $_SERVER["DOCUMENT_ROOT"]."/my_cookies.txt");  
        curl_setopt($ch, CURLOPT_COOKIEFILE, $_SERVER["DOCUMENT_ROOT"]."/my_cookies.txt");

        $content = curl_exec($ch);
        curl_close($ch);
        return $content;
}

function result($result){
    $movie_content = "{\"status\":\"ok\",\"result\":{";

    foreach ($result as $key => $value) {
        $movie_content.= make_content($value).",";
    }
    $movie_content = substr($movie_content,0,-1);
    $movie_content .= "}}";
    return $movie_content;
}

function make_content($movie){
    $content = "\"movie\":[{";

    foreach ($movie as $key => $value) {
        $content .= "\"$key\":\"$value\",";
    }
    $content = substr($content,0,-1);
    $content .="}]";
    return $content;
}

function create_json($content){
    file_put_contents("result.json",$content);
}