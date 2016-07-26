<?php

class Common
{
    public static function parseRedirectUrl()
    {
        $redirectUrl = explode('redirecturl=', $_SERVER['REQUEST_URI']);
        if (count($redirectUrl) == 2) {
            $redirectUrl = $redirectUrl[1];
            return urldecode($redirectUrl);
        }
        return "";
    }


    public static function isMobile()
    {
        $mobileDetect = new Mobile_Detect();
        $mobile = Input::get('mobile');
        if ($mobileDetect->isMobile() || (isset($mobile) && $mobile == '1')) {
            Session::put('ismobile', true);
        }

        if (Session::get('ismobile')) {
            return true;
        }
    }

    public static function pagination($currentPage, $maxPage, $pageUrl, $hasParam = false)
    {
        if (!$currentPage) $currentPage = 1;
        if ($hasParam) {
            $paramChar = '&';
        } else {
            $paramChar = '?';
        }
        $nav = array(
            // bao nhiêu trang bên trái currentPage
            'left' => 2,
            // bao nhiêu trang bên phải currentPage
            'right' => 2,
        );
        // nếu maxPage < currentPage thì cho currentPage = maxPage
        if ($maxPage < $currentPage) {
            $currentPage = $maxPage;
        }

        // số trang hiển thị
        $max = $nav['left'] + $nav['right'];

        // phân tích cách hiển thị
        if ($max >= $maxPage) {
            $start = 1;
            $end = $maxPage;
        } elseif ($currentPage - $nav['left'] <= 0) {
            $start = 1;
            $end = $max + 1;
        } elseif (($right = $maxPage - ($currentPage + $nav['right'])) <= 0) {
            $start = $maxPage - $max;
            $end = $maxPage;
        } else {
            $start = $currentPage - $nav['left'];
            if ($start == 2) {
                $start = 1;
            }

            $end = $start + $max;
            if ($end == $maxPage - 1) {
                ++$end;
            }
        }

        $navig = '<ul class="pagination ' . (Input::get('webapp') == 1 ? 'pagination-sm' : false) . '">';
        if ($currentPage >= 2) {
            if ($currentPage >= $nav['left']) {
                if ($currentPage - $nav['left'] > 2 && $max < $maxPage) {
                    // thêm nút "First"
                    $navig .= '<li><a rel="nofollow" href="' . $pageUrl . $paramChar . 'p=1">1</a></li>';
                    $navig .= '<li><a href="javascript:;">...</a></li>';
                }
            }
            // thêm nút "«"
            $navig .= '<li><a rel="nofollow" href="' . $pageUrl . $paramChar . 'p=' . ($currentPage - 1) . '">«</a></li>';
        }

        for ($i = $start; $i <= $end; $i++) {
            // trang hiện tại
            if ($i == $currentPage) {
                $navig .= '<li class="active"><a href="javascript:;">' . $i . ' </a></li>';
            } // trang khác
            else {
                $navig .= '<li><a rel="nofollow" href="' . $pageUrl . $paramChar . 'p=' . $i . '">' . $i . '</a></li>';
            }
        }

        if ($currentPage <= $maxPage - 1) {
            // thêm nút "»"
            $navig .= '<li><a rel="nofollow" href="' . $pageUrl . $paramChar . 'p=' . ($currentPage + 1) . '">»</a></li>';

            if ($currentPage + $nav['right'] < $maxPage - 1 && $max + 1 < $maxPage) {
                // thêm nút "Last"
                $navig .= '<li><a href="javascript:;">...</a></li>';
                $navig .= '<li><a rel="nofollow" href="' . $pageUrl . $paramChar . 'p=' . $maxPage . '">' . $maxPage . '</a></li>';
            }
        }
        $navig .= '</ul>';

        return $navig;
    }

    public static function getClientIP()
    {

        if (array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER)) {
            return $_SERVER["HTTP_X_FORWARDED_FOR"];
        } else if (array_key_exists('REMOTE_ADDR', $_SERVER)) {
            return $_SERVER["REMOTE_ADDR"];
        } else if (array_key_exists('HTTP_CLIENT_IP', $_SERVER)) {
            return $_SERVER["HTTP_CLIENT_IP"];
        }

        return '';
    }

    public static function isWebApp()
    {
        $webapp = Input::get('webapp');
        if (isset($webapp) && $webapp == '1') {
            return true;
        } else {
            return false;
        }
    }

    public static function groupBy($data, $key)
    {
        $result = array();
        foreach ($data as $row) {
            $result[$row[$key]][] = $row;
        }
        return $result;
    }

    public static function urlHasParam($url)
    {
        $url = parse_url($url);
        if (isset($url['query'])) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * auto generate SKU (type = Char)
     * @param $num
     * @param int $min_length
     * @return int|string
     * return -1: is error
     */
    public static function generateSkuChar($num, $min_length = 0) {
        if(is_int($num) && $num > 0){
            $arrChar = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z");// O is code
            $countArrChar = 25; //array start to 0
            $kq = '';
            $totalChar = 0;
            while($num > 0){
                $kq = $arrChar[$num % $countArrChar].$kq;
                $num = floor($num / $countArrChar);
                $totalChar++;
            }
            if($min_length > 0){
                while(0 < $totalChar && $totalChar < $min_length){
                    $kq .= 'O';
                    $totalChar++;
                }
            }
            return $kq;

        }else{
            return -1;
        }
    }

    /**
     * automatic generate SKU (type = char merge number)
     * @param $num
     * @param int $min_length
     * @return int|string
     * return -1 is error
     */
    public static function generateSkuCharNum($num, $min_length = 0) {
        if (is_int($num) && $num > 0) {
            $arrCharNum = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z", "0", "1", "2", "3", "4", "5", "6", "7", "8", "9");// ) is backup
            $countArrCharNum = 35; //array start to 0
            $kq = '';
            $totalChar = 0;
            while($num > 0){
                $kq = $arrCharNum[$num % $countArrCharNum].$kq;
                $num = floor($num / $countArrCharNum);
                $totalChar++;
            }
            if($min_length > 0){
                while(0 < $totalChar && $totalChar < $min_length){
                    $kq .= 'O';
                    $totalChar++;
                }
            }
            return $kq;
        } else {
            return -1;
        }

    }
    public static function createStringSlug($str){
        $str = strtolower($str);
        $unicode = array(
            'a'=>'á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ',
            'd'=>'đ',
            'e'=>'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
            'i'=>'í|ì|ỉ|ĩ|ị',
            'o'=>'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
            'u'=>'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
            'y'=>'ý|ỳ|ỷ|ỹ|ỵ',
            '-'=>' '
            );
            foreach($unicode as $nonUnicode=>$uni){
                $str = preg_replace("/($uni)/i", $nonUnicode, $str);
            }

       return substr($str, 2);
    }

} 