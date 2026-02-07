#!/bin/bash

################################################################################
# 后端部署脚本 - 日期地址配置功能
# 用途：自动化部署后台管理和API文件到生产服务器
# 作者：AI Assistant
# 日期：2026-02-06
################################################################################

# 颜色输出
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# 服务器配置
SERVER_HOST="8.154.32.2"
SERVER_USER="root"
SERVER_PASSWORD="Pp@123456"
SERVER_PATH="/www/wwwroot/cwls.turuifanxin.cn"

# 数据库配置
DB_USER="cwls"
DB_PASSWORD="ewXHjm77twRm3xL2"
DB_NAME="cwls"

# 本地文件路径
PROJECT_ROOT="$(cd "$(dirname "$0")/.." && pwd)"
BACKEND_EDIT_HTML="$PROJECT_ROOT/kcbg/application/admin/view/shop/goods/edit.html"
API_GOODS_PHP="$PROJECT_ROOT/kcbg/addons/shop/controller/api/Goods.php"
DB_SQL="$PROJECT_ROOT/database_update_date_urls.sql"

# 备份目录（服务器上）
BACKUP_DIR="/backup/$(date +%Y%m%d_%H%M%S)"

################################################################################
# 工具函数
################################################################################

# 打印信息
print_info() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

# 打印成功
print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

# 打印警告
print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

# 打印错误
print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# 检查命令是否存在
check_command() {
    if ! command -v $1 &> /dev/null; then
        print_error "$1 未安装，请先安装"
        exit 1
    fi
}

# SSH执行命令
ssh_exec() {
    sshpass -p "$SERVER_PASSWORD" ssh -o StrictHostKeyChecking=no "$SERVER_USER@$SERVER_HOST" "$1"
}

# SCP上传文件
scp_upload() {
    local local_file="$1"
    local remote_file="$2"
    sshpass -p "$SERVER_PASSWORD" scp -o StrictHostKeyChecking=no "$local_file" "$SERVER_USER@$SERVER_HOST:$remote_file"
}

################################################################################
# 部署步骤
################################################################################

# 步骤1：前置检查
step1_check() {
    print_info "============================================"
    print_info "步骤1：前置检查"
    print_info "============================================"

    # 检查必需命令
    check_command "sshpass"
    check_command "ssh"
    check_command "scp"

    # 检查本地文件是否存在
    if [ ! -f "$BACKEND_EDIT_HTML" ]; then
        print_error "后台文件不存在: $BACKEND_EDIT_HTML"
        exit 1
    fi

    if [ ! -f "$API_GOODS_PHP" ]; then
        print_error "API文件不存在: $API_GOODS_PHP"
        exit 1
    fi

    print_success "前置检查通过"
    echo ""
}

# 步骤2：创建备份目录
step2_backup_dir() {
    print_info "============================================"
    print_info "步骤2：创建备份目录"
    print_info "============================================"

    ssh_exec "mkdir -p $BACKUP_DIR"
    if [ $? -eq 0 ]; then
        print_success "备份目录创建成功: $BACKUP_DIR"
    else
        print_error "备份目录创建失败"
        exit 1
    fi
    echo ""
}

# 步骤3：备份现有文件
step3_backup_files() {
    print_info "============================================"
    print_info "步骤3：备份现有文件"
    print_info "============================================"

    # 备份后台文件
    print_info "备份后台管理文件..."
    ssh_exec "cp $SERVER_PATH/application/admin/view/shop/goods/edit.html $BACKUP_DIR/goods_edit.html.bak"
    if [ $? -eq 0 ]; then
        print_success "后台文件备份成功"
    else
        print_warning "后台文件备份失败（可能文件不存在）"
    fi

    # 备份API文件
    print_info "备份API文件..."
    ssh_exec "cp $SERVER_PATH/addons/shop/controller/api/Goods.php $BACKUP_DIR/Goods.php.bak"
    if [ $? -eq 0 ]; then
        print_success "API文件备份成功"
    else
        print_warning "API文件备份失败（可能文件不存在）"
    fi

    echo ""
}

# 步骤4：部署后台文件
step4_deploy_backend() {
    print_info "============================================"
    print_info "步骤4：部署后台管理文件"
    print_info "============================================"

    print_info "上传后台文件..."
    scp_upload "$BACKEND_EDIT_HTML" "$SERVER_PATH/application/admin/view/shop/goods/edit.html"

    if [ $? -eq 0 ]; then
        print_success "后台文件部署成功"
    else
        print_error "后台文件部署失败"
        exit 1
    fi
    echo ""
}

# 步骤5：部署API文件
step5_deploy_api() {
    print_info "============================================"
    print_info "步骤5：部署API文件"
    print_info "============================================"

    print_info "上传API文件..."
    scp_upload "$API_GOODS_PHP" "$SERVER_PATH/addons/shop/controller/api/Goods.php"

    if [ $? -eq 0 ]; then
        print_success "API文件部署成功"
    else
        print_error "API文件部署失败"
        exit 1
    fi
    echo ""
}

# 步骤6：清理缓存
step6_clear_cache() {
    print_info "============================================"
    print_info "步骤6：清理缓存"
    print_info "============================================"

    print_info "清理runtime缓存..."
    ssh_exec "cd $SERVER_PATH/runtime/ && rm -rf cache/* temp/*"

    if [ $? -eq 0 ]; then
        print_success "缓存清理成功"
    else
        print_error "缓存清理失败"
        exit 1
    fi
    echo ""
}

