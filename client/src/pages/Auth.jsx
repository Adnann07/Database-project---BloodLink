import { useState } from 'react'
import { useNavigate } from 'react-router-dom'
import axios from 'axios'
import Navbar from '../components/Navbar'

const API_URL = 'http://localhost:8000/api'

const api = axios.create({
  baseURL: API_URL,
  withCredentials: true, // Crucial for Laravel Sanctum
  headers: {
    'Accept': 'application/json',
    'Content-Type': 'application/json'
  }
});

function Auth() {
  const navigate = useNavigate()
  const [tab, setTab] = useState('login')

  // Login state
  const [loginData, setLoginData] = useState({ email: '', password: '' })
  const [loginError, setLoginError] = useState('')

  // Register state
  const [regData, setRegData] = useState({
    name: '', email: '', password: '', phone: '', role: '',
    blood_group: '', date_of_birth: '', gender: '', weight_kg: '',
    hospital_name: '', city: '', license_number: ''
  })
  const [regError, setRegError] = useState('')

  async function handleLogin() {
    setLoginError('')
    if (!loginData.email || !loginData.password) {
      setLoginError('Please fill in all fields.')
      return
    }
    try {
      const res = await api.post('/login', loginData)
      
      if (res.data.requires_verification) {
        // Store email for verification page
        localStorage.setItem('pending_verification_email', loginData.email)
        setLoginError('Please verify your email first. Redirecting...')
        setTimeout(() => {
          navigate('/verify-email')
        }, 1500)
        return
      }
      
      localStorage.setItem('token', res.data.token)
      localStorage.setItem('user', JSON.stringify(res.data.user))
      // Use redirect_url from server response or fallback
      const redirectUrl = res.data.redirect_url || '/dashboard'
      navigate(redirectUrl)
    } catch (err) {
      setLoginError(err.response?.data?.message || 'Login failed. Please try again.')
    }
  }

  async function handleRegister() {
    setRegError('')
    if (!regData.name || !regData.email || !regData.password || !regData.role) {
      setRegError('Please fill in all required fields.')
      return
    }
    if (regData.role === 'donor' && (!regData.blood_group || !regData.date_of_birth || !regData.gender)) {
      setRegError('Please fill in all donor fields.')
      return
    }
    if (regData.role === 'hospital' && (!regData.hospital_name || !regData.city)) {
      setRegError('Please fill in all hospital fields.')
      return
    }
    try {
      const res = await api.post('/register', regData)
      console.log('Registration response:', res.data)
      
      if (res.data.requires_verification) {
        // Store email for verification page
        localStorage.setItem('pending_verification_email', regData.email)
        // Navigate to verification page
        navigate('/verify-email')
      } else if (res.data.is_admin_pending) {
        setTab('login')
        setLoginError('Registration successful! Please wait for Super Admin approval before logging in.')
        setLoginData({ ...loginData, email: regData.email })
      } else {
        // Direct registration (fallback)
        localStorage.setItem('token', res.data.token)
        localStorage.setItem('user', JSON.stringify(res.data.user))
        const role = res.data.user.role
        if (role === 'admin') navigate('/admin-dashboard')
        else if (role === 'super_admin') navigate('/super-admin-dashboard')
        else if (role === 'donor') navigate('/dashboard')
        else if (role === 'hospital') navigate('/hospital/dashboard')
      }
    } catch (err) {
      console.error('Registration error:', err)
      console.error('Response:', err.response)
      setRegError(err.response?.data?.message || err.message || 'Registration failed. Please try again.')
    }
  }

  return (
    <div className="auth-body">
      <Navbar />
      <div className="auth-page">
        <div className="auth-card">

          {/* Tabs */}
          <div className="auth-tabs">
            <button className={`auth-tab ${tab === 'login' ? 'active' : ''}`} onClick={() => setTab('login')}>Login</button>
            <button className={`auth-tab ${tab === 'register' ? 'active' : ''}`} onClick={() => setTab('register')}>Register</button>
          </div>

          {/* Login Form */}
          {tab === 'login' && (
            <div className="auth-form">
              <h2>Welcome Back</h2>
              <p className="auth-sub">Login to your BloodLink account</p>

              <div className="form-group">
                <label>Email</label>
                <input
                  type="email"
                  placeholder="Enter your email"
                  value={loginData.email}
                  onChange={e => setLoginData({ ...loginData, email: e.target.value })}
                />
              </div>

              <div className="form-group">
                <label>Password</label>
                <input
                  type="password"
                  placeholder="Enter your password"
                  value={loginData.password}
                  onChange={e => setLoginData({ ...loginData, password: e.target.value })}
                />
              </div>

              {loginError && <p className="auth-error">{loginError}</p>}

              <button className="btn-auth" onClick={handleLogin}>Login</button>
              <p className="auth-switch">Don't have an account? <a href="#" onClick={() => setTab('register')}>Register</a></p>
            </div>
          )}

          {/* Register Form */}
          {tab === 'register' && (
            <div className="auth-form">
              <h2>Create Account</h2>
              <p className="auth-sub">Join BloodLink today</p>

              <div className="form-group">
                <label>Full Name</label>
                <input
                  type="text"
                  placeholder="Enter your full name"
                  value={regData.name}
                  onChange={e => setRegData({ ...regData, name: e.target.value })}
                />
              </div>

              <div className="form-group">
                <label>Email</label>
                <input
                  type="email"
                  placeholder="Enter your email"
                  value={regData.email}
                  onChange={e => setRegData({ ...regData, email: e.target.value })}
                />
              </div>

              <div className="form-group">
                <label>Password</label>
                <input
                  type="password"
                  placeholder="Create a password"
                  value={regData.password}
                  onChange={e => setRegData({ ...regData, password: e.target.value })}
                />
              </div>

              <div className="form-group">
                <label>Phone (optional)</label>
                <input
                  type="tel"
                  placeholder="Enter your phone number"
                  value={regData.phone}
                  onChange={e => {
                    // Only allow digits, +, -, (, ), and spaces
                    const value = e.target.value.replace(/[^0-9+\-\(\)\s]/g, '');
                    setRegData({ ...regData, phone: value });
                  }}
                />
              </div>

              <div className="form-group">
                <label>Role</label>
                <select value={regData.role} onChange={e => setRegData({ ...regData, role: e.target.value })}>
                  <option value="">Select your role</option>
                  <option value="donor">Donor</option>
                  <option value="hospital">Hospital</option>
                  <option value="admin">Admin</option>
                </select>
              </div>

              {/* Donor Fields */}
              {regData.role === 'donor' && (
                <div className="extra-fields">
                  <div className="form-group">
                    <label>Blood Group</label>
                    <select value={regData.blood_group} onChange={e => setRegData({ ...regData, blood_group: e.target.value })}>
                      <option value="">Select blood group</option>
                      {['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'].map(bg => (
                        <option key={bg} value={bg}>{bg}</option>
                      ))}
                    </select>
                  </div>
                  <div className="form-group">
                    <label>Date of Birth</label>
                    <input
                      type="date"
                      value={regData.date_of_birth}
                      onChange={e => setRegData({ ...regData, date_of_birth: e.target.value })}
                    />
                  </div>
                  <div className="form-group">
                    <label>Gender</label>
                    <select value={regData.gender} onChange={e => setRegData({ ...regData, gender: e.target.value })}>
                      <option value="">Select gender</option>
                      <option value="male">Male</option>
                      <option value="female">Female</option>
                      <option value="other">Other</option>
                    </select>
                  </div>
                  <div className="form-group">
                    <label>Weight in kg (optional)</label>
                    <input
                      type="number"
                      placeholder="Enter your weight"
                      value={regData.weight_kg}
                      onChange={e => setRegData({ ...regData, weight_kg: e.target.value })}
                    />
                  </div>
                </div>
              )}

              {/* Hospital Fields */}
              {regData.role === 'hospital' && (
                <div className="extra-fields">
                  <div className="form-group">
                    <label>Hospital Name</label>
                    <input
                      type="text"
                      placeholder="Enter hospital name"
                      value={regData.hospital_name}
                      onChange={e => setRegData({ ...regData, hospital_name: e.target.value })}
                    />
                  </div>
                  <div className="form-group">
                    <label>City</label>
                    <input
                      type="text"
                      placeholder="Enter city"
                      value={regData.city}
                      onChange={e => setRegData({ ...regData, city: e.target.value })}
                    />
                  </div>
                  <div className="form-group">
                    <label>License Number (optional)</label>
                    <input
                      type="text"
                      placeholder="Enter license number"
                      value={regData.license_number}
                      onChange={e => setRegData({ ...regData, license_number: e.target.value })}
                    />
                  </div>
                </div>
              )}

              {regError && <p className="auth-error">{regError}</p>}

              <button className="btn-auth" onClick={handleRegister}>Create Account</button>
              <p className="auth-switch">Already have an account? <a href="#" onClick={() => setTab('login')}>Login</a></p>
            </div>
          )}

        </div>
      </div>
    </div>
  )
}

export default Auth
