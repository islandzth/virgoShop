<?php

class StringUtils
{

    public static function notEmpty($str)
    {
        if (isset($str) && $str != null && $str != '') {
            return true;
        } else {
            return false;
        }
    }

    public static function thousandsCurrencyFormat($num)
    {
        $x = round($num);
        $x_number_format = number_format($x);
        $x_array = explode(',', $x_number_format);
        $x_parts = array('k', 'm', 'b', 't');
        $x_count_parts = count($x_array) - 1;
        $x_display = $x_array[0] . ((int)$x_array[1][0] !== 0 ? '.' . $x_array[1][0] : '');
        $x_display .= $x_parts[$x_count_parts - 1];
        return $x_display;
    }

    public static function mb_ucwords($str)
    {
        $str = mb_convert_case($str, MB_CASE_TITLE, "UTF-8");
        return ($str);
    }

    public static function getAscii($str)
    {
        $coDau = array("à", "á", "ạ", "ả", "ã", "â", "ầ", "ấ", "ậ", "ẩ", "ẫ", "ă", "ằ", "ắ"
        , "ặ", "ẳ", "ẵ", "è", "é", "ẹ", "ẻ", "ẽ", "ê", "ề", "ế", "ệ", "ể", "ễ", "ì", "í", "ị", "ỉ", "ĩ",
            "ò", "ó", "ọ", "ỏ", "õ", "ô", "ồ", "ố", "ộ", "ổ", "ỗ", "ơ"
        , "ờ", "ớ", "ợ", "ở", "ỡ",
            "ù", "ú", "ụ", "ủ", "ũ", "ư", "ừ", "ứ", "ự", "ử", "ữ",
            "ỳ", "ý", "ỵ", "ỷ", "ỹ",
            "đ",
            "À", "Á", "Ạ", "Ả", "Ã", "Â", "Ầ", "Ấ", "Ậ", "Ẩ", "Ẫ", "Ă"
        , "Ằ", "Ắ", "Ặ", "Ẳ", "Ẵ",
            "È", "É", "Ẹ", "Ẻ", "Ẽ", "Ê", "Ề", "Ế", "Ệ", "Ể", "Ễ",
            "Ì", "Í", "Ị", "Ỉ", "Ĩ",
            "Ò", "Ó", "Ọ", "Ỏ", "Õ", "Ô", "Ồ", "Ố", "Ộ", "Ổ", "Ỗ", "Ơ"
        , "Ờ", "Ớ", "Ợ", "Ở", "Ỡ",
            "Ù", "Ú", "Ụ", "Ủ", "Ũ", "Ư", "Ừ", "Ứ", "Ự", "Ử", "Ữ",
            "Ỳ", "Ý", "Ỵ", "Ỷ", "Ỹ",
            "Đ", "ê", "ù", "à");
        $khongDau = array("a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a"
        , "a", "a", "a", "a", "a", "a",
            "e", "e", "e", "e", "e", "e", "e", "e", "e", "e", "e",
            "i", "i", "i", "i", "i",
            "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o"
        , "o", "o", "o", "o", "o",
            "u", "u", "u", "u", "u", "u", "u", "u", "u", "u", "u",
            "y", "y", "y", "y", "y",
            "d",
            "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A"
        , "A", "A", "A", "A", "A",
            "E", "E", "E", "E", "E", "E", "E", "E", "E", "E", "E",
            "I", "I", "I", "I", "I",
            "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O"
        , "O", "O", "O", "O", "O",
            "U", "U", "U", "U", "U", "U", "U", "U", "U", "U", "U",
            "Y", "Y", "Y", "Y", "Y",
            "D", "e", "u", "a");
        return str_replace($coDau, $khongDau, $str);
    }

    public static function rewriteUrl($input, $keyword = false)
    {
        $input = trim($input);

        $input = self::get_words($input, 15);
        if ($keyword) {
            $input = preg_replace("![^a-z0-9]+!i", "+", self::getAscii($input));
        } else {
            $input = preg_replace("![^a-z0-9]+!i", "-", self::getAscii($input));
        }
        $input = filter_var($input, FILTER_SANITIZE_URL);
        return trim(mb_strtolower($input, 'UTF-8'), '-');
    }

