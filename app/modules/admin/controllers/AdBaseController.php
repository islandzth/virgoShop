<?php
class AdBaseController extends Controller
{

    public function __construct()
    {
        // $this->loadCategory();
        // $this->getTagCloud();
        if (Auth::check()) {

        }else{
            
        }
    }

    /**
     * Setup the layout used by the controller.
     *
     * @return void
     */
    protected function setupLayout()
    {
        if (!is_null($this->layout)) {
            $this->layout = View::make($this->layout);
        }
    }


    private function surveyGlad()
    {
        if (Auth::check()) {
            $userObj = User::getInstance();
            $surveyObj = Survey::getInstance();
            $productViewed = $userObj->countProductViewed(Auth::getUserId());
            $isSurvey = $surveyObj->isSurvey(Auth::getUserId(), 'glad');
            if ($productViewed >= 5 && !$isSurvey) {
                View::share('surveyglad', true);
            }
        }
    }

    private function emailTrackOpen()
    {
        if (Input::get('emtrack') == '1') {
            $_redis = new Redis();
            $_redis->pconnect(Config::get('database.redis_statistic.default.host'), Config::get('database.redis_statistic.default.port'));
            $_redis->setOption(Redis::OPT_PREFIX, 'TTS:STAT:');
            $_redis->incr("count:emailopened");
        }
    }

    private function getSysNoti()
    {
        if (Auth::check()) {
            if (Auth::isShop()) {
                $sysnotiObj = Sysnoti::getInstance();
                View::share('unreadsysnoti', $sysnotiObj->countNotiByUserId(Auth::getUserId(), '0'));
            }
        }
    }


    private function isRetailerPhone()
    {
        if (Auth::check()) {
            if (Auth::accountType() == 0) {
                //is retailer
                //check phone
                $retailerObj = Retailer::getInstance();
                $retailerInfo = $retailerObj->getRetailerByUserId(Auth::getUserId());
                $retailerPhone = $retailerObj->getPhone($retailerInfo['id']);
                if (isset($retailerPhone[0]) && $retailerPhone[0]['phone'] == '0') {
                    View::share('enterretailerphone', true);
                }
            }
        }
    }

    private function isRetailerWrongAdress()
    {
        if (Auth::check()) {
            if (Auth::accountType() == 0) {
                $retailerObj = Retailer::getInstance();
                $retailerInfo = $retailerObj->getRetailerByUserId(Auth::getUserId());
                if ($retailerInfo) {
                    $retailerAddress = $retailerObj->getOffice($retailerInfo['id']);
                    if (strlen($retailerAddress['0']['address']) < 2) {
                        View::share('enterretaileraddress', true);
                    }
                }
            }
        }
    }

    public function userHaveName()
    {
        $userObj = User::getInstance();
        $firstName = $userObj->getUserField(Auth::getUserId(), 'first_name');
        $lastName = $userObj->getUserField(Auth::getUserId(), 'last_name');

        if (!$firstName || !$lastName) {
            View::share('noname', true);
        }
    }

    public function getTagCloud()
    {
        $tagObj = Tag::getInstance();
        $mcKey = 'tagcloud';
        $mcContent = Cache::get($mcKey);
        if ($mcContent) {
            $tagCloud = $mcContent;
        } else {
            $tagCloud = $tagObj->getTagList(0, 35, 'total');
            Cache::put($mcKey, $tagCloud, 60);
        }
        View::share('tagcloud', $tagCloud);
    }


    public function loadCategory()
    {
        $category_obj = Category::getInstance();
        //fetch parent list
        $pCat = [];

        $parentCatListId = $category_obj->getParentCategory();
        for ($i = 0; $i < count($parentCatListId); $i++) {
            $c = $category_obj->getCategoryById($parentCatListId[$i]);

            //fetch child
            $c['child'] = $this->fetchChildCat($parentCatListId[$i]);

            $pCat[] = $c;
        }
        View::share('navbarCatList', $pCat);
    }

    public function fetchChildCat($parent_id)
    {
        $child = null;
        $catObj = Category::getInstance();
        $productObj = Product::getInstance();
        $childCatListId = $catObj->getCategoryListByParentId($parent_id);
        for ($i = 0; $i < count($childCatListId); $i++) {
            $cat = $catObj->getCategoryById($childCatListId[$i]);
            $cat['total'] = $productObj->countProductByCategoryId($childCatListId[$i]);

            $childLv3 = $catObj->getCategoryListByParentId($childCatListId[$i]);
            $childLv3List = array();
            if (count($childLv3) > 0) {
                for ($x = 0; $x < count($childLv3); $x++) {
                    $c = $catObj->getCategoryById($childLv3[$x]);
                    $childLv3List[] = $c;
                }
            }
            $cat['child'] = $childLv3List;

            $child[] = $cat;
        }
        return $child;
    }

    private function getRecentCollection()
    {
        $wiliObj = Wili::getInstance();
        $recent = $wiliObj->getGroupListByUserId(Auth::getUserId(), 0, 1);
        if (isset($recent[0])) {
            $recent = $wiliObj->getGroupById($recent[0]);
            View::share('recentCollection', $recent);
        }
    }

}
