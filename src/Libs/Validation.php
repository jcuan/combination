<?php

namespace Star\Libs;

use Star\Core\StarException;

/**
 * 借助了GUMP类
 * 
 * 带有一套默认的错误码，规则如下（可以通过覆写Validation中的requiredErrorCode改变required规则默认的错误码）：
 * 0:正常
 * 1-9:服务器错误（一般不会使用，但是没准什么时候会使用呢）
 * 10-99:前端程序员的问题（不属于用户的问题，比如ajax请求应该符合特定的格式但是传来的却不是）
 * 100- :用户的问题，比如没有权限，输入字符格式不符合
 */

class Validation extends \GUMP
{

    /**
     * 没有指定错误码时候的默认错误码
     *
     * @var integer
     */
    public $defaultErrorCode=100;  
    
    /**
     * 错误信息错误码和重写错误信息的映射
     *
     * @var array
     */
    protected $errorInfoMapping=[]; 
    

    /**
     * 是否是单次验证
     *
     * @var boolean
     */
    protected $oneValid=false;

    /**
     * 覆写父类的方法
     *
     * @return null|static
     */
    public static function get_instance(){
        if(self::$instance === null)
        {
            self::$instance = new static();
        }
        return self::$instance;
    }

    /**
     * 设置可读的表单域名称
     *
     * @param array
     * @return void
     */
    protected function setNames($array)
    {
        foreach($array as $filed => $readableName){
            self::$fields[$filed]=$readableName;
        }
    }

    /**
     * 设置验证的格式
     * 
     * 示例：
     * 下边name的验证没有包含alpha规则对应的信息，也就是说，验证码和错误信息都是可选的
     * [
     *      [
     *              'name', //要验证的表单域的名称
     *              '姓名',   //一个可读的名称
     *              'required|min_len,10|max_len,11|alpha',   //规则集合
     *              [
     *                  'required'=>'{filed}的信息是必须的',   //require的错误信息是可选的
     *                  'min_len'=>'11_'，   //使用lang/validation.php中的默认的错误信息
     *                  'max_len'=>'11_{filed}的长度不能超过{param}'
     *              ]
     *       ],
     *       ...
     * ]
     *
     * @param array $patterns
     * @return void
     */
    public function setRules($patterns)
    {
        $renameArray=[];
        $gumpPatterns=[];    //传给gump的验证格式

        //设置一个error_handler，防止设置规则的时候有问题
        $errorHandler=function($errorLevel, $errorMessage ){
            //恢复原来的错误处理函数
            restore_error_handler();
            throw new StarException('invalid validation pattern:'.$errorMessage);
        };

        set_error_handler($errorHandler,E_NOTICE);

        foreach($patterns as $pattern){
            $gumpPatterns[$pattern[0]]=$pattern[2];
            $renameArray[$pattern[0]]=$pattern[1];
            if(isset($pattern[3])){ //如果设置了错误信息
                $this->errorInfoMapping[$pattern[0]]=[];
                foreach($pattern[3] as $rule => $errorMsg){
                    $p_array=&$this->errorInfoMapping[$pattern[0]];
                    if( ($_location=strpos($errorMsg,'_')) !== false ){  //说明设置了错误码信息
                        $errorCode=(int)substr($errorMsg,0,$_location);
                        $errorMsg=substr($errorMsg+1,$_location); 
                        if(!$errorMsg){  //如果errorMsg是空的，说明只想改一下错误码
                            $p_array['validate_'.$rule]=['errorCode'=>$errorCode];        
                        }else{
                            $p_array['validate_'.$rule]=['errorCode'=>$errorCode,'errorMsg'=>$errorMsg];
                        }
                    }else{  //说明只设置了错误信息，使用默认的错误码
                        $p_array['validate_'.$rule]=['errorCode'=>$this->defaultErrorCode];
                    }
                }
            }
        }

        $this->setNames($renameArray);
        $this->validation_rules($gumpPatterns);
        restore_error_handler();
    }

    /**
     * 直接验证一个的
     *
     * @param array $data
     * @param array $validators
     * @return boolean|string 有错误的时候直接返回错误信息
     */
    public static function isValid(array $data, array $validators)
    {
        $validation = self::get_instance();
        
        $validation->validation_rules($validators);

        if ($validation->run($data) === false) {
            return $validation->getOneError();
        } else {
            return true;
        }
    }

