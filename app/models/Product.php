<?php

class Product extends BaseModel
{

    protected $table = 'product';
    protected $primaryKey = 'product_id';

    protected $guarded = array('product_id', 'created_at', 'updated_at');

    public $timestamps = true;


    // public function options(){
    //     return $this->belongsToMany('AttributeOption', 'product_option', 'product_id', 'attr_option_id');
    // }


    public function option()
    {
        return $this->hasMany('ProductOption', 'product_id', 'product_id');
    }
    
    public function images()
    {
        return $this->hasMany('ProductImage','product_id','product_id');
    }
    

    public function categories(){
        return $this->belongsToMany('Category', 'product_category', 'product_id', 'category_id')/*->orderBy('id','ASC')*/;
    }

    public function inProgressRequests(){
        // get in progress load product request status
        $inProgressStatus = LoadProductRequestStatus::getInprogressStatus()->toArray();

        $inProgressStatusIds = __::pluck($inProgressStatus,'load_product_request_status_id');

        $query = $this->belongsToMany('LoadProductRequest', 'load_product_request_product', 'product_id', 'load_product_request_id');
        if(count($inProgressStatusIds)){
            $query->whereIn('status_id',$inProgressStatusIds);
        }
        return $query;
    }
    
    /*public function validate(){
        $rules = array(
                'name'=>'required',
                'identity'=>'required|unique:shop,identity,'.$this->id,
                'is_admin_updated'=>'required',
                'address'=>'required',
                'city'=>'required',
                'district'=>'required',
                'website'=>'required|url',
            );
        $validator = Validator::make($this->toArray(), $rules);
        return $validator;
    }*/

    // check if product has variants
    public function hasVariant(){
        return $this->variants()->count();
    }

    public function generateSKU(){
        $shopId = Auth::getShopId();
        $shop = Shop::find($shopId);
        
        $categories = DB::table('product_category')->where('product_id','=',$this->product_id)->get();
        $lastCategory = $categories[count($categories)-1];
        $lastCategory = Category::find($lastCategory->category_id);
        
        // generate SKU
        $sku = $lastCategory->identity.'_'.$shop->identity.'_'.$this->product_id;
        $this->sku = $sku;
        $this->save();
    }

    public function getProductByShopCategory($shopId, $category)
    {

    }

    /* get product instock quantity */
    public static function getInStockQuantity($productId){
        $product = self::find($productId);
        return $product->inStockQuantity();
    }

    public function inStockQuantity(){
        return $this->inventory_quantity;
    }

    public function setRetailerView($productId, $retailerId)
    {
        $this->redis->lrem('product:' . $productId . ':retailerview', $retailerId);
        $this->redis->lpush('product:' . $productId . ':retailerview', $retailerId);
    }

    public function getRetailerView($productId, $start = 0, $end = -1)
    {
        return $this->redis->lrange('product:' . $productId . ':retailerview', $start, $end);
    }

    public function countRetailerView($productId)
    {
        return $this->redis->lsize('product:' . $productId . ':retailerview');
    }

