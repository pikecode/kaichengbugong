#!/bin/bash

# ============================================
# 开成不公电商溯源系统 - 服务启动脚本
# ============================================
# 功能：
# 1. 环境检查（PHP 7.4、MySQL）
# 2. 端口检查
# 3. 停止旧服务
# 4. 启动 PHP 7.4 服务器
# 5. 验证服务状态
# ============================================

set -e  # 遇到错误立即退出

# 颜色定义
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# 配置变量
PROJECT_DIR="/Users/peakom/worko/kaichengbugong/kcbg"
PHP74_PATH="/opt/homebrew/opt/php@7.4/bin/php"
SERVER_HOST="localhost"
SERVER_PORT="8080"
LOG_FILE="/tmp/php74-server.log"
PID_FILE="/tmp/php74-server.pid"

# 打印带颜色的消息
print_info() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# 打印分隔线
print_separator() {
    echo "============================================"
}

# 检查命令是否存在
command_exists() {
    command -v "$1" >/dev/null 2>&1
}

# 检查 PHP 7.4
check_php74() {
    print_info "检查 PHP 7.4..."

    if [ ! -f "$PHP74_PATH" ]; then
        print_error "PHP 7.4 未安装！"
        print_info "请运行: brew install php@7.4"
        exit 1
    fi

    PHP_VERSION=$($PHP74_PATH -v | head -n 1)
    print_success "PHP 7.4 已安装: $PHP_VERSION"
}

# 检查 MySQL
check_mysql() {
    print_info "检查 MySQL 服务..."

    if ! command_exists mysql; then
        print_error "MySQL 客户端未安装！"
        exit 1
    fi

    if ! pgrep -x mysqld > /dev/null; then
        print_error "MySQL 服务未运行！"
        print_info "请启动 MySQL: brew services start mysql@8.0"
        exit 1
    fi

    print_success "MySQL 服务正在运行"
}

# 检查数据库连接
check_database() {
    print_info "检查数据库连接..."

    # 从 .env 文件读取数据库配置
    if [ -f "$PROJECT_DIR/.env" ]; then
        DB_HOST=$(grep DATABASE_HOSTNAME "$PROJECT_DIR/.env" | cut -d '=' -f2 | tr -d ' ')
        DB_USER=$(grep DATABASE_USERNAME "$PROJECT_DIR/.env" | cut -d '=' -f2 | tr -d ' ')
        DB_PASS=$(grep DATABASE_PASSWORD "$PROJECT_DIR/.env" | cut -d '=' -f2 | tr -d ' ')
        DB_NAME=$(grep DATABASE_DATABASE "$PROJECT_DIR/.env" | cut -d '=' -f2 | tr -d ' ')

        if mysql -h"$DB_HOST" -u"$DB_USER" -p"$DB_PASS" -e "USE $DB_NAME" 2>/dev/null; then
            print_success "数据库连接成功: $DB_NAME"
        else
            print_warning "数据库连接失败，但服务器仍将启动"
            print_info "数据库配置: $DB_USER@$DB_HOST/$DB_NAME"
            print_info "如果 API 无法正常工作，请检查数据库配置"
        fi
    else
        print_warning ".env 文件不存在，跳过数据库连接检查"
    fi
}

# 检查端口占用
check_port() {
    print_info "检查端口 $SERVER_PORT..."

    if lsof -Pi :$SERVER_PORT -sTCP:LISTEN -t >/dev/null 2>&1; then
        print_warning "端口 $SERVER_PORT 已被占用"
        return 1
    else
        print_success "端口 $SERVER_PORT 可用"
        return 0
    fi
}

# 停止旧的 PHP 服务
stop_old_server() {
    print_info "停止旧的 PHP 服务..."

    # 检查是否有 PHP 进程在运行
    if pgrep -f "php.*$SERVER_PORT" > /dev/null; then
        print_warning "发现运行中的 PHP 服务，正在停止..."
        pkill -f "php.*$SERVER_PORT" || true
        sleep 2
        print_success "旧服务已停止"
    else
        print_info "没有发现运行中的 PHP 服务"
    fi

    # 清理 PID 文件
    if [ -f "$PID_FILE" ]; then
        rm -f "$PID_FILE"
    fi
}