    /**
     * 设置filters，就是什么trim，htmlspecialchars这种处理函数
     *
     * @param array $filters
     * @return void
     */
    public function setFilters($filters)
    {
        $this->filter_rules($filters);
    }

    /**
     * get_errors_array的同名函数
     *
     * @return array|null
     */
    public function getErrorsArray()
    {
        return $this->get_errors_array();
    }

    /**
     * 获得lang中的错误信息提示文件
     *
     * @return array
     */
    public function get_messages()
    {
        $lang_file = BASE_PATH.DIRECTORY_SEPARATOR.'src/lang'.DIRECTORY_SEPARATOR.'validation.php';
        $messages = require $lang_file;

        if ($validation_methods_errors = self::$validation_methods_errors) {
            $messages = array_merge($messages, $validation_methods_errors);
        }
        return $messages;

    }

    /**
     * 用于单次验证的时候获得错误信息,主要GUMP自带的那一个里边有html标签，这个主要是为了去掉它们
     *
     * @return string
     * @throws \Exception
     */
    protected function getOneError()
    {
        if (empty($this->errors)) {
            return '';
        }

        // Error messages
        $messages = $this->get_messages();

        $e=$this->errors[0];
        $field = ucwords(str_replace($this->fieldCharsToRemove, chr(32), $e['field']));
        $param = $e['param'];

        if (isset($messages[$e['rule']])) {
            if (is_array($param)) {
                $param = implode(', ', $param);
            }
            $message = str_replace('{param}', $param, str_replace('{field}', $field, $messages[$e['rule']]));
        } else {
            throw new \Exception ('Rule "'.$e['rule'].'" does not have an error message');
        }
        return $message;
    }

    /**
     * 返回错误信息数组
     * 
     * 重写小助手：
     * $this->errors长下边这样
     * [
     *      [
     *          'filed'=>'name',
     *          'value'=>'xxxxxxxx',
     *          'rule'=>'validation_min_len',
     *          'param'=>6  //要求那个参数
     *      ],
     *      [
     *          'filed'=>'gender',
     *          'value'=>'x',
     *          'rule'=>'contains',
     *          'param'=>['m','f']  //允许值的列表
     *      ]
     * ]
     * 
     * @override 为了加入对语言的可控和返回信息的格式，重写了这个方法，去掉了一个参数
     *
     * @return array | null (if empty)
     */
    public function get_errors_array($convert_to_string = NULL)
    {

        $resp = array();

        // Error messages
        $messages = $this->get_messages();

        foreach ($this->errors as $e)
        {
            $field = $e['field'];
            $fieldName = self::$fields[$e['field']];
            $param = $e['param'];
            $rule = $e['rule'];

            // self::$fields是重命名的可读的信息

            // If param is a field (i.e. equalsfield validator)
            //'The {field} field does not equal {param} field',
            if ( !is_array($param) && array_key_exists($param, self::$fields)) {
                $param = self::$fields[$e['param']];
            }
            
            // Messages
            if (isset($messages[$rule])) {
                // Show first validation error and don't allow to be overwritten
                if (!isset($resp[$field])) {
                    if (is_array($param)) {
                        $param = implode(', ', $param);
                    }
                    if(isset($this->errorInfoMapping[$field][$rule]['errorMsg'])){ //用户重写了错误信息
                        $message = $this->errorInfoMapping[$field][$rule]['errorMsg'];
                    } else {
                        $message = $messages[$rule];
                    }
                    $message = str_replace('{param}', $param, str_replace('{field}', $fieldName, $message));
                    $resp[$field] = [
                        'field'=>$field,
                        'fieldName'=>$fieldName,
                        'errorCode'=>isset($this->errorInfoMapping[$field][$rule]['errorCode'])?$this->errorInfoMapping[$field][$rule]['errorCode'] : $this->defaultErrorCode,
                        'errorMsg'=>$message
                    ];
                }
            } else {
                throw new \Exception ('Rule "'.$rule.'" does not have an error message');
            }
        }

        return $resp;
    }

}