    public static function kqRewrite($q)
    {
        return trim(str_replace(' ', '+', $q));
    }

    public static function validateEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    public static function validateUrl($url)
    {
        return filter_var($url, FILTER_VALIDATE_URL);
    }

    public static function validateIp($ip)
    {
        return filter_var($ip, FILTER_VALIDATE_IP);
    }

    /**
     * Using to get domain name from URL
     * @param string $url
     * @return string|boolean
     */
    public static function getDomainName($url)
    {
        $parse = parse_url($url);
        return $parse['host'];
    }

    /**
     * Time ago
     * @param int $time
     * @return string
     */
    public static function time_ago($time, $format = null)
    {
        $time2 = time() - $time;
        if ($time2 > 86400) {
            $days = floor($time2 / 86400);
            $hours = floor(($time2 - ($days * 86400)) / 3600);

            if ($days > 5) {

                $took = date('d/m/Y', $time);
            } else {
                if ($format == 2) {
                    $took = $days . ' ngày trước';
                } else {
                    $took = 'cách đây ' . $days . ' ngày';
                }
            }
        } elseif ($time2 > 3600) {
            $hours = floor(($time2 / 60) / 60);
            $mins = floor(($time2 - ($hours * 3600)) / 60);

            if ($format == 2) {
                $took = $hours . ' giờ trước';
            } else {
                $took = 'cách đây ' . $hours . ' giờ';
            }
        } elseif ($time2 > 60) {
            $mins = floor($time2 / 60);
            if ($format == 2) {
                $took = $mins . ' phút trước';
            } else {
                $took = 'cách đây ' . $mins . ' phút';
            }
        } else {
            if ($format == 2) {
                $took = 'vài giây trước';
            } else {
                $took = 'cách đây vài giây';
            }
        }

        return $took;
    }

    public static function cut_string($str, $len)
    {
        if ($str == '' || $str == NULL)
            return $str;
        if (is_array($str))
            return $str;
        $str = trim($str);
        if (strlen($str) <= $len)
            return $str;
        $str = substr($str, 0, $len);
        return $str;
    }

    public static function get_words($str, $num, $dot = true)
    {
        $limit = $num - 1;
        $str_tmp = '';
        $arrstr = explode(" ", $str);
        if (count($arrstr) <= $num) {
            return $str;
        }
        if (!empty($arrstr)) {
            for ($j = 0; $j < count($arrstr); $j++) {
                $str_tmp .= " " . $arrstr[$j];
                if ($j == $limit) {
                    break;
                }
            }
        }
        if ($dot == true) {
            return $str_tmp . ' ...';
        } else {
            return $str_tmp;
        }
    }

    public static function mb_ucfirst($string)
    {
        $string = mb_strtoupper(mb_substr($string, 0, 1, "UTF-8"), "UTF-8") . mb_substr($string, 1, null, "utf-8");
        return $string;
    }

    public static function hasUtmParam($input)
    {
        $input = strtolower($input);
        if (strpos($input, 'utm_campaign') !== false) {
            return true;
        } else {
            return false;
        }
    }

    public static function hasEmTrackParam($input)
    {
        $input = strtolower($input);
        if (strpos($input, 'emtrack') !== false) {
            return true;
        } else {
            return false;
        }
    }

    public static function AppendCampaignToString($string)
    {
        if (!self::hasUtmParam($string)) {
            $regex = '#(<a href=")([^"]*)("[^>]*?>)#i';
            return preg_replace_callback($regex, array(self, "_appendCampaignToString"), $string);
        } else {
            return $string;
        }
    }

    public static function _AppendCampaignToString($match)
    {
        $url = $match[2];
        if (strpos($url, '?') === false) {
            $url .= '?';
        }
        $url .= '&utm_source=emailsys&utm_medium=emailsys&utm_campaign=emailsys&emtrack=1';
        return $match[1] . $url . $match[3];
    }

