## 纸飞机同步登入系统

### 安装 
    1. 下载sso项目，放入/path/to/sso(假设为D:/sso),并进入，git bash执行initialize.sh,
	2. 复制config.inc.sample为 config.inc，针对本机环境进行修改，
	    修改数据库密码，
	    记下有关dbname的东西比如sso是myauth
	    修改define('UC_API', 'http://localhost/uc_server')，改成自己的uc_server url，
	    修改define('UC_DBTABLEPRE', 'ucenter.uc_')，改成自己的数据库名和前缀，默认安装的uc_center即为此，此即为独立安装ucenter的用处
	3. 配置数据库，将sso下的myauth.sql文件导入数据库
		详细步骤为：
		1，创建新数据库 myauth，排序为utf8
		2，进入数据库，导入sso下的myauth.sql
		
#### 然后如果其他项目要使用同步登入，找到项目文件夹下的initialize.sh,执行即可