#!/bin/bash

# ============================================
# 开成不公电商溯源系统 - 服务停止脚本
# ============================================

set -e

# 颜色定义
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

# 配置变量
SERVER_PORT="8080"
PID_FILE="/tmp/php74-server.pid"
LOG_FILE="/tmp/php74-server.log"

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

print_separator() {
    echo "============================================"
}

# 停止 PHP 服务
stop_server() {
    print_separator
    echo "🛑 停止 PHP 服务器"
    print_separator
    echo ""

    # 检查是否有 PHP 进程在运行
    if pgrep -f "php.*$SERVER_PORT" > /dev/null; then
        print_info "发现运行中的 PHP 服务..."

        # 尝试优雅停止
        pkill -f "php.*$SERVER_PORT"
        sleep 2

        # 检查是否还在运行
        if pgrep -f "php.*$SERVER_PORT" > /dev/null; then
            print_warning "进程未停止，强制终止..."
            pkill -9 -f "php.*$SERVER_PORT"
            sleep 1
        fi

        print_success "PHP 服务已停止"
    else
        print_info "没有发现运行中的 PHP 服务"
    fi

    # 清理 PID 文件
    if [ -f "$PID_FILE" ]; then
        rm -f "$PID_FILE"
        print_info "已清理 PID 文件"
    fi

    echo ""
    print_separator
}

# 主函数
main() {
    stop_server

    echo ""
    echo "📝 提示："
    echo "  - 日志文件保留在: $LOG_FILE"
    echo "  - 重新启动服务: ./start-server.sh"
    echo ""
}

main
