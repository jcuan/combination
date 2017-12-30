<?php

namespace App\Models;

use Star\Core\BaseModel;
use Star\Components\Pagination;

class Admin extends BaseModel
{
    /**
     * @param string $password
     * @return bool
     */
    public function checkPassword($password)
    {
        $info=$this->app->db->table('data')->where(['id'=>1])->first();
        if($info && password_verify($password,$info->content)){
            return true;
        }
        return false;
    }

    /**
     * @param $name
     * @param $description
     * @param $image
     */
    public function add($name,$description,$image)
    {
        $insert=[
            'name'=>$name,
            'description'=>$description,
            'image'=>$image,
            'time'=>time()
        ];
        return $this->app->db->table('object')->insertGetId($insert);
    }

    public function update($id,$name,$description,$image=null)
    {
        $update=[
            'name'=>$name,
            'description'=>$description,
        ];
        if($image){
            $update['image']=$image;
        }
        $this->app->db->table('object')->where(['oid'=>$id])->update($update);
        return;
    }

    public function getObjById($id)
    {
        return $this->app->db->table('object')->where(['oid'=>$id])->first();
    }

    public function objList()
    {
        $builder=$this->app->db->table('object')->orderBy('vote','desc');
        $pagination = Pagination::createService(['perPage'=>20]);
        return $pagination->pagenate($builder);
    }

    /**
     * @param $id
     */
    public function delete($id)
    {
        //没有删除对应的图片
        $this->app->db->table('object')->where(['oid'=>$id])->delete();
        $this->app->db->table('vote')->where(['oid'=>$id])->delete();
    }

    public function settings()
    {
        $result=$this->app->db->table('data')->orderBy('id','asc')->get()->toArray();
        $return=[
            'voteName'=>$result[1]->content,
            'rule'=>$result[2]->content
        ];
        return $return;
    }

    public function updateSettings($voteName, $rule)
    {
        $db=$this->app->db;
        $db->table('data')->where(['id'=>2])->update(['content'=>$voteName]);
        $db->table('data')->where(['id'=>3])->update(['content'=>$rule]);
    }

}