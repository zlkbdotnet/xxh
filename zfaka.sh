#!/bin/bash                                                                                               
#===================================================================#
#   System Required:  CentOS 7                                 #
#   Description: Install rrshare for CentOS7 #
#   Author: Azure <2894049053@qq.com>                               #
#   github: @baiyutribe <https://github.com/baiyuetribe>                     #
#   Blog:  佰阅部落 https://baiyue.one                           #
#===================================================================#
#
#  .______        ___       __  ____    ____  __    __   _______      ______   .__   __.  _______ 
#  |   _  \      /   \     |  | \   \  /   / |  |  |  | |   ____|    /  __  \  |  \ |  | |   ____|
#  |  |_)  |    /  ^  \    |  |  \   \/   /  |  |  |  | |  |__      |  |  |  | |   \|  | |  |__   
#  |   _  <    /  /_\  \   |  |   \_    _/   |  |  |  | |   __|     |  |  |  | |  . `  | |   __|  
#  |  |_)  |  /  _____  \  |  |     |  |     |  `--'  | |  |____  __|  `--'  | |  |\   | |  |____ 
#  |______/  /__/     \__\ |__|     |__|      \______/  |_______|(__)\______/  |__| \__| |_______|
#
#一键脚本
#
#
# 设置字体颜色函数
function blue(){
    echo -e "\033[34m\033[01m $1 \033[0m"
}
function green(){
    echo -e "\033[32m\033[01m $1 \033[0m"
}
function greenbg(){
    echo -e "\033[43;42m\033[01m $1 \033[0m"
}
function red(){
    echo -e "\033[31m\033[01m $1 \033[0m"
}
function redbg(){
    echo -e "\033[37;41m\033[01m $1 \033[0m"
}
function yellow(){
    echo -e "\033[33m\033[01m $1 \033[0m"
}
function white(){
    echo -e "\033[37m\033[01m $1 \033[0m"
}
#            
# @安装docker
install_docker() {
    docker version > /dev/null || curl -fsSL get.docker.com | bash 
    service docker restart 
    systemctl enable docker  
}
install_docker_compose() {
	curl -L "https://github.com/docker/compose/releases/download/1.23.2/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
	chmod +x /usr/local/bin/docker-compose
}


# 单独检测docker是否安装，否则执行安装docker。
check_docker() {
	if [ -x "$(command -v docker)" ]; then
		echo "docker is installed"
		# command
	else
		echo "Install docker"
		# command
		install_docker
	fi
}
check_docker_compose() {
	if [ -x "$(command -v docker-compose)" ]; then
		echo "docker-compose is installed"
		# command
	else
		echo "Install docker-compose"
		# command
		install_docker_compose
	fi
}


# check docker


# 以上步骤完成基础环境配置。
echo "恭喜，您已完成基础环境安装，可执行安装程序。"

restart_zfaka(){
    cd /opt/zfaka
    docker-compose restart
}



# 输出结果
notice(){
    green "=================================================="
    green "搭建成功，现在您可以直接访问了"
    green "---------------------------"
    green " 首页地址： http://ip:$port"
    green " 后台地址：http://ip:$port/admin"
    green " phpadmin地址： http://ip:8080"
    green "---------------------------"
    white "其他信息"
    white "已配置的端口：$port  数据库root密码：$rootpwd "
    green "=================================================="
    white "开发者：资料空白   Dcocker by 佰阅部落  "
    white "项目地址： https://github.com/zlkbdotnet/zfaka"
}