# 步骤7：数据库迁移（可选）
step7_database_migration() {
    print_info "============================================"
    print_info "步骤7：数据库迁移（可选）"
    print_info "============================================"

    read -p "是否执行数据库迁移（添加 date_urls 字段）？[y/N]: " -n 1 -r
    echo ""

    if [[ $REPLY =~ ^[Yy]$ ]]; then
        print_info "执行数据库迁移..."

        # 检查字段是否已存在
        FIELD_EXISTS=$(ssh_exec "mysql -u$DB_USER -p$DB_PASSWORD $DB_NAME -e \"SHOW COLUMNS FROM fa_shop_goods LIKE 'date_urls';\" 2>&1 | grep -c 'date_urls'")

        if [ "$FIELD_EXISTS" -gt 0 ]; then
            print_warning "date_urls 字段已存在，跳过迁移"
        else
            ssh_exec "mysql -u$DB_USER -p$DB_PASSWORD $DB_NAME -e \"ALTER TABLE fa_shop_goods ADD COLUMN \\\`date_urls\\\` TEXT COMMENT '日期地址映射JSON' AFTER \\\`traceability_url\\\`;\""

            if [ $? -eq 0 ]; then
                print_success "数据库迁移成功"

                # 验证字段
                ssh_exec "mysql -u$DB_USER -p$DB_PASSWORD $DB_NAME -e \"SHOW COLUMNS FROM fa_shop_goods LIKE 'date_urls';\""
            else
                print_error "数据库迁移失败"
                exit 1
            fi
        fi
    else
        print_info "跳过数据库迁移"
    fi
    echo ""
}

# 步骤8：验证部署
step8_verify() {
    print_info "============================================"
    print_info "步骤8：验证部署"
    print_info "============================================"

    # 检查文件是否存在
    print_info "验证文件..."

    BACKEND_EXISTS=$(ssh_exec "[ -f $SERVER_PATH/application/admin/view/shop/goods/edit.html ] && echo 'yes' || echo 'no'")
    API_EXISTS=$(ssh_exec "[ -f $SERVER_PATH/addons/shop/controller/api/Goods.php ] && echo 'yes' || echo 'no'")

    if [ "$BACKEND_EXISTS" = "yes" ]; then
        print_success "后台文件存在"
    else
        print_error "后台文件不存在"
    fi

    if [ "$API_EXISTS" = "yes" ]; then
        print_success "API文件存在"
    else
        print_error "API文件不存在"
    fi

    echo ""
}

################################################################################
# 回滚功能
################################################################################

rollback() {
    print_info "============================================"
    print_info "回滚到备份"
    print_info "============================================"

    if [ -z "$1" ]; then
        print_error "请指定备份目录，例如: ./deploy-backend.sh rollback /backup/20260206_123456"
        exit 1
    fi

    ROLLBACK_DIR="$1"

    print_info "从备份恢复: $ROLLBACK_DIR"

    # 恢复后台文件
    print_info "恢复后台文件..."
    ssh_exec "cp $ROLLBACK_DIR/goods_edit.html.bak $SERVER_PATH/application/admin/view/shop/goods/edit.html"
    if [ $? -eq 0 ]; then
        print_success "后台文件恢复成功"
    else
        print_error "后台文件恢复失败"
    fi

    # 恢复API文件
    print_info "恢复API文件..."
    ssh_exec "cp $ROLLBACK_DIR/Goods.php.bak $SERVER_PATH/addons/shop/controller/api/Goods.php"
    if [ $? -eq 0 ]; then
        print_success "API文件恢复成功"
    else
        print_error "API文件恢复失败"
    fi

    # 清理缓存
    print_info "清理缓存..."
    ssh_exec "cd $SERVER_PATH/runtime/ && rm -rf cache/* temp/*"
    print_success "缓存清理成功"

    print_success "回滚完成"
}

################################################################################
# 主流程
################################################################################

main() {
    clear

    print_info "╔════════════════════════════════════════════╗"
    print_info "║     后端部署脚本 - 日期地址配置功能      ║"
    print_info "╚════════════════════════════════════════════╝"
    echo ""

    # 执行部署步骤
    step1_check
    step2_backup_dir
    step3_backup_files
    step4_deploy_backend
    step5_deploy_api
    step6_clear_cache
    step7_database_migration
    step8_verify

    # 部署完成
    print_info "============================================"
    print_success "✅ 部署完成！"
    print_info "============================================"
    echo ""
    print_info "备份位置: $BACKUP_DIR"
    print_info "如需回滚，请执行: $0 rollback $BACKUP_DIR"
    echo ""
    print_info "下一步："
    print_info "1. 登录后台测试配置功能"
    print_info "2. 编译并上传小程序"
    print_info "3. 测试完整流程"
    echo ""
}

################################################################################
# 脚本入口
################################################################################

# 检查参数
if [ "$1" = "rollback" ]; then
    rollback "$2"
elif [ "$1" = "help" ] || [ "$1" = "-h" ] || [ "$1" = "--help" ]; then
    echo "用法："
    echo "  $0              # 执行部署"
    echo "  $0 rollback <备份目录>  # 回滚到指定备份"
    echo "  $0 help         # 显示帮助信息"
    echo ""
    echo "示例："
    echo "  $0"
    echo "  $0 rollback /backup/20260206_123456"
else
    main
fi
