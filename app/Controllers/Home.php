<?php

namespace App\Controllers;

use PHPUnit\Runner\Exception;
use Star\Core\BaseController;
use App\Components\Auth;
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\Models\Home as HomeModel;
use App\Models\Admin as AdminModel;
use Star\Core\Funcs;
use EasyWeChat\Foundation\Application;


class Home extends BaseController
{
    /**
     * @var object 用户信息
     */
    private $user;

    /**
     * @var HomeModel
     */
    private $model;

    public function __construct()
    {
        parent::__construct();
        $auth=Auth::createService(['timeLen'=>3600*24*10]);   //登录之后十天之内有效
        $this->user=$auth->check();
        $this->model = new HomeModel();
        $this->user=new \stdClass();
        $this->user->uid=1;
    }

    public function index()
    {
        $config=$this->app->getConfig('wechat');
        $wechat = new Application($config);

        if(!$this->user){
            $wechat->oauth->redirect();
            return null;
        }

        $this->model->addClick();
        $data=$this->model->getHeaderData();
        $data['title']='投票';
        $data['nav']='index';
        $name=$this->app->request->query->get('keyword',null);
        $objList=$this->model->objList($name);
        $this->app->loadView('header',$data);
        $this->app->loadView('index',$objList);
        $this->app->loadView('footer',['scripts'=>['/static/js/index.js']]);
        return $this->getCache();
    }

    public function login()
    {
        $config=$this->app->getConfig('wechat');
        $wechat = new Application($config);
        try{
            $userInfo=$wechat->oauth->user()->toArray();
        }catch(\Exception $e) {
            Funcs::log('error','wechat login failed ：'.$e->getMessage());
            return '登录失败，轻稍后再试！';
        }

        $id=$this->model->addUser($userInfo);
        if(!$id) {
            Funcs::log('error','wechat insert db failed');
            return '登录失败，请稍后再试！';
        }

        (new RedirectResponse('/'))->send();
        return null;
    }

    public function vote()
    {
        if(!$this->user){
            Funcs::makeAndSendWrongJson(1,'您未登录');
            return;
        }
        $id=$this->app->request->get('id');
        if($id===null){
            Funcs::makeAndSendWrongJson(1,'您未选择候选人');
            return;
        }
        $id=(int)$id;
        $result=$this->model->vote($id,$this->user->uid);
        if($result){
            Funcs::sendJson([],0);
            return;
        }else{
            Funcs::makeAndSendWrongJson(1,'您今天已经投过票了');
            return;
        }
    }

    public function rank()
    {
        $this->model->addClick();
        $data=$this->model->getHeaderData();
        $data['title']='投票';
        $data['nav']='rank';
        $this->app->loadView('header',$data);
        $rank=$this->model->rank();
        $this->app->loadView('rank',$rank);
        $this->app->loadView('footer',['scripts'=>['/static/js/index.js']]);
        return $this->getCache();
    }

    public function rules()
    {
        $this->model->addClick();
        $data=$this->model->getHeaderData();
        $data['title']='投票';
        $data['nav']='rules';
        $this->app->loadView('header',$data);
        $rule=(new AdminModel())->settings()['rule'];
        $this->app->loadView('rules',['rule'=>$rule]);
        $this->app->loadView('footer');
        return $this->getCache();
    }

    public function view($param=['id'=>null])
    {
        if($param['id']===null){
            (new RedirectResponse('/'))->send();
            return;
        }
        $obj=$this->model->getObjById((int)$param['id']);
        if(!$obj){
            (new RedirectResponse('/'))->send();
            return;
        }
        $this->model->addClick();
        $data=$this->model->getHeaderData();
        $data['title']='投票';
        $data['nav']='rules';
        $this->app->loadView('header',$data);
        $this->app->loadView('detail',['obj'=>$obj]);
        $this->app->loadView('footer',['scripts'=>['/static/js/detail.js']]);
        return $this->getCache();
    }

}