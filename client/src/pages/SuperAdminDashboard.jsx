import React, { useState, useEffect } from 'react';
import axios from 'axios';
import { useNavigate } from 'react-router-dom';
import Navbar from '../components/Navbar';
import '../styles/SuperAdminDashboard.css';

const API_URL = 'http://localhost:8000/api';

const api = axios.create({
  baseURL: API_URL,
  withCredentials: true,
  headers: {
    'Accept': 'application/json',
    'Content-Type': 'application/json'
  }
});

api.interceptors.request.use((config) => {
  const token = localStorage.getItem('token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

function SuperAdminDashboard() {
  const navigate = useNavigate();
  const [pendingAdmins, setPendingAdmins] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    // Quick role check
    const user = JSON.parse(localStorage.getItem('user') || '{}');
    if (user.role !== 'super_admin') {
      navigate('/auth');
      return;
    }
    
    fetchPendingAdmins();
  }, [navigate]);

  const fetchPendingAdmins = async () => {
    try {
      setLoading(true);
      const res = await api.get('/superadmin/pending');
      setPendingAdmins(res.data);
      setError(null);
    } catch (err) {
      console.error(err);
      setError('Failed to fetch pending admin requests.');
    } finally {
      setLoading(false);
    }
  };

  const handleApprove = async (id) => {
    try {
      await api.post(`/superadmin/approve/${id}`);
      // Remove from list
      setPendingAdmins(pendingAdmins.filter(admin => admin.id !== id));
      alert('Admin approved successfully!');
    } catch (err) {
      console.error(err);
      alert('Failed to approve admin.');
    }
  };

  const handleReject = async (id) => {
    if (!window.confirm("Are you sure you want to reject this admin?")) return;
    try {
      await api.post(`/superadmin/reject/${id}`);
      // Remove from list
      setPendingAdmins(pendingAdmins.filter(admin => admin.id !== id));
      alert('Admin rejected successfully.');
    } catch (err) {
      console.error(err);
      alert('Failed to reject admin.');
    }
  };

  return (
    <div className="superadmin-layout">
      <Navbar />
      <div className="superadmin-container">
        <header className="sa-header">
          <h1>Super Admin Dashboard</h1>
          <p>Review and verify incoming administrative requests.</p>
        </header>

        {error && <div className="sa-error">{error}</div>}

        <div className="sa-card">
          <h2>Pending Admin Approvals</h2>
          
          {loading ? (
            <p className="loading-text">Loading requests...</p>
          ) : pendingAdmins.length === 0 ? (
            <div className="empty-state">
              <p>No pending admin requests at the moment.</p>
            </div>
          ) : (
            <div className="table-responsive">
              <table className="sa-table">
                <thead>
                  <tr>
                    <th>User ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Requested At</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  {pendingAdmins.map((profile) => (
                    <tr key={profile.id}>
                      <td>#{profile.user_id}</td>
                      <td className="font-medium">{profile.user?.name}</td>
                      <td>{profile.user?.email}</td>
                      <td>{new Date(profile.created_at).toLocaleDateString()}</td>
                      <td className="sa-actions">
                        <button 
                          className="btn-approve" 
                          onClick={() => handleApprove(profile.id)}
                        >
                          Approve
                        </button>
                        <button 
                          className="btn-reject" 
                          onClick={() => handleReject(profile.id)}
                        >
                          Reject
                        </button>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          )}
        </div>
      </div>
    </div>
  );
}

export default SuperAdminDashboard;