notice2(){
    green "=================================================="
    green "搭建成功，现在您可以直接访问了"
    green "---------------------------"
    green " 首页地址： http://ip:$port"
    green " 后台地址：http://ip:$port/admin"
    green " phpadmin地址： http://ip:8080"
    green " kodexplore源码编辑器：http://ip:899 "
    green " 源码编辑ZFAKA目录地址:/var/www/zfaka"
    green "---------------------------"
    white "其他信息"
    white "已设置的端口：$port  数据库root密码：$rootpwd"
    white "ZFAKA后台登录账号：$admin_user  默认密码：123456  "
    green "=================================================="
    white "开发者：资料空白   Dcocker by 佰阅部落  "
    white "项目地址： https://github.com/zlkbdotnet/zfaka"
    greenbg "一键脚本说明文档 ：https://baiyue.one/archives/478.html"
}
# 开始安装zfaka
install_main(){
    blue "获取配置文件"
    mkdir -p /opt/zfaka && cd /opt/zfaka
    rm -f docker-compose.yml  
    wget https://github.com/baiyuetribe/zfaka/raw/master/docker-compose.yml      
    blue "配置文件获取成功"
    sleep 3s
    white "请仔细填写参数，部署完毕会反馈已填写信息"
    green "访问端口：如果想通过域名访问，请设置80端口，其余端口可随意设置"
    read -e -p "请输入访问端口(默认端口2020)：" port
    [[ -z "${port}" ]] && port="2020"
    green "设置数据库ROOT密码"
    read -e -p "请输入ROOT密码(默认baiyue.one)：" rootpwd
    [[ -z "${rootpwd}" ]] && rootpwd="baiyue.one"    
    green "请选择安装版本"
    yellow "1.[zfaka1.4.0](稳定版)"
    yellow "2.[zfaka1.3.9](稳定版)"
    yellow "3.[zfaka1.3.8](稳定版)" 
    green "4.[zfaka-dev]（开发版，同步zfaka官网最新git分支）"
    echo
    read -e -p "请输入数字[1~4](默认1)：" vnum
    [[ -z "${vnum}" ]] && vnum="1"       
	if [[ "${vnum}" == "1" ]]; then
        greenbg "开始安装zfaka1.4.0版本"
        sed -i "s/数据库密码/$rootpwd/g" /opt/zfaka/docker-compose.yml
        sed -i "s/版本号/1.4.0/g" /opt/zfaka/docker-compose.yml
        sed -i "s/"访问端口/"$port/g" /opt/zfaka/docker-compose.yml
        greenbg "已完成配置部署"
        greenbg "程序将下载镜像，请耐心等待下载完成"
        cd /opt/zfaka
        docker-compose up -d
	elif [[ "${vnum}" == "2" ]]; then
        greenbg "开始安装zfaka1.3.9版本"
        sed -i "s/数据库密码/$rootpwd/g" /opt/zfaka/docker-compose.yml
        sed -i "s/版本号/1.3.9/g" /opt/zfaka/docker-compose.yml
        sed -i "s/"访问端口/"$port/g" /opt/zfaka/docker-compose.yml
        greenbg "已完成配置部署"
        greenbg "程序将下载镜像，请耐心等待下载完成"
        cd /opt/zfaka
        docker-compose up -d
	elif [[ "${vnum}" == "3" ]]; then
        greenbg "开始安装zfaka1.3.8版本"
        sed -i "s/数据库密码/$rootpwd/g" /opt/zfaka/docker-compose.yml
        sed -i "s/版本号/1.3.8/g" /opt/zfaka/docker-compose.yml
        sed -i "s/"访问端口/"$port/g" /opt/zfaka/docker-compose.yml
        greenbg "已完成配置部署"
        greenbg "程序将下载镜像，请耐心等待下载完成"
        cd /opt/zfaka
        docker-compose up -d
    elif [[ "${vnum}" == "4" ]]; then
        white "此版本实时同步官方开发版"
        green "请设置网站管理员账号"
        read -e -p "请输入网站管理员账号(默认admin)：" admin_user
        [[ -z "${admin_user}" ]] && admin_user="admin"  
        init_step       
        zfaka_master
	fi   
   
}


