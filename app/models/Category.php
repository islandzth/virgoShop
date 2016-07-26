<?php

/**
 * Description of tag
 *
 * @author NhatTieu
 */
class Category extends BaseModel
{
    protected $table = 'category';
    protected $primaryKey = 'category_id';
    public $timestamps = false;

    protected $guarded = array('category_id','created_at','updated_at');
    protected $fillable = array('category_name', 'name', 'identity');

    

    public function products(){
        return $this->belongsToMany('Product', 'category', 'category_id', 'product_id');
    }
    
    public function validate(){
        $rules = array(
                'category_name'=>'required',
                'identity'=>'required|unique:category,identity,'.$this->id.',category_id'
            );
        $validator = Validator::make($this->toArray(), $rules);
        return $validator;
    }
    public function addCategory($categoryName, $identity, $sex = 0, $description, $parentCategory = null)
    {
        if ($parentCategory != null) {
            $level = $this->getLevel($parentCategory) + 1;
        }
        $category = new Category;
        $category->name = $categoryName;
        $category->identity = $identity;
        $category->sex = $sex;
        $category->discription = $description;
        $category->save();
        return $category->category_id;
    }
    public function getByIdentity($identity){
        return Category::where('identity', '=', $identity)->first();
    }
    



}
