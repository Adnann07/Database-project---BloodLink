import { useState, useRef, useEffect } from 'react'
import Navbar from '../components/Navbar'

// Gemini API key from Vite env
const GEMINI_API_KEY = import.meta.env.VITE_GEMINI_API_KEY
const GEMINI_URL = `https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash-lite:generateContent?key=${GEMINI_API_KEY}`


const SYSTEM_PROMPT = `You are BloodLink AI — a friendly and knowledgeable blood donation assistant. 
Your role is to help users understand blood donation, eligibility criteria, blood types, donation processes, and after-care advice.
You can respond in both English and Bangla depending on the user's language.
Keep your answers concise, warm, and encouraging. Always promote safe and voluntary blood donation.`

async function callGeminiDirect(userMessage) {
  const response = await fetch(GEMINI_URL, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      contents: [
        {
          parts: [
            { text: SYSTEM_PROMPT + '\n\nUser: ' + userMessage }
          ]
        }
      ],
      generationConfig: {
        temperature: 0.7,
        maxOutputTokens: 512,
      }
    })
  })

  if (!response.ok) {
    const errBody = await response.json().catch(() => ({}))
    throw new Error(errBody?.error?.message || `HTTP ${response.status}`)
  }

  const data = await response.json()
  const text = data?.candidates?.[0]?.content?.parts?.[0]?.text
  if (!text) throw new Error('Empty response from Gemini.')
  return text
}

async function callLaravelBackend(userMessage) {
  const res = await fetch('http://localhost:8000/api/chat', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
    body: JSON.stringify({ message: userMessage })
  })
  const data = await res.json()
  if (data.text) return data.text
  throw new Error(data.error || 'Server error')
}

const CHIPS = [
  'Eligibility criteria',
  'Blood types',
  'Donation process',
  'After donation care',
  'Find blood drives',
]

function AskAI() {
  const [messages, setMessages] = useState([
    {
      role: 'ai',
      text: 'হ্যালো! রক্তদান সম্পর্কে যেকোনো প্রশ্ন করুন।\n\nHello! Ask me anything about blood donation.',
    },
  ])
  const [input, setInput] = useState('')
  const [loading, setLoading] = useState(false)
  const bottomRef = useRef(null)

  useEffect(() => {
    bottomRef.current?.scrollIntoView({ behavior: 'smooth' })
  }, [messages])

  async function askAI(userMessage) {
    setLoading(true)
    try {
      let text

      if (GEMINI_API_KEY) {
        // Preferred: call Gemini directly from the browser
        text = await callGeminiDirect(userMessage)
      } else {
        // Fallback: route through Laravel backend
        text = await callLaravelBackend(userMessage)
      }

      setMessages(prev => [...prev, { role: 'ai', text }])
    } catch (err) {
      console.error('AI Error:', err)
      setMessages(prev => [
        ...prev,
        {
          role: 'ai',
          text: `⚠️ Could not get a response.\n\n${err.message || 'Unknown error. Please try again.'}`,
        },
      ])
    }
    setLoading(false)
  }

  function sendMessage() {
    const text = input.trim()
    if (!text || loading) return
    setInput('')
    setMessages(prev => [...prev, { role: 'user', text }])
    askAI(text)
  }

  function sendChip(text) {
    if (loading) return
    setMessages(prev => [...prev, { role: 'user', text }])
    askAI(text)
  }

  function handleKey(e) {
    if (e.key === 'Enter') sendMessage()
  }

  return (
    <div className="askai-body">
      <Navbar />
      <div className="page">
        <div className="page-header">
          <h1>QnA about Blood Donation</h1>
          <p>Blood related awareness and QnA answering AI assistance</p>
        </div>

        <div className="chat-container">
          <div className="chat-header">
            <span className="chat-avatar">🩸</span>
            <div>
              <h3>Blood Donation Assistant</h3>
              <p>Powered by Gemini AI — Available 24/7</p>
            </div>
            <div className="chat-status">
              <div className="status-dot"></div>
              Online
            </div>
          </div>

          <div className="chat-messages">
            {messages.map((msg, i) =>
              msg.role === 'ai' ? (
                <div key={i} className="msg-ai">
                  <span className="msg-avatar">🩸</span>
                  <div className="msg-bubble" style={{ whiteSpace: 'pre-line' }}>
                    {msg.text}
                  </div>
                </div>
              ) : (
                <div key={i} className="msg-user">
                  <div className="msg-user-bubble">{msg.text}</div>
                </div>
              )
            )}
            {loading && (
              <div className="msg-ai">
                <span className="msg-avatar">🩸</span>
                <div className="msg-bubble typing-indicator">
                  <span></span><span></span><span></span>
                </div>
              </div>
            )}
            <div ref={bottomRef} />
          </div>

          <div className="chips-area">
            {CHIPS.map(chip => (
              <button key={chip} className="chip" onClick={() => sendChip(chip)}>
                {chip}
              </button>
            ))}
          </div>

          <div className="chat-input-area">
            <input
              type="text"
              className="chat-input"
              placeholder="Type your question here..."
              value={input}
              onChange={e => setInput(e.target.value)}
              onKeyDown={handleKey}
              disabled={loading}
            />
            <button className="send-btn" onClick={sendMessage} disabled={loading}>
              ➤
            </button>
          </div>
        </div>
      </div>
    </div>
  )
}

export default AskAI
