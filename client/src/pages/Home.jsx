import { useEffect } from 'react'
import { Link } from 'react-router-dom'
import Navbar from '../components/Navbar'

function Home() {

  useEffect(() => {
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) entry.target.classList.add('visible')
      })
    }, { threshold: 0.05 })

    document.querySelectorAll('.fade-up').forEach(el => observer.observe(el))

    // Trigger elements already on screen
    document.querySelectorAll('.fade-up').forEach(el => {
      const rect = el.getBoundingClientRect()
      if (rect.top < window.innerHeight) el.classList.add('visible')
    })

    return () => observer.disconnect()
  }, [])

  return (
    <div>
      <Navbar />

      {/* Hero */}
      <section className="hero">
        <div className="hero-text fade-up">
          <h1>Give Blood,<br />Save Lives</h1>
          <p>Your one drop of blood can change someone's entire world. Join thousands of heroes making a difference every day.</p>
          <div className="hero-buttons">
            <Link to="/auth"><button className="btn-primary">Donate Now</button></Link>
            <button className="btn-outline">Learn More</button>
          </div>
        </div>
        <div className="hero-logo fade-up">
          <img src="/logo.png" alt="BloodLink Logo" />
        </div>
      </section>

      {/* Why Donate */}
      <section className="why">
        <h2 className="section-title fade-up">Why Donate Blood?</h2>
        <p className="section-sub fade-up">Every donation makes a real difference</p>
        <div className="why-cards">
          <div className="why-card fade-up">
            <div className="why-icon">❤️</div>
            <h3>Save Lives</h3>
            <p>One donation can save up to three lives. Your contribution directly impacts patients in urgent need.</p>
          </div>
          <div className="why-card fade-up">
            <div className="why-icon">🩸</div>
            <h3>Always Needed</h3>
            <p>Blood cannot be manufactured. Regular donations ensure hospitals have a steady, reliable supply.</p>
          </div>
          <div className="why-card fade-up">
            <div className="why-icon">🤝</div>
            <h3>Build Community</h3>
            <p>Join a network of compassionate donors. Be part of something bigger than yourself.</p>
          </div>
          <div className="why-card fade-up">
            <div className="why-icon">💪</div>
            <h3>Health Benefits</h3>
            <p>Regular donation can improve cardiovascular health and provide free health screenings.</p>
          </div>
        </div>
      </section>

      {/* How It Works */}
      <section className="how">
        <h2 className="section-title fade-up">How It Works</h2>
        <p className="section-sub fade-up">Simple steps to become a life-saver</p>
        <div className="steps">
          <div className="step fade-up">
            <span className="step-num">1</span>
            <div className="step-circle">👤</div>
            <h3>Register</h3>
            <p>Create your donor account and provide basic information.</p>
          </div>
          <div className="step fade-up">
            <span className="step-num">2</span>
            <div className="step-circle">✅</div>
            <h3>Eligibility Check</h3>
            <p>Complete a quick health screening to ensure eligibility.</p>
          </div>
          <div className="step fade-up">
            <span className="step-num">3</span>
            <div className="step-circle">📍</div>
            <h3>Donate at Center</h3>
            <p>Visit our donation center and complete the process.</p>
          </div>
          <div className="step fade-up">
            <span className="step-num">4</span>
            <div className="step-circle">❤️</div>
            <h3>Save a Life</h3>
            <p>Your blood goes directly to patients in need.</p>
          </div>
        </div>
      </section>

      {/* Upcoming Events */}
      <section className="events">
        <h2 className="section-title fade-up">Upcoming Donation Drives</h2>
        <p className="section-sub fade-up">Find a drive near you and make a difference</p>
        <div className="event-cards">
          <div className="event-card fade-up">
            <h3>Community Blood Drive</h3>
            <p className="event-date">📅 January 25, 2024</p>
            <p className="event-location">📍 Central Community Center</p>
            <p className="event-time">9:00 AM – 5:00 PM</p>
            <button className="btn-join">Join Now</button>
          </div>
          <div className="event-card fade-up">
            <h3>University Campus Drive</h3>
            <p className="event-date">📅 February 2, 2024</p>
            <p className="event-location">📍 State University Main Hall</p>
            <p className="event-time">10:00 AM – 4:00 PM</p>
            <button className="btn-join">Join Now</button>
          </div>
          <div className="event-card fade-up">
            <h3>Corporate Donation Day</h3>
            <p className="event-date">📅 February 10, 2024</p>
            <p className="event-location">📍 Tech Park Business Center</p>
            <p className="event-time">8:00 AM – 3:00 PM</p>
            <button className="btn-join">Join Now</button>
          </div>
        </div>
      </section>

      {/* CTA */}
      <section className="cta">
        <h2 className="fade-up">Ready to Give Hope?<br />Become a Blood Donor Today.</h2>
        <p className="fade-up">Join our community of heroes and start making a difference. Your donation can save lives.</p>
        <Link to="/auth"><button className="btn-cta fade-up">Become a Donor</button></Link>
      </section>
    </div>
  )
}

export default Home
