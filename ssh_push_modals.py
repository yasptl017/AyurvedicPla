#!/usr/bin/env python3
import paramiko, sys, io, time
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8', errors='replace')

HOST, PORT, USER, PASS = "31.170.166.119", 65002, "u262763368", "257038@Yrp"
BASE_LOCAL  = "f:/Laravel/New/arya"
BASE_REMOTE = "/home/u262763368/domains/scrapeguru.com"

files = [
    "resources/views/filament/app/tables/patient-history-details.blade.php",
]

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect(HOST, port=PORT, username=USER, password=PASS, timeout=30, allow_agent=False, look_for_keys=False)
sftp = ssh.open_sftp()
for f in files:
    local = BASE_LOCAL.replace("/", "\\") + "\\" + f.replace("/", "\\")
    remote = BASE_REMOTE + "/" + f
    sftp.put(local, remote)
    print(f"Uploaded: {f}")
sftp.close()

CMD = "cd ~/domains/scrapeguru.com && php artisan view:clear 2>&1 && echo DONE"
channel = ssh.get_transport().open_session()
channel.set_combine_stderr(False)
channel.exec_command(CMD)
while True:
    if channel.recv_ready():
        print(channel.recv(4096).decode('utf-8', errors='replace'), end='', flush=True)
    if channel.exit_status_ready():
        while channel.recv_ready():
            print(channel.recv(4096).decode('utf-8', errors='replace'), end='', flush=True)
        break
    time.sleep(0.2)
ssh.close()
