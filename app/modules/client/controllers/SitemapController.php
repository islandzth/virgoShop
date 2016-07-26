<?php

class SitemapController extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        ini_set("memory_limit", "500M");
        header("Content-type: text/xml");
        echo '<?xml version="1.0" encoding="UTF-8"?>';
    }

    public function getIndex($args = null)
    {
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        echo '

<url>
<loc>http://thitruongsi.com/</loc>
    <changefreq>daily</changefreq>
    <priority>1</priority>
 </url>

<url>
<loc>http://thitruongsi.com/new/</loc>
    <changefreq>daily</changefreq>
    <priority>1</priority>
 </url>
';


        echo '</urlset>';
    }

    public function getProduct()
    {
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        $productObj = Product::getInstance();
        $productList = $productObj->getProductList(1, 0, 50000);
        foreach ($productList as $p) {
            $url = Config::get('app.url') . StringUtils::rewriteUrl($p['product_name']) . '-' . $p['product_id'] . '.html';
            echo '<url>
                    <loc>' . $url . '</loc>
                    <lastmod>' . date('Y-m-d', $p['uptime']) . '</lastmod>
                    <changefreq>daily</changefreq>
                    <priority>0.8</priority>
                 </url>';
        }
        echo '</urlset>';
    }

    public function getTag()
    {
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        $tagObj = Tag::getInstance();
        $tagList = $tagObj->getTagList(0, 20000);
        foreach ($tagList as $t) {
            $url = Config::get('app.url') . 'tag/' . $t['id'] . '-' . $t['tag_rewrite'];
            echo '<url>
                    <loc>' . $url . '</loc>
                    <changefreq>daily</changefreq>
                    <priority>0.6</priority>
                 </url>';
        }
        echo '</urlset>';
    }

    public function getSearch()
    {
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        $searchObj = Search::getInstance();
        $searchQuery = $searchObj->getTopQuery(0, 20000);
        foreach ($searchQuery as $t) {
            $t['query'] = str_replace(' ', '+', $t['query']);

            $t['query'] = str_replace('&', '+', $t['query']);
            $url = Config::get('app.url') . 'search/product?q=' . $t['query'];
            echo '<url>
                    <loc>' . $url . '</loc>
                    <changefreq>daily</changefreq>
                    <priority>0.3</priority>
                 </url>';
        }
        echo '</urlset>';
    }

    public function getCategory()
    {
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        $catObj = Category::getInstance();
        $catParentList = $catObj->getParentCategory();
        for ($i = 0; $i < count($catParentList); $i++) {
            $url = Config::get('app.url') . 'category/' . $catParentList[$i] . '.html';
            echo '<url>
                    <loc>' . $url . '</loc>
                    <changefreq>daily</changefreq>
                    <priority>0.9</priority>
                 </url>';

            //get list child
            $childList = $catObj->getCategoryListByParentId($catParentList[$i]);
            for ($x = 0; $x < count($childList); $x++) {
                $url = Config::get('app.url') . 'category/' . $childList[$x] . '.html';
                echo '<url>
                    <loc>' . $url . '</loc>
                    <changefreq>daily</changefreq>
                    <priority>0.9</priority>
                 </url>';
            }
        }
        echo '</urlset>';
    }

    public function getCollection()
    {

        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        $userObj = User::getInstance();
        $userList = $userObj->getUserList(0, 10000, 'logged');
        foreach ($userList as $user) {
            $url = Config::get('app.url') . 'collection/' . $user['user_code'] . '/';
            echo '<url>
                    <loc>' . $url . '</loc>
                    <changefreq>weekly</changefreq>
                    <priority>0.4</priority>
                 </url>';
        }
        echo '</urlset>';
    }

}