    public static function getTips()
    {
        $tips = array(
            'TTS sẽ tự động xóa các sản phẩm không đúng giá sỉ',
            'Sử dụng chức năng theo dõi NCC để nhận thông báo khi có sản phẩm mới',
            'Nhấn nút "Tôi đã liên hệ" để lưu lại những NCC đã liên hệ',
            'Đăng nhiều sản phẩm hơn để tăng hiệu quả kinh doanh',
            'Đánh giá sản phẩm để nhận được gợi ý tốt hơn'
        );
        return $tips[rand(0, count($tips) - 1)];
    }

    public static function getProductUnit($unitType)
    {
        switch ($unitType) {
            case 1:
                $unitText = 'cái';
                break;
            case 2:
                $unitText = 'cặp';
                break;
            case 3:
                $unitText = 'ri';
                break;
            case 4:
                $unitText = 'đôi';
                break;
            case 5:
                $unitText = 'bộ';
                break;
        }
        return $unitText;
    }

    public static function makeNoFollow($str)
    {
        $nofollow = 'nofollow';
        //See if there is already a "rel" attribute
        if (strpos($str, "rel")) {
            $pattern = "/rel=([\"'])([^\\1]+?)\\1/";
            $replace = "rel=\\1\\2 $nofollow\\1";
        } else {
            $pattern = "/<a /";
            $replace = "<a rel=\"$nofollow\" ";
        }
        $str = preg_replace($pattern, $replace, $str);
        return $str;
    }

    public static function makeTargetBlank($str)
    {
        $str = preg_replace('/<(a.*?href=\"[^#])([^>]+)>/', '<\\1\\2 target="_blank">', $str);
        return $str;
    }

    public static function replaceLinks($content)
    {

        $regexp = "<a\s[^>]*href=(\"??)([^\" >]*?)\\1[^>]*>";
        if (preg_match_all("/$regexp/siU", $content, $matches, PREG_SET_ORDER)) {
            if (!empty($matches)) {
                for ($i = 0; $i < count($matches); $i++) {

                    $tag = $matches[$i][0];
                    $tag2 = $matches[$i][0];
                    $url = $matches[$i][0];

                    $noFollow = '';

                    $pattern = '/target\s*=\s*"\s*_blank\s*"/';
                    preg_match($pattern, $tag2, $match, PREG_OFFSET_CAPTURE);
                    if (count($match) < 1)
                        $noFollow .= ' target="_blank" ';

                    $pattern = '/rel\s*=\s*"\s*[n|d]ofollow\s*"/';
                    preg_match($pattern, $tag2, $match, PREG_OFFSET_CAPTURE);
                    if (count($match) < 1)
                        $noFollow .= ' rel="nofollow" ';

                    $pos = strpos($url, Config::get('app.url'));
                    if ($pos === false) {
                        $tag = rtrim($tag, '>');
                        $tag .= $noFollow . '>';
                        $content = str_replace($tag2, $tag, $content);
                    }
                }
            }
        }

        $content = str_replace(']]>', ']]&gt;', $content);
        return $content;


    }

    public static function productNameSpell($input)
    {
        $wrong = array('xương may', 'XƯƠNG MAY');
        $right = array('xưởng may', 'XƯỞNG MAY');
        return str_replace($wrong, $right, $input);
    }

    public static function genShopUrl($shopName)
    {
        $shopUrl = self::getAscii($shopName);
        $shopUrl = preg_replace("![^a-z0-9]+!i", "", $shopUrl);
        $shopUrl = filter_var($shopUrl, FILTER_SANITIZE_URL);
        $shopUrl = trim(mb_strtolower($shopUrl, 'UTF-8'));
        return $shopUrl;
    }

    public static function removeNewLine($input)
    {
        return trim(preg_replace('/\s+/', ' ', $input));
    }
}
