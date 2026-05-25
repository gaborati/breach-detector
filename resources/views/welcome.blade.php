<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Breach Detector</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            background: #0a0a0a;
            color: #00ff41;
            font-family: 'JetBrains Mono', monospace;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .terminal {
            width: 100%;
            max-width: 700px;
            border: 1px solid #1a1a1a;
            border-radius: 8px;
            overflow: hidden;
        }
        .terminal-header {
            background: #111;
            padding: 10px 16px;
            display: flex;
            align-items: center;
            gap: 8px;
            border-bottom: 1px solid #1a1a1a;
        }
        .dot { width: 12px; height: 12px; border-radius: 50%; }
        .dot-red { background: #ff5f57; }
        .dot-yellow { background: #febc2e; }
        .dot-green { background: #28c840; }
        .terminal-title { color: #444; font-size: 12px; margin-left: 8px; }
        .terminal-body { padding: 24px 28px; }
        .prompt { color: #00ff41; font-size: 13px; margin-bottom: 4px; }
        .subtitle { color: #333; font-size: 12px; margin-bottom: 24px; }
        .input-label { color: #00ff41; font-size: 12px; margin-bottom: 8px; }
        .input-row { display: flex; gap: 10px; margin-bottom: 28px; }
        .input-prefix { color: #00ff41; font-size: 13px; align-self: center; }
        .password-input {
            flex: 1;
            background: #0f0f0f;
            border: 1px solid #00ff4133;
            padding: 10px 14px;
            color: #00ff41;
            font-size: 13px;
            font-family: 'JetBrains Mono', monospace;
            border-radius: 4px;
            outline: none;
        }
        .password-input:focus { border-color: #00ff4166; }
        .check-btn {
            background: #00ff4115;
            border: 1px solid #00ff4144;
            color: #00ff41;
            font-size: 12px;
            padding: 10px 18px;
            border-radius: 4px;
            cursor: pointer;
            font-family: 'JetBrains Mono', monospace;
            white-space: nowrap;
        }
        .check-btn:hover { background: #00ff4125; }
        .divider { border-top: 1px solid #1a1a1a; padding-top: 20px; margin-bottom: 20px; }
        .section-title { color: #444; font-size: 11px; margin-bottom: 12px; }
        .result-box {
            border-radius: 4px;
            padding: 14px 16px;
            margin-bottom: 12px;
            display: none;
        }
        .result-box.breached {
            background: #0f0f0f;
            border: 1px solid #ff000033;
            border-left: 3px solid #ff4444;
        }
        .result-box.clean {
            background: #0f0f0f;
            border: 1px solid #00ff4122;
            border-left: 3px solid #00ff41;
        }
        .result-title { font-size: 12px; margin-bottom: 6px; }
        .result-title.red { color: #ff4444; }
        .result-title.green { color: #00ff41; }
        .result-desc { color: #666; font-size: 11px; }
        .result-note { color: #333; font-size: 11px; margin-top: 8px; }
        .stats-grid { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 8px; margin-bottom: 16px; display: none; }
        .stat-box {
            background: #0f0f0f;
            border: 1px solid #1a1a1a;
            padding: 10px 12px;
            border-radius: 4px;
        }
        .stat-label { color: #444; font-size: 10px; margin-bottom: 4px; }
        .stat-value { font-size: 16px; font-weight: bold; }
        .stat-value.red { color: #ff4444; }
        .stat-value.green { color: #28c840; }
        .log-section { border-top: 1px solid #1a1a1a; padding-top: 16px; }
        .log-line { font-size: 11px; color: #333; line-height: 2; }
        .log-time { color: #444; }
        .log-breached { color: #ff4444; }
        .log-clean { color: #28c840; }
        .footer { margin-top: 20px; color: #222; font-size: 10px; }
        .loading { color: #444; font-size: 12px; display: none; margin-bottom: 12px; }
        @keyframes blink { 0%, 100% { opacity: 1; } 50% { opacity: 0; } }
        .cursor { display: inline-block; width: 2px; height: 14px; background: #00ff41; margin-left: 4px; animation: blink 1s infinite; vertical-align: middle; }
    </style>
</head>
<body>
    <div class="terminal">
        <div class="terminal-header">
            <div class="dot dot-red"></div>
            <div class="dot dot-yellow"></div>
            <div class="dot dot-green"></div>
            <span class="terminal-title">breach-detector — terminal</span>
        </div>
        <div class="terminal-body">
            <div class="prompt">$ breach-detector v1.0.0</div>
            <div class="subtitle">Password breach checker — powered by Have I Been Pwned</div>

            <div class="input-label">&gt; Enter password to check:</div>
            <div class="input-row">
                <span class="input-prefix">$</span>
                <input type="password" class="password-input" id="passwordInput" placeholder="type password...">
                <button class="check-btn" onclick="checkPassword()">[ CHECK ]</button>
            </div>

            <div class="divider">
                <div class="section-title">&gt; SCAN RESULT ━━━━━━━━━━━━━━━━━━━━━━━━━━</div>
                <div class="loading" id="loading">&gt; scanning... <span class="cursor"></span></div>

                <div class="result-box breached" id="resultBreached">
                    <div class="result-title red">[!] BREACH DETECTED</div>
                    <div class="result-desc" id="breachDesc"></div>
                    <div class="result-note">k-anonymity check completed — your password never left this server.</div>
                </div>

                <div class="result-box clean" id="resultClean">
                    <div class="result-title green">[✓] NO BREACH FOUND</div>
                    <div class="result-desc">This password was not found in any known data breach.</div>
                    <div class="result-note">k-anonymity check completed — your password never left this server.</div>
                </div>

                <div class="stats-grid" id="statsGrid">
                    <div class="stat-box">
                        <div class="stat-label">BREACH COUNT</div>
                        <div class="stat-value red" id="statCount">0</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-label">RISK LEVEL</div>
                        <div class="stat-value red" id="statRisk">-</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-label">STATUS</div>
                        <div class="stat-value green" id="statStatus">-</div>
                    </div>
                </div>
            </div>

            <div class="log-section">
                <div class="section-title">&gt; RECENT CHECKS ━━━━━━━━━━━━━━━━━━━━━━━━</div>
                <div id="logContainer"></div>
            </div>

            <div class="footer">breach-detector v1.0.0 &nbsp;|&nbsp; powered by HIBP API &nbsp;|&nbsp; k-anonymity enabled</div>
        </div>
    </div>

    <script>
        const log = [];

        async function checkPassword() {
            const password = document.getElementById('passwordInput').value;
            if (!password) return;

            document.getElementById('loading').style.display = 'block';
            document.getElementById('resultBreached').style.display = 'none';
            document.getElementById('resultClean').style.display = 'none';
            document.getElementById('statsGrid').style.display = 'none';

            try {
               const response = await fetch('/api/check-password', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
    },
    body: JSON.stringify({ password })
});

                const data = await response.json();

                document.getElementById('loading').style.display = 'none';
                document.getElementById('statsGrid').style.display = 'grid';

                if (data.breached) {
                    document.getElementById('resultBreached').style.display = 'block';
                    document.getElementById('breachDesc').textContent =
                        `This password has been exposed ${data.count.toLocaleString()} times in known data breaches.`;
                    document.getElementById('statCount').textContent = data.count.toLocaleString();
                    document.getElementById('statRisk').textContent = data.count > 100000 ? 'CRITICAL' : 'HIGH';
                    document.getElementById('statStatus').textContent = 'UNSAFE';
                    document.getElementById('statStatus').className = 'stat-value red';
                    addLog(true, data.count);
                } else {
                    document.getElementById('resultClean').style.display = 'block';
                    document.getElementById('statCount').textContent = '0';
                    document.getElementById('statRisk').textContent = 'NONE';
                    document.getElementById('statStatus').textContent = 'SAFE';
                    document.getElementById('statStatus').className = 'stat-value green';
                    addLog(false, 0);
                }

                document.getElementById('passwordInput').value = '';

            } catch (error) {
                document.getElementById('loading').style.display = 'none';
                console.error(error);
            }
        }

        function addLog(breached, count) {
            const now = new Date().toISOString().replace('T', ' ').substring(0, 19);
            log.unshift({ time: now, breached, count });
            if (log.length > 5) log.pop();
            renderLog();
        }

        function renderLog() {
            const container = document.getElementById('logContainer');
            container.innerHTML = log.map(entry => `
                <div class="log-line">
                    <span class="log-time">${entry.time}</span> &nbsp;
                    <span class="${entry.breached ? 'log-breached' : 'log-clean'}">${entry.breached ? '[BREACHED]' : '[CLEAN]   '}</span> &nbsp;
                    <span style="color:#555">count: ${entry.count.toLocaleString()}</span>
                </div>
            `).join('');
        }

        document.getElementById('passwordInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') checkPassword();
        });
    </script>
</body>
</html>