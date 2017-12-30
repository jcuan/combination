<?php

namespace Star\Components;

use Star\Core\BaseComponent;

/**
 * Class Pagination  分页类
 *
 * 只支持通过get参数分页
 *
 * @package Star\component
 */

class Pagination extends BaseComponent
{

    /**
     * @var string 分页get参数名称
     */
    protected $paramName='page';

    /**
     * @var int 每页数量
     */
    protected $perPage;

    /**
     * @var null|int 最大分页数目，超过本页数不再分类
     */
    protected $maxPage=null;

    /**
     * @var string 前一页名称
     */
    protected $prePage='prePage';

    /**
     * @var string 下一页名称
     */
    protected $nextPage='nextPage';

    /**
     * @var string 跳页链接名称
     */
    protected $goUrl = 'go';

    /**
     * @var string 包含结果数组的名称
     */
    protected $list = 'list';

    /**
     * @var string 总页数名称
     */
    protected $pageSum = 'pageSum';

    /**
     * @var string 当前页
     */
    protected $pageNow = 'page';

    /**
     * @var string 分页数目名字
     */
    protected $perPageName = 'perPage';


    static function requiredConfigAttributes()
    {
        return ['perPage'];
    }

    static function optionalConfigAttributes()
    {
        return ['paramName','maxPage','prePage','nextPage','go','perPageName','page','pageSum','list'];
    }

    /**
     * 分页
     *
     * @param \Illuminate\Database\Query\Builder $builder
     * @param bool $allowClientLimit 是否允许客户端制定分页数目
     * @param int $maxClientLimit 客户端分页数每页最大值
     * @return array
     */
    public function pagenate(\Illuminate\Database\Query\Builder $builder, $allowClientLimit=false, $maxClientLimit=100)
    {
        $request=$this->container['request'];
        $page=$request->query->get('page',1);
        $page=(int)$page;
        $page = $page > 0 ? $page : 1;
        if ($this->maxPage!==null){
            if ($page > $this->maxPage) {
                $page=$this->maxPage;
            }
        }
        //允许客户端自选分页
        $clientPerPage=$request->query->get($this->perPageName);
        if($allowClientLimit && $clientPerPage){
            $clientPerPage=(int)$clientPerPage;
            if($clientPerPage> 0 && $clientPerPage <= $maxClientLimit){
                $this->perPage=$clientPerPage;
            }
        }
        $sum = $builder->count();
        $result = $builder->limit($this->perPage)->offset(($page-1)*$this->perPage)->get();
        $return = $this->doPagination($sum,$page);
        $return[$this->list]=$result->toArray();
        return $return;
    }

    /**
     * 得到分页的其他各项信息
     *
     * @param int $sum 总页数
     * @param int $pageNow 当前页
     * @return array
     */
    private function doPagination($sum, $pageNow)
    {
        $list=[
            $this->nextPage=>null,
            $this->prePage=>null,
            $this->perPageName=>$this->perPage,
        ];
        $list[$this->pageSum]=ceil($sum / $this->perPage);
        $list[$this->pageNow]=$pageNow;
        $url=$_SERVER['REQUEST_URI'];   //此时带get参数
        $qMark = strpos($url, '?');     //问号的位置
        $pageLocation = strpos($url, $this->paramName);   //page这个参数的位置

        if($pageLocation){  //带page参数，直接正则替换数字

            //设置上一页链接
            if ($pageNow > 1) {
                $list[$this->prePage]=preg_replace('/'.$this->paramName.'=\d+/', $this->paramName.'='.($pageNow-1), $url);
            }
            //下一页链接
            if($sum > 0 && $pageNow < $list[$this->pageSum]){
                $list['nextPage']=preg_replace('/'.$this->paramName.'=\d+/', $this->paramName.'='.($pageNow+1), $url);
            }
            //跳转的链接,需要移除page这个get参数
            if(strpos($url, "?{$this->paramName}=")){ //page在最开头
                if(strpos($url, '&')){      //page后边还有参数
                    $list[$this->goUrl]=preg_replace('/'.$this->paramName.'=\d+&/','',$url);
                } else {
                    $list[$this->goUrl]=preg_replace('/\?'.$this->paramName.'=\d+/','',$url);
                }
            } else { //page在中间或者最后
                $list[$this->goUrl]=preg_replace('/&'.$this->paramName.'=\d+$/', '', $url);
            }

        } else {    //不带page参数

            if($qMark){ //带有get参数
                $newUrl=$url.'&';  //加上&
            } else {
                $newUrl=$url.'?';  //加上问号
            }
            if ($pageNow > 1) {
                $list[$this->prePage]=$newUrl.$this->paramName.'='.($pageNow-1);
            }
            //下一页链接
            if($pageNow < $list[$this->pageSum]){
                $list[$this->nextPage]=$newUrl.$this->paramName.'='.($pageNow+1);
            }
            $list[$this->goUrl]=$url;
        }

        return $list;
    }

}