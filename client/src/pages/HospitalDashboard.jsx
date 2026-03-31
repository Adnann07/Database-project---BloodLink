import { useEffect, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import Navbar from '../components/Navbar';
import '../styles/Dashboard.css';
import axios from 'axios';

function HospitalDashboard() {
  const navigate = useNavigate();
  const [user, setUser] = useState(null);
 // eslint-disable-next-line no-unused-vars
const [stats, setStats] = useState({}); 

// eslint-disable-next-line no-unused-vars
const [recentActivities, setRecentActivities] = useState([]);
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

    // Check if user is hospital
    if (userData.role !== 'hospital') {
      navigate('/dashboard'); // Redirect to donor dashboard
      return;
    }
    
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

  if (!user || user.role !== 'hospital') {
    return <div>Access denied</div>;
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
          {/* Hospital Profile Card */}
          <div className="dashboard-card profile-card">
            <h2>Hospital Profile</h2>
            <div className="profile-info">
              <p><strong>Hospital Name:</strong> {user.hospital_profile?.hospital_name || 'N/A'}</p>
              <p><strong>Email:</strong> {user.email}</p>
              <p><strong>License Number:</strong> {user.hospital_profile?.license_number || 'Not provided'}</p>
              <p><strong>Phone:</strong> {user.phone || 'Not provided'}</p>
              <p><strong>City:</strong> {user.hospital_profile?.city || 'Not provided'}</p>
            </div>
          </div>

          {/* Blood Bank Statistics */}
          <div className="dashboard-card stats-card">
            <h2>Blood Bank Statistics</h2>
            <div className="stats-grid">
              <div className="stat-item">
                <span className="stat-number">{stats?.total_donors || 0}</span>
                <label>Total Donors</label>
              </div>
              <div className="stat-item">
                <span className="stat-number">{stats?.recent_donations || 0}</span>
                <label>Recent Donations (30 days)</label>
              </div>
              <div className="stat-item">
                <span className="stat-number">{stats?.blood_requests || 0}</span>
                <label>Pending Blood Requests</label>
              </div>
            </div>
          </div>

          {/* Blood Groups Availability */}
          <div className="dashboard-card blood-availability-card">
            <h2>Blood Groups Availability</h2>
            <div className="blood-groups-grid">
              {stats?.available_blood_groups && Object.entries(stats.available_blood_groups).map(([group, data]) => (
                <div key={group} className="blood-group-item">
                  <span className="blood-type">{group}</span>
                  <div className="blood-stats">
                    <span className="available">Available: {data.available}</span>
                    <span className="recent">Recent: {data.recent_donations}</span>
                  </div>
                </div>
              ))}
            </div>
          </div>

          {/* Quick Actions */}
          <div className="dashboard-card actions-card">
            <h2>Quick Actions</h2>
            <div className="quick-actions">
              <button className="btn-action btn-primary">Request Blood</button>
              <button className="btn-action">View Donors</button>
              <button className="btn-action">Manage Requests</button>
              <button className="btn-action">Edit Profile</button>
            </div>
          </div>

          {/* Recent Activities */}
          <div className="dashboard-card activity-card">
            <h2>Recent Activities</h2>
            <div className="activity-list">
              {recentActivities.length === 0 ? (
                <p className="no-activity">No recent activities</p>
              ) : (
                recentActivities.map((activity, index) => (
                  <div key={index} className="activity-item">
                    <span className="activity-date">{new Date(activity.created_at).toLocaleDateString()}</span>
                    <span className="activity-type">{activity.type === 'donation' ? 'Blood Donation' : 'Blood Request'}</span>
                    <span className="activity-status">{activity.status}</span>
                    {activity.blood_group && (
                      <span className="blood-type-small">{activity.blood_group}</span>
                    )}
                  </div>
                ))
              )}
            </div>
          </div>

          {/* Urgent Requests */}
          <div className="dashboard-card urgent-card">
            <h2>Urgent Blood Requests</h2>
            <div className="urgent-list">
              {recentActivities.filter(a => a.type === 'request' && a.urgency_level === 'critical').length === 0 ? (
                <p className="no-urgent">No urgent requests at the moment</p>
              ) : (
                recentActivities
                  .filter(a => a.type === 'request' && a.urgency_level === 'critical')
                  .map((request, index) => (
                    <div key={index} className="urgent-item">
                      <span className="urgent-type">{request.blood_group}</span>
                      <span className="urgent-units">{request.units_needed} units</span>
                      <span className="urgent-patient">{request.patient_name}</span>
                      <button className="btn-urgent">Respond</button>
                    </div>
                  ))
              )}
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}

export default HospitalDashboard;
