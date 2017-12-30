<?php

namespace App\Models;

use Star\Core\BaseModel;
use Star\Components\Pagination;


class Home extends BaseModel
{

    public function getHeaderData()
    {
        $db=$this->app->db;
        $return=[];
        $return['voteName']=$db->table('data')->where(['id'=>2])->first()->content;
        $return['voteSum']=$db->table('object')->sum('vote');
        $return['clickSum']=$db->table('click')->sum('click');
        return $return;
    }

    public function addClick()
    {
        $day=date('Ymd');
        $clickInfo=$this->app->db->table('click')->where(['day'=>$day])->first();
        $db=$this->app->db;
        if($clickInfo){
            $db->table('click')->where(['day'=>$day])->update(['click'=>(int)$clickInfo->click+1]);
        }else{
            $db->table('click')->insert(['day'=>$day,'click'=>1]);
        }
    }

    public function objList($name)
    {
        $builder=$this->app->db->table('object')->orderBy('oid','asc');
        if($name!==null){
            $builder->where('name','like',$name);
        }
        $pagination = Pagination::createService(['perPage'=>10]);
        return $pagination->pagenate($builder);
    }


    public function rank()
    {
        $builder=$this->app->db->table('object')->orderBy('vote','desc');
        $pagination = Pagination::createService(['perPage'=>20]);
        return $pagination->pagenate($builder);
    }

    /**
     * @param $oid
     * @param $uid
     * @return bool 今天已经投过票
     */
    public function vote($oid,$uid)
    {
        $db=$this->app->db;
        //检查用户今天是否透过票了
        if($db->table('vote')->where(['dayTime'=>date('Ymd'),'uid'=>$uid])->first()){
            return false;
        }
        $insert=[
            'oid'=>$oid,
            'uid'=>$uid,
            'dayTime'=>date('Ymd')
        ];

        $db->table('vote')->insert($insert);
        $db->table('object')->where(['oid'=>$oid])->increment('vote');
        return true;
    }

    public function getObjById($id)
    {
        return $this->app->db->table('object')->where(['oid'=>$id])->first();
    }

    public function addUser($userInfo){
        $insert=[
            'openid'=>$userInfo['id'],
            'avatar'=>$userInfo['avatar'],
            'nickname'=>$userInfo['nickname'],
            'city'=>$userInfo['original']['city'],
            'province'=>$userInfo['original']['province'],
            'country'=>$userInfo['original']['country'],
            'time'=>time(),
        ];
        return $this->app->db->table('user')->insertGetId($insert);
    }


}