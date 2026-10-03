const express = require('express');
const app = express();
app.use(express.urlencoded({ extended: true }));
app.use(express.json());

const BOT_TOKEN = process.env.TG_BOT_TOKEN;
const CHAT_ID = process.env.TG_CHAT_ID;

app.get('/', (req, res) => {
    res.send('<h1>Black Rose Telegram Relay</h1>');
});

app.get('/test', (req, res) => {
    sendTelegram('🧪 Test from Render.com').then(() => {
        res.json({ status: 'sent' });
    });
});

app.post('/user', async (req, res) => {
    const { name, email, ip } = req.body;
    const text = `🔥 <b>NEW BLACK ROSE CLIENT</b>\n\n` +
                 `👤 Name: ${name}\n📧 Email: ${email}\n🌐 IP: ${ip}\n⏰ ${new Date().toISOString()}`;
    await sendTelegram(text);
    res.json({ status: 'sent' });
});

app.post('/login', async (req, res) => {
    const { username, ip } = req.body;
    const text = `🔐 <b>CLIENT LOGIN</b>\n\n` +
                 `👤 User: ${username}\n🌐 IP: ${ip}\n⏰ ${new Date().toISOString()}`;
    await sendTelegram(text);
    res.json({ status: 'sent' });
});

async function sendTelegram(message) {
    const url = `https://api.telegram.org/bot${BOT_TOKEN}/sendMessage`;
    const data = { chat_id: CHAT_ID, text: message, parse_mode: 'HTML' };
    
    const response = await fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams(data)
    });
    return response.json();
}

const port = process.env.PORT || 3000;
app.listen(port, () => console.log(`Server running on port ${port}`));