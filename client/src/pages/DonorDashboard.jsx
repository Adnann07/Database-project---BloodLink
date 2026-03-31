import { useEffect, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import Navbar from '../components/Navbar';
import '../styles/Dashboard.css';
import axios from 'axios';

function DonorDashboard() {
  const navigate = useNavigate();
  const [user, setUser] = useState(null);
 const [donations] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const storedUser = localStorage.getItem('user');
    const token = localStorage.getItem('token');
    
    if (!token || !storedUser) {
      navigate('/auth');
      return;
    }
    
    const userData = JSON.parse(storedUser);
    setUser(userData);

    // Fetch fresh user data with profile
    const fetchUserData = async () => {
      try {
        const response = await axios.get('http://localhost:8000/api/me', {
          headers: {
            'Authorization': `Bearer ${token}`
          }
        });
        
        // Update localStorage and state with fresh data
        localStorage.setItem('user', JSON.stringify(response.data));
        setUser(response.data);
      } catch (error) {
        console.error('Failed to fetch user data:', error);
      } finally {
        setLoading(false);
      }
    };

    fetchUserData();
  }, [navigate]);

  const handleLogout = () => {
    localStorage.removeItem('token');
    localStorage.removeItem('user');
    navigate('/auth');
  };

  if (loading) {
    return <div>Loading...</div>;
  }

  return (
    <div className="dashboard">
      <Navbar />
      <div className="dashboard-container">
        <div className="dashboard-header">
          <h1>Welcome, {user.name}!</h1>
          <button className="btn-logout" onClick={handleLogout}>Logout</button>
        </div>

        <div className="dashboard-grid">
          {/* Profile Card */}
          <div className="dashboard-card profile-card">
            <h2>Profile Information</h2>
            <div className="profile-info">
              <p><strong>Name:</strong> {user.name}</p>
              <p><strong>Email:</strong> {user.email}</p>
              <p><strong>Role:</strong> {user.role}</p>
              <p><strong>Phone:</strong> {user.phone || 'Not provided'}</p>
              <p><strong>Address:</strong> {user.address || 'Not provided'}</p>
            </div>
          </div>

          {/* Blood Info Card */}
          <div className="dashboard-card blood-info-card">
            <h2>Blood Donation Info</h2>
            <div className="blood-info">
              <div className="blood-type-display">
                <span className="blood-type">{user.donorProfile?.blood_group || 'N/A'}</span>
                <label>Blood Group</label>
              </div>
              <div className="info-grid">
                <p><strong>Gender:</strong> {user.donorProfile?.gender || 'N/A'}</p>
                <p><strong>Date of Birth:</strong> {user.donorProfile?.date_of_birth || 'N/A'}</p>
                <p><strong>Weight:</strong> {user.donorProfile?.weight_kg ? `${user.donorProfile.weight_kg} kg` : 'N/A'}</p>
              </div>
            </div>
          </div>

          {/* Stats Card */}
          <div className="dashboard-card stats-card">
            <h2>Donation Statistics</h2>
            <div className="stats-grid">
              <div className="stat-item">
                <span className="stat-number">{donations.length}</span>
                <label>Total Donations</label>
              </div>
              <div className="stat-item">
                <span className="stat-number">0</span>
                <label>Lives Saved (Est.)</label>
              </div>
              <div className="stat-item">
                <span className="stat-number">0</span>
                <label>Points Earned</label>
              </div>
            </div>
          </div>

          {/* Next Donation Card */}
          <div className="dashboard-card next-donation-card">
            <h2>Next Eligible Donation</h2>
            <div className="next-donation">
              <p className="eligible-text">You are eligible to donate now!</p>
              <button className="btn-donate">Schedule Donation</button>
            </div>
          </div>

          {/* Recent Activity */}
          <div className="dashboard-card activity-card">
            <h2>Recent Activity</h2>
            <div className="activity-list">
              {donations.length === 0 ? (
                <p className="no-activity">No donation history yet. Make your first donation!</p>
              ) : (
                donations.map((donation, index) => (
                  <div key={index} className="activity-item">
                    <span className="activity-date">{donation.date}</span>
                    <span className="activity-type">Blood Donation</span>
                    <span className="activity-status">{donation.status}</span>
                  </div>
                ))
              )}
            </div>
          </div>

          {/* Quick Actions */}
          <div className="dashboard-card actions-card">
            <h2>Quick Actions</h2>
            <div className="quick-actions">
              <button className="btn-action">Edit Profile</button>
              <button className="btn-action">View Certificates</button>
              <button className="btn-action">Find Blood Banks</button>
              <button className="btn-action">Contact Support</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}

export default DonorDashboard;