# 初始化zfaka程序
init_step(){
    rm -rf /opt/zfaka
    mkdir -p /opt/zfaka/
    cd /opt/zfaka
    git clone -b master https://github.com/zlkbdotnet/zfaka.git && mv zfaka/* . && rm -rf zfaka
    git clone -b docker https://github.com/Baiyuetribe/zfaka.git && mv zfaka/* . && rm -rf zfaka
    cp conf/application.ini.new conf/application.ini
    sed -i 's/127.0.0.1/mysql/' application/modules/Install/views/setptwo/index.html
    sed -i "30s/root/$rootpwd/" application/modules/Install/views/setptwo/index.html
    sed -i "s/43036456@qq.com/$admin_user/" application/modules/Install/views/last/index.html
    sed -i "s/43036456@qq.com/$admin_user/s" install/faka.sql
    chmod a+w conf/application.ini
    chmod -R a+w+r install
    chmod -R a+w+r public/res/upload 
    chmod -R a+w+r temp 
    chmod -R a+w log
    greenbg "本地初始化完成"
}

zfaka_master(){
    cd /opt/zfaka
    docker-compose up -d
    greenbg "服务已启动，等待初始化约5s"
    sleep 5s
    notice2
}

# 停止服务
stop_zfaka(){
    cd /opt/zfaka
    docker-compose kill
}

# 重启服务
restart_zfaka(){
    cd /opt/zfaka
    docker-compose restart
}

# 卸载
remove_all(){
    cd /opt/zfaka
    docker-compose down
	echo -e "\033[32m已完成卸载\033[0m"
}



#开始菜单
start_menu(){
    clear
	echo "


  ██████╗  █████╗ ██╗██╗   ██╗██╗   ██╗███████╗    ██████╗ ███╗   ██╗███████╗
  ██╔══██╗██╔══██╗██║╚██╗ ██╔╝██║   ██║██╔════╝   ██╔═══██╗████╗  ██║██╔════╝
  ██████╔╝███████║██║ ╚████╔╝ ██║   ██║█████╗     ██║   ██║██╔██╗ ██║█████╗  
  ██╔══██╗██╔══██║██║  ╚██╔╝  ██║   ██║██╔══╝     ██║   ██║██║╚██╗██║██╔══╝  
  ██████╔╝██║  ██║██║   ██║   ╚██████╔╝███████╗██╗╚██████╔╝██║ ╚████║███████╗
  ╚═════╝ ╚═╝  ╚═╝╚═╝   ╚═╝    ╚═════╝ ╚══════╝╚═╝ ╚═════╝ ╚═╝  ╚═══╝╚══════╝                                                            
    "
    greenbg "==============================================================="
    greenbg "简介：ZFAKA一键安装脚本                                          "
    greenbg "系统：Centos7、Ubuntu等                                         "
    greenbg "作者：Azure                                                     "
    greenbg "开发者：资料空白 Github:zlkbdotnet/zfaka                                            "
    greenbg "网站： https://baiyue.one                                       "
    greenbg "主题：专注分享优质web资源                                        "
    greenbg "Youtube/B站： 佰阅部落                                          "
    greenbg "==============================================================="
    echo
    yellow "使用前提：脚本会自动安装docker，国外服务器搭建只需30s~1min"
    yellow "国内服务器下载镜像稍慢，请耐心等待"
    blue "备注：非80端口可以用caddy反代，自动申请ssl证书，到期自动续期"
    echo
    white "—————————————程序安装——————————————"
    white "1.安装ZFAKA"
    white "—————————————杂项管理——————————————"
    white "2.停止ZFAKA"
    white "3.重启ZFAKA"
    white "4.卸载ZFAKA"
    white "—————————————域名访问——————————————" 
    white "5.Caddy域名反代一键脚本(可以实现非80端口使用域名直接访问)"
    blue "0.退出脚本"
    echo
    echo
    read -p "请输入数字:" num
    case "$num" in
    1)
	check_docker
    check_docker_compose
    install_main
	;;
	2)
    stop_zfaka
    green "ZFAKA程序已停止运行"
	;;
	3)
    restart_zfaka
    green "ZFAKA程序已重启完毕"
	;;
	4)
    remove_all
	;;
	5)
    bash <(curl -L -s https://raw.githubusercontent.com/Baiyuetribe/codes/master/caddy/caddy.sh)
	;;
	0)
	exit 1
	;;
	*)
	clear
	echo "请输入正确数字"
	sleep 5s
	start_menu
	;;
    esac
}

start_menu
