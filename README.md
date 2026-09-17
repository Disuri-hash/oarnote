# OarNote — Minimal Rowing Sessions (MVP)

This is a minimal implementation of OarNote: a tiny PHP + MySQL app for recording rowing sessions, intended as a learning project for Docker and Ansible.

Quick run (local with Docker Compose):

```bash
docker compose up -d --build
# open http://localhost:8080
```

If you already have an OarNote database volume from the earlier MVP, run the one-time schema migration before opening the new session builder:

PowerShell:

```powershell
cmd /c "docker compose exec -T db mysql -uoaruser -poarpw oarnote < sql\migrate-session-programming.sql"
```

Bash, zsh, or Command Prompt:

```bash
docker compose exec -T db mysql -uoaruser -poarpw oarnote < sql/migrate-session-programming.sql
```

What is included:
- `app/` — simple PHP app (`index.php`, `db.php`)
- `Dockerfile` — PHP/Apache image
- `docker-compose.yml` — runs `web` and `db`
- `ansible/` — `inventory.ini` and `playbook.yml` to provision an Ubuntu host and deploy the app

Session programming now includes five erg formats, flexible ordered water sheds, rower submissions, coach review, and overall coach feedback.

After applying the main session-programming migration, apply the current MVP additions (status, purpose, boat class, RPE/attendance, and interval results):

```powershell
cmd /c "docker compose exec -T db mysql -uoaruser -poarpw oarnote < sql\migrate-mvp-improvements.sql"
```

Next steps:
- Test locally via Docker Compose
- Launch an Ubuntu EC2 instance (Ubuntu 22.04 recommended)
- Edit `ansible/inventory.ini` with your EC2 IP and key
- Run `ansible-playbook -i ansible/inventory.ini ansible/playbook.yml`
