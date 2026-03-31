import { useState } from 'react'
import { useNavigate } from 'react-router-dom'
import axios from 'axios'
import Navbar from '../components/Navbar'

const API_URL = 'http://localhost:8000/api'

const api = axios.create({
  baseURL: API_URL,
  withCredentials: true,
  headers: {
    'Accept': 'application/json',
    'Content-Type': 'application/json'
  }
})

function EmailVerification() {
  const navigate = useNavigate()
  const [email, setEmail] = useState('')
  const [otp, setOtp] = useState('')
  const [error, setError] = useState('')
  const [success, setSuccess] = useState('')
  const [loading, setLoading] = useState(false)

  // Get email from localStorage if available
  useState(() => {
    const storedEmail = localStorage.getItem('pending_verification_email')
    if (storedEmail) {
      setEmail(storedEmail)
    }
  })

  async function handleVerify() {
    setError('')
    setSuccess('')
    setLoading(true)

    if (!email || !otp) {
      setError('Please fill in all fields.')
      setLoading(false)
      return
    }

    if (otp.length !== 6) {
      setError('OTP must be 6 digits.')
      setLoading(false)
      return
    }

    try {
      const res = await api.post('/verify-email', { email, otp })
      
      // Store token and user data
      localStorage.setItem('token', res.data.token)
      localStorage.setItem('user', JSON.stringify(res.data.user))
      localStorage.removeItem('pending_verification_email')
      
      setSuccess('Email verified successfully! Redirecting...')
      
      // Redirect after 2 seconds
      setTimeout(() => {
        const redirectUrl = res.data.redirect_url || '/dashboard'
        navigate(redirectUrl)
      }, 2000)
      
    } catch (err) {
      setError(err.response?.data?.message || 'Verification failed. Please try again.')
    } finally {
      setLoading(false)
    }
  }

  async function handleResend() {
    setError('')
    setSuccess('')
    setLoading(true)

    if (!email) {
      setError('Please enter your email address.')
      setLoading(false)
      return
    }

    try {
     
await api.post('/verify-email', { email });
      setSuccess('New OTP sent to your email!')
    } catch (err) {
      setError(err.response?.data?.message || 'Failed to resend OTP. Please try again.')
    } finally {
      setLoading(false)
    }
  }

  const handleOtpChange = (e) => {
    const value = e.target.value.replace(/\D/g, '') // Only allow digits
    setOtp(value.slice(0, 6)) // Limit to 6 digits
  }

  return (
    <div className="auth-body">
      <Navbar />
      <div className="auth-page">
        <div className="auth-card">
          <div className="auth-form">
            <h2>Verify Your Email</h2>
            <p className="auth-sub">Enter the 6-digit code sent to your email</p>

            <div className="form-group">
              <label>Email Address</label>
              <input
                type="email"
                placeholder="Enter your email"
                value={email}
                onChange={e => setEmail(e.target.value)}
                disabled={loading}
              />
            </div>

            <div className="form-group">
              <label>Verification Code</label>
              <input
                type="text"
                placeholder="Enter 6-digit code"
                value={otp}
                onChange={handleOtpChange}
                maxLength={6}
                disabled={loading}
                className="otp-input"
                style={{ 
                  letterSpacing: '8px', 
                  textAlign: 'center', 
                  fontSize: '1.5rem',
                  fontWeight: 'bold',
                  fontFamily: 'monospace'
                }}
              />
            </div>

            {error && <p className="auth-error">{error}</p>}
            {success && <p className="auth-success">{success}</p>}

            <div className="verification-buttons">
              <button 
                className="btn-auth" 
                onClick={handleVerify}
                disabled={loading || otp.length !== 6}
              >
                {loading ? 'Verifying...' : 'Verify Email'}
              </button>
              
              <button 
                className="btn-resend" 
                onClick={handleResend}
                disabled={loading || !email}
                style={{
                  background: 'transparent',
                  border: '2px solid #c0392b',
                  color: '#c0392b',
                  marginTop: '1rem'
                }}
              >
                {loading ? 'Sending...' : 'Resend Code'}
              </button>
            </div>

            <div className="auth-help">
              <p style={{ fontSize: '0.9rem', color: '#666', textAlign: 'center', marginTop: '2rem' }}>
                Didn't receive the email? Check your spam folder or click "Resend Code"
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  )
}

export default EmailVerification
