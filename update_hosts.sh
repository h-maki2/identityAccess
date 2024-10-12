#!/bin/bash

# カスタムドメインとホストIPを指定
CUSTOM_DOMAIN="identity.todoapp.local"
HOSTS_FILE="/etc/hosts"
DOCKER_IP="127.0.0.1"

# /etc/hosts にエントリが存在するか確認
if grep -q "$CUSTOM_DOMAIN" "$HOSTS_FILE"; then
    echo "$CUSTOM_DOMAIN already exists in $HOSTS_FILE"
else
    # エントリがない場合は追加
    echo "Adding $CUSTOM_DOMAIN to $HOSTS_FILE"
    echo "$DOCKER_IP $CUSTOM_DOMAIN" | sudo tee -a "$HOSTS_FILE" > /dev/null
fi