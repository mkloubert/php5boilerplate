<?php

/**********************************************************************************************************************
 * php5boilerplate (https://github.com/mkloubert/php5boilerplate)                                                     *
 * Copyright (c) Marcel Joachim Kloubert <marcel.kloubert@gmx.net>, All rights reserved.                              *
 *                                                                                                                    *
 *                                                                                                                    *
 * This software is free software; you can redistribute it and/or                                                     *
 * modify it under the terms of the GNU Lesser General Public                                                         *
 * License as published by the Free Software Foundation; either                                                       *
 * version 3.0 of the License, or (at your option) any later version.                                                 *
 *                                                                                                                    *
 * This software is distributed in the hope that it will be useful,                                                   *
 * but WITHOUT ANY WARRANTY; without even the implied warranty of                                                     *
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU                                                  *
 * Lesser General Public License for more details.                                                                    *
 *                                                                                                                    *
 * You should have received a copy of the GNU Lesser General Public                                                   *
 * License along with this software.                                                                                  *
 **********************************************************************************************************************/

namespace php5bp\Http\Requests;

use \php5bp\IO\Files\UploadedFile;
use \php5bp\IO\Files\UploadedFileInterface;
use \System\Linq\Enumerable;


/**
 * A HTTP request context.
 *
 * @package php5bp\Http\Requests
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
class Context extends \php5bp\Object implements ContextInterface {
    /**
     * Checks the user agent header field for a specific expression.
     *
     * @param string $expr The expression.
     *
     * @return bool Expression was found or not.
     */
    protected static function checkAgentFor($expr) {
        if (\array_key_exists('HTTP_USER_AGENT', $_SERVER)) {
            return false !== \stripos($_SERVER['HTTP_USER_AGENT'], $expr);
        }

        return false;
    }

    public function cookie($name, $defaultValue = null, &$found = null) {
        return static::getArrayValue($_COOKIE, $name, $defaultValue, $found);
    }

    public function doNotTrack() {
        switch (\trim($this->header('DNT'))) {
            case '1':
                return true;

            case '0':
                return false;
        }

        // not set or invalid value
        return null;
    }

    public function env($name, $defaultValue = null, &$found = null) {
        return static::getArrayValue($_ENV, $name, $defaultValue, $found);
    }

    public function file($name) {
        $name = \trim(\strtolower($name));

        return $this->uploadedFiles()
                    ->lastOrDefault(function(UploadedFileInterface $x) use ($name) {
                                        return \trim(\strtolower($x->field())) == $name;
                                    });
    }

    public function get($name, $defaultValue = null, &$found = null) {
        return static::getArrayValue($_GET, $name, $defaultValue, $found);
    }

    /**
     * Returns the value of an array.
     *
     * @param array &$arr The array.
     * @param string $name The name of the key.
     * @param mixed $defaultValue The value to return if $name was not found.
     * @param bool &$found The variable where to write if $name was not found.
     *
     * @return mixed The value.
     */
    protected static function getArrayValue(array &$arr, $name, $defaultValue, &$found) {
        $result = $defaultValue;

        $found = false;

        $name = \trim(\strtolower($name));
        foreach ($arr as $key => $value) {
            if (\trim(\strtolower($key)) == $name) {
                // last wins

                $found  = true;
                $result = $value;
            }
        }

        return $result;
    }

    public function hasCookie($name) {
        $this->cookie($name, null, $result);
        return $result;
    }

    public function hasFile($name) {
        return $this->file($name) instanceof UploadedFileInterface;
    }

    public function hasGet($name) {
        $this->get($name, null, $result);
        return $result;
    }

    public function hasHeader($name) {
        $this->header($name, null, $result);
        return $result;
    }

    public function hasPost($name) {
        $this->post($name, null, $result);
        return $result;
    }

    public function hasRequest($name) {
        $this->request($name, null, $result);
        return $result;
    }

    public function header($name, $defaultValue = null, &$found = null) {
        $headers = \getallheaders();

        return static::getArrayValue($headers,
                                     $name, $defaultValue, $found);
    }

    public function isChrome() {
        return static::checkAgentFor('chrome');
    }

    public function isEdge() {
        return static::checkAgentFor('edge');
    }

    public function isFacebook() {
        return static::checkAgentFor('facebookexternalhit');
    }

    public function isFirefox() {
        return static::checkAgentFor('firefox') ||
               static::checkAgentFor('gecko');
    }

    public function isMobile() {
        if (\array_key_exists('HTTP_USER_AGENT', $_SERVER)) {
            // based on: http://detectmobilebrowsers.com/
            return \preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i', $_SERVER['HTTP_USER_AGENT']) ||
                   \preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i', \substr($_SERVER['HTTP_USER_AGENT'], 0, 4));
        }

        return false;
    }

    public function isSafari() {
        return static::checkAgentFor('safari');
    }

    public function isSearchEngine() {
        if (\array_key_exists('HTTP_USER_AGENT', $_SERVER)) {
            $userAgent = $_SERVER['HTTP_USER_AGENT'];

            return Enumerable::create(\php5bp::conf('known.searchengines'))
                             ->any(function($x) use ($userAgent) {
                                       return false !== \stripos($userAgent, $x);
                                   });
        }

        return false;
    }

    public function isTrident() {
        return static::checkAgentFor('trident');
    }

    public function isTwitter() {
        return static::checkAgentFor('twitterbot');
    }

    public function post($name, $defaultValue = null, &$found = null) {
        return static::getArrayValue($_POST, $name, $defaultValue, $found);
    }

    public function request($name, $defaultValue = null, &$found = null) {
        return static::getArrayValue($_REQUEST, $name, $defaultValue, $found);
    }

    public function server($name, $defaultValue = null, &$found = null) {
        return static::getArrayValue($_SERVER, $name, $defaultValue, $found);
    }

    public function session($name, $defaultValue = null, &$found = null) {
        return static::getArrayValue($_SESSION, $name, $defaultValue, $found);
    }

    public function uploadedFiles() {
        return UploadedFile::create();
    }
}
