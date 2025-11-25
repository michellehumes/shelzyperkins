#!/bin/bash
# Auto-deploy ShelzyPerkins theme to GoDaddy

expect << 'EOF'
spawn sftp client_4daf1db90_712419@5h9.454.myftpupload.com
expect {
    "yes/no" { send "yes\r"; exp_continue }
    "password:" { send "XKZFC2gF4e1QTD\r" }
}
expect "sftp>"
send "cd wp-content/themes\r"
expect "sftp>"
send "put ~/Downloads/shelzyperkins-theme.zip\r"
expect "sftp>"
send "exit\r"
expect eof
EOF

echo ""
echo "✅ Theme uploaded! Now:"
echo "1. Go to WordPress Admin → Appearance → Themes"
echo "2. You'll need to unzip the theme via File Manager or install via WordPress"
