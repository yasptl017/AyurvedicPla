#!/usr/bin/env python3
import paramiko
import sys
import io
import time

sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8', errors='replace')

HOST = "31.170.166.119"
PORT = 65002
USER = "u262763368"
PASS = "257038@Yrp"

BASE_LOCAL  = r"f:\Laravel\New\arya"
BASE_REMOTE = "/home/u262763368/domains/scrapeguru.com"

files = [
    ("database/migrations/2026_03_03_092453_add_history_of_to_patients_table.php",
     "database/migrations/2026_03_03_092453_add_history_of_to_patients_table.php"),
    ("app/Filament/App/Resources/Patients/Schemas/PatientForm.php",
     "app/Filament/App/Resources/Patients/Schemas/PatientForm.php"),
    ("app/Filament/App/Resources/Patients/Resources/PatientHistories/Tables/PatientHistoriesTable.php",
     "app/Filament/App/Resources/Patients/Resources/PatientHistories/Tables/PatientHistoriesTable.php"),
    ("resources/views/filament/app/tables/patient-history-details.blade.php",
     "resources/views/filament/app/tables/patient-history-details.blade.php"),
]

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect(HOST, port=PORT, username=USER, password=PASS, timeout=30, allow_agent=False, look_for_keys=False)
sftp = ssh.open_sftp()

for local_rel, remote_rel in files:
    local  = BASE_LOCAL.replace("\\", "/") + "/" + local_rel
    remote = BASE_REMOTE + "/" + remote_rel
    sftp.put(local.replace("/", "\\") if sys.platform == "win32" else local, remote)
    print(f"Uploaded: {remote_rel}")

sftp.close()

CMD = """
cd ~/domains/scrapeguru.com
php artisan migrate --force --no-interaction 2>&1
php artisan view:clear 2>&1
php artisan config:cache 2>&1
echo DONE
"""
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
    time.sleep(0.3)

ssh.close()
