## 环境变量的影响

环境变量 `ENV` 的可选值: `pro` 、 `dev` 

1. 配置文件的载入  
   如果是env，载入的是config下的配置文件; 如果是prod，载入的是config/prod下的配置文件

2. 错误展示  
   `Funcs::showError`在当前环境是`prod`的时候，会隐藏错误信息，只说明出现了错误
   