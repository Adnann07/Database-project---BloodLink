import { useNavigate, Link } from 'react-router-dom';
import { useState, useEffect } from 'react';

function Navbar() {
  const [isLoggedIn, setIsLoggedIn] = useState(false);
  const [user, setUser] = useState(null);
  const navigate = useNavigate();

  useEffect(() => {
    const token = localStorage.getItem('token');
    const storedUser = localStorage.getItem('user');
    
    if (token && storedUser) {
      setIsLoggedIn(true);
      setUser(JSON.parse(storedUser));
    } else {
      setIsLoggedIn(false);
      setUser(null);
    }
  }, []);

  const handleLogout = () => {
    localStorage.removeItem('token');
    localStorage.removeItem('user');
    setIsLoggedIn(false);
    setUser(null);
    navigate('/auth');
  };

  return (
    <>
      <nav className="navbar">
        <span className="nav-brand">BloodLink</span>
        <ul className="nav-links">
          <li><Link to="/">Home</Link></li>
          <li><a href="#">Donor</a></li>
          <li><a href="#">Donate</a></li>
          <li><Link to="/askai">Ask AI</Link></li>
          <li><a href="#">Volunteers</a></li>
          <li><Link to="/contact">Contact</Link></li>
        </ul>
        <div className="nav-right">
          {isLoggedIn ? (
            <>
              <span className="nav-user">Welcome, {user?.name}</span>
              <button className="nav-logout" onClick={handleLogout}>Logout</button>
            </>
          ) : (
            <>
              <Link to="/auth" className="nav-login">Log in or create account</Link>
              <button className="btn-nav">Donate Now</button>
            </>
          )}
        </div>
      </nav>
      <div className="nav-underline"></div>
    </>
  )
}

export default Navbar
