<?php

namespace App\Controllers;

use Star\Core\BaseController;
use App\Models\Admin as AdminModel;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Star\Libs\Uploader;
use Gregwar\Image\Image;

class Admin extends BaseController
{
    private $user;

    /**
     * @var \App\Models\Admin
     */
    private $model;

    public function __construct()
    {
        parent::__construct();
        $this->model=new AdminModel();
        $this->user=$this->app->session->get('admin');
        if($this->app->request->getRequestUri()!=='/admin/login' && !$this->user){
            (new RedirectResponse('/admin/login'))->send();
            die;
        }
    }

    public function login()
    {
        if($this->user){
            (new RedirectResponse('/admin'))->send();
            return null;
        }
        $password=$this->app->request->get('password');
        if($password){
            if($this->model->checkPassword($password)){
                $this->app->session->set('admin',true);
                (new RedirectResponse('/admin'))->send();
                return null;
            }else{
                $this->app->loadView('admin/login',['error'=>'密码错误']);
            }
        }else{
            $this->app->loadView('admin/login');
        }
        return $this->getCache();
    }

    public function index()
    {
        $this->app->loadView('admin/index');
        return $this->getCache();
    }

    public function delete($param=['id'=>null])
    {
        $this->model->delete($param['id']);
        $url = $this->app->request->headers->get('Referer');
        if(!$url){
            $url='/admin/rank';
        }
        (new RedirectResponse($url))->send();
    }

    public function rank()
    {
        $list=$this->model->objList();
        $this->app->loadView('admin/rank',$list);
        return $this->getCache();
    }

    public function setting()
    {
        $input=$this->app->request->request->all();
        $data=[];
        if(isset($input['voteName'])){
            $this->model->updateSettings($input['voteName'],$input['rule']);
            $data['info']='更新成功';
        }
        $data=array_merge($data,$this->model->settings());
        $this->app->loadView('admin/setting',$data);
    }

    public function edit($param=['id'=>null])
    {
        $input=$this->app->request->request->all();
        $image=null;
        $data=[];
        if(isset($input['name'])){
            if($_FILES['file']['error']==0) { //说明要修改图片
                $imageInfo = $this->uploadFile();
                if ($imageInfo['state'] == 'SUCCESS') {
                    $image = $imageInfo['url'];
                } else {
                    $data['err'] = '图片修改失败：' . $imageInfo['state'];
                }
            }
            $this->model->update($param['id'],$input['name'],$input['description'],$image);
            $data['info']='修改成功！';
        }
        $objInfo=$this->model->getObjById($param['id']);
        $data['obj']=$objInfo;
        $this->app->loadView('admin/edit',$data);
        return $this->getCache();
    }

    public function add()
    {
        $input=$this->app->request->request->all();
        $data=[];
        if(isset($input['name'])){
            //得到上传的图片
            $imageInfo=$this->uploadFile();
            if($imageInfo['state']!=='SUCCESS'){
                $data['error']=$imageInfo['state'];
            }else{
                $data['id']=$this->model->add($input['name'],$input['description'],$imageInfo['url']);
            }
        }
        $this->app->loadView('admin/add',$data);
        return $this->getCache();
    }

    private function uploadFile()
    {
        $config=[
            "pathFormat" => '/uploads/image/{yy}{mm}{dd}/{time}{rand:6}',
            "maxSize" => '4194304', //4m
            "allowFiles" => [".png", ".jpg", ".jpeg", ".gif", ".bmp"]
        ];
        $uploader=new Uploader('file',$config);
        $fileInfo=$uploader->getFileInfoPro();
        if($fileInfo['state']=='SUCCESS' && $fileInfo['size']>1024*100){    //照片大于100k
            $this->compressImage($fileInfo['fullPath']);
        }
        return $fileInfo;
    }

    Private function compressImage($imgPath)
    {
        list($width, $height, $type, $attr) = getimagesize($imgPath);
        $changKuanBi = $height/$width;
        if($changKuanBi > 2){   //说明有可能是长图，根据宽度的比例来确定长度压缩比例
            //限制宽度700
            $percent=700/$width;
            if($percent >= 1){
                $percent=0.9;
            }
            $configWidth=$percent*$width;
            $configHeight=$percent*$height;
        }else{
            //限制像素
            if($width/800 > 1 ){
                $configWidth=800;
                $configHeight=($configWidth/$width)*$height;
            }elseif($height/800 >1){
                $configHeight=800;
                $configWidth=($configHeight/$height)*$width;
            }else{  //像素没有限制住，使用百分比
                $percent=0.9;
                $configWidth=$percent*$width;
                $configHeight=$percent*$height;
            }
        }

        Image::open($imgPath)->resize($configWidth, $configHeight)->save($imgPath);;
    }
}