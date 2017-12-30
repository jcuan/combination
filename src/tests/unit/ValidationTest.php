<?php

use Star\Components\Validation;

class ValidationTest extends \Codeception\Test\Unit
{
    /**
     * @var Validation
     */
    private $validation;

    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {
        $this->validation = new Validation();
    }

    protected function _after()
    {
        unset($this->validation);
    }


    /**
     * test wrong pattens 
     * @expectedException \Star\Core\StarException
     *
     */
    public function testWrongPatterns()
    {
        $patterns=[
            [
                'name',
                'xxxx'
            ]
        ];
        $this->validation->setRules($patterns);

    }

    //test required rules
    public function testRequiredRule()
    {
        //使用的GUMP组件只有在第三种情况的时候认为没有传值，会在ajax验证的时候用到
        $array=[
            'one'=>0,
            'two'=>false,
            'three'=>'',
            'four'=>' '
        ];
        $patterns=[
            ['one','one','required'],
            ['two','two','required'],
            ['three','three','required'],
            ['four','four','required'],
        ];
        $this->validation->setRules($patterns);
        $this->validation->run($array);
        $validatedData = $this->validation->run($array);
        $this->assertFalse($validatedData);
        $errors=$this->validation->getErrorsArray();
        $this->assertTrue((bool)$errors['three']);
    }

    
    public function testOnlyOneRule()
    {
        $array=[
            'username'=>'star'
        ];
        $is_valid = Validation::isValid($array, ['username'=>'required|min_len,5']);
        $this->assertEquals($is_valid,'Username长度至少为5');
        $is_valid = Validation::isValid($array, array(
            'username' => 'required|min_len,4',
        ));
        $this->assertTrue($is_valid);
    }

    // test functions
    public function testValidationFunction()
    {
        $defaultCode = $this->validation->defaultErrorCode;
        
        $array=[
            'name'=>'star ',    //filter在rules检查之后运行  
            'password'=>'123456',    //test default errorMsg and errorCode
            'passwordCopy'=>'12345',    //equalsField
            'optionalFilter'=>'xxxx',   //optional 
            'onlyCode'=>'12345',    //costom errorCode only
            'onlyMsg'=>'12345', //costom errorMsg only
            'containsRuleCodeAndMsg'=>'unsure',   //costom errorCode and errorMsg
        ];
        $patterns=[
            [
                'name',
                '姓名',
                'required|max_len,4'
            ],
            [
                'password',
                '密码',
                'required|max_len,5'
            ],
            [
                'passwordCopy',
                '密码副本',
                'required|equalsfield,password'
            ],
            [
                'optionalFilter',
                'optionalFilter',
                'min_len,5'
            ],
            [
                'onlyCode',
                'onlyCode',
                'required|max_len,4',
                [
                    'max_len'=>'1000_'
                ]
            ],
            [
                'onlyMsg',
                'onlyMsg',
                'required|max_len,4',
                [
                    'max_len'=>'长度不得超过{param}'
                ]
            ],
            [
                'containsRuleCodeAndMsg',
                'containsRuleCodeAndMsg',
                'required|contains,no yes',
                [
                    'contains'=>'1000_{param}'
                ]
            ]
             
        ];
        $filters=[
            'username'=>'trim'
        ];
        $this->validation->setRules($patterns);
        $this->validation->setFilters($filters);
        $validatedData = $this->validation->run($array);
        $this->assertFalse($validatedData);
        $errors=$this->validation->getErrorsArray();
        $this->assertCount(7,$errors,'检测到的错误数量不对');

        $this->assertEquals($errors['password']['errorCode'],$defaultCode);
        $this->assertEquals($errors['password']['errorMsg'],'密码长度至多为5');

        $this->assertEquals($errors['passwordCopy']['errorMsg'],'密码副本和密码必须一致');

        $this->assertEquals($errors['optionalFilter']['errorCode'],$defaultCode);

        $this->assertEquals($errors['onlyCode']['errorCode'],1000);
        $this->assertEquals($errors['onlyCode']['errorMsg'],'onlyCode长度至多为4');

        $this->assertEquals($errors['onlyMsg']['errorCode'],$defaultCode);
        $this->assertEquals($errors['onlyMsg']['errorMsg'],'onlyMsg长度至多为4');

        $this->assertEquals($errors['containsRuleCodeAndMsg']['errorCode'],1000);
        $this->assertEquals($errors['containsRuleCodeAndMsg']['errorMsg'],'containsRuleCodeAndMsg只能是下列的值：no, yes');
    }
}