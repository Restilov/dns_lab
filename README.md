# Web Recon Lab — Intentionally Vulnerable Target

A Docker-based, **deliberately vulnerable** web target for teaching web reconnaissance.
It presents a realistic corporate site (a fictional Facebook-style social network, *Feysbook (not fake)*) that
hides **5 chained flags**. Each flag reveals the hint for the next, following the sections of
a live presentation (passive → active → final).

Built on **Alpine + Nginx + PHP-FPM** — a small image with real, dynamic endpoints (not static HTML).

> ⚠️ This system is intentionally insecure and is for training only. Only apply these techniques
> to systems you are authorised to test.

## Files

```
Dockerfile              Alpine + Nginx + PHP-FPM image
docker-compose.yml      optional one-command run
entrypoint.sh           starts php-fpm + nginx
nginx/
  default.conf          vhost; custom HTTP headers (FLAG 1) live here
  www.conf              php-fpm pool (127.0.0.1:9000)
  php-hardening.ini     expose_php = Off
src/                    web root (the site + flags)
SOLUTION.md             presenter only — all 5 flags + exact commands (do NOT share)
README-katilimci.md     participant handout — how to connect, no flags
```

## Run it

### Option A — plain Docker

```bash
docker build -t recon-lab:latest .
docker run -p 8080:80 -p 1337:1337 recon-lab:latest
```

### Option B — docker compose

```bash
docker compose up --build
```

Then open the target at <http://localhost:8080> and the CTF panel at <http://localhost:1337>.

Quick smoke test:

```bash
curl -I http://localhost:8080/          # should show the X-Debug-Token header (FLAG 1)
curl http://localhost:8080/robots.txt   # should list /internal-backup/
```

### Stop / reset

```bash
docker rm -f recon-lab      # or: docker compose down
```

## CTF panel (flag submission)

Alongside the target on port 80, the image serves an **HTB-style CTF panel** on port **1337**:

- Target (the vulnerable site): <http://localhost:8080>
- Panel (submit your flags): <http://localhost:1337>

The panel tracks progress **per browser session** (no accounts), unlocks flags **sequentially**
(flag N+1 opens only after flag N is correct), and shows a progress bar and a final *PWNED* screen.

Flags are **not** stored in plaintext in the panel — only their **SHA-256 hashes** are, so poking at
the panel does not reveal the flag list. The panel and the target are served from **separate document
roots**, so the panel never exposes the target's files and vice-versa.

The image also **bakes in the gobuster wordlist** (dirb `common.txt`) at build time and serves it
at <http://localhost:1337/common.txt>, so participants grab it from the lab itself (offline-friendly)
instead of downloading it from GitHub:

```bash
curl -o common.txt http://localhost:1337/common.txt
gobuster dir -u http://localhost:8080 -w common.txt
```

## Publishing to Docker Hub

So participants can run it with a single `docker run` and no build step.

```bash
# 1. Log in (uses your Docker Hub account)
docker login

# 2. Build and tag with your Docker Hub username
docker build -t <username>/recon-lab:latest .
#   (or retag an existing image)
docker tag recon-lab:latest <username>/recon-lab:latest

# 3. Push
docker push <username>/recon-lab:latest
```

Participants then need only:

```bash
docker run -p 8080:80 <username>/recon-lab:latest
```

> Multi-arch (Apple Silicon + x86) — build once for both:
>
> ```bash
> docker buildx create --use            # first time only
> docker buildx build --platform linux/amd64,linux/arm64 \
>   -t <username>/recon-lab:latest --push .
> ```

## The flags (overview — details in SOLUTION.md)

| # | Section | Technique | Where |
| --- | --- | --- | --- |
| 1 | Passive | HTTP header analysis | response header on `/` |
| 2 | Passive | robots.txt → directory listing | `/internal-backup/` |
| 3 | Active | unlinked page (HTML-comment hint) | `/admin-panel/` |
| 4 | Active | directory brute-force (gobuster) | `/api/` |
| 5 | Final | leaked backup/config file | `/api/config.php.bak` |

**`SOLUTION.md` is for the presenter only** — it contains every flag value and the exact
command for each step. Do not hand it to participants; give them `README-katilimci.md` instead.
