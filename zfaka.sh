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
    docker-compose restart
}

install_zfaka1.4.0(){
    greenbg "开始安装zfaka1.4.0版本"
    sed -i 's/baiyue.one/$rootpwd/g' /opt/zfaka/.env
    sed -i 's/1.4.0/1.4.0/g' /opt/zfaka/.env
    sed -i 's/PORT=80/PORT=$port/g' /opt/zfaka/.env
    greenbg "已完成配置部署"
    greenbg "程序将下载镜像，请耐心等待下载完成"
    docker-compose up -d
}
install_zfaka1.3.9(){
    greenbg "开始安装zfaka1.3.9版本"
    sed -i 's/baiyue.one/$rootpwd/g' /opt/zfaka/.env
    sed -i 's/1.4.0/1.3.9/g' /opt/zfaka/.env
    sed -i 's/PORT=80/PORT=$port/g' /opt/zfaka/.env
    greenbg "已完成配置部署"
    greenbg "程序将下载镜像，请耐心等待下载完成"
    docker-compose up -d
}
install_zfaka1.3.8(){
    greenbg "开始安装zfaka1.3.8版本"
    sed -i 's/baiyue.one/$rootpwd/g' /opt/zfaka/.env
    sed -i 's/1.4.0/1.3.8/g' /opt/zfaka/.env
    sed -i 's/PORT=80/PORT=$port/g' /opt/zfaka/.env
    greenbg "已完成配置部署"
    greenbg "程序将下载镜像，请耐心等待下载完成"
    docker-compose up -d
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
    white "已配置的端口：$port  数据库root密码：$pwd "
    green "=================================================="
    white "开发者：资料空白   Dcocker by 佰阅部落  "
    white "项目地址： https://github.com/zlkbdotnet/zfaka"
}
# 开始安装zfaka
install_main(){
    blue "获取配置文件"
    mkdir -p /opt/zfaka && cd /opt/zfaka  
    wget https://github.com/baiyuetribe/zfaka/raw/master/docker-compose.yml   
    wget https://github.com/baiyuetribe/zfaka/raw/master/.env   
    blue "配置文件获取成功"
    sleep 3s
    white "请仔细填写参数，部署完毕会反馈已填写信息"
    green "访问端口：如果想通过域名访问，请设置80端口，其余端口可随意设置"
    read -p "请输入访问端口：" port
    green "请填写数据库ROOT密码"
    read -p "请输入ROOT密码：" rootpwd
    green "请选择安装版本"
    yellow "1.[zfaka1.4.0](稳定版)"
    yellow "2.[zfaka1.3.9](稳定版)"
    yellow "3.[zfaka1.3.8](稳定版)" 
    yellow "4.[zfaka-dev]（开发版，同步zfaka官网最新git分支）"
    echo
    read -p "请输入数字[1~4]:" vnum
    case "$vnum" in
    1)
    install_zfaka1.4.0
    notice
	;;
    2)
    install_zfaka1.3.9
    notice
	;;
    3)
    install_zfaka1.3.8 
    notice
	;;    
    4)
    yellow "暂未适配，镜像开发中"
	;;
	*)
	clear
	echo "请输入正确数字[1~4]"
	sleep 5s
	install_main
	;;
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
    greenbg "开发者：资料空白                                                 "
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
