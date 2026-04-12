import { BrowserRouter, Routes, Route } from 'react-router-dom'
import Home from './pages/Home'
import Auth from './pages/Auth'
import EmailVerification from './pages/EmailVerification'
import AskAI from './pages/AskAI'
import DonorDashboard from './pages/DonorDashboard'
import HospitalDashboard from './pages/HospitalDashboard'
import Contact from './pages/Contact'
import SuperAdminDashboard from './pages/SuperAdminDashboard'

function App() {
  return (
    <BrowserRouter>
      <Routes>
        <Route path="/" element={<Home />} />
        <Route path="/auth" element={<Auth />} />
        <Route path="/verify-email" element={<EmailVerification />} />
        <Route path="/askai" element={<AskAI />} />
        <Route path="/contact" element={<Contact />} />
        <Route path="/dashboard" element={<DonorDashboard />} />
        <Route path="/hospital/dashboard" element={<HospitalDashboard />} />
        <Route path="/super-admin-dashboard" element={<SuperAdminDashboard />} />
      </Routes>
    </BrowserRouter>
  )
}

export default App
