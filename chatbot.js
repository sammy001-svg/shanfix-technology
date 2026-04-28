/**
 * SHANFIX TECHNOLOGY - AI CHAT BOT
 * Logic for site-wide AI assistant
 */

document.addEventListener('DOMContentLoaded', () => {
    // 1. Inject Chatbot HTML
    const chatbotHTML = `
        <div class="chatbot-container">
            <div class="chatbot-overlay" id="chatbotOverlay">Chat with us! 👋</div>
            <button class="chatbot-toggle" id="chatbotToggle">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                </svg>
            </button>
            <div class="chatbot-window" id="chatbotWindow">
                <div class="chatbot-header">
                    <h3>Shanfix AI Assistant</h3>
                    <button class="chatbot-close" id="chatbotClose">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
                </div>
                <div class="chatbot-messages" id="chatbotMessages">
                    <div class="message message-bot">
                        Hello! Welcome to Shanfix Technology. How can I help you today?
                    </div>
                </div>
                <form class="chatbot-input-area" id="chatbotForm">
                    <input type="text" class="chatbot-input" id="chatbotInput" placeholder="Type a message..." autocomplete="off">
                    <button type="submit" class="chatbot-send">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="22" y1="2" x2="11" y2="13"></line>
                            <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    `;
    document.body.insertAdjacentHTML('beforeend', chatbotHTML);

    // 2. DOM Elements
    const toggle = document.getElementById('chatbotToggle');
    const windowEl = document.getElementById('chatbotWindow');
    const closeBtn = document.getElementById('chatbotClose');
    const form = document.getElementById('chatbotForm');
    const input = document.getElementById('chatbotInput');
    const messagesContainer = document.getElementById('chatbotMessages');
    const overlay = document.getElementById('chatbotOverlay');

    // Notification Sound (Base64 for immediate availability)
    const notificationSound = new Audio('data:audio/wav;base64,UklGRl9vT19XQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YV9vT18AAAAA'); 
    // Note: This is an empty placeholder base64, usually I'd use a small beep. 
    // I will use a standard platform-neutral notification sound approach.
    const playNotification = () => {
        const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
        const oscillator = audioCtx.createOscillator();
        const gainNode = audioCtx.createGain();

        oscillator.type = 'sine';
        oscillator.frequency.setValueAtTime(880, audioCtx.currentTime); // A5
        oscillator.frequency.exponentialRampToValueAtTime(440, audioCtx.currentTime + 0.1);

        gainNode.gain.setValueAtTime(0.1, audioCtx.currentTime);
        gainNode.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.1);

        oscillator.connect(gainNode);
        gainNode.connect(audioCtx.destination);

        oscillator.start();
        oscillator.stop(audioCtx.currentTime + 0.1);
    };

    // 3. Toggle Logic
    toggle.addEventListener('click', () => {
        windowEl.classList.add('active');
        overlay.style.display = 'none'; // Hide overlay when chat opens
        input.focus();
    });

    closeBtn.addEventListener('click', () => {
        windowEl.classList.remove('active');
    });

    // 4. Gemini AI Logic
    const GEMINI_API_KEY = "AIzaSyCm-b7lwcFpqPXs8QlRl9J8Gz5_NjSGcP4"; // Google Gemini API Key
    const API_URL = `https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent?key=${GEMINI_API_KEY}`;

    const SYSTEM_INSTRUCTION = `
        You are an AI Assistant for Shanfix Technology, a technology and business solutions company in Nairobi, Kenya.
        Your goal is to assist clients by providing information about the company's services and contact details.
        
        Company Details:
        - Services: Web Development, System Development (Software Solutions), Bulk SMS, SEO Boost, Networking Solutions, Printing & Branding, Event Management, Web Hosting, Graphic Design, 3D & 2D Signages.
        - Contact Phone: +254 751 869 165
        - Contact Email: info@shanfixtechnology.com
        - Location: Tana House, Karen, Nairobi.
        - Brand: Trusted, innovative, professional, and customer-centric.
        
        Instructions:
        - Be professional, friendly, and helpful.
        - Keep responses concise (under 3 sentences unless asked for more).
        - If a user asks a question outside of company services, politely redirect them back to what Shanfix offers.
        - If someone asks for pricing, explain that it depends on the project scope and suggest they contact us for a custom quote.
    `;

    async function getGeminiResponse(userMessage) {
        if (GEMINI_API_KEY === "YOUR_API_KEY_HERE" || !GEMINI_API_KEY) {
            return "Please configure the Gemini API Key to enable AI responses. Contact support if you need assistance.";
        }

        try {
            // Concatenated prompt for maximum compatibility
            const prompt = `${SYSTEM_INSTRUCTION}\n\nUser Question: ${userMessage}\nAssistant Response: `;

            const payload = {
                contents: [
                    {
                        parts: [
                            { text: prompt }
                        ]
                    }
                ],
                generationConfig: {
                    temperature: 0.5,
                    topP: 0.95,
                    topK: 40,
                    maxOutputTokens: 1024,
                }
            };

            const response = await fetch(API_URL, {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(payload)
            });

            if (!response.ok) {
                const errorData = await response.json();
                console.error("Gemini API HTTP Error:", response.status, errorData);
                return `API Error (${response.status}): ${errorData.error?.message || "Something went wrong"}. Please check your API key or connection.`;
            }

            const data = await response.json();
            if (data.candidates && data.candidates[0].content && data.candidates[0].content.parts) {
                const aiResponse = data.candidates[0].content.parts[0].text.trim();
                console.log("Gemini AI Response:", aiResponse);
                return aiResponse;
            } else {
                console.error("Gemini API Unexpected Response:", data);
                throw new Error("Invalid API response format");
            }
        } catch (error) {
            console.error("Gemini API Connection Error:", error);
            return "I'm having trouble connecting to my brain right now. It could be a network issue or an invalid API key. Please try again later or contact us directly at +254 751 869 165.";
        }
    }

    function addMessage(text, sender) {
        const msgDiv = document.createElement('div');
        msgDiv.className = `message message-${sender}`;
        msgDiv.textContent = text;
        messagesContainer.appendChild(msgDiv);
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
        
        if (sender === 'bot') {
            try {
                playNotification();
            } catch (e) {
                console.warn("Audio play blocked by browser:", e);
            }
        }
    }

    function showTypingIndicator() {
        const indicator = document.createElement('div');
        indicator.className = 'typing-indicator';
        indicator.id = 'typingIndicator';
        indicator.innerHTML = `
            <div class="typing-dot"></div>
            <div class="typing-dot"></div>
            <div class="typing-dot"></div>
        `;
        messagesContainer.appendChild(indicator);
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
        return indicator;
    }

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const text = input.value.trim();
        if (!text) return;

        // User Message
        addMessage(text, 'user');
        input.value = '';

        // Bot Response
        const indicator = showTypingIndicator();
        const response = await getGeminiResponse(text);
        indicator.remove();
        addMessage(response, 'bot');
    });
});