    public function existsProductByShop($productName, $shopId)
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) as num, product_id FROM product WHERE product_name = :product_name AND shop_id = :shop_id LIMIT 1");
        $stmt->execute(array(
            'product_name' => $productName,
            'shop_id' => $shopId
        ));
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($data['num'] > 0) {
            return $data['product_id'];
        } else {
            return false;
        }
    }

    public function setProductStatusByShopId($shopId, $productStatus)
    {
        $stmt = $this->db->prepare("UPDATE product SET product_status = :product_status WHERE shop_id = :shop_id");
        $stmt->execute(array(
            'product_status' => $productStatus,
            'shop_id' => $shopId
        ));

        $stmt = $this->db->prepare("SELECT product_id FROM product WHERE shop_id = :shop_id");
        $stmt->execute(array(
            'shop_id' => $shopId
        ));
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($data as $product) {
            $this->cache->forget('product_info:' . $product['product_id']);
            $this->redis->hset('product:' . $product['product_id'], 'product_status', $productStatus);
        }
    }

    public function setLastView($productId)
    {
        $this->cache->forget('product_info:' . $productId);
        $this->redis->hset('product:' . $productId, 'lastview', time());
    }

    public function getProductByField($productId, $field)
    {
        return $this->redis->hget('product:' . $productId, $field);
    }

    public function setProductField($productId, $field, $value, $sql = false)
    {
        $this->cache->forget('product_info:' . $productId);
        $this->redis->hset('product:' . $productId, $field, $value);
        if ($sql) {
            $stmt = $this->db->prepare("UPDATE product SET " . $field . " = :value WHERE product_id = :product_id LIMIT 1");
            $stmt->execute(array(
                'value' => $value,
                'product_id' => $productId
            ));
        }
    }

    public function setProductStatus($productId, $status)
    {
        $stmt = $this->db->prepare("UPDATE product SET product_status = :product_status WHERE product_id = :product_id LIMIT 1");
        $stmt->execute(array(
            'product_id' => $productId,
            'product_status' => $status
        ));
        $this->redis->hset('product:' . $productId, 'product_status', $status);
        $this->cache->forget('product_info:' . $productId);
    }

    public function setProductAvailability($productId, $status)
    {
        $stmt = $this->db->prepare("UPDATE product SET product_availability = :product_availability WHERE product_id = :product_id LIMIT 1");
        $exec = $stmt->execute(array(
            'product_id' => $productId,
            'product_availability' => $status
        ));
        $this->redis->hset('product:' . $productId, 'product_availability', $status);
        $this->cache->forget('product_info:' . $productId);
        return $exec;
    }


    public function getProductById($product_id)
    {
//        $stmt = $this->db->prepare("SELECT * FROM product WHERE product_id = :product_id LIMIT 1");
//        $stmt->execute(array(
//            'product_id' => $product_id
//        ));
//        $productInfo = $stmt->fetch(PDO::FETCH_ASSOC);
        /*if ($product_id) {
            $mcKey = 'product_info:' . $product_id;
            $mcContent = $this->cache->get($mcKey);
            if (!$mcContent) {
                $mcContent = $this->redis->hGetAll('product:' . $product_id);
                $this->cache->put($mcKey, $mcContent, 60);
            }
            if ($mcContent) {
                return $mcContent;
            } else {
                return false;
            }
        } else {
            return false;
        }*/
        return self::find($product_id)->toArray();
    }

    public function getRandomProductId($limit)
    {
        $mcKey = 'product:random';
        $mcContent = $this->cache->get($mcKey);
        if ($mcContent) {
            return $mcContent;
        } else {
            $stmt = $this->db->query("SELECT product_id FROM product ORDER BY RAND() LIMIT " . $limit . " ");
            $data = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
            $this->cache->put($mcKey, $data, 1);
            return $data;
        }
    }

    public function getProductByListId($productListId, $offset = 0, $limit = 20, $sort = null, $status = 1, $shop_city = null, $pricefrom = null, $priceto = null, $getIdOnly = true)
    {
        if ($productListId) {
            $productListId = implode(",", $productListId);

            $query = "";


            if ($shop_city != null) {
                $query .= " AND shop_city = " . $shop_city . " ";
            }

            if (StringUtils::notEmpty($pricefrom) && StringUtils::notEmpty($priceto)) {
                $query .= " AND product_price >= " . $pricefrom . " AND product_price <= " . $priceto . " ";
            } else if ($pricefrom) {
                $query .= " AND product_price >= " . $pricefrom . " ";
            }

            if ($getIdOnly) {
                $column = 'product_id';
            } else {
                $column = '*';
            }


            if ($sort == null || $sort == 'new') {
                $stmt = $this->db->query("SELECT " . $column . " FROM product WHERE product_id IN (" . $productListId . ") AND product_status = " . $status . $query . " AND product_price > 0 ORDER BY vip DESC, uptime DESC, product_id DESC LIMIT " . $offset . ", " . $limit . "");
            } else if ($sort == 'pricedesc') {
                $stmt = $this->db->query("SELECT " . $column . " FROM product WHERE product_id IN (" . $productListId . ") AND product_status = " . $status . $query . " AND product_price > 0 ORDER BY product_price DESC LIMIT " . $offset . ", " . $limit . "");
            } else if ($sort == 'priceasc') {
                $stmt = $this->db->query("SELECT " . $column . " FROM product WHERE product_id IN (" . $productListId . ") AND product_status = " . $status . $query . " AND product_price > 0 ORDER BY product_price ASC LIMIT " . $offset . ", " . $limit . "");
            } else if ($sort == 'popular') {
                $stmt = $this->db->query("SELECT " . $column . " FROM product WHERE product_id IN (" . $productListId . ") AND product_status = " . $status . $query . " AND product_price > 0 ORDER BY week_views DESC LIMIT " . $offset . ", " . $limit . "");
            } else {
                $stmt = $this->db->query("SELECT " . $column . " FROM product WHERE product_id IN (" . $productListId . ") AND product_status = " . $status . $query . " AND product_price > 0 ORDER BY vip DESC, uptime DESC, product_id DESC LIMIT " . $offset . ", " . $limit . "");
            }

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            return false;
        }
    }

    public function getProductByCategoryId($categoryId, $offset = 0, $limit = 20, $sort = null, $status = 1, $province_id = null, $exclude_shop = null, $shop_city = null, $pricefrom = null, $priceto = null, $attrBy = null, $attrVal = null, $getIdOnly = null, $filterVip = false)
    {
        //get product by categoryId
        if ($categoryId == 16 || $categoryId == 9) {
            $stmt1 = $this->db->query("SELECT product_id FROM product_category WHERE category_id IN (16, 9)");
        } else {
            $stmt1 = $this->db->query("SELECT product_id FROM product_category WHERE category_id = " . $categoryId);
        }
        $productListId = $stmt1->fetchAll(PDO::FETCH_ASSOC);

        if ($productListId) {
            $array = array();
            foreach ($productListId as $p) {
                array_push($array, $p['product_id']);
            }
            $productListId = implode(",", $array);

            $query = "";

            if ($province_id != null) {
                if ($province_id != 999) {
                    $query = " AND (province_id = " . $province_id . " OR province_id = 999) ";
                } else {
                    $query = " AND province_id = " . $province_id . " ";
                }
            }


            if ($shop_city != null) {
                $query .= " AND shop_city = " . $shop_city . " ";
            }

            if($filterVip) {
                $query .= " AND vip = 1";
            }

            if ($exclude_shop != null) {
                $query .= " AND shop_id != " . $exclude_shop . " ";
            }

            if (StringUtils::notEmpty($pricefrom) && StringUtils::notEmpty($priceto)) {
                $query .= " AND product_price >= " . $pricefrom . " AND product_price <= " . $priceto . " ";
            } else if ($pricefrom) {
                $query .= " AND product_price >= " . $pricefrom . " ";
            }

            if ($attrBy && $attrVal) {
                $attrBy = addslashes($attrBy);
                $attrVal = addslashes($attrVal);
                $query .= " AND (SELECT COUNT(id) FROM product_attribute WHERE product.product_id = product_attribute.product_id AND product_attribute.attribute_value = '" . $attrVal . "' AND product_attribute.attribute = '" . $attrBy . "') > 0";
            }

            if ($getIdOnly) {
                $column = 'product_id';
            } else {
                $column = '*';
            }

            if ($sort == null || $sort == 'new') {
                $stmt = $this->db->query("SELECT " . $column . " FROM product WHERE product_id IN (" . $productListId . ") AND product_status = " . $status . $query . " AND product_price > 0 ORDER BY vip DESC, uptime DESC, product_id DESC LIMIT " . $offset . ", " . $limit . "");
            } else if ($sort == 'pricedesc') {
                $stmt = $this->db->query("SELECT " . $column . " FROM product WHERE product_id IN (" . $productListId . ") AND product_status = " . $status . $query . " AND product_price > 0 ORDER BY product_price DESC LIMIT " . $offset . ", " . $limit . "");
            } else if ($sort == 'priceasc') {
                $stmt = $this->db->query("SELECT " . $column . " FROM product WHERE product_id IN (" . $productListId . ") AND product_status = " . $status . $query . " AND product_price > 0 ORDER BY product_price ASC LIMIT " . $offset . ", " . $limit . "");
            } else if ($sort == 'popular') {
                $stmt = $this->db->query("SELECT " . $column . " FROM product WHERE product_id IN (" . $productListId . ") AND product_status = " . $status . $query . " AND product_price > 0 ORDER BY week_views DESC LIMIT " . $offset . ", " . $limit . "");
            } else {
                $stmt = $this->db->query("SELECT " . $column . " FROM product WHERE product_id IN (" . $productListId . ") AND product_status = " . $status . $query . " AND product_price > 0 ORDER BY vip DESC, uptime DESC, product_id DESC LIMIT " . $offset . ", " . $limit . "");
            }

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            return false;
        }
    }

    public function getProductCategory($productId)
    {
        $stmt = $this->db->prepare("SELECT * FROM product_category WHERE product_id = :product_id");
        $stmt->execute(array(
            'product_id' => $productId
        ));
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteProductCategory($productId)
    {
        $stmt = $this->db->prepare("DELETE FROM product_category WHERE product_id = :product_id");
        $stmt->execute(array(
            'product_id' => $productId
        ));
    }

    public function getProductList($status = 1, $offset = 0, $limit = 20, $sort = null, $getIdOnly = null)
    {
        if ($getIdOnly) {
            $column = 'product_id';
        } else {
            $column = '*';
        }

        if ($limit != null) {
            $limitQuery = "LIMIT $offset, $limit";
        }
        if ($sort == null || $sort == 'new') {
            $stmt = $this->db->query("SELECT " . $column . " FROM product WHERE product_status = " . $status . " AND product_price > 0 ORDER BY vip DESC, uptime DESC, product_id DESC " . $limitQuery);
        } else if ($sort == 'hot') {
            $stmt = $this->db->query("SELECT " . $column . " FROM product WHERE product_status = " . $status . " AND product_price > 0 ORDER BY vip DESC, views DESC " . $limitQuery);
        } else if ($sort == 'priceasc') {
            $stmt = $this->db->query("SELECT " . $column . " FROM product WHERE product_status = " . $status . " AND product_price > 0 ORDER BY vip DESC, product_price ASC " . $limitQuery);
        } else if ($sort == 'pricedesc') {
            $stmt = $this->db->query("SELECT " . $column . " FROM product WHERE product_status = " . $status . " AND product_price > 0 ORDER BY vip DESC, product_price DESC " . $limitQuery);
        }

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getProductListNameLike($like)
    {
        $stmt = $this->db->query("SELECT * FROM product WHERE product_name LIKE '%" . $like . "%'");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addProduct($name, $price, $shop_id, $province, $product_image, $shop_city, $product_status = 1, $product_desc = null, $product_unit = null, $vip = 0, $productStatus, $productWarrantyStatus)
    {

        $stmt = $this->db->prepare("INSERT INTO product (product_name, product_price, shop_id, province_id, product_image, dateline, product_status, shop_city, product_info, product_unit, uptime, vip, product_availability, status_id, warranty_status_id) "
            . "VALUES (:product_name, :product_price, :shop_id, :province_id, :product_image, :dateline, :product_status, :shop_city, :product_info, :product_unit, :uptime, :vip, :product_availability, :status_id, :warranty_status_id)");

        $inputParam = array(
            'product_name' => $name,
            'product_price' => $price,
            'shop_id' => $shop_id,
            'province_id' => $province,
            'product_image' => $product_image,
            'dateline' => time(),
            'shop_city' => $shop_city,
            'product_status' => $product_status,
            'product_info' => $product_desc,
            'product_unit' => $product_unit,
            'uptime' => time(),
            'product_availability' => 1,
            'vip' => $vip,
            'status_id' => $productStatus,
            'warranty_status_id' => $productWarrantyStatus
        );

        $exec = $stmt->execute($inputParam);

        if ($exec) {
            $productId = $this->db->lastInsertId();
            $inputParam['product_id'] = $productId;
            $this->redis->hmset('product:' . $productId, $inputParam);
            $this->addProductPrice($productId, $price);
            return $productId;
        } else {
            return false;
        }
    }

    public function addProductPrice($productId, $price)
    {
        $stmt = $this->db->prepare("INSERT INTO product_price (product_id, product_price, dateline) VALUES (:product_id, :product_price, :dateline)");
        $stmt->execute(array(
            'product_id' => $productId,
            'product_price' => $price,
            'dateline' => time()
        ));
    }

    public function deleteProductImage($imageId)
    {
        //get image file
        $stmt = $this->db->prepare("SELECT * FROM product_image WHERE id = :id");
        $stmt->execute(array(
            'id' => $imageId
        ));
        $imageData = $stmt->fetch(PDO::FETCH_ASSOC);
        @unlink(UPLOAD_FOLDER . 'product_images/' . $imageData['image']);
        @unlink(UPLOAD_FOLDER . 'product_images/400_' . $imageData['image']);
        @unlink(UPLOAD_FOLDER . 'product_images/thumbs/100_' . $imageData['image']);
        $stmt = $this->db->prepare("DELETE FROM product_image WHERE id = :id");
        $stmt->execute(array(
            'id' => $imageId
        ));
    }

    public function editProduct($productId, $name, $price, $productImage, $shopId, $productDesc = null, $product_unit, $contentEdited)
    {

        if(isset($contentEdited)){
            $contentEdited = $contentEdited==1?1:0;
            $stmt = $this->db->prepare("UPDATE product SET product_name = :product_name, product_price = :product_price, product_image = :product_image, product_modify_date = :product_modify_date, shop_id = :shop_id, product_info = :product_info, product_unit = :product_unit, is_content_updated= :is_content_updated WHERE product_id = :product_id");

            $inputParam = array(
                'product_name' => $name,
                'product_price' => $price,
                'product_image' => $productImage,
                'product_modify_date' => time(),
                'product_id' => $productId,
                'shop_id' => $shopId,
                'product_info' => $productDesc,
                'product_unit' => $product_unit,
                'is_content_updated' => $contentEdited
            );
        }else{
            $stmt = $this->db->prepare("UPDATE product SET product_name = :product_name, product_price = :product_price, product_image = :product_image, product_modify_date = :product_modify_date, shop_id = :shop_id, product_info = :product_info, product_unit = :product_unit WHERE product_id = :product_id");

            $inputParam = array(
                'product_name' => $name,
                'product_price' => $price,
                'product_image' => $productImage,
                'product_modify_date' => time(),
                'product_id' => $productId,
                'shop_id' => $shopId,
                'product_info' => $productDesc,
                'product_unit' => $product_unit
            );
        }
        
        $exec = $stmt->execute($inputParam);

        if ($exec) {
            $this->cache->forget('product_info:' . $productId);
            $this->redis->hmset('product:' . $productId, $inputParam);
            return true;
        } else {
            return false;
        }
    }

    public function deleteProduct($productId)
    {
        $stmt = $this->db->prepare("DELETE FROM product WHERE product_id = :product_id LIMIT 1");
        $stmt->execute(array(
            ':product_id' => $productId
        ));
        $this->redis->del('product:' . $productId);
    }

    public function restoreProduct($productId)
    {
        $stmt = $this->db->prepare("UPDATE product SET product_status = 1 WHERE product_id = :product_id LIMIT 1");
        $stmt->execute(array(
            ':product_id' => $productId
        ));
    }

    public function getProductStatus($productId)
    {
        $stmt = $this->db->prepare("SELECT product_status FROM product WHERE product_id = :product_id LIMIT 1");
        $stmt->execute(array(
            'product_id' => $productId
        ));
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        return $product['product_status'];
    }

    public function isOwner($productId, $shopId)
    {
//        $stmt = $this->db->prepare("SELECT shop_id FROM product WHERE product_id = :product_id LIMIT 1");
//        $stmt->execute(array(
//            'product_id' => $productId
//        ));
//        $product = $stmt->fetch();
//        $productShopId = $product['shop_id'];
        $productShopId = $this->redis->hGet('product:' . $productId, 'shop_id');
        if ($shopId == $productShopId) {
            return true;
        } else {
            return false;
        }
    }

    public function countProductByStatus($productStatus = 1)
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) AS total FROM product WHERE product_status = :product_status");
        $stmt->execute(array(
            'product_status' => $productStatus
        ));
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        return $product['total'];
    }

    public function countProductByCategoryId($catId, $productStatus = null, $province_id = null, $shop_city = null, $pricefrom = null, $priceto = null, $attrBy = null, $attrVal = null, $filterVip = false)
    {
        $query = '';
        if ($productStatus == null) {
            if ($province_id == null && $shop_city == null) {
                if ($catId == 16 || $catId == 9) {
                    $stmt = $this->db->query("SELECT product_id FROM product_category WHERE category_id IN (16, 9)");
                    $stmt->execute();
                } else {
                    $stmt = $this->db->prepare("SELECT COUNT(*) AS total FROM product_category WHERE category_id = :category_id");
                    $stmt->execute(array(
                        'category_id' => $catId
                    ));
                }


                $productCategory = $stmt->fetch(PDO::FETCH_ASSOC);
                return $productCategory['total'];
            } else {
                if ($catId == 16 || $catId == 9) {
                    $stmt = $this->db->query("SELECT product_id FROM product_category WHERE category_id IN (16, 9)");
                    $stmt->execute();
                } else {
                    $stmt = $this->db->prepare("SELECT product_id FROM product_category WHERE category_id = :category_id");
                    $stmt->execute(array(
                        'category_id' => $catId
                    ));
                }
                $productListId = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if ($productListId) {
                    $array = array();
                    foreach ($productListId as $p) {
                        array_push($array, $p['product_id']);
                    }
                    $productListId = implode(",", $array);
                    if ($province_id != null) {
                        if ($province_id != 999) {
                            $query = " AND (province_id = " . $province_id . " OR province_id = 999) ";
                        } else {
                            $query = " AND province_id = " . $province_id . " ";
                        }
                    }

                    if ($shop_city != null) {
                        $query .= " AND shop_city = " . $shop_city . " ";
                    }

                    if ($filterVip) {
                        $query .= " AND vip = 1";
                    }

                    if (StringUtils::notEmpty($pricefrom) && StringUtils::notEmpty($priceto)) {
                        $query .= " AND product_price >= " . $pricefrom . " AND product_price <= " . $priceto . " ";
                    } else if ($pricefrom) {
                        $query .= " AND product_price >= " . $pricefrom . " ";
                    }

                    if ($attrBy && $attrVal) {
                        $attrBy = addslashes($attrBy);
                        $attrVal = addslashes($attrVal);
                        $query .= " AND (SELECT COUNT(id) FROM product_attribute WHERE product.product_id = product_attribute.product_id AND product_attribute.attribute_value = '" . $attrVal . "' AND product_attribute.attribute = '" . $attrBy . "') > 0 ";
                    }


                    $stmt = $this->db->query("SELECT COUNT(*) AS total FROM product WHERE product_id IN (" . $productListId . ") " . $query);
                    if ($stmt === false) {
                        return false;
                    } else {
                        $productTotal = $stmt->fetch();
                        return $productTotal['total'];
                    }
                } else {
                    return false;
                }
            }
        } else {
            //get product by categoryId
            if ($catId == 16 || $catId == 9) {
                $stmt1 = $this->db->query("SELECT product_id FROM product_category WHERE category_id IN (16, 9)");
            } else {
                $stmt1 = $this->db->query("SELECT product_id FROM product_category WHERE category_id = " . $catId);
            }
            $productListId = $stmt1->fetchAll(PDO::FETCH_ASSOC);
            if ($productListId) {
                $array = array();
                foreach ($productListId as $p) {
                    array_push($array, $p['product_id']);
                }
                $productListId = implode(",", $array);
                if ($province_id != null) {

                    if ($province_id != 999) {
                        $query = "AND (province_id = " . $province_id . " OR province_id = 999)";
                    } else {
                        $query = "AND province_id = " . $province_id;
                    }

                    if ($shop_city != null) {
                        $query .= " AND shop_city = " . $shop_city . " ";
                    }

                    if ($filterVip) {
                        $query .= " AND vip = 1";
                    }

                    if (StringUtils::notEmpty($pricefrom) && StringUtils::notEmpty($priceto)) {
                        $query .= " AND product_price >= " . $pricefrom . " AND product_price <= " . $priceto . " ";
                    } else if ($pricefrom) {
                        $query .= " AND product_price >= " . $pricefrom . " ";
                    }

                    if ($attrBy && $attrVal) {
                        $attrBy = addslashes($attrBy);
                        $attrVal = addslashes($attrVal);
                        $query .= " AND (SELECT COUNT(id) FROM product_attribute WHERE product.product_id = product_attribute.product_id AND product_attribute.attribute_value = '" . $attrVal . "' AND product_attribute.attribute = '" . $attrBy . "') > 0 ";
                    }

                    $query = "SELECT COUNT(*) AS total FROM product WHERE product_id IN (" . $productListId . ") AND product_status = " . $productStatus . " " . $query;
                } else {
                    $query = '';
                    if ($shop_city != null) {
                        $query = " AND shop_city = " . $shop_city . " ";
                    }

                    if (StringUtils::notEmpty($pricefrom) && StringUtils::notEmpty($priceto)) {
                        $query .= " AND product_price >= " . $pricefrom . " AND product_price <= " . $priceto . " ";
                    } else if ($pricefrom) {
                        $query .= " AND product_price >= " . $pricefrom . " ";
                    }

                    if ($filterVip) {
                        $query .= " AND vip = 1";
                    }

                    if ($attrBy && $attrVal) {
                        $attrBy = addslashes($attrBy);
                        $attrVal = addslashes($attrVal);
                        $query .= " AND (SELECT COUNT(id) FROM product_attribute WHERE product.product_id = product_attribute.product_id AND product_attribute.attribute_value = '" . $attrVal . "' AND product_attribute.attribute = '" . $attrBy . "') > 0 ";
                    }

                    $query = "SELECT COUNT(*) AS total FROM product WHERE product_id IN (" . $productListId . ") AND product_status = " . $productStatus . $query;

                }

                $stmt = $this->db->query($query);
                if ($stmt === false) {
                    return false;
                } else {
                    $productTotal = $stmt->fetch();
                    return $productTotal['total'];
                }
            } else {
                return false;
            }
        }
    }

    public function getProductByCatShop($categoryId, $shopId, $offset = 0, $limit = 10)
    {
        $stmt = $this->db->prepare("SELECT * FROM product WHERE shop_id = :shop_id AND (SELECT id FROM product_category WHERE product_category.product_id = product.product_id AND product_category.category_id = :category_id LIMIT 1) LIMIT :offset, :limit");
        $stmt->bindParam('shop_id', $shopId, PDO::PARAM_INT);
        $stmt->bindParam('offset', $offset, PDO::PARAM_INT);
        $stmt->bindParam('limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam('category_id', $categoryId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countProductByCatShop($categoryId, $shopId)
    {
        $stmt = $this->db->prepare("SELECT COUNT(product.product_id) AS total FROM product WHERE shop_id = :shop_id AND (SELECT id FROM product_category WHERE product_category.product_id = product.product_id AND product_category.category_id = :category_id LIMIT 1)");
        $stmt->bindParam('shop_id', $shopId, PDO::PARAM_INT);
        $stmt->bindParam('category_id', $categoryId, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data['total'];
    }

    public function getProductByShopQuery($shopId, $query, $status = 1, $sort = 'new', $offset = 0, $limit = 20)
    {
        $statusQuery = '';
        if ($status != 'all') {
            $statusQuery = 'AND product_status = :product_status';
        }
        if ($sort == 'new' || $sort == null) {
            $query = "SELECT * FROM product WHERE shop_id = :shop_id " . $statusQuery . " AND product_name LIKE '%" . $query . "%' ORDER BY product_id DESC LIMIT :offset, :limit";
        } else if ($sort == 'hot') {
            $query = "SELECT * FROM product WHERE shop_id = :shop_id " . $statusQuery . " AND product_name LIKE '%" . $query . "%' ORDER BY views DESC LIMIT :offset, :limit";
        } else if ($sort == 'cheap') {
            $query = "SELECT * FROM product WHERE shop_id = :shop_id " . $statusQuery . " AND product_name LIKE '%" . $query . "%' ORDER BY product_price ASC LIMIT :offset, :limit";
        } else {
            $query = "SELECT * FROM product WHERE shop_id = :shop_id " . $statusQuery . " AND product_name LIKE '%" . $query . "%' ORDER BY product_id DESC LIMIT :offset, :limit";
        }

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':shop_id', $shopId, PDO::PARAM_INT);

        if ($status != 'all') {
            $stmt->bindParam(':product_status', $status, PDO::PARAM_INT);
        }
        $stmt->execute();
        $productList = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $productList;
    }

    public function getProductByShop($shopId, $status = 1, $sort = 'new', $offset = 0, $limit = 20)
    {
        $statusQuery = "";
        if ($status != 'all') {
            $statusQuery = 'AND product_status = :product_status';
        }
        if ($sort == 'new' || $sort == null) {
            $query = "SELECT * FROM product WHERE shop_id = :shop_id " . $statusQuery . " ORDER BY uptime DESC LIMIT :offset, :limit";
        } else if ($sort == 'hot') {
            $query = "SELECT * FROM product WHERE shop_id = :shop_id " . $statusQuery . " ORDER BY week_views DESC LIMIT :offset, :limit";
        } else if ($sort == 'cheap') {
            $query = "SELECT * FROM product WHERE shop_id = :shop_id " . $statusQuery . " ORDER BY product_price ASC LIMIT :offset, :limit";
        } else {
            $query = "SELECT * FROM product WHERE shop_id = :shop_id " . $statusQuery . " ORDER BY product_id DESC LIMIT :offset, :limit";
        }

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':shop_id', $shopId, PDO::PARAM_INT);

        if ($status != 'all') {
            $stmt->bindParam(':product_status', $status, PDO::PARAM_INT);
        }
        $stmt->execute();
        $productList = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $productList;
    }

    public function countProductByShopId($shopId, $status = null, $searchQuery = null)
    {
        if ($searchQuery) {
            $query = "SELECT COUNT(*) AS total FROM product WHERE shop_id = " . $shopId . " AND product_name LIKE '%" . $searchQuery . "%'";
            if ($status) {
                $query .= " AND product_status = " . $status . " ";
            }
        } else {
            if (!$status) {
                $query = "SELECT COUNT(*) AS total FROM product WHERE shop_id = " . $shopId . " ";
            } else {
                $query = "SELECT COUNT(*) AS total FROM product WHERE shop_id = " . $shopId . " AND product_status = " . $status . " ";
            }
        }
        $stmt = $this->db->query($query);
        $total = $stmt->fetch(PDO::FETCH_ASSOC);
        return $total['total'];
    }

    public function addProductCategory($productId, $categoryId)
    {
        $stmt = $this->db->prepare("INSERT INTO product_category (product_id, category_id) VALUES (:product_id, :category_id)");
        $stmt->execute(array(
            'product_id' => $productId,
            'category_id' => $categoryId
        ));
    }

    public function addProductProvince($productId, $provinceId)
    {
        $stmt = $this->db->prepare("INSERT INTO product_province (product_id, province_id) VALUES (:product_id, :province_id)");
        $stmt->execute(array(
            'product_id' => $productId,
            'province_id' => $provinceId
        ));
    }

    public function addProductImage($productId, $image, $description)
    {
        $stmt = $this->db->prepare("INSERT INTO product_image (product_id, image, image_info) VALUES (:product_id, :image, :image_info)");
        $stmt->execute(array(
            'product_id' => $productId,
            'image' => $image,
            'image_info' => $description
        ));
        return $this->db->lastInsertId();
    }

    public function getImageByImageId($imageId)
    {
        $stmt = $this->db->prepare("SELECT * FROM product_image WHERE id = :id LIMIT 1");
        $stmt->execute(array(
            'id' => $imageId
        ));
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getImageByProductId($productId)
    {
        $stmt = $this->db->prepare("SELECT * FROM product_image WHERE product_id = :product_id");
        $stmt->execute(array(
            'product_id' => $productId
        ));
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function setFeatureImage($productId, $image)
    {
        $stmt = $this->db->prepare("UPDATE product SET product_image = :product_image WHERE product_id = :product_id");
        $stmt->execute(array(
            'product_image' => $image,
            'product_id' => $productId
        ));
        $this->cache->forget('product_info:' . $productId);
        $this->redis->hset('product:' . $productId, 'product_image', $image);
    }

    public function setImageDescription($imageId, $description)
    {
        $stmt = $this->db->prepare("UPDATE product_image SET image_info = :image_info WHERE id = :id LIMIT 1");
        $stmt->execute(array(
            'id' => $imageId,
            'image_info' => $description
        ));
    }

    public function getFirstProductImage($productId)
    {
        $stmt = $this->db->query("SELECT image FROM product_image WHERE product_id = " . $productId . " ");
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getListProductByShopId($shopId, $status = 1, $offset = 0, $limit = 20, $getIdOnly = null)
    {
        if ($getIdOnly) {
            $column = 'product_id';
        } else {
            $column = '*';
        }
        $stmt = $this->db->prepare("SELECT " . $column . " FROM product WHERE shop_id = :shop_id AND product_status = :product_status LIMIT :offset, :limit");
        $stmt->bindParam('shop_id', $shopId, PDO::PARAM_INT);
        $stmt->bindParam('offset', $offset, PDO::PARAM_INT);
        $stmt->bindParam('limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam('product_status', $status, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Search product
     * @param string $q
     * @param int $offset
     * @param int $limit
     * @param string $sort
     * @return array|boolean
     */
    public function searchProduct($q, $offset, $limit, $sort = null, $province_id = null, $shop_city = null, $pricefrom = null, $priceto = null)
    {
        $qq = urlencode($q);

        if ($sort == 'pricedesc') {
            $sortQuery = '&sort=price%20desc';
        } else if ($sort == 'priceasc') {
            $sortQuery = '&sort=price%20asc';
        } else if ($sort == 'new') {
            $sortQuery = '&sort=id%20desc';
        } else if ($sort == 'popular') {
            $sortQuery = '&sort=views%20desc';
        }

        if ($shop_city != null) {
            $shopCityQuery = urlencode(' AND shop_city:' . $shop_city);
        }

        $priceQuery = '';

        if ($pricefrom && $priceto) {
            $priceQuery = urlencode(' AND price:[' . $pricefrom . ' TO ' . $priceto . ']');
        } else {
            if ($pricefrom) {
                $priceQuery = urlencode(' AND price:[' . $pricefrom . ' TO *]');
            }
            if ($priceto) {
                $priceQuery = urlencode(' AND price:[* TO ' . $priceto . ']');
            }
        }

        $solr_url = 'http://42.117.5.114:8983/solr/thitruongsi/select?q=(product_name:' . $qq . '%20OR%20product_name:"' . $qq . '"^1%20OR%20product_name:"' . $qq . '"^1)' . (isset($shopCityQuery) ? $shopCityQuery : false) . $priceQuery . '&wt=json' . (isset($sortQuery) ? $sortQuery : false) . '&start=' . $offset . '&rows=' . $limit;

        $http = new Http_Common(true);
        $solr_result = $http->get($solr_url);

        if ($solr_result) {
            return json_decode($solr_result);
        } else {
            return false;
        }
    }

    public function initProductPrice()
    {
        $stmt = $this->db->query("SELECT * FROM product");
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $p) {
            $this->addProductPrice($p['product_id'], $p['product_price']);
        }
    }

    public function nextId($id)
    {
        $stmt = $this->db->query("SELECT product_id FROM product WHERE product_id > " . $id . " ORDER BY product_id ASC LIMIT 1");
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        return $product['product_id'];
    }

    public function updateViews($productId, $views, $weekViews)
    {

        //$this->db->query("UPDATE product SET views = " . $views . ", week_views = " . $weekViews . " WHERE product_id = " . $productId . " LIMIT 1");
        $gearman = GMJob::getInstance();
        $workload = array('query' => "UPDATE product SET views = " . $views . ", week_views = " . $weekViews . " WHERE product_id = " . $productId . " LIMIT 1");
        $gearman->doBackground('mysql_query_async', json_encode($workload));

        $this->cache->forget('product_info:' . $productId);
        $this->redis->hset('product:' . $productId, 'views', $views);
        $this->redis->hset('product:' . $productId, 'week_views', $weekViews);
    }


    public function getNewProductByUserId($userId, $start = 0, $end = 15)
    {
        return $this->redis->zRevRange('user:' . $userId . ':newproduct', $start, $end);
        //$list = $this->cache->get('user:' . $userId . ':newproduct');
        //return array_slice($list, $start, $end);
    }

    public function setNewProductByUserId($userId, $productList)
    {
        $this->redis->del('user:' . $userId . ':newproduct');
        foreach ($productList as $p) {
            $this->redis->zadd('user:' . $userId . ':newproduct', $p['uptime'], $p['product_id']);
        }
    }

    public function setHotScore($productId, $score)
    {
        $stmt = $this->db->prepare("UPDATE product SET hot_score = :hot_score WHERE product_id = :product_id LIMIT 1");
        $stmt->execute(
            array(
                'hot_score' => $score,
                'product_id' => $productId
            )
        );
    }

    public function countProductByProvinceId($provinceId)
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM product WHERE province_id = :province_id");
        $stmt->execute(array(
            'province_id' => $provinceId
        ));
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data['total'];
    }

    public function addProductToCategoryProvince($productId, $categoryId, $provinceId)
    {
        $this->redis->lpush('productcategoryprovince:' . $categoryId . ':' . $provinceId, $productId);
        //set exists
        $this->redis->set('productcategoryprovinceexits:' . $categoryId . ':' . $provinceId . ':' . $productId, 1);
    }

    public function getProductFromCategoryProvince($categoryId, $provinceId, $start = 0, $end = -1, $sort = null)
    {
        if ($sort == null) {
            return $this->redis->lrange('productcategoryprovince:' . $categoryId . ':' . $provinceId, $start, $end);
        } else {
            if ($sort == 'popular') {
                return $this->redis->sort('productcategoryprovince:' . $categoryId . ':' . $provinceId, array('by' => 'stat:product:*:view', 'sort' => 'desc', 'limit' => array($start, $end)));
            }
        }
    }

    public function existsProductCategoryProvince($productId, $categoryId, $provinceId)
    {
        return $this->redis->exists('productcategoryprovinceexits:' . $categoryId . ':' . $provinceId . ':' . $productId);
    }

    public function countProductCategoryProvince($categoryId, $provinceId)
    {
        return $this->redis->lsize('productcategoryprovince:' . $categoryId . ':' . $provinceId);
    }

    public function remProductFromCategoryProvince($productId, $categoryId, $provinceId)
    {
        $this->redis->lrem('productcategoryprovince:' . $categoryId . ':' . $provinceId, $productId);
    }

    public function initToHashMap($productId, $productInfo)
    {
        $this->redis->hmset('product:' . $productId, $productInfo);
    }

    public function updateProductShopCity($productId, $shopCityId)
    {
        $stmt = $this->db->prepare("UPDATE product SET shop_city = :shop_city WHERE product_id = :product_id");
        $stmt->execute(array(
            'shop_city' => $shopCityId,
            'product_id' => $productId
        ));
    }

    public function getProductCreateStat($startDate = null, $endDate = null)
    {
        if ($startDate != null) {
            $query = " AND dateline >= UNIX_TIMESTAMP('" . $startDate . "') ";
        }


        if ($endDate != null) {
            $query .= " AND dateline <= UNIX_TIMESTAMP('" . $endDate . "') ";
        }

        $stmt = $this->db->query("SELECT COUNT(*) as total, DATE_FORMAT(DATE(FROM_UNIXTIME(dateline)), '%Y/%m/%d') as date FROM product WHERE 1 " . $query . " GROUP BY DATE(FROM_UNIXTIME(dateline))");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function setProductSimInCat($productId, $productList)
    {
        $this->cache->forever('product_sim:' . $productId, $productList);
//        $this->redis->del('product_sim:' . $productId);
//        $productListCount = count($productList);
//        for ($i = 0; $i < $productListCount; $i++) {
//            $this->redis->rpush('product_sim:' . $productId, $productList[$i]);
//        }
    }

    public function getProductSimInCat($productId)
    {
        return $this->cache->get('product_sim:' . $productId);
//        return $this->redis->lrange('product_sim:' . $productId, 0, -1);
    }

    #### Up To Top ####

    public function up($productId, $time)
    {
        $stmt = $this->db->prepare("UPDATE product SET uptime = :uptime WHERE product_id = :product_id LIMIT 1");
        $exec = $stmt->execute(array(
            'uptime' => $time,
            'product_id' => $productId
        ));
        if ($exec) {
            $this->setProductField($productId, 'uptime', $time);
        }
    }


    #### Attribute ####
    public function addAttribute($productId, $attribute, $attribute_value)
    {
        $stmt = $this->db->prepare("INSERT INTO product_attribute (product_id, attribute, attribute_value) VALUES (:product_id, :attribute, :attribute_value)");
        $attr = array(
            'product_id' => $productId,
            'attribute' => $attribute,
            'attribute_value' => $attribute_value
        );
        $exec = $stmt->execute($attr);
        if ($exec) {
            $attributeId = $this->db->lastInsertId();
            $attr['id'] = $attributeId;
            $this->redis->lpush('product:' . $productId . ':attrlist', $attributeId);
            $this->redis->hmset('product_attribute:' . $attributeId, $attr);
        }
    }

    public function deleteAttribute($attributeId)
    {
        $attr = $this->getAttributeById($attributeId);
        if ($attr) {
            $this->redis->lrem('product:' . $attr['product_id'] . ':attrlist', $attributeId);
            $this->redis->del('product_attribute:' . $attributeId);
        }
    }

    public function deleteAttrByProductId($productId)
    {
        $attrs = $this->getAttributesByProductId($productId);
        for ($i = 0; $i < count($attrs); $i++) {
            $this->deleteAttribute($attrs[$i]);
        }
    }

    public function getAttributesByProductId($productId, $start = 0, $end = -1)
    {
        return $this->redis->lrange('product:' . $productId . ':attrlist', $start, $end);
    }

    public function getAttributeById($attrId)
    {
        return $this->redis->hGetAll('product_attribute:' . $attrId);
    }

    public function attrsReplaceName($input)
    {
        $raw = array('origin', 'style', 'color', 'material', 'size');
        $text = array('Xuất xứ', 'Kiểu dáng', 'Màu sắc', 'Chất liệu', 'Kích thước');
        return str_replace($raw, $text, $input);
    }

    public function addToAutoUp($productList, $shopId, $hour, $minute)
    {
        $queueId = $this->redis->incr('autoup:queueid');
        $queue = array(
            'schedule_time' => strtotime($hour . ':' . $minute),
            'shop_id' => $shopId,
            'queue_id' => $queueId
        );

        $this->redis->hmset('autoup:queue:' . $queueId, $queue);
        //add to queue list
        for ($i = 0; $i < count($productList); $i++) {
            $this->redis->lpush('autoup:queue:' . $queueId . ':list', $productList[$i]);
            $this->redis->lpush('autoup:shop:' . $shopId . ':queue_product', $productList[$i]);
        }
        $this->redis->lpush('autoup:shop:' . $shopId . ':queue', $queueId);
        //add to main queue list
        $this->redis->lpush('autoup:queuelist', $queueId);
    }

    public function getQueueById($queueId)
    {
        return $this->redis->hGetAll('autoup:queue:' . $queueId);
    }

    public function countAutoUpByShop($shopId)
    {
        return $this->redis->lSize('autoup:shop:' . $shopId . ':queue_product');
    }

    public function clearQueue($shopId)
    {
        $listQueue = $this->getQueueListByShop($shopId);
        if ($listQueue) {
            $listQueueCount = count($listQueue);
            for ($i = 0; $i < $listQueueCount; $i++) {
                $this->remQueueList($listQueue[$i], $shopId);
            }
        }
    }

    public function remQueueList($queueId, $shopId)
    {
        $this->redis->lrem('autoup:queuelist', $queueId);
        $this->redis->lrem('autoup:shop:' . $shopId . ':queue', $queueId);
        $productList = $this->getProductFromQueueList($queueId);
        for ($i = 0; $i < count($productList); $i++) {
            $this->redis->lrem('autoup:queue:' . $queueId, $productList[$i]);
            $this->redis->lrem('autoup:shop:' . $shopId . ':queue_product', $productList[$i]);
        }
    }

    public function remProductFromQueue($queueId, $productId, $shopId)
    {
        $this->redis->lrem('autoup:queue:' . $queueId . ':list', $productId);
        $this->redis->lrem('autoup:shop:' . $shopId . ':queue_product', $productId, 1);
    }

    public function getQueueList()
    {
        return $this->redis->lrange('autoup:queuelist', 0, -1);
    }

    public function getProductFromQueueList($queueId)
    {
        return $this->redis->lrange('autoup:queue:' . $queueId . ':list', 0, -1);
    }

    public function getQueueListByShop($shopId)
    {
        return $this->redis->lrange('autoup:shop:' . $shopId . ':queue', 0, -1);
    }

    public function setProductFieldByShop($shopId, $field, $value)
    {
        $stmt = $this->db->query("UPDATE product SET $field = '" . $value . "' WHERE shop_id = " . $shopId);
        $exec = $stmt->execute();

        $shopProduct = $this->getProductByShop($shopId, 'all', 'new', 0, 1000);
        foreach ($shopProduct as $p) {
            $this->setProductField($p['product_id'], $field, $value);
        }
        return $exec;
    }

    public function setproductstatus20($shopId, $upTime)
    {
        $stmt = $this->db->query("UPDATE product SET product_status = 0 WHERE shop_id = " . $shopId . " AND uptime < " . $upTime . " ");
        $stmt->execute();

        $stmt2 = $this->db->query("SELECT product_id FROM product WHERE product_status = 0 AND shop_id = " . $shopId);
        $stmt2->execute();
        foreach ($stmt2->fetchAll(PDO::FETCH_ASSOC) as $p) {
            $this->redis->hset('product:' . $p['product_id'], 'product_status', 0);
            $this->redis->lRem('shop:' . $shopId . ':product:enable', $p['product_id']);
        }
    }

    public function addProductUnitPrice($productId, $minUnit, $maxUnit, $price)
    {
        $stmt = $this->db->prepare("INSERT INTO product_price_unit (product_id, product_min_unit, product_max_unit, product_price) VALUES (:product_id, :product_min_unit, :product_max_unit, :product_price)");

        $exec = $stmt->execute(array(
            'product_id' => $productId,
            'product_min_unit' => $minUnit,
            'product_max_unit' => $maxUnit,
            'product_price' => $price
        ));
        return $exec;
    }

    public function deleteProductUnitPrice($productId)
    {
        $stmt = $this->db->prepare("DELETE FROM product_price_unit WHERE product_id = :product_id");
        $stmt->execute(array(
            'product_id' => $productId
        ));
    }

    public function getProductUnitPriceByProductId($productId)
    {
        $stmt = $this->db->prepare("SELECT * FROM product_price_unit WHERE product_id = :product_id");
        $stmt->execute(array(
            'product_id' => $productId
        ));

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