# 启动 PHP 7.4 服务器
start_server() {
    print_info "启动 PHP 7.4 服务器..."

    cd "$PROJECT_DIR" || exit 1

    # 启动服务器
    nohup $PHP74_PATH -S $SERVER_HOST:$SERVER_PORT -t public/ public/router.php > "$LOG_FILE" 2>&1 &

    # 保存 PID
    echo $! > "$PID_FILE"

    # 等待服务器启动
    sleep 3

    # 检查进程是否还在运行
    if [ -f "$PID_FILE" ] && kill -0 $(cat "$PID_FILE") 2>/dev/null; then
        print_success "PHP 服务器已启动 (PID: $(cat $PID_FILE))"
        print_info "服务地址: http://$SERVER_HOST:$SERVER_PORT"
        print_info "日志文件: $LOG_FILE"
    else
        print_error "PHP 服务器启动失败！"
        print_info "查看日志: cat $LOG_FILE"
        exit 1
    fi
}

# 验证服务状态
verify_server() {
    print_info "验证服务状态..."

    # 测试 API 接口
    API_URL="http://$SERVER_HOST:$SERVER_PORT/index.php/addons/shop/api.common/init"

    if command_exists curl; then
        RESPONSE=$(curl -s -o /dev/null -w "%{http_code}" "$API_URL" 2>/dev/null || echo "000")

        if [ "$RESPONSE" = "200" ]; then
            print_success "API 接口响应正常 (HTTP $RESPONSE)"

            # 获取 API 响应内容
            API_DATA=$(curl -s "$API_URL" 2>/dev/null | head -c 100)
            if echo "$API_DATA" | grep -q '"code":1'; then
                print_success "API 返回数据正常"
            else
                print_warning "API 返回数据可能异常"
            fi
        else
            print_error "API 接口响应异常 (HTTP $RESPONSE)"
            print_info "请检查日志: cat $LOG_FILE"
        fi
    else
        print_warning "curl 未安装，跳过 API 测试"
    fi
}

# 显示服务信息
show_info() {
    print_separator
    print_success "服务启动成功！"
    print_separator
    echo ""
    echo "📋 服务信息："
    echo "  - PHP 版本: PHP 7.4.33"
    echo "  - 服务地址: http://$SERVER_HOST:$SERVER_PORT"
    echo "  - 项目目录: $PROJECT_DIR"
    echo "  - 日志文件: $LOG_FILE"
    echo "  - PID 文件: $PID_FILE"
    echo ""
    echo "🔗 访问地址："
    echo "  - 后台管理: http://$SERVER_HOST:$SERVER_PORT/xSZaYJEibq.php"
    echo "  - API 接口: http://$SERVER_HOST:$SERVER_PORT/index.php/addons/shop/api.common/init"
    echo ""
    echo "📝 常用命令："
    echo "  - 查看日志: tail -f $LOG_FILE"
    echo "  - 停止服务: pkill -f 'php.*$SERVER_PORT'"
    echo "  - 查看进程: ps aux | grep 'php.*$SERVER_PORT'"
    echo ""
    print_separator
}

# 主函数
main() {
    print_separator
    echo "🚀 开成不公电商溯源系统 - 服务启动"
    print_separator
    echo ""

    # 1. 环境检查
    check_php74
    check_mysql
    check_database

    echo ""

    # 2. 端口检查和停止旧服务
    if ! check_port; then
        stop_old_server
    fi

    echo ""

    # 3. 启动服务
    start_server

    echo ""

    # 4. 验证服务
    verify_server

    echo ""

    # 5. 显示信息
    show_info
}

# 执行主函数
main
