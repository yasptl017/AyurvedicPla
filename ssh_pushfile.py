#!/usr/bin/env python3
import paramiko
import sys
import io

sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8', errors='replace')

HOST = "31.170.166.119"
PORT = 65002
USER = "u262763368"
PASS = "257038@Yrp"

LOCAL = r"f:\Laravel\New\arya\app\Filament\App\Resources\Patients\Pages\EditPatient.php"
REMOTE = "/home/u262763368/domains/scrapeguru.com/app/Filament/App/Resources/Patients/Pages/EditPatient.php"

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect(HOST, port=PORT, username=USER, password=PASS, timeout=30, allow_agent=False, look_for_keys=False)

sftp = ssh.open_sftp()
sftp.put(LOCAL, REMOTE)
sftp.close()
print("Uploaded EditPatient.php")

# Clear view cache
stdin, stdout, stderr = ssh.exec_command("cd ~/domains/scrapeguru.com && php artisan view:clear 2>&1 && php artisan route:cache 2>&1")
print(stdout.read().decode('utf-8', errors='replace'))

ssh.close()
print("Done.")
