<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Log;
use WpOrg\Requests\Requests;

class LemneController extends Controller
{
    public function woods(){

        $utmn = rand(10000000, 60000000);
        $utmhid = rand(100000000, 900000000);
        $time = time();
        $acum = time();
        $prevsession = $acum - rand(2,4)*60;
        $firstsession = $prevsession - rand(8,10)*60;

        $data = file_get_contents('https://www.deschide.md/articole/rss.xml');
        $data = simplexml_load_string($data);

        $articles = $data->channel->item;
        $count = count($articles) - 1;
        $rand = rand(0,$count);

        $title = $articles[$rand]->title;
        $link = $articles[$rand]->link;
        $page = substr($link, 18);

        $title = rawurlencode($title);
        $page = rawurlencode($page);

        $request = Requests::get('http://www.google-analytics.com/__utm.gif?utmwv=4.7.2&utmn='.$utmn.'&utmhn=deschide.md&utmcs=UTF-8&utmsr=1366×768&utmsc=24-bit&utmul=en-us&utmje=1&utmfl=10.1%20r53&utmdt='.$title.'&utmhid='.$utmhid.'&utmr=http%3A%2F%2Fdeschide.md%2F&utmp='.$page.'&utmac=UA-45464002-1&utmcc=__utma%3D199946558.1448353202.'.$firstsession.'.'.$prevsession.'.'.$acum.'.4%3B%2B__utmz%3D199946558.'.$time.'.4.1.utmcsr%3D(direct)%7Cutmccn%3D(direct)%7Cutmcmd%3D(none)%3B&gaq=1', array('Accept' => 'text/html'));

        Log::info(json_encode($request));

    }
}
