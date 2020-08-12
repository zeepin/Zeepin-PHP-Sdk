#!/usr/bin/expect -f

set timeout 30

log_user 0

spawn screen -S zeepin

expect "Press Space for next page; Return to end"

send "\r"

expect "#"

send "cd /home/go/src/github.com/zeepin/Zeepin\r"

expect "#"

send "./Zeepin --testmode\r"

expect "Password"

send "123456\r"

expect "INFO"

send "\x01"; send "d"